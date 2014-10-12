<?php
/**
* @file infogroupe.php
* Informations du groupe et accès au bataille pour le groupe
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();

$perso = joueur::get_perso();


// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Groupe') );

$onglets = $cadre->add( new interf_onglets('onglets_groupe', 'groupe') );

$action = array_key_exists('action', $_GET) ? $_GET['action'] : 'infos';

$onglets->add_onglet('Infos groupe', 'infogroupe.php?action=infos&ajax=2', 'ongl_infos', false, $action=='infos');
//$onglets->add_onglet('Batailles', 'infogroupe.php?action=batailles&ajax=2', 'ongl_batailles', $action=='batailles');

$groupe = new groupe( $perso->get_groupe() );
switch($action)
{
case 'modifier_infos':
	$groupe->set_nom(sSQL($_GET['nom']));
	$groupe->set_partage(sSQL($_GET['partage']));
	$groupe->sauver();
	/// @todo passer à l'objet
	$requete = "UPDATE groupe_joueur SET leader = 'n' WHERE id_groupe = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'y' WHERE id_joueur = ".sSQL($_GET['leader']);
	$db->query($requete);
case 'infos':
	$onglets->get_onglet('ongl_infos')->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
	break;
case 'suppr_invit':
	/// @todo passer à l'objet
	$requete = "DELETE FROM invitation WHERE ID = ".sSQL($_GET['id']);
	$db->query($requete);
	$onglets->get_onglet('ongl_infos')->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
	break;
}

$interf_princ->maj_tooltips();

