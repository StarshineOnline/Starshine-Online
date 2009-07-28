<?php
if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'haut_roi.php');
include_once(root.'../class/map.class.php');

//-- Récupération de la position X, Y du joueur.
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
else if(!array_key_exists('poscase', $_GET))
{
	$RqXY = $db->query("SELECT x, y FROM perso WHERE ID=".$_SESSION["ID"].";");
	$objXY = $db->read_object($RqXY);
	$x = $objXY->x;
	$y = $objXY->y;
}
else
{
	$xy = convertd_in_coord($_GET['poscase']);
	$x = $xy['x'];
	$y = $xy['y'];
}

$map = new map($x, $y, 14, '../', false, 'low');
$map->set_batiment();

$map->affiche();

/*
//-- Champ de vision = 3 par défaut
$champ_vision = 14;
//-- Nombre de case affichées en longueur et largeur
$case_affiche = ($champ_vision * 2) + 1;

{//-- Sert à calculer le point d'origine en haut a gauche pour la carte
	if($x < ($champ_vision + 1))			{ $x_min = 1;		$x_max = $x + ($case_affiche - ($x)); }
	elseif($x > (150 - $champ_vision))		{ $x_max = 150;		$x_min = $x - ($case_affiche - (150 - $x + 1)); }
	else								{ $x_min = $x - $champ_vision;	$x_max = $x + $champ_vision; };
	
	if($y < ($champ_vision + 1))		{ $y_min = 1;		$y_max = $y + ($case_affiche - ($y)); }
	elseif($y > (150 - $champ_vision))	{ $y_max = 150;		$y_min = $y - ($case_affiche - (150 - $y + 1)); }
	else								{ $y_min = $y - $champ_vision; 	$y_max = $y + $champ_vision; }
}	

//On va afficher la carte
$RqMap = $db->query("SELECT * FROM map 
					 WHERE ( (FLOOR(ID / $G_ligne) >= $y_min) AND (FLOOR(ID / $G_ligne) <= $y_max) ) 
					 AND ( ((ID - (FLOOR(ID / $G_colonne) * 1000) ) >= $x_min) AND ((ID - (FLOOR(ID / $G_colonne) * 1000)) <= $x_max) ) 
					 ORDER BY ID;");
					 
echo '<div id="carte" style="width : 605px; height : 610px;">';
{//-- Affichage du bord haut (bh) de la map
	echo "<ul id='map_bord_haut'>
		   <li id='map_bord_haut_gauche' style='width : 20px; height : 20px;' onclick=\"switch_map();\">&nbsp;</li>";
	for ($bh = $x_min; $bh <= $x_max; $bh++)
	{
		if($bh == $x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
		echo "<li $class_x style='width : 20px; height : 20px;'>$bh</li>";
	}
	echo "</ul>";
}
{//-- Affichage du reste de la map
	$y_BAK = 0;
	$Once = false;
	$case = 0;
	while($objMap = $db->read_object($RqMap))
	{
		$coord = convert_in_coord($objMap->ID);
		$class_map = "decor texl".$objMap->decor;	//-- Nom de la classe "terrain" contenu dans texture.css
		
		if($coord['y'] != $y_BAK)
		{//-- On passe a la ligne
			if($Once) { echo "</ul>"; } else { $Once = true; };
			if($coord['y'] == $y) { $class_y = "id='bord_haut_y' "; } else { $class_y = ""; }; //-- Pour mettre en valeur la position Y ou se trouve le joueur
			echo "<ul class='map' style='height : 20px;'>
			 	   <li $class_y style='width : 20px; height : 20px;'>".$coord['y']."</li>"; //-- Bord gauche de la map
			 
			$y_BAK = $coord['y'];
		}
		$background = "";
		$overlib = "";
		
		$border = "border:0px solid ".$Gcouleurs[$objMap->royaume].";";
		echo "<li class='$class_map' style='width : 20px; height : 20px;'>
			   <div class='map_contenu' 
			   		id='marq$case' 
			   		style=\"".$background.$border."width : 20px; height : 20px;\" ";
		echo " onclick=\"new Ajax.Updater('information', 'informationcase_roi.php?case=".$objMap->ID."');\">&nbsp;</div>
			  </li>";	
		
		$case++;
	}
	echo "</ul>";
}
*/
?>
</div>
<div id="information" style="float : right;">
	INFOS
</div>
