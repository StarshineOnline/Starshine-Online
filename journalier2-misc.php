<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

$mail .= "\nStars pour les nouveaux joueurs\n\n";
//Calcul des stars pour nouveau joueur
$count = count($tableau_race);
print_r($tableau_race);
echo "\n";
$keys = array_keys($tableau_race);
$i = 0;
while($i < $count)
{
	$race_nb = $tableau_race[$keys[$i]][1];
	$repartition = 15 - (($race_nb / $total) * 100);
	echo ' - '.$repartition;
	if($repartition > 0)
	{
		$stars = round($repartition * $repartition) * 3;
	}
	else $stars = 0;
	if($keys[$i] != '')
	{
		$mail .= $keys[$i]." - ".$stars."\n";
		$requete = "UPDATE royaume SET star_nouveau_joueur = ".$stars." WHERE id = ".$Trace[$keys[$i]]['numrace'];
		echo " ($requete)";
		$db->query($requete);
	}
	$i++;
}
echo "\n";

?>