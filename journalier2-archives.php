<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

// Mise en archive de la population de chaque royaume
$mail .= "\nJoueur et Stars de chaque royaume\n\n";
echo "Mise en archive des stars et population de chaque royaume\n";
/**
 * Stats à mémoriser pour chaque race.
 * Tableau bi-dimensionnel, la première clé est la race, la deuxième un indice indexant
 * les stats à conserver :
 * <ol>
 *  <li>Stars du royaume</li>
 *  <li>Population du royaume</li>
 *  <li>Argent gagné par l'hotel de vente</li> 
 *  <li>Argent gagné par la taverne</li>
 *  <li>Argent gagné par le forgeron</li>
 *  <li>Argent gagné par l'armurerie</li>
 *  <li>Argent gagné par l'alchimiste</li>
 *  <li>Argent gagné par l'enchanteur</li>
 *  <li>Argent gagné par l'école de magie</li>
 *  <li>Argent gagné par l'école de combat</li>
 *  <li>Argent gagné par la téléportation</li>
 *  <li>Argent gagné par le chasse</li>
 *  <li>Somme de l'honneur</li>
 *  <li>Somme des niveaux</li>
 *  <li>Total des coûts des bâtiments hors ville</li>
 *  <li>Nombre de cases contôlées</li> 
 *  <li>Total des coûts des bâtiments de la ville</li>
 *  <li>Total des coûts des quêtes achetées</li>
 *  <li>Pierre gagnée par les terrains, mines et extracteurs</li>
 *  <li>Bois gagnée par les terrains, scieries et extracteurs</li>
 *  <li>Eau gagnée par les terrains, puits et extracteurs</li>
 *  <li>Sable gagnée par les terrains, carrière de sable et extracteurs</li>
 *  <li>Charbon gagnée par les terrains, meules et extracteurs</li>
 *  <li>Essence Magique gagnée par les terrains, puits à essence et extracteurs</li>
 *  <li>Star gagnée par les terrains et extracteurs</li>
 *  <li>Nourriture gagnée par les terrains, fermes et extracteurs</li>
 * </ol>     
 */
$tableau_race = array();
$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
$req = $db->query($requete);
$total = 0; ///< Population totale
while($row = $db->read_array($req))
{
	$total += $row['total'];
	$tableau_race[$row['race']][1] = $row['total'];
	$mail .= $row['race']." - Joueurs : ".$row['total']."\n";
}

// Mise en archive des stars de chaque royaume
$requete = "SELECT race, star FROM royaume";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	if($row['race'] != '') $tableau_race[$row['race']][0] = $row['star'];
	$mail .= " - Stars : ".$row['star']."\n";
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