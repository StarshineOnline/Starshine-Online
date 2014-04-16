<?php
	if (file_exists('../root.php'))
	  include_once('../root.php');
	$admin = true;
	
	$textures = false;
include_once(root.'inc/fp.php');
	setlocale(LC_ALL, 'fr_FR');
	
	
	include_once (root."../jpgraph/src/jpgraph.php");
	include_once (root."../jpgraph/src/jpgraph_pie.php");
	include_once (root."../jpgraph/src/jpgraph_pie3d.php");
	include_once (root."../jpgraph/src/jpgraph_line.php");
	include_once (root."../jpgraph/src/jpgraph_bar.php");
	include_once (root."../jpgraph/src/jpgraph_scatter.php");
	include_once (root."../jpgraph/src/jpgraph_regstat.php");
	
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
	  $graph->Stroke();
  }

?>