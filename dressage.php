<?php // -*- tab-width:2; mode: php -*-
/**
* @file dressage.php
* Dressage d'une créature
*/
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Récupération des informations du personnage
$perso = joueur::get_perso();
$perso->check_perso();
//Vérifie si le perso est mort
$interf_princ->verif_mort($perso);



$monstre = new map_monstre($_GET['id']);
$distance = $perso->calcule_distance($monstre->get_x(), $monstre->get_y());
if( $distance != 0 )
	security_block(URL_MANIPULATION, 'Vous êtes trop loin !');

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Dressage') );
$G_url->add('id', $_GET['id']);

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'fin':
	//Fin du dressage
	if( $perso->get_buff('dressage', 'fin') - time() > 86400)
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas fini de dresser ce monstre.');
    break;
	}
	$pet = new monstre($monstre->get_type());
	//On regarde si le joueur a assez dressé
	if($perso->get_buff('dressage', 'effet') < $pet->get_dressage())
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas réussi à dresser ce monstre.');
		$buff = $perso->get_buff('dressage');
		$buff->supprimer();
    break;
	}
	$p_suc = $cadre->add( new interf_bal_cont('p') );
	$p_suc->add( new interf_txt( 'Vous avez réussi à dresser ce monstre.') );
	//On le met dans son inventaire de monstre
	if( !$perso->add_pet($pet->get_id(), $monstre->get_hp(), $pet->get_energie() * 10) )
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez plus de place pour ce monstre.');
    break;
	}
	$buff = $perso->get_buff('dressage');
	$buff->supprimer();
	$monstre->supprimer();
	$p_suc->add( new interf_txt( '<br/>Le '.$pet->get_nom().' est maintenant votre créature.') );
	
	$drop = $pet->get_drops();
	//Drop d'un objet ?
	$drops = explode(';', $drop);
	if($drops[0] != '')
	{
		$count = count($drops);
		$i = 0;
		while($i < $count)
		{
			$share = explode('-', $drops[$i]);
			$objet = $share[0];
			$taux = ceil($share[1] / ($G_drop_rate*1.5));
			if($perso->get_race() == 'humain') $taux = $taux / 1.3;
			if($perso->is_buff('fouille_gibier')) $taux = $taux / (1 + ($perso->get_buff('fouille_gibier', 'effet') / 100));
			if ($taux < 2) $taux = 2; // Comme ca, pas de 100%
			$tirage = rand(1, floor($taux));

			if($tirage == 1 AND ($objet[0] == "h" OR $objet[0] == "l"))
			{
				$type_obj = '';
				//Nom de l'objet
				switch($objet[0])
				{
					case 'h' :
						$objet_nom = 'Objet non identifié';
						//Gemme aléatoire
						if($objet[1] == 'g')
						{
							//Niveau de la gemme
							$niveau_gemme = $objet[2];
							//Recherche des gemmes de ce niveau
							$ids = array();
							$requete = "SELECT id FROM gemme WHERE niveau = ".$niveau_gemme;
							$req_g = $db->query($requete);
							while($row = $db->read_row($req_g))
							{
								$ids[] = $row[0];
							}
							$num = rand(0, (count($ids) - 1));
							$objet = 'hg'.$ids[$num];
						}
					break;
					case 'l' :
						$id_objet = mb_substr($objet, 1);
						$requete = "SELECT nom FROM grimoire WHERE id = $id_objet";
						$req = $db->query($requete);
						$row = $db->read_row($req);
						$objet_nom = 'Grimoire : '.$row[0];
					break;
				}
				$p_loot = $cadre->add( new interf_bal_cont('p') );
				$p_suc->add( new interf_txt( 'Vous fouillez le corps du monstre et découvrez "'.$objet_nom.'" !<br />') );
				//Si le joueur a un groupe
				if($perso->get_groupe() > 0)
				{
					$groupe = new groupe($perso->get_groupe());
					$groupe->get_membre();
					//Répartition en fonction du mode de distribution
					switch($groupe->get_partage())
					{
					case 'r' :  //Aléatoire
						$p_suc->add( new interf_txt('Répartition des objets aléatoire.<br />') );
						$chance = count($groupe->membre);
						$aleat = rand(1, $chance);
						$gagnant = new perso($groupe->membre[($aleat - 1)]->get_id_joueur());
						break;
					case 't' :  //Par tour
						$p_suc->add( new interf_txt('Répartition des objets par tour.<br />') );
						$gagnant = new perso($groupe->get_prochain_loot());
						//Changement du prochain loot
						$j_g = $groupe->trouve_position_joueur($groupe->get_prochain_loot());
						//Si c'est pas le dernier alors suivant
						if((count($groupe->membre) - 1) != $j_g)
							$groupe->set_prochain_loot($groupe->membre[($j_g + 1)]->get_id_joueur());
						//Sinon premier
						else
							$groupe->set_prochain_loot($groupe->membre[0]->get_id_joueur());
						$groupe->sauver();
						break;
					case 'l' :  //Leader
						$p_suc->add( new interf_txt('Répartition des objets au leader.<br />') );
						$gagnant = new perso($groupe->get_id_leader());
						break;
					case 'k' :  //Celui qui trouve garde
						$p_suc->add( new interf_txt('Répartition des objets, celui qui trouve garde.<br />') );
						$gagnant = new perso($perso->get_id());
						break;
					}
					$p_suc->add( new interf_txt($gagnant->get_nom().' reçoit "'.$objet_nom.'"') );
				}
				else
					$gagnant = new perso($perso->get_id());
				//Insertion du loot dans le journal du gagnant
				$requete = "INSERT INTO journal VALUES(NULL, ".$gagnant->get_id().", 'loot', '', '', NOW(), '".sSQL($objet_nom)."', 0, ".$perso->get_x().", ".$perso->get_y().")";
				$db->query($requete);
				
				$gagnant->restack_objet();
				$gagnant->prend_objet($objet);
			}
			$i++;
		}
	}
	break;
default:
  // les monstres cachés ne sont pas dressables
	if ($monstre->get_affiche() == 'h')
  {
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas dresser ce monstre.');
    break;
  }
	//On vérifie si il a pas déjà le buff dressage sur lui
	if($perso->is_buff('dressage'))
	{
		if($perso->get_pa() < 10)
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA.');
	    break;
		}
		//On compare l'id du mob avec l'id du mob qu'il été en train de dresser, et si c'est le même on continue.
		if($perso->get_buff('dressage', 'effet2') != $_GET['id'])
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous tentez déjà de dresser un monstre.');
	    break;
		}
		//Si il reste moins d'un jour sur le buff, on propose au joueur de finir le dressage
		if(($perso->get_buff('dressage', 'fin') - time()) < 86400)
		{
			$cadre->add( new interf_lien('Finir le dressage', $G_url->get('action', 'fin'), false, 'btn btn-default') );
		}
		else
		{
			$debugs = 0;
			//Calcul du potentiel du joueur => Eventuellement rajouter les connaissances
			$potentiel = $perso->get_potentiel_dressage($monstre->get_type());
			$rand = rand(0, $potentiel);
			//On modifie le buff
			$buff = $perso->get_buff('dressage');
			$buff->set_effet($buff->get_effet() + $rand);
			$buff->sauver();
			$augmentation = augmentation_competence('dressage', $perso, 0.5);
			if ($augmentation[1] == 1)
			{
				$perso->set_dressage($augmentation[0]);
				$perso->recalcule_avancement();
			}
			$perso->set_pa($perso->get_pa() - 10);
			$perso->sauver();
			$cadre->add( new interf_bal_smpl('p', 'Vous apprenez quelques tours à votre animal. Il semble se familiariser de plus en plus à vous.') );
			$cadre->add( new interf_lien('Continuer le dressage', $G_url->get(), false, 'btn btn-default') );
		}
	}
	else //Sinon on commence le dressage sur ce monstre
	{
		//On vérifie qu'il a le niveau en dressage requis pour dresser un monstre de ce niveau
		if( !$perso->can_dresse($monstre) )
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez en dressage pour dresser ce monstre.');
	    break;
		}
		if($perso->nb_pet() >= $perso->get_comp('max_pet'))
		{
	    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas dresser plus de '.$perso->get_comp('max_pet').' créatures.');
	    break;
		}
		lance_buff('dressage', $perso->get_id(), 0, $_GET['id'], 172800, 'Dressage', 'On dresse le monstre', 'perso', 1, 0, 0, 0);
		$cadre->add( new interf_bal_smpl('h4', 'Dressage en cours') );
		$p = $cadre->add( new interf_bal_cont('p') );
		$p->add( new interf_txt('Le dressage a commencé.') );
		$p->add( new interf_bal_smpl('br') );
		$p->add( new interf_txt('Vous pouvez continuer le dressage (10 PA) pour augmenter vos chances de dresser cette créature.') );
		$p->add( new interf_bal_smpl('br') );
		$p->add( new interf_txt('Un jour après avoir commencer le dressage, vous pouvez décider de finir le dressage de cette créature et ainsi savoir si il est réussi ou non.') );
		$p->add( new interf_bal_smpl('br') );
		$p->add( new interf_txt('Attention, vous ne pouvez plus ni bouger ni attaquer lorsque vous êtes en train de dresser une créature.') );
		$p->add( new interf_bal_smpl('br') );
		$p->add( new interf_txt('De plus, si un joueur vous attaque, le dressage sera arrêté.') );
		$p->add( new interf_bal_smpl('br') );
		$cadre->add( new interf_bal_smpl('p', 'Vous pouvez à tout moment décider d\'arrêter le dressage, pour cela rendez vous dans la partie "dressage" du jeu.') );
		$cadre->add( new interf_lien('Continuer le dressage', $G_url->get(), false, 'btn btn-default') );
	}
	break;
}

interf_alerte::aff_enregistres($cadre);
interf_debug::aff_enregistres($cadre);
$interf_princ->maj_perso();
$interf_princ->maj_tooltips();

?>