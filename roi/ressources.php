<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');
include_once(root.'inc/ressource.inc.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $royaume->get_ministre_economie() != $perso->get_id() )
{
	/// @todo logguer triche
	exit;
}

$cadre = $G_interf->creer_royaume();

function ressource($nom)
{
	$ressource['Pierre'] = 18;
	$ressource['Bois'] = 19;
	$ressource['Eau'] = 20;
	$ressource['Sable'] = 21;
	$ressource['Charbon'] = 22;
	$ressource['Essence Magique'] = 23;
	$ressource['Star'] = 24;
	$ressource['Nourriture'] = 25;
	return $ressource[$nom];
}

if(array_key_exists('ress', $_GET))
{
	include_once(root.'interface/interf_ressources.class.php');
	$cadre->set_dialogue( new interf_ressource_graph($royaume, $_GET['ress']) );
}
else
{
	$cadre->set_gestion( $G_interf->creer_ressources($royaume) );
}
$cadre->maj_tooltips();
?>
