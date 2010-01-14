<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
if(array_key_exists('javascript', $_GET))
{
	include_once(root.'inc/fp.php');
	$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
	$joueur = new perso($_SESSION['ID']);
	$groupe = new groupe($joueur->get_groupe());
	$num_joueur = $groupe->trouve_position_joueur($joueur->get_id());
}
else
{
	$joueur = new perso($_SESSION['ID']);
	if($joueur->get_groupe() != 0)
	{
		$groupe = new groupe($joueur->get_groupe());
	}
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
				<input name="nom" id="nom" value="<?php echo $groupe->get_nom(); ?>" <?php if($groupe->get_id_leader() != $joueur->get_id()) echo ' disabled="true"'; ?>>
			</td>
		</tr>
		<tr>
			<td>
				Groupe niveau
			</td>
			<td>
				: <?php echo $groupe->get_level();?>
			</td>
		</tr>
		<tr>
			<td>
				Mode de partage
			</td>
			<td>
				<select name="partage" id="partage" <?php if($groupe->get_id_leader() != $joueur->get_id()) echo ' disabled="true"'; ?>>
					<?php
					foreach($partages as $part)
					{
						?>
						<option value="<?php echo $part[0]; ?>"<?php if($groupe->get_partage() == $part[0]) echo ' selected'; ?>><?php echo $part[1]; ?></option>
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
				<select name="leader" id="leader" <?php if($groupe->get_id_leader() != $joueur->get_id()) echo ' disabled="true"'; ?>>
					<?php
					$groupe_membre = $groupe->get_membre_joueur();
					foreach($groupe_membre as $membre)
					{
						?>
						<option value="<?php echo $membre->get_id(); ?>"<?php if($groupe->get_id_leader() == $membre->get_id()) echo ' selected'; ?>><?php echo $membre->get_nom(); ?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		</table>
		<?php if($groupe->get_id_leader() == $joueur->get_id()) echo '<input type="button" onclick="envoiInfo(\'infogroupe.php?id='.$groupe->get_id().'&amp;partage=\' + $(\'#partage\').val() + \'&amp;leader=\' + $(\'#leader\').val() + \'&amp;nom=\' + encodeURIComponent($(\'#nom\').val()), \'information\');" value="Modifier" />'; ?>
		<br />
		<?php echo '<a href="degroup.php?ID='.$joueur->get_groupe().'" onclick="if(confirm(\'Voulez vous quitter le groupe ?\')) return envoiInfo(this.href, \'information\'); else return false;">Quitter le groupe actuel</a>'; ?>
		</div>
		</form>
