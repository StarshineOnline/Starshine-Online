<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

// Mise en archive de la population de chaque royaume
echo "\nMise en archive des stars et population de chaque royaume\n";

$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
$req = $db->query($requete);
$total = 0; ///< Population totale
while($row = $db->read_array($req))
{
	$total += $row['total'];
	$tableau_race[$row['race']][0] = $row['total'];
	echo $row['race']." - Joueurs : ".$row['total'];
}

// Mise en archive des stars de chaque royaume
$requete = "SELECT race, star FROM royaume";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	if($row['race'] != '') $tableau_race[$row['race']][1] = $row['star'];
	echo ' - Stars : '.$row['star']."\n";
}

//Nombre de joueurs total et niveau moyen
$requete = "SELECT COUNT(*), AVG(level) FROM perso WHERE statut = 'actif'";
$req = $db->query($requete);
$row = $db->read_row($req);
$nbr_perso = $row[0];  ///< Nombre de personnages actifs
$niveau_moyen = $row[1];  ///< Niveau moyen
$level_moyen = ceil($row[1]); ///< Arrondi par excés du niveau moyen

//Nombre de monstres total
$requete = "SELECT COUNT(*) FROM map_monstre";
$req = $db->query($requete);
$row = $db->read_row($req);
$nbr_monstre = $row[0]; ///< Nombre de monstres

//On récupère comment est gagné l'argent du royaume
$requete = "SELECT * FROM argent_royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race']][2] = $row['hv'];
	$tableau_race[$row['race']][3] = $row['taverne'];
	$tableau_race[$row['race']][4] = $row['forgeron'];
	$tableau_race[$row['race']][5] = $row['armurerie'];
	$tableau_race[$row['race']][6] = $row['magasin'];
	$tableau_race[$row['race']][7] = $row['enchanteur'];
	$tableau_race[$row['race']][8] = $row['ecole_magie'];
	$tableau_race[$row['race']][9] = $row['ecole_combat'];
	$tableau_race[$row['race']][10] = $row['teleport'];
	$tableau_race[$row['race']][11] = $row['monstre'];
}

?>