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
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

switch($action)
{
case 'voir':
	$quete = new quete($_GET['id']);
	$cadre->set_dialogue( $G_interf->creer_infos_quete($quete, $royaume) );
	if( array_key_exists('ajax', $_GET) )
		exit;
	break;
case 'achat':
	/// @todo vérifier si déja acheter et logguer triche
	$quete = new quete( sSQL($_GET['id']) );
	$quete->achat($royaume);
	break;
}

$cadre->set_gestion( $G_interf->creer_quete_royaume($royaume) );

?>
