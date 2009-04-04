<?php
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
$map = new map($x, $y);

$map->get_pnj();
$map->get_joueur($objXY->race);
$map->get_drapeau();
$map->get_batiment();
$map->get_monstre($level);

if(isset($_GET['cache_monstre']))
	$map->change_cache_monstre();
	
$map->affiche();
?>
