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
$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : 'balance';

$cadre = $G_interf->creer_royaume();
if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
		switch($onglet)
		{
		case 'balance':
			$cadre->add( $G_interf->creer_balance_hier($royaume) );
			break;
		case 'recettes':
			$cadre->add( $G_interf->creer_recettes($royaume) );
			break;
		case 'evolution':
			$cadre->add( $G_interf->creer_evol_gains($royaume) );
			break;
		case 'repartition':
			$cadre->add( $G_interf->creer_repart_gains($royaume) );
			break;
		}
}
else
{
	$cadre->set_gestion( $G_interf->creer_entretien($royaume, $onglet) );
	$cadre->maj_tooltips();
}


//echo 'Niveau de référence pour l\'entretien: '.royaume::get_niveau_ref_actifs();
?>