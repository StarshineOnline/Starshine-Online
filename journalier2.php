<?php
if (file_exists('root.php'))
  include_once('root.php');

//JOURNALIER STATISTIQUES ET AUTRES //
/*if (isset($_SERVER['REMOTE_ADDR']) &&
	$_SERVER['REMOTE_ADDR']	!= "127.0.0.1" &&
	$_SERVER['REMOTE_ADDR']	!= "::1") {
	header('HTTP/1.0 403 Forbidden');
	die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);
}
*/
$mail = '';
$root = '';

function __autoload($class_name)
{
	global $root;
	require_once($root.'class/'.$class_name .'.class.php');
}

include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

include_once(root."pChart/pData.class");
include_once(root."pChart/pChart.class");

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

//Verification des joueurs inactifs
$requete = "UPDATE perso SET statut = 'inactif' WHERE dernier_connexion <= ".(time() - (86400 * 21))." AND statut = 'actif'";
$db->query($requete);

//Réduction de l'honneur pour tous les joueurs
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.02) WHERE honneur <= reputation";
$db->query($requete);
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.03) WHERE honneur > reputation";
$db->query($requete);

//Point de crime -1
$requete = "UPDATE perso SET crime = IF(crime - 1 < 0, 0, crime -1) WHERE crime > 0";
$db->query($requete);

//Vérification si batiments fini de construire
$case = new map_case();
$case->check_case('all');

echo 'Création du dossier '.$date.'<br />';
if(mkdir('image/stat/'.$date, 0777))
  echo 'Répertoire '.$date.' créé<br />';
else
  echo 'Le répertoire '.$date.' existe déjà<br />';

echo 'Déplacement des anciennes images dans le nouveau dossier<br />';
copy('image/carte.png', 'image/stat/'.$date.'/carte.png');
copy('image/carte_royaume.png', 'image/stat/'.$date.'/carte_royaume.png');
copy('image/carte_densite_mob.png', 'image/stat/'.$date.'/carte_densite_mob.png');
copy('image/stat_lvl.png', 'image/stat/'.$date.'/stat_lvl.png');
copy('image/stat_race.png', 'image/stat/'.$date.'/stat_race.png');
copy('image/stat_classe1.png', 'image/stat/'.$date.'/stat_classe1.png');
copy('image/stat_classe2.png', 'image/stat/'.$date.'/stat_classe2.png');
copy('image/stat_classe3.png', 'image/stat/'.$date.'/stat_classe3.png');
copy('image/stat_classe4.png', 'image/stat/'.$date.'/stat_classe4.png');
copy('image/stat_star1.png', 'image/stat/'.$date.'/stat_star1.png');
copy('image/stat_star2.png', 'image/stat/'.$date.'/stat_star2.png');
copy('image/stat_star3.png', 'image/stat/'.$date.'/stat_star3.png');
copy('image/stat_joueur.png', 'image/stat/'.$date.'/stat_joueur.png');
copy('image/stat_monstre.png', 'image/stat/'.$date.'/stat_monstre.png');
copy('image/stat_niveau_moyen.png', 'image/stat/'.$date.'/stat_niveau_moyen.png');

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
$moyenne_niveau = floor($row[0]);
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
echo 'Création des images statistiques<br />';
require_once('stats/stat_lvl.php');
require_once('stats/stat_race.php');
require_once('stats/stat_classe.php');

echo 'Création de l\'image de la carte du monde<br />';
$im = imagecreate (600, 600)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

$color1 = imagecolorallocate($im, 0x66, 0xdd, 0x66);
$color2 = imagecolorallocate($im, 0x00, 0x99, 0x00);
$color3 = imagecolorallocate($im, 0xff, 0xff, 0x00);
$color4 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color5 = imagecolorallocate($im, 0x00, 0x00, 0xff);
$color6 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
$color7 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
$color8 = imagecolorallocate($im, 0x5d, 0x43, 0x00);
$color9 = imagecolorallocate($im, 0x00, 0x00, 0x00);
$color11 = imagecolorallocate($im, 0x41, 0x35, 0x3e);
$show_info[0] = $color1;
$show_info[1] = $color1;
$show_info[2] = $color2;
$show_info[3] = $color3;
$show_info[4] = $color4;
$show_info[5] = $color5;
$show_info[6] = $color8;
$show_info[7] = $color9;
$show_info[8] = $color6;
$show_info[9] = $color6;
$show_info[10] = $color7;
$show_info[11] = $color11;
$col = 'info';
$carte = 'image/carte.png';

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map ORDER BY id';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['id']);
	$rowid = $row['id'];
	$W_terrain_case = $row['decor'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		imagefilledrectangle($im, (($coord['x'] - 1) * 3), (($coord['y'] - 1) * 3), ((($coord['x'] - 1) * 3) + 2), ((($coord['y'] - 1) * 3) + 2), $show_info[$row[$col]]);
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);

$show_info = array();

echo 'Création de la carte des royaumes<br />';

$im = imagecreate (600, 600)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

$color1 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
$color2 = imagecolorallocate($im, 0x00, 0x68, 0xff);
$color3 = imagecolorallocate($im, 0x00, 0x99, 0x00);
$color4 = imagecolorallocate($im, 0xff, 0x00, 0x00);
$color5 = imagecolorallocate($im, 0xff, 0xff, 0x00);
$color6 = imagecolorallocate($im, 0x00, 0x00, 0xff);
$color7 = imagecolorallocate($im, 0xff, 0xcc, 0xcc);
$color8 = imagecolorallocate($im, 0xff, 0xa5, 0x00);
$color9 = imagecolorallocate($im, 0x5c, 0x1e, 0x00);
$color10 = imagecolorallocate($im, 0x00, 0x00, 0x00);
$color11 = imagecolorallocate($im, 0x00, 0x00, 0xff);
$color12 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color13 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
$show_info[0] = $color1;
$show_info[1] = $color2;
$show_info[2] = $color3;
$show_info[3] = $color4;
$show_info[4] = $color5;
$show_info[5] = $color6;
$show_info[6] = $color7;
$show_info[7] = $color8;
$show_info[8] = $color9;
$show_info[9] = $color10;
$show_info[10] = $color11;
$show_info[11] = $color12;
$show_info[12] = $color13;
$col = 'royaume';
$carte = 'image/carte_royaume.png';

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map ORDER BY id';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['id']);
	$rowid = $row['id'];
	$W_terrain_case = $row['decor'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		imagefilledrectangle($im, (($coord['x'] - 1) * 3), (($coord['y'] - 1) * 3), ((($coord['x'] - 1) * 3) + 2), ((($coord['y'] - 1) * 3) + 2), $show_info[$row[$col]]);
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);

//Création de la carte de densité des mobs
$requete = "SELECT x, y, COUNT( * ) AS tot FROM `map_monstre` GROUP BY x, y ORDER BY tot DESC";
$req = $db->query($requete);
$check = false;
while($row = $db->read_assoc($req))
{
	if(!$check)
	{
		$max = $row['tot'];
		$check = true;
	}
	$map_monstre[$row['x']][$row['y']] = $row['tot'];
}

$im = imagecreate (600, 600)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);
$carte = 'image/carte_densite_mob.png';
$part = round($max / 6);

$color0 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color1 = imagecolorallocate($im, 0x66, 0xff, 0x33);
$color2 = imagecolorallocate($im, 0xe6, 0xfa, 0x37);
$color3 = imagecolorallocate($im, 0xe6, 0xba, 0x04);
$color4 = imagecolorallocate($im, 0xe6, 0x76, 0x04);
$color5 = imagecolorallocate($im, 0xe6, 0x3b, 0x07);
$color6 = imagecolorallocate($im, 0xff, 0x00, 0x00);
$show_info[0] = $color0;
$show_info[1] = $color1;
$show_info[2] = $color2;
$show_info[3] = $color3;
$show_info[4] = $color4;
$show_info[5] = $color5;
$show_info[6] = $color6;

//Création de la map
for($i = 1; $i <= 190; $i++)
{
	for($j = 1; $j <= 190; $j++)
	{
		if(!isset($map_monstre[$i][$j])) $densite = 0;
		else
		{
			if($map_monstre[$i][$j] < $part) $densite = 1;
			elseif($map_monstre[$i][$j] < $part * 2) $densite = 2;
			elseif($map_monstre[$i][$j] < $part * 3) $densite = 3;
			elseif($map_monstre[$i][$j] < $part * 4) $densite = 4;
			elseif($map_monstre[$i][$j] < $part * 5) $densite = 5;
			else $densite = 6;	
		}
		imagefilledrectangle($im, (($i - 1) * 3), (($j - 1) * 3), ((($i - 1) * 3) + 2), ((($j - 1) * 3) + 2), $show_info[$densite]);
	}
}
imagepng ($im, $carte);
imagedestroy($im);

// Mise en archive de la population de chaque royaume
$mail .= "\nJoueur et Stars de chaque royaume\n\n";
echo 'Mise en archive des stars et population de chaque royaume<br />';
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

$mail .= "\nStars pour les nouveaux joueurs\n\n";
//Calcul des stars pour nouveau joueur
$count = count($tableau_race);
print_r($tableau_race);
echo "<br />";
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
echo "<br />";
//On vire le monstres trop vieux
$requete = "DELETE FROM map_monstre WHERE mort_naturelle < ".time();
$db->query($requete);

require_once('stats/stat_star.php');
require_once('stats/stat_autres.php');

//Groupes du forum
$groupe = array();
$groupe['barbare'][0] = 5;
$groupe['barbare'][1] = 16;
$groupe['barbare'][2] = 27;
$groupe['elfebois'][0] = 6;
$groupe['elfebois'][1] = 17;
$groupe['elfebois'][2] = 29;
$groupe['elfehaut'][0] = 7;
$groupe['elfehaut'][1] = 18;
$groupe['elfehaut'][2] = 30;
$groupe['humain'][0] = 8;
$groupe['humain'][1] = 19;
$groupe['humain'][2] = 31;
$groupe['humainnoir'][0] = 9;
$groupe['humainnoir'][1] = 20;
$groupe['humainnoir'][2] = 28;
$groupe['mortvivant'][0] = 10;
$groupe['mortvivant'][1] = 21;
$groupe['mortvivant'][2] = 32;
$groupe['nain'][0] = 11;
$groupe['nain'][1] = 22;
$groupe['nain'][2] = 33;
$groupe['orc'][0] = 12;
$groupe['orc'][1] = 23;
$groupe['orc'][2] = 34;
$groupe['scavenger'][0] = 13;
$groupe['scavenger'][1] = 24;
$groupe['scavenger'][2] = 35;
$groupe['troll'][0] = 14;
$groupe['troll'][1] = 25;
$groupe['troll'][2] = 36;
$groupe['vampire'][0] = 15;
$groupe['vampire'][1] = 26;
$groupe['vampire'][2] = 37;
//On regarde si une élection a lieu
$requete = "SELECT id, id_royaume, type FROM elections WHERE date = '".date("Y-m-d", time())."'";
$req = $db->query($requete);
$elections = Array();
//S'il y a une élection de prévue
if($db->num_rows > 0)
{
	require_once(root.'fonction/forum.inc.php');
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT race FROM royaume WHERE id = ".$row['id_royaume'];
		$req_n = $db->query($requete);
		$row_n = $db->read_assoc($req_n);
		$race = $row_n['race'];
		$royaumes[ $row['id_royaume'] ]["race"] = $race;
		if( $row["type"] == "nomination" )
		{
		  $requete = "SELECT id FROM perso WHERE rang_royaume = 6 AND race = '$race'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
		  $id_roi = $row_r["id"];
    }
		$data = array();
		$legend = array();
		$label = array();
		if( $row["type"] == "nomination" )
		  $requete = "SELECT * FROM vote WHERE id_election = ".$row['id']." AND id_perso = $id_roi";
		else
		  $requete = "SELECT vote.id_candidat, COUNT(*) as count, perso.honneur FROM vote, perso WHERE id_election = ".$row['id']." AND perso.id = vote.id_candidat GROUP BY id_candidat ORDER BY count DESC, honneur DESC";
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
  		//Suppression de l'ancien roi
  		$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][2]." WHERE group_id = ".$groupe[$race][1];
  		$db_forum->query($requete);
		  //Groupe forum
  		$requete = "UPDATE perso SET rang_royaume = 7 WHERE rang_royaume = 6 AND race = '$race'";
  		$db->query($requete);
  		// Résultat des votes
			while($row_v = $db->read_assoc($req_v))
			{
				$requete = "SELECT * FROM candidat WHERE id_perso = ".$row_v['id_candidat']." AND id_election = ".$row['id'];
				$req_c = $db->query($requete);
				$row_c = $db->read_assoc($req_c);
				//C'est le roi on l'active, et on met en place la prochaine élection
				if($i == 0)
				{
					$requete = "UPDATE perso SET rang_royaume = 6 WHERE id = ".$row_v['id_candidat'];
					$db->query($requete);
					$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][1]." WHERE username = '".$row_c['nom']."'";
					$db_forum->query($requete);

					//Prochaine élection
					if($row_c['duree'] == 1 && date('d') > 12) $date_e = mktime(0, 0, 0, date("m") + 2, 1, date("Y"));
					else $date_e = mktime(0, 0, 0, date("m") + $row_c['duree'], 1, date("Y"));
					$election = new elections();
					$election->set_id_royaume($row['id_royaume']);
					$election->set_date( date("Y-m-d", $date_e) );
					$election->set_type($row_c['type']);
					$election->sauver();
					// Ministres
					$royaume = new royaume( $row['id_royaume'] );
					$royaume->set_ministre_economie( $row_c["id_ministre_economie"] );
					$royaume->set_ministre_militaire( $row_c["id_ministre_militaire"] );
					$royaume->sauver();
					
					// Message du forum
					$elections[ $row['id_royaume'] ]["prochain"] = "Prochaine ".($row_c['type']=="universel" ? "élection" : "nomination")
            ." le ".date("d / m / Y", $date_e).".";
				}
				$data[] = $row_v['count'];
				$legend[] = $row_c['nom'].'('.$row_v['count'].')';
				$label[] = $row_c['nom']."(".$row_v['count'].")\n%.1f%%";
				$i++;
			}

      // Création du graphe si c'est une élection
		  if( $row["type"] == "universel" )
		  {
  			$DataSet = new pData;
  			$DataSet->AddPoint($data,"Serie1");
  			$DataSet->AddPoint($legend,"Serie2");
  			$DataSet->AddAllSeries();
  			$DataSet->SetAbsciseLabelSerie("Serie2");
  
  			// Initialise the graph
  			$graph = new pChart(700, 400);
  			$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
  			$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);
  
  			// Draw the pie chart
  			$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
  			$graph->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),315,210,200,PIE_LABELS,TRUE,50,20,5);
  			//$graph->drawPieLegend(590,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
  			$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
  			$graph->drawTitle(50,22,'Elections du roi '.$Gtrad[$race].' du '.$date ,50,50,50,585);
  
  			$graph->Render('image/election_'.$race.'.png');
  			
				// Message du forum
				$elections[ $row['id_royaume'] ]["resultat"] = "[img]".BASE."image/election_$race.png[/img]";
      }
      else
      {
				// Message du forum
				$elections[ $row['id_royaume'] ]["resultat"] = "Nomination ".creer_cdn($row_c['nom']).".";
      }
		}
  	else // pas de votant
  	{
      // On garde le roi et les ministres et on crée une nouvelle élection universelle pour le mois prochain
			$date_e = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
			$election = new elections();
			$election->set_id_royaume($row['id_royaume']);
			$election->set_date( date("Y-m-d", $date_e) );
			$election->set_type("universel");
			$election->sauver();
			
			// Récupération du nom du roi
      $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '$race'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
		  $nom_roi = $row_r["nom"];
			// Message du forum
			$elections[ $row['id_royaume'] ]["resultat"] = "$nom_roi reconduit pour un mois, suffrage universel.";
			$elections[ $row['id_royaume'] ]["prochain"] = "Prochaine élection le ".date("d / m / Y", $date_e).".";
    }
	}
}

//On regarde si une révolution a lieu
$requete = "SELECT id, id_royaume FROM revolution WHERE date = '".date("Y-m-d", time())."'";
$req = $db->query($requete);
//S'il y a une révolution de prévue
if($db->num_rows > 0)
{
	require_once(root.'fonction/forum.inc.php');
	while($row = $db->read_assoc($req))
	{
		$requete = "SELECT race FROM royaume WHERE id = ".$row['id_royaume'];
		$req_n = $db->query($requete);
		$row_n = $db->read_assoc($req_n);
		$race = $row_n['race'];
		$data = array();
		$legend = array();
		$label = array();
		$requete = "SELECT *, SUM(poid_vote) as count FROM vote_revolution WHERE id_revolution = ".$row['id']." GROUP BY pour ORDER BY count DESC";
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
		  $pour = $contre = 0;
			while($row_v = $db->read_assoc($req_v))
			{
				if($row_v['pour'] == 1)
				{
					$pour = $row_v['count'];
					$data[] = $row_v['count'];
					$legend[] = 'Pour ('.$row_v['count'].')';
					$label[] = 'Pour ('.$row_v['count'].")\n%.1f%%";
				}
				else
				{
					$contre = $row_v['count'];
					$data[] = $row_v['count'];
					$legend[] = 'Contre ('.$row_v['count'].')';
					$label[] = 'Contre ('.$row_v['count'].")\n%.1f%%";
				}
			}

			$DataSet = new pData;
			$DataSet->AddPoint($data,"Serie1");
			$DataSet->AddPoint($legend,"Serie2");
			$DataSet->AddAllSeries();
			$DataSet->SetAbsciseLabelSerie("Serie2");

			// Initialise the graph
			$graph = new pChart(700, 400);
			$graph->drawFilledRoundedRectangle(7,7,693,393,5,240,240,240);
			$graph->drawRoundedRectangle(5,5,695,395,5,230,230,230);

			// Draw the pie chart
			$graph->setFontProperties("pChart/fonts/tahoma.ttf",8);
			$graph->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),315,210,200,PIE_LABELS,TRUE,50,20,5);
			//$graph->drawPieLegend(590,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
			$graph->setFontProperties("pChart/fonts/tahoma.ttf",12);
			$graph->drawTitle(50,22,'Révolution du peuple '.$Gtrad[$race].' du '.$date ,50,50,50,585);

			$graph->Render('image/revolution_'.$race.'_'.date("Y-m-d").'.png');
			
			// Message du forum
			$elections[ $row['id_royaume'] ]["resultat"] = "[img]".BASE."image/revolution_$race"."_".date("Y-m-d").".png[/img]\n";

			//On met en route la révolution si pour > contre
			if($pour > $contre)
			{
				//Suppression de l'ancien roi
				$requete = "UPDATE punbbusers SET group_id = ".$groupe[$race][2]." WHERE group_id = ".$groupe[$race][1];
				$db_forum->query($requete);
				$requete = "UPDATE perso SET rang_royaume = 7 WHERE rang_royaume = 6 AND race = '".$race."'";
				$db->query($requete);
				// Supression des ministres
				$royaume = new royaume( $row['id_royaume'] );
				$royaume->set_ministre_economie( 0 );
				$royaume->set_ministre_militaire( 0 );
				$royaume->sauver();
				//Mis en route de nouvelles élections pour le mois suivant
				if(date('d') > 12) $date_e = mktime(0, 0, 0, date("m") + 2, 1, date("Y"));
				else $date_e = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
				$election = new elections();
				$election->set_id_royaume($row['id_royaume']);
				$election->set_date( date("Y-m-d", $date_e) );
				$election->set_type('universel');
				$election->sauver();
  			// Message du forum
  			$elections[ $row['id_royaume'] ]["resultat"] .= "Le roi et ses ministres ont été destitués";
			  $elections[ $row['id_royaume'] ]["prochain"] = "Prochaine élection le ".date("d / m / Y", $date_e).".";
			}
			else
			{
    		// Récupération du nom du roi
        $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '$race'";
    	  $req_r = $db->query($requete);
    	  $row_r = $db->read_assoc($req_r);
    	  $nom_roi = $row_r["nom"];
  			$elections[ $row['id_royaume'] ]["resultat"] .= "$nom_roi reste roi.";
  			// Récupération de la date de la prochaine élection
        $prochaine = elections::get_prochain_election($row["id_royaume"], true);
        $date_e = explode('-', $prochaine[0]->get_date());
        $elections[ $row['id_royaume'] ]["prochain"] = "Prohaine ".($prochaine[0]->get_type()=="universel" ? "élection" : "nomination").
          " le ".$date_e[2]." / ".$date_e[1]." / ".$date_e[0].".";
      }
		}
	}
}

// Annonce sur le forum s'il y a eu des élections
if( count($elections) )
{
	// Début du message
	$msg_elec = "[i]Voici le résultat des élections ".creer_cdn(nom_mois_prec())." pour chaque royaume :[/i]\n\n";
	$msg_elec .= "(CTRL + F5 pour ceux qui ne voient pas les bonnes images)";
	// On parcours les royaume et donne l'évolution pour chacun
	$requete = "SELECT id,race FROM royaume WHERE race NOT LIKE ''";
	$req = $db->query($requete);
	while( $row = $db->read_assoc($req) )
	{
    $msg_elec .= "\n\n[b]".$Gtrad[ $row["race"] ]."[/b]\n";
    // Est-ce qu'il y a eut une élection ?
    if( array_key_exists($row["id"], $elections) )
    {
      $msg_elec .= $elections[$row["id"]]["resultat"]."\n".$elections[$row["id"]]["prochain"];
    }
    else // Pas de changement => on rappelle le roi et la date de la prochaine élection
    {
			// Récupération du nom du roi
      $requete = "SELECT nom FROM perso WHERE rang_royaume = 6 AND race = '".$row["race"]."'";
		  $req_r = $db->query($requete);
		  $row_r = $db->read_assoc($req_r);
      $msg_elec .= "Mandat de ".$row_r["nom"]." non terminé.\n";
      // Récupération de la prochaine élection
      $prochaine = elections::get_prochain_election($row["id"], true);
      $date_e = explode('-', $prochaine[0]->get_date());
      $msg_elec .= "Prohaine ".($prochaine[0]->get_type()=="universel" ? "élection" : "nomination").
        " le ".$date_e[2]." / ".$date_e[1]." / ".$date_e[0].".";
    }
  }
  // Création de l'annonce
  creer_annonce("Élections pour le mois ".creer_cdn(nom_mois()), $msg_elec);
}

//Fin des enchères
$ids = array();
$requete = "SELECT id, id_joueur, prix FROM vente_terrain WHERE date_fin <= ".time();
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$ids[] = $row['id'];
	if($row['id_joueur'] != 0)
	{
		$terrain = new terrain(0, $row['id_joueur'], 2);
		$terrain->sauver();
		$mail .= $row['id_joueur']." gagne un terrain pour ".$row['prix']." stars.\n";
	}
}
//On supprime les enchères finies
if(count($ids) > 0)
{
	$implode_ids = implode(', ', $ids);
	$requete = "DELETE FROM vente_terrain WHERE id IN (".$implode_ids.")";
	$db->query($requete);
}

if(date("N") == 1)
{
	//Attribution des grades
	require_once('grade.php');
	//Les rois peuvent de nouveau se téléporter
	$requete = "UPDATE perso SET teleport_roi = 'false' WHERE rang_royaume = 6";
	$db->query($requete);

	$semaine = (60 * 60 * 24 * 7) - 3600;
	$fin_vente = time() + $semaine;
	//Mis en vente de nouveaux terrains
	foreach($tableau_race as $race => $stats)
	{
		$nb_terrains = floor($stats[15] / 500);
		$i = 0;
		while($i < $nb_terrains)
		{
			$requete = "INSERT INTO vente_terrain (id_royaume, date_fin, id_joueur, prix) VALUES (".$Trace[$race]['numrace'].", ".$fin_vente.", 0, 5000)";
			$db->query($requete);
			$i++;
		}
	}
}

$mail_send = getenv('SSO_MAIL');
if ($mail_send == null || $mail_send == '') $mail_send = 'starshineonline@gmail.com';
mail($mail_send, 'Starshine - Script journalier du '.$date, $mail);

?>