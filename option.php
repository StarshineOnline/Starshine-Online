<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$titre_perso = new titre($_SESSION['ID']);
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
					case 'titre' :
						if(array_key_exists('final', $_POST))
						{
							$titre_perso->set_id_titre($_POST['final']);
							echo '<h6>Votre titre a bien été modifié !</br> Pensez à réactualiser !</h6>';
						}
						else
						{
							$requete = "SELECT * FROM achievement WHERE id_perso = ".$_SESSION['ID'];
							$req = $db->query($requete);
							?>
							<form method="post" action="option.php?action=titre" id="formtitre">
							<select name="final" >
							<?php
							echo '<option value="0">Titre : Aucun titre</option>';
							while($row = $db->read_array($req))
							{
								$requete2 = "SELECT * FROM achievement_type WHERE id = ".$row['id_achiev'];
								$req2 = $db->query($requete2);
								$row2 = $db->read_array($req2);
								$titre = explode('-', $row2['titre']);
								if ($titre[1] != null ) echo '<option value="'.$row['id_achiev'].'">Titre : '.$titre[1].'</option>';
							}
							?>
							
							<input type="submit" id='new_titre' name="new_titre" value="Ok" onclick="return envoiFormulaire('formtitre', 'popup_content');">	
							</select></form>
							<?php
						}
					break;
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
									$perso = new perso($_SESSION['ID']);
									$perso->set_password(md5($new_pass));
									$perso->sauver();
									
									if(array_key_exists('id_joueur', $_SESSION)) 
									{
										$joueur = new joueur($_SESSION['id_joueur']);
										$joueur->set_mdp(md5($new_pass));
										$joueur->sauver();
									}

									require('connect_forum.php');
									$requete = "UPDATE punbbusers SET password = '".sha1($new_pass)."' WHERE username = '".$_SESSION['nom']."'";
									$db_forum->query($requete);
									echo '<h6>Votre mot de passe a bien été modifié !</h6>';
								}
							}
						}
						else
						{
						?>
						<form method="post" action="option.php?action=mdp" id="formMDP">
							<strong>Veuillez indiquez votre mot de passe actuel :</strong><br />
							<input type="password" id='ancien_pass' name="ancien_pass" /><br />
							<strong>Veuillez indiquez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" id='new_pass' name="new_pass" /><br />
							<strong>Veuillez retappez votre NOUVEAU mot de passe :</strong><br />
							<input type="password" id='new_pass2' name="new_pass2" /><br />
							<input type="submit" value="Modifier votre mot de passe" onclick="return envoiFormulaire('formMDP', 'popup_content');" />
						</form>
						<?php
						}
					break;
					case 'email' :
						$perso = new perso($_SESSION['ID']);
						if(array_key_exists('new_email', $_POST))
						{
							$new_email = sSQL($_POST['new_email']);
							$perso->set_email($new_email);
							$perso->sauver();
							
							if(array_key_exists('id_joueur', $_SESSION)) 
							{
								$joueur = new joueur($_SESSION['id_joueur']);
								$joueur->set_email($new_email);
								$joueur->sauver();
							}
							echo '<h6>Votre email a bien été modifié !</h6>';
						}
						else
						{
						?>
						<form method="post" action="option.php?action=email" id="formemail">
							<input type="text" id='new_email' name="new_email" value="<?php echo $perso->get_email(); ?>" /><br />
							<input type="submit" value="Modifier votre email" onclick="return envoiFormulaire('formemail', 'popup_content');" />
						</form>
						<?php
						}
					break;
					case 'journal' :
						$liste_options = array('soin', 'gsoin', 'buff', 'gbuff',  'degat', 'kill', 'quete', 'loot', 'nbrLignesJournal');
						$liste_options_nom = array('Soins', 'Soins de groupe', 'Buffs', 'Buffs de groupe', 'Dégâts', 'Kills', 'Quêtes', 'Loots', 'Nombre de lignes');
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
								elseif(array_key_exists($liste_options[$i], $_POST) AND is_numeric($_POST[$liste_options[$i]]))
								{
									if(array_key_exists($liste_options[$i], $options))
									{
										$requete = "UPDATE options SET valeur = '".$_POST[$liste_options[$i]]."' WHERE id_perso = ".$_SESSION['ID']." AND nom = '".$liste_options[$i]."'";
										$db->query($requete);
									}
									else
									{
										$requete = "INSERT INTO options(id, id_perso, nom, valeur) VALUES('', ".$_SESSION['ID'].", '".$liste_options[$i]."', '".$_POST[$liste_options[$i]]."')";
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
							?>
							<script type="text/javascript">
								refresh('journal.php','information');
							</script>
							<?php
						}
						?>
						Éléments que vous ne voulez pas voir apparaitre dans votre journal des actions :
						<form method="post" action="option.php?action=journal" id="formJournal">
							<ul>
								<?php
								$i = 0;
								$count = count($liste_options);
								while($i < $count-1)
								{
									if(array_key_exists($liste_options[$i], $options) AND $options[$liste_options[$i]] == 1) $check = true; else $check = false;
									?>
									<li><input type="checkbox" value="on" name="<?php echo $liste_options[$i]; ?>" <?php if($check) echo 'checked'; ?> /> <?php echo $liste_options_nom[$i]; ?></li>
									<?php
									$i++;
								}
								?>
							</ul><br />
							Nombre de lignes : 
							<select name="nbrLignesJournal">
								<option value="<?php  if($options[$liste_options[$i]] != 0) echo ($options[$liste_options[$i]]); else echo '15' ?>" selected><?php if($options[$liste_options[$i]] != 0) echo ($options[$liste_options[$i]]); else echo '15'; ?></option>
								<option value="15">15</option>
								<option value="30">30</option>
								<option value="45">45</option>
								<option value="60">60</option>
							</select>
							<br />
							<input type="hidden" name="submit" />
							<input type="submit" value="Valider" onclick="return envoiFormulaire('formJournal', 'options');" />
						</form>
						<?php
					break;
					case 'supp' :
						$perso = new perso($_SESSION['ID']);
						$perso->set_statut('ban');
						$perso->set_fin_ban((time() + (3600 * 24 * 36500)));
						$perso->sauver();
						require('connect_forum.php');
						$groupe = $perso->get_groupe();
						if($groupe != 0)
						{	degroup($perso->get_id(), $groupe->get_id());}
						$requete = "INSERT INTO punbbbans VALUES(NULL, '".$perso->get_nom()."', NULL, NULL, NULL, NULL, 2)";
						if($db_forum->query($requete))
						{
						 	echo 'Votre personnage est bien supprimé';
							$perso->sauver();
							unset($_COOKIE['nom']);
							unset($_SESSION['nom']);
							unset($_SESSION['ID']);
						}
						else
							echo 'ERREUR: Veuillez contacter un admin!';
					break;
					case 'hibern' :
						$requete = "UPDATE perso SET statut = 'hibern', fin_ban = ".(time() + (3600 * 24 * 14))." WHERE ID = ".$_SESSION['ID'];
						if($db->query($requete)) echo 'Votre personnage hiberne... zzz...';
						unset($_COOKIE['nom']);
						unset($_SESSION['nom']);
						unset($_SESSION['ID']);
					break;
					case 'atm' :
					{
						$requete = false;
						$val = sSQL($_GET['val']);
						switch ($_GET['effet'])
						{
						case 'sky':
							$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
								$_SESSION['ID'].", 'desactive_atm', $val)";
							break;
						case 'time':
							$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
								$_SESSION['ID'].", 'desactive_atm_all', $val)";
							break;
						default:
							echo "<h5>Erreur de parametre</h5>";
						}
						if ($requete) {
							header("Location: ?");
							$db->query($requete);
							exit(0);
						}
					}
					break;
					case 'sound' :
					{
						$val = sSQL($_GET['val']);
						$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".
							$_SESSION['ID'].", 'no_sound', $val)";
						header("Location: ?");
						$db->query($requete);
						exit(0);
					}
					break;
				}
			}
			else
			{
				$perso = new perso($_SESSION['ID']);
				$q = $db->query("select password from perso where id = $_SESSION[ID]");
				if($q)
				{
					$row = $db->read_row($q);
					$clef_api = sha1($row[0]);
				}
				
				$atm_val = 1;
				$atm_all_val = 1;
				$q = $db->query("select nom, valeur from options where ".
												"id_perso = $_SESSION[ID] and nom in ".
												"('desactive_atm', 'desactive_atm_all')");
				if ($q) {
					while ($row = $db->read_row($q)) {
						switch ($row[0]) {
						case 'desactive_atm':
							$atm_val = $row[1] ? 0 : 1;
							break;
						case 'desactive_atm_all':
							$atm_all_val = $row[1] ? 0 : 1;
							break;
						}
					}
				}
				$atm_verb = $atm_val ? 'Désactiver' : 'Activer';
				$atm_all_verb = $atm_all_val ? 'Désactiver <strong>tous</strong>' : 'Activer';

				$no_sound = $db->query_get_object("select valeur from options where ".
												"id_perso = $_SESSION[ID] and nom = 'no_sound'");
				if ($no_sound && $no_sound->valeur)
				{
					$sound_verb = 'Activer';
					$sound_val = 0;
				}
				else
				{
					$sound_verb = 'Désactiver';
					$sound_val = 1;
				}

			?>
			<div class"news">
				<h3>Options du jeu</h3>
				<ul>
				<?php
				if(($perso->get_id_joueur() == NULL) OR ($perso->get_id_joueur() == 0))
				{
				?>
					<li><a href="configure_compte_joueur.php" onclick="return envoiInfo(this.href, 'popup_content');">Gestion du compte joueur</a></li>
				<?php
				}
				?>	
					<li><a href="option.php?action=journal" onclick="return envoiInfo(this.href, 'popup_content');">Filtrer votre journal des actions</a></li>
					<li><a href="configure_point_sso.php" onclick="return envoiInfo(this.href, 'popup_content');">Configurer vos bonus Shine</a></li>
					<li><a href="option.php?action=email" onclick="return envoiInfo(this.href, 'popup_content');">Modifier votre email</a></li>
					<li><a href="option.php?action=titre" onclick="return envoiInfo(this.href, 'popup_content');">Choisir un titre</a></li>

				</ul>
				
			</div>
			<div class"news">
				<h3>Options graphiques et son</h3>
				  <ul>
<?php if (isset($G_use_atmosphere) && $G_use_atmosphere) { ?>
					  <li><a href="option.php?action=atm&amp;effet=sky&amp;val=<?php echo $atm_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $atm_verb; ?> les effets atmospheriques</a></li>
					  <li><a href="option.php?action=atm&amp;effet=time&amp;val=<?php echo $atm_all_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $atm_all_verb; ?> les effets atmosphériques et liés à l'heure</a></li>
<?php } ?>

					  <li><a href="option.php?action=sound&amp;val=<?php echo $sound_val; ?>" onclick="return envoiInfo(this.href, 'popup_content');"><?php echo $sound_verb; ?> les effets sons</a></li>

				  </ul>
				<h3>Mot de passe</h3>
				<strong>Attention cela change &eacute;galement votre mot de passe sur le forum!</strong><br />
				<ul>
					<li><a href="option.php?action=mdp" onclick="return envoiInfo(this.href, 'popup_content');">Modifier votre mot de passe</a></li>
					<li>Clef API: <?php echo $clef_api ?></li>
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
