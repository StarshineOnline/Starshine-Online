<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

require_once('class/map.class.php');
{//-- Initialisation
	$MAP = Array();
}
{//-- Récupération de la position X, Y du joueur et de son level pour la detection des monstres.
	$RqXY = $db->query("SELECT x, y, level, race FROM perso WHERE ID=".$_SESSION["ID"].";");
	$objXY = $db->read_object($RqXY);
	$x = $objXY->x;
	$y = $objXY->y;
	$level = $objXY->level;
}

$visu = 3;
$map = new map($x, $y, $visu, '', true);
//if(is_array($MAPTAB[$x_map][$y_map])) { $class_map = "decor tex".$MAPTAB[$x_map][$y_map]["decor"]; } else { $class_map = "texblack"; };

if (is_object($joueur) && $joueur->in_arene()) {
	$map->arene = true;
}
elseif(is_object($joueur) && $joueur->get_y() > 190) { $map->set_dungeon_layer(true); }
$map->get_pnj();
$map->get_joueur($objXY->race);
$map->get_monstre($level, true, $joueur);
$map->get_drapeau();
$map->get_batiment();
$map->onclick_status = true;

$map->affiche();
?>