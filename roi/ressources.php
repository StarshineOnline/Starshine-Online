<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'inc/ressource.inc.php');

function ressource($nom)
{
	$ressource['Pierre'] = 18;
	$ressource['Bois'] = 19;
	$ressource['Eau'] = 20;
	$ressource['Sable'] = 21;
	$ressource['Charbon'] = 22;
	$ressource['Essence Magique'] = 23;
	$ressource['Star'] = 24;
	$ressource['Nourriture'] = 25;
	return $ressource[$nom];
}
if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_economie())
	echo '<p>Cheater</p>';
else if(array_key_exists('ress', $_GET))
{
	include_once(root."pChart/pData.class");
	include_once(root."pChart/pChart.class");
	$date_semaine = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 7, date("Y")));
	$requete = "SELECT ".$royaume->get_race().", date FROM stat_jeu WHERE date > '".$date_semaine."' ORDER BY date ASC";
	$req = $db->query($requete);
	$data = array();
	$dates = array();
	while($row = $db->read_assoc($req))
	{
		$stat_race = $row[$royaume->get_race()];
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
	$graph->Render('../image/'.$royaume->get_race().'_'.$_GET['ress'].'.png');
	?>
	<img src="../image/<?php echo $royaume->get_race(); ?>_<?php echo $_GET['ress']; ?>.png" />
	<?php
}
else
{
	$requete = "SELECT ".$royaume->get_race()." FROM stat_jeu ORDER BY date DESC";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$explode_stat = explode(';', $row[$royaume->get_race()]);
	
	$hier['Pierre'] = $explode_stat[18];
	$hier['Bois'] = $explode_stat[19];
	$hier['Eau'] = $explode_stat[20];
	$hier['Sable'] = $explode_stat[21];
	$hier['Charbon'] = $explode_stat[22];
	$hier['Essence Magique'] = $explode_stat[23];
	$hier['Star'] = $explode_stat[24];
	$hier['Nourriture'] = $explode_stat[25];
	
	$requete = "SELECT info, COUNT(*) as tot_terrain FROM `map` WHERE royaume = ".$royaume->get_id()." GROUP BY info";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{

			$typeterrain = type_terrain($row['info']);
			$ressources[$typeterrain[1]] += $row['tot_terrain']/10;
			$terrain_ress[$typeterrain[1]] += $row['tot_terrain'];
	}
	//Ressource normale
	foreach($ress as $key_terr => $terr)
	{
		foreach($terr as $key => $res)
		{
			$ressource_final[$key] += $res * floor($ressources[$key_terr]);
			$ress_terrain[$key_terr][$key] +=  $res * floor($ressources[$key_terr]);
		}
	}
	//Ressource mine
	//On récupère la liste des batiments de type mine
	$batiment = array();
	$requete = "SELECT b.id, bp.valeur production, bs.valeur specialite 
FROM batiment b 
LEFT JOIN batiment_bonus bp ON bp.id_batiment = b.id and bp.bonus = 'production' 
LEFT JOIN batiment_bonus bs ON bs.id_batiment = b.id and bs.bonus = 'specialite' 
where b.type = 'mine'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$batiment[$row['id']] = $row;
	}
	//@TODO gérer les mines dans construction
	$requete = "SELECT * FROM construction LEFT JOIN map USING(x, y) WHERE construction.type = 'mine' AND construction.royaume = ".$royaume->get_id();
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$terrain = type_terrain($row['info']);
		$ress_terrain = $ress[$terrain[1]];
		if($batiment[$row['id_batiment']]['specialite'] != 0)
		{
			switch($batiment[$row['id_batiment']]['specialite'])
			{
				case 1 :
					$ress_final = array('Pierre' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Pierre']);
				break;
				case 2 :
					$ress_final = array('Bois' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Bois']);
				break;
				case 3 :
					$ress_final = array('Eau' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Eau']);
				break;
				case 4 :
					$ress_final = array('Sable' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Sable']);
				break;
				case 5 :
					$ress_final = array('Nourriture' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Nourriture']);
				break;
				case 6 :
					$ress_final = array('Star' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Star']);
				break;
				case 7 :
					$ress_final = array('Charbon' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Charbon']);
				break;
				case 8 :
					$ress_final = array('Essence Magique' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Essence Magique']);
				break;
			}			
		}
		else
		{
			$ress_final = array();
			foreach($ress_terrain as $key => $value)
			{
				$ress_final[$key] = $batiment[$row['id_batiment']]['production'] * $value;
			}

		}
		foreach($ress_final as $key => $value)
		{
			$ressource_mine_final[$key] += $value;
			$ress_mine_terrain[$key_terr][$key] +=  $res * $ressources[$key_terr];
		}
	}
	$liste_ressources = array();
	$liste_ressources[] = 'Pierre';
	$liste_ressources[] = 'Bois';
	$liste_ressources[] = 'Eau';
	$liste_ressources[] = 'Sable';
	$liste_ressources[] = 'Charbon';
	$liste_ressources[] = 'Essence Magique';
	$liste_ressources[] = 'Star';
	$liste_ressources[] = 'Nourriture';
	echo "
	<div id='ressource'>
	<ul>
	<li class='haut'>
		<span class='ressource_nom'>Ressource</span>
		<span class='ressource_gains'>Gains hier</span>
		<span class='ressource_case'>Cases actuellement</span>
		<span class='ressource_mines'>Mines actuellement</span>
		<span class='ressource_total'>Total actuellement</span>
	</li>
	";
	$class='t1';
	foreach($liste_ressources as $type_ressource)
	{
		if ($type_ressource == 'Essence Magique')
		{$class_ressource='essence';}else{	$class_ressource = strtolower($type_ressource);}
	 if(empty($ressource_mine_final[$type_ressource])){$ressource_mine_final[$type_ressource] = 0;}
	?>
	
	<li class='<?php echo $class; ?>' onclick="affichePopUp('ressources.php?ress=<?php echo $type_ressource; ?>');">
		<span class='ressource_nom'><span class='<?php echo $class_ressource; ?>'></span><?php echo $type_ressource; ?></span>
		<span class='ressource_gains'><?php echo $hier[$type_ressource]; ?></span>
		<span class='ressource_case'><?php echo $ressource_final[$type_ressource]; ?></span>
		<span class='ressource_mines'><?php  echo $ressource_mine_final[$type_ressource]; ?></span>
		<span class='ressource_total'><?php echo ($ressource_mine_final[$type_ressource] + $ressource_final[$type_ressource]); ?></span>
	</li>
	<?php
	if ($class == 't1'){$class = 't2';}else{$class = 't1';}		
	}
	
	?>
	</ul></div>
	<h3>Vous contrôlez </h3>
	<ul>
	<?php
	foreach($terrain_ress as $terrain => $total)
	{
		?>
		<li><?php echo $total; ?> cases de <?php echo $terrain; ?></li>
		<?php
	}
	?>
	</ul>
	<?php
}
?>
