<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);
?>
		<h2>Script de combat</h2>
		<?php
		//Suppression du script
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'suppr_action')
		{
			$requete = "DELETE FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			if($db->query($requete))
			{
				echo '<h5>Script effac� avec succ�s !</h5>';
			}
		}
		if(array_key_exists('valid', $_GET))
		{
			//Si ya pas d'id_action alors cr�ation
			if($_GET['id_action'] == '')
			{
				$requete = "INSERT INTO action_perso VALUES('', ".$joueur['ID'].", '".sSQL($_POST['action_nom'])."', '', '".sSQL($_POST['mode'])."')";
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
				$numfinal = substr($final, 1, strlen($final));
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
			echo '<h6>Le script est bien �t� ajout� / modifi�</h6>';
		}

		if(array_key_exists('action', $_GET) && $_GET['action'] == 'select' && $_GET['id_action'] != '')
		{
			if($_GET['type'] == 'attaque') $t = 'a';
			else $t = 'd';
			$requete = "UPDATE perso SET action_".$t." = ".sSQL($_GET['id_action'])." WHERE ID = ".$joueur['ID'];
			if($db->query($requete))
			{
				$joueur['action_'.$t] = $_GET['id_action'];
				echo '<h6>Script '.$_GET['type'].' bien s�l�ctionn�.</h6>';
			}
		}
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'dupliq' && $_GET['id_action'] != '')
		{
			$requete = "SELECT action, mode FROM action_perso WHERE id = ".sSQL($_GET['id_action']);
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$requete = "INSERT INTO action_perso VALUES('', ".$joueur['ID'].", '".sSQL($_GET['nom_copie'])."', '".$row['action']."', '".$row['mode']."')";
			//echo $requete;
			if($db->query($requete))
			{
				echo '<h6>Script dupliqu�.</h6>';
			}
		}
		$script_attaque = recupaction_all($joueur['action_a']);
		$script_defense = recupaction_all($joueur['action_d']);
		?>
		<div class="information_case">
			Voici l'interface du script de combat, grace � celui ci vous pourrez attaquer avec des sorts ou des comp�tences.<br />
			<h3>Cr�ation</h3>
				<ul>
					<li><a href="javascript:envoiInfo('action.php?mode=s', 'information');">Cr�er un script de combat en mode simplifi�</a></li>
					<li><a href="javascript:envoiInfo('action.php?mode=a', 'information');">Cr�er un script de combat en mode avanc�</a></li>
					<li>
						Copier le script : <select name="id_action_c" id="id_action_c">
							<?php
								$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID']." ORDER BY nom ASC";
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
						$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID']." ORDER BY nom ASC";
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
							Script de d�fense
						</td>
						<td>
							: <select name="id_action_d" id="id_action_d">
							<?php
								$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID']." ORDER BY nom ASC";
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
						$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID']." ORDER BY nom ASC";
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
							$requete = "SELECT * FROM action_perso WHERE id_joueur = ".$joueur['ID']." ORDER BY nom ASC";
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
		<p><strong>G�n�ralit�s :</strong> Une attaque sur un monstre ou un joueur se fait g�n�ralement en 10 rounds (11 si l'un des deux est un Orc, 9 si l'attaquant
		a le buff Sacrifice sur lui). Vous pouvez param�trer les 10 actions que vous allez faire dans le script de combat, afin de les r�aliser � chaque attaque. Il
		est donc conseill� de cr�er un script d'attaque, et de cr�er vos 10 actions en ajoutant les comp�tences que vous voulez utiliser. Vous pouvez aussi cr�er un script
		de d�fense qui s'ex�cutera automatiquement si vous �tes attaqu�s par d'autre joueurs. (les comp�tences que vous pourrez utiliser dans votre script sont limit�es par votre r�serve de mana)</p>
		<p><a href="http://wiki.starshine-online.com/index.php?n=PmWiki.ScriptsDeCombat">Pour avoir plus d'information sur le script de combat</a></p>
		</div>
	</div>