<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
// Infos sur un objet
if( $action == 'infos' )
{
  $interf->creer_infos_objet($_GET['id']);
  exit;
}

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$joueur_id = $_SESSION['ID'];
}
$joueur = new perso($joueur_id);

switch( $action )
{
case 'princ':
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_equip($joueur, $_GET['page'], !$visu) );
  exit;
case 'sac':
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_sac($joueur, $_GET['slot'], !$visu) );
  exit;
case 'hotel_vente':
  $princ = $interf->creer_vente_hotel($joueur, $_GET['objet']);
  exit;
}

//Filtre
if(array_key_exists('filtre', $_GET))
{
  $filtre = $_GET['filtre'];
  $filtre_url = '&amp;filtre='.$_GET['filtre'];
}
else
{
  $filtre = 'utile';
  $filtre_url = '&amp;filtre=utile';
}
$W_requete = 'SELECT royaume, type, info FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

$princ = $interf->creer_princ_droit('Inventaire du Personnage');
//Switch des actions
if( !$visu && $action )
{
  verif_mort($joueur, 1);
	switch($action)
	{
		case 'desequip' :
			if(!$joueur->desequip($_GET['partie']))
        $princ->add_message($G_erreur, false);
		break;
		case 'equip' :
			if($joueur->equip_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'])))
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
				$joueur->sauver();
			}
			else
				$princ->add_message($G_erreur, false);
		break;
		case 'utilise' :
				// Pose d'un bâtiment ou ADS
			if($_GET['type'] == 'fort' OR $_GET['type'] == 'tour' OR $_GET['type'] == 'bourg' OR $_GET['type'] == 'mur' OR $_GET['type'] == 'arme_de_siege')
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
				$joueur->sauver();
      }
			switch($_GET['type'])
			{
				case 'drapeau' :
					//On supprime l'objet de l'inventaire
					$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
					$joueur->sauver();
				break;
				case 'identification' :
				break;
			case 'grimoire':
				if ($ok)
				{
					$joueur->supprime_objet($id_objet, 1);
				}
        else
          $princ->add_message('Vous ne pouvez pas lire ce grimoire', false);
				break;
			default:
				error_log('Utilisation d\'un objet invalide: '.$_GET['type']);
			}
		break;
		//Dépot de l'objet au dépot militaire
		case 'depot' :
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
		break;
		case 'vente' :
			$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
		break;
		case 'ventehotel' :
			//On vérifie qu'il a moins de 10 objets en vente actuellement
			$requete = "SELECT COUNT(*) FROM hotel WHERE id_vendeur = ".$joueur->get_id();
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$objet_max = 10;
			$bonus_craft = ceil($joueur->get_artisanat() / 5);
			$objet_max += $bonus_craft;
			if($row[0] >= $objet_max)
			{
        $princ->add_message('Vous avez déjà '.$objet_max.' objets ou plus en vente.', false);
			}
			else
			{
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$categorie = $objet['categorie'];
				switch ($categorie)
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'main_droite';
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'd' :
						$requete = "SELECT * FROM objet_pet WHERE id = ".$objet['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$partie = $row['type'];
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$row['prix'] = pow(8, ($row['niveau'] + 1)) * 10;
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
					case 'l' :
						$requete = "SELECT * FROM grimoire WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
				}
				$modif_prix = 1;
				if($objet['slot'] > 0)
				{
					$modif_prix = 1 + ($objet['slot'] / 5);
				}
				if($objet['slot'] == '0')
				{
					$modif_prix = 0.9;
				}
				if($objet['enchantement'] > '0')
				{
					$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
					$req = $db->query($requete);
					$row_e = $db->read_assoc($req);
					$modif_prix = 1 + ($row_e['niveau'] / 2);
				}
				$prix = floor((2 * $row['prix']) * $modif_prix / $G_taux_vente);
				$prixmax = $prix * 10;
        //$princ->add( new interf_bal_smpl('h2', 'Inventaire') );
        $div = $princ->add( new interf_bal_cont(div) );
        $div->set_attribut('style', 'font-size : 0.9em;');
        $form = $div->add( new interf_form('javascript:envoiInfo(\'inventaire.php\', \'information\');', 'get') );
        $form->set_attribut('name', 'formulaire');
        $form->add( new interf_txt('Mettre en vente à l\'hotel des ventes pour ') );
        $chp1 = $form->add( new interf_chp_form('text', 'prix', false, $prix) );
        $chp1->set_attribut('onchange', 'formulaire.comm.value = formulaire.prix.value * '.($R->get_taxe_diplo($joueur->get_race()) / 100));
        $chp1->set_attribut('onkeyup', 'formulaire.comm.value = formulaire.prix.value * '.($R->get_taxe_diplo($joueur->get_race()) / 100));
        $form->add( new interf_txt(' Stars') );
        $form->add( new interf_bal_smpl('br') );
        $form->add( new interf_txt('Taxe : ') );
        $chp2 = $form->add( new interf_chp_form('text', 'comm', false, $prix * $R->get_taxe_diplo($joueur->get_race()) / 100) );
        $chp2->set_attribut('disabled', 'true');
        $form->add( new interf_bal_smpl('br') );
        $form->add( new interf_txt('Maximum = '.$prixmax.' stars.') );
        $form->add( new interf_bal_smpl('br') );
        $form->add( new interf_chp_form('hidden', 'action', false, 'ventehotel2') );
        $btn = $form->add( new interf_chp_form('button', 'btnSubmit', false, 'Mettre en vente') );
        $btn->set_attribut('onclick', 'javascript:envoiInfo(\'inventaire.php?action=ventehotel2&amp;key_slot='.$_GET['key_slot'].'&amp;prix=\' + formulaire.prix.value + \'&amp;max='.$prixmax.'&amp;comm=\' + formulaire.comm.value, \'information\');');
				exit();
			}
		break;
		case 'ventehotel2' :
			$comm = $_GET['comm'];
			if($_GET['prix'] > $_GET['max'])
			{
        $princ->add_message('Vous voulez vendre cet objet trop chère, le commissaire priseur n\'en veut pas !', false);
			}
			else
			{
				if($_GET['prix'] > 0)
				{
					if($joueur->get_star() >= $comm)
					{
						$objet = $joueur->get_inventaire_slot_partie($_GET['key_slot']);
						$objet_d = decompose_objet($objet);
						switch ($objet_d['categorie'])
						{
							//Si c'est une arme
							case 'a' :
								$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = 'main_droite';
								$objet_id = $objet;
							break;
							//Si c'est une protection
							case 'p' :
								$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = $row['type'];
								$objet_id = $objet;
							break;
							case 'd' :
								$requete = "SELECT * FROM objet_pet WHERE id = ".$objet_d['id_objet'];
								$req = $db->query($requete);
								$row = $db->read_assoc($req);
								$partie = $row['type'];
								$objet_id = $objet;
							break;
							case 'o' :
								$requete = "SELECT * FROM objet WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet_d['id'];
							break;
							case 'g' :
								$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$row['prix'] = pow(15, $row['niveau']) * 10;
								$objet_id = $objet;
							break;
							case 'm' :
								$requete = "SELECT * FROM accessoire WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet;
							break;
							case 'l' :
								$requete = "SELECT * FROM grimoire WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet;
							break;
						}
						$prix = $_GET['prix'];
						if($objet_id != '')
						{
							$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
							$joueur->set_star($joueur->get_star() - $comm);
							$joueur->sauver();
							$requete = "INSERT INTO hotel VALUES (NULL, '".$objet_id."', ".$joueur->get_id().", ".sSQL($_GET['prix']).", 1, '".$R->get_race()."', ".time().")";
							$req = $db->query($requete);
							$R->set_star($R->get_star() + $comm);
							$R->sauver();
							$requete = "UPDATE argent_royaume SET hv = hv + ".$comm." WHERE race = '".$R->get_race()."'";
							$db->query($requete);
							$message_mail = $joueur->get_nom()." vend ".nom_objet($objet_id)." (".$objet_id.") pour ".$_GET['prix']." stars. Commission : ".$comm." stars";
							$princ->add( new interf_txt('Vous mettez en vente '.nom_objet($objet_id).' pour '.$_GET['prix'].' stars. Commission : '.$comm.' stars') );
							$princ->add( new interf_bal_smpl('br') );
						}
						$log_admin = new log_admin();
						$log_admin->send($joueur->get_id(), 'mis en vente HV', $message_mail);
					}
					else
					{
            $princ->add_message('Vous n\'avez pas assez de stars pour payer la commission', false);
					}
				}
				else
				{
          $princ->add_message('Pas de prix négatif ou nul !', false);
				}
			}
		break;
		case 'slot' :
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}
			
			// Gemme de fabrique : augmente de effet % le craft
			$joueur->get_armure();
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			$chance_reussite1 = pourcent_reussite($craft, 10);
			$chance_reussite2 = pourcent_reussite($craft, 30);
			$chance_reussite3 = pourcent_reussite($craft, 100);
			$opt = $princ->add( new interf_menu('Quel niveau d\'enchâssement voulez vous ?', '', '') );
			$elt1 = $opt->add( 	new interf_bal_cont('li') );
			$lien1 = $elt1->add( new interf_bal_smpl('a', 'Niveau 1') );
			$lien1->set_attribut('href', 'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=1'.$filtre_url);
			$lien1->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
			$elt1->add( new interf_txt(' ') );
			$elt1->add( new interf_bal_smpl('span', '('.$chance_reussite1.'% de chances de réussite)', false, 'small') );
			$elt2 = $opt->add( new interf_bal_cont('li') );
			$lien2 = $elt2->add( new interf_bal_smpl('a', 'Niveau 2') );
			$lien2->set_attribut('href', 'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=2'.$filtre_url);
			$lien2->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
			$elt2->add( new interf_txt(' ') );
			$elt2->add( new interf_bal_smpl('span', '('.$chance_reussite2.'% de chances de réussite)', false, 'small') );
			$elt3 = $opt->add( new interf_bal_cont('li') );
			$lien3 = $elt3->add( new interf_bal_smpl('a', 'Niveau 3') );
			$lien3->set_attribut('href', 'inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=3'.$filtre_url);
			$lien3->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
			$elt3->add( new interf_txt(' ') );
			$elt3->add( new interf_bal_smpl('span', '('.$chance_reussite3.'% de chances de réussite)', false, 'small') );
		break;
		case 'slot2' :
			if($joueur->get_pa() >= 10)
			{
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				if(empty($objet['slot']))
				{
					switch($_GET['niveau'])
					{
						case '1' :
							$difficulte = 10;
						break;
						case '2' :
							$difficulte = 30;
						break;
						case '3' :
							$difficulte = 100;
						break;
					}
					$craft = $joueur->get_forge();
					if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
					if($joueur->get_accessoire() !== false)
					{
						$accessoire = $joueur->get_accessoire();
						if($accessoire->type == 'fabrication')
							$craft = round($craft * (1 + ($accessoire->effet / 100)));
					}

					// Gemme de fabrique : augmente de effet % le craft
					$joueur->get_armure();
					if ($joueur->get_enchantement()!== false &&
							$joueur->is_enchantement('forge')) {
						$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
					}

					$craftd = rand(0, $craft);
					$diff = rand(0, $difficulte);
					/*echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
					Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';*/
					if($craftd >= $diff)
					{
						//Craft réussi
            $princ->add( new interf_txt('Réussite !') );
            $princ->add( new interf_bal_smpl('br') );
						$objet['slot'] = $_GET['niveau'];
						
						// Augmentation du compteur de l'achievement
						$achiev = $joueur->get_compteur('objets_slot');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
					else
					{
						//Craft échec
            $princ->add( new interf_txt('Echec... L\'objet ne pourra plus être enchâssable') );
            $princ->add( new interf_bal_smpl('br') );
						$objet['slot'] = 0;
					}
					$augmentation = augmentation_competence('forge', $joueur, 2);
					if ($augmentation[1] == 1)
					{
						$joueur->set_forge($augmentation[0]);
						$joueur->sauver();
					}
					$objet_r = recompose_objet($objet);
					$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot']);
					$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
					$joueur->set_pa($joueur->get_pa() - 10);
					$joueur->sauver();
				}
				else
          $princ->add_message('Cet objet &agrave; d&eacute;j&agrave; un slot!', false);
			}
			else
			{
        $princ->add_message('Vous n\'avez pas assez de PA.', false);
			}
		break;
		case 'enchasse' :
			$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
			$requete = "SELECT * FROM gemme WHERE id = ".$gemme['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			switch($row['type'])
			{
				case 'arme' :
					$type = 'a';
				break;
				case 'armure' :
					$type = 'p';
				break;
				case 'accessoire' :
					$type = 'm';
				break;
			}
			switch($row['niveau'])
			{
				case 1 :
					$difficulte = 10;
				break;
				case 2 :
					$difficulte = 30;
				break;
				case 3 :
					$difficulte = 100;
				break;
			}
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}

			// Gemme de fabrique : augmente de effet % le craft
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			$opt = new interf_menu('Dans quel objet voulez vous enchâsser cette gemme de niveau '.$row['niveau'].' ?', '', '');
      $princ->add($opt);
			//Recherche des objets pour enchassement possible
			$i = 0;
			while($i <= $G_place_inventaire)
			{
				if($joueur->get_inventaire_slot_partie($i) != '')
				{
					$objet_i = decompose_objet($joueur->get_inventaire_slot_partie($i));
					//echo '<br />'.$joueur->get_inventaire_slot()[$i].'<br />';
					if($objet_i['identifier'] AND $objet_i['categorie'] != 'r')
					{
						if($objet_i['categorie'] == 'a') $table = 'arme';
						elseif($objet_i['categorie'] == 'p') $table = 'armure';
						elseif($objet_i['categorie'] == 'm') $table = 'accessoire';
						elseif($objet_i['categorie'] == 'o') $table = 'objet';
						elseif($objet_i['categorie'] == 'g') $table = 'gemme';
						else {
							print_debug("table introuvable pour $objet_i[categorie]");
							$i++;
							continue;
						}
						$requete = "SELECT type FROM ".$table." WHERE id = ".$objet_i['id_objet'];
						$req_i = $db->query($requete);
						$row_i = $db->read_row($req_i);
						$check = true;
						$j = 0;
						$parties = explode(';', $row['partie']);
						$count = count($parties);
						if (strlen($row['partie']) > 0) $check = false;
						while(!$check AND $j < $count)
						{
							if($parties[$j] == $row_i[0]) $check = true;
							//echo $parties[$j].' '.$row_i[0].'<br />';
							$j++;
						}
						if($check AND ($objet_i['categorie'] == $type) AND ($objet_i['slot'] >= $row['niveau']))
						{
							$nom = nom_objet($joueur->get_inventaire_slot_partie($i));
							$chance_reussite = pourcent_reussite($craft, $difficulte);
							//On peut mettre la gemme
							$elt = new interf_bal_cont('li');
        			$opt->add($elt);
        			$lien = new interf_bal_smpl('a', $nom.' / slot niveau '.$objet_i['slot']);
        			$lien->set_attribut('href', 'inventaire.php?action=enchasse2&amp;key_slot='.$_GET['key_slot'].'&amp;key_slot2='.$i.'&amp;niveau='.$row['niveau'].$filtre_url);
        			$lien->set_attribut('onclick', 'return envoiInfo(this.href, \'information\');');
        			$elt->add($lien);
        			$elt->add( new interf_txt(' ') );
        			$elt->add( new interf_bal_smpl('span', $chance_reussite.'% de chance de réussite', false, 'xsmall') );
        			unset($elt, $lien);
						}
					}
				}
				$i++;
			}
			echo '
			</ul>';
		break;
		case 'enchasse2' :
			if($joueur->get_pa() >= 20)
			{
				$craft = $joueur->get_forge();
				if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
				if($joueur->get_accessoire() !== false)
				{
					$accessoire = $joueur->get_accessoire();
					if($accessoire->type == 'fabrication')
						$craft = round($craft * (1 + ($accessoire->effet / 100)));
				}				

				$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot2']));
				switch($_GET['niveau'])
				{
					case '1' :
						$difficulte = 10;
					break;
					case '2' :
						$difficulte = 30;
					break;
					case '3' :
						$difficulte = 100;
					break;
				}

				// Gemme de fabrique : augmente de effet % le craft
				if ($joueur->get_enchantement()!== false &&
						$joueur->is_enchantement('forge')) {
					$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
				}

				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				/*echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';*/
				$gemme_casse = false;
				if($craftd >= $diff)
				{
					//Craft réussi
          $princ->add( new interf_txt('Réussite !') );
          $princ->add( new interf_bal_smpl('br') );
					$objet['enchantement'] = $gemme['id_objet'];
					$objet['slot'] = 0;
					$gemme_casse = true;
					
					// Augmentation du compteur de l'achievement
					$achiev = $joueur->get_compteur('objets_slotted');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}
				else
				{
					//Craft échec
					//66% chance objet plus enchassable, 34% gemme disparait
					$rand = rand(1, 100);
					if($rand <= 34)
					{
            $princ->add( new interf_txt('Echec… la gemme a cassé…') );
            $princ->add( new interf_bal_smpl('br') );
						$gemme_casse = true;
					}
					else
					{
            $princ->add( new interf_txt('Echec… L\'objet ne pourra plus être enchassable…') );
            $princ->add( new interf_bal_smpl('br') );
						$objet['slot'] = 0;
					}
				}
				$augmentation = augmentation_competence('forge', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_forge($augmentation[0]);
					$joueur->sauver();
				}
				$objet_r = recompose_objet($objet);
				$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot2']);
				$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
				$joueur->set_pa($joueur->get_pa() - 20);
				$joueur->sauver();
				if($gemme_casse) $joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
			}
			else
			{
        $princ->add_message('Vous n\'avez pas assez de PA.', false);
			}
		break;
	}
	refresh_perso();
}

$perso = new perso($joueur_id);
$invent = $interf->creer_inventaire($perso, 'inventaire.php', $filtre);
$princ->add($invent);
$invent->set_contenu('perso', !$visu);
$invent->affiche_slots();

// Augmentation du compteur de l'achievement
$achiev = $joueur->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
