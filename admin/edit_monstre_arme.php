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
	$requete = "SELECT id, nom, arme FROM monstre WHERE id = ".$id_monstre;
	$req = $db->query($requete);
	$monstre = $db->read_assoc($req);

	$sel_e = '';
	$sel_d = '';
	$sel_a = '';
	$sel_n = '';

	if ($monstre['arme'] == 'epee') $sel_e = 'selected="selected"';
	elseif ($monstre['arme'] == 'dague') $sel_d = 'selected="selected"';
	elseif ($monstre['arme'] == 'arc') $sel_a = 'selected="selected"';
	else $sel_n = 'selected="selected"';

	if(array_key_exists('arme', $_POST))
	{
		$requete = "UPDATE monstre SET arme = '".sSQL($_POST['arme'])."' WHERE id = ".$id_monstre;
		$db->query($requete);
		echo 'Arme mise à jour';
	}
	else 
	{
	?>
	<div id="contenu">
		<div id="centre3">
			<div class="titre">
			Edition de l´arme du <?php echo $monstre['nom'];?>
			</div>
			<form action="edit_monstre_arme.php" method="post">
				<select name="arme">
					<option value="" <?php echo $sel_n; ?>>Aucune</option>
					<option value="epee" <?php echo $sel_e; ?>>Épée</option>
					<option value="dague" <?php echo $sel_d; ?>>Dague</option>
					<option value="arc" <?php echo $sel_a; ?>>Arc</option>
				</select>
				<input type="hidden" name="id_monstre"
							 value="<?php echo $id_monstre ?>" />
				<input type="submit" value="Valider" />
			</form>

		</div>
	</div>
	
<?php
	}
}
?>
