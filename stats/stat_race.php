<?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
echo $requete.'<br />';
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	$data[] = $row['total'];
	$legend[] = $Gtrad[$row['race']].' ('.$row['total'].')';
	$label[] = $Gtrad[$row['race']]."(".$row['total'].")\n%.1f%%";
}

// Dataset definition
$DataSet = new pData;
$DataSet->AddPoint($data,"Serie1");
$DataSet->AddPoint($legend,"Serie2");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie("Serie2");

// Initialise the graph
$graph = new pChart(700, 400);
$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);

// Draw the pie chart  
$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
$graph->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),315,210,250,PIE_PERCENTAGE_LABEL,TRUE,50,20,5);
//$graph->drawPieLegend(590,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Répartition de la population par race le '.$date ,50,50,50,585);  

$graph->Render('image/stat_race.png');

?>