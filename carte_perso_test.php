<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);

$x = $joueur->get_x();
$y = $joueur->get_y();

$longueur_map = 20;

$xmax = $x + $longueur_map/2;
$xmin = $x - $longueur_map/2;

$ymax = $y + $longueur_map/2;
$ymin = $y - $longueur_map/2;

$RqMapTxt = $db->query("SELECT x,y,decor,royaume,info,type FROM map 
				 WHERE y >= $ymin AND y <= $ymax 
				 AND x >= $xmin AND x <= $xmax
				 ORDER BY y,x;");
if($db->num_rows($RqMapTxt) > 0)
{
	$c = 0;
	while($objMapTxt = $db->read_object($RqMapTxt))
	{
		if($c == 0)
			echo "<ul style='float:left;margin:0;padding:0;clear:both;'>";

		$typeterrain = type_terrain($objMapTxt->info);
		switch($typeterrain[0])
		{
			case 'plaine' : 		$color = "#9ACD32";break;
			case 'foret' :			$color = "#008000";break;
			case 'desert' :			$color = "#F0E68C";break;
			case 'glace' : 			$color = "#E0FFFF";break;
			case 'eau' : 			$color = "#4682B4";break;
			case 'montagne' : 		$color = "#A0522D";break;
			case 'marais' : 		$color = "#DAA520";break;
			case 'route' : 			$color = "#D3D3D3";break;
			case 'terre_maudite' : 	$color = "#696969";break; 
			case 'objet' : 			$color = "#708090";break;
			default : 				$color = "#000000";break;
		}
		if($x == $objMapTxt->x AND $y == $objMapTxt->y)
		{
			$color = "#DC143C";
		}
		echo "<li style='background:$color;height:5px;width:5px;list-style:none;float:left;'></li>";
		$c++;
		if($c == (($xmax - $xmin)+1))
		{
			echo "</ul>";
			$c = 0;
		}
	}
}


?>
