<?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT level, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY level";
$req = $db->query($requete);
echo $requete.'<br />';
while($row = $db->read_array($req))
{
	$data[] = $row['total'];
	$legend[] = $row['level'];
	$label[] = "Niv ".$row['level']."(".$row['total'].")\n%.1f%%";
}

print_r($data);

// Dataset definition
$DataSet = new pData;
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($legend,"Serie2");
$DataSet->SetAbsciseLabelSerie("Serie2");
$DataSet->SetYAxisName("Population");
$DataSet->SetXAxisName("Niveau");

// Initialise the graph
$graph = new pChart(700,400);
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(50,35,680,360);
$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);
$graph->drawGraphArea(255,255,255,TRUE);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
$graph->drawGrid(4,TRUE,230,230,230,50);

// Draw the 0 line
$graph->setFontProperties("pChart/font/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);
 
// Draw the bar graph
$graph->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);
 
// Write values on Serie1
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->writeValues($DataSet->GetData(),$DataSet->GetDataDescription(),"Serie1");

// Finish the graph
$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Répartition de la population par niveau le '.$date,50,50,50,585);

$graph->Render('image/stat_lvl.png');
?>