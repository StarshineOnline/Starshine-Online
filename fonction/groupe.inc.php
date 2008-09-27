<?php

//Récupération des infos du groupe
function recupgroupe($id, $option)
{
	if($id > 0)
	{
		global $db;
		$groupe = array();
		$requete = 'SELECT * FROM groupe WHERE ID = '.$id;
		$req = $db->query($requete);
		$groupe = $db->read_assoc($req);
		$requete = 'SELECT * FROM groupe_joueur WHERE id_groupe = '.$id.' ORDER BY leader ASC, id_joueur ASC';
		$req = $db->query($requete);
		$groupe['share_xp'] = 0;
		$i = 0;
		$groupe['somme_groupe'] = 0;
		$max = 0;
		while($row = $db->read_assoc($req))
		{
			$groupe['membre'][] = $row;
			if($option != '')
			{
				$requete = "SELECT x, y, level, race, nom FROM perso WHERE ID = ".$row['id_joueur'];
				$req_j = $db->query($requete);
				$row_j = $db->read_assoc($req_j);
				$groupe['membre'][$i] = array_merge($groupe['membre'][$i], $row_j);
				$groupe['somme_groupe'] += $row_j['level'];
				if($row_j['level'] > $max) $max = $row_j['level'];
				$pos1 = convert_in_pos($row_j['x'], $row_j['y']);
				$coord_j2 = explode('-', $option);
				$pos2 = convert_in_pos($coord_j2[0], $coord_j2[1]);
				$distance = calcul_distance_pytagore($pos1, $pos2);
				$groupe['membre'][$i]['distance'] = $distance;
				if($distance <= 10) $groupe['membre'][$i]['share_xp'] = 100;
				else
				{
					$groupe['membre'][$i]['share_xp'] = 100 - ($distance * 2);
					if($groupe['membre'][$i]['share_xp'] < 0) $groupe['membre'][$i]['share_xp'] = 0;
				}
				$groupe['membre'][$i]['share_xp'] = $groupe['membre'][$i]['share_xp'] * $groupe['membre'][$i]['level'];
				$groupe['share_xp'] += $groupe['membre'][$i]['share_xp'];
			}
			if($row['leader'] == 'y')
			{
				$groupe['leader'] = $i;
				$groupe['id_leader'] = $row['id_joueur'];
			}
			$i++;
		}
		$groupe['nombre_joueur'] = count($groupe['membre']);
		if($option != '')
		{
			$groupe['moy_groupe'] = ($groupe['somme_groupe'] - $max) / $groupe['nombre_joueur'];
			$groupe['level_groupe'] = $max + ceil(($groupe['moy_groupe'] / $max) * floor(sqrt($groupe['moy_groupe'])));
		}
		return $groupe;
	}
	else return false;
}

function level_groupe($groupe)
{
	global $db;
	$level_groupe = 0;
	$somme_groupe_carre = 0;
	$group_id = array();
	foreach($groupe['membre'] as $membre)
	{
		$group_id[] = $membre['id_joueur'];
	}
	$group_id = implode(',', $group_id);
	//Requete de sélection des persos membres du groupe
	$W_requete_perso = 'SELECT level FROM perso WHERE ID IN ('.$group_id.')';
	$W_req_perso = $db->query($W_requete_perso);
	while($row = $db->read_array($W_req_perso))
	{
		$somme_groupe += $row['level'];
		$somme_groupe_carre += $row['level'] * $row['level'];
		$group[] = $row['level'];
	}
	$max_groupe = max($group);
	$moy_groupe = ($somme_groupe - $max_groupe) / count($group);
	$level_groupe = $max_groupe + ceil(($somme_groupe_carre) / ($max_groupe * 25)) + 1;
	$array = array($level_groupe, $somme_groupe);
	return $array;
}

//Fonction chargée de dégroupé le joueur ID_joueur du groupe ID_groupe
function degroup($ID_joueur, $ID_groupe)
{
	global $db;
	//Requète de séléction du groupe du joueur
	$groupe = recupgroupe($ID_groupe, '');
	//print_r($groupe);
	//Cherche quel numéro de membre il a.
	$num_joueur = groupe_trouve_joueur($ID_joueur, $groupe);
	//echo count($groupe['membre']);
	//Si il y a d'autre joueurs
	if($groupe['nombre_joueur'] > 1)
	{
		//Si le joueur est leader, on transfère au joueur suivant
		if($groupe['id_leader'] == $ID_joueur)
		{
			$requete = "UPDATE groupe_joueur SET leader = 'y' WHERE id = ".$groupe['membre'][1]['id'];
			$db->query($requete);
		}
		//On supprime le joueur du groupe
		$requete = 'DELETE FROM groupe_joueur WHERE id_joueur = '.$ID_joueur;
		$db->query($requete);
		$requete = 'UPDATE perso SET groupe = 0 WHERE ID = '.$ID_joueur;
		$db->query($requete);
		//Modif du prochain loot si mode par tour
		if($groupe['partage'] == 't')
		{
			//Si c'est pas le dernier alors suivant
			if(($groupe['nombre_joueur'] - 1) != $num_joueur)
			{
				$requete = "UPDATE groupe SET prochain_loot = ".$groupe['membre'][($num_joueur + 1)]['id_joueur']." WHERE id = ".$ID_groupe;
			}
			//Sinon premier
			else
			{
				$requete = "UPDATE groupe SET prochain_loot = ".$groupe['membre'][0]['id_joueur']." WHERE id = ".$ID_groupe;
			}
			$db->query($requete);
		}
	}
	//Sinon on supprime le groupe
	else
	{
		$requete = "DELETE FROM groupe WHERE id = ".$groupe['id'];
		$db->query($requete);
		$requete = "UPDATE perso SET groupe = 0 WHERE groupe = ".$groupe['id'];
		$db->query($requete);
		$requete = 'DELETE FROM groupe_joueur WHERE id_groupe = '.$groupe['id'];
		$db->query($requete);
		$requete = 'DELETE FROM invitation WHERE groupe = '.$groupe['id'];
		$db->query($requete);
	}
	echo 'Le joueur a bien quitté le groupe';
}

function groupe_trouve_joueur($id_joueur, $groupe)
{
	$i = 0;
	$bool = true;
	while(($i < 5) AND $bool)
	{
		if ($groupe['membre'][$i]['id_joueur'] == $id_joueur)
		{
			return $i;
			$bool = false;
		}
		$i++;
	}
	return false;
}

?>