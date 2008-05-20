<?php
$data = array();
$legend = array();
$label = array();
$requete = "SELECT LOWER(nom) as name, rang FROM classe WHERE rang > 0 ORDER BY rang";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$classes[$row['name']]['rang'] = $row['rang'];
	$classes[$row['name']]['total'] = 0;
}

$rang = array(false, false, false, false);

$requete = "SELECT classe, COUNT(*) as total, classe.rang as rang FROM perso RIGHT JOIN classe ON perso.classe_id = classe.id WHERE statut = 'actif' GROUP BY classe ORDER BY classe.rang ASC";
$req = $db->query($requete);
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
		$graph = new PieGraph(700, 400, "auto");
		$graph->SetShadow();
		
		$graph->title->Set("Répartition de la population par classe - Rang ".$i.", le ".$date);
		//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
		
		$p1 = new PiePlot3D($data[$i]);
		$p1->SetLabels($label[$i]);
		$p1->SetSize(0.5);
		$p1->SetCenter(0.45);
		//$p1->SetLegends($legend);
		$p1->SetLabelPos(0.6);
		$graph->Add($p1);
		$graph->Stroke('image/stat_classe'.$i.'.jpg');
	}
}
?>