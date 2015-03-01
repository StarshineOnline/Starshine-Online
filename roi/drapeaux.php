<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 || $royaume->is_raz() || !verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	/// @todo logguer triche
	exit;
}

// On fait éclore les drapeaux arrivés à terme
$case = new map_case();
$case->check_case('all');


$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$mag_factor = array_key_exists('mag_factor', $_GET) ? $_GET['mag_factor'] : 1;
$mleft = array_key_exists('mleft', $_GET) ? $_GET['mleft'] : 0;
$mtop = array_key_exists('mtop', $_GET) ? $_GET['mtop'] : 0;
$cadre = $G_interf->creer_royaume();

if( !$perso->is_buff('debuff_rvr') )
{
	switch($action)
	{
	case 'pose':
		if( pose_drapeau_roi($_REQUEST['x'], $_REQUEST['y']) )
			interf_alerte::enregistre(interf_alerte::msg_succes, 'Drapeau posé en '.$_REQUEST['x'].', '.$_REQUEST['y']);
		break;
	case 'tout':
		$nb = pose_drapeau_roi_all();
		if ($nb !== false)
			interf_alerte::enregistre(interf_alerte::msg_succes, $nb.' drapeaux posés.');
		break;
	}
}
else if( $action )
{
	interf_alerte::enregistre(interf_alerte::msg_erreur, 'RvR impossible pendant la trêve');
}

$cadre->set_gestion($G_interf->creer_drapeaux($royaume, $mag_factor, $mleft, $mtop));
$cadre->maj_tooltips();
