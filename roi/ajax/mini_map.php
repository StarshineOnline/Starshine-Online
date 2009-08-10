<?php
if (file_exists('../../root.php'))
  include_once('../../root.php');

//Inclusion du haut du document html
include(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

$R = get_royaume_info($joueur->get_race(), $Trace[$joueur->get_race()]['numrace']);


$W_distance = detection_distance($W_case,convert_in_pos($joueur->get_x(), $joueur->get_y()));

$W_coord = convert_in_coord($W_case);

$batiments = array();
$dimensions = dimension_map($_POST['x'], $_POST['y'], 8);
$requete = "SELECT x, y, hp, nom, type, image FROM construction WHERE royaume = ".$R['ID']." AND x >= ".$dimensions['xmin']." AND x <= ".$dimensions['xmax']." AND y >= ".$dimensions['ymin']." AND y <= ".$dimensions['ymax'];
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$batiments[convert_in_pos($row['x'], $row['y'])] = $row;
}	
$map = new map($_POST['x'], $_POST['y'], 8, '../', false, 'low');
$map->set_batiment($batiments);
$map->quadrillage = true;
$map->affiche();

?>



