<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
check_undead_players();

if ($joueur->is_buff('debuff_rvr'))
{
	echo '<h5>RvR impossible pendant la trêve</h5>';
}
//Si le joueur a assez de PA
elseif($joueur->get_pa() >= 30)
{
	$ID_CONSTRUCTION = sSQL($_GET['id_construction'], SSQL_INTEGER);

	//On recherche les informations sur ce placement
	$requete = 'SELECT x, y, fin_placement, debut_placement FROM placement WHERE id = \''.$ID_CONSTRUCTION.'\'';
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

	if ($row['fin_placement'] < time())
	{
		security_block(BAD_ENTRY, "Construction déjà finie !");
	}

	$buffs = placement::get_placement_buff($ID_CONSTRUCTION);
	foreach ($buffs as $b) {
		if ($b->type == 'sabotage') {
			echo '<h5>Ce bâtiment est saboté</h5></div>';
			print_onload_infoperso();
			exit(0);
		}
	}
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur->get_x(), $joueur->get_y()), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{
		if ($row['debut_placement'] == 0)
			security_block(BAD_ENTRY, "Erreur de paramètre");

		//Seconde supprimées du décompte
		$seconde_prct = floor(($row['fin_placement'] - $row['debut_placement']) * (sqrt($joueur->get_architecture()) / 100));
		$secondes_max = min(round(15000 * sqrt($joueur->get_architecture())), $seconde_prct);

		$secondes_min = min(250 * $joueur->get_architecture(), round($seconde_prct / 2));
		// Gemme de fabrique : augmente de effet % l'accélération
		if ($joueur->is_enchantement('forge'))
		{
			$secondes_max += floor($joueur->get_enchantement('forge', 'effet') / 100 * $secondes_max);
		}
		$secondes = rand($secondes_min, $secondes_max);
    if( $joueur->is_buff('convalescence') ) $secondes=floor($secondes / 2);
		//On met à jour le placement
		$requete = "UPDATE placement SET fin_placement = fin_placement - ".$secondes." WHERE id = ".sSQL($_GET['id_construction']);
		if($db->query($requete))
		{
			//On supprime les PA du joueurs
			$joueur->set_pa($joueur->get_pa() - 30);
			//Augmentation de la compétence d'architecture
			$augmentation = augmentation_competence('architecture', $joueur, 1);
			if ($augmentation[1] == 1)
			{
				$joueur->set_architecture($augmentation[0]);
			}
			echo '<h6>La construction a été accélérée de '.transform_sec_temp($secondes).'</h6>';
			echo '<a href="archi_accelere_construction.php?id_construction='.$_GET['id_construction'].'" onclick="return envoiInfo(this.href, \'information\');">Accélérer de nouveau</a>';
			$joueur->sauver();
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
