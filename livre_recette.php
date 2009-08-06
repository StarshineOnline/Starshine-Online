<?php
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include ('livre.php');
$case = new map_case(convert_in_pos($joueur->get_x(), $joueur->get_y()));
$R = get_royaume_info($joueur->get_race(), $case->get_royaume());
?>
<hr>
<?php
//Fabrication
if(array_key_exists('action', $_GET))
{
	switch($_GET['action'])
	{
		case 'fabrique' :
			$recette = new craft_recette($_GET['id_recette']);
			$types = $recette->get_info_joueur($joueur, $R);
			$recette->get_recipients();
			?>
			Quel récipient voulez vous utiliser ?<b />
			<select id="id_recipient" name="id_recipient">
			<?php
			foreach($recette->recipients as $recipient)
			{
				$joueur_recipient = recherche_objet($joueur, 'o'.$recipient->id_objet);
				if($joueur_recipient[0] > 0)
				{
				?>
				<option value="<?php echo $recipient->id; ?>"><?php echo $recipient->prefixe; ?></option>
				<?php
				}
			}
			?>
			</select>
			<input type="button" value="Créer" onclick="envoiInfo('livre_recette.php?action=fabrique_final&amp;id_recette=<?php echo $recette->id; ?>&amp;id_recipient=' + $('id_recipient').value, 'information');" />
			<?php
		break;
		case 'fabrique_final' :
			$recette = new craft_recette($_GET['id_recette']);
			$recipient = new craft_recette_recipient($_GET['id_recipient']);
			$types = $recette->get_info_joueur($joueur, $R);
			$recette->get_ingredients();
			$recette->get_recipients();
			$recette->get_instruments();
			$pa_total = 0;
			$mp_total = 0;
			$star_total = 0;
			foreach($recette->instruments as $instrument)
			{
				$pa_total += $types[$instrument->type]['pa'];
				$mp_total += $types[$instrument->type]['mp'];
				$star_total += $types[$instrument->type]['cout'];
			}
			if($pa_total <= $joueur->get_pa())
			{
				if($mp_total <= $joueur->get_mp())
				{
					if($star_total <= $joueur['star'])
					{
						//On utilise le recipient
						supprime_objet($joueur, 'o'.$recipient->id_objet, 1);
						$joueur = new perso($_SESSION['ID']);;
						//alchiming
						$player = rand(0, $joueur->get_alchimie());
						$thing = rand(0, $recette->difficulte);
						echo $joueur->get_alchimie().' / '.$recette->difficulte.' ---- '.$player.' VS '.$thing;
						//Si la préparation réussie
						if($player > $thing)
						{
							echo '<h6>Fabrication réussie !</h6>';
							$resultats = explode(';', $recipient->resultat);
							$i = 0;
							while($i < count($resultats))
							{
								$objets = explode('-', $resultats[$i]);
								$j = $objets[1];
								while($j > 0)
								{
									//echo 'prend objet' . $objets[0];
									$joueur = prend_objet($objets[0], $joueur);
									//echo $G_erreur;
									$j--;
								}
								$i++;
							}
							$reussie = true;
						}
						else
						{
							echo 'La fabrication a échoué...<br />';
							$reussie = false;
						}
						//On utilise tous les objets de la recette
						foreach($recette->ingredients as $ingredient)
						{
							$rand = rand(1, 100);
							//Suppression des objets de l'inventaire
							if($reussie OR ($rand < 50))
							{
								supprime_objet($joueur, 'o'.$ingredient->id_ingredient, $ingredient->nombre);
							}
							$joueur = new perso($_SESSION['ID']);;
						}
						$difficulte = 3 * 2.65 / sqrt($pa_total);
						$augmentation = augmentation_competence('alchimie', $joueur, $difficulte);
						if ($augmentation[1] == 1)
						{
							$joueur->set_alchimie($augmentation[0]);
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_alchimie().' en alchimie</span><br />';
						}
						$joueur->set_pa($pa_total);
						$joueur->set_mp($mp_total);
						$joueur->set_star($star_total);
						$joueur->sauver();
					}
					else
					{
						echo '<h5>Vous n\'avez pas assez de stars pour faire cette recette.</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de MP pour faire cette recette.</h5>';
				}
			}
			else
			{
				echo '<h5>Vous n\'avez pas assez de PA pour faire cette recette.</h5>';
			}
		break;
	}
}
$recette = new craft_recette();
$types = $recette->get_info_joueur($joueur, $R);
$requete = "SELECT * FROM perso_recette WHERE id_perso = ".$joueur->get_id();
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	ob_start();
	$complet = true;
	if(count($types['mortier']) > 0) $possible = true;
	else $possible = false;
	//recherche de la recette
	$recette = new craft_recette($row['id_recette']);
	$recette->get_ingredients();
	$recette->get_recipients();
	$recette->get_instruments();
	$alchimie = $joueur->get_alchimie();
	if($joueur->get_race() == 'scavenger') $alchimie = round($alchimie * 1.45);
	if($joueur['accessoire']['id'] != '0' AND $joueur['accessoire']['type'] == 'fabrication') $alchimie = round($alchimie * (1 + ($joueur['accessoire']['effet'] / 100)));
	$chance_reussite = pourcent_reussite($alchimie, $recette->difficulte);
	?>
	<div class="information_case" id="recette<?php echo $row['id_recette']; ?>" style="display : none;">
		<strong>Difficulté : <?php echo $recette->difficulte; ?></strong> <span class="small">(<?php echo $chance_reussite; ?>% de chances de réussite)</span><br />
		<div class="ingredient" style="float : left;">
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
			if(!$complet) $possible = false;
			?>
			</ul>
		</div>
		<div class="recipient" style="float : left;">
			<strong>Recipients (au choix) :</strong><br />
			<ul>
			<?php
			if(count($recette->recipients) > 0) $check_recip = false;
			else $check_recip = true;
			foreach($recette->recipients as $recipient)
			{
				$joueur_recipient = recherche_objet($joueur, 'o'.$recipient->id_objet);
				if($joueur_recipient[0] < 1)
				{
					$class = '';
				}
				else
				{
					$class = 'reward';
					$check_recip = true;
				}
				//Recherche de l'objet
				$requete = "SELECT nom FROM objet WHERE id = ".$recipient->id_objet;
				$req_r = $db->query($requete);
				$row_r = $db->read_row($req_r);
				//Recherche du résultat
				$id_resultat = explode('-', $recipient->resultat);
				$id_resultat = decompose_objet($id_resultat[0]);
				$requete = "SELECT description, pa, mp, effet FROM objet WHERE id = ".$id_resultat['id_objet'];
				$req_i = $db->query($requete);
				$row_i = $db->read_assoc($req_i);
				echo '<li><span class="'.$class.'" title="'.description($row_i['description'], $row_i).' - (coute '.$row_i['pa'].' PA / '.$row_i['mp'].' MP a utiliser)">- '.$row_r[0].'</span></li>';
			}
			if(!$check_recip) $complet = false;
			if(!$complet) $possible = false;
			?>
			</ul>
		</div>
		<div class="instrument" style="float : left;">
			<strong>Instruments :</strong><br />
			<ul>
			<?php
			$pa_total = 0;
			$mp_total = 0;
			$star_total = 0;
			foreach($recette->instruments as $instrument)
			{
				echo '<li><span>'.$instrument->type.'</span></li>';
				$pa_total += $types[$instrument->type]['pa'];
				$mp_total += $types[$instrument->type]['mp'];
				$star_total += $types[$instrument->type]['cout'];
			}
			?>
			</ul>
			<?php
			echo 'PA : '.$pa_total.' - MP : '.$mp_total.' - Stars : '.$star_total.'<br />';
			?>
		</div>
		<?php
		if(count($types['mortier']) == 0) echo 'Création impossible';
		?>
		<br style="clear : both;"/>
		<?php
		if($possible) $lien = '<a href="livre_recette.php?action=fabrique&amp;id_recette='.$row['id_recette'].'" onclick="return envoiInfo(this.href, \'information\');">Fabriquer <span class="xsmall">('.$pa_total.' PA - '.$mp_total.' MP  - '.$star_total.' stars)</span></a>';
		else $lien = '';
		echo $lien;
	?>
	</div>
	<?php
	$echo = ob_get_contents();
	ob_end_clean();
	?>
	<h3><span onclick="$('recette<?php echo $row['id_recette']; ?>').toggle();"><?php echo $recette->nom.'</span> '.$lien; ?></h3>
	<?php
	echo $echo;
}
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</div>
