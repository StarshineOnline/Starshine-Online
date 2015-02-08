	<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');
include_once(root.'inc/ressource.inc.php');
include_once(root.'interface/interf_quete.class.php');


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
	$cadre->set_dialogue( new interf_quete($_GET['id'], $royaume) );
	if( array_key_exists('ajax', $_GET) )
		exit;
	break;
case 'achat':
	$quete = new quete( sSQL($_GET['id']) );
	$quete->achat($royaume);
	break;
}

$cadre->set_gestion( $G_interf->creer_quete_royaume($royaume) );

?>
