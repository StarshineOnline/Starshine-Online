<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

include_once ("jpgraph/src/jpgraph.php");
include_once ("jpgraph/src/jpgraph_pie.php");
include_once ("jpgraph/src/jpgraph_pie3d.php");
include_once ("jpgraph/src/jpgraph_line.php");
include_once ("jpgraph/src/jpgraph_bar.php");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
//Si on est le premier, élection du roi de chaque race
if(date("d") == 1)
{
	//Suppression des anciens rois
	$groupe = array();
	$groupe['barbare'][0] = 15;
	$groupe['barbare'][1] = 19;
	$groupe['elfebois'][0] = 5;
	$groupe['elfebois'][1] = 20;
	$groupe['elfehaut'][0] = 12;
	$groupe['elfehaut'][1] = 21;
	$groupe['humain'][0] = 10;
	$groupe['humain'][1] = 22;
	$groupe['humainnoir'][0] = 13;
	$groupe['humainnoir'][1] = 23;
	$groupe['nain'][0] = 8;
	$groupe['nain'][1] = 24;
	$groupe['orc'][0] = 7;
	$groupe['orc'][1] = 25;
	$groupe['scavenger'][0] = 14;
	$groupe['scavenger'][1] = 26;
	$groupe['troll'][0] = 9;
	$groupe['troll'][1] = 27;
	$groupe['vampire'][0] = 11;
	$groupe['vampire'][1] = 28;
	$groupe['mortvivant'][0] = 16;
	$groupe['mortvivant'][1] = 18;
	
	require_once('connect_forum.php');
	foreach($groupe as $group)
	{
		$requete = "UPDATE punbbusers SET group_id = ".$group[0]." WHERE group_id = ".$group[1];
		$db_forum->query($requete);
	}
	$requete = "UPDATE perso SET rang_royaume = 7 WHERE rang_royaume = 6";
	$db->query($requete);
	//Groupe forum

	echo 'Election des rois';
	$requete = "SELECT * FROM royaume WHERE ID <> 0";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$data = array();
		$legend = array();
		$label = array();
		$requete = "SELECT *, COUNT(*) as count FROM vote WHERE royaume = ".$row['ID']." AND date = '".date("Y-m")."' GROUP BY id_candidat ORDER BY count DESC";
		echo $requete.'<br />';
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
			while($row_v = $db->read_assoc($req_v))
			{
				$requete = "SELECT * FROM perso WHERE ID = ".$row_v['id_candidat'];
				echo $requete.'<br />';
				$req_c = $db->query($requete);
				$row_c = $db->read_assoc($req_c);
				if($i == 0)
				{
					$graph = new PieGraph(700, 400, "auto");
					$graph->SetShadow();
					$graph->title->Set("Elections du roi ".$Gtrad[$row['race']]." du ".$row_v['date']);
					$requete = "UPDATE perso SET rang_royaume = 6 WHERE ID = ".$row_c['ID'];
					echo $requete.'<br />';
					$db->query($requete);
					$requete = "UPDATE punbbusers SET group_id = ".$groupe[$row_c['race']][1]." WHERE username = '".$row_c['nom']."'";
					echo $requete.'<br />';
					$db->query($requete);
					
				}
				$data[] = $row_v['count'];
				$legend[] = $row_c['nom'].'('.$row_v['count'].')';
				$label[] = $row_c['nom']."(".$row_v['count'].")\n%.1f%%";
				$i++;
			}
			
			//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
			
			$p1 = new PiePlot3D($data);
			$p1->SetLabels($label);
			$p1->SetSize(0.5);
			$p1->SetCenter(0.45);
			//$p1->SetLegends($legend);
			$p1->SetLabelPos(0.6);
			$graph->Add($p1);
			$graph->Stroke('image/election_'.$row['race'].'.jpg');
		}
	}
}
?>