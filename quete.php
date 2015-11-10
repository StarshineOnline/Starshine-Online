<?php
if (file_exists('root.php'))
	include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();
//Récupération des informations du personnage
$perso = joueur::get_perso();
$perso->check_perso();

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$G_url->add('terrain', $_GET['type']);
	$interf_princ->add( $G_interf->creer_quetes_terrain($perso, $_GET['type']) );
	exit;
}

if( array_key_exists('terrain', $_GET) )
	$terrain = $_GET['terrain'];
else
{
	$case = new map_case($perso->get_pos());
	$terrain = type_terrain($case->get_info())[0];
	switch( $terrain )
	{
	case 'plaine':
	case 'foret':
	case 'desert':
	case 'neige':
	case 'montagne':
	case 'marais':
		break;
	case 'terre_maudite':
		$terrain = 'marais';
		break;
	default:
		$terrain = 'autre';
	}
}
$G_url->add('terrain', $terrain);

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Quêtes') );
$G_url->add('id', $_GET['id']);

$type = array_key_exists('type', $_GET) ? $_GET['type'] : $terrain;
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'abandonner':
	/// @todo à améliorer
	$quete_perso = new quete_perso($_GET['id']);
	$quete_perso->supprimer();
	break;
}

$cadre->add( $G_interf->creer_quetes($perso, $type) );
$interf_princ->maj_tooltips();
