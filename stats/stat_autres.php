<?php
$root = '';

$data = array();
$legend = array();
$label = array();
$dates = array();

$date = date("Y-m-d");
$requete = "SELECT niveau_moyen, nombre_joueur, nombre_monstre, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date;";
echo $requete.'<br />';
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
$DataSet = new pData();
$DataSet->AddPoint($data['nombre_joueur'], "Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($dates, "dates");
$DataSet->SetAbsciseLabelSerie("dates");

//Graph
$graph = new pChart(900, 400);
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,880,375);
$graph->drawFilledRoundedRectangle(7,7,893,393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,895,395,5,230,230,230);
$graph->drawGraphArea(255,255,255,TRUE);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230,50);

// Draw the 0 line
$graph->setFontProperties("pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

// Finish the graph  
$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du nombre de joueurs',50,50,50,585);
$graph->Render($root.'image/stat_joueur.png');

//GRAPHES NBR MONSTRES
$DataSet = new pData();
$DataSet->AddPoint($data['nombre_monstre'], "Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($dates, "dates");
$DataSet->SetAbsciseLabelSerie("dates");

//Graph
$graph = new pChart(900, 400);
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,880,375);
$graph->drawFilledRoundedRectangle(7,7,893,393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,895,395,5,230,230,230);
$graph->drawGraphArea(255,255,255,TRUE);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230,50);

// Draw the 0 line
$graph->setFontProperties("pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

// Finish the graph  
$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du nombre de monstres',50,50,50,585);
$graph->Render($root.'image/stat_monstre.png');

//GRAPHES NBR NIVEAUX MOYEN
$DataSet = new pData();
$DataSet->AddPoint($data['niveau_moyen'], "Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($dates, "dates");
$DataSet->SetAbsciseLabelSerie("dates");

//Graph
$graph = new pChart(900, 400);
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,880,375);
$graph->drawFilledRoundedRectangle(7,7,893,393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,895,395,5,230,230,230);
$graph->drawGraphArea(255,255,255,TRUE);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230,50);

// Draw the 0 line
$graph->setFontProperties("pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

// Finish the graph  
$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du niveau moyen',50,50,50,585);
$graph->Render($root.'image/stat_niveau_moyen.png');
?>