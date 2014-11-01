<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
  
$stat = array_key_exists('stat', $_GET) ? $_GET['stat'] : 'stat_lvl';

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
	$interf_princ->add( new interf_img('image/'.$stat.'.png') );
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Statistiques', true, 'dlg_stats') );
	$dlg->add( $G_interf->creer_stats($stat) );
}