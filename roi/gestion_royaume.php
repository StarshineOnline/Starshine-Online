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

$cadre = $G_interf->creer_royaume();


$cadre->set_gestion( $G_interf->creer_gestion_royaume($royaume, $perso->get_rang() != 6) );
$cadre->maj_tooltips();




?>

