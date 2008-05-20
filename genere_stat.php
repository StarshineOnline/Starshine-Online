<?php
include('class/db.class.php');
include('fonction/time.inc');
include('fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include('fonction/quete.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include('fonction/equipement.inc');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_pie.php");
include ("jpgraph/src/jpgraph_pie3d.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_bar.php");
include ("jpgraph/src/jpgraph_scatter.php");
include ("jpgraph/src/jpgraph_regstat.php");
$root = '';

$data = array();
$legend = array();
$label = array();
$dates = array();

//Couleur des royaumes
$color['barbare'] = "#0068ff";
$color['elfebois'] = "#009900";
$color['troll'] = "#ff0000";
$color['scavenger'] = "#ffff00";
$color['orc'] = "#ffcccc";
$color['nain'] = "#ffa500";
$color['mortvivant'] = "#5c1e00";
$color['humainnoir'] = "#000000";
$color['humain'] = "#0000ff";
$color['elfehaut'] = "#aaaaaa";
$color['vampire'] = "#cccccc";

$type = 'moyenne';
$races = 'elfebois,elfehaut,scavenger';
$races_array = explode(',', $races);

$date = date("Y-m-d");
$requete = "SELECT ".$races.", EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 230 DAY) ORDER BY date;";
$req = $db->query($requete);
$z = 0;
while($row = $db->read_assoc($req))
{
	$dates[] = $row['month'].'-'.$row['day'];
	if($z > 3)
	{
		$dates_m[] = $row['month'].'-'.$row['day'];
		$plop[] = $z - 4;
	}
	foreach($races_array as $race)
	{
		$donnees = explode(';', $row[$race]);
		$data[$race][] = $donnees[1];
		if($type == 'moyenne')
		{
			if($z > 3)
			{
				$moyenne[$race][] = ($data[$race][$z] + $data[$race][($z - 1)] + $data[$race][($z - 2)] + $data[$race][($z - 3)] + $data[$race][($z - 4)]) / 5;
			}
		}
		else $moyenne[$race][] = $data[$race][$z];
	}
	$z++;
	/*$data[] = $row['total'];
	$legend[] = $row['race'].'('.$row['total'].')';
	$label[] = $row['race']."(".$row['total'].")\n%.1f%%";*/
}

//GRAPHES
$graph = new Graph(900, 400, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);
$graph->SetScale('linlin');

$graph->tabtitle->Set('Evolution du nombre de stars par royaume (moyenne sur 5 jours) - Graph '.($i + 1));
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();
$graph->xaxis->SetTickLabels($dates_m);

// Create lines
foreach($races_array as $race)
{
	if($type == 'moyenne')
	{
		$splot = new ScatterPlot($plop, $moyenne[$race]);
		$spline = new Spline($plop, $moyenne[$race]);
		list($newx, $newy) = $spline->Get(100);
		$p1 = new LinePlot($newy, $newx);
	}
	else $p1 = new LinePlot($moyenne[$race]);
	$p1 ->SetWeight(2);
	$p1->SetLegend($Gtrad[1]);
	$p1->SetColor($color[$race]);
	$graph->Add($p1);
}

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_star_genere.jpg');

?>