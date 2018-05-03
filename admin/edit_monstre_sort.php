<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mise à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	if(array_key_exists('id_monstre', $_GET)) $id_monstre = $_GET['id_monstre'];
	else $id_monstre = $_POST['id_monstre'];
	$requete = "SELECT id, nom, description FROM monstre WHERE id = ".$id_monstre;
	$req = $db->query($requete);
	$monstre = $db->read_assoc($req);

	if(array_key_exists('sort_dressage', $_POST))
	{
		$requete = "UPDATE monstre SET sort_dressage = '".$_POST['sort_dressage']."' WHERE id = ".$id_monstre.";";
		echo $requete;
		$db->query($requete);
	}
	else 
	{
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
			Modification du sort de <?php echo $monstre['nom'];?>
			</div>
			<form action="edit_monstre_sort.php" method="post">
				Sort : <select name="sort_dressage">
				<?php
				$requete = "SELECT id, nom, incantation FROM sort_jeu ORDER BY incantation ASC";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="s<?php echo $row['id']; ?>"><?php echo $row['nom']; ?> (<?php echo $row['incantation']; ?>)</option>
					<?php
				}
				?>
				</select><br />
				<input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
				<input type="submit" value="Valider" />
			</form>

		</div>
	</div>
	
<?php
	}
}
?>