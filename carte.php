<?php
if (file_exists('root.php'))
  include_once('root.php');
  
include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
$carte = array_key_exists('carte', $_GET) ? $_GET['carte'] : 'general';

if( $_GET['ajax'] == 2 )
	$interf = &$interf_princ;
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Cartes', true) );
	$onglets = $dlg->add( new interf_onglets('ongl_cartes', 'cartes') );
	$onglets->add_onglet('Générale', 'carte.php?carte=general&ajax=2', 'ongl_general', false, $carte=='general');
	$onglets->add_onglet('Royaumes', 'carte.php?carte=royaume&ajax=2', 'ongl_royaume', false, $carte=='royaume');
	/*$onglets->add_onglet('Carte 3D', 'carte.php?carte=3d&ajax=2', 'ongl_3d', false, $carte=='3d');
	$onglets->add_onglet('Carte 3D des Royaumes', 'carte.php?carte=royaume_3d&ajax=2', 'ongl_royaume_3d', false, $carte=='royaume_3d');*/
	$onglets->add_onglet('Densité des monstres', 'carte.php?carte=monstres&ajax=2', 'ongl_monstres', false, $carte=='monstres');
	$interf = &$onglets->get_onglet('ongl_'.$carte);
}

$div = $interf->add( new interf_bal_cont('div', false, 'invent') );
switch( $carte )
{
case 'general':
	$carte = $div->add( $G_interf->creer_carte_monde() );
	$carte->aff_options();
	$carte->aff_svg();
	$carte->aff_groupe( joueur::get_perso()->get_id_groupe() );
	if( array_key_exists('x', $_GET) && array_key_exists('y', $_GET) )
		$carte->aff_pos($_GET['x'], $_GET['y']);
	break;
case 'royaume':
	$div->add( new interf_img('image/carte_royaume.png', 'Carte des royaumes') );
	break;
case '3d':
	$div->add( new interf_img('image/carte3d-4.png', 'Carte 3D') );
	break;
case 'royaume_3d':
	$div->add( new interf_img('image/carte3d-royaumes.png', 'Carte 3D des royaumes') );
	break;
case 'monstres':
	$div->add( new interf_img('image/carte_densite_mob.png', 'Carte 3D') );
	break;
}
$interf_princ->maj_tooltips();
?>