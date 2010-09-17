<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Fin des enchères
$ids = array();
$requete = "SELECT id, id_joueur, prix FROM vente_terrain WHERE date_fin <= ".time();
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$ids[] = $row['id'];
	if($row['id_joueur'] != 0)
	{
		$terrain = new terrain(0, $row['id_joueur'], 2);
		$terrain->sauver();
		$mail .= $row['id_joueur']." gagne un terrain pour ".$row['prix']." stars.\n";
	}
}
//On supprime les enchères finies
if(count($ids) > 0)
{
	$implode_ids = implode(', ', $ids);
	$requete = "DELETE FROM vente_terrain WHERE id IN (".$implode_ids.")";
	$db->query($requete);
}

?>