<?php

include('inc/fp.php');
$joueur = recupperso($_SESSION['ID']);

$partages = array(array('r', 'Aléatoire'), array('t', 'Par tour'), array('l', 'Leader'), array('k', 'Trouve = Garde'));
if(array_key_exists('partage', $_GET))
{
	$requete = "UPDATE groupe SET partage = '".sSQL($_GET['partage'])."' WHERE id = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'n' WHERE id_groupe = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'y' WHERE id_joueur = ".sSQL($_GET['leader']);
	$db->query($requete);
	?>
	<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" style="float : left;" />
	<?php
}
if(array_key_exists('suppinvit', $_GET))
{
	$requete = "DELETE FROM invitation WHERE ID = ".sSQL($_GET['suppinvit']);
	$db->query($requete);
}
$groupe = recupgroupe($_GET['id'], $joueur['x'].'-'.$joueur['y']);
$level_groupe = level_groupe($groupe);
$num_joueur = groupe_trouve_joueur($joueur['ID'], $groupe);
$share_xp = ($groupe['membre'][$num_joueur]['share_xp'] / $groupe['share_xp']);
?>
<h2>Informations sur votre groupe</h2>
<form>
<div class="information_case">
<table>
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
</div>
<?php if($groupe['id_leader'] == $_SESSION['ID']) echo '<input type="button" onclick="javascript:envoiInfo(\'infogroupe.php?id='.$groupe['id'].'&amp;partage=\' + document.getElementById(\'partage\').value + \'&amp;leader=\' + document.getElementById(\'leader\').value, \'information\');" value="Modifier" />'; ?>
</form>
<br />
<? echo '<a href="javascript:if(confirm(\'Voulez vous quitter le groupe ?\')) envoiInfo(\'degroup.php?ID='.$joueur['groupe'].'\', \'information\')">Quitter le groupe actuel</a>' ?>

<?php
if($groupe['id_leader'] ==  $_SESSION['ID'])
{
	$requete = "SELECT * FROM invitation WHERE groupe = ".$groupe['id'];
	$req = $db->query($requete);

	echo '<h3>Invitations envoyées</h3>';
	echo '<ul>';
	while($row = $db->read_assoc($req))
	{
		$perso = recupperso($row['receveur']);
		echo '<li>'.$perso['nom'].' - '.$Gtrad[$perso['race']].' '.$perso['classe'].' - Niv.'.$perso['level'].' <a href="javascript:envoiInfo(\'infogroupe.php?id='.$groupe['id'].'&amp;suppinvit='.$row['ID'].'\', \'information\');">X</a></li>';
	}
	?>
	</ul>
	<?php

}
?>