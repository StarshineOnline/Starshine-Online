<?php
include('haut.php');
$requete = "SELECT ID, x, y, hp, mp FROM perso WHERE statut = 'actif' AND level >= 5";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	echo $row['ID'].','.$row['hp'].','.$row['mp'].','.$row['x'].','.$row['y'].';';
}
$requete = "UPDATE perso SET hp = floor(hp_max), mp = floor(mp_max), x = 24, y = 209 WHERE statut = 'actif' AND level >= 5";
//$db->query($requete);
?>
