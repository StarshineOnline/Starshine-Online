<?php

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

/* Tableau des types de zones à gérer */
$zones_type = array(
										'vide', /* Pas de zone */
										'nuage',
);
/* Fin du tableau */

if (array_key_exists('erase', $_GET)) {
	if ($_GET['erase'] == 'all') {
		$db->query("delete from map_zone");
	}
	else {
		// TODO
	}
}
if (array_key_exists('type', $_GET)) {
	$type = $_GET['type'];
	$x1 = ($_GET['x'] / 3) + 1;
	$y1 = ($_GET['y'] / 3) + 1;
	$x2 = (($_GET['x'] + $_GET['width']) / 3) + 1;
	$y2 = (($_GET['y'] + $_GET['height']) / 3) + 1;
	$db->query("insert into map_zone values ('$type', $x1, $y1, $x2, $y2)");
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

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

?>
<div id="imgJSselbox"></div>
<img src="carte_zone.php" id="map"
      onclick="getImageCropSelectionPoint('map',event);">

<table>
<thead><th>Appercu</th><th>Type</th><th>Action</th></thead>
<tbody>
<?php

foreach ($zones_type as $type) {
	echo '<tr><td>';
	foreach (array('nuit', 'jour', 'soir', 'matin') as $moment) {
		echo '&nbsp;<img src="../image/interface/calque-atmosphere-'.
			$type.'-'.$moment.'.png" alt="'.$type.'" width=100 height=100 />';
	}
	echo '</td><td>'.$type.'</td><td><input type="button" value="Appliquer" '.
	'onclick="setImageCropAreaSubmit(\'?type='.$type
	.'\',\'map\');" /></td></tr>';
}

echo '</tbody></table>';

echo '<p><a href="?erase=all">Tout effacer</a></p>';

print_foot();
?>
