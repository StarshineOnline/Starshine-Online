<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion du haut du document html
include(root.'roi/haut_roi.php');

$joueur = new perso($_SESSION['ID']);

$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);


$map = new map($_GET['x'], $_GET['y'], 8, '../', false, 'low');
$map->get_drapeau();
$map->get_batiment($royaume->get_id());
$map->quadrillage = true;
$map->onclick_status = false;
$map->affiche();

?>



