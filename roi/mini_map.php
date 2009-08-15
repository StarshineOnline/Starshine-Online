<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Inclusion du haut du document html
include(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);


$map = new map($_POST['x'], $_POST['y'], 8, '../', false, 'low');
$map->get_drapeau();
$map->get_batiment();
$map->quadrillage = true;
$map->affiche();

?>



