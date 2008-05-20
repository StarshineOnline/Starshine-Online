<?php

if (isset($_SERVER['REMOTE_ADDR']) &&
	$_SERVER['REMOTE_ADDR']	!= "127.0.0.1" &&
	$_SERVER['REMOTE_ADDR']	!= "::1") {
	header('HTTP/1.0 403 Forbidden');
	die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);
}

$mail = '';

include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include('fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include('fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_pie.php");
include ("jpgraph/src/jpgraph_pie3d.php");
include ("jpgraph/src/jpgraph_line.php");
include ("jpgraph/src/jpgraph_bar.php");
include ("jpgraph/src/jpgraph_scatter.php");
include ("jpgraph/src/jpgraph_regstat.php");
$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

//Verification des joueurs inactifs
$requete = "UPDATE perso SET statut = 'inactif' WHERE dernier_connexion <= ".(time() - (86400 * 21))." AND statut = 'actif'";
$db->query($requete);

//Réduction de l'honneur pour tous les joueurs
$requete = "UPDATE perso SET honneur = ROUND(honneur / 1.03)";
$db->query($requete);

//Point de crime -1
$requete = "UPDATE perso SET crime = crime - 1";
$db->query($requete);

//Vérification si batiments fini de construire
check_case('all');

echo 'Création du dossier '.$date.'<br />';
if(@mkdir('image/stat/'.$date)) echo 'Répertoire '.$date.' créé<br />'; echo 'Le répertoire '.$date.' existe déjà<br />';

echo 'Déplacement des anciennes images dans le nouveau dossier<br />';
copy('image/carte.png', 'image/stat/'.$date.'/carte.png');
copy('image/carte_royaume.png', 'image/stat/'.$date.'/carte_royaume.png');
copy('image/carte_densite_mob.png', 'image/stat/'.$date.'/carte_densite_mob.png');
copy('image/stat_lvl.jpg', 'image/stat/'.$date.'/stat_lvl.jpg');
copy('image/stat_race.jpg', 'image/stat/'.$date.'/stat_race.jpg');
copy('image/stat_classe1.jpg', 'image/stat/'.$date.'/stat_classe1.jpg');
copy('image/stat_classe2.jpg', 'image/stat/'.$date.'/stat_classe2.jpg');
copy('image/stat_classe3.jpg', 'image/stat/'.$date.'/stat_classe3.jpg');
copy('image/stat_star1.jpg', 'image/stat/'.$date.'/stat_star1.jpg');
copy('image/stat_star2.jpg', 'image/stat/'.$date.'/stat_star2.jpg');
copy('image/stat_star3.jpg', 'image/stat/'.$date.'/stat_star3.jpg');
copy('image/stat_joueur.jpg', 'image/stat/'.$date.'/stat_joueur.jpg');
copy('image/stat_monstre.jpg', 'image/stat/'.$date.'/stat_monstre.jpg');
copy('image/stat_niveau_moyen.jpg', 'image/stat/'.$date.'/stat_niveau_moyen.jpg');

//Entretien des batiments et constructions
//On récupère le nombre d'habitants très actifs
$semaine = time() - (3600 * 24 * 7);
$royaumes = array();
$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > 3 AND dernier_connexion > ".$semaine." GROUP BY race";
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
$requete = "SELECT ID, star FROM royaume WHERE ID <> 0 ORDER BY ID ASC";
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
	$requete = "UPDATE royaume SET star = ".$royaume['stars']." WHERE ID = ".$royaume['id'];
	$db->query($requete);
}
//PHASE 2, entretien des batiments externes
//On récupère les couts d'entretiens
$requete = "SELECT *, construction.id AS id_const, batiment.hp AS hp_m, construction.hp AS hp_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id ORDER by royaume ASC";
echo $requete;
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
	$requete = "UPDATE royaume SET star = ".$royaume['stars']." WHERE ID = ".$royaume['id'];
	$db->query($requete);
}
echo 'Création des images statistiques<br />';
require_once('stats/stat_lvl.php');
require_once('stats/stat_race.php');
require_once('stats/stat_classe.php');

echo 'Création de l\'image de la carte du monde<br />';
$im = imagecreate (450, 450)
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
$requete = 'SELECT * FROM map ORDER BY ID';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['ID']);
	$rowid = $row['ID'];
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
$requete = 'SELECT * FROM map ORDER BY ID';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['ID']);
	$rowid = $row['ID'];
	$W_terrain_case = $row['decor'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		imagefilledrectangle($im, (($coord['x'] - 1) * 4), (($coord['y'] - 1) * 4), ((($coord['x'] - 1) * 4) + 3), ((($coord['y'] - 1) * 4) + 3), $show_info[$row[$col]]);
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
for($i = 1; $i <= 150; $i++)
{
	for($j = 1; $j <= 150; $j++)
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
		imagefilledrectangle($im, (($i - 1) * 4), (($j - 1) * 4), ((($i - 1) * 4) + 3), ((($j - 1) * 4) + 3), $show_info[$densite]);
	}
}
imagepng ($im, $carte);
imagedestroy($im);

$mail .= "\nJoueur et Stars de chaque royaume\n\n";
echo 'Mise en archive des stars et population de chaque royaume<br />';
$tableau_race = array();
$requete = "SELECT race, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY race";
$req = $db->query($requete);
$total = 0;
while($row = $db->read_array($req))
{
	$total += $row['total'];
	$tableau_race[$row['race']][1] = $row['total'];
	$mail .= $row['race']." - Joueurs : ".$row['total']."\n";
}

$requete = "SELECT race, star FROM royaume";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	if($row['race'] != '') $tableau_race[$row['race']][0] = $row['star'];
	$mail .= " - Stars : ".$row['star']."\n";
}

//Nombre de joueurs total
$requete = "SELECT COUNT(*), AVG(level) FROM perso WHERE statut = 'actif'";
$req = $db->query($requete);
$row = $db->read_row($req);
$nbr_perso = $row[0];
$niveau_moyen = $row[1];
$level_moyen = ceil($row[1]);

//Nombre de monstres total
$requete = "SELECT COUNT(*) FROM map_monstre";
$req = $db->query($requete);
$row = $db->read_row($req);
$nbr_monstre = $row[0];

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
$requete = "INSERT INTO `stat_jeu` ( `id` , `date` , `barbare` , `elfebois` , `elfehaut` , `humain` , `humainnoir` , `nain` , `orc` , `scavenger` , `troll` , `vampire` , `mortvivant`, `niveau_moyen`, `nombre_joueur`, `nombre_monstre` ) VALUES (NULL , '".$date."', '".$tableau_races['barbare']."', '".$tableau_races['elfebois']."', '".$tableau_races['elfehaut']."', '".$tableau_races['humain']."', '".$tableau_races['humainnoir']."', '".$tableau_races['nain']."', '".$tableau_races['orc']."', '".$tableau_races['scavenger']."', '".$tableau_races['troll']."', '".$tableau_races['vampire']."', '".$tableau_races['mortvivant']."', ".$niveau_moyen.", ".$nbr_perso.", ".$nbr_monstre.");";
$req = $db->query($requete);

$mail .= "\nStars pour les nouveaux joueurs\n\n";
//Calcul des stars pour nouveau joueur
$count = count($tableau_race);
print_r($tableau_race);
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
		$requete = "UPDATE royaume SET star_nouveau_joueur = ".$stars." WHERE ID = ".$Trace[$keys[$i]]['numrace'];
		echo $requete;
		$db->query($requete);
	}
	$i++;
}
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
//Si on est le premier, élection du roi de chaque race
if(date("d") == 1)
{	
	//Suppression des anciens rois
	foreach($groupe as $group)
	{
		$requete = "UPDATE punbbusers SET group_id = ".$group[0]." WHERE group_id = ".$group[1];
		$db->query($requete);
	}
	$requete = "UPDATE perso SET rang_royaume = 7 WHERE rang_royaume = 6";
	$db->query($requete);
	//Groupe forum

	echo 'Election des rois';
	$requete = "SELECT * FROM royaume WHERE ID <> 0";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$data = array();
		$legend = array();
		$label = array();
		$requete = "SELECT *, COUNT(*) as count FROM vote WHERE royaume = ".$row['ID']." AND date = '".date("Y-m")."' GROUP BY id_candidat ORDER BY count DESC";
		$req_v = $db->query($requete);
		$i = 0;
		if($db->num_rows > 0)
		{
			while($row_v = $db->read_assoc($req_v))
			{
				$requete = "SELECT * FROM perso WHERE ID = ".$row_v['id_candidat'];
				$req_c = $db->query($requete);
				$row_c = $db->read_assoc($req_c);
				if($i == 0)
				{
					$graph = new PieGraph(700, 400, "auto");
					$graph->SetShadow();
					$graph->title->Set("Elections du roi ".$Gtrad[$row['race']]." du ".$row_v['date']);
					$requete = "UPDATE perso SET rang_royaume = 6 WHERE ID = ".$row_c['ID'];
					$db->query($requete);
					$requete = "UPDATE punbbusers SET group_id = ".$groupe[$row_c['race']][1]." WHERE username = '".$row_c['nom']."'";
					$db->query($requete);
					
				}
				$data[] = $row_v['count'];
				$legend[] = $row_c['nom'].'('.$row_v['count'].')';
				$label[] = $row_c['nom']."(".$row_v['count'].")\n%.1f%%";
				$i++;
			}
			
			//$graph->tabtitle->SetFont(FF_ARIAL,FS_BOLD,13);
			
			$p1 = new PiePlot3D($data);
			$p1->SetLabels($label);
			$p1->SetSize(0.5);
			$p1->SetCenter(0.45);
			//$p1->SetLegends($legend);
			$p1->SetLabelPos(0.6);
			$graph->Add($p1);
			$graph->Stroke('image/election_'.$row['race'].'.jpg');
		}
	}
}

if(date("N") == 1)
{
	//Attribution des grades
	require_once('grade.php');
	//Les rois peuvent de nouveau se téléporter
	$requete = "UPDATE perso SET teleport_roi = 'false' WHERE rang_royaume = 6";
	$db->query($requete);
}

mail('masterob1@chello.fr', 'Starshine - Script journalier du '.$date, $mail);

?>