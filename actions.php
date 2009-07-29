<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();
?>
	<fieldset>
		<legend>Script de combat</legend>
		<?php
		//Suppression du script
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'suppr_action')
		{
			$requete = "DELETE FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			if($db->query($requete))
			{
				echo '<h6>Script effacé avec succès !</h6>';
			}
		}
		if(array_key_exists('valid', $_GET))
		{
			//Si ya pas d'id_action alors création
			if($_GET['id_action'] == '')
			{
				$requete = "INSERT INTO action_perso VALUES('', ".$joueur->get_id().", '".sSQL($_POST['action_nom'])."', '', '".sSQL($_POST['mode'])."')";
				$req = $db->query($requete);
				$id_action = $db->last_insert_id();
			}
			else
			{
				$id_action = $_GET['id_action'];
			}
			$actionexplode = explode(';', recupaction($id_action));
			$i = 1;
			while($i <= $_POST['r'])
			{
				$final = $_POST['final'.$i];
				$action = '#09='.$i.'@';
				$typefinal = $final[0];
				$action_nom = sSQL($_POST['action_nom']);
				$numfinal = mb_substr($final, 1, strlen($final));
				if($final == 'attaque') $a_final = '!';
				elseif($typefinal == 's') $a_final = '~'.$numfinal;
				else $a_final = '_'.$numfinal;
				$action .= $a_final;
				$actionexplode[($i - 1)] = $action;
				$i++;
			}
			$requete = "UPDATE action_perso SET action = '".implode(';', $actionexplode)."', nom = '".$action_nom."' WHERE id = ".$id_action;
			//echo $requete;
			$db->query($requete);
			echo '<h6>Le script est bien été ajouté / modifié</h6>';
		}

		if(array_key_exists('action', $_GET) && $_GET['action'] == 'select' && $_GET['id_action'] != '')
		{
			if($_GET['type'] == 'attaque') $t = 'a';
			else $t = 'd';
			$id_action = mysql_escape_string($_GET['id_action']);
			$requete = "UPDATE perso SET action_".$t." = '".sSQL($id_action)."' WHERE ID = ".$joueur->get_id();
			if($db->query($requete))
			{
				$joueur['action_'.$t] = $_GET['id_action'];
				echo '<h6>Script '.$_GET['type'].' bien séléctionné.</h6>';
			}
		}
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'dupliq' && $_GET['id_action'] != '')
		{
			$requete = "SELECT action, mode FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$requete = "INSERT INTO action_perso VALUES('', ".$joueur->get_id().", '".sSQL($_GET['nom_copie'])."', '".$row['action']."', '".$row['mode']."')";
			//echo $requete;
			if($db->query($requete))
			{
				echo '<h6>Script dupliqué.</h6>';
			}
		}
		$script_attaque = recupaction_all($joueur->get_action_a());
		$script_defense = recupaction_all($joueur->get_action_d());
		?>
			Voici l'interface du script de combat, grâce à celui-ci vous pourrez attaquer avec des sorts ou des compétences.<br />
			<h3>Création</h3>
				<ul>
					<li><a href="action.php?mode=s" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat en mode simplifié</a></li>
					<li><a href="action.php?mode=a" onclick="return envoiInfo(this.href, 'information');">Créer un script de combat en mode avancé</a></li>
					<li>
						Copier le script : <select name="id_action_c" id="id_action_c">
							<?php
								$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
								$req = $db->query($requete);
								while($row = $db->read_assoc($req))
								{
									echo '<option value="'.$row['id'].'">'.$row['nom'].'</option>';
								}
							?>
								</select>
								en le nommant <input type="text" value="copie" name="nom_copie" id="nom_copie" style="width : 50px;" />
 								<input type="button" name="valid" value="Copier" onclick="envoiInfo('actions.php?action=dupliq&amp;id_action=' + document.getElementById('id_action_c').value + '&amp;nom_copie=' + document.getElementById('nom_copie').value, 'information');" />
					</li>
				</ul>
			<h3>Utilisation</h3>
				<table>
					<tr>
						<td>
							Script d'attaque
						</td>
						<td>
							: <select name="id_action_a" id="id_action_a">
						<?php
						$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
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
							<input type="button" name="valid" value="Utiliser" onclick="envoiInfo('actions.php?action=select&amp;type=attaque&amp;id_action=' + document.getElementById('id_action_a').value, 'information');" />
						</td>
					</tr>
					<tr>
						<td>
							Script de défense
						</td>
						<td>
							: <select name="id_action_d" id="id_action_d">
							<?php
								$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
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
							<input type="button" name="valid" value="Utiliser" onclick="envoiInfo('actions.php?action=select&amp;type=defense&amp;id_action=' + document.getElementById('id_action_d').value, 'information');" />
						</td>
					</tr>
					</table>
			<h3>Modification</h3>
				<table>
					<tr>
						<td>
							Modifier
						</td>
						<td>
							 : <select name="id_action" id="id_action">
						<?php
						$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
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
							<input type="button" name="valid" value="Modifier" style="width : 100px;" onclick="envoiInfo('action.php?from=modif&amp;id_action=' + document.getElementById('id_action').value, 'information');" />
						</td>
					</tr>
					<tr>
						<td>
							Supprimer
						</td>
						<td>
							: <select name="id_action_suppr" id="id_action_suppr">
							<?php
							$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur->get_id()." ORDER BY nom ASC";
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
							<input type="button" name="valid" value="Supprimer" style="width : 100px;" onclick="if(confirm('Voulez vous vraiment supprimer cette action ?')) envoiInfo('actions.php?action=suppr_action&amp;id_action=' + document.getElementById('id_action_suppr').value, 'information');" />
						</td>
					</tr>
				</table>
		<br />
		</form>
		<h3>Aide</h3>
		<p><strong>Généralités :</strong> Une attaque sur un monstre ou un joueur se fait généralement en 10 rounds (11 si l'un des deux est un Orc, 9 si l'attaquant
		a le buff Sacrifice sur lui). Vous pouvez paramétrer les 10 actions que vous allez faire dans le script de combat, afin de les réaliser à chaque attaque. Il
		est donc conseillé de créer un script d'attaque, et de créer vos 10 actions en ajoutant les compétences que vous voulez utiliser. Vous pouvez aussi créer un script
		de défense qui s'exécutera automatiquement si vous êtes attaqué par d'autres joueurs. (les compétences que vous pourrez utiliser dans votre script sont limitées par votre réserve de mana)</p>
		<p><a href="http://wiki.starshine-online.com/index.php?n=PmWiki.ScriptsDeCombat">Pour avoir plus d'informations sur le script de combat</a></p>
		</div>

	</fieldset>
