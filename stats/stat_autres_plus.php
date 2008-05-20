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

$date = date("Y-m-d");
$requete = "SELECT niveau_moyen, nombre_joueur, nombre_monstre, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 150 DAY) ORDER BY date;";
$req = $db->query($requete);
$strips = array('id', 'date', 'year', 'day', 'month');
while($row = $db->read_assoc($req))
{
	$dates[] = $row['month'].'-'.$row['day'];
	$keys = array_keys($row);
	$j = 0;
	while($j < count($keys))
	{
		if(!in_array($keys[$j], $strips))
		{
				$data[$keys[$j]][] = $row[$keys[$j]];
		}
		$j++;
	}
	/*$data[] = $row['total'];
	$legend[] = $row['race'].'('.$row['total'].')';
	$label[] = $row['race']."(".$row['total'].")\n%.1f%%";*/
}

//GRAPHES NBR JOUEURS
$graph = new Graph(900, 400, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du nombre de joueurs');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

// Create lines
$p1 = new LinePlot($data['nombre_joueur']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_joueur_plus.jpg');

//GRAPHES NBR MONSTRES
$graph = new Graph(900, 400, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du nombre de monstres');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

// Create lines
$p1 = new LinePlot($data['nombre_monstre']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_monstre_plus.jpg');

//GRAPHES NBR NIVEAUX MOYEN
$graph = new Graph(900, 400, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du niveau moyen');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

// Create lines
$p1 = new LinePlot($data['niveau_moyen']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_niveau_moyen_plus.jpg');
?>