<?php
/**
 * @file message_acceuil.php
 * Afichage du message d'acceuil 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
$interf_princ->set_droite( $G_interf->creer_accueil() );

?>
