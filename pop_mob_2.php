<?php
include('class/db.class.php');
include('fonction/time.inc');
include('fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include('fonction/quete.inc');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include('fonction/equipement.inc');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

$nbr_perso = 500;
//Sélection des monstres
$requete = "SELECT * FROM monstre WHERE level < 5";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	//Génération de monstres sur la carte dont l'identifiant est generation_id
	$id = $row['id'];
	//Selectionne les informations du monstres
	$spawn = $row['spawn'];
	$terrain = explode(';', $row['terrain']);
	$hp = $row['hp'];
	$nom = $row['nom'];
	$lib = $row['lib'];
	$where = '1 ';
	$spawns = explode(';', $row['spawn_loc']);
	if($row['spawn_loc'] != '')
	{
		foreach($spawns as $spawn_loc)
		{
			$where .= 'AND (';
			$type = $spawn_loc[0];
			$list = substr($spawn_loc, 1, strlen($spawn_loc));
			$coords = explode('.', $list);
			foreach($coords as $coord)
			{
				$xy = explode('-', $coord);
				print_r($xy);
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
				$where .= ')';
			}
		}
	}
	$requete = "SELECT * FROM map WHERE ".$where;
	//echo $requete.'<br />';
	$req2 = $db->query($requete);
	while($row2 = $db->read_array($req2))
	{
		echo $row2['ID'].' '.$row2['info'].' ';
		if (($row2['info'] == '') OR ($row2['info'] == '0')) $row2['info'] = 1;
		if (in_array($row2['info'], $terrain))
		{
			echo $row2['info'].'<br />';
			$rand = rand(0, floor(150000000 / ($G_spawn_rate * $nbr_perso)));
			if($spawn > $rand OR $spawn == 0)
			{
				$coord = convert_in_coord($row2['ID']);
				//Création d'un monstre sur la map
				$requete = "INSERT INTO map_monstre VALUES('','".$id."','".$coord['x']."','".$coord['y']."','".$hp."','".addslashes($nom)."','".$lib."')";
				//echo $requete;
				$req3 = $db->query($requete);
			}
		}
	}
}
?>