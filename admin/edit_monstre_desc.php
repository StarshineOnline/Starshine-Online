<?php // -*- mode: php; tab-width:2 -*-
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

	if(array_key_exists('description', $_POST))
	{
		$requete = "UPDATE monstre SET description = '".sSQL($_POST['description'])."' WHERE id = ".$id_monstre;
		$db->query($requete);
		echo 'Description mise à jour';
	}
	else 
	{
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
			Edition de la description du <?php echo $monstre['nom'];?>
			</div>
			<form action="edit_monstre_desc.php" method="post">
			Description : <textarea name="description" rows=10 COLS=40><?php echo $monstre['description'];?></textarea><br />
			<input type="hidden" name="id_monstre" value="<?php echo $id_monstre; ?>" />
			<input type="submit" value="Valider" />
			</form>

		</div>
	</div>
	
<?php
	}
}
?>
