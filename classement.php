<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();
  
$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 'royaumes';
$type = array_key_exists('type', $_GET) ? $_GET['type'] : null;

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$G_url->add('categorie', $categorie);
	switch($categorie)
	{
	case 'royaumes':
		$interf_princ->add( $G_interf->creer_classement_royaumes($type) );
		break;
	case 'groupes':
		$interf_princ->add( $G_interf->creer_classement_groupes($type) );
		break;
	case 'perso_race':
		$interf_princ->add( $G_interf->creer_classement_perso_race($type) );
		break;
	case 'perso_tous':
		$interf_princ->add( $G_interf->creer_classement_perso_tous($type) );
		break;
	}
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Classements', true, 'dlg_class') );
	$dlg->add( $G_interf->creer_classements($categorie, $type) );
}