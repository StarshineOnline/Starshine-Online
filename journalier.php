<?php
if (file_exists('root.php'))
  include_once('root.php');

//JOURNALIER POP DES MONSTRES //

include_once(root.'class/db.class.php');
//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

$date = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

echo "Simulation de Génération des monstres sur la carte\n";

//Récupération du nombre de joueurs par niveau
$requete = "SELECT level, COUNT(*) as total FROM perso WHERE statut = 'actif' GROUP BY level";
$req = $db->query($requete);

$mail = "\nGénération des monstres\n\n";

while($row = $db->read_assoc($req))
{
	$joueur[$row['level']] = $row['total'];
}

//Récupération du nombre de monstres par niveau
$requete = "SELECT level, mm.type, COUNT(1) as total FROM map_monstre mm, monstre m WHERE mm.type = m.id GROUP BY level, type ORDER BY level ASC";
$req = $db->query($requete);

$tot = 0;
$level = 0;

while($row = $db->read_assoc($req))
{
	$monstre[$row['level']]['total'] += $row['total'];
	$monstre[$row['level']]['tot_type'] += 1;
}

/*$nbr_monstre = 1000;
$nbr_perso = 750;
$level_moyen = 6;
$joueur[1] = 10;
*/

$arenes = array();
$requete = "SELECT * FROM arenes";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	for ($x = $row['x']; $x <= $row['x'] + $row['size']; $x++)
		for ($y = $row['y']; $y <= $row['y'] + $row['size']; $y++) {
			$arenes[] = convert_in_pos($x, $y);
		}
}

// peuplement du plan de karn
$mort = time() + 22000000;
$id_plan = 145;
// Les cases considérées
$sql_cases = "select x,y from map where ".
  "x > 25 and x < 50 and y > 205 and y < 235 and info in (15,25)";
// Les cases occupées par le plan dans les cases en question
$sql_cases_occupees = "select c.x,c.y from map_monstre mm, ($sql_cases) c ".
  "where c.x = mm.x and c.y = mm.y and nc.x = c.x and nc.y = c.y and ".
  "mm.type = $id_plan";
// Les cases en questions MOINS les cases occupées parmis elles
$sql_cases_vides = "select nc.x, nc.y from ($sql_cases) nc ".
  "where not exists ($sql_cases_occupees)";
// La jointure sur les caracs du mob
$sql_insert = "select m.id, f.x, f.y, m.hp, $mort ".
  "from monstre m, ($sql_cases_vides) f where m.id = $id_plan";
// L'insert
$db->query("insert into map_monstre(type,x,y,hp,mort_naturelle) $sql_insert");

//Sélection des monstres
$requete = "SELECT * FROM monstre ORDER BY level";
//$requete = "SELECT * FROM monstre WHERE id > 140 ORDER BY level";
//$requete = "SELECT * FROM monstre WHERE id = 144 ORDER BY level";
$req = $db->query($requete);
$insert_file = tempnam("/tmp", "journalier");
$handle = fopen($insert_file, "w");
$check_virgule = false;
$total_monstre = 0;
while($row = $db->read_array($req))
{
	echo "Gestion de l'id $row[id]: $row[nom]\n";
	$tot_monstre = 0;
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
						$where .= 'x = '.$xy[0].' AND y = '.$xy[1];
					break;
				}
			}
			$where .= ')';
			$i++;
		}
		$where .= ')';
	}
	if($row['spawn_loc'] != '' OR $row['spawn'] != 0)
	{
		echo "HERE: niveau: $niveau - j[n]: ${joueur[$niveau]} \n";
		$up = ($joueur[$niveau] * 1000) / sqrt($niveau);
		if($monstre[$niveau]['tot_type'] == 0) $monstre[$niveau]['tot_type'] = 1;
		$down = $monstre[$niveau]['total'] / $monstre[$niveau]['tot_type'];
		if($down == 0) $down = 1;
		$ratio = $up / $down;
		if($ratio > 50) $ratio = 50;
		$limite = $ratio * 10000;
		$requete = "SELECT x, y, info FROM map WHERE ".$where;
		//echo $requete."\n";
		$req2 = $db->query($requete);
		while($row2 = $db->read_array($req2))
		{
			$id2 = convert_in_pos($row2['x'], $row2['y']);
			if (in_array($id2, $arenes))
				continue; // On ne fait pas poper dans les arenes

			//echo $row2['id'].' '.$row2['info'].' ';
			if (($row2['info'] === '') OR ($row2['info'] === '0')) $row2['info'] = 1;
			if (in_array($row2['info'], $terrain))
			{
				//echo $row2['info']."\n";
				//Plus c'est élevé moins les monstres spawn
				$rand = rand(0, (5000 * 1000));
				if($rand < $limite OR $spawn == 0)
				{
					$check = true;
					if($spawn == 0)
					{
						$requete = "SELECT id FROM map_monstre WHERE x = ".$row2['x']." AND y = ".$row2['y']." AND type = ".$id;
						$req4 = $db->query($requete);
						echo $requete."\n";
						if($db->num_rows > 0) $check = false;
					}
					if($check)
					{
						$temps_mort = $niveau * 1 * 30 * 24 * 60 * 60;
						$mort_naturelle = time() + $temps_mort;
						//Création d'un monstre sur la map
						//$insert .= "(NULL,'".$id."','".$coord['x']."','".$coord['y']."','".$hp."', ".$niveau.", '".addslashes($nom)."','".$lib."', ".$mort_naturelle.")";
						fwrite($handle, $id."\t".$row2['x']."\t".$row2['y']."\t".$hp."\t".
                   $mort_naturelle."\n");
						$tot_monstre++;
						$total_monstre++;
					}
				}
			}
		}
	}
	$next_line = $nom." : ".$tot_monstre." - Up : ".$up." / Down : ".$down." / Ratio : ".$ratio."\n";
	echo $next_line;
	$mail .= $next_line;
}

fclose($handle);
$ret = $db->query("LOAD DATA LOCAL INFILE \"$insert_file\" INTO TABLE map_monstre (type, x, y, hp, mort_naturelle)");
$ret_info = $db->get_mysql_info();
echo "insert done\n";
var_dump($ret_info);
if ($ret_info['warnings'] == 0) {
	unlink($insert_file);
} else {
	$msg = "Warnings detected: file `${insert_file}` kept \n";
	$log = new log_admin();
	$log->send(0, 'journalier', $msg);
}

//Si le premier du mois, pop des boss de donjons
if(date("j") == 1)
{
	//Myriandre
	$requete = "SELECT type FROM map_monstre WHERE type = 64 OR type = 65 OR type = 75";
	$db->query($requete);
	//Si il n'est pas là on le fait pop
	if($db->num_rows == 0)
	{
		$time = time() + 2678400;
		$requete = "INSERT INTO map_monstre VALUES(NULL, '64','21','217','6400',"
      .$time.")";
		$db->query($requete);
		$mail .= "Pop de Devorsis\n";
	}
	//Donjon Gob
	//Draconide 1
	$requete = "SELECT type FROM map_monstre WHERE type = 125 OR type = 126 OR type = 123";
	$db->query($requete);
	//Si il n'est pas là on le fait pop
	if($db->num_rows == 0)
	{
		$time = time() + 2678400;
		$requete = "INSERT INTO map_monstre VALUES(NULL,'125','38','284','5000',"
      .$time.")";
		$db->query($requete);
		$requete = "INSERT INTO map_monstre VALUES(NULL,'126','11','287','5000',"
      .$time.")";
		$db->query($requete);
		$mail .= "Pop du construct draconide 1, construct draconide 2\n";
	}
}
$mail .= mysql_error();

$mail_send = getenv('SSO_MAIL');
if ($mail_send == null || $mail_send == '') $mail_send = 'starshineonline@gmail.com';
mail($mail_send, 'Starshine - Génération des monstres du '.$date, $mail);
?>