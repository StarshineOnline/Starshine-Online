<?php
$mail = '';

include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//R�cup�re le timestamp en milliseconde de d�but de cr�ation de la page
$debut = getmicrotime();

//R�cup�ration des variables de connexion � la base et connexion � cette base
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

//Inclusion du fichier contenant les fonctions permettant de g�rer les qu�tes
include('fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de g�rer l'�quipement
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
$root = '';

$data = array();
$legend = array();
$label = array();
$dates = array();

$date = date("Y-m-d");
$requete = "SELECT niveau_moyen, nombre_joueur, nombre_monstre, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 78 DAY) ORDER BY date;";
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
$graph->Stroke($root.'image/stat_joueur2.jpg');

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
$graph->Stroke($root.'image/stat_monstre2.jpg');

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
$graph->Stroke($root.'image/stat_niveau_moyen2.jpg');

$data = array();
$legend = array();
$label = array();
$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	$data[] = $row['total'];
	$legend[] = $Gtrad[$row['race']];
	$label[] = $Gtrad[$row['race']]."(".$row['total'].")\n%.1f%%";
}

/* *********************** */
$graph = new Graph(700, 500, "auto");
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(20);
$graph->SetShadow();

$graph->title->Set("R�partition de la population par race le ".$date);
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$p1 = new BarPlot($data);
$p1->SetShadow();
$p1->SetWidth(0.7);
$p1->value->Show();
//$p1->value->SetFont(FF_ARIAL, FS_BOLD, 10);
//$p1->value->SetAngle(45);
$p1->value->SetFormat('%d');
$graph->Add($p1);
$graph->xaxis->title->Set("Races");
$graph->yaxis->title->Set("Joueurs");
$graph->xaxis->SetTickLabels($legend);
$graph->xaxis->SetLabelAngle(90);
$graph->Stroke('image/stat_race2.jpg'); 
?>