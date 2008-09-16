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
$graph->Stroke($root.'image/stat_joueur.jpg');

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
$graph->Stroke($root.'image/stat_monstre.jpg');

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
$graph->Stroke($root.'image/stat_niveau_moyen.jpg');
?>