<?php
$data = array();
$legend = array();
$label = array();
$dates = array();

$date = date("Y-m-d");
$requete = "SELECT niveau_moyen, nombre_joueur, nombre_monstre, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 31 DAY) ORDER BY date;";
$req = $db->query($requete);
$strips = array('id', 'date', 'year', 'day', 'month');
while($row = $db->read_assoc($req))
{
	$dates[] = $row['month'].'-'.$row['day'];
	$keys = array_keys($row);
	$j = 0;
	while($j < count($keys))
	{
		if(!in_array($keys[$j], $strips))
		{
				$data[$keys[$j]][] = $row[$keys[$j]];
		}
		$j++;
	}
	/*$data[] = $row['total'];
	$legend[] = $row['race'].'('.$row['total'].')';
	$label[] = $row['race']."(".$row['total'].")\n%.1f%%";*/
}
include_once( '../ofc/ofc-library/open-flash-chart.php' );
$date = date("Y-m-d");
if(@mkdir('../stat/'.$date)) echo 'Répertoire '.$date.' créé<br />'; echo 'Le répertoire '.$date.' existe déjà<br />';

//GRAPHES NBR JOUEURS
// use the chart class to build the chart:
$g = new graph_flash();
$g->bg_colour = '#E4F5FC';
$g->set_inner_background( '#E3F0FD', '#CBD7E6', 90 );
// Spoon sales, March 2007
$g->title( 'Evolution du nombre de joueurs le '.$date, '{font-size: 12px; color: #800000}' );

$g->set_data( $data['nombre_joueur'] );
$g->line_hollow( 2, 4, '#447799', 'Nombre de joueurs', 10 );
// label each point with its value
$g->set_x_labels( $dates );
$g->set_x_label_style( 10, '0x9933CC', 2 );

// set the Y max
$max = max($data['nombre_joueur']);
$min = min($data['nombre_joueur']);
$max_min = $max - $min;
$g->set_y_max( $max + ceil($max_min * 0.05) );
$g->set_y_min( $min - floor($max_min * 0.05) );
$g->set_tool_tip( 'Le #x_label# : #val# joueurs' );
// label every 20 (0,20,40,60)
$g->y_label_steps( 10 );

// display the data
// display the data
$fichier = '../stat/'.$date.'/stat_joueur.data';
$f = fopen($fichier, "w");
fwrite($f, $g->render());
fclose($f);

//GRAPHES NBR MONSTRES
$g = new graph();
$g->bg_colour = '#E4F5FC';
$g->set_inner_background( '#E3F0FD', '#CBD7E6', 90 );
// Spoon sales, March 2007
$g->title( 'Evolution du nombre de monstres le '.$date, '{font-size: 12px; color: #800000}' );

$g->set_data( $data['nombre_monstre'] );
$g->line_hollow( 2, 4, '#447799', 'Nombre de monstres', 10 );
// label each point with its value
$g->set_x_labels( $dates );
$g->set_x_label_style( 10, '0x9933CC', 2 );

// set the Y max
$max = max($data['nombre_monstre']);
$min = min($data['nombre_monstre']);
$max_min = $max - $min;
$g->set_y_max( $max + ceil($max_min * 0.05) );
$g->set_y_min( $min - floor($max_min * 0.05) );
$g->set_tool_tip( 'Le #x_label# : #val# monstres' );
// label every 20 (0,20,40,60)
$g->y_label_steps( 10 );

// display the data
// display the data
$fichier = '../stat/'.$date.'/stat_monstre.data';
$f = fopen($fichier, "w");
fwrite($f, $g->render());
fclose($f);

//GRAPHES NBR NIVEAUX MOYEN
$g = new graph();
$g->bg_colour = '#E4F5FC';
$g->set_inner_background( '#E3F0FD', '#CBD7E6', 90 );
// Spoon sales, March 2007
$g->title( 'Evolution du niveau moyen le '.$date, '{font-size: 12px; color: #800000}' );

$g->set_data( $data['niveau_moyen'] );
$g->line_hollow( 2, 4, '#447799', 'Niveau moyen', 10 );
// label each point with its value
$g->set_x_labels( $dates );
$g->set_x_label_style( 10, '0x9933CC', 2 );

// set the Y max
$max = max($data['niveau_moyen']);
$min = min($data['niveau_moyen']);
$max_min = $max - $min;
$g->set_y_max( $max + ceil($max_min * 0.05) );
$g->set_y_min( $min - floor($max_min * 0.05) );
$g->set_tool_tip( 'Le #x_label#, niveau moyen : #val#' );
// label every 20 (0,20,40,60)
$g->y_label_steps( 10 );

// display the data
$fichier = '../stat/'.$date.'/stat_niveau_moyen.data';
$f = fopen($fichier, "w");
fwrite($f, $g->render());
fclose($f);
?>