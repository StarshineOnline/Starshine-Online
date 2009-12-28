<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$bonus = recup_bonus($joueur->get_id());
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
							$joueur->set_cache_classe($_GET['etat']);
						break;
						case 8 :
							$joueur->set_cache_stat($_GET['etat']);
						break;
						case 11 :
							$joueur->set_cache_niveau($_GET['etat']);
						break;
					}
					$joueur->sauver();
				}
				$bonus_total = recup_bonus_total($joueur->get_id());
				$requete = "UPDATE bonus_perso SET etat = ".sSQL($_GET['etat'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur->get_id());
			}
			//Changement de sexe
			if(array_key_exists('sexe', $_GET))
			{
				$bonus_total = recup_bonus_total($joueur->get_id());
				$requete = "UPDATE bonus_perso SET valeur = ".sSQL($_GET['sexe'])." WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur->get_id());
			}
			//Changement de description
			if(array_key_exists('description', $_POST))
			{
				$bonus_total = recup_bonus_total($joueur->get_id());
				$requete = "UPDATE bonus_perso SET valeur = '".sSQL(htmlspecialchars($_POST['description']))."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur->get_id());
				echo '<h6>Votre description a bien été modifié !</h6>';
			}
			//Changement de css
			if(array_key_exists('css', $_GET))
			{
				$bonus_total = recup_bonus_total($joueur->get_id());
				$requete = "UPDATE bonus_perso SET valeur = '".sSQL(htmlspecialchars($_GET['css']))."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
				$db->query($requete);
				$bonus = recup_bonus($joueur->get_id());
				echo '<h6>Votre css a bien été modifié !</h6>';
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
					$nom_fichier = root.$chemin_destination.$joueur->get_id().$type;
					if(move_uploaded_file($_FILES['nom_du_fichier']['tmp_name'], $nom_fichier))
					{
						//On vérifie la taille de l'image
						$size = getimagesize($nom_fichier);
						//Si compris entre 80 * 80
						if($size[0] <= 80 AND $size[1] <= 80)
						{
							$bonus_total = recup_bonus_total($joueur->get_id());
							$requete = "UPDATE bonus_perso SET valeur = '".sSQL($joueur->get_id().$type)."' WHERE id_bonus_perso = ".$bonus_total[$id]['id_bonus_perso'];
							$db->query($requete);
							$bonus = recup_bonus($joueur->get_id());
						}
						//Sinon on efface l'image
						else
						{
							unlink($nom_fichier);
							echo '<h5>Votre fichier n\'a pas les bonnes dimensions !</h5>';
						}
					}
				}
				echo '<h6>Votre avatar a bien été modifié !</h6>';
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
				$bonus_total = recup_bonus_total($joueur->get_id());
				//Différents type de modification
				switch($id)
				{
					//Sexe
					case 12 :
						if($bonus_total[$id]['valeur'] == '0' OR $bonus_total[$id]['valeur'] == '')
						{
						?>
						<form action="configure_point_sso.php" method="get" id="formSexe">
							<select name="sexe" id='sexe'>
								<option value="1">Masculin</option>
								<option value="2">Feminin</option>
							</select>
							<input type="hidden" value="<?php echo $id; ?>" name="id" id='id_sexe' />
							<input type="button" value="Valider" onclick="envoiInfo('configure_point_sso.php?css=' + $('#sexe').val()+'&amp;id='+$('#id_sexe').val(), 'popup_content');envoiInfo('configure_point_sso.php', 'popup_content');" />
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
						<form action="configure_point_sso.php" method="post" id="formDesc">
							<textarea id='description' name="description"><?php echo $bonus_total[$id]['valeur']; ?></textarea>
							<input type="hidden" value="<?php echo $id; ?>" name="id" id='id_desc'/>
							<input type="submit" value="Valider" onclick="return envoiFormulaire('formDesc', 'popup_content');" />
						</form>
						<?php
					break;
					//Avatar
					case 19 :
						?>
						Poids maximum du fichier : 20ko<br />
						Dimensions maximums du fichier : 80px * 80px<br />
						<form action="configure_point_sso.php" method="post" enctype="multipart/form-data" id="formAvatar">
							<input type="hidden" name="MAX_FILE_SIZE"  VALUE="20240" />
							<input type="file" name="nom_du_fichier" id="fileUpload" />
							<input type="hidden" value="<?php echo $id; ?>" name="id" />
							<input type="submit" value="Envoyer" onclick="return envoiFichier('formAvatar', 'popup_content');">
						</form>
						<?php
					break;
					//Css
					case 27 :
						?>
						<form action="configure_point_sso.php" method="get" id="formCSS">
							<textarea id='css' name="css"><?php echo $bonus_total[$id]['valeur']; ?></textarea>
							<input type="hidden" value="<?php echo $id; ?>" name="id" id='id_css' />
							<input type="button" value="Valider" onclick="envoiInfo('configure_point_sso.php?css=' + $('#css').val()+'&amp;id='+$('#id_css').val(), 'popup_content');envoiInfo('configure_point_sso.php', 'popup_content');" />
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
					<select name="etat" id='etat'>
						<option value="0" <?php if($bonus[$id] == 0) echo 'selected="selected"'; ?>>Afficher a tout le monde</option>
						<option value="1" <?php if($bonus[$id] == 1) echo 'selected="selected"'; ?>>Afficher aux joueurs de votre race</option>
						<option value="2" <?php if($bonus[$id] == 2) echo 'selected="selected"'; ?>>Afficher a personne</option>
					</select>
					<input type="hidden" value="<?php echo $id; ?>" name="id" id='id_etat' />
					<input type="button" value="Valider" onclick="envoiInfo('configure_point_sso.php?etat=' + $('#etat').val()+'&amp;id='+$('#id_etat').val(), 'popup_content');envoiInfo('configure_point_sso.php', 'popup_content');" />
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
			$requete = "SELECT * FROM bonus_perso RIGHT JOIN bonus ON bonus_perso.id_bonus = bonus.id_bonus WHERE bonus_perso.id_perso = ".$joueur->get_id()." ORDER BY bonus.id_categorie ASC";
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