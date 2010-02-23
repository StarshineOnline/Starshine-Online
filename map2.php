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
if(!empty($joueur->in_arene())) $map->set_arene(true);
$map->get_pnj();
$map->get_joueur($objXY->race);
$map->get_drapeau();
$map->get_batiment();
$map->onclick_status = true;
if(isset($_GET['show_only'])) $map->change_show_only($_GET['show_only']);
$map->get_monstre($level);

if(isset($_GET['cache_monstre'])) $map->change_cache_monstre();
if(isset($_GET['affiche_royaume'])) $map->change_affiche_royaume();

$map->affiche();
?>
