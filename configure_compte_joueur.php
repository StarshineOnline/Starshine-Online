<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$perso = new perso($_SESSION['ID']);
if(array_key_exists('id_joueur', $_SESSION)) 
	$joueur = new joueur($_SESSION['id_joueur']);
else
	$joueur = new joueur();
?>
	<div id="centre2">
		<?php
		if(array_key_exists('create', $_GET))
		{
			if(array_key_exists('pseudo', $_GET) AND $_GET['pseudo'] != NULL)
			{
				$pseudo = $_GET['pseudo'];
				$erreur = false;
				
				if(check_secu($pseudo))
				{
					$login = pseudo_to_login($pseudo);
					
          $check_pseudo = check_existing_account($pseudo, true, true, false, $perso->get_id());
          $check_login = check_existing_account($login, false, false, true, $perso->get_id());
									
					if ($check_pseudo == 0 AND $check_login == 0)
					{
						if(array_key_exists('email', $_GET) AND $_GET['email'] != NULL)
						{
							$email = sSQL($_GET['email']);
							if(!preg_match("#^[\w.-]+@([\w.-]+\.)*[a-zA-Z]{2,16}$#", $email))
							{
								echo '<h5>Email non valide</h5>';
								$erreur = true;
							}
						}
						else
							$email = $perso->get_email();						
						
						if(!$erreur)
						{
							$joueur->set_pseudo(sSQL($pseudo));
							$joueur->set_login(sSQL($login));
							$joueur->set_email(sSQL($email));
							$joueur->set_mdp($perso->get_password());
							require_once('connect_forum.php');
							$requete = 'SELECT password FROM punbbusers WHERE username LIKE "'.$perso->get_nom().'"';
							$res = $db_forum->query($requete);
							if( $db->num_rows($req) > 0 )
							{
							  $row = $db_forum->read_assoc($req);
                $joueur->set_mdpç_forum( $row['password'] );
              }
							$joueur->sauver();
							
							$perso->set_id_joueur($joueur->get_id());
							$perso->sauver();
							
							$_SESSION['id_joueur'] = $joueur->get_id();
							
							echo "<h6>Compte créé avec succès</h6>
							Vous pouvez désormais vous connecté par le biais de votre compte en utilisant votre pseudo (".$pseudo.") ou votre login (".$login.")";
						}
					}
					else
						echo '<h5>Identifiant deja utilisé</h5>';
				}
				else
					echo '<h5>Les caractères spéciaux ne sont pas autorisés</h5>';
			}
			else
				echo "<h5>Le pseudo est obligatoire</h5>";
		}
		elseif(array_key_exists('affilier', $_GET))
		{
				$pseudo = sSQL($_GET['pseudo']);
				$mdp = $_GET['mdp'];
				$requete = "SELECT id, mdp FROM joueur WHERE pseudo = '".$pseudo."' OR login = '".$pseudo."'";
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
					$row = $db->read_assoc($req);
					
					if($row['mdp'] == md5($mdp))
					{
						$perso->set_id_joueur($row['id']);
						$perso->sauver();
						$_SESSION['id_joueur'] = $row['id'];
						echo "<h6>[".$perso->get_nom()." - ".$perso->get_classe()." ".$perso->get_race()." - Niv.".$perso->get_level()."] a bien été affilié au compte ".$pseudo."</h6>";
					}
					else
					{
					?>
						<h5>Erreur, mot de passe incorrecte.</h5>
						<a href="configure_compte_joueur.php" onclick="return envoiInfo(this.href, 'popup_content');">Reessayer</a>
					<?php
					}
				}
				else
					echo "<h5>Le compte ".$pseudo." n'existe pas</h5>";
		}
		else
		{
		?>
		<div class="news">
		<?php
			if(($perso->get_id_joueur() == NULL) OR ($perso->get_id_joueur() == 0))
			{
			?>
			<h3>Création de compte</h3>
			<form action="configure_compte_joueur.php" method="get" id="formModif">
					Pseudo : <input type="text" value="<?php echo $perso->get_nom(); ?>" name="pseudo" id="pseudo" /> (peut etre identique au nom du personnage)<br/>
					Email : <input type="text" value="<?php echo $perso->get_email(); ?>" name="email" id="email" /> (facultatif, utilisé pour retrouver le mot de passe)<br/>
					<input type="button" value="Valider" onclick="envoiInfo('configure_compte_joueur.php?create=1&amp;pseudo=' + encodeURIComponent($('#pseudo').val()) +'&amp;email=' + encodeURIComponent($('#email').val()), 'popup_content');" />
			</form>
		</div>
			<div class="news" style='display:none'>
				<h3>Ou affilier à un compte déjà existant<?php echo "[".$perso->get_nom()." - ".$perso->get_classe()." ".$perso->get_race()." - Niv.".$perso->get_level()."]";?></h3>
				<form action="configure_compte_joueur.php" method="get" id="formModif">
					Pseudo du compte : <input type="text" value="" name="compte" id="compte" /> (ou login)<br />
					Mot de passe du compte : <input type="password" value="" name="mdp" id="mdp" /><br />
					<input type="button" value="Valider" onclick="if(confirm('Etes vous sûr d\'affilier <?php echo $perso->get_nom(); ?> au compte ' + ($('#compte').val()) + ' ?')) envoiInfo('configure_compte_joueur.php?affilier=1&amp;pseudo=' + encodeURIComponent($('#compte').val()) + '&amp;mdp=' + encodeURIComponent($('#mdp').val()), 'popup_content');" />
				</form>
			</div>
			<?php
			}
			else
			{
			?>
        Vous avez déjà un compte joueur.
			<?php
      }
		}
		?>
	</div>