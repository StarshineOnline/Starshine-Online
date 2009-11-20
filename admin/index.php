<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
$admin = true;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
?>
<div id="site"><?php
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
				Load
			</div>
			<img src="http://munin.starshine-online.com/localdomain/localhost.localdomain-load-day.png" /> <img src="http://munin.starshine-online.com/localdomain/localhost.localdomain-mysql_slowqueries-day.png" />
			<div class="titre">
				Derniers loots
			</div>
			<table>
			<tr>
				<td>
					Joueur
				</td>
				<td>
					Loot
				</td>
				<td>
					Date
				</td>
			</tr>
			<?php
			//Derniers Loots
			$requete = "SELECT * FROM journal WHERE action = 'loot' ORDER BY time DESC LIMIT 0, 10";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$joueur = new perso($row['id_perso']);
				?>
			<tr>
				<td>
					<a href="admin_joueur.php?direction=info_joueur&id=<?php echo $row['id_perso']; ?>"><?php echo $joueur->get_nom(); ?></a>
				</td>
				<td>
					<?php echo $row['valeur']; ?>
				</td>
				<td>
					<?php echo $row['time']; ?>
				</td>
			</tr>
				<?php
			}
			?>
			<table>
		</div>
	</div>
	<?php
	include_once(root.'bas.php');
}
?>
