<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
require('class/map.class.php');
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

$map = new map($x, $y, 3, '', true);
//if(is_array($MAPTAB[$x_map][$y_map])) { $class_map = "decor tex".$MAPTAB[$x_map][$y_map]["decor"]; } else { $class_map = "texblack"; };

$map->get_pnj();
$map->get_joueur($objXY->race);
$map->get_monstre($level);

$map->affiche();
?>