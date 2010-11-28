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

if(isset($_GET['id_pet']) AND $_GET['id_pet'] != NULL)
{
	$pet = new pet($_GET['id_pet']);
	$table = "action_pet";
	$sujet = $pet;
	$check_pet = true;
	$link = "id_pet=".$_GET['id_pet']."&amp;";
}
else
{
	$table = "action_perso";
	$sujet = $joueur;
	$check_pet = false;
	$link = "";
}

$round_max = 10;
if($joueur->get_race() == 'orc') $round_max++;
?>
	<fieldset>
		<legend>Script de combat - Création</legend>
	<?php
	//Changement du nom du script
	if(array_key_exists('action', $_GET) && $_GET['action'] == 'change_nom')
	{
		if (array_key_exists('id_action', $_GET) && $_GET['id_action'] != '')
		{
			$requete = "UPDATE ".$table." SET nom = '".sSQL($_GET['nom_action'])."' WHERE id = ".sSQL($_GET['id_action']);
		}
		else
		{
			echo '<h5>Erreur de paramètres ...</h5>';
			exit();
		}
		if($db->query($requete))
		{
			echo '<h6>Modification du nom effectué avec succès !</h6>';
		}
	}
	?>
	<h3><strong>Vous avez <?php echo $sujet->get_reserve_bonus(); ?> réserves de mana au total par combat</h3>
	<?php
if(array_key_exists('id_action', $_GET) && $_GET['id_action'] == '')
{
	exit();
}
//On récupère le script id_action
if(array_key_exists('from', $_GET) && $_GET['id_action'] != '')
{
	if($check_pet) $action_t = recupaction_all($_GET['id_action'], true);
	else $action_t = recupaction_all($_GET['id_action']);
	$_GET['id_action'] = $action_t['id'];
	$_GET['nom_action'] = $action_t['nom'];
	$_GET['mode'] = $action_t['mode'];
}

	if(array_key_exists('id_action', $_GET))
	{
		if($check_pet) $action_t = recupaction_all($_GET['id_action'], true);
		else $action_t = recupaction_all($_GET['id_action']);
		$id_action = $_GET['id_action'];
		if($check_pet) $actionexplode = explode(';', recupaction($id_action, true));
		else $actionexplode = explode(';', recupaction($id_action));
	}
	else
	{
		$action_t['nom'] = $_GET['nom_action'];
		if($_GET['mode'] == 'a') $actionexplode = array();
		else
		{
			for($i = 1; $i <= $round_max; $i++)
			{
				$actionexplode[] = '#09='.$i.'@!';
			}
		}
		if($check_pet) $requete = "INSERT INTO action_pet VALUES(NULL, ".$joueur->get_id().", ".$pet->get_id_monstre().", '".sSQL($_GET['nom_action'])."', '".implode(';', $actionexplode)."', '".sSQL($_GET['mode'])."')";
		else $requete = "INSERT INTO action_perso VALUES(NULL, ".$joueur->get_id().", '".sSQL($_GET['nom_action'])."', '".implode(';', $actionexplode)."', '".sSQL($_GET['mode'])."')";
		$req = $db->query($requete);
		$id_action = $db->last_insert_id();
	}
	if($_GET['mode'] == 'a')
	{
	?>
	<h3>Nom du script : <input type="text" value="<?php echo $action_t['nom']; ?>" id="nom_action" name="nom_action" /> <a href="#" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;action=change_nom&amp;nom_action=' + document.getElementById('nom_action').value.replace(new RegExp(' '), '%20'), 'information');"><img src="image/valid.png" alt="Valider" title="Valider le nom du script" style="vertical-align : bottom;"/></a></h3>
	<?php
		if(array_key_exists('id_action', $_GET)) $action_t = recupaction_all($_GET['id_action']);
		//On supprime une action de script de combat
		if (isset($_GET['suppr']))
		{
			$suppr = $_GET['suppr'];
			$i = $suppr;
			//On décale tout le tableau
			while($i < count($actionexplode))
			{
				if ($i == (count($actionexplode) - 1))
				{
					unset($actionexplode[$i]);
				}
				else
				{
					$actionexplode[$i] = $actionexplode[$i+1];
				}
				$i++;
			}
			$action = implode(';', $actionexplode);
			$requete = "UPDATE ".$table." SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
		}
		
		//On monte une action d'un cran
		if (isset($_GET['up']) AND $_GET['up'] >= 1)
		{
			$up = $_GET['up'];
			$i = $up;
			$actionexplode_tmp = $actionexplode[$i];
			$actionexplode[$i] = $actionexplode[$i-1];
			$actionexplode[$i-1] = $actionexplode_tmp;
			
			$action = implode(';', $actionexplode);
			$requete = "UPDATE ".$table." SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
		}
		//Ajout d'une condition a l'action
		if(array_key_exists('valid_cond', $_GET))
		{
			switch($_GET['qui'])
			{
				//vous affecté
				case 've' :
					$_GET['op'] = '+';
					$_GET['si'] = 12;
				break;
				//Adversaire affecté
				case 'ae' :
					$_GET['op'] = '+';
					$_GET['si'] = 13;
				break;
				//Vous n'êtes pas affecté
				case 'vne' :
					$_GET['op'] = '°';
					$_GET['si'] = 10;
				break;
				//L'adversaire n'est pas affecté
				case 'ane' :
					$_GET['op'] = '°';
					$_GET['si'] = 11;
				break;
			}
			$_SESSION['script'][$id_action]['condition'][] = array('si' => $_GET['si'], 'op' => $_GET['op'], 'valeur' => $_GET['valeur']);
		}
		//On a choisi le sort / competence
		if(array_key_exists('final', $_GET))
		{
			$_SESSION['script'][$id_action]['final'] = $_GET['final'];
		}
		//Validation du script
		if (isset($_GET['valid']))
		{
			$action_temp = '';
			$i = 0;
			//Liste des conditions
			foreach($_SESSION['script'][$id_action]['condition'] as $condition)
			{
				if($i != 0) $action_temp .= 'µ';
				$si = $condition['si'];
				$op = $condition['op'];
				$valeur = $condition['valeur'];
				$action_temp .= '#'.$si;
				$action_temp .= $op.$valeur;
				$i++;
			}
			$action_temp .= '@';
			//On récupère la compétence / sort à lancer
			$final = $_SESSION['script'][$id_action]['final'];
			$typefinal = $final[0];
			$numfinal = mb_substr($final, 1, strlen($final));

			if ($typefinal == 's')
				$sujet->check_sort_combat_connu($numfinal);
			if ($typefinal == 'c')
				$sujet->check_comp_combat_connu($numfinal);			
			
			if ($final == '!')
			{
				$action_temp .= '!';
			}
			else
			{
				if ($typefinal == 's')
				{
					$action_temp .= '~'.$numfinal;
				}
				else $action_temp .= '_'.$numfinal;
			}
			$actionexplode[count($actionexplode)] = $action_temp;
			$action = implode(';', $actionexplode);
			$requete = "UPDATE ".$table." SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
			$_SESSION['script'] = array();
			
			if($sujet->get_action_a() == '')
				$sujet->set_action_a($id_action);
			if($sujet->get_action_d() == '')
				$sujet->set_action_d($id_action);
			$sujet->sauver();
		}
		?>
			</table>
		<?php
		//print_r($_SESSION['script'][$id_action]);
		echo affiche_condition_session($_SESSION['script'][$id_action], $sujet);
		//=== VALIDATION DE LA CONDITION ===
		if(array_key_exists('valid_cond', $_GET))
		{
			?>
			<input type="button" name="ajout" value="Ajouter une condition" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>', 'information');" />
			<input type="button" name="valid" value="Valider cette ligne" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;valid=ok', 'information');" />
			<?php
		}
		//=== CONDITION ===
		elseif(is_array($_SESSION['script'][$id_action]) AND array_key_exists('final', $_SESSION['script'][$id_action]))
		{
			//Liste des états
			$etats = get_etats();
		?>
			<h3>Ajouter une condition</h3>
			<form action="action.php" method="POST">
			<table>
			<tr>
				<td>
					Si 
					<select name="si" id="si" onchange="javascript:opii(this);">
						<option value=""></option>
						<option value="00">HP</option>
						<option value="01">Réserve de mana</option>
						<option value="09">Round</option>
						<option value="14">Utilisation de la compétence</option>
					</select>
				</td>
				<td>
					<select name="op" id="op" style="visibility : hidden">
						<option value=">">></option>
						<option value="<"><</option>
						<option value="=">=</option>
					</select>
					<input type="text" name="valeur" id="valeur" style="visibility : hidden; width : 40px;" /><br />
				</td>
				<td>
					<input type="button" name="valid_cond" value="Valider" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;si=' + document.getElementById('si').value + '&amp;op=' + document.getElementById('op').value + '&amp;valeur=' + document.getElementById('valeur').value + '&amp;valid_cond=ok', 'information');" /><br />
				</td>
			</tr>
			<?php
			//On récupère l'état lié au sort / à la compétence
			$id = mb_substr($_SESSION['script'][$id_action]['final'], 1, strlen($_SESSION['script'][$id_action]['final']));
			$type = $_SESSION['script'][$id_action]['final'][0];
			if($type != '!')
			{
				if($type == 's') $table = 'sort_combat'; else $table = 'comp_combat';
				$requete = "SELECT etat_lie FROM ".$table." WHERE id = ".$id;
				$req_etat = $db->query($requete);
				$row_etat = $db->read_assoc($req_etat);
				$etat_lie = $row_etat['etat_lie'];
				if($etat_lie != '')
				{
					$etat_explode = explode('-', $etat_lie);
					$qui = $etat_explode[0];
					$etat = $etat_explode[1];
				?>
				<tr colspan="2">
					<td style="text-align : right;">
						<select name="qui_s" id="qui_s">
							<option value="ve"<?php if($qui == 've') echo ' selected="selected"'; ?>>Vous êtes</option>
							<option value="vne"<?php if($qui == 'vne') echo ' selected="selected"'; ?>>Vous n'êtes pas</option>
							<option value="ae"<?php if($qui == 'ae') echo ' selected="selected"'; ?>>L'adversaire est</option>
							<option value="ane"<?php if($qui == 'ane') echo ' selected="selected"'; ?>>L'adversaire n'est pas</option>
						</select>
					</td>
					<td>
						<select name="etat_s" id="etat_s">
							<option value="<?php echo $etats[$etat]['id']; ?>"><?php echo $etats[$etat]['nom']; ?></option>
						</select>
					</td>
					<td>
						<input type="button" name="valid_cond" value="Valider" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;si=10&amp;op=o&amp;valeur=' + document.getElementById('etat_s').value + '&amp;qui=' + document.getElementById('qui_s').value + '&amp;valid_cond=ok', 'information');" /><br />
					</td>
				</tr>
				<?php
				}
			}
			else $etat = '';
			?>
			<tr colspan="2">
				<td style="text-align : right;">
					<select name="qui" id="qui">
						<option value="ve">Vous êtes</option>
						<option value="vne">Vous n'êtes pas</option>
						<option value="ae">L'adversaire est</option>
						<option value="ane">L'adversaire n'est pas</option>
					</select>
				</td>
				<td>
					<select name="etat" id="etat">
						<?php
						foreach($etats as $etat_liste)
						{
							if($etat_liste['id'] != $etats[$etat]['id'])
							{
							?>
							<option value="<?php echo $etat_liste['id']; ?>"><?php echo $etat_liste['nom']; ?></option>
							<?php
							}
						}
						?>
					</select>
				</td>
				<td>
					<input type="button" name="valid_cond" value="Valider" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;si=10&amp;op=o&amp;valeur=' + document.getElementById('etat').value + '&amp;qui=' + document.getElementById('qui').value + '&amp;valid_cond=ok', 'information');" /><br />
				</td>
			</tr>
			</table>
			</form>
		<?php
		}
		//=== SORT / COMPETENCE ===
		else
		{
		?>
			<form action="action.php" method="POST">
			<h3>Quel action voulez vous faire ?</h3>
		<?php
		//Affichage de la liste des sorts de combat
		$sort = explode(';', $sujet->get_sort_combat());
		if ($sort[0] != '')
		{
		?>
			<select name="final_s" id="final_s">
		<?php
			$requete = "SELECT * FROM sort_combat WHERE id IN (".implode(',', $sort).") ORDER BY comp_assoc, type, nom";
			$req = $db->query($requete);
			$comp_assoc = '';
			$i = 0;
			while($row = $db->read_array($req))
			{
				if($comp_assoc != $row['comp_assoc'])
				{
					if($i > 0) echo '</optgroup>';
					echo '<optgroup label="'.$Gtrad[$row['comp_assoc']].'">';
					$comp_assoc = $row['comp_assoc'];
				}
				$mpsort = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
				echo '<option value="s'.$row['id'].'">'.$row['nom'].' ('.$mpsort.' RM)</option>';
				$i++;
			}
		?>
			</select>
			<input type="button" name="valid" value="Utiliser ce sort" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;final=' + document.getElementById('final_s').value, 'information');" />
		<?php
		}
		//Affichage des compétences de combat
		$comp = explode(';', $sujet->get_comp_combat());
		if ($comp[0] != '')
		{
		?>
			<select name="final_c" id="final_c">
		<?php
			$requete = "SELECT * FROM comp_combat WHERE id IN (".implode(',', $comp).") ORDER BY comp_assoc, type, nom";
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				echo '<option value="c'.$row['id'].'">'.$row['nom'].' ('.$row['mp'].' Réserves)</option>';
			}
		?>
			</select>
			<input type="button" name="valid" value="Utiliser cette compétence" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;final=' + document.getElementById('final_c').value, 'information');" />
		<?php
		}
		?>
			</form>
			<input type="button" name="valid" value="Attaque simple" onclick="return envoiInfo('action.php?<?php echo $link; ?>mode=a&amp;id_action=<?php echo $id_action; ?>&amp;final=!', 'information');" />
		<?php
		}
		?>
		<br />
		Script en cours :<br />
		<?php
		//=== Affichage de la liste des actions ===
		$i = 0;
		echo '<table>';
		$count = count($actionexplode);
		while ($i < $count)
		{
			$echo = affiche_condition($actionexplode[$i], $sujet);
			echo 
			'
				<tr class="combat">
					<td>
						'.$echo.'
					</td>
					<td>
			';
			if ($i != 0) echo ' <a href="action.php?'.$link.'mode=a&amp;id_action='.$id_action.'&amp;up='.$i.'" onclick="return envoiInfo(this.href, \'information\')">Monter</a>';
			if($actionexplode[$i][0] != '') echo '</td><td><a href="action.php?'.$link.'mode=a&amp;id_action='.$id_action.'&amp;suppr='.$i.'" onclick="return envoiInfo(this.href, \'information\')">Supprimer</a>';
			echo '
					</td>
				</tr>';
			$i++;
		}
	}
	//Mode simplifié
	else
	{
		$dataJS = '';
		echo '<form action="actions.php" method="POST">';
		echo 'Nom du script : <input type="text" name="action_nom" id="action_nom" value="'. $action_t['nom'].'" /><br />';
		echo '<table>';
		for($i = 1; $i <= $round_max; $i++)
		{
			?>
				<tr><td>
				Round <?php echo $i; ?></td><td>
				<select name="final" id="final<?php echo $i; ?>">
					<option value="attaque">Attaquer</option>
			<?php
			
			$sort = explode(';', $sujet->get_sort_combat());
			if ($sort[0] != '')
			{
				$requete = "SELECT * FROM sort_combat WHERE id IN (".implode(',', $sort).")";
				$req = $db->query($requete);
				while($row = $db->read_array($req))
				{
					$mpsort = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
					if($actionexplode[($i - 1)] == '#09='.$i.'@~'.$row['id']) $selected = ' selected'; else $selected = '';
					echo '<option value="s'.$row['id'].'"'.$selected.'>Lancer '.$row['nom'].' ('.$mpsort.' Réserves)</option>';
				}
			}
			$comp = explode(';', $sujet->get_comp_combat());
			if ($comp[0] != '')
			{
				$requete = "SELECT * FROM comp_combat WHERE id IN (".implode(',', $comp).")";
				$req = $db->query($requete);
				while($row = $db->read_array($req))
				{
					if($actionexplode[($i - 1)] == '#09='.$i.'@_'.$row['id']) $selected = ' selected'; else $selected = '';
					echo '<option value="c'.$row['id'].'"'.$selected.'>Utiliser '.$row['nom'].' ('.$row['mp'].' Réserves)</option>';
				}
			}
			?>
			</select>
			</td></tr>
			<?php
			$dataJS .= "&amp;final".$i."=' + document.getElementById('final".$i."').value + '&amp;";
		}
		$dataJS .= "r=".($i - 1)."&amp;action_nom=' + encodeURIComponent($('#action_nom').val()) + '";
	echo '</table>';
	?>
	</form>
	<input type="button" name="valid" value="Ok" onclick="envoiInfo('<?php if(isset($check_pet)) echo "actions_pet.php?".$link; else echo "actions.php?"; ?>mode=s&amp;id_action=<?php echo $id_action; ?>&amp;valid=ok<?php echo $dataJS; ?>', 'information');" />
	<?php
		//echo $dataJS;
		/*&amp;r=<?php echo $i; ?>&amp;final=' + document.getElementById('final<?php echo $i; ?>').value + '*/
	}

?>
	</fieldset>
