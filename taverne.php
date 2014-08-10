<?php // -*- mode: php; tab-width:2 -*-
/**
* @file qg.php
* Quartier général
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
if( $action == 'infos' )
{
	$service = new taverne($_GET['id']);
	///TODO: passer par $G_interf
  new interf_infos_popover($service->get_noms_infos(), $service->get_valeurs_infos());
  exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///TODO: à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// TODO: logguer triche
$case = new map_case(array('x' => $perso->get_x(), 'y' => $perso->get_y()));
if( !$case->is_ville(true, 'taverne') )
	exit();

// On vérifie la diplomatie
/// TODO: logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// TODO: logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

switch($action)
{
case 'achat':
	/// TODO : à vérifier
	$requete = "SELECT * FROM taverne WHERE id = ".sSQL($_GET['id'], SSQL_INTEGER);
	$req_taverne = $db->query($requete);
	$row_taverne = $db->read_array($req_taverne);
	$taxe = ceil($row_taverne['star'] * $R->get_taxe_diplo($perso->get_race()) / 100);
	$cout = $row_taverne['star'] + $taxe;
	if ($perso->get_star() >= $cout)
	{
		if($perso->get_pa() >= $row_taverne['pa'])
		{
			$valid = true;
			$bloque_regen = false;
			if($row_taverne['pute'] == 1)
			{
				$debuff = false;
				$buff = false;
				$honneur_need = $row_taverne['honneur'] + (($row_taverne['honneur_pc'] * $perso->get_honneur()) / 100);
				if($perso->get_honneur() >= $honneur_need)
				{
					$perso->set_honneur($perso->get_honneur() - $honneur_need);
				}
				else $perso->set_honneur(0);
			
        $texte .= pute_effets($perso, $honneur_need);

      }
			if($valid)
			{
				$perso->set_star($perso->get_star() - $cout);
				$perso->set_pa($perso->get_pa() - $row_taverne['pa']);
				if(!$bloque_regen)
				{
					$perso->set_hp($perso->get_hp() + $row_taverne['hp'] + floor($row_taverne['hp_pc'] * $perso->get_hp_maximum() / 100));
					if ($perso->get_hp() > $perso->get_hp_maximum()) $perso->set_hp(floor($perso->get_hp_maximum()));
					$perso->set_mp($perso->get_mp() + $row_taverne['mp'] + floor($row_taverne['mp_pc'] * $perso->get_mp_maximum() / 100));
					if ($perso->get_mp() > $perso->get_mp_maximum()) $perso->set_mp(floor($perso->get_mp_maximum()));
				}
				$perso->sauver();
				//Récupération de la taxe
				if($taxe > 0)
				{
					$R->add_star_taxe($taxe, 'taverne');
					$R->sauver();
				}
				/// TODO: séparer le texte & vérifier les dépassement en hauteur
				interf_alerte::enregistre(interf_alerte::msg_succes, 'La taverne vous remercie de votre achat !<br />'.$texte);
				$interf_princ->maj_perso();
				
				if($row_taverne['pa'] == 12 AND $row_taverne['pute'] == 0) // Equivaut à "c'est un repas"
				{
					// Augmentation du compteur de l'achievement
					$achiev = $perso->get_compteur('stars_en_repas');
					$achiev->set_compteur($achiev->get_compteur() + $cout);
					$achiev->sauver();
					
					// Augmentation du compteur de l'achievement
					$achiev = $perso->get_compteur('nbr_repas');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}
			}
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA');
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de Stars');
	break;
}
$interf_princ->set_gauche( $G_interf->creer_taverne($R, $case) );



		/*Bien le bonjour ami voyageur !<br />
		//Affichage des quêtes
		if($R->get_nom() != 'Neutre') $return = affiche_quetes('taverne', $perso);
		if($return[1] > 0 AND !array_key_exists('fort', $_GET))
		{
			echo 'Voici quelques petits services que j\'ai à vous proposer :';
			echo $return[0];
		}
		*/
	
?>