<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte complète, l\'expérience acquérie grâce à l\'alpha m\'a permis de voir les gros problèmes qui pourraient se poser.<br />
	Je vais donc travailler sur la béta.<br />';
}
else
{
	include('menu.php');
	//Connecté
	if (isset($_SESSION['nom']))
	{
		if(array_key_exists('login_alpha', $_POST))
		{
			$cfg["sql"]['host'] = "localhost";
			$cfg["sql"]['user'] = "root";
			$cfg["sql"]['pass'] = "ilove50";
			$cfg["sql"]['db'] = "starshine2";
			$db_old = new db();

			$login = $_POST['login_alpha'];
			$pass = md5($_POST['password_alpha']);
			$requete = "SELECT * FROM perso WHERE nom = '".$login."' AND password = '".$pass."'";
			$req = $db_old->query($requete);
			if($db_old->num_rows > 0)
			{
				$row = $db_old->read_assoc($req);
				if($row['cadeau'] == 0)
				{
					$cfg["sql"]['host'] = "localhost";
					$cfg["sql"]['user'] = "root";
					$cfg["sql"]['pass'] = "ilove50";
					$cfg["sql"]['db'] = "starshine";
					$db = new db();

					$joueur = recupperso($_SESSION['ID']);
					if($joueur['cadeau'] == 0)
					{
						//Bonus de l'alpha
						$armures = array();
						$armures[] = '';
						$armures[] = '';
						$stars = 500;
						$honneur = 500;
						$objets[0][] = "1 parchemin de téléportation de 20 cases";
						$objets[0][] = "2 potions de vie";
						$objets[0][] = "3 parchemin de téléportation de 10 cases";
						$objets[0][] = "4 potions de vie mineure";
						$objets[1][] = 'o25';
						$objets[1][] = 'o7';
						$objets[1][] = 'o24';
						$objets[1][] = 'o4';
						$objets[2][] = 1;
						$objets[2][] = 2;
						$objets[2][] = 3;
						$objets[2][] = 4;
						$titres[] = "Colon de Starshine Alpha";
						
						//Cadeaux spécifiques !
						switch($row['ID'])
						{
							case 16 :
								$stars += 500;
								$honneur += 5000;
								$objets[0][] = "3 potions de vie majeure";
								$objets[0][] = "Set complet de bijoux";
								$objets[1][] = 'o8';
								$armures[1][] = 'p93';
								$armures[1][] = 'p99';
								$armures[1][] = 'p105';
								$armures[2][] = 1;
								$armures[2][] = 1;
								$armures[2][] = 1;
								$objets[2][] = 3;
								$titres[] = "Héro de Starshine";
								$titres[] = "Guerrier légendaire de Starshine Alpha";
								$titres[] = "Grand érudit de Starshine Alpha";
								$titres[] = "Vieux sage de Starshine Alpha";
							break;
							case 191 :
								$titres[] = "Kamikaze à tendance suicidaire";
								$titres[] = "Chevalier de Starshine Alpha";
								$titres[] = "Petite frappe de Starshine Alpha";
							break;
							case 139 :
								$stars += 500;
								$titres[] = "Tireur d'élite";
							break;
							case 127 :
								$titres[] = "Artiste royal";
								$titres[] = "Erudit de Starshine Alpha";
								$stars += 500;
							break;
							case 6 :
								$objets[0][] = "Une gemme d'arme niveau 1";
								$objets[0][] = "20 points en fabrication d'objet";
								$titres[] = "Grand Joaillier de Starshine";
								$titres[] = "Champion de Starshine Alpha";
								$titres[] = "Petite frappe de Starshine Alpha";
							break;
							case 143 :
								$titres[] = "Grand érudit de Starshine Alpha";
								$titres[] = "Champion de Starshine Alpha";
								$titres[] = "Bourreau de Starshine Alpha";
							break;
							case 191 :
								$titres[] = "Kamikaze à tendance suicidaire";
								$titres[] = "Chevalier de Starshine Alpha";
								$titres[] = "Petite frappe de Starshine Alpha";
							break;
							case 31 :
								$titres[] = "Erudit de Starshine Alpha";
								$titres[] = "grand champion de Starshine Alpha";
								$titres[] = "Vieux sage de Starshine Alpha";
								$titres[] = "Kamikaze de Starshine Alpha";
							break;
							case 98 :
								$titres[] = "Silent Bob";
							break;
							case 30 :
								$titres[] = "Champion de Starshine Alpha";
								$titres[] = "Bourreau de Starshine Alpha";								
							break;
							case 47 :
								$titres[] = "Champion de Starshine Alpha";
							break;
							case 138 :
								$titres[] = "Champion de Starshine Alpha";
								$titres[] = "Bourreau de Starshine Alpha";
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 146 :
								$titres[] = "Pourfendeur d'Arderax";
								$titres[] = "Chevalier de Starshine Alpha";
								$titres[] = "Erudit de Starshine Alpha";
								$armures[1][] = 'p109';
								$armures[2][] = 1;
							break;
							case 92 :
								$titres[] = "Grand érudit de Starshine Alpha";							
							break;
							case 144 :
								$titres[] = "Grand chasseur de têtes";
							break;
							case 216 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 234 :
								$titres[] = "Erudit de Starshine Alpha";
								$titres[] = "Héritier de Champolion";
							break;
							case 413 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 382 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 373 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 378 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 297 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
							case 74 :
								$titres[] = "Champion de Starshine Alpha";
							break;
							case 166 :
								$titres[] = "Erudit de Starshine Alpha";
							break;
						}
						//MAJ perso
						$requete = "UPDATE perso SET star = star + ".$stars.", honneur = honneur + ".$honneur." WHERE ID = ".$_SESSION['ID'];
						$db->query($requete);
						//Prise des objets
						$i = 0;
						foreach($objets[1] as $objet)
						{
							for($j = 0;$j < $objets[2][$i]; $j++)
							{
								prend_objet($objet, $joueur);
							}
							$joueur = recupperso($joueur['ID']);
							$i++;
						}
						//Prise des armures
						$i = 0;
						foreach($armures[1] as $armure)
						{
							for($j = 0;$j < $armures[2][$i]; $j++)
							{
								equip_objet($armure, $joueur);
							}
							$joueur = recupperso($joueur['ID']);
							$i++;
						}
						//Mis en place du titre
						foreach($titres as $titre)
						{
							$requete = "INSERT INTO titre_honorifique VALUES('', ".$_SESSION['ID'].", '".$titre."')";
							$db->query($requete);
						}
						//Validation du compte :
						$requete = "UPDATE perso SET cadeau = 1 WHERE ID = ".$_SESSION['ID'];
						$db->query($requete);
						$cfg["sql"]['host'] = "localhost";
						$cfg["sql"]['user'] = "root";
						$cfg["sql"]['pass'] = "ilove50";
						$cfg["sql"]['db'] = "starshine2";
						$db_old = new db();
						$requete = "UPDATE perso SET cadeau = 1 WHERE ID = ".$row['ID'];
						$db_old->query($requete);
						echo "<div id=\"jouer\">Vous avez participé à l'alpha, et en remerciement, nous vous offrons :<br />
						- ".$stars." stars.<br />
						- ".$honneur." points d'honneur<br />";
						foreach($objets[0] as $objet)
						{
							echo '- '.$objet.'<br />';
						}
						echo "
						Et comme titre honorifique :<br />";
						foreach($titres as $titre)
						{
							echo '- '.$titre.'<br />';
						}
					}
					else
					{
						echo 'Ce personnage a déjà reçu son cadeau !';
					}
				}
				else
				{
					echo 'Ce personnage alpha a déjà utilisé son cadeau !';
				}
			}
			else
			{
				echo 'Erreur, ce login / password n\'existe pas, ou a déjà recu son cadeau.';
			}
		}
		else
		{
		?>
		<div id="presentation">
			<div class="titre">
				Présentation
			</div>
			Pour recevoir votre cadeau
		</div>
		<div id="news">
			<form method="post" action="cadeau_alpha.php">
				Login pendant l'alpha : <input type="text" name="login_alpha" /><br />
				Password pendant l'alpha : <input type="password" name="password_alpha" /><br />
				<input type="submit" value="Valider" />
			</form>
		</div>
		<?php
		}
	}
}

	?>
	</div>
	<?php
include('bas.php');

?>