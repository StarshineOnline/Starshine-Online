<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
check_undead_players();

//Si le joueur a assez de PA
if($joueur['pa'] > 30)
{
	//On recherche les informations sur ce placement
	$requete = 'SELECT x, y, construction.hp as hp_c, batiment.hp as hp_b FROM construction LEFT JOIN batiment ON batiment.id = construction.id_batiment WHERE construction.id = '.sSQL($_GET['id_construction']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur['x'], $joueur['y']), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{
		//HP redonnés
		$hp_repare = ceil(pow($joueur['architecture'], 1.5));
		$hp = $row['hp_c'] + $hp_repare;
		if($hp > $row['hp_b'])
		{
			$hp = $row['hp_b'];
			$hp_repare = $row['hp_b'] - $row['hp_c'];
		}
		
		//On met à jour le placement
		$requete = "UPDATE construction SET hp = ".$hp." WHERE id = ".sSQL($_GET['id_construction']);
		if($db->query($requete))
		{
			//On supprime les PA du joueurs
			$requete = "UPDATE perso SET pa = pa - 30 WHERE ID = ".$joueur['ID'];
			if($db->query($requete))
			{
				//Augmentation de la compétence d'architecture
				$augmentation = augmentation_competence('architecture', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur['architecture'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['architecture'].' en architecture</span><br />';
					$requete = "UPDATE perso SET architecture = ".$joueur['architecture']." WHERE ID = ".$joueur['ID'];
					$db->query($requete);
				}
				echo '<h6>La construction a été réparée de '.$hp_repare.' HP</h6>';
			}
		}
	}
}
?>
</div>