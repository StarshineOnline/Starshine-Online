<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require_once('haut_roi.php');

$requete = "select sum(level)/count(id) moy from perso WHERE statut = 'actif'";
$req = $db->query($requete);
$row = $db->read_row($req);
$ref_ta = floor($row[0] - 1.5); // Bastien : on fait -1.5 pour eviter
if ($ref_ta < 1)                // les escaliers, il faut qu'une race
  $ref_ta = 1;                  // soit vraiment a la bourre pour
                                        // creer des grosses marches
//On récupère le nombre d'habitants très actifs suivant le niveau moyen


$semaine = time() - (3600 * 24 * 7);
$royaumes = array();
if ($ref_ta > 3)
{
	echo "Niveau de référence pour l'entretien: 4";
	$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > 3 AND dernier_connexion > $semaine GROUP BY race";
} else {
	echo "Niveau de référence pour l'entretien: $ref_ta";
	$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > $ref_ta AND dernier_connexion > $semaine GROUP BY race";
}
$req = $db->query($requete);
while($row = $db->read_row($req))
{
	$habitants[$row[0]] = $row[1];
}
$min_habitants = @min($habitants);
if($min_habitants > 0) $ratio = $habitants[$royaume->get_race()] / $min_habitants;
if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_economie())
	echo '<p>Cette page vous est interdite</p>';
else 
{
if($ratio < 1) $ratio = 1;
echo ' , Multiplicateur d\'entretien : '.$ratio.'<br />';
echo '
<fieldset class="tier">
<legend>Batiments interne</legend>
			<ul>';
			$requete = "SELECT *, construction_ville.id as id_const FROM construction_ville RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE construction_ville.statut = 'actif' AND id_royaume = ".$royaume->get_id()." ORDER BY entretien DESC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$entretien = ceil($row['entretien'] * $ratio);
				if($ratio != 1) $base = ' <span class="xsmall">('.$row['entretien'].')</span>';
				else $base = '';
				echo '<li><span class="nom">'.$row['nom'].'</span><span class="cout">: -'.$entretien.' '.$base.'</span></li>';
				$royaumes[$row['id_royaume']]['batiments'][$row['id_const']] = $entretien;
				$royaumes[$row['id_royaume']]['total'] += $entretien;
			}
			echo '
				<li style="border-top:1px dashed #FFF;"><span class="nom">Sous Total</span><span class="cout">-'.$royaumes[$royaume->get_id()]['total'].'</span></li>
				</ul>';
				
			//PHASE 2, entretien des batiments externes
			//On récupère les couts d'entretiens
			echo '
				</fieldset>
				<fieldset class="tier">
				<legend>Batiments externe</legend>';
			$requete = "SELECT *, construction.id AS id_const, batiment.nom AS nom_b, construction.x AS x_c, construction.y AS y_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id WHERE royaume = ".$royaume->get_id()." ORDER BY entretien DESC";
			$req = $db->query($requete);
			echo "<ul>";
			while($row = $db->read_assoc($req))
			{
				$entretien = ceil($row['entretien'] * $ratio);
				if($ratio != 1) $base = ' <span class="xsmall">('.$row['entretien'].')</span>';
				else $base = '';
				echo '
				<li><span class="nom">'.$row['nom_b'].' ('.$row['x_c'].' - '.$row['y_c'].')</span><span class="cout">: -'.$entretien.''.$base.'</span></li>';
				$royaumes[$row['royaume']]['total_c'] += $entretien;
			}
			echo '
				<li style="border-top:1px dashed #FFF;"><span class="nom">Sous Total </span><span class="cout">-'.$royaumes[$royaume->get_id()]['total_c'].'</span></li>
				</ul>
</fieldset>


<fieldset class="tier">
			<legend>Recette, taxe (hier)</legend>
			<ul>
			';
			$sources[2] = 'Hotel des ventes';
			$sources[3] = 'Taverne';
			$sources[4] = 'Forgeron';
			$sources[5] = 'Armurerie';
			$sources[6] = 'Alchimiste';
			$sources[7] = 'Enchanteur';
			$sources[8] = 'Ecole de Magie';
			$sources[9] = 'Ecole de Combat';
			$sources[10] = 'Teleportation';
			$sources[11] = 'Monstres';
			$sources[24] = 'Mines';
			if(date("G") > 4) $time = mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
			else $time = mktime(0, 0, 0, date("m") , date("d")-2, date("Y"));
			$requete = "SELECT ".$royaume->get_race()." FROM stat_jeu WHERE date = '".date("Y-m-d", $time)."'";
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$stats = explode(';', $row[$royaume->get_race()]);
			$i = 0;
			$total = 0;
			$count = count($stats);
			while($i < $count)
			{
				if(array_key_exists($i, $sources))
				{
					echo '
					<li><span class="nom">'.$sources[$i].' : </span><span class="cout"> +'.$stats[$i].'</span></li>';
					$total += $stats[$i];
				}
				$i++;
			}
			echo '
			<li style="border-top:1px dashed #FFF;"><span class="nom">TOTAL</span><span class="cout"> +'.$total.'</span></li>';
			echo "</ul></fieldset>";
			$balance = $total - ($royaumes[$royaume->get_id()]['total_c'] + $royaumes[$royaume->get_id()]['total']);
			if($balance > 0)
			{
				$class='green';
				$balance = '+ '.$balance;
			} else $class='red';
			?>
<fieldset class="tier">
			<legend>Balance</legend>
			<ul>
			<?php
			echo '<li><span class="nom">Entretiens</span><span class="cout">-'.($royaumes[$royaume->get_id()]['total_c'] + $royaumes[$royaume->get_id()]['total']).'</span><li>
			<li><span class="nom">Recette</span><span class="cout"> +'.$total.'</span></li>
			<li style="border-top:1px dashed #FFF;" class='.$class.'><span class="nom">Total</span><span class="cout">'.$balance.'</span></li>';
			?>
			</ul>
			
</fieldset>			
<?php
if(date("G") > 4) $time = mktime(0, 0, 0, date("m") , date("d")-1, date("Y"));
else $time = mktime(0, 0, 0, date("m") , date("d")-2, date("Y"));
$requete = "SELECT ".$royaume->get_race().", date, UNIX_TIMESTAMP(date) as stamp FROM stat_jeu WHERE EXTRACT(YEAR_MONTH FROM date) = '".date("Ym", time())."'";
$req = $db->query($requete);
$total_source = array();
$total_total = 0;
$jours = 0;
$data = array();
while($row = $db->read_array($req))
{
	$stats = explode(';', $row[$royaume->get_race()]);
	$i = 0;
	$total = 0;
	$count = count($stats);
	while($i < $count)
	{
		if(array_key_exists($i, $sources))
		{
			$data[$sources[$i]][$row['stamp']] = $stats[$i];
			$total += $stats[$i];
			$total_total += $stats[$i];
			$total_source[$i] += $stats[$i];
		}
		$i++;
	}
	$jours++;
}
?>
		<div id="graph_recette" style="float : left;">
			<div id="placeholder_recette" style="width:900px;height:300px;"></div>
		<script id="source" language="javascript" type="text/javascript">
			<?php
			$d = array();
			foreach($data as $ressource => $da)
			{
				$d = array();
				foreach($da as $date => $m)
				{
					$d[] = '['.$date.'*1000,'.$m.']';
				}
				?>
				<?php
				$datas[] = '{data:['.implode(', ', $d).'], name: "'.$ressource.'"}';
			}
			?>
		$(document).ready(function()
		{
			chart_recette = new Highcharts.Chart({
			 chart: {
				renderTo: 'placeholder_recette',
				defaultSeriesType: 'column'
			 },
			 title: {
				text: 'Recettes'
			 },
			 xAxis: {type: 'datetime'},
			 yAxis: {min : 0},
			 plotOptions:
			 {
				column:
				{
					stacking: 'normal'
				}
			},
			 series: [<?php echo implode(', ', $datas); ?>]
		  });
		});
		</script>
		</div>

<table style="clear : both;">
<?php
echo '
<tr>
	<td>Total pour ce mois</td><td></td>
</tr>';
$datas = array();
foreach($total_source as $key => $value)
{
	$datas[] = '["'.$sources[$key].'",'.$value.']';
	$pourcent = round(($value / $total_total), 4) * 100;
		echo '
<tr>
	<td>'.$sources[$key].'</td><td> : +'.$value.'</td><td>'.$pourcent.'%</td>
</tr>';
}
$jours = $jours > 0 ? $jours : 1;
echo '
<tr>
	<td><h6>TOTAL</h6></td><td><h6> +'.$total_total.'</h6></td><td><h6> '.round(($total_total / $jours), 2).' / jour</h6></td>
</tr>';
}
?>
</table>
<br />
		<div id="graph_recette_total" style="float : left;">
			<div id="placeholder_recette_total" style="width:900px;height:600px;"></div>
		<script type="text/javascript">
		$(document).ready(function()
		{
			chart_recette_total = new Highcharts.Chart({
			 chart: {
				renderTo: 'placeholder_recette_total',
				plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false
			 },
			 title: {
				text: 'Recettes totales'
			 },
			 xAxis: {categories:'Total'},
			 yAxis: {min : 0},
			 tooltip: {
		         formatter: function() {
				    return ''+
					this.series.name +': '+ this.y +' ('+ Math.round(this.percentage) +'%)';
				}
			},
			 plotOptions: {
         pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
               enabled: true,
               formatter: function() {
                  return '<b>'+ this.point.name +'</b>: '+ this.y;
               }
            }
         }
      },
			 series: [{type: 'pie', data : [<?php echo implode(', ', $datas); ?>]}]
		  });
		});
		</script>
		</div>
