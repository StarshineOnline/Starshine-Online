<?php
/**
* @file moment_jour.php
* Prochains moment de la journée.
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();


// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Calendrier') );

$cadre->add( $G_interf->creer_calendrier() );


?>