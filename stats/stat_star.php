<?php
$root = '';

$data = array();
$dates_m = array();
$moyenne = array();
$legend = array();
$label = array();
$dates = array();
$plop = array();

$color = array();
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

$date = date("Y-m-d");
$requete = "SELECT *, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date;";
$req = $db->query($requete);
$strips = array('id', 'date', 'year', 'day', 'month', 'niveau_moyen', 'nombre_joueur', 'nombre_monstre');
$z = 0;
while($row = $db->read_assoc($req))
{
	$dates[] = $row['month'].'-'.$row['day'];
	if($z > 3)
	{
		$dates_m[] = $row['month'].'-'.$row['day'];
		$plop[] = $z - 4;
	}
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
				if($z > 3)
				{
					$moyenne[$keys[$j]][] = ($data[$keys[$j]][$z] + $data[$keys[$j]][($z - 1)] + $data[$keys[$j]][($z - 2)] + $data[$keys[$j]][($z - 3)] + $data[$keys[$j]][($z - 4)]) / 5;
				}
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
$keys = array_keys($moyenne);
$i = 0;
while($i < count($moyenne))
{
	$maximums[$keys[$i]] = $moyenne[$keys[$i]][($z - 5)];
	$i++;
}
array_multisort($maximums, SORT_DESC, $maximums);

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
	$graph->SetScale('linlin');
	
	$graph->tabtitle->Set('Evolution du nombre de stars par royaume (moyenne sur 5 jours) - Graph '.($i + 1));
	//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
	
	$graph->yaxis->HideZeroLabel();
	$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
	$graph->xgrid->Show();
	$graph->xaxis->SetTickLabels($dates_m);
	
	// Create lines
	$keys = array_keys($moyenne);
	$keys2 = array_keys($maximums);
	$z = 0;
	while($z < 4 AND $j < 11)
	{
		$splot = new ScatterPlot($plop, $moyenne[$keys2[$j]]);
		$spline = new Spline($plop, $moyenne[$keys2[$j]]);
		list($newx, $newy) = $spline->Get(100);
		$p1 = new LinePlot($newy, $newx);
		$p1 ->SetWeight(2);
		$p1->SetLegend($Gtrad[$keys2[$j]]);
		$p1->SetColor($color[$keys2[$j]]);
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