<?php

include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include('fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include('fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_pie.php");
include ("jpgraph/src/jpgraph_pie3d.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_bar.php");
include ("jpgraph/src/jpgraph_scatter.php");
include ("jpgraph/src/jpgraph_regstat.php");
$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

$show_info = array();

echo 'Création de la carte des royaumes<br />';

$im = imagecreate (600, 600)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 0, 0, 0);

$color1 = imagecolorallocate($im, 0xc0, 0x07, 0x14);
$color2 = imagecolorallocate($im, 0x33, 0xcc, 0x00);
$color3 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color4 = imagecolorallocate($im, 0xfc, 0xff, 0x00);

$requete = "SELECT * FROM diplomatie WHERE race = '".$_GET['race']."'";
$req = $db->query($requete);
$row = $db->read_assoc($req);
$keys = array_keys($row);
$i = 0;
$count = count($keys);
while($i < $count)
{
	if($row[$keys[$i]] == 127) $show_info[$Trace[$keys[$i]]['numrace']] = $color4;
	elseif($row[$keys[$i]] > 6) $show_info[$Trace[$keys[$i]]['numrace']] = $color1;
	elseif($row[$keys[$i]] > 3) $show_info[$Trace[$keys[$i]]['numrace']] = $color3;
	else $show_info[$Trace[$keys[$i]]['numrace']] = $color2;
	$i++;
}

$col = 'royaume';
$carte = 'image/carte/carte_diplo_'.$_GET['race'].'.png';

//Requète pour l'affichage de la map
$requete = 'SELECT ID, royaume FROM map ORDER BY ID';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['ID']);
	$rowid = $row['ID'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		imagefilledrectangle($im, (($coord['x'] - 1) * 4), (($coord['y'] - 1) * 4), ((($coord['x'] - 1) * 4) + 3), ((($coord['y'] - 1) * 4) + 3), $show_info[$row[$col]]);
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);
?>