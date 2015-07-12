<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();

/// @todo à améliorer
$perso = joueur::get_perso();
$groupe = new groupe($perso->get_groupe());

$id = false;
if( array_key_exists('id', $_GET) )
{
	$autre = new perso($_GET['id']);
	if( $autre->get_groupe() == $groupe->get_id() && $perso->get_id() == $groupe->get_leader() )
	{
		// On debloque l'achievement
		$autre->unlock_achiev('etre_expulse');
		$perso->unlock_achiev('expulser');
		$id = $_GET['id'];
	}
}
else
	$id = $perso->get_perso();

if( $id )
	degroup($id, $groupe->get_id())
	
$interf_princ->maj_perso();

