<?php
if (file_exists('root.php'))
  include_once('root.php');

//JOURNALIER RESSOURCES DE ROYAUME //


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

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

include_once(root.'inc/ressource.inc.php');
//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));
$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));


$ressources = array();

/// Liste des royaumes
$lst_roy = array_keys($Trace);
/// Liste des ressources
$lst_rsrc = array_keys($ress['Plaine']);

//On initialise le tableau de ressources des royaumes
foreach($lst_roy as $royaume)
{
	foreach($lst_rsrc as $ressource)
	{
		$ressource_final[$royaume][$ressource] = 0;
	}
}

$requete = "SELECT royaume.race as race, info, COUNT(*) as tot_terrain FROM `map` LEFT JOIN royaume ON map.royaume = royaume.id WHERE royaume <> 0 AND x <= 190 AND y <= 190  GROUP BY info, royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['tot_terrain'] > 0)
	{
		$typeterrain = type_terrain($row['info']);
		$ressources[$row['race']][$typeterrain[1]] += $row['tot_terrain']/10;
		$terrain[$row['race']][$typeterrain[1]] += $row['tot_terrain'];
	}
}

//print_r($ress);
//Ressource normale
$tot_nou = 0;
foreach($ressources as $royaume=>$packs)
{
	if (!array_key_exists($royaume, $Trace)) {
		continue;
	}
  $msg_packs = '';
	foreach($packs as $terr=>$nbr_packs)
	{
	  if( $msg_packs )
      $msg_packs .= ', ';
    $msg_packs .= $nbr_packs.' '.$terr;
    if( $terr == 'Bâtiment' || $terr == 'Donjon' )
      continue;
    $msg_gains = '';
		$gains_pack = $ress[$terr];
		foreach($gains_pack as $rsrc=>$gain)
		{
		  if( $msg_gains )
		    $msg_gains .= ', ';
			$ressource_final[$royaume][$rsrc] += $gain * floor($nbr_packs);
			if($rsrc == 'Nourriture') $tot_nou += $gain * floor($nbr_packs);
			$msg_gains .= ($gain * floor($nbr_packs)).' '.$rsrc;
		}
		$msg_packs .= ' ('.$msg_gains.')';
	}
  echo $royaume.' : '.$msg_packs."\n";
}


//Suppression des mines sans bourg
$requete = "SELECT a.id as id, a.rechargement, c.id as bourg_id FROM `construction` as a LEFT JOIN construction as c ON a.rechargement = c.id WHERE a.type ='mine'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['bourg_id'] == '')
	{
		$mine = new construction($row['id']);
    log_admin::log('journalier', 'Destruction de la construction '.$mine->get_nom().' de '.$royaume.', en '.$mine->get_x().'-'.$mine->get_y().' à cause de la disparition du bourg #'.$mine->get_rechargement() );
		$mine->supprimer();
	}
}

//Ressource mine
//On récupère la liste des batiments de type mine
$batiment = array();
$requete = "SELECT b.id, bp.valeur AS production, bs.valeur AS specialite, bf.effet AS buff, db.effet AS debuff 
FROM batiment b 
LEFT JOIN batiment_bonus AS bp ON bp.id_batiment = b.id and bp.bonus = 'production' 
LEFT JOIN batiment_bonus AS bs ON bs.id_batiment = b.id and bs.bonus = 'specialite' 
LEFT JOIN buff_batiment AS bf ON bf.id_construction = bf.id and bf.type = 'buff_prod'
LEFT JOIN buff_batiment AS db ON db.id_construction = db.id and db.type = 'debuff_prod' 
where b.type = 'mine'"; // Oui c'est gore
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$batiment[$row['id']] = $row;
}
///@TODO gérer les mines dans construction
$requete = "SELECT * FROM construction LEFT JOIN map ON (map.y = construction.y AND construction.x = map.x) WHERE construction.type = 'mine'";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$terrain = type_terrain($row['info']);
	$ress_terrain = $ress[ $terrain[1] ];
	$royaume = get_royaume_info($row['royaume'], $row['royaume']);
	$buff = (1 + $batiment[$row['id_batiment']]['buff']/100) / (1 + $batiment[$row['id_batiment']]['debuff']/100);
	if($batiment[$row['id_batiment']]['specialite'] != 0)
	{
		switch($batiment[$row['id_batiment']]['specialite'])
		{
			case 1 :
				$ress_final = array('Pierre' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Pierre'] * $buff);
			break;
			case 2 :
				$ress_final = array('Bois' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Bois'] * $buff);
			break;
			case 3 :
				$ress_final = array('Eau' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Eau'] * $buff);
			break;
			case 4 :
				$ress_final = array('Sable' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Sable'] * $buff);
			break;
			case 5 :
				$ress_final = array('Nourriture' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Nourriture'] * $buff);
			break;
			case 6 :
				$ress_final = array('Star' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Star'] * $buff);
			break;
			case 7 :
				$ress_final = array('Charbon' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Charbon'] * $buff);
			break;
			case 8 :
				$ress_final = array('Essence Magique' => $batiment[$row['id_batiment']]['production'] * $ress_terrain['Essence Magique'] * $buff);
			break;
		}
	}
	else
	{
		$ress_final = array();
		foreach($ress_terrain as $key => $value)
		{
			$ress_final[$key] = $batiment[$row['id_batiment']]['production'] * $value * $buff;
		}
	}
	foreach($ress_final as $key => $value)
	{
		if (!array_key_exists($royaume['race'], $Trace)) {
			continue;
		}
		$ressource_final[$royaume['race']][$key] += $value;
		if($key == 'Nourriture') $tot_nou += $value;
	}
}

foreach($ressource_final as $key => $value)
{
	if( $key == 'liste' )
		continue;
	$requete = "SELECT ".$key." FROM stat_jeu WHERE date = '".$date."'";
	//echo $requete."\n";
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
	echo $requete."\n";
	$db->query($requete);
	$requete = "UPDATE stat_jeu SET ".$key." = '".$implode_stat."' WHERE date = '".$date."'";
	echo $requete."\n";
	$db->query($requete);
}

//Mis à jour de la nourriture totales
$food_total = $tot_nou;//ceil($tot_nou * 1.01);
$requete = "UPDATE stat_jeu SET food = ".$food_total." WHERE date = '".$date."'";
$db->query($requete);

//On récupère les infos des royaumes
$requete = "SELECT id, race, food, conso_food FROM royaume WHERE id != 0";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tab_royaume[$row['race']] = array('id' => $row['id'], 'food' => $row['food'], 'food_necessaire' => $row['conso_food']);
}
foreach($tab_royaume as $race => $royaume)
{
	//Si ya assez de food
	echo "Race : ".$race." - Nécessaire : ".$royaume['food_necessaire']." / Possède : ".$royaume['food']."\n";
	
	// La durée du debuff famine n'intervient pas dans les règles donc il faut s'assurer qu'il n'influence pas la disparition du debuff,
	// il faut donc réactualiser sa valeur régulièrement.
	// Durée quelconque du buff : 30 jours (au moins 1 jour car ce script est lancé quotidiennement)
	$duree = 30 * 24 * 60 * 60;
	$fin = time() + $duree;
	$requete = "
		UPDATE buff b
		INNER JOIN perso p ON b.id_perso = p.id
		SET b.duree = ".$duree.", b.fin = ".$fin."
		WHERE
		b.type = 'famine'
		AND p.race = '$race' AND p.statut = 'actif'
	";
	$db->query($requete);
	if($royaume['food_necessaire'] < $royaume['food'])
	{
		$requete = "UPDATE royaume SET food = food - ".$royaume['food_necessaire']." WHERE id = ".$royaume['id'];
		$db->query($requete);
		//On réduit de 3 les debuff famines (1 ??)
		$requete = "
			UPDATE buff b
			INNER JOIN perso p ON b.id_perso = p.id
			SET b.effet = b.effet -3
			WHERE
			b.type = 'famine'
			AND p.race = '$race' AND p.statut = 'actif'
		";
		$db->query($requete);
	}
	else
	{
		//Calcul du debuff
		$royaume['food_doit'] = $royaume['food_necessaire'] - $royaume['food'];
		if ($royaume['food_necessaire'] != 0)
			$ratio = $royaume['food_doit'] / $royaume['food_necessaire'];
		else
			$ratio = 0;
    $debuff = min(floor($ratio * 50), 25);

		$persos = array();
		$requete = "SELECT id FROM perso WHERE race = '".$race."' AND statut = 'actif'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$persos[$row['id']] = $row['id'];
		}
		if (count($persos) == 0)
			continue;
		$perso_implode = implode(',', $persos);
		//On sélectionne les buffs à modifier
		$ids_buff = array();
		$requete = "SELECT id, id_perso FROM buff WHERE type = 'famine' AND id_perso IN (".$perso_implode.")";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$ids_buff[] = $row;
		}
		$buffs = array();
		//On supprime les perso qui ont déjà un buff pour mettre à jour
		foreach($ids_buff as $buff)
		{
			unset($persos[$buff['id_perso']]);
			$buffs[] = $buff['id'];
		}
		$buffs_implode = implode(',', $buffs);
		if(count($buffs) > 0)
		{
			$requete = 'UPDATE buff SET effet = effet + FLOOR( 2 * ATAN(('.$debuff.'-effet)/2) - 0.5 ) WHERE id IN ('.$buffs_implode.')';
			$db->query($requete);
		}
		echo "Mis à jour du buff famine sur ".count($buffs)." ".$race.", effet + ".$debuff.".\n";
		foreach($persos as $joueur)
		{
			//Lancement du buff
			lance_buff('famine', $joueur, min(floor($debuff/3), 3), 0, $duree, 'Famine', 'Vos HP et MP max sont réduits de %effet%%', 'perso', 1, 0, 0, 0);
		}
		echo "Lancement du buff famine sur ".count($persos)." ".$race.", effet : ".$debuff.".\n";

		$requete = "UPDATE royaume SET food = 0 WHERE id = ".$royaume['id'];
		echo $requete."\n";
		$db->query($requete);
	}
}
// Nettoyage
$requete = "DELETE FROM buff WHERE type = 'famine' AND effet <= 0";
$db->query($requete);
?>
