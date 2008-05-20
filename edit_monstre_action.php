<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	$id_monstre = $_GET['id_monstre'];
	$monstre = recupmonstre($id_monstre, false);
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Edition des actions de <?php echo $monstre['nom']; ?> - <?php echo $monstre['reserve']; ?> RM - Arme : <?php echo $monstre['arme_type']; ?>
			</div>
			<?php echo $monstre['action_d']; ?>
			<?php
		$action_t = $monstre['action_d'];
		$actionexplode = explode(';', $action_t);
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
			$requete = "UPDATE monstre SET action = '$action' WHERE id = '".$id_monstre."'";
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
			$requete = "UPDATE monstre SET action = '$action' WHERE id = '".$id_monstre."'";
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
			$_SESSION['script']['condition'][] = array('si' => $_GET['si'], 'op' => $_GET['op'], 'valeur' => $_GET['valeur']);
		}
		if (isset($_GET['valid']))
		{
			$action_temp = '';
			$i = 0;
			foreach($_SESSION['script']['condition'] as $condition)
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
			$requete = "UPDATE monstre SET action = '$action' WHERE id = '".$id_monstre."'";
			$req = $db->query($requete);
			$_SESSION['script'] = array();
		}
		
		$i = 0;
		echo '<table>';
		while ($i < count($actionexplode))
		{
			$echo = affiche_condition($actionexplode[$i], $monstre);
			echo 
			'
				<tr class="combat">
					<td>
						'.$echo.'
					</td>
					<td>
			';
			if ($i != 0) echo ' <a href="edit_monstre_action.php?mode=a&amp;id_monstre='.$id_monstre.'&amp;up='.$i.'">Monter</a>';
			echo '</td><td><a href="edit_monstre_action.php?mode=a&amp;id_monstre='.$id_monstre.'&amp;suppr='.$i.'">Supprimer</a>';
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
					<input type="button" onclick="document.location.href = 'edit_monstre_action.php?mode=a&amp;id_monstre=<?php echo $id_monstre; ?>'" value="Ajouter une condition" />
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
		
		$comp_actuel = '';
		$requete = "SELECT * FROM sort_combat WHERE incantation < ".$monstre['incantation']." ORDER BY comp_assoc ASC, incantation DESC";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			if($row['comp_assoc'] != $comp_actuel)
			{
				if($comp_actuel != '') echo '</optgroup>';
				echo '
				<optgroup label="'.$row['comp_assoc'].'" >';
				$comp_actuel = $row['comp_assoc'];
			}
			if($monstre[$row['comp_assoc']] >= $row['comp_requis'])
			{
				echo '<option value="s'.$row['id'].'">Lancer '.$row['nom'].' ('.$row['mp'].' RÃ©serves)</option>';
			}
		}
		echo '</optgroup>';
		
		$comp_actuel = '';
		$requete = "SELECT * FROM comp_combat ORDER BY comp_assoc ASC, comp_requis DESC";
		$req = $db->query($requete);
		while($row = $db->read_array($req))
		{
			if($row['comp_assoc'] != $comp_actuel)
			{
				if($comp_actuel != '') echo '</optgroup>';
				echo '
				<optgroup label="'.$row['comp_assoc'].'" >';
				$comp_actuel = $row['comp_assoc'];
			}
			if($monstre[$row['comp_assoc']] >= $row['comp_requis'])
			{
				echo '<option value="c'.$row['id'].'">Utiliser '.$row['nom'].' ('.$row['mp'].' RÃ©serves)</option>';
			}
		}
		echo '</optgroup>';
		?>
					</select>
				</td>
			</tr>
			</table>
				<input type="button" name="valid" value="Créer l'action" onclick="document.location.href = 'edit_monstre_action.php?mode=a&amp;id_monstre=<?php echo $id_monstre; ?>&amp;final=' + document.getElementById('final').value + '&amp;valid=ok'" /><br />
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
					<input type="button" name="valid_cond" value="Valider" onclick="document.location.href = 'edit_monstre_action.php?mode=a&amp;id_monstre=<?php echo $id_monstre; ?>&amp;si=' + document.getElementById('si').value + '&amp;op=' + document.getElementById('op').value + '&amp;valeur=' + document.getElementById('valeur').value + '&amp;valid_cond=ok'" /><br />
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
						<option value="posture">En posture</option>
						<option value="berzeker">Berzeker</option>
						<option value="appel_foret">Appel de la forêt</option>
						<option value="appel_tenebre">Appel des ténèbres</option>
						<option value="recuperation">Récupération</option>
						<option value="benediction">Béni</option>
						<option value="lien_sylvestre">Lien Sylvestre</option>
					</select>
				</td>
				<td>
					<input type="button" name="valid_cond" value="Valider" onclick="document.location.href = 'edit_monstre_action.php?mode=a&amp;id_monstre=<?php echo $id_monstre; ?>&amp;si=10&amp;op=o&amp;valeur=' + document.getElementById('etat').value + '&amp;qui=' + document.getElementById('qui').value + '&amp;valid_cond=ok'" /><br />
				</td>
			</tr>
			</table>
			</form>
		<?php
		}
		?>
	<?php
}
			?>
		</div>
	</div>