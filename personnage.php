<?php // -*- php -*-
/**
 * @file personnage.php
 * Feuille de personnage
 */ 
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

if( array_key_exists('id', $_GET) )
{
	$perso = new perso( $_GET['id'] );
	$bonus = recup_bonus($_GET['id']);
	if(!array_key_exists(23, $bonus) || !check_affiche_bonus($bonus[23], joueur::get_perso(), $perso))
		security_check('Vous ne pouvez accéder à cette feuille de persnnage !');
	$G_url->add('id', $_GET['id']);
	$actions = false;
}
else
{
	$actions = true;
	$perso = joueur::get_perso();
}
	
$perso->check_perso();


$interf_princ = $G_interf->creer_jeu();
$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : null;


if( array_key_exists('action', $_GET) && $_GET['action'] == 'teleport'  && $joueur->get_teleport_roi() != 'true' )
{
	$perso->set_x($Trace[$perso->get_race()]['spawn_x']);
	$perso->set_y($Trace[$perso->get_race()]['spawn_y']);
	$perso->set_teleport_roi('true');
	$perso->sauver();
	$interf_princ->recharger_interface();
}

switch($onglet)
{
case 'carac':
	$interf_princ->add( $G_interf->creer_fiche_perso_carac($perso, $actions) );
	break;
case 'apt':
	$interf_princ->add( $G_interf->creer_fiche_perso_apt($perso) );
	break;
case 'stat':
	$interf_princ->add( $G_interf->creer_fiche_perso_stat($perso) );
	break;
case 'achiev':
	//On debloque les achievements uniques
	$perso->unlock_achiev('race_'.$perso->get_race());
	$perso->unlock_achiev('classe_'.$perso->get_classe());
	$interf_princ->add( $G_interf->creer_fiche_perso_achiev($perso) );
	break;
default:
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Nom : '.$perso->get_nom()) );
	$cadre->add( $G_interf->creer_fiche_perso($perso, $actions) );
}
$interf_princ->maj_tooltips();

			
?>
