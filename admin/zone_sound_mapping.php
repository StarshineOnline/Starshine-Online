<?php //  -*- tab-width:2; mode: php  -*-

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

/* Tableau des types de zones à gérer */
$zones_type = array( /* Pas de zone par défaut */
										'Goutte',
										'bulle_sous_eau',
										'sous_eau',
										'chutte'
);
/* Fin du tableau */

if (array_key_exists('erase', $_GET)) {
	if ($_GET['erase'] == 'all') {
		$db->query("delete from map_sound_zone");
	}
	elseif ($_GET['erase'] == 'one') {
		$q = "delete from map_sound_zone where x1 = $_GET[x1] and y1 = $_GET[y1] ".
			"and x2 = $_GET[x2] and y2 = $_GET[y2]";
		$db->query($q);
	}
	header("Location: ?");
	exit (0);
}
if (array_key_exists('type', $_GET)) {
	$type = $_GET['type'];
	$x1 = round($_GET['x'] / 3) + 1;
	$y1 = round($_GET['y'] / 3) + 1;
	$x2 = round(($_GET['x'] + $_GET['width']) / 3) + 1;
	$y2 = round(($_GET['y'] + $_GET['height']) / 3) + 1;
	$db->query("insert into map_sound_zone values ('$type', $x1, $y1, $x2, $y2, 0)");
	header("Location: ?");
	exit (0);
}
if (array_key_exists('order', $_GET)) {
	$sens = $_GET['order'] == 'plus' ? '+' : '-';
	$db->query("update map_sound_zone set ordre = ordre $sens 1 where ".
						 "x1 = $_GET[x1] and y1 = $_GET[y1] ".
						 "and x2 = $_GET[x2] and y2 = $_GET[y2]");
	header("Location: ?");
	exit (0);
}

add_data_to_head('    <style type="text/css">
        #imgJSselbox{
            position: absolute;
            margin: 0px;
            padding: 0px;
            visibility: hidden;
            width: 0px;
            height: 0px;
            border: 1px solid #006;
            color: #fff;
            background-image: url(selection_area.png);
            z-index: 20;
        }
</style>');
add_data_to_head('<script type="text/javascript" src="image_cropper.js"></script>');
add_data_to_head('<script type="text/javascript">
$(document).ready(function() {
    $("#dialog").dialog({ autoOpen: false });
  });
var disable_select = 0;
function popUp(x1,y1,x2,y2,event) {
  if (!(imgx == -1 && imgy == -1) || (cropw>0 || croph>0) || disable_select) {
    return getImageCropSelectionPoint("map", event);
  }
  $("#dialog").dialog("open");
  $("#remove_zone").html("<a href=\"?erase=one&x1=" + x1 + "&y1=" + y1
    + "&x2=" + x2 + "&y2=" + y2 + "\">Effacer</a>");
  $("#modif_order").html("<a href=\"?order=plus&x1=" + x1 + "&y1=" + y1
    + "&x2=" + x2 + "&y2=" + y2 + "\">Monter l\'ordre</a> " + 
    "<a href=\"?order=minus&x1=" + x1 + "&y1=" + y1
    + "&x2=" + x2 + "&y2=" + y2 + "\">Baisser l\'ordre</a>");
}
function popDown() { $("#dialog").dialog("close"); }
function toggleSelect() {
  disable_select = !disable_select;
  if (disable_select) {
    $("#toggleSelect").html("Activer la sélection des zones");
  } else {
    $("#toggleSelect").html("Desactiver la sélection des zones");
  }
}
</script>');

add_data_to_head('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />');

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

?>
<div id="imgJSselbox"></div>
<div id="dialog" title="Modifier la zone">
  <div id="remove_zone"></div>
  <hr/>
  <div id="modif_order"></div>
	<hr/>
  <a href="javascript:popDown()">Fermer</a>
</div>
<p><a href="javascript:toggleSelect()" id="toggleSelect">Desactiver la
selection des zones</a></p>
<img src="carte_sound_zone.php" id="map" usemap="#zones_map"
      onclick="getImageCropSelectionPoint('map', event);">
<table>
<thead><th>Appercu</th><th>Type</th><th>Action</th></thead>
<tbody>
<?php

foreach ($zones_type as $type) {
	echo '<tr><td><audio controls="controls"><source src="../image/son/'.$type.
	'.ogg" type="audio/ogg"><source src="../image/son/'.$type.
	'.mp3" type="audio/mp3"></audio>';
	echo '</td><td>'.$type.'</td><td><input type="button" value="Appliquer" '.
	'onclick="setImageCropAreaSubmit(\'?type='.$type
	.'\',\'map\');" /></td></tr>';
}

echo '</tbody></table>';

echo '<map name="zones_map" id="zones_map">';
$req = $db->query("select * from map_sound_zone order by ordre desc, type");
while($row = $db->read_object($req))
{
	$x = ($row->x1 - 1) * 3;
	$y = ($row->y1 - 1) * 3;
	$x_fin = (($row->x2 - 1) * 3) + 2;
	$y_fin = (($row->y2 - 1) * 3) + 2;
  $coords = "$x,$y,$x_fin,$y_fin";
  $r_coords = "$row->x1,$row->y1,$row->x2,$row->y2";
	echo "\n";
	echo '<area shape="rect" coords="'.$coords.
    '" onClick="popUp('.$r_coords.', event)" alt="'.$row->type.'" />';
}
echo "</map>\n";

echo '<p><a href="?erase=all">Tout effacer</a></p>';

print_foot();
?>
