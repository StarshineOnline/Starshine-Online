<?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT level, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY level";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	$data[] = $row['total'];
	$legend[] = $row['level'].'('.$row['total'].')';
	$label[] = "Niv ".$row['level']."(".$row['total'].")\n%.1f%%";
}

$graph = new Graph(700, 400, "auto");
$graph->SetScale("textlin");
$graph->yaxis->scale->SetGrace(20);
$graph->SetShadow();

$graph->title->Set("Répartition de la population par niveau le ".$date);
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$p1 = new BarPlot($data);
$p1->SetShadow();
$p1->SetWidth(0.7);
$p1->value->Show();
//$p1->value->SetFont(FF_ARIAL, FS_BOLD, 10);
//$p1->value->SetAngle(45);
$p1->value->SetFormat('%d');
$graph->Add($p1);
$graph->xaxis->title->Set("Niveau");
$graph->yaxis->title->Set("Population");
$graph->Stroke('image/stat_lvl.jpg'); 
?>