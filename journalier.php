<?php


include('class/db.class.php');
//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

echo 'Simulation de Génération des monstres sur la carte<br />';

//Récupération du nombre de joueurs par niveau
$requete = "SELECT level, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY level";
$req = $db->query($requete);

$mail = "\nGénération des monstres\n\n";

while($row = $db->read_assoc($req))
{
	$joueur[$row['level']] = $row['total'];
}

//Récupération du nombre de monstres par niveau
$requete = "SELECT level, type, COUNT(*) as total FROM map_monstre GROUP BY level, type ORDER BY level ASC";
$req = $db->query($requete);

$tot = 0;
$level = 0;

while($row = $db->read_assoc($req))
{
	$monstre[$row['level']]['total'] += $row['total'];
	$monstre[$row['level']]['tot_type'] += 1;
}

/*$nbr_monstre = 250000;
$nbr_perso = 750;
$level_moyen = 6;*/

//Sélection des monstres
$requete = "SELECT * FROM monstre ORDER BY level";
$req = $db->query($requete);
$insert = 'INSERT INTO map_monstre VALUES';
$check_virgule = false;
while($row = $db->read_array($req))
{
	$tot_monstre = 0;
	$total_monstre = 0;
	//Génération de monstres sur la carte dont l'identifiant est generation_id
	$id = $row['id'];
	//Selectionne les informations du monstres
	$spawn = $row['spawn'];
	$terrain = explode(';', $row['terrain']);
	$hp = $row['hp'];
	$nom = $row['nom'];
	$lib = $row['lib'];
	$niveau = $row['level'];
	$where = '1';
	$spawns = explode(';', $row['spawn_loc']);
	if($row['spawn_loc'] != '')
	{
		$where .= ' AND (';
		$i = 0;
		foreach($spawns as $spawn_loc)
		{
			if($i != 0) $where .= ' OR ';
			$where .= '(';
			$type = $spawn_loc[0];
			$list = mb_substr($spawn_loc, 1, strlen($spawn_loc));
			$coords = explode('.', $list);
			foreach($coords as $coord)
			{
				$xy = explode('-', $coord);
				switch($type)
				{
					case 'c' :
					break;
					case 'd' :
					break;
					case 'p' :
						$where .= '(ID - (floor(ID / 1000) * 1000)) = '.$xy[0].' AND (floor(ID / 1000)) = '.$xy[1];
					break;
				}
			}
			$where .= ')';
			$i++;
		}
		$where .= ')';
	}
	$up = ($joueur[$niveau] * 1000) / sqrt($niveau);
	if($monstre[$niveau]['tot_type'] == 0) $monstre[$niveau]['tot_type'] = 1;
	$down = $monstre[$niveau]['total'] / $monstre[$niveau]['tot_type'];
	if($up < 1) $up = 1;
	if($down == 0) $down = 1;
	$ratio = $up / $down;
	if($ratio > 50) $ratio = 50;
	$limite = $ratio * 10000;
	$requete = "SELECT ID, info FROM map WHERE ".$where;
	//echo $requete.'<br />';
	$req2 = $db->query($requete);
	while($row2 = $db->read_array($req2))
	{
		//echo $row2['ID'].' '.$row2['info'].' ';
		if (($row2['info'] == '') OR ($row2['info'] == '0')) $row2['info'] = 1;
		if (in_array($row2['info'], $terrain))
		{
			//echo $row2['info'].'<br />';
			$rand = rand(0, 1000000);
			if($rand < $limite OR $spawn == 0)
			{
				$check = true;
				$coord = convert_in_coord($row2['ID']);
				if($spawn == 0)
				{
					$requete = "SELECT id FROM map_monstre WHERE x = ".$coord['x']." AND y = ".$coord['y']." AND type = ".$id;
					$req4 = $db->query($requete);
					//echo $requete.'<br />';
					if($db->num_rows > 0) $check = false;
				}
				if($check)
				{
					$temps_mort = $niveau * 1 * 30 * 24 * 60 * 60;
					$mort_naturelle = time() + $temps_mort;
					if(!$check_virgule) $check_virgule = true;
					else
					{
						$insert .= ', ';
					}
					//Création d'un monstre sur la map
					$insert .= "('','".$id."','".$coord['x']."','".$coord['y']."','".$hp."', ".$niveau.", '".addslashes($nom)."','".$lib."', ".$mort_naturelle.")";
					//echo $requete.'<br />';
					//$req3 = $db->query($requete);
					$tot_monstre++;
					$total_monstre++;
					if(($total_monstre % 500) == 0)
					{
						$db->query($insert);
						//echo $insert.'<br /><br /><br /><br />';
						$insert = 'INSERT INTO map_monstre VALUES';
						$check_virgule = false;
					}
				}
			}
		}
	}
	$mail .= $nom." : ".$tot_monstre." - Up : ".$up." / Down : ".$down." / Ratio : ".$ratio."\n";
}

$db->query($requete);
//echo $insert.'<br /><br /><br /><br />';

$mail .= mysql_error();

mail('masterob1@chello.fr', 'Starshine - Génération des monstres du '.$date, $mail);

?>