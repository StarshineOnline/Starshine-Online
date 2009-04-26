<?php
$data = array();
$legend = array();
$label = array();
$dates = array();

$LARGEUR = 750;
$HAUTEUR = 400;
$bord_gauche = $LARGEUR - 20;

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
$graph = new pChart($LARGEUR, $HAUTEUR);
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,$bord_gauche,375);
$graph->drawFilledRoundedRectangle(7, 7, ($LARGEUR - 7), 393, 5, 240, 240, 240);
$graph->drawRoundedRectangle(5, 5, ($LARGEUR - 5), 395, 5, 230, 230, 230);
$graph->drawGraphArea(200,200,200);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230);

// Draw the 0 line
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

//Changer la couleur des graphes
$graph->setColorPalette(0, 32, 38, 111);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2,1,200,200,200);

// Finish the graph  
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du nombre de joueurs',50,50,50,585);
$graph->Render($root.'image/stat_joueur.png');

//GRAPHES NBR MONSTRES
$DataSet = new pData();
$DataSet->AddPoint($data['nombre_monstre'], "Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($dates, "dates");
$DataSet->SetAbsciseLabelSerie("dates");

//Graph
$graph = new pChart($LARGEUR, $HAUTEUR);
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,$bord_gauche,375);
$graph->drawFilledRoundedRectangle(7,7,($LARGEUR - 7),393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,($LARGEUR - 5),395,5,230,230,230);
$graph->drawGraphArea(200,200,200);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230);

// Draw the 0 line
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

//Changer la couleur
$graph->setColorPalette(0, 32, 38, 111);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2,1,200,200,200);

// Finish the graph  
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du nombre de monstres',50,50,50,585);
$graph->Render($root.'image/stat_monstre.png');

//GRAPHES NBR NIVEAUX MOYEN
$DataSet = new pData();
$DataSet->AddPoint($data['niveau_moyen'], "Serie1");
$DataSet->AddAllSeries();
$DataSet->AddPoint($dates, "dates");
$DataSet->SetAbsciseLabelSerie("dates");

//Graph
$graph = new pChart($LARGEUR,$HAUTEUR);
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",8);
$graph->setGraphArea(70,30,$bord_gauche,375);
$graph->drawFilledRoundedRectangle(7,7,($LARGEUR - 7),393,5,240,240,240);
$graph->drawRoundedRectangle(5,5,($LARGEUR - 5),395,5,230,230,230);
$graph->drawGraphArea(200,200,200);
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$graph->drawGrid(4,TRUE,230,230,230);

// Draw the 0 line
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",6);
$graph->drawTreshold(0,143,55,72,TRUE,TRUE);

//Changer la couleur
$graph->setColorPalette(0, 32, 38, 111);

// Draw the cubic curve graph
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2,1,200,200,200);

// Finish the graph  
$graph->setFontProperties($root."pChart/fonts/tahoma.ttf",12);
$graph->drawTitle(50,22,'Evolution du niveau moyen',50,50,50,585);
$graph->Render($root.'image/stat_niveau_moyen.png');
?>