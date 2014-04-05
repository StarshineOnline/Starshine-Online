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
	error_reporting(E_ALL);

	include_once (root."../jpgraph/src/jpgraph.php");
	include_once (root."../jpgraph/src/jpgraph_pie.php");
	include_once (root."../jpgraph/src/jpgraph_pie3d.php");
	include_once (root."../jpgraph/src/jpgraph_line.php");
	include_once (root."../jpgraph/src/jpgraph_bar.php");
	include_once (root."../jpgraph/src/jpgraph_scatter.php");
	include_once (root."../jpgraph/src/jpgraph_regstat.php");
	include_once(root.'admin/menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Statistiques Royaumes
	</div>
  <form action="admin_stats_royaume.php" method="get">
    <label>Stat : </label>
    <select name="stat">
      <option value="0">Population</option>
      <option value="1">Stars</option>
      <option value="2">hotel de vente</option>
      <option value="3">taverne</option>
      <option value="4">forgeron</option>
      <option value="5">armurerie</option>
      <option value="6">alchimiste</option>
      <option value="7">enchanteur</option>
      <option value="8">école de magie</option>
      <option value="9">école de combat</option>
      <option value="10">téléportation</option>
      <option value="11">chasse</option>
      <option value="12">honneur</option>
      <option value="13">niveaux</option>
      <option value="14">coûts des bâtiments hors ville</option>
      <option value="15">cases contôlées</option>
      <option value="16">coûts des bâtiments de la ville</option>
      <option value="17">coûts des quêtes achetées</option>
      <option value="18">Pierre gagnée</option>
      <option value="19">Bois gagné</option>on>
      <option value="20">Eau gagnée</option>
      <option value="21">Sable gagné</option>
      <option value="22">Charbon gagné</option>
      <option value="23">Essence gagnée</option>
      <option value="24">Routes</option>
      <option value="25">Nourriture gagnée</option>
      <option value="26">Très actifs</option>
      <option value="27">Facteur d'entretien actuel</option>
      <option value="28">Facteur d'entretien théorique</option>
      <option value="29">Consommation de nouriture actuelle</option>
      <option value="30">Consommation de nouriture théorique</option>
    </select>
    <input type="submit" value="Afficher"/>
  </form>
	<?php
    if( array_key_exists('stat', $_GET) )
    {
      $stat = $_GET['stat'];
      $date = date("Y-m-d");
      $requete = 'SELECT *, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB("'.$date.'", INTERVAL 31 DAY) ORDER BY date;';
	    $req = $db->query($requete);
      $z = 0;
      $data = array();
      $dates = array();
      include_once(root.'inc/race.inc.php');
      $races = array_keys($Trace);
    	foreach($races as $r)
    	{
        $data[$r] = array();
      }
      while( $row = $db->read_assoc($req) )
      {
      	$dates[] = $row['month'].'-'.$row['day'];
      	$keys = array_keys($row);
      	$j = 0;
      	foreach($races as $r)
      	{
  				$donnees = explode(';', $row[$r]);
  				if(count($donnees) > $stat)
  				{
  					$data[$r][] = $donnees[$stat];
  				}
  				else $data[$r][] = 0;
      	}
      }
      //Couleur des royaumes
      $color['barbare'] = array(0, 104, 255);
      $color['elfebois'] = array(0, 153, 0);
      $color['troll'] = array(255, 0, 0);
      $color['scavenger'] = array(255, 255, 0);
      $color['orc'] = array(255, 204, 204);
      $color['nain'] = array(255, 165, 0);
      $color['mortvivant'] = array(92, 30, 0);
      $color['humainnoir'] = array(0, 0, 0);
      $color['humain'] = array(0, 0, 255);
      $color['elfehaut'] = array(170, 170, 170);
      $color['vampire'] = array(130, 30, 130);
  	  $graph = new Graph(700, 400);
      $graph->SetScale('textint');
      $z = 0;
      foreach($races as $r)
      {
        $lineplot = new LinePlot($data[$r]);
        $graph->Add($lineplot);
      }
      $img = 'images/stat.jpg';
      if(file_exists($img))
        unlink($img);
  	  $graph->Stroke($img);
      echo '<img src="'.$img.'" />';
    }

	/*$sources[0] = 'HV';
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
				$data = array_fill(0, 10, 0);
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
	$graph->Stroke('images/test_admin.jpg');*/
	?>
	<!--<img src="images/test_admin.jpg" />-->
	<?php
	include_once(root.'bas.php');
}
?>