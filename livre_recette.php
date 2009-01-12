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
				//alchimieing
				$player = rand(0, $joueur['alchimie']);
				$thing = rand(0, $row['difficulte']);
				//echo $joueur['alchimie'].' / '.$row['difficulte'].' ---- '.$player.' VS '.$thing;
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
				$augmentation = augmentation_competence('alchimie', $joueur, 3);
				if ($augmentation[1] == 1)
				{
					$joueur['alchimie'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur['alchimie'].' en alchimie</span><br />';
					$requete = "UPDATE perso SET alchimie = ".$joueur['alchimie']." WHERE ID = ".$joueur['ID'];
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
	$recette = new craft_recette($row['id_recette']);
	$recette->get_ingredients();
	$alchimie = $joueur['alchimie'];
	if($joueur['race'] == 'scavenger') $alchimie = round($alchimie * 1.45);
	if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $alchimie = round($alchimie * (1 + ($joueur['accessoire']['effet'] / 100)));
	$chance_reussite = pourcent_reussite($alchimie, $recette->difficulte);
	?>
	<h3><?php echo $recette->nom; ?></h3>
	<div class="information_case">
	<strong>Difficulté : <?php echo $recette->difficulte; ?></strong> <span class="small">(<?php echo $chance_reussite; ?>% de chances de réussite)</span><br />
	<strong>Ingrédients :</strong><br />
	<ul>
	<?php
	foreach($recette->ingredients as $ingredient)
	{
		$joueur_ingredient = recherche_objet($joueur, 'o'.$ingredient->id_ingredient);
		if($joueur_ingredient[0] < $ingredient->nombre)
		{
			$class = '';
			$complet = false;
		}
		else $class = 'reward';
		//Recherche de l'objet
		$requete = "SELECT nom FROM objet WHERE id = ".$ingredient->id_ingredient;
		$req_i = $db->query($requete);
		$row_i = $db->read_row($req_i);
		echo '<li><span class="'.$class.'">- '.$row_i[0].' X '.$ingredient->nombre.'</span></li>';
	}
	?>
	</ul>
	<br />
	<?php
	if($complet) echo '<a href="livre_recette.php?action=fabrique&amp;id_recette='.$row['id_recette'].'&amp;id='.$row['id'].'" onclick="return envoiInfo(this.href, \'information\');">Fabriquer <span class="xsmall">('.$row_r['pa'].' PA)</span></a>';
echo '</div>';
}
?>
</div>