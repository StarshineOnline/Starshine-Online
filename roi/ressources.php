<?php
require('haut_roi.php');

$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
$requete = "SELECT ".$R['race']." FROM stat_jeu WHERE date = '".$date_hier."'";
$req = $db->query($requete);
$row = $db->read_assoc($req);
$explode_stat = explode(';', $row[$R['race']]);

$pierre = $explode_stat[18];
$bois = $explode_stat[19];
$eau = $explode_stat[20];
$sable = $explode_stat[21];
$charbon = $explode_stat[22];
$em = $explode_stat[23];
$star = $explode_stat[24];
$nourriture = $explode_stat[25];

echo '
Pierre : '.$pierre.'<br />
Bois : '.$bois.'<br />
Eau : '.$eau.'<br />
Sable : '.$sable.'<br />
Charbon : '.$charbon.'<br />
Essence Magique : '.$em.'<br />
Star : '.$star.'<br />
Nourriture : '.$nourriture.'<br />
';
?>