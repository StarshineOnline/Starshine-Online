<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);
check_perso($joueur);
$round_max = 10;
if($joueur['race'] == 'orc') $round_max++;
?>
	<h2><a href="javascript:envoiInfo('actions.php', 'information');">Script de combat</a> -  Création</h2>
	<?php
	//Changement du nom du script
	if(array_key_exists('action', $_GET) && $_GET['action'] == 'change_nom')
	{
		$requete = "UPDATE action_perso SET nom = '".sSQL($_GET['nom_action'])."' WHERE id = ".sSQL($_GET['id_action']);
		if($db->query($requete))
		{
			echo '<h5>Modification du nom effectué avec succès !</h5>';
		}
	}
	?>
	<h3><strong>Vous avez <?php echo $joueur['reserve']; ?> réserves de mana au total par combat.<br />
	Votre réserve de mana vous permet de lancer en combat un nombre défini de compétences ou de sorts.</strong><br />
	</h3>
<?php
if(array_key_exists('id_action', $_GET) && $_GET['id_action'] == '')
{
	exit();
}
if(array_key_exists('from', $_GET) && $_GET['id_action'] != '')
{
	$action_t = recupaction_all($_GET['id_action']);
	$_GET['id_action'] = $action_t['id'];
	$_GET['nom_action'] = $action_t['nom'];
	$_GET['mode'] = $action_t['mode'];
}

	if(array_key_exists('id_action', $_GET))
	{
		$action_t = recupaction_all($_GET['id_action']);
		$id_action = $_GET['id_action'];
		$actionexplode = explode(';', recupaction($id_action));
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
		$requete = "INSERT INTO action_perso VALUES('', ".$joueur['ID'].", '".sSQL($_GET['nom_action'])."', '".implode(';', $actionexplode)."', '".sSQL($_GET['mode'])."')";
		$req = $db->query($requete);
		$id_action = $db->last_insert_id();
	}
	if($_GET['mode'] == 'a')
	{
		if(array_key_exists('id_action', $_GET)) $action_t = recupaction_all($_GET['id_action']);
		if (isset($_GET['suppr']))
		{
			$suppr = $_GET['suppr'];
			$i = $suppr;
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
			$requete = "UPDATE action_perso SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
		}
		
		if (isset($_GET['up']) AND $_GET['up'] >= 1)
		{
			$up = $_GET['up'];
			$i = $up;
			$actionexplode_tmp = $actionexplode[$i];
			$actionexplode[$i] = $actionexplode[$i-1];
			$actionexplode[$i-1] = $actionexplode_tmp;
			
			$action = implode(';', $actionexplode);
			$requete = "UPDATE action_perso SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
		}
		//Ajout d'une condition a l'action
		if(array_key_exists('valid_cond', $_GET))
		{
			switch($_GET['qui'])
			{
				case 've' :
					$_GET['op'] = '+';
					$_GET['si'] = 12;
				break;
				case 'ae' :
					$_GET['op'] = '+';
					$_GET['si'] = 13;
				break;
				case 'vne' :
					$_GET['op'] = '°';
					$_GET['si'] = 10;
				break;
				case 'ane' :
					$_GET['op'] = '°';
					$_GET['si'] = 11;
				break;
			}
			$_SESSION['script'][$id_action]['condition'][] = array('si' => $_GET['si'], 'op' => $_GET['op'], 'valeur' => $_GET['valeur']);
		}
		if (isset($_GET['valid']))
		{
			$action_temp = '';
			$i = 0;
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
			$final = $_GET['final'];
			$typefinal = $final[0];
			$numfinal = substr($final, 1, strlen($final));
			
			if ($final == 'attaque')
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
			$requete = "UPDATE action_perso SET action = '$action' WHERE id = '".$id_action."'";
			$req = $db->query($requete);
			$_SESSION['script'] = array();
		}
		
		$i = 0;
		echo '<table>';
		while ($i < count($actionexplode))
		{
			$echo = affiche_condition($actionexplode[$i], $joueur);
			echo 
			'
				<tr class="combat">
					<td>
						'.$echo.'
					</td>
					<td>
			';
			if ($i != 0) echo ' <a href="javascript:envoiInfo(\'action.php?mode=a&amp;id_action='.$id_action.'&amp;up='.$i.'\', \'information\')">Monter</a>';
			if($actionexplode[$i][0] != '') echo '</td><td><a href="javascript:envoiInfo(\'action.php?mode=a&amp;id_action='.$id_action.'&amp;suppr='.$i.'\', \'information\')">Supprimer</a>';
			echo '
					</td>
				</tr>';
			$i++;
		}
		?>
			</table>
			<br />
		<?php
		if(array_key_exists('valid_cond', $_GET))
		{
		?>
			<form action="action.php" method="POST">
			<table>
			<tr>
				<td>
				</td>
				<td>
					<input type="button" onclick="envoiInfo('action.php?mode=a&amp;id_action=<?php echo $id_action; ?>', 'information');" value="Ajouter une condition" />
				</td>
			</tr>
			<tr>
				<td>
					Alors
				</td>
				<td>
					<select name="final" id="final">
						<option value="attaque">Attaquer</option>
		<?php
		
		$sort = explode(';', $joueur['sort_combat']);
		if ($sort[0] != '')
		{
			$requete = "SELECT * FROM sort_combat WHERE id IN (".implode(',', $sort).") ORDER BY type, nom";
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				$mpsort = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
				echo '<option value="s'.$row['id'].'">Lancer '.$row['nom'].' ('.$mpsort.' Réserves)</option>';
			}
		}
		
		$comp = explode(';', $joueur['comp_combat']);
		if ($comp[0] != '')
		{
			$requete = "SELECT * FROM comp_combat WHERE id IN (".implode(',', $comp).") ORDER BY type, nom";
			$req = $db->query($requete);
			while($row = $db->read_array($req))
			{
				echo '<option value="c'.$row['id'].'">Utiliser '.$row['nom'].' ('.$row['mp'].' Réserves)</option>';
			}
		}
		?>
					</select>
				</td>
			</tr>
			</table>
				<input type="button" name="valid" value="Créer l'action" onclick="envoiInfo('action.php?mode=a&amp;id_action=<?php echo $id_action; ?>&amp;final=' + document.getElementById('final').value + '&amp;valid=ok', 'information');" /><br />
			</form>
		<?php
		}
		else
		{
		?>
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
					<input type="button" name="valid_cond" value="Valider" onclick="envoiInfo('action.php?mode=a&amp;id_action=<?php echo $id_action; ?>&amp;si=' + document.getElementById('si').value + '&amp;op=' + document.getElementById('op').value + '&amp;valeur=' + document.getElementById('valeur').value + '&amp;valid_cond=ok', 'information');" /><br />
				</td>
			</tr>
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
						<option value="poison">Empoisonné</option>
						<option value="paralysie">Paralysé</option>
						<option value="etourdit">Etourdit</option>
						<option value="silence">Silence</option>
						<option value="dissimulation">Dissimulé</option>
						<option value="glacer">Glacé</option>
						<option value="posture">En posture / Aura</option>
						<option value="berzeker">Berzeker</option>
						<option value="appel_foret">Appel de la forêt</option>
						<option value="appel_tenebre">Appel des ténèbres</option>
						<option value="recuperation">Récupération</option>
						<option value="benediction">Béni</option>
						<option value="lien_sylvestre">Lien Sylvestre</option>
					</select>
				</td>
				<td>
					<input type="button" name="valid_cond" value="Valider" onclick="envoiInfo('action.php?mode=a&amp;id_action=<?php echo $id_action; ?>&amp;si=10&amp;op=o&amp;valeur=' + document.getElementById('etat').value + '&amp;qui=' + document.getElementById('qui').value + '&amp;valid_cond=ok', 'information');" /><br />
				</td>
			</tr>
			</table>
			</form>
		<?php
		}
		?>
		<input type="text" value="<?php echo $action_t['nom']; ?>" id="nom_action" name="nom_action" />
		<input type="button" name="valid_script" value="Valider le nom" onclick="envoiInfo('action.php?mode=a&amp;id_action=<?php echo $id_action; ?>&amp;action=change_nom&amp;nom_action=' + document.getElementById('nom_action').value, 'information');" />
	<?php
	}
	//Mode simplifié
	else
	{
		$dataJS = '';
		echo '<div class="information_case">';
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
			
			$sort = explode(';', $joueur['sort_combat']);
			if ($sort[0] != '')
			{
				$requete = "SELECT * FROM sort_combat WHERE id IN (".implode(',', $sort).")";
				$req = $db->query($requete);
				while($row = $db->read_array($req))
				{
					$mpsort = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
					if($actionexplode[($i - 1)] == '#09='.$i.'@~'.$row['id']) $selected = ' selected'; else $selected = '';
					echo '<option value="s'.$row['id'].'"'.$selected.'>Lancer '.$row['nom'].' ('.$mpsort.' Réserves)</option>';
				}
			}
			$comp = explode(';', $joueur['comp_combat']);
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
			$dataJS .= "final".$i."=' + document.getElementById('final".$i."').value + '&amp;";
		}
		$dataJS .= "r=".($i - 1)."&amp;action_nom=' + document.getElementById('action_nom').value + '";
	echo '</table>';
	?>
	</form>
	<input type="button" name="valid" value="Ok" onclick="envoiInfoPostData('actions.php?mode=s&amp;id_action=<?php echo $id_action; ?>&amp;valid=ok', 'information', '<?php echo $dataJS; ?>');" />
	<?php
		//echo $dataJS;
		/*&amp;r=<?php echo $i; ?>&amp;final=' + document.getElementById('final<?php echo $i; ?>').value + '*/
	}
echo '</div>';

?>
</div>