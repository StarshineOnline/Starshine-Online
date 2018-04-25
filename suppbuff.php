<?php
/**
 * @file suppbuff.php
 * Suppression d'un buff 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$buff = new buff($_GET['id']);

/// @todo loguer triche
if( $buff->get_debuff() )
{
	exit();
}
$ok = $_SESSION['ID'] == $buff->get_id_perso();
if( !$ok )
{
	$perso = joueur::get_perso();
	$groupe = $perso->get_id_groupe();
	if($groupe)
	{
		$cible = new perso($buff->get_id_perso());
		$ok = $groupe == $cible->get_id_groupe();
	}
}
if( $ok )
{
	$buff->supprimer();
	$interf_princ = $G_interf->creer_jeu();
	$interf_princ->maj_perso();
}

?>