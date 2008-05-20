<?php

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut.php');
include('haut_site.php');
include('menu.php');
?>
<div id="contenu">
	<div id="centre2">
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
								Erreur lors de la saisie du nouveau mot de passe.<br />
								<a href="option.php?action=mdp">Retour à la modification du mot de passe jeu.</a>
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
									Erreur, l'ancien mot de passe n'est pas le bon.<br />
									<a href="option.php?action=mdp">Retour à la modification du mot de passe jeu.</a>
									<?php
								}
								else
								{
									//update dans la bdd
									$requete = "UPDATE perso SET password = '".md5($new_pass)."' WHERE ID = ".$_SESSION['ID'];
									if($db->query($requete))
									{
										echo 'Votre mot de passe a bien été modifié !';
									}
								}
							}
						}
						else
						{
						?>
						<form method="post" action="option.php?action=mdp">
							<strong>Veuillez indiquez votre mot de passe actuel :</strong><br />
							<input type="password" name="ancien_pass" /><br />
							<strong>Veuillez indiquez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" name="new_pass" /><br />
							<strong>Veuillez retappez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" name="new_pass2" /><br />
							<input type="submit" value="Modifier votre mot de passe jeu" />
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
						}
						?>
						<form method="post" action="option.php?action=journal">
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
							<input type="submit" value="Valider" name="submit" />
						</form>
						<?php
					break;
					case 'supp' :
						$requete = "UPDATE perso SET statut = 'ban', fin_ban = ".(time() + (3600 * 24 * 36500))." WHERE ID = ".$_SESSION['ID'];
						if($db->query($requete))
						{
							$perso = recupperso($_SESSION['ID']);
							$requete = "INSERT INTO punbbbans VALUES('', '".$perso['nom']."', NULL, NULL, NULL, NULL)";
							if($db->query($requete)) echo 'Votre personnage est bien supprimé';
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
					<li><a href="option.php?action=journal">Filtrer votre journal des actions</a></li>
					<li><a href="configure_point_sso.php">Configurer vos bonus Shine</a></li>
				</ul>
			</div>
			<div class"news">
				<h3>Mot de passe</h3>
				<strong>Attention votre mot de passe jeu, et votre mot de passe forum sont à changer séparément !</strong><br />
				<ul>
					<li><a href="option.php?action=mdp">Modifier votre mot de passe JEU</a></li>
					<li><a href="http://forum.starshine-online.com/profile.php?action=change_pass&id=2">Modifier votre mot de passe FORUM</a></li>
				</ul>
			</div>
			<div class"news">
				<h3>Hibernation / Suppression</h3>
				<ul>
					<li><a href="javascript:if(confirm('Êtes vous sur de vouloir effacer votre personnage ?')) document.location.href='option.php?action=supp'">Supprimer mon personnage</a></li>
					<li><a href="javascript:if(confirm('Êtes vous sur de vouloir hiberner ?')) document.location.href='option.php?action=hibern'">Mettre mon personnage en hibernation (l'hibernation durera au MINIMUM 2 semaines)</a></li>
				</ul>
			</div>
			</ul>
			<?php
			}
			?>
		</p>
	</div>
</div>