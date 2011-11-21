<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');
session_start();
if (array_key_exists('update_maree', $_REQUEST)) {
	$x = intval($_REQUEST['x']);
	$y = intval($_REQUEST['y']);
	$type = intval($_REQUEST['type']);
	$db->query("update maree set type = $type where x = $x and y = $y");
	exit(0);
}

if (array_key_exists('delete_maree', $_REQUEST)) {
	$x = intval($_REQUEST['x']);
	$y = intval($_REQUEST['y']);
	if ($db->query("delete from maree where x = $x and y = $y"))
		die('1');
	else
	{
		$db->query("update map set info = floor(decor / 100) where x = $x and y = $y");
		die('0');
	}
}

if (array_key_exists('add_maree', $_REQUEST)) {
	$x = intval($_REQUEST['x']);
	$y = intval($_REQUEST['y']);
	$type = intval($_REQUEST['type']);
	$zone = intval($_REQUEST['zone']);
	if ($db->query("insert into maree(x, y, type, zone) values ($x, $y, $type, $zone)"))
		die('1');
	else
	{
		if ($zone == 1)
			$db->query("update map, maree set map.info = 101 where map.x = maree.x and map.y = maree.y and maree.zone = 1");
		die();
	}
}

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');

echo '<div style="margin-left: 200px">';

$requete = 'select * from maree';
$req = $db->query($requete);
echo '<table id="maree"><thead><tr><th>x</th><th>y</th><th>zone</th><th>calque</th><th>action</th></tr></thead><tbody>';
while ($row = $db->read_object($req)) {
	$id = $row->x.'_'.$row->y;
	echo "<tr id=\"tr_${id}\"><td>$row->x</td><td>$row->y</td><td>$row->zone</td><td><input name=\"type\" id=\"$id\" size=\"3\" type=\"text\" value=\"$row->type\" /></td><td>";
	echo '<button type="button" style="background:transparent; cursor:hand; border:none;" onclick="javascript:update_maree('.$row->x.', '.$row->y.')">';
	echo '<img alt="Mise à jour" title="Mise à jour" src="../image/interface/icone_refresh_bataille.png"></button>';
	echo '<button type="button" style="background:transparent; cursor:hand; border:none;" onclick="javascript:delete_maree('.$row->x.', '.$row->y.')">';
	//echo '<img alt="Suppression" title="Suppression" src="../image/messagerie_del.png" style="height: 16px"></button>';
	echo  '<span class="del"></button>';
	echo "</td></tr>\n";
}
echo '</tbody></table>';
print_js_onload('gTable = $("#maree").dataTable({"sPaginationType": "full_numbers", "bJQueryUI": false});'.
								'$("#maree div.dataTables_wrapper").css("width", "150px");');
print_js_onload('$("#maree td").css("width", "30px");');


?>

</div>
<script type="text/javascript">
function update_maree(x, y) {
	var v = $('#' + x + '_' + y);
	$.get('marees.php?update_maree', {x: x, y: y, type: v.val()});
}
function delete_maree(x, y) {
	if (confirm('Supprimer la case ' + x + '/' + y + ' ?')) {
		$.get('marees.php?delete_maree', {x: x, y: y}, function(data) {
				if (data == '1') {
					var tr = $('#tr_' + x + '_' + y).get(0);
					gTable.fnDeleteRow(tr);
				} else { alert(data); }
			});
	}
}
function aj_maree() {
	var a = $("#add_maree");
	var x = a.find("input[name='x']").val();
	var y = a.find("input[name='y']").val();
	var zone = a.find("input[name='zone']").val();
	var type = a.find("select option:selected").val();
	if (x > 0 && y > 0 && zone > 0 && type) {
		$.get('marees.php?add_maree', a.serialize(), function(data) {
				if (data == '1') {
					var i = gTable.fnAddData([x, y, zone, type, '']);
				} else { alert(data); }
			});
	}
}
</script>

<p>
<hr/>
</p>

<form name="add_maree" id="add_maree">
<p>
<label>X: <input type="text" size="3" name="x"/></label>
<label>Y: <input type="text" size="3" name="y"/></label>
<label>Zone: <input type="text" size="3" name="zone"/></label>
<label>Type: <select name="type">
<?php
$req = $db->query("select type, nom from map_type_calque order by nom");
while ($row = $db->read_object($req)) {
	echo "<option value=\"$row->type\">$row->nom</option>\n";
}
?>
</select</label>
<input type="button" onclick="javascript:aj_maree(0,0)" value="Ajouter" />
</p>
</form>

<?php


include_once(root.'admin/admin_bas.php');

?>
