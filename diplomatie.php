<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_princ();
$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Diplomatie', true, 'dlg_diplo') );
$dlg->add( $G_interf->creer_diplomatie() );

?>