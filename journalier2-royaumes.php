<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Récupération de points de victoire
$requete = "UPDATE royaume r SET point_victoire = point_victoire + (select count(1) from map where type = 3 and royaume = r.id and r.id <> 0)";
$req = $db->query($requete);

//Entretien des batiments et constructions
$semaine = time() - (3600 * 24 * 7);
$royaumes = array();
// On récupère le niveau moyen
$requete = "select sum(level)/count(id) moy from perso WHERE statut = 'actif'";
$req = $db->query($requete);
$row = $db->read_row($req);
$moyenne_niveau = floor($row[0] - 1.5); // Bastien : on fait -1.5 pour eviter
if ($moyenne_niveau < 1)                // les escaliers, il faut qu'une race
  $moyenne_niveau = 1;                  // soit vraiment a la bourre pour
                                        // creer des grosses marches
//On récupère le nombre d'habitants très actifs suivant le niveau moyen
if ($moyenne_niveau > 3)
{
	echo "Niveau de référence pour l'entretien: 4\n<br />";
	$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > 3 AND dernier_connexion > $semaine GROUP BY race";
} else {
	echo "Niveau de référence pour l'entretien: $moyenne_niveau\n<br />";
	$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > $moyenne_niveau AND dernier_connexion > $semaine GROUP BY race";
}
$req = $db->query($requete);
while($row = $db->read_row($req))
{
	$habitants[$row[0]] = $row[1];
}
$min_habitants = min($habitants);
$ii = 0;
$keys = array_keys($habitants);
while($ii < count($habitants))
{
	$royaumes[$Trace[$keys[$ii]]['numrace']]['ratio'] = $habitants[$keys[$ii]] / $min_habitants;
	$ii++;
}
//On récupère les stars de chaque royaume
$requete = "SELECT id, star FROM royaume WHERE id <> 0 ORDER BY id ASC";
$req = $db->query($requete);
while($row = $db->read_row($req))
{
	$royaumes[$row[0]]['stars'] = $row[1];
	$royaumes[$row[0]]['id'] = $row[0];
}

//PHASE 1, entretien des batiments internes
//On récupère les couts d'entretiens
$requete = "SELECT *, construction_ville.id as id_const FROM construction_ville RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE construction_ville.statut = 'actif' ORDER by id_royaume ASC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$entretien = ceil($row['entretien'] * $royaumes[$row['id_royaume']]['ratio']);
	$royaumes[$row['id_royaume']]['batiments'][$row['id_const']] = $entretien;
	$royaumes[$row['id_royaume']]['total'] += $entretien;
}
//Entretien !

foreach($royaumes as $royaume)
{
	$royaume['stars'] -= $royaume['total'];
	if($royaume['stars'] < 0)
	{
		$dette = $royaume['stars'] * -1;
		$pourcent = $dette / $royaume['total'];
		$keys = array_keys($royaume['batiments']);
		$i = 0;
		while($i < count($royaume['batiments']))
		{
			$dette_const = floor($pourcent * $royaume['batiments'][$keys[$i]]);
			if($dette_const > 0)
			{
				$requete = "UPDATE construction_ville SET statut = 'inactif', dette = ".$dette_const." WHERE id = ".$keys[$i];
				$db->query($requete);
			}
			$i++;
		}
		$royaume['stars'] = 0;
	}
	$requete = "UPDATE royaume SET star = ".$royaume['stars']." WHERE id = ".$royaume['id'];
	$db->query($requete);
}
//PHASE 2, entretien des batiments externes
//On récupère les couts d'entretiens
$requete = "SELECT *, construction.id AS id_const, batiment.hp AS hp_m, construction.hp AS hp_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id ORDER by royaume ASC";
echo $requete.'<br />';
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$entretien = ceil($row['entretien'] * $royaumes[$row['royaume']]['ratio']);
	$royaumes[$row['royaume']]['constructions'][$row['id_const']]['entretien'] = $entretien;
	$royaumes[$row['royaume']]['constructions'][$row['id_const']]['max_hp'] = $row['hp_m'];
	$royaumes[$row['royaume']]['constructions'][$row['id_const']]['hp'] = $row['hp_c'];
	$royaumes[$row['royaume']]['total_c'] += $entretien;
}
//Entretien !
foreach($royaumes as $royaume)
{
	$royaume['stars'] -= $royaume['total_c'];
	if($royaume['stars'] < 0)
	{
		$dette = $royaume['stars'] * -1;
		$pourcent = $dette / $royaume['total_c'];
		$pourcent_vie = $pourcent / 10;
		$keys = array_keys($royaume['constructions']);
		$i = 0;
		while($i < count($royaume['constructions']))
		{
			$perte_const = floor($pourcent_vie * $royaume['constructions'][$keys[$i]]['max_hp']);
			$vie = $royaume['constructions'][$keys[$i]]['hp'] - $perte_const;
			//Perte de HP
			if($vie > 0)
			{
				$requete = "UPDATE construction SET hp = ".$vie." WHERE id = ".$keys[$i];
			}
			//Suppression du batiment
			else
			{
				$requete = "DELETE FROM construction WHERE id = ".$keys[$i];
			}
			$db->query($requete);
			$i++;
		}
		$royaume['stars'] = 0;
	}
	$requete = "UPDATE royaume SET star = ".$royaume['stars']." WHERE id = ".$royaume['id'];

	echo $requete."\n";

	$db->query($requete);
}

?>