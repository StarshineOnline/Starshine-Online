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
	$race_nb = $tableau_race[$keys[$i]][0];
	$repartition = 15 - (($race_nb / $total) * 100);
	echo ' - '.$repartition;
	if($repartition > 0)
	{
		$stars = 70 + round($repartition * $repartition) * 3;
	}
	else $stars = 70;
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

// events
try
{
  $events = event::create('','', 'id ASC', false, 'statut < '.event::en_cours.' AND date_debut <= CURDATE()');
  foreach($events as $event)
  {
    $event->demarer();
  }
  $events = event::create('statut', event::en_cours);
  foreach($events as $event)
  {
    $event->journalier();
  }
}
catch(Exception $e)
{
  $mail .= 'Erreur gestion des events : '.$e->getMessage()."\n";
}


?>
