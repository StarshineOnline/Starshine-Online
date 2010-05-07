<?php
if (file_exists('root.php'))
  include_once('root.php');

require('class/map.class.php');
{//-- Initialisation
	$MAP = Array();
}
{//-- Récupération de la position X, Y du joueur et de son level pour la detection des monstres.
	$RqXY = $db->query("SELECT x, y, level, race FROM perso WHERE ID=".$joueur->get_id().";");
	$objXY = $db->read_object($RqXY);
	$x = $objXY->x;
	$y = $objXY->y;
	$level = $objXY->level;
}
$map = new map($x, $y);
$arene = $joueur->in_arene();
if(!empty($arene)) $map->set_arene(true);
elseif ($joueur->get_option('desactive_atm_all') != 1 &&
				isset($G_use_atmosphere) && $G_use_atmosphere)
{ // Calcul de l'état atmosphérique si on a pas désactivé la fonction
	if ($joueur->get_option('desactive_atm') == 0) {
// 		$q = "select * from map_zone where x1 <= $x and y1 <= $y and ".
// 			"x2 >= $x and y2 >= $y limit 1";
// 		$req = $db->query($q);
// 		if ($row = $db->read_object($req)) {
// 			$atmosphere_type = $row->type;
// 		}
// 		else {
// 			$atmosphere_type = 'vide';
// 		}
		$map->compute_atmosphere();
		//$map->set_atmosphere_decal($x, $y);
	} else {
		$atmosphere_type = 'vide';
	}
}
$map->get_pnj();
$map->get_joueur($objXY->race);
$map->get_drapeau();
$map->get_batiment();
if (isset($atmosphere_type) && isset($G_use_atmosphere) && $G_use_atmosphere) {
	$atmosphere_moment = strtolower(moment_jour());
	//$atmosphere_moment = 'soir';
  //echo "load ${atmosphere_type}-${atmosphere_moment}";
	$map->set_atmosphere($atmosphere_type.'-'.$atmosphere_moment);
	//$map->set_atmosphere_decal($x, $y);
}
$map->onclick_status = true;
if(isset($_GET['show_only'])) $map->change_show_only($_GET['show_only']);
$map->get_monstre($level);

if(isset($_GET['cache_monstre'])) $map->change_cache_monstre();
if(isset($_GET['affiche_royaume'])) $map->change_affiche_royaume();

$map->affiche();
?>
