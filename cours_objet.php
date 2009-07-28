<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_pie.php");
include ("jpgraph/src/jpgraph_pie3d.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_bar.php");

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'menu_admin.php');
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Cours du "<?php echo $_GET['objet']; ?>" depuis le début du jeu
			</div>
			<ul>
	<?php
	$dates = array();
	$requete = "SELECT *, EXTRACT(YEAR FROM time) as annee, EXTRACT(MONTH FROM time) as mois FROM journal WHERE action = 'vend' AND valeur = '".$_GET['objet']."'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$anmois = $row['annee'].'-'.$row['mois'];
		$objets[$anmois]['total'] += 1;
		$objets[$anmois]['somme'] += $row['valeur2'];
		$objets[$anmois]['nom'] = $anmois;
		$objets[$anmois]['liste'][] = $row['valeur2'];
	}
	$i = 0;
	$count = count($objets);
	$keys = array_keys($objets);
	while($i < $count)
	{
		//Si données non significatives (plus de 2 echanges)
		if($objets[$keys[$i]]['total'] > 2)
		{
			$moyenne = $objets[$keys[$i]]['somme'] / $objets[$keys[$i]]['total'];
			$j = 0;
			$count_liste = count($objets[$keys[$i]]['liste']);
			while($j < $count_liste AND $count_liste > 1)
			{
				$valeur = intval($objets[$keys[$i]]['liste'][$j]);
				if($valeur < (0.1 * $moyenne) OR $valeur > (3 * $moyenne))
				{
					$objets[$keys[$i]]['somme'] -= $valeur;
					$objets[$keys[$i]]['total'] -= 1;
					//echo $objets[$keys[$i]]['nom'].' - '.$moyenne.' -> '.$valeur.' '.$keys[$i].' '.$j.'<br />';
				}
				$j++;
			}
			$moyenne = $objets[$keys[$i]]['somme'] / $objets[$keys[$i]]['total'];
			$dates[] = $keys[$i];
			$data[] = $moyenne;
		}
		$i++;
	}
	?>
	<table>
	<?php
	//GRAPHES cours de l'objet
	$graph = new Graph(900, 400, "auto");
	$graph->SetShadow();
	
	$graph->SetMarginColor('white');
	$graph->SetScale("textlin");
	$graph->SetFrame(false);
	$graph->SetMargin(50,120,30,30);
	
	$graph->tabtitle->Set('Cours du "'.$_GET['objet'].'"');
	//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
	
	$graph->yaxis->HideZeroLabel();
	$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
	$graph->xgrid->Show();
	
	$graph->xaxis->SetTickLabels($dates);
	
	// Create lines
	$p1 = new LinePlot($data);
	$p1 ->SetWeight(2);
	$graph->Add($p1);
	
	$graph->legend->SetShadow('gray@0.4',5);
	$graph->legend->SetPos(0, 0.1, "right", "top");
	// Output line
	$graph->Stroke('image/cours.jpg');

	?>
	<img src="image/cours.jpg" />
	<?php
	//array_multisort($objets, SORT_DESC);
	foreach($objets as $objet)
	{
		if($objet['total'] > 0)
		{
			$moyen = ($objet['somme'] / $objet['total']);
			$requete = "SELECT prix FROM objet WHERE nom = '".$objet['nom']."'";
			$req = $db->query($requete);
			if($db->num_rows > 0)
			{
				$row = $db->read_assoc($req);
				$prix_max = $row['prix'] * 8;
				$prix_moyen = $prix_max / 20;
				$pourcent = ($moyen - $prix_mini) / ($prix_max) * 100;
			}
			else
			{
				$prix_max = 'inconnu';
				$pourcent = '';
				$prix_mini = '';
			}
			echo '
			<tr>
				<td>
					'.$objet['total'].' <strong>'.$objet['nom'].'</strong>
				</td>
				<td>
					au prix moyen de : '.$moyen.'
				</td>
				<td>
					 -> Max : '.$prix_max.' -> Moyen  '.$prix_moyen.'
				</td>
				<td>
					'.$pourcent.'
				</td>
			</tr>';
		}
	}
}
	?>
			</table>
		</div>
	</div>