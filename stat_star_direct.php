<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

include_once ("jpgraph/src/jpgraph.php");
include_once ("jpgraph/src/jpgraph_pie.php");
include_once ("jpgraph/src/jpgraph_pie3d.php");
include_once ("jpgraph/src/jpgraph_line.php");
include_once ("jpgraph/src/jpgraph_bar.php");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

$root = '';

$data = array();
$legend = array();
$label = array();
$dates = array();

$color = array();
//Couleur des royaumes
$color[] = "#aaaaaa";
$color[] = "#0068ff";
$color[] = "#009900";
$color[] = "#ff0000";
$color[] = "#ffff00";
$color[] = "#0000ff";
$color[] = "#ffcccc";
$color[] = "#ffa500";
$color[] = "#5c1e00";
$color[] = "#000000";
$color[] = "#0000ff";
$color[] = "#ffffff";
$color[] = "#cccccc";

$date = date("Y-m-d");
$requete = "SELECT *, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date;";
echo $requete;
$req = $db->query($requete);
$strips = array('id', 'date', 'year', 'day', 'month');
$z = 0;
while($row = $db->read_assoc($req))
{
	$dates[] = $row['year'].'-'.$row['month'].'-'.$row['day'];
	$keys = array_keys($row);
	$j = 0;
	while($j < count($keys))
	{
		if(!in_array($keys[$j], $strips))
		{
				$donnees = explode(';', $row[$keys[$j]]);
				if(count($donnees) > 1)
				{
					$data[$keys[$j]][] = $donnees[1];
				}
				else $data[$keys[$j]][] = $row[$keys[$j]];
		}
		$j++;
	}
	$z++;
	/*$data[] = $row['total'];
	$legend[] = $row['race'].'('.$row['total'].')';
	$label[] = $row['race']."(".$row['total'].")\n%.1f%%";*/
}
$maximums = array();
//Tri des peuples par ceux qui ont le plus d'argents pendant le mois
$keys = array_keys($data);
$i = 0;
echo '<pre>';
print_r($data);
while($i < count($data))
{
	$maximums[$keys[$i]] = $data[$keys[$i]][($z - 1)];
	echo $z.' '.$keys[$i].' '.$data[$keys[$i]][($z - 1)].'<br />';
	$i++;
}
echo '<pre>';
print_r($maximums);
array_multisort($maximums, SORT_DESC, $maximums);
print_r($maximums);

//GRAPHES
$i = 0;
$j = 0;
while($j < 11)
{
	$graph = new Graph(900, 400, "auto");
	$graph->SetShadow();
	
	$graph->SetMarginColor('white');
	$graph->SetScale("textlin");
	$graph->SetFrame(false);
	$graph->SetMargin(50,120,30,30);
	
	$graph->tabtitle->Set('Evolution du nombre de stars par royaume - Graph '.($i + 1));
	//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
	
	$graph->yaxis->HideZeroLabel();
	$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
	$graph->xgrid->Show();
	
	$graph->xaxis->SetTickLabels($dates);
	
	// Create lines
	$keys = array_keys($data);
	$keys2 = array_keys($maximums);
	$z = 0;
	while($z < 4 AND $j < 11)
	{
		$p1 = new LinePlot($data[$keys2[$j]]);
		$p1 ->SetWeight(2);
		$p1->SetLegend($Gtrad[$keys2[$j]]);
		$p1->SetColor($color[$j]);
		$graph->Add($p1);
		$z++;
		$j++;
	}
	
	$graph->legend->SetShadow('gray@0.4',5);
	$graph->legend->SetPos(0, 0.1, "right", "top");
	// Output line
	$graph->Stroke($root.'image/stat_star'.($i + 1).'.jpg');
	$i++;
}
?>