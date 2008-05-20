<?php
//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include ('livre.php');
$W_case = 1000 * $joueur['y'] + $joueur['x'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
?>
<hr>
<?php
//Fabrication
if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'fabrique' :
			$requete = "SELECT * FROM recette WHERE id = ".sSQL($_GET['id_recette']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$pa_r = $row['pa'];
			if($pa_r <= $joueur['pa'])
			{
				$ingredients = explode(';', $row['ingredient']);
				$i = 0;
				//On utilise tous les objets de la recette
				while($i < count($ingredients))
				{
					$ingredient_exp = explode('-', $ingredients[$i]);
					$ingredient_id = $ingredient_exp[0];
					$ingredient_nb = $ingredient_exp[1];
					//Suppression des objets de l'inventaire
					supprime_objet($joueur, 'o'.$ingredient_id, $ingredient_nb);
					$joueur = recupperso($_SESSION['ID']);
					$i++;
				}
				$requete = "SELECT nombre FROM perso_recette WHERE id = ".sSQL($_GET['id']);
				$req_n = $db->query($requete);
				$row_n = $db->read_row($req_n);
				//On supprime la recette si recette limitée
				if($row_n[0] > 0)
				{
					if($row_n[0] == 1)
					{
						$requete = "DELETE FROM perso_recette WHERE id = ".sSQL($_GET['id']);
					}
					else
					{
						$requete = "UPDATE perso_recette SET nombre = nombre - 1 WHERE id = ".sSQL($_GET['id']);
					}
					$db->query($requete);
				}
				//Crafting
				$player = rand(0, $joueur['craft']);
				$thing = rand(0, $row['difficulte']);
				//echo $joueur['craft'].' / '.$row['difficulte'].' ---- '.$player.' VS '.$thing;
				//Si la préparation réussie
				if($player > $thing)
				{
					echo 'Fabrication réussie !<br />';
					$resultats = explode(';', $row['resultat']);
					$i = 0;
					while($i < count($resultats))
					{
						$objets = explode('-', $resultats[$i]);
						$j = $objets[1];
						while($j > 0)
						{
							prend_objet($objets[0], $joueur);
							//echo $G_erreur;
							$j--;
						}
						$i++;
					}
				}
				else
				{
					echo 'La fabrication a échoué...<br />';
				}
				$augmentation = augmentation_competence('craft', $joueur, 3);
				if ($augmentation[1] == 1)
				{
					$joueur['craft'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['craft'].' en fabrication d\'objets</span><br />';
					$requete = "UPDATE perso SET craft = ".$joueur['craft']." WHERE ID = ".$joueur['ID'];
					$req = $db->query($requete);
				}
				$joueur['pa'] -= $pa_r;
				$requete = "UPDATE perso SET pa = ".$joueur['pa']." WHERE ID = ".$joueur['ID'];
				$req = $db->query($requete);
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA pour faire cette recette.';
			}
		break;
	}
}
$requete = "SELECT * FROM perso_recette WHERE id_perso = ".$joueur['ID'];
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$complet = true;
	//recherche de la recette
	$requete = "SELECT * FROM recette WHERE id = ".$row['id_recette'];
	$req_r = $db->query($requete);
	$row_r = $db->read_assoc($req_r);
	$craft = $joueur['craft'];
	if($joueur['race'] == 'scavenger') $craft = round($craft * 1.45);
	if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $craft = round($craft * (1 + ($joueur['accessoire']['effet'] / 100)));
	$chance_reussite = pourcent_reussite($craft, $row_r['difficulte']);
	?>
	<h3><?php echo $row_r['nom']; ?></h3>
	<div class="information_case">
	<strong>Difficulté : <?php echo $row_r['difficulte']; ?></strong> <span class="small">(<?php echo $chance_reussite; ?>% de chances de réussite)</span><br />
	<strong>Nombre d'utilisations : </strong><?php if($row['nombre'] == 0) echo 'Illimité'; else echo $row['nombre']; ?><br />
	<strong>Ingrédients :</strong><br />
	<ul>
	<?php
	$ingredients = explode(';', $row_r['ingredient']);
	$i = 0;
	while($i < count($ingredients))
	{
		$ingredient_exp = explode('-', $ingredients[$i]);
		$ingredient_id = $ingredient_exp[0];
		$ingredient_nb = $ingredient_exp[1];
		$joueur_ingredient = recherche_objet($joueur, 'o'.$ingredient_id);
		if($joueur_ingredient[0] < $ingredient_nb) $complet = false;
		//Recherche de l'objet
		$requete = "SELECT nom FROM objet WHERE id = ".$ingredient_id;
		$req_i = $db->query($requete);
		$row_i = $db->read_row($req_i);
		if($joueur_ingredient[0] >= $ingredient_nb) $class = 'reward';
		else $class = '';
		echo '<li><span class="'.$class.'">- '.$row_i[0].' X '.$ingredient_nb.'</span></li>';
		$i++;
	}
	?>
	</ul>
	<br />
	<?php
	if($complet) echo '<a href="javascript:envoiInfo(\'livre_recette.php?action=fabrique&amp;id_recette='.$row['id_recette'].'&amp;id='.$row['id'].'\', \'information\');">Fabriquer <span class="xsmall">('.$row_r['pa'].' PA)</span></a>';
echo '</div>';
}
?>
</div>