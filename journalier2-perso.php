<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Verification des joueurs inactifs
$requete = "UPDATE perso SET statut = 'inactif' WHERE dernier_connexion <= ".(time() - (86400 * 21))." AND statut = 'actif'";
$db->query($requete);

//Réduction de l'honneur pour tous les joueurs
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.02) WHERE honneur <= reputation";
$db->query($requete);
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.03) WHERE honneur > reputation";
$db->query($requete);

	$G_crime_groupe[10]= 0.5;
	$G_crime_groupe[9] = 0.4;
	$G_crime_groupe[8] = 0.3;
	$G_crime_groupe[7] = 0.2;
	$G_crime_groupe[6] = 0.1;
	
	$requete = "SELECT ID FROM perso WHERE groupe > 0";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{	
		$perso = new perso($row[0]);
		$groupe = new groupe($perso->get_id_groupe());
		$groupe->get_membre_joueur();
		foreach($groupe->membre_joueur as $membre_id)
		{
			if($membre_id->get_id() != $perso->get_id())
			{
				$requete2 = "SELECT ".$membre_id->get_race()." FROM diplomatie WHERE race = '".$perso->get_race()."'";
				$req2 = $db->query($requete2);
				$row2 = $db->read_row($req2);
				if ($row2[0] > 5 AND $row2[0] < 127)
				{
					$points = $G_crime_groupe[$row2[0]];
					$perso->set_crime($perso->get_crime() + $points);
					$perso->sauver();
				}
			}
		}
	}
//Ancienne version de la diminution des points de crime.
//Point de crime -1
//$requete = "UPDATE perso SET crime = IF(crime - 1 < 0, 0, crime -1) WHERE crime > 0";
//$db->query($requete);

?>
