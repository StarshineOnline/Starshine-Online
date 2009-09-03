<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
check_undead_players();

//Si le joueur a assez de PA
if($joueur->get_pa() >= 30)
{
	//On recherche les informations sur ce placement
	$requete = 'SELECT x, y, construction.hp as hp_c, batiment.hp as hp_b FROM construction LEFT JOIN batiment ON batiment.id = construction.id_batiment WHERE construction.id = '.sSQL($_GET['id_construction']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur->get_x(), $joueur->get_y()), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{
		//HP redonnés
		$hp_repare_max = ceil(pow($joueur->get_architecture(), 1.5));
		$hp_repare_min = ceil($hp_repare_max / 3);
		$hp_repare = rand($hp_repare_min, $hp_repare_max);
		$hp = $row['hp_c'] + $hp_repare;
		if($hp > $row['hp_b'])
		{
			$hp = $row['hp_b'];
			$hp_repare = $row['hp_b'] - $row['hp_c'];
		}
		//On vérifie qu'il y a bien eu réparation du batiment
		if($hp_repare > 0)
		{
			//On met à jour le placement
			$requete = "UPDATE construction SET hp = ".$hp." WHERE id = ".sSQL($_GET['id_construction']);
			if($db->query($requete))
			{
				//On supprime les PA du joueurs
				$joueur->set_pa($joueur->get_pa() - 30);
				//Augmentation de la compétence d'architecture
				$augmentation = augmentation_competence('architecture', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_architecture($augmentation[0]);
					$joueur->sauver();
				}
				echo '<h6>La construction a été réparée de '.$hp_repare.' HP</h6>';
				$joueur->sauver();
			}
		}
		else echo '<h5>La construction est déjà totalement réparée</h5>';
	}
}
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</div>
