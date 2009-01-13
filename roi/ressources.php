<?php
require('haut_roi.php');

function ressource($nom)
{
	$ressource['pierre'] = 18;
	$ressource['bois'] = 19;
	$ressource['eau'] = 20;
	$ressource['sable'] = 21;
	$ressource['charbon'] = 22;
	$ressource['em'] = 23;
	$ressource['star'] = 24;
	$ressource['nourriture'] = 25;
	return $ressource[$nom];
}

if(array_key_exists('ress', $_GET))
{
	include("../pChart/pData.class");
	include("../pChart/pChart.class");
	$date_semaine = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
	$requete = "SELECT ".$R['race'].", date FROM stat_jeu WHERE date > '".$date_semaine."' ORDER BY date ASC";
	$req = $db->query($requete);
	$data = array();
	$dates = array();
	while($row = $db->read_assoc($req))
	{
		$stat_race = $row[$R['race']];
		$explode = explode(';', $stat_race);
		$dates[] = $row['date'];
		$data[] = $explode[ressource($_GET['ress'])];
	}

	$DataSet = new pData();
	$DataSet->AddPoint($data, "Serie1");
	$DataSet->AddAllSeries();
	$DataSet->AddPoint($dates, "dates");
	$DataSet->SetAbsciseLabelSerie("dates");

	//Graph
	$graph = new pChart(900, 400);
	$graph->setFontProperties("../pChart/fonts/tahoma.ttf",8);
	$graph->setGraphArea(70,30,880,375);
	$graph->drawFilledRoundedRectangle(7,7,893,393,5,240,240,240);
	$graph->drawRoundedRectangle(5,5,895,395,5,230,230,230);
	$graph->drawGraphArea(255,255,255,TRUE);
	$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
	$graph->drawGrid(4,TRUE,230,230,230,50);
	
	// Draw the 0 line
	$graph->setFontProperties("../pChart/fonts/tahoma.ttf",6);
	$graph->drawTreshold(0,143,55,72,TRUE,TRUE);
	
	// Draw the cubic curve graph
	$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
	
	// Finish the graph  
	$graph->setFontProperties("../pChart/fonts/tahoma.ttf",12);
	$graph->drawTitle(50,22,$_GET['ress'].' dans la semaine',50,50,50,585);
	$graph->Render('image/'.$R['race'].'_'.$_GET['ress'].'.png');
	?>
	<img src="image/<?php echo $R['race']; ?>_<?php echo $_GET['ress']; ?>.png" />
	<?php
}
else
{
	$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
	$requete = "SELECT ".$R['race']." FROM stat_jeu WHERE date = '".$date_hier."'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$explode_stat = explode(';', $row[$R['race']]);
	
	$pierre = $explode_stat[18];
	$bois = $explode_stat[19];
	$eau = $explode_stat[20];
	$sable = $explode_stat[21];
	$charbon = $explode_stat[22];
	$em = $explode_stat[23];
	$star = $explode_stat[24];
	$nourriture = $explode_stat[25];
	
	echo '
	<h3>Ressources gagnées hier</h3>
	<a href="ressources.php?ress=pierre" onclick="return envoiInfo(this.href, \'conteneur\');">Pierre</a> : '.$pierre.'<br />
	<a href="ressources.php?ress=bois" onclick="return envoiInfo(this.href, \'conteneur\');">Bois</a> : '.$bois.'<br />
	<a href="ressources.php?ress=eau" onclick="return envoiInfo(this.href, \'conteneur\');">Eau</a> : '.$eau.'<br />
	<a href="ressources.php?ress=sable" onclick="return envoiInfo(this.href, \'conteneur\');">Sable</a> : '.$sable.'<br />
	<a href="ressources.php?ress=charbon" onclick="return envoiInfo(this.href, \'conteneur\');">Charbon</a> : '.$charbon.'<br />
	<a href="ressources.php?ress=em" onclick="return envoiInfo(this.href, \'conteneur\');">Essence Magique</a> : '.$em.'<br />
	<a href="ressources.php?ress=star" onclick="return envoiInfo(this.href, \'conteneur\');">Star</a> : '.$star.'<br />
	<a href="ressources.php?ress=nourriture" onclick="return envoiInfo(this.href, \'conteneur\');">Nourriture</a> : '.$nourriture.'<br />
	';
}
?>