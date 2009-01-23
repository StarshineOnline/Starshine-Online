<?php
//JOURNALIER RESSOURCES DE ROYAUME //
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

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

include('inc/ressource.inc.php');
//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));


$ressources = array();

$requete = "SELECT royaume.race as race, info, FLOOR(COUNT(*) / 10) as tot, COUNT(*) as tot_terrain FROM `map` LEFT JOIN royaume ON map.royaume = royaume.id WHERE royaume <> 0 GROUP BY info, royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['tot'] > 0)
	{
		$typeterrain = type_terrain($row['info']);
		$ressources[$row['race']][$typeterrain[1]] = $row['tot'];
		$terrain[$row['race']][$typeterrain[1]] = $row['tot_terrain'];
	}
}

//Ressource normale
$i = 0;
$key = array_keys($ressources);
foreach($ressources as $res)
{
	$j = 0;
	$keys = array_keys($res);
	while($j < count($res))
	{
		$k = 0;
		$kei = array_keys($ress[$keys[$j]]);
		foreach($ress[$keys[$j]] as $rr)
		{
			$ressource_final[$key[$i]][$kei[$k]] += $rr * $ressources[$key[$i]][$keys[$j]];
			if($kei[$k] == 'Nourriture') $tot_nou += $rr * $ressources[$key[$i]][$keys[$j]];
			$k++;
		}
		$j++;
	}
	$i++;
}
//Ressource mine
//On récupère la liste des batiments de type mine
$batiment = array();
$requete = "SELECT * FROM batiment WHERE type = 'mine'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$batiment[$row['id']] = $row;
}
echo '<pre>';
//@TODO gérer les mines dans construction
$requete = "SELECT * FROM construction LEFT JOIN map ON map.ID = (construction.y * 1000) + construction.x WHERE construction.type = 'mine'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$terrain = type_terrain($row['info']);
	$ress_terrain = $ress[$terrain[1]];
	$royaume = get_royaume_info($row['royaume'], $row['royaume']);
	if($batiment[$row['id_batiment']]['bonus2'] != 0)
	{
		switch($batiment[$row['id_batiment']]['bonus2'])
		{
			case 1 :
				$ress_final = array('Pierre' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Pierre']);
			break;
			case 2 :
				$ress_final = array('Bois' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Bois']);
			break;
			case 3 :
				$ress_final = array('Eau' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Eau']);
			break;
			case 4 :
				$ress_final = array('Sable' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Sable']);
			break;
			case 5 :
				$ress_final = array('Nourriture' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Nourriture']);
			break;
			case 6 :
				$ress_final = array('Star' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Star']);
			break;
			case 7 :
				$ress_final = array('Charbon' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Charbon']);
			break;
			case 8 :
				$ress_final = array('Essence Magique' => $batiment[$row['id_batiment']]['bonus1'] * $ress_terrain['Essence Magique']);
			break;
		}
	}
	else
	{
		$ress_final = array();
		foreach($ress_terrain as $key => $value)
		{
			$ress_final[$key] = $batiment[$row['id_batiment']]['bonus1'] * $value;
		}
	}
	foreach($ress_final as $key => $value)
	{
		$ressource_final[$royaume['race']][$key] += $value;
		if($key == 'Nourriture') $tot_nou += $value;
	}
}

foreach($ressource_final as $key => $value)
{
	$requete = "SELECT ".$key." FROM stat_jeu WHERE date = '".$date."'";
	$req_stat_jeu = $db->query($requete);
	$row_stat_jeu = $db->read_assoc($req_stat_jeu);
	$explode_stat = explode(';', $row_stat_jeu[$key]);
	$explode_stat[18] = $value['Pierre'];
	$explode_stat[19] = $value['Bois'];
	$explode_stat[20] = $value['Eau'];
	$explode_stat[21] = $value['Sable'];
	$explode_stat[22] = $value['Charbon'];
	$explode_stat[23] = $value['Essence Magique'];
	$explode_stat[24] = $value['Star'];
	$explode_stat[25] = $value['Nourriture'];
	$implode_stat = implode(';', $explode_stat);
	$requete = "UPDATE royaume SET pierre = pierre + ".$value['Pierre'].", bois = bois + ".$value['Bois'].", eau = eau + ".$value['Eau'].", sable = sable + ".$value['Sable'].", charbon = charbon + ".$value['Charbon'].", essence = essence + ".$value['Essence Magique'].", star = star + ".$value['Star'].", food = food + ".$value['Nourriture']." WHERE race = '".$key."'";
	$db->query($requete);
	$requete = "UPDATE stat_jeu SET ".$key." = '".$implode_stat."' WHERE date = '".$date."'";
	$mail .= $requete."\n";
	$db->query($requete);
}

//Mis à jour de la nourriture totales
$food_total = ceil($tot_nou * 1.01);
$requete = "UPDATE stat_jeu SET food = ".$food_total." WHERE date = '".$date."'";
$db->query($requete);

//Nourriture
//On réduit de 1 les debuff famines
$requete = "UPDATE buff SET effet = effet - 1 WHERE type= 'famine'";
$db->query($requete);
$requete = "DELETE FROM buff WHERE type = 'famine' AND effet <= 0";
$db->query($requete);
//On récupère la food nécessaire par habitant
$requete = "SELECT food, nombre_joueur FROM stat_jeu WHERE date = '".$date_hier."'";
$req = $db->query($requete);
$row = $db->read_assoc($req);
if($row['nombre_joueur'] != 0) $food_necessaire = $row['food'] / $row['nombre_joueur'];
else $food_necessaire = 0;

$mail .= 'Nourriture nécessaire'.$food_necessaire."\n";

//On récupère les infos des royaumes
$requete = "SELECT ID, race, food FROM royaume WHERE ID != 0";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tab_royaume[$row['race']] = array('id' => $row['ID'], 'food' => $row['food'], 'actif' => nb_habitant($row['race']));
}
foreach($tab_royaume as $race => $royaume)
{
	$royaume['food_necessaire'] = floor($food_necessaire * $royaume['actif']);
	//echo $royaume['race'].' '.$royaume['food_necessaire'].'<br />';
	//Si ya assez de food
	$mail .= "Race : ".$race." - Nécessaire : ".$royaume['food_necessaire']." / Possède : ".$royaume['food']."\n";
	if($royaume['food_necessaire'] < $royaume['food'])
	{
		$requete = "UPDATE royaume SET food = food - ".$royaume['food_necessaire']." WHERE ID = ".$royaume['id'];
		$db->query($requete);
	}
	//Sinon
	else
	{
		//Calcul du debuff
		$royaume['food_doit'] = $royaume['food_necessaire'] - $royaume['food'];
		$ratio = $royaume['food_doit'] / $royaume['food_necessaire'];
		$debuff = ceil($ratio * 10) - 1;
		if($debuff > 6) $debuff = 6;
		$persos = array();
		$requete = "SELECT ID FROM perso WHERE race = '".$race."' AND statut = 'actif'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$persos[$row['ID']] = $row['ID'];
		}
		$perso_implode = implode(',', $persos);
		//On sélectionne les buffs à modifier
		$ids_buff = array();
		$requete = "SELECT ID FROM buff WHERE type = 'famine' AND id_perso IN (".$perso_implode.")";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$ids_buff[] = $row;
		}
		$buffs = array();
		//On supprime les perso qui ont déjà un buff pour mettre à jour
		foreach($ids_buff as $buff)
		{
			unset($persos[$buff['id_joueur']]);
			$buffs[] = $buff['ID'];
		}
		//30 jours
		$duree = 30 * 24 * 60 * 60;
		$fin = time() + $duree;
		$buffs_implode = implode(',', $buffs);
		if(count($buffs) > 0)
		{
			$requete = "UPDATE buff SET effet = effet + ".$debuff.", duree = ".$duree.", fin = ".$fin." WHERE ID IN (".$buffs_implode.")";
			$mail .= $requete."\n";
			$db->query($requete);
		}
		foreach($persos as $joueur)
		{
			//Lancement du buff
			lance_buff('famine', $joueur, $debuff, 0, $duree, 'Famine', 'Vos HP et MP max sont réduits de %effet%%', 'perso', 1, 0, 0);
		}
		$requete = "UPDATE royaume SET food = 0 WHERE ID = ".$royaume['id'];
		$db->query($requete);
	}
	$requete = "UPDATE buff SET effet = 50 WHERE type = 'famine' AND effet > 50";
	$db->query($requete);
}
$db->query("UPDATE perso SET beta = 3");

mail('starshineonline@gmail.com', 'Starshine - Script journalier 4 du '.$date, $mail);

?>