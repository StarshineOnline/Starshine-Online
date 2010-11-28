<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
?>
	<fieldset>
		<legend>Script de combat - Créatures</legend>
<ul id="messagerie_onglet">
	<li><a href="actions.php" onclick="return envoiInfo(this.href, 'information');">Personnage</a></li>
	<li><a href="actions_pet.php" onclick="return envoiInfo(this.href, 'information');">Créature</a></li>
</ul>
<br /><br />
<?php
if (isset($_GET['id_pet']))
{
$pet = new pet($_GET['id_pet']);
echo "<h3>Scripts de ".$pet->get_nom()."</h3>";

		//Suppression du script
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'suppr_action')
		{
			$requete = "DELETE FROM action_pet WHERE id = ".sSQL($_GET['id_action']);
			if($db->query($requete))
			{
				echo '<h6>Script effacé avec succès !</h6>';
			}
			$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." AND type_monstre = ".$pet->get_id_monstre()." ORDER BY nom ASC";
			$req = $db->query($requete);
			if($db->num_rows($req) == 0)
			{
				$requete = "UPDATE pet SET action_a = '0', action_d = '0' WHERE ID = ".$pet->get_id();
				$req = $db->query($requete);
			}
		}
		if(array_key_exists('valid', $_GET))
		{
			//Si ya pas d'id_action alors création
			if($_GET['id_action'] == '')
			{
				$requete = "INSERT INTO action_pet VALUES('', ".$joueur->get_id().", ".$pet->get_id_monstre().", '".sSQL($_POST['action_nom'])."', '', '".sSQL($_POST['mode'])."')";
				$req = $db->query($requete);
				$id_action = $db->last_insert_id();
			}
			else
			{
				$id_action = $_GET['id_action'];
			}
			$actionexplode = explode(';', recupaction($id_action));
			$i = 1;
			while($i <= $_GET['r'])
			{
				$final = $_GET['final'.$i];
				$action = '#09='.$i.'@';
				$typefinal = $final[0];
				$action_nom = sSQL($_GET['action_nom']);
				$numfinal = mb_substr($final, 1, strlen($final));
				if($final == 'attaque') $a_final = '!';
				elseif($typefinal == 's') $a_final = '~'.$numfinal;
				else $a_final = '_'.$numfinal;
				$action .= $a_final;
				$actionexplode[($i - 1)] = $action;
				$i++;
			}
			$requete = "UPDATE action_pet SET action = '".implode(';', $actionexplode)."', nom = '".$action_nom."' WHERE id = ".$id_action;
			//echo $requete;
			$db->query($requete);
			echo '<h6>Le script est bien été ajouté / modifié</h6>';
		}

		if(array_key_exists('action', $_GET) && $_GET['action'] == 'select' && $_GET['id_action'] != '')
		{
			if($_GET['type'] == 'attaque') $t = 'a';
			else $t = 'd';
			$id_action = mysql_escape_string($_GET['id_action']);
			$requete = "UPDATE pet SET action_".$t." = '".sSQL($id_action)."' WHERE ID = ".$pet->get_id();
			if($db->query($requete))
			{
				$set = 'set_action_'.$t;
				$pet->$set($_GET['id_action']);
				$pet->sauver();
				echo '<h6>Script '.$_GET['type'].' bien séléctionné.</h6>';
			}
		}
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'dupliq' && $_GET['id_action'] != '')
		{
			$requete = "SELECT action, mode FROM action_pet WHERE id = ".sSQL($_GET['id_action']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$requete = "INSERT INTO action_pet VALUES(null, ".$joueur->get_id().", ".$pet->get_id_monstre().", '".sSQL($_GET['nom_copie'])."', '".$row['action']."', '".$row['mode']."')";
			//echo $requete;
			if($db->query($requete))
			{
				echo '<h6>Script dupliqué.</h6>';
			}
		}
		$joueur->check_perso();
		$script_attaque = recupaction_all($pet->get_action_a(), true);
		$script_defense = recupaction_all($pet->get_action_d(), true);
		?>
			Voici l'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.<br />
			<fieldset>
				<legend>Création</legend>
				<ul>
					<li><a href="action.php?mode=s&id_pet=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat en mode simplifié</a></li>
					<li><a href="action.php?mode=a&id_pet=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat en mode avancé</a></li>
					<li>
						Copier le script : <select name="id_action_c" id="id_action_c">
							<?php
								$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
								$req = $db->query($requete);
								while($row = $db->read_assoc($req))
								{
									echo '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
								}
							?>
								</select>
								en le nommant <input type="text" value="copie" name="nom_copie" id="nom_copie" style="width : 50px;" />
 								<input type="button" name="valid" value="Copier" onclick="envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>&action=dupliq&amp;id_action=' + document.getElementById('id_action_c').value + '&amp;nom_copie=' + document.getElementById('nom_copie').value, 'information');" />
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend>Utilisation</legend>
				<table>
					<tr>
						<td>
							Script d'attaque
						</td>
						<td>
							: <select name="id_action_a" id="id_action_a">
						<?php
						$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." AND type_monstre = ".$pet->get_id_monstre()." ORDER BY nom ASC";
						$req = $db->query($requete);
						while($row = $db->read_assoc($req))
						{
							if ($row['nom']==$script_attaque['nom'])
							{
								echo '<option value="'.$row['id'].'" selected="selected">'.$row['nom'].'</option>';
							}
							else
							{
								echo '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
							}
						}
						?>
							</select>
						</td>
						<td>
							<input type="button" name="valid" value="Utiliser" onclick="envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>&action=select&amp;type=attaque&amp;id_action=' + document.getElementById('id_action_a').value, 'information');" />
						</td>
					</tr>
					<!--<tr>
						<td>
							Script de défense
						</td>
						<td>
							: <select name="id_action_d" id="id_action_d">
							<?php
								$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
								$req = $db->query($requete);
								while($row = $db->read_assoc($req))
								{
									if ($row['nom']==$script_defense['nom'])
									{
										echo '<option value="'.$row['id'].'" selected="selected">'.$row['nom'].'</option>';
									}
									else
									{
										echo '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
									}
								}
							?>
								</select>
						</td>
						<td>
							<input type="button" name="valid" value="Utiliser" onclick="envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>&action=select&amp;type=defense&amp;id_action=' + document.getElementById('id_action_d').value, 'information');" />
						</td>
					</tr>-->
					</table>
			</fieldset>
			<fieldset>
				<legend>Modification</legend>
				<table>
					<tr>
						<td>
							Modifier
						</td>
						<td>
							 : <select name="id_action" id="id_action">
						<?php
						$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
						$req = $db->query($requete);
						while($row = $db->read_assoc($req))
						{
							?>
							<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option>
							<?php
						}
						?>
							</select>
						</td>
						<td>
							<input type="button" name="valid" value="Modifier" style="width : 100px;" onclick="envoiInfo('action.php?id_pet=<?php echo $pet->get_id(); ?>&amp;from=modif&amp;id_action=' + document.getElementById('id_action').value, 'information');" />
						</td>
					</tr>
					<tr>
						<td>
							Supprimer
						</td>
						<td>
							: <select name="id_action_suppr" id="id_action_suppr">
							<?php
							$requete = "SELECT * FROM action_pet WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
							$req = $db->query($requete);
							while($row = $db->read_assoc($req))
							{
								?>
								<option value="<?php echo $row['id']; ?>"><?php echo $row['nom']; ?></option>
								<?php
							}
							?>
							</select>
						</td>
						<td>
							<input type="button" name="valid" value="Supprimer" style="width : 100px;" onclick="if(confirm('Voulez vous vraiment supprimer cette action ?')) envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>&action=suppr_action&amp;id_action=' + document.getElementById('id_action_suppr').value, 'information');" />
						</td>
					</tr>
				</table>
		<br />
		</form>
		</fieldset>
		</div>
<?php
}
//On affiche la lsite des créatures poru choisir celle que l'on modif son script
else
{
$pets = $joueur->get_pets(true);
if(count($pets) > 0)
{
	foreach($pets as $pet)
	{
		$pet->get_monstre();
		$script_attaque = recupaction_all($pet->get_action_a(), true);
		$script_defense = recupaction_all($pet->get_action_d(), true);
		?>
	<div class="monstre combat" onclick="envoiInfo('actions_pet.php?id_pet=<?php echo $pet->get_id(); ?>', 'information');">
		<h3><?php if($pet->get_principale() == 1) echo '<img src="image/icone/couronne.png">'; ?><?php echo $pet->get_nom(); ?></h3>
			<img src="image/monstre/<?php echo $pet->monstre->get_lib(); ?>.png">
			<div class="monstre_infos">Script : [<?php echo $script_attaque['nom']; ?>]</div><br />
	</div>
		<?php
	}
}
else
{
	echo '<h5>Vous n\'avez pas de monstre</h5>';
}
}
?>
	</fieldset>
