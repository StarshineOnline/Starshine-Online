<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include (root."../jpgraph/src/jpgraph.php");
	include (root."../jpgraph/src/jpgraph_pie.php");
	include (root."../jpgraph/src/jpgraph_pie3d.php");
	include (root."../jpgraph/src/jpgraph_line.php");
	include (root."../jpgraph/src/jpgraph_bar.php");
	include (root."../jpgraph/src/jpgraph_scatter.php");
	include (root."../jpgraph/src/jpgraph_regstat.php");
	include_once(root.'admin/menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Statistiques Royaumes
	</div>
	<?php
	$sources[0] = 'HV';
	$sources[1] = 'Taverne';
	$sources[2] = 'Forgeron';
	$sources[3] = 'Armurerie';
	$sources[4] = 'Alchimiste';
	$sources[5] = 'Enchanteur';
	$sources[6] = 'Ecole Magie';
	$sources[7] = 'Ecole Combat';
	$sources[8] = 'Teleport';
	$sources[9] = 'Monstres';
	$date = date("Y-m-d");
	$requete = "SELECT *, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date;";
	$req = $db->query($requete);
	$strips = array('id', 'date', 'year', 'day', 'month', 'niveau_moyen', 'nombre_joueur', 'nombre_monstre');
	$z = 0;
	while($row = $db->read_assoc($req))
	{
		$keys = array_keys($row);
		$j = 0;
		while($j < count($keys))
		{
			if(!in_array($keys[$j], $strips))
			{
				$donnees = explode(';', $row[$keys[$j]]);
				if(count($donnees) > 2)
				{
					$data[0] += $donnees[2];
					$data[1] += $donnees[3];
					$data[2] += $donnees[4];
					$data[3] += $donnees[5];
					$data[4] += $donnees[6];
					$data[5] += $donnees[7];
					$data[6] += $donnees[8];
					$data[7] += $donnees[9];
					$data[8] += $donnees[10];
					$data[9] += $donnees[11];
				}
			}
			$j++;
		}
		$z++;
	}
	//Création de l'image
	$graph = new Graph(700, 400, "auto");
	$graph->SetScale("textlin");
	$graph->SetShadow();
	
	$graph->title->Set("Répartition des taxes");
	//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
	
	$p1 = new BarPlot($data);
	$p1->SetShadow();
	$p1->SetWidth(0.7);
	$p1->value->Show();
	//$p1->value->SetFont(FF_ARIAL, FS_BOLD, 10);
	//$p1->value->SetAngle(45);
	//$p1->value->SetFormat('%d');
	$graph->Add($p1);
	$graph->xaxis->SetTickLabels($sources);
	$graph->Stroke('image/test_admin.jpg');
	?>
	<img src="image/test_admin.jpg" />
	<?php
	include_once(root.'bas.php');
}
?>