<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);;

$joueur->check_perso();

$position = convert_in_pos($joueur->get_x(), $joueur->get_y());

//V?rifie si le perso est mort
verif_mort($joueur, 1);
$verif_ville = verif_ville($joueur->get_x(), $joueur->get_y());
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($position).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);
//$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);
$_SESSION['position'] = $position;
?>
	<?php 
	include_once(root.'ville_bas.php');
	?>
	<div class="ville_test">
	<?php
	if($verif_ville AND $R->get_diplo($joueur->get_race()) == 127)
	{
		if(array_key_exists('id_chantier', $_GET))
		{
			//Il faut qu'il ai 10 PA
			if($joueur->get_pa() >= 10)
			{
				$chantier = new terrain_chantier($_GET['id_chantier']);
				$batiment = $chantier->get_batiment();
				//dé d'Architecture
				$de_architecture = rand(1, $joueur->get_architecture());
				$taxe = floor(($chantier->star_point * $de_architecture) * $R->get_taxe_diplo($joueur->get_race()) / 100);
				$stars = ($chantier->star_point * $de_architecture) - $taxe;
				echo 'Vous aidez à construire le batiment pour '.$de_architecture.' points de structure.<br />
				Et vous recevez '.$stars.' stars<br />';
				//Augmentation de la compétence d'architecture
				$augmentation = augmentation_competence('architecture', $joueur, 2);
				if ($augmentation[1] == 1)
				{
					$joueur->set_architecture($augmentation[0]);
				}
				//Gain de stars et Suppression des PA
        $joueur->set_star($joueur->get_star() + $stars);
        $joueur->set_pa($joueur->get_pa() - 10);
        $joueur->sauver();
				$delete = false;
				//Fin de la construction ?
				if(($chantier->point + $de_architecture) >= $batiment->point_structure)
				{
					//Si c'est un aggrandissement
					if($batiment->type == 'agrandissement')
					{
						$terrain = new terrain($chantier->id_terrain);
						$terrain->nb_case = $batiment->effet;
						$terrain->sauver();
					}
					//Sinon on fait la construction
					else
					{
						if($chantier->upgrade_id_construction == 0)
						{
							$construction = new terrain_construction();
							$construction->id_terrain = $chantier->id_terrain;
							$construction->id_batiment = $chantier->id_batiment;
							$construction->sauver();
						}
						else
						{
							$construction = new construction($chantier->upgrade_id_construction);
							$construction->id_batiment = $chantier->id_batiment;
							$construction->sauver();
						}
					}
					//On supprime le chantier
					$chantier->supprimer();
					$delete = true;
				}
				//Avancée de la construction
				else
				{
					$chantier->point += $de_architecture;
					$chantier->sauver();
				}
				if(!$delete) echo '<a href="terrain_chantier.php?id_chantier='.$chantier->id.'" onclick="return envoiInfo(this.href, \'carte\');">Continuer 10 (PA)</a>';
			}
			else echo '<h5>Vous n\'avez pas assez de PA</h5>';
		}
		else
		{
			echo 'Liste des chantiers disponibles :<br />';
			$requete = "SELECT terrain_chantier.id as id, id_terrain, id_batiment, point, star_point FROM terrain_chantier LEFT JOIN terrain ON terrain.id = terrain_chantier.id_terrain LEFT JOIN perso ON terrain.id_joueur = perso.ID WHERE perso.race = '".$R->get_race()."' ORDER BY star_point DESC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$chantier = new terrain_chantier($row);
				$batiment = $chantier->get_batiment();
				$taxe = floor(($chantier->star_point * 100) * $R->get_taxe_diplo($joueur->get_race()) / 100);
				$prix = ($chantier->star_point * 100) - $taxe;
				echo ucwords($batiment->type).' ('.$prix.' stars par 100 point) => '.$chantier->point.' / '.$batiment->point_structure.' <a href="terrain_chantier.php?id_chantier='.$chantier->id.'" onclick="return envoiInfo(this.href, \'carte\');">Construire (10 PA)</a><br />';
			}
		}
	}
	?>
	<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
	</div>