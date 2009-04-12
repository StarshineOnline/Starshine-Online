<?php
include('inc/fp.php');
?>
		<div class="titre">
			Options de votre compte
		</div>
		<p>
			<?php
			if(array_key_exists('action', $_GET))
			{
				switch($_GET['action'])
				{
					case 'mdp' :
						if(array_key_exists('ancien_pass', $_POST))
						{
							$ancien_pass = $_POST['ancien_pass'];
							$new_pass = $_POST['new_pass'];
							$new_pass2 = $_POST['new_pass2'];
							if($new_pass != $new_pass2)
							{
								?>
								<h5>Erreur lors de la saisie du nouveau mot de passe.</h5>
								<a href="option.php?action=mdp" onclick="return envoiInfo(this.href, 'popup_content');">Retour à la modification du mot de passe jeu.</a>
								<?php
							}
							else
							{
								$requete = "SELECT password FROM perso WHERE ID = ".$_SESSION['ID'];
								$req = $db->query($requete);
								$row = $db->read_row($req);
								if($row[0] != md5($ancien_pass))
								{
									?>
									<h5>Erreur, l'ancien mot de passe n'est pas le bon.</h5>
									<a href="option.php?action=mdp" onclick="return envoiInfo(this.href, 'popup_content');">Retour à la modification du mot de passe jeu.</a>
									<?php
								}
								else
								{
									//update dans la bdd
									$requete = "UPDATE perso SET password = '".md5($new_pass)."' WHERE ID = ".$_SESSION['ID'];
									if($db->query($requete))
									{
										require('connect_forum.php');
										$requete = "UPDATE punbbusers SET password = '".sha1($new_pass)."' WHERE username = '".$_SESSION['nom']."'";
										$db_forum->query($requete);
										echo '<h6>Votre mot de passe a bien été modifié !</h6>';
									}
								}
							}
						}
						else
						{
						?>
						<form method="post" action="option.php?action=mdp" id="formMDP">
							<strong>Veuillez indiquez votre mot de passe actuel :</strong><br />
							<input type="password" name="ancien_pass" /><br />
							<strong>Veuillez indiquez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" name="new_pass" /><br />
							<strong>Veuillez retappez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" name="new_pass2" /><br />
							<input type="button" value="Modifier votre mot de passe jeu" onclick="envoiFormulaire('formMDP', 'popup_content');" />
						</form>
						<?php
						}
					break;
					case 'journal' :
						echo 'Elements que vous ne voulez pas voir apparaitre dans votre journal des actions :';
						$liste_options = array('soin', 'gsoin', 'buff', 'gbuff',  'degat', 'kill', 'quete', 'loot');
						$liste_options_nom = array('Soins', 'Soins de groupe', 'Buffs', 'Buffs de groupe', 'Dégats', 'Kills', 'Quètes', 'Loots');
						$options = recup_option($_SESSION['ID']);
						if(array_key_exists('submit', $_POST))
						{
							$i = 0;
							$count = count($liste_options);
							while($i < $count)
							{
								if(array_key_exists($liste_options[$i], $_POST) AND $_POST[$liste_options[$i]] == 'on')
								{
									if(array_key_exists($liste_options[$i], $options))
									{
										if($options[$liste_options[$i]] == 0)
										{
											$requete = "UPDATE options SET valeur = 1 WHERE id_perso = ".$_SESSION['ID']." AND nom = '".$liste_options[$i]."'";
											$db->query($requete);
										}
									}
									else
									{
										$requete = "INSERT INTO options(id, id_perso, nom, valeur) VALUES('', ".$_SESSION['ID'].", '".$liste_options[$i]."', 1)";
										$db->query($requete);
									}
								}
								else
								{
									if(array_key_exists($liste_options[$i], $options))
									{
										if($options[$liste_options[$i]] == 1)
										{
											$requete = "UPDATE options SET valeur = 0 WHERE id_perso = ".$_SESSION['ID']." AND nom = '".$liste_options[$i]."'";
											$db->query($requete);
										}
									}
									else
									{
										$requete = "INSERT INTO options(id, id_perso, nom, valeur) VALUES('', ".$_SESSION['ID'].", '".$liste_options[$i]."', 0)";
										$db->query($requete);
									}
								}
								$i++;
							}
							$options = recup_option($_SESSION['ID']);
							echo '<h6>Filtre du journal modifié avec succès</h6>';
						}
						?>
						<form method="post" action="option.php?action=journal" id="formJournal">
							<ul>
								<?php
								$i = 0;
								$count = count($liste_options);
								while($i < $count)
								{
									if(array_key_exists($liste_options[$i], $options) AND $options[$liste_options[$i]] == 1) $check = true; else $check = false;
									?>
									<li><input type="checkbox" value="on" name="<?php echo $liste_options[$i]; ?>" <?php if($check) echo 'checked'; ?> /> <?php echo $liste_options_nom[$i]; ?></li>
									<?php
									$i++;
								}
								?>
							</ul>
							<input type="button" value="Valider" name="submit" onclick="envoiFormulaire('formJournal', 'popup_content');" />
						</form>
						<?php
					break;
					case 'supp' :
						$requete = "UPDATE perso SET statut = 'ban', fin_ban = ".(time() + (3600 * 24 * 36500))." WHERE ID = ".$_SESSION['ID'];
						if($db->query($requete))
						{
							require('connect_forum.php');
							$perso = recupperso($_SESSION['ID']);
							$requete = "INSERT INTO punbbbans VALUES('', '".$perso['nom']."', NULL, NULL, NULL, NULL, 0)";
							if($db_forum->query($requete)) echo 'Votre personnage est bien supprimé';
							unset($_COOKIE['nom']);
							unset($_SESSION['nom']);
							unset($_SESSION['ID']);
						}
					break;
					case 'hibern' :
						$requete = "UPDATE perso SET statut = 'hibern', fin_ban = ".(time() + (3600 * 24 * 14))." WHERE ID = ".$_SESSION['ID'];
						if($db->query($requete)) echo 'Votre personnage hiberne... zzz...';
						unset($_COOKIE['nom']);
						unset($_SESSION['nom']);
						unset($_SESSION['ID']);
					break;
				}
			}
			else
			{
			?>
			<div class"news">
				<h3>Options du jeu</h3>
				<ul>
					<li><a href="option.php?action=journal" onclick="return envoiInfo(this.href, 'popup_content');">Filtrer votre journal des actions</a></li>
					<li><a href="configure_point_sso.php" onclick="return envoiInfo(this.href, 'popup_content');">Configurer vos bonus Shine</a></li>
				</ul>
			</div>
			<div class"news">
				<h3>Mot de passe</h3>
				<strong>Attention votre mot de passe jeu, et votre mot de passe forum sont à changer séparément !</strong><br />
				<ul>
					<li><a href="option.php?action=mdp" onclick="return envoiInfo(this.href, 'popup_content');">Modifier votre mot de passe JEU</a></li>
				</ul>
			</div>
			<div class"news">
				<h3>Hibernation / Suppression</h3>
				<ul>
					<li><a href="option.php?action=supp" onclick="if(confirm('Êtes vous sur de vouloir effacer votre personnage ?')) return envoiInfo(this.href, 'popup_content'); else return false;">Supprimer mon personnage</a></li>
					<li><a href="option.php?action=hibern" onclick="if(confirm('Êtes vous sur de vouloir hiberner ?')) return envoiInfo(this.href, 'popup_content'); else return false;">Mettre mon personnage en hibernation (l'hibernation durera au MINIMUM 2 semaines)</a></li>
				</ul>
			</div>
			</ul>
			<?php
			}
			?>
		</p>
	</div>