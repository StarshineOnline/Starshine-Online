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

var cibledlg = null;
function selCible(t) {
	querier = t;
	cibledlg.dialog("open");
}

function doSelM(id) {
	cibledlg.dialog("close");
	querier.value = 'M' + id;
}

function doSelP(id) {
	cibledlg.dialog("close");
	querier.value = 'P' + id;
}
</script>
<?php

print_js_onload('cibledlg = $("#cibledlg").dialog({ autoOpen: false });');

echo '<div id="cibledlg" title="cibles"><table>'.
'<tr><th>Monstres</th><th>PNJ</th></tr><tr><td>';
$req = $db->query("select id, nom, lib as image from monstre");
while ($m = $db->read_object($req)) {
	echo '<img src="../image/monstre/'.$m->image.'.png" alt="'.$m->nom.
		'" /> <a href="javascript:doSelM('.$m->id.')">'.$m->nom.'</a><br/>';
}
echo '</td><td style="vertical-align: text-top">';
$req2 = $db->query("select id, nom, image from pnj");
while ($m = $db->read_object($req2)) {
	echo '<img src="../image/pnj/'.$m->image.'.png" alt="'.$m->nom.
		'" /> <a href="javascript:doSelP('.$m->id.')">'.$m->nom.'</a><br/>';
}
echo '</td></tr></table></div>';

include_once(root.'admin/admin_bas.php');

?>