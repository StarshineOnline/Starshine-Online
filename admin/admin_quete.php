<?php
$textures = false;
include('../haut.php');
setlocale(LC_ALL, 'fr_FR');
include('../haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Quetes
	</div>
	<table>
		<tr>
			<td>
				Nom
			</td>
			<td>
				Level
			</td>
			<td>
				Star
			</td>
			<td>
				Honneur
			</td>
			<td>
				XP
			</td>
			<td>
				Star / nombre
			</td>
			<td>
				Honneur / nombre
			</td>
			<td>
				XP / nombre
			</td>
		</tr>
	<?php
	$requete = "SELECT * FROM quete ORDER BY mode DESC, repete DESC, lvl_joueur ASC";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$objectif = unserialize($row['objectif']);
		$nombre = $objectif[0]->nombre;
		?>
		<tr>
			<td>
				<?php echo $row['nom']; ?>
			</td>
			<td>
				<?php echo $row['lvl_joueur']; ?>
			</td>
			<td>
				<?php echo $row['star']; ?>
			</td>
			<td>
				<?php echo $row['honneur']; ?>
			</td>
			<td>
				<?php echo $row['exp']; ?>
			</td>
			<td>
				<?php echo $row['star'] / $nombre; ?>
			</td>
			<td>
				<?php echo $row['honneur'] / $nombre; ?>
			</td>
			<td>
				<?php echo $row['exp'] / $nombre; ?>
			</td>
			<td>
				<?php echo strtoupper($row['mode']); ?>
			</td>
			<td>
				<?php echo strtoupper($row['repete']); ?>
			</td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
	include('../bas.php');
}
?>