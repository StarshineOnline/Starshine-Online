<?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	$data[] = $row['total'];
	$legend[] = $Gtrad[$row['race']].'('.$row['total'].')';
	$label[] = $Gtrad[$row['race']]."(".$row['total'].")\n%.1f%%";
}

$graph = new PieGraph(700, 400, "auto");
$graph->SetShadow();

$graph->title->Set("Répartition de la population par race le ".$date);
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$p1 = new PiePlot3D($data);
$p1->SetLabels($label);
$p1->SetSize(0.5);
$p1->SetCenter(0.45);
//$p1->SetLegends($legend);
$p1->SetLabelPos(0.6);
$graph->Add($p1);
$graph->Stroke('image/stat_race.jpg'); 
?>