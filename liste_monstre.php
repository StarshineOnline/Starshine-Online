<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_princ();

$terrain =  array_key_exists('terrain', $_GET) ? $_GET['terrain'] : 'plaine';

if( $_GET['ajax'] == 2 )
{
	$interf_princ->add( $G_interf->creer_liste_monstres($terrain) );
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Bestiaire', true, 'dlg_bestiaire') );
	//$dlg->add( new interf_bal_smpl('div', 'truc') );
	$dlg->add( $G_interf->creer_bestiaire($terrain) );
}
?>