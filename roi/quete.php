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

if(array_key_exists('q', $_GET))
{
	include_once(root.'interface/interf_quete.class.php');
	$cadre->set_dialogue( new interf_quete($_GET['q'], $royaume) );
}
elseif(array_key_exists('a', $_GET))
{
	include_once(root.'interface/interf_quete.class.php');
	$cadre->set_dialogue( new interf_achat_quete(, $royaume) );
}
else
{
	$cadre->set_gestion( $G_interf->creer_listequete_royaume($royaume) );
}
?>
