<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $perso->get_rang() != 1 )
{
	/// @todo logguer triche
	exit;
}

$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : 'royaume';
$cadre = $G_interf->creer_royaume();

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'infos':
  include_once(root.'interface/interf_roi_groupe.class.php');
	$cadre->set_dialogue( new interf_roi_groupe_info( new groupe($_GET['id']) ) );
	if( array_key_exists('ajax', $_GET) )
		exit;
	break;
}



if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
		switch($onglet)
		{
		case 'royaume':
			$cadre->add( $G_interf->creer_roi_groupe_roy($royaume) );
			break;
		case 'etrangers':
			$cadre->add( $G_interf->creer_roi_groupe_ext($royaume) );
			break;
		case 'sans':
			$cadre->add( $G_interf->creer_roi_groupe_sans($royaume) );
			break;
		}
}
else
{
	$cadre->set_gestion( $G_interf->creer_roi_groupe($royaume, $onglet) );
	$cadre->maj_tooltips();
}


?>