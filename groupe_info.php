<?php
if(array_key_exists('javascript', $_GET))
{
	include('inc/fp.php');
	$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
	$joueur = recupperso($_SESSION['ID']);
	$groupe = recupgroupe($joueur['groupe'], $joueur['x'].'-'.$joueur['y']);
	$level_groupe = level_groupe($groupe);
	$num_joueur = groupe_trouve_joueur($joueur['ID'], $groupe);
	$share_xp = ($groupe['membre'][$num_joueur]['share_xp'] / $groupe['share_xp']);
}
?>
		<form>
		<div class="information_case">
		<table>
		<tr>
			<td>
				Nom
			</td>
			<td>
				<input name="nom" id="nom" value="<?php echo $groupe['nom']; ?>" <?php if($groupe['id_leader'] != $_SESSION['ID']) echo ' disabled="true"'; ?>>
			</td>
		</tr>
		<tr>
			<td>
				Groupe niveau
			</td>
			<td>
				: <?php echo $level_groupe[0]; ?>
			</td>
		</tr>
		<tr>
			<td>
				Mode de partage
			</td>
			<td>
				<select name="partage" id="partage" <?php if($groupe['id_leader'] != $_SESSION['ID']) echo ' disabled="true"'; ?>>
					<?php
					foreach($partages as $part)
					{
						?>
						<option value="<?php echo $part[0]; ?>"<?php if($groupe['partage'] == $part[0]) echo ' selected'; ?>><?php echo $part[1]; ?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Leader du groupe
			</td>
			<td>
				<select name="leader" id="leader" <?php if($groupe['id_leader'] != $_SESSION['ID']) echo ' disabled="true"'; ?>>
					<?php
					foreach($groupe['membre'] as $membre)
					{
						$memb = recupperso($membre['id_joueur']);
						?>
						<option value="<?php echo $membre['id_joueur']; ?>"<?php if($groupe['id_leader'] == $membre['id_joueur']) echo ' selected'; ?>><?php echo $memb['nom']; ?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		</table>
		<?php if($groupe['id_leader'] == $_SESSION['ID']) echo '<input type="button" onclick="envoiInfo(\'infogroupe.php?id='.$groupe['id'].'&amp;partage=\' + document.getElementById(\'partage\').value + \'&amp;leader=\' + document.getElementById(\'leader\').value + \'&amp;nom=\' + document.getElementById(\'nom\').value, \'information\');" value="Modifier" />'; ?>
		<br />
		<?php echo '<a href="degroup.php?ID='.$joueur['groupe'].'" onclick="if(confirm(\'Voulez vous quitter le groupe ?\')) return envoiInfo(this.href, \'information\'); else return false;">Quitter le groupe actuel</a>' ?>
		</div>
		</form>