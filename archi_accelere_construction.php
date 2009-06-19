<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
check_undead_players();

//Si le joueur a assez de PA
if($joueur['pa'] >= 30)
{
	//On recherche les informations sur ce placement
	$requete = 'SELECT * FROM placement WHERE id = '.sSQL($_GET['id_construction']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

	if ($row['fin_placement'] < time()) {
		security_block(BAD_ENTRY, "Construction déjà finie !");
	}
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur['x'], $joueur['y']), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{
		//Seconde supprimées du décompte
		$secondes_max = floor(($row['fin_placement'] - $row['debut_placement']) * sqrt($joueur['architecture']) / 100);

		// Gemme de fabrique : augmente de effet % le max possible
		if (isset($joueur['enchantement']) &&
				isset($joueur['enchantement']['forge'])) {
			$secondes_max += floor($joueur['enchantement']['forge']['effet'] / 100 * $secondes_max);
		}

		$secondes_min = round($secondes_max / 2);
		$secondes = round(rand($secondes_min, $secondes_max));
		//On met à jour le placement
		$requete = "UPDATE placement SET fin_placement = fin_placement - ".$secondes." WHERE id = ".sSQL($_GET['id_construction']);
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
				echo '<h6>La construction a été accélérée de '.transform_sec_temp($secondes).'</h6>';
				echo '<a href="archi_accelere_construction.php?id_construction='.$_GET['id_construction'].'" onclick="return envoiInfo(this.href, \'information\');">Accélérer de nouveau</a>';
			}
		}
	}
}
else
{
	echo '<h5>Vous n\'avez pas assez de PA</h5>';
}
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</div>