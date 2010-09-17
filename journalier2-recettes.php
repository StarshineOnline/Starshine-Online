<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

// creation de $tableau_race
include_once('journalier2-archives.php');

//On vide la table argent_royaume
$requete = "UPDATE argent_royaume SET hv = 0, taverne = 0, forgeron = 0, armurerie = 0, magasin = 0, enchanteur = 0, ecole_magie = 0, ecole_combat = 0, teleport = 0, monstre = 0";
$req = $db->query($requete);

//Somme de l'honneur des joueurs du royaume
$requete = "SELECT SUM(honneur) as tot, race as race_joueur FROM perso WHERE statut = 'actif' GROUP BY race ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][12] = $row['tot'];
}
//Somme des levels
$requete = "SELECT SUM(level) as tot, race as race_joueur FROM perso WHERE statut = 'actif' GROUP BY race ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][13] = $row['tot'];
}
//Constructions hors ville
foreach(array_keys($tableau_race) as $race)
{
  // valeur par défaut (pour s'il n'y a pas d'évènement)
  $tableau_race[$race][14] = 0;
}
$requete = "SELECT royaume.race as race_joueur, SUM( cout ) AS tot FROM `construction` LEFT JOIN batiment ON construction.id_batiment = batiment.id LEFT JOIN royaume ON construction.royaume = royaume.id GROUP BY royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][14] = $row['tot'];
}
//Case controllées
$requete = "SELECT COUNT(*) as tot, royaume.race as race_joueur FROM `map` LEFT JOIN royaume ON royaume.id = map.royaume WHERE royaume <> 0 GROUP BY royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][15] = $row['tot'];
}
//Construction en ville
$requete = "SELECT royaume.race as race_joueur, SUM( cout ) AS tot FROM `construction_ville` LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id LEFT JOIN royaume ON construction_ville.id_royaume = royaume.id GROUP BY id_royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][16] = $row['tot'];
}
//Quètes
$requete = "SELECT royaume.race as race_joueur, SUM( star_royaume ) AS tot FROM `quete_royaume` LEFT JOIN quete ON quete_royaume.id_quete = quete.id LEFT JOIN royaume ON quete_royaume.id_royaume = royaume.id GROUP BY id_royaume ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tableau_race[$row['race_joueur']][17] = $row['tot'];
}

$i = 0;
$keys = array_keys($tableau_race);
while($i < count($tableau_race))
{
	$tableau_races[$keys[$i]] = implode(';', $tableau_race[$keys[$i]]);
	$i++;
}

//Non modification de la nourriture
$requete = "SELECT `food` FROM `stat_jeu` WHERE `id` IN (SELECT MAX(id) FROM `stat_jeu`)";
$req = $db->query($requete);
$row = $db->read_row($req);
$nourriture = $row[0];

$requete = "INSERT INTO `stat_jeu` ( `id` , `date` , `barbare` , `elfebois` , `elfehaut` , `humain` , `humainnoir` , `nain` , `orc` , `scavenger` , `troll` , `vampire` , `mortvivant`, `niveau_moyen`, `nombre_joueur`, `nombre_monstre`, `food` ) VALUES (NULL , '".$date."', '".$tableau_races['barbare']."', '".$tableau_races['elfebois']."', '".$tableau_races['elfehaut']."', '".$tableau_races['humain']."', '".$tableau_races['humainnoir']."', '".$tableau_races['nain']."', '".$tableau_races['orc']."', '".$tableau_races['scavenger']."', '".$tableau_races['troll']."', '".$tableau_races['vampire']."', '".$tableau_races['mortvivant']."', ".$niveau_moyen.", ".$nbr_perso.", ".$nbr_monstre.", ".$nourriture.");";
$req = $db->query($requete);

?>