<?php // -*- mode: php; tab-width:2 -*-
/**
* @file bureau_quete.php
* Bureau des quêtes
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

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
if( !$case->is_ville(true, 'quete') )
	exit();

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$type = array_key_exists('type', $_GET) ? $_GET['type'] : 'autre';

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$interf_princ->add( $G_interf->creer_tbl_quetes($R, 'bureau_quete', $type) );
	exit;
}

switch($action)
{
case 'description':
	$quete = new quete( sSQL($_GET['id']) );
	$interf_princ->set_gauche( $G_interf->creer_bureau_quete_descr($quete, $R) );
	$interf_princ->maj_tooltips();
	exit;
case 'prendre':
	if($perso->prend_quete($_GET['id']) )
	{
		/// @todo ajouter un message : 'Merci de votre aide !'
	}
	break;
case 'prendre_tout' :
	if($perso->prend_quete_tout($R) )
	{
		/// @todo ajouter un message : nombre de quêtes prises
	}
	break;
}

//Affichage des quêtes
$interf_princ->set_gauche( $G_interf->creer_bureau_quete($R, $type) );
$interf_princ->maj_tooltips();
