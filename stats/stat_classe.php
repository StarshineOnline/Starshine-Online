<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT LOWER(nom) as name, rang FROM classe WHERE rang > 0 ORDER BY rang";
$req = $db->query($requete);
echo $requete.'<br />';
while($row = $db->read_assoc($req))
{
	$classes[$row['name']]['rang'] = $row['rang'];
	$classes[$row['name']]['total'] = 0;
}

$rang = array(false, false, false, false);

$requete = "SELECT classe, COUNT(*) as total, classe.rang as rang FROM perso RIGHT JOIN classe ON perso.classe_id = classe.id WHERE statut = 'actif' GROUP BY classe ORDER BY classe.rang ASC";
$req = $db->query($requete);
echo $requete.'<br />';
while($row = $db->read_array($req))
{
	if(!$rang[$row['rang']])
	{
		$rang[$row['rang']] = true;
	}
	$data[$row['rang']][] = $row['total'];
	$legend[$row['rang']][] = $row['classe'].'('.$row['total'].')';
	$label[$row['rang']][] = $row['classe']."(".$row['total'].")\n%.1f%%";
}

for($i = 0; $i < 5; $i++)
{
	if($rang[$i])
	{
		// Dataset definition
		$DataSet = new pData;
		$DataSet->AddPoint($data[$i],"Serie1");
		$DataSet->AddPoint($legend[$i],"Serie2");
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
		$graph->drawTitle(50,22,'Répartition de la population par classe - Rang '.$i.', le '.$date ,50,50,50,585);  
		
		$graph->Render('image/stat_classe'.$i.'.png');
	}
}
?>