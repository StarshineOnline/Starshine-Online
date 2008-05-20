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

//Nombre de joueurs total
$requete = "SELECT COUNT(*) FROM perso WHERE statut = 'actif'";
$req = $db->query($requete);
$row = $db->read_row($req);
$nbr_perso = $row[0];

echo 'Nombre de joueurs '.$nbr_perso.'<br />';

//On vire le monstres trop vieux
$requete = "DELETE FROM map_monstre WHERE mort_naturelle < ".time();
echo $requete.'<br />';
//$db->query($requete);
//Sélection des monstres
$requete = "SELECT * FROM monstre WHERE id = 57";
echo $requete.'<br />';
$req = $db->query($requete);
while($row = $db->read_array($req))
{
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
	$ecart = abs($niveau - $level_moyen);
	if($ecart == 0) $rate_niveau = 2;
	elseif($ecart == 1) $rate_niveau = 1.8;
	elseif($ecart == 2) $rate_niveau = 1.6;
	elseif($ecart == 3) $rate_niveau = 1.3;
	elseif($ecart == 4) $rate_niveau = 1;
	else $rate_niveau = 0.5;
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
			$list = substr($spawn_loc, 1, strlen($spawn_loc));
			$coords = explode('.', $list);
			foreach($coords as $coord)
			{
				$xy = explode('-', $coord);
				//print_r($xy);
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
	$requete = "SELECT ID, info FROM map WHERE ".$where;
	echo $requete.'<br />';
	$req2 = $db->query($requete);
	while($row2 = $db->read_array($req2))
	{
		//echo $row2['ID'].' '.$row2['info'].' ';
		if (($row2['info'] == '') OR ($row2['info'] == '0')) $row2['info'] = 1;
		if (in_array($row2['info'], $terrain))
		{
			//echo $row2['info'].'<br />';
			$rand = rand(0, floor((1500000 * $nbr_monstre) / ($G_spawn_rate * $nbr_perso * $nbr_perso * $rate_niveau)));
			if($spawn > $rand OR $spawn == 0)
			{
				$check = true;
				$coord = convert_in_coord($row2['ID']);
				if($spawn == 0)
				{
					echo $spawn.' Check si ya déjà un monstre<br />';
					$requete = "SELECT id FROM map_monstre WHERE x = ".$coord['x']." AND y = ".$coord['y']." AND type = ".$id;
					$req4 = $db->query($requete);
					echo $requete.'<br />';
					if($db->num_rows > 0)
					{
						echo 'FALSE '.$db->num_rows.'<br />';
						$check = false;
					}
					else echo 'TRUE '.$db->num_rows.'<br />';
				}
				if($check)
				{
					$temps_mort = $niveau * 1 * 30 * 24 * 60 * 60;
					$mort_naturelle = time() + $temps_mort;
					//Création d'un monstre sur la map
					$requete = "INSERT INTO map_monstre VALUES('','".$id."','".$coord['x']."','".$coord['y']."','".$hp."', ".$niveau.", '".addslashes($nom)."','".$lib."', ".$mort_naturelle.")";
					//echo $requete.'<br />';
					//$req3 = $db->query($requete);
					$tot_monstre++;
				}
			}
		}
	}
	$mail .= $nom." : ".$tot_monstre."\n";
}
?>