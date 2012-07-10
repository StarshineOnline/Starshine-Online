<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

include_once ("jpgraph/src/jpgraph.php");
include_once ("jpgraph/src/jpgraph_pie.php");
include_once ("jpgraph/src/jpgraph_pie3d.php");
include_once ("jpgraph/src/jpgraph_line.php");
include_once ("jpgraph/src/jpgraph_bar.php");
include_once ("jpgraph/src/jpgraph_scatter.php");
include_once ("jpgraph/src/jpgraph_regstat.php");
$root = '';

$data = array();
$legend = array();
$label = array();
$dates = array();
$LARGEUR = 750;
$HAUTEUR = 400;

$date = date("Y-m-d");
$requete = "SELECT niveau_moyen, nombre_joueur, nombre_monstre, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, EXTRACT(DAY FROM date) as day FROM stat_jeu WHERE date > DATE_SUB('".$date."', INTERVAL 150 DAY) ORDER BY date;";
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

//GRAPHES NBR JOUEURS
$graph = new Graph($LARGEUR, $HAUTEUR, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du nombre de joueurs');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

//Changer la couleur
$graph->setColorPalette(0, 32, 38, 111);

// Create lines
$p1 = new LinePlot($data['nombre_joueur']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_joueur_plus.jpg');

//GRAPHES NBR MONSTRES
$graph = new Graph($LARGEUR, $HAUTEUR, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du nombre de monstres');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

//Changer la couleur
$graph->setColorPalette(0, 32, 38, 111);

// Create lines
$p1 = new LinePlot($data['nombre_monstre']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_monstre_plus.jpg');

//GRAPHES NBR NIVEAUX MOYEN
$graph = new Graph($LARGEUR, $HAUTEUR, "auto");
$graph->SetShadow();

$graph->SetMarginColor('white');
$graph->SetScale("textlin");
$graph->SetFrame(false);
$graph->SetMargin(50,120,30,30);

$graph->tabtitle->Set('Evolution du niveau moyen');
//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);

$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true, '#EFEFEF@0.5', '#BBCCFF@0.5');
$graph->xgrid->Show();

$graph->xaxis->SetTickLabels($dates);

//Changer la couleur
$graph->setColorPalette(0, 32, 38, 111);

// Create lines
$p1 = new LinePlot($data['niveau_moyen']);
$p1 ->SetWeight(2);
$graph->Add($p1);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->SetPos(0, 0.1, "right", "top");
// Output line
$graph->Stroke($root.'image/stat_niveau_moyen_plus.jpg');
?>