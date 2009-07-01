<?php
$root = './../../';
//Inclusion du haut du document html
include($root.'haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Véifie si le perso est mort
verif_mort($joueur, 1);

$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

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



