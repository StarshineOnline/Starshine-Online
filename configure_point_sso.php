<?php
include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);
$bonus = recup_bonus($joueur['ID']);
?>
	<div id="centre2">
		<?php
		if(array_key_exists('id', $_GET) OR array_key_exists('id', $_POST))
		{
			if(array_key_exists('id', $_GET)) $id = $_GET['id'];
			elseif(array_key_exists('id', $_POST)) $id = $_POST['id'];
			//Changement de l'état
			if(array_key_exists('etat', $_GET))
			{
				//Si besoin de modifier dans la table personnage
				if($id == 7 OR $id == 8 OR $id == 11)
				{
					switch($id)
					{
						case 7 :
							$champ = 'cache_classe';
						break;
						case 8 :
							$champ = 'cache_stat';
						break;
						case 11 :
							$champ = 'cache_niveau';
						break;
					}
					$requete = "UPDATE perso SET ".$champ." = ".$_GET['etat']." WHERE ID = ".$joueur['ID'];
					$db->query($requete);
				}
				$bonus_total = recup_bonus_total($joueur['ID']);
				$requete = "UPDATE bonus_perso SET etat = ".sSQL($_GET['etat'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur['ID']);
			}
			//Changement de sexe
			if(array_key_exists('sexe', $_GET))
			{
				$bonus_total = recup_bonus_total($joueur['ID']);
				$requete = "UPDATE bonus_perso SET valeur = ".sSQL($_GET['sexe'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur['ID']);
			}
			//Changement de description
			if(array_key_exists('description', $_GET))
			{
				$bonus_total = recup_bonus_total($joueur['ID']);
				$requete = "UPDATE bonus_perso SET valeur = '".sSQL(htmlspecialchars($_GET['description']))."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur['ID']);
			}
			//Changement de css
			if(array_key_exists('css', $_GET))
			{
				$bonus_total = recup_bonus_total($joueur['ID']);
				$requete = "UPDATE bonus_perso SET valeur = '".sSQL(htmlspecialchars($_GET['css']))."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur['ID']);
			}
			//Avatar
			if(array_key_exists('nom_du_fichier', $_FILES))
			{
				if($_FILES['nom_du_fichier']['error'])
				{
					switch ($_FILES['nom_du_fichier']['error'])
					{
						case 1 :
							echo "Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";
						break;
						case 2 :
							echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
						break;
						case 3 :
							echo "L'envoi du fichier a été interrompu pendant le transfert !";
						break;
						case 4 :
							echo "Le fichier que vous avez envoyé a une taille nulle !";
						break;
					}
				}
				else
				{
					$chemin_destination = 'image/avatar/';
					//Vérification du type
					$type_file = $_FILES['nom_du_fichier']['type'];
					if(strstr($type_file, 'jpg') OR strstr($type_file, 'jpeg'))
					{
						$type = '.jpg';
					}
					elseif(strstr($type_file, 'bmp'))
					{
						$type = '.bmp';
					}
					elseif(strstr($type_file, 'gif'))
					{
						$type = '.gif';
					}
					elseif(strstr($type_file, 'png'))
					{
						$type = '.png';
					}
					else
					{
						exit("Le fichier n'est pas une image");
					}
					//Récupère le type
					$nom_fichier = $chemin_destination.$joueur['ID'].$type;
					if(move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $chemin_destination.$joueur['ID'].$type))
					{
						//On vérifie la taille de l'image
						$size = getimagesize($nom_fichier);
						//Si compris entre 80 * 80
						if($size[0] <= 80 AND $size[1] <= 80)
						{
							$bonus_total = recup_bonus_total($joueur['ID']);
							$requete = "UPDATE bonus_perso SET valeur = '".sSQL($joueur['ID'].$type)."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
							$db->query($requete);
							$bonus = recup_bonus($joueur['ID']);
						}
						//Sinon on efface l'image
						else
						{
							unlink($nom_fichier);
							echo '<h5>Votre fichier n\'a pas les bonnes dimensions !</h5>';
						}
					}
				}
			}
			$requete = "SELECT * FROM bonus WHERE id_bonus = ".sSQL($id);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			?>
		<div class="titre">
			Configuration de "<?php echo $row['nom']; ?>"
		</div>
			<?php
			//Configuration de la valeur
			if($row['valeur_modifiable'])
			{
				$bonus_total = recup_bonus_total($joueur['ID']);
				//Différents type de modification
				switch($id)
				{
					//Sexe
					case 12 :
						if($bonus_total[$id]['valeur'] == '0' OR $bonus_total[$id]['valeur'] == '')
						{
						?>
						<form action="configure_point_sso.php" method="get" id="formSexe">
							<select name="sexe">
								<option value="1">Masculin</option>
								<option value="2">Feminin</option>
							</select>
							<input type="hidden" value="<?php echo $id; ?>" name="id" />
							<input type="button" value="Valider" onclick="envoiFormulaireGET('formSexe', 'popup_content');" />
						</form>
						<?php
						}
						else
						{
							if($bonus_total[$id]['valeur'] == 1) $sexe = 'Masculin'; else $sexe = 'Feminin';
							echo 'Sexe '.$sexe;
						}
					break;
					//Description
					case 16 :
						?>
						<form action="configure_point_sso.php" method="get" id="formDesc">
							<textarea name="description"><?php echo $bonus_total[$id]['valeur']; ?></textarea>
							<input type="hidden" value="<?php echo $id; ?>" name="id" />
							<input type="button" value="Valider" onclick="envoiFormulaireGET('formDesc', 'popup_content');" />
						</form>
						<?php
					break;
					//Avatar
					case 19 :
						?>
						Poids maximum du fichier : 20ko<br />
						Dimensions maximums du fichier : 80px * 80px<br />
						<form action="configure_point_sso.php" method="POST" enctype="multipart/form-data" id="formAvatar">
							<input type="hidden" name="MAX_FILE_SIZE"  VALUE="20240">
							<input type="file" name="nom_du_fichier">
							<input type="hidden" value="<?php echo $id; ?>" name="id" />
							<input type="button" value="Envoyer" onclick="envoiFormulaire('formAvatar', 'popup_content');">
						</form>
						<?php
					break;
					//Css
					case 27 :
						?>
						<form action="configure_point_sso.php" method="get" id="formCSS">
							<textarea name="css"><?php echo $bonus_total[$id]['valeur']; ?></textarea>
							<input type="hidden" value="<?php echo $id; ?>" name="id" />
							<input type="button" value="Valider" onclick="envoiFormulaireGET('formCSS', 'popup_content');" />
						</form>
						<?php
					break;
				}
			}
			//Configuration de l'état
			if($row['etat_modifiable'])
			{
				?>
				<form action="configure_point_sso.php" method="get" id="formModif">
					<select name="etat">
						<option value="0" <?php if($bonus[$id] == 0) echo 'selected="selected"'; ?>>Afficher a tout le monde</option>
						<option value="1" <?php if($bonus[$id] == 1) echo 'selected="selected"'; ?>>Afficher aux joueurs de votre race</option>
						<option value="2" <?php if($bonus[$id] == 2) echo 'selected="selected"'; ?>>Afficher a personne</option>
					</select>
					<input type="hidden" value="<?php echo $id; ?>" name="id" />
					<input type="button" value="Valider" onclick="envoiFormulaireGET('formModif', 'popup_content');" />
				</form>
				<?php
			}
			?>
			<br /><a href="configure_point_sso.php" onclick="return envoiInfo(this.href, 'popup_content');">Retour a la liste des bonus</a>
			<?php
		}
		else
		{
		?>
		<div class="titre">
			Configuration de vos bonus Shine
		</div>
		<?php
			$requete = "SELECT * FROM bonus_perso RIGHT JOIN bonus ON bonus_perso.id_bonus = bonus.id_bonus WHERE bonus_perso.id_perso = ".$joueur['ID']." ORDER BY bonus.id_categorie ASC";
			$req = $db->query($requete);
			$categorie = '';
			while($row = $db->read_assoc($req))
			{
				if($row['id_categorie'] != $categorie)
				{
					$categorie = $row['id_categorie'];
					?>
					<h3><?php echo $categorie; ?></h3>
					<?php
				}
				?>
				<ul>
				<?php
				echo '<li><a href="configure_point_sso.php?id='.$row['id_bonus'].'" onclick="return envoiInfo(this.href, \'popup_content\');"><img src="image/niveau/'.$row['id_bonus'].'.png" style="vertical-align : middle;" /> '.$row['nom'].'</a></li>';
				?>
				</ul>
				<?php
			}
		}
		?>
	</div>