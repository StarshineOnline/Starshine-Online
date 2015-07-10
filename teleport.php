<?php // -*- mode: php; tab-width:2 -*-
/**
* @file teleport.php
* Pierre de téléportation
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///@todo à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// @todo logguer triche
$case = new map_case(array('x' => $perso->get_x(), 'y' => $perso->get_y()));
if( !$case->is_ville(true, 'taverne') )
	exit();

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
switch($action)
{
case 'tp':
	$cout = false;
	switch($_GET['type'])
	{
	case 'ville':
		/// @todo remplacer cette base par autre chose
		$requete = 'SELECT * FROM teleport WHERE ID = '.sSQL($_GET['id']);
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$P_distance = $perso->calcule_distance($row['posx'], $row['posy']);
		if($row['cout'] > 0)
		{
			$cout = $row['cout'];
			$taxe = 0;
		}
		else
		{
			$cout = ($P_distance * 10);
			$taxe = ceil($cout * $R->get_taxe_diplo($perso->get_race()) / 100);
			$cout = $cout + $taxe;
		}
		$x = $row['posx'];
		$y = $row['posy'];
		break;
	case 'bourg':
		/// @todo à revoir
		$W_distance = detection_distance($W_case, $_SESSION['position']);
		if($W_distance != 0)
		{
			/// @todo passer par un objet
			$bourg = new construction(sSQL($_GET['id']));
			$distance = $perso->calcule_distance($bourg);
			$cout = $distance * 7;
			if($perso->is_buff('buff_cout_tp'))
				$cout = round($cout / (1 + (($perso->get_buff('buff_cout_tp', 'effet')) / 100)));
			if($perso->is_buff('debuff_cout_tp'))
				$cout = round($cout * (1 + (($perso->get_buff('debuff_cout_tp', 'effet')) / 100)));
			$taxe = ceil($cout * $R->get_taxe_diplo($perso->get_race()) / 100);
			$cout = $cout + $taxe;
			$x = $bourg->get_x();
			$y = $bourg->get_y();
		}
		break;
	}
	if( $cout && $perso->get_star() >= $cout && $perso->get_pa() >= 5 )
	{
		$perso->set_x($x);
		$perso->set_y($y);
		$perso->add_star( -$cout );
		$perso->add_pa( -5 );
		$perso->sauver();
		$tp = true;
		//Récupération de la taxe
		if($taxe > 0)
		{
			$R->add_star_taxe($taxe, 'teleport');
			$R->sauver();
		}
		$interf_princ->recharger_interface();
		// Augmentation du compteur de l'achievement
		$achiev = $perso->get_compteur('nbr_tp');
		$achiev->set_compteur($achiev->get_compteur() + 1);
		$achiev->sauver();
	}
	else 
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars ou de PA !');
	break;
}
$interf_princ->set_gauche( $G_interf->creer_tp($R, $case) );

	
?>