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
$color['barbare'] = array(0, 104, 255);
$color['elfebois'] = array(0, 153, 0);
$color['troll'] = array(255, 0, 0);
$color['scavenger'] = array(255, 255, 0);
$color['orc'] = array(255, 204, 204);
$color['nain'] = array(255, 165, 0);
$color['mortvivant'] = array(92, 30, 0);
$color['humainnoir'] = array(0, 0, 0);
$color['humain'] = array(0, 0, 255);
$color['elfehaut'] = array(170, 170, 170);
$color['vampire'] = array(130, 30, 130);

$date = date("Y-m-d");
$requete = "SELECT *, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date ASC;";
$mail .= $requete."\n";
$req = $db->query($requete);
$strips = array('id', 'date', 'year', 'day', 'month', 'niveau_moyen', 'nombre_joueur', 'nombre_monstre', 'food');
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
	$graph = new pChart(750, 400);
	//Datas
	// Create lines
	$keys = array_keys($moyenne);
	$keys2 = array_keys($maximums);
	$z = 0;
	$plots = array();
	$DataSet = new pData;
	while($z < 4 AND $j < 11)
	{
		$DataSet->AddPoint($moyenne[$keys2[$j]], $Gtrad[$keys2[$j]]);
		$graph->setColorPalette($z, $color[$keys2[$j]][0], $color[$keys2[$j]][1], $color[$keys2[$j]][2]);
		$z++;
		$j++;
	}
	$DataSet->AddAllSeries();
	$DataSet->AddPoint($dates, "dates");
	$DataSet->SetAbsciseLabelSerie("dates");

	//Graph
	$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
	$graph->setGraphArea(70,30,730,375);
	$graph->drawFilledRoundedRectangle(7,7,730,393,5,240,240,240);
	$graph->drawRoundedRectangle(5,5,730,395,5,230,230,230);
	$graph->drawGraphArea(255,255,255,TRUE);
	$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
	$graph->drawGrid(4,TRUE,230,230,230,200);
	
	// Draw the 0 line
	$graph->setFontProperties("pChart/fonts/tahoma.ttf",6);
	$graph->drawTreshold(0,143,55,72,TRUE,TRUE);
	 
	 
	// Draw the cubic curve graph
	$graph->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
	 
	// Finish the graph  
	$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
	$graph->drawLegend(680,30,$DataSet->GetDataDescription(),235,235,235);
	$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
	$graph->drawTitle(50,22,'Evolution du nombre de stars par royaume (moyenne sur 5 jours) - Graph '.($i + 1),50,50,50,585);
	$graph->Render($root.'image/stat_star'.($i + 1).'.png');

	$i++;
}
?>