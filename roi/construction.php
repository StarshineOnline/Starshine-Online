<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$constr = new construction($batiment['id_batiment'])
		$lieu = $bourg->has_bonus('royaume') && !$constr->is_buff('assaut');
	}
}
if( ($perso->get_rang() != 6 && $perso->get_rang() != 1) || !$lieu || $perso->get_hp() <= 0 )
{
	/// @todo logguer triche
	exit;
}

$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : 'batiments';
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;

$cadre = $G_interf->creer_royaume();

switch($action)
{
case 'renommer':
	$constr = new construction($_GET['id']);
  include_once(root.'interface/interf_bat_drap.class.php');
	$cadre->set_dialogue( new interf_batiment_nom($constr) );
	if( $ajax )
		exit;
	break;
case 'modif_nom':
	$constr = new construction($_GET['id']);
	$constr->set_nom( sSQL($_GET['nom'], SSQL_STRING) );
	$constr->sauver();
	break;
case 'suppr':
	$constr = new construction($_GET['id']);
	if( !$constr->get_buff('assiege') )
	{
		$constr->supprimer();
		journal_royaume::ecrire_perso('suppr_batiment', $constr->get_def(), $constr->get_nom(), $constr->get_id(), $constr->get_x(), $constr->get_y());
	}
	break;
}

if( $ajax == 2 )
{
		switch($onglet)
		{			
		case 'invasions':
			$cadre->add( $G_interf->creer_bd_invasions($royaume) );
			break;
		case 'constructions':
			$cadre->add( $G_interf->creer_bd_constructions($royaume) );
			break;
		case 'ads':
			$cadre->add( $G_interf->creer_bd_ads($royaume) );
			break;
		case 'drapeaux':
			$cadre->add( $G_interf->creer_bd_drapeaux($royaume) );
			break;
		case 'batiments':
			$cadre->add( $G_interf->creer_bd_batiments($royaume) );
			break;
		case 'depot':
			$cadre->add( $G_interf->creer_bd_depot($royaume) );
			break;
		}
}
else if( $ajax == 1 && array_key_exists('x', $_GET) && array_key_exists('y', $_GET) )
{
	$cadre->add_section('minicarte_'.$onglet, new interf_carte($_GET['x'], $_GET['y'], interf_carte::aff_gestion, 5, 'carte_'.$onglet));
}
else
{
	$cont = $cadre->set_gestion( new interf_bal_cont('div') );
	interf_alerte::aff_enregistres($cont);
	$cont->add( $G_interf->creer_bat_drap($royaume, $onglet, $_GET['x'], $_GET['y']) );
	$cadre->maj_tooltips();
}

?>
