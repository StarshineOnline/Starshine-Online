<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$perso->check_perso();
$bonus = recup_bonus($perso->get_id());


$interf_princ = $G_interf->creer_jeu();

$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 1;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;


if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
	case 'prend' :
		if(array_key_exists($_GET['id'], $bonus))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous possédez déjà ce bonus !');
			break;
		}
		//Récupération des infos interessantes du bonus
		$requete = "SELECT * FROM bonus WHERE id_bonus = ".sSQL($_GET['id']);
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		//Vérification si il a assez de points
		if($perso->get_point_sso() >= $row['point'])
		{
			//Vérifie si il a assez en compétence requise
			if($perso->get_comp($row['competence_requis']) >= $row['valeur_requis'])
			{
				$requete = "SELECT * FROM bonus_permet WHERE id_bonus_permet = ".sSQL($_GET['id']);
				$req_bn = $db->query($requete);
				$bn_num_rows = $db->num_rows;
				$check = true;
				while(($row_bn = $db->read_assoc($req_bn)) AND $check)
				{
					if(!array_key_exists($row_bn['id_bonus'], $bonus)) $check = false;
				}
				if($check)
				{
					if( in_array($_GET['id'], array(bonus_perso::CACHE_GRADE_ID, bonus_perso::CACHE_CLASSE_ID, bonus_perso::CACHE_STATS_ID, bonus_perso::CACHE_NIVEAU_ID)) )
						$perso->ajout_bonus_shine($_GET['id'], '', 1);
					else
						$perso->ajout_bonus_shine($_GET['id']);
					$perso->set_point_sso($perso->get_point_sso() - $row['point']);
					$perso->sauver();
					$bonus = recup_bonus($perso->get_id());
				}
				else
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous manque un bonus pour apprendre celui-ci');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$row['valeur_requis'].' en '.$Gtrad[$row['competence_requis']]);
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de points Shine');
		break;
	case 'configure':
		$interf_princ->set_dialogue( $G_interf->creer_bonus_shine_config($_GET['id']) );
		break;
	case 'modifier':
		$bonus_total = recup_bonus_total(joueur::get_perso()->get_id());
		// Changement d'état
		if( array_key_exists('etat', $_POST) )
		{
			switch($id)
			{
				case bonus_perso::CACHE_CLASSE_ID :
					$perso->set_cache_classe($_GET['etat']);
				break;
				case bonus_perso::CACHE_STATS_ID :
					$perso->set_cache_stat($_GET['etat']);
				break;
				case bonus_perso::CACHE_NIVEAU_ID :
					$perso->set_cache_niveau($_GET['etat']);
				break;
			}
			// On modifie dans la table perso, si nécessaire
			$perso->sauver();
			
			$bonus_total = recup_bonus_total($perso->get_id());
			$requete = "UPDATE bonus_perso SET etat = ".sSQL($_POST['etat'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
			$db->query($requete);
			$bonus = recup_bonus($perso->get_id());
		}
		// Changement de valeur
		if( array_key_exists('valeur', état) )
		{
			$bonus_total = recup_bonus_total($perso->get_id());
			$requete = "UPDATE bonus_perso SET valeur = ".sSQL($_POST['valeur'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
			$db->query($requete);
			$bonus = recup_bonus($perso->get_id());
		}
		// Changement de texte
		else if( array_key_exists('texte', état) )
		{
			$bonus_total = recup_bonus_total($perso->get_id());
			$requete = "UPDATE bonus_perso SET valeur = ".sSQL(htmlspecialchars($_POST['texte']))." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
			$db->query($requete);
			$bonus = recup_bonus($perso->get_id());
		}
		//Avatar
		else if(array_key_exists('nom_du_fichier', $_FILES))
		{
			if($_FILES['nom_du_fichier']['error'])
			{
				switch ($_FILES['nom_du_fichier']['error'])
				{
				case 1 :
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le fichier dépasse la limite autorisée par le serveur !');
					break;
				case 2 :
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le fichier dépasse la limite autorisée dans le formulaire HTML !');
					break;
				case 3 :
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'L\'envoi du fichier a été interrompu pendant le transfert !');
					break;
				case 4 :
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le fichier que vous avez envoyé a une taille nulle !');
					break;
				}
			}
			else
			{
				$chemin_destination = 'image/avatar/';
				//Vérification du type
				$type_file = $_FILES['nom_du_fichier']['type'];
				switch( strstr($type_file, 'jpg') )
				{
				case 'jpg':
				case 'jpeg':
					$type = '.jpg';
					break;
				case 'bmp':
					$type = '.bmp';
					break;
				case 'gif':
					$type = '.gif';
					break;
				case 'png':
					$type = '.png';
					break;
				default:
					$type = false;
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le fichier n\'est pas une image !');
				}
				if( !$type )
					false;
				//Récupère le type
				$nom_fichier = root.$chemin_destination.$joueur->get_id().$type;
				if( move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $nom_fichier) )
				{
					//On vérifie la taille de l'image
					$size = getimagesize($nom_fichier);
					//Si compris entre 80 * 80
					if($size[0] <= 80 && $size[1] <= 80)
					{
						$bonus_total = recup_bonus_total($joueur->get_id());
						$requete = "UPDATE bonus_perso SET valeur = '".sSQL($joueur->get_id().$type)."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
						$db->query($requete);
						$bonus = recup_bonus($joueur->get_id());
					}
					//Sinon on efface l'image
					else
					{
						unlink($nom_fichier);
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre fichier n\'a pas les bonnes dimensions !');
					}
				}
			}
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre avatar a bien été modifié ');
		}
		break;
	}
}

if( $ajax == 2 )
{
	interf_alerte::aff_enregistres($interf_princ);
	$interf_princ->add( $G_interf->creer_bonus_shine($categorie) );
	$interf_princ->add( new interf_bal_smpl('p', 'Cliquez sur un bonus que vous avez déjà pour le configurer', false, 'xsmall') );
}
else
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Points shine <span class="badge">'.$perso->get_point_sso().'</span>') );
	interf_alerte::aff_enregistres($cadre);
	$cadre->add( $G_interf->creer_points_shine($categorie) );
}
