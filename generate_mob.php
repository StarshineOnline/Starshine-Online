<?php

include('inc/fp.php');

echo 'Génération des monstres sur la carte<br />';
//Nombre de joueurs total
$nbr_perso = 200;
//Sélection des monstres
$requete = "SELECT * FROM monstre WHERE level = 1";
$req = $db->query($requete);
while($row = $db->read_array($req))
{
	//Génération de monstres sur la carte dont l'identifiant est generation_id
	$id = $row['id'];
	//Selectionne les informations du monstres
	$spawn = $row['spawn'];
	$terrain = $row['terrain'];
	$hp = $row['hp'];
	$nom = $row['nom'];
	$lib = $row['lib'];
	$requete = "SELECT * FROM map";
	$req2 = $db->query($requete);
	while($row2 = $db->read_array($req2))
	{
		if (($row2['info'] == '') OR ($row2['info'] == '0')) $row2['info'] = 1;
		if ($row2['info'] == $terrain)
		{
			$rand = rand(0, floor(150000000 / ($G_spawn_rate * $nbr_perso)));
			if($spawn > $rand)
			{
				$coord = convert_in_coord($row2['ID']);
				//Création d'un monstre sur la map
				$requete = "INSERT INTO map_monstre VALUES('','".$id."','".$coord['x']."','".$coord['y']."','".$hp."','".addslashes($nom)."','".$lib."')";
				$req3 = $db->query($requete);
			}
		}
	}
}