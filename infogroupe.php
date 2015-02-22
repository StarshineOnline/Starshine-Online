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
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
$action = array_key_exists('action', $_GET) ? $_GET['action'] : 'infos';
$groupe = new groupe( $perso->get_groupe() );

if( $ajax == 2 )
{
	switch($action)
	{
	case 'infos':
		$interf_princ->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
		exit;
	case 'batailles':
		$interf_princ->add( $G_interf->creer_batailles('batailles_groupe', $groupe) );
		exit;
	}
}
else if( $action == 'carte' )
{
	$bataille = new bataille(sSQL($_GET['bataille']));
	$interf_princ->set_dialogue( $G_interf->creer_carte_bataille($bataille) );
	if($ajax == 1)
	{
		$interf_princ->maj_tooltips();
		exit;
	}
	$action = 'batailles';
}

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Groupe') );
$onglets = $cadre->add( new interf_onglets('onglets_groupe', 'groupe') );


$onglets->add_onglet('Infos groupe', 'infogroupe.php?action=infos&ajax=2', 'ongl_infos', false, $action=='infos' || $action == 'modifier_infos' || $action == 'suppr_invit');
$onglets->add_onglet('Batailles', 'infogroupe.php?action=batailles&ajax=2', 'ongl_batailles', 'invent', $action=='batailles' || $action == 'accepter');

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
case 'accepter':
	$repere = new bataille_groupe_repere($_GET['id']);
	$repere->accepte();
	// Augmentation du compteur de l'achievement
	foreach($groupe->get_membre_joueur() as $membre)
	{
		$achiev = $membre->get_compteur('bataille');
		$achiev->set_compteur($achiev->get_compteur() + 1);
		$achiev->sauver();
	}
case 'batailles':
	$onglets->get_onglet('ongl_batailles')->add( $G_interf->creer_batailles('batailles_groupe', $groupe) );
	break;
}

$interf_princ->maj_tooltips();

