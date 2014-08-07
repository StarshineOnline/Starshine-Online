<?php // -*- mode: php; tab-width:2 -*-
/**
* @file ecurie.php
* Ecuries
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
///TODO: à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// TODO: logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// TODO: logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// TODO: logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
/// TODO: ajouter des vérifications et des messages
switch($action)
{
case 'rez':
	$pet = new pet($_GET['id']);
	if($pet->get_id_joueur() == $perso->get_id())
	{
		$taxe = ceil($pet->get_cout_rez() * $R->get_taxe_diplo($perso->get_race()) / 100);
		$cout = $pet->get_cout_rez() + $taxe;
		if($perso->get_star() >= $cout)
		{
			$pet->get_monstre();
			$pet->set_hp($pet->monstre->get_hp());
			$pet->set_mp($pet->get_mp_max());
			$pet->sauver();
			$perso->add_star( -$cout );
			$perso->sauver();
			$R->add_star_taxe($taxe, 'ecurie');
			$R->sauver();
			$interf_princ->maj_perso();
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars !');
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette créature ne vous appartient pas !');
	break;
case 'soin':
	$pet = new pet($_GET['id']);
	if($pet->get_id_joueur() == $perso->get_id())
	{
		if($pet->get_hp() > 0)
		{
			$taxe = ceil($pet->get_cout_soin() * $R->get_taxe_diplo($perso->get_race()) / 100);
			$cout = $pet->get_cout_soin() + $taxe;
			if($perso->get_star() >= $cout)
			{
				$pet->get_monstre();
				$pet->set_hp(ceil($pet->get_hp() + 0.1 * $pet->monstre->get_hp()));
				if($pet->get_hp() > $pet->monstre->get_hp()) $pet->set_hp($pet->monstre->get_hp());
				$pet->set_mp(ceil($pet->get_mp() + 0.1 * $pet->get_mp_max()));
				if($pet->get_mp() > $pet->get_mp_max()) $pet->set_mp($pet->get_mp_max());
				$pet->sauver();
				$perso->add_star( -$cout );
				$perso->sauver();
				$R->add_star_taxe($taxe, 'ecurie');
				$R->sauver();
				$interf_princ->maj_perso();
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars !');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette créature peut uniquement être réssucitée.');
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette créature ne vous appartient pas !');
	break;
case 'reprendre':
	$perso->pet_from_ecurie($_GET['id']);
	break;
case 'deposer_ville':
	$pet = new pet($_GET['id']);
	$taxe = ceil($pet->get_cout_depot() * $R->get_taxe_diplo($perso->get_race()) / 100);
	if($perso->pet_to_ecurie($_GET['id'], 1, 10, $taxe))
	{
		$R->add_star_taxe($taxe, 'ecurie');
		$R->sauver();
	}
	$interf_princ->maj_perso();
	break;
case 'deposer_terrain':
	/// TODO: passer par des objets & loguer triche
	$requete = 'SELECT b.effet FROM terrain AS t INNER JOIN terrain_construction AS c ON c.id_terrain = t.id INNER JOIN terrain_batiment AS b ON c.id_batiment = b.id WHERE b.type = "ecurie" AND t.id_joueur = '.$this->perso->get_id();
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if( $row )
		$perso->pet_to_ecurie($_GET['id'], 2, $row['effet']);
	break;
}
$interf_princ->set_gauche( $G_interf->creer_ecurie($R) );
$interf_princ->maj_tooltips();


?>