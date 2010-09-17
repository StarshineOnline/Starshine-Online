<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

if(date("N") == 1)
{
	//Attribution des grades
	require_once('grade.php');
	//Les rois peuvent de nouveau se téléporter
	$requete = "UPDATE perso SET teleport_roi = 'false' WHERE rang_royaume = 6";
	$db->query($requete);

	$semaine = (60 * 60 * 24 * 7) - 3600;
	$fin_vente = time() + $semaine;
	//Mis en vente de nouveaux terrains
	foreach($tableau_race as $race => $stats)
	{
		$nb_terrains = floor($stats[15] / 500);
		$i = 0;
		while($i < $nb_terrains)
		{
			$requete = "INSERT INTO vente_terrain (id_royaume, date_fin, id_joueur, prix) VALUES (".$Trace[$race]['numrace'].", ".$fin_vente.", 0, 5000)";
			$db->query($requete);
			$i++;
		}
	}
}

?>