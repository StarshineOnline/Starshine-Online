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
	$label[] = $row['level'];
}

/*
//Création de l'image
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
//$graph->Stroke('image/stat_lvl.jpg'); 
*/

//Création du Flash
// use the chart class to build the chart:
include_once('../ofc/ofc-library/open-flash-chart.php');
$g = new graph_flash();
$g->bg_colour = '#E4F5FC';
$g->set_inner_background( '#E3F0FD', '#CBD7E6', 90 );
// Spoon sales, March 2007
$g->title("Répartition de la population par niveau le ".$date, '{font-size: 12px; color: #800000}');

$g->set_data( $data );
$g->bar_3D( 75, '#225577', 'Niveau', 10 );
// label each point with its value
$g->set_x_axis_3d( 12 );
$g->x_axis_colour( '#909090', '#ADB5C7' );
$g->y_axis_colour( '#909090', '#ADB5C7' );
$g->set_x_labels( $label );
$g->set_x_label_style( 15, '0x9933CC', 0 );

// set the Y max
$max = max($data);
$g->set_y_max( $max );
$g->set_tool_tip( 'Niveau #x_label# : #val# joueurs' );
// label every 20 (0,20,40,60)
$g->y_label_steps( 10 );

// display the data
$date = date("Y-m-d");
if(@mkdir('../stat/'.$date)) echo 'Répertoire '.$date.' créé<br />'; echo 'Le répertoire '.$date.' existe déjà<br />';
$fichier = '../stat/'.$date.'/stat_lvl.data';
$f = fopen($fichier, "w");
fwrite($f, $g->render());
fclose($f);
?>