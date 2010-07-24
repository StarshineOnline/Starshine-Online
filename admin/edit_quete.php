<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;

include_once(root.'inc/fp.php');

add_css_to_head('../css/datatables.css');
add_css_to_head('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

$rows = array();
$req = $db->query("select lvl_joueur,id,nom,description from quete order by lvl_joueur");
while ($row = $db->read_assoc($req))
{
	$nom = '<a href="javascript:showQuest('.$row['id'].')">'.$row['nom'].'</a>';
	$r = array($row['lvl_joueur'], $nom, nl2br($row['description']));
	$rows[] = $r;
}
$fields = array('niveau', 'nom', 'description');

echo '<div style="margin-left: 200px">';
print_dataTables($fields, $rows);
echo '<div id="showquest" style="margin: 10px;"></div>';
echo '</div>';

?>
<script type="text/javascript">
function showQuest(id) {
	$('#showquest').load('edit_quete_ajax.php?id=' + id);
}
</script>
<?php

include_once(root.'admin/admin_bas.php');

?>