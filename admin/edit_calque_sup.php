<?php // -*- mode: php; tab-width: 2 -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

/* add_*_to_head(); */

if (array_key_exists('newq', $_POST)) {
	$db->query("insert into map_type_calque select max(type) + 1 , '$_POST[calque]', '$_POST[nom]', '$_POST[decalage_x]', '$_POST[decalage_y]' from map_type_calque");
	header('Location: ?');
	exit(0);
}

if (array_key_exists('suppr', $_GET)) {
	$db->query("delete from map_type_calque where type = $_GET[suppr]");
	$db->query("update map set type = 2 where type = $_GET[suppr] and (x > 190 or y > 190)");
	$db->query("update map set type = 0 where type = $_GET[suppr]");
	header('Location: ?');
	exit(0);
}


include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');
echo '<div style="margin-left: 200px">';

/* site */
echo '<table><tr><th>id</td><th>calque</th><th>nom</th><th>decalage X</th><th>decalage Y</th><th>Action</th></tr>';
$req = $db->query("select * from map_type_calque");
while ($row = $db->read_object($req)) {
	echo "<tr><td>$row->type</td><td onclick=\"showCalque('$row->calque')\">$row->calque</td><td>$row->nom</td><td>$row->decalage_x</td><td>$row->decalage_y</td><td><a href=\"?suppr=$row->type\">Supprimer</a></td></tr>\n";
}
echo '<form id="newq" method="post" action="?">
<tr><td>Nouveau</td>
<td><input type="text" name="calque" style="width: 95%"></td>
<td><input type="text" name="nom"></td>
<td><input type="text" name="decalage_x" size="2" value="0"></td>
<td><input type="text" name="decalage_y" size="2" value="0"></td>
<td><input type="submit" name="newq" value="Ajouter"></td></tr>
</form>';
echo '</table>';

?>
<div id="previs" style="">Prévisualisation</div>
<script type="text/javascript">
	function showCalque(tex) {
	//$("#previs").attr({'background-image': "../image/texture/" + tex});

	$("#previs").html('<img src="../image/texture/' + tex + '" />');
	}
</script>
<?php

echo '</div>';
include_once(root.'admin/admin_bas.php');

?>