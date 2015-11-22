<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
if( $perso->get_rang() != 6 && $perso->get_rang() != 1 )
{
	/// @todo logguer triche
	exit;
}
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);

if( array_key_exists('action', $_GET) && $perso->get_rang() == 6 )
{
	switch( $_GET['action'] )
	{
	case 'suppr_eco':
		$royaume->set_ministre_economie(0);
		$royaume->sauver();
		break;
	case 'suppr_mil':
		$royaume->set_ministre_militaire(0);
		$royaume->sauver();
		break;
	}
}

$cadre = $G_interf->creer_royaume();
$cadre->set_gestion( $G_interf->creer_gestion_royaume($royaume, $perso->get_rang() != 6) );
$cadre->maj_tooltips();
