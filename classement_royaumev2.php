<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	function point_royaume($tab_royaume)
	{
		$tab_point = array(
		'quete' => 100,
		'consthv' => 200,
		'constv' => 200,
		'star' => 50,
		'honneur' => 4,
		'level' => 4000,
		'case' => 1000,
		);
		$somme = array();
		$count = count($tab_royaume);
		$keys = array_keys($tab_royaume);
		$i = 0;
		while($i < $count)
		{
			$j = 0;
			$count_p = count($tab_point);
			$keys_p = array_keys($tab_point);
			while($j < $count_p)
			{
				$tab_royaume[$keys[$i]]['point_'.$keys_p[$j]] = $tab_royaume[$keys[$i]][$keys_p[$j]] * $tab_point[$keys_p[$j]];
				$somme[$keys_p[$j]] += $tab_royaume[$keys[$i]]['point_'.$keys_p[$j]];
				$total += $tab_royaume[$keys[$i]]['point_'.$keys_p[$j]];
				$tab_royaume[$keys[$i]]['point_total'] += $tab_royaume[$keys[$i]]['point_'.$keys_p[$j]];
				$j++;
			}
			$i++;
		}
		$count = count($tab_royaume);
		$keys = array_keys($tab_royaume);
		$i = 0;
		while($i < $count)
		{
			$moyenne = $total / 11;
			$tab_royaume[$keys[$i]]['score'] = ($tab_royaume[$keys[$i]]['point_total'] / $moyenne) * 100;
			$tab_royaume[$keys[$i]]['recette'] = $tab_royaume[$keys[$i]]['hv'] + $tab_royaume[$keys[$i]]['taverne'] + $tab_royaume[$keys[$i]]['forgeron'] + $tab_royaume[$keys[$i]]['armurerie'] + $tab_royaume[$keys[$i]]['magasin'] + $tab_royaume[$keys[$i]]['enchanteur'] + $tab_royaume[$keys[$i]]['ecole_magie'] + $tab_royaume[$keys[$i]]['ecole_combat'] + $tab_royaume[$keys[$i]]['teleport'] + $tab_royaume[$keys[$i]]['monstre'];
			$i++;
		}
		return $tab_royaume;
	}
	$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
	$requete = "SELECT EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day, barbare, elfebois, elfehaut, humain, humainnoir, nain, orc, scavenger, troll, vampire, mortvivant FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 30 DAY) ORDER BY date";
	$req = $db->query($requete);
	$suppr = array('year', 'day', 'month');
	while($row = $db->read_assoc($req))
	{
		$keys = array_keys($row);
		$i = 0;
		foreach($row as $r)
		{
			if(!in_array($keys[$i], $suppr))
			{
				$r_e = explode(';', $r);
				$jour = $row['year'].'-'.$row['month'].'-'.$row['day'];
				$tab_royaume[$jour][$keys[$i]]['actif'] = $r_e[0];
				$tab_royaume[$jour][$keys[$i]]['star'] = $r_e[1];
				$tab_royaume[$jour][$keys[$i]]['hv'] = $r_e[2];
				$tab_royaume[$jour][$keys[$i]]['taverne'] = $r_e[3];
				$tab_royaume[$jour][$keys[$i]]['forgeron'] = $r_e[4];
				$tab_royaume[$jour][$keys[$i]]['armurerie'] = $r_e[5];
				$tab_royaume[$jour][$keys[$i]]['magasin'] = $r_e[6];
				$tab_royaume[$jour][$keys[$i]]['enchanteur'] = $r_e[7];
				$tab_royaume[$jour][$keys[$i]]['ecole_magie'] = $r_e[8];
				$tab_royaume[$jour][$keys[$i]]['ecole_combat'] = $r_e[9];
				$tab_royaume[$jour][$keys[$i]]['teleport'] = $r_e[10];
				$tab_royaume[$jour][$keys[$i]]['monstre'] = $r_e[11];
				$tab_royaume[$jour][$keys[$i]]['honneur'] = $r_e[12];
				$tab_royaume[$jour][$keys[$i]]['level'] = $r_e[13];
				$tab_royaume[$jour][$keys[$i]]['consthv'] = $r_e[14];
				$tab_royaume[$jour][$keys[$i]]['case'] = $r_e[15];
				$tab_royaume[$jour][$keys[$i]]['constv'] = $r_e[16];
				$tab_royaume[$jour][$keys[$i]]['quete'] = $r_e[17];
			}
			$i++;
		}
	}
	include('menu.php');
	//Si le joueur est connecté on affiche le menu de droite
	?>
	<div id="contenu">
		<div id="centre2">
		<pre>
	<?php
	print_r($tab_royaume);
	
	echo '<br /><br /><br />#####<br /><br /><br />';
	
	$i = 0;
	$keys = array_keys($tab_royaume);
	foreach($tab_royaume as $t_royaume)
	{
		$dates[] = $keys[$i];
			$dates[] = $keys[$i];
			$tab_royaume[$keys[$i]] = point_royaume($t_royaume);
			print_r($tab_royaume[$keys[$i]]);
		$i++;
	}
	if(array_key_exists('race', $_GET))
	{
		include ("jpgraph/src/jpgraph.php");
		include ("jpgraph/src/jpgraph_pie.php");
		include ("jpgraph/src/jpgraph_pie3d.php");
		include ("jpgraph/src/jpgraph_line.php");
		include ("jpgraph/src/jpgraph_bar.php");
		include ("jpgraph/src/jpgraph_scatter.php");
		include ("jpgraph/src/jpgraph_regstat.php");
		$race = $_GET['race'];
		$var = $_GET['var'];

		foreach($tab_royaume as $tr)
		{
			$data[$var][] = $tr[$race][$var];
			if(array_key_exists('star', $_GET)) $data['star'][] = $tr[$race]['star'];
		}
		//GRAPHES NBR ACTIFS
		$graph = new Graph(900, 400, "auto");
		$graph->SetShadow();
		
		$graph->SetMarginColor('white');
		$graph->SetScale("textlin");
		$graph->SetFrame(false);
		$graph->SetMargin(50,120,30,30);
		
		$graph->tabtitle->Set($var.' '.$race);
		//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
		
		$graph->yaxis->HideZeroLabel();
		$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
		$graph->xgrid->Show();
		
		$graph->xaxis->SetTickLabels($dates);
		
		// Create lines
		if(array_key_exists('star', $_GET))
		{
			$p1 = new BarPlot($data[$var]);
		}
		else $p1 = new LinePlot($data[$var]);
		//$p1 ->SetWeight(2);
		$graph->Add($p1);
		if(array_key_exists('star', $_GET))
		{
			$p2 = new LinePlot($data['star']);
			$graph->SetYScale(0, 'lin');
			$graph->AddY(0, $p2);
		}
		
		
		$graph->legend->SetShadow('gray@0.4',5);
		$graph->legend->SetPos(0, 0.1, "right", "top");
		// Output line
		$graph->Stroke('image/stat/test_'.$var.'_'.$race.'.jpg');
	}
}
	?>