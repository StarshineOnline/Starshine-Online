<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

// RAZ capitale
$now = time();
$requete = "select id from royaume where fin_raz_capitale > 0 and fin_raz_capitale < $now";
$req = $db->query($requete);
if ($req)
	while($row = $db->read_object($req)) {
		$requete = "update construction_ville set dette = 0, date = 0, hp = 1000 where id_royaume = ".$row->id;
		$db->query($requete);
	}
$requete = "UPDATE royaume set fin_raz_capitale = 0, capitale_hp = 30000 where fin_raz_capitale > 0 and fin_raz_capitale < $now";
$req = $db->query($requete);

// Distribution des pts de victoire a partir des pts exceptionnels
$requete = "UPDATE royaume r";
$requete .= " SET point_victoire = point_victoire + (select count(1) from map where type = 3 and royaume = r.id and r.id <> 0)";
$requete .= ", point_victoire_total = point_victoire_total + (select count(1) from map where type = 3 and royaume = r.id and r.id <> 0)";
$req = $db->query($requete);

//Entretien des batiments et constructions
/*$semaine = time() - (3600 * 24 * 7);
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
	echo "Niveau de référence pour l'entretien: 4\n\n";
	$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > 3 AND dernier_connexion > $semaine GROUP BY race";
} else {
	echo "Niveau de référence pour l'entretien: $moyenne_niveau\n\n";
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
}*/
//On récupère les stars de chaque royaume
$requete = "SELECT id, star, facteur_entretien FROM royaume WHERE id <> 0 ORDER BY id ASC";
$req = $db->query($requete);
while($row = $db->read_row($req))
{
	$royaumes[$row[0]]['stars'] = $row[1];
	$royaumes[$row[0]]['id'] = $row[0];
	$royaumes[$row[0]]['ratio'] = $row[2];
}

/**
 * Stats à mémoriser pour chaque race.
 * Tableau bi-dimensionnel, la première clé est la race, la deuxième un indice indexant
 * les stats à conserver :
 * <ol>
 *  <li>Population du royaume</li>
 *  <li>Stars du royaume</li>
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
 *  <li>Nombre d'habitants très actifs</li>
 *  <li>Facteur d'entretien actuel</li>
 *  <li>Facteur d'entretien théorique</li>
 *  <li>Consommation de nouriture actuelle</li>
 *  <li>Consommation de nouriture théorique</li>
 * </ol>
 */
$tableau_race = array();
$roy = royaume::create(null, null, 'id ASC', false, 'id <> 0');
foreach($roy as $r)
{
  $tableau_race[$r->get_race()] = array_pad(array(), 30, 0);
  $tableau_race[$r->get_race()][26] = $r->get_habitants_actif();
  $tableau_race[$r->get_race()][27] = $r->get_facteur_entretien();
  $tableau_race[$r->get_race()][28] = $r->get_facteur_entretien_th();
  $tableau_race[$r->get_race()][29] = $r->get_conso_food();
  $tableau_race[$r->get_race()][30] = $r->get_conso_food_th();
  $r->maj_facteur_entretien();
  $r->sauver();
}
//print_r($tableau_race);

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
/// Augmente la dette des batiments inactifs (de la moitie de l'entretien)
$requete = 'update construction_ville set dette = dette + (select FLOOR(b.entretien / 2) from batiment_ville b where b.id = construction_ville.id_batiment) where construction_ville.dette > 0';
$req = $db->query($requete);
/// Calcule l'entertien courant des batiments
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
$requete = "SELECT *, construction.id AS id_const, batiment.hp AS hp_m, construction.hp AS hp_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id WHERE x <= 190 AND y <= 190 ORDER by royaume ASC";
echo $requete."\n";
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
		echo "Dette: $dette, pourcent_vie: $pourcent_vie \n";
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