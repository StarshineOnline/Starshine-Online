<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( ($perso->get_rang() != 6 && $perso->get_rang() != 1) || $perso->get_hp() <= 0 )
{
	/// @todo logguer triche
	exit;
}

$cadre = $G_interf->creer_royaume();
$carte = $cadre->set_gestion( $G_interf->creer_carte_monde('carte') );
$carte->aff_options();
$carte->aff_svg();
$carte->aff_habitants($royaume);
$carte->aff_batiments($royaume);

$cadre->maj_tooltips();


?>