<?php

if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');

$im = imagecreatefrompng(root.'image/carte.png');
imagealphablending($im, false);
imagesavealpha($im, true);
$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
$type_img = array();

// L'ordre est inversé ici, car le premier est écrasé par le second
$req = $db->query("select * from map_zone order by ordre asc, type");
while($row = $db->read_object($req))
{
	if (!array_key_exists($row->type, $type_img)) {
		$file = root.'image/interface/calque-atmosphere-'.$row->type.'-journee.png';
		$type_img[$row->type] =	imagecreatefrompng($file);
		imagesavealpha($type_img[$row->type], true);
		list($width, $height) = getimagesize($file);
		$type_img[$row->type.'-width'] = $width;
		$type_img[$row->type.'-height'] = $height;
	}
	$x = ($row->x1 - 1) * 3;
	$y = ($row->y1 - 1) * 3;
	$x_fin = (($row->x2 - 1) * 3) + 2 - $x;
	$y_fin = (($row->y2 - 1) * 3) + 2 - $y;
	imagecopyresampled($im, $type_img[$row->type], $x, $y, 0, 0, $x_fin, $y_fin,
										 $type_img[$row->type.'-width'],
										 $type_img[$row->type.'-height']);
}

header ("Content-type: image/png");
imagepng ($im);
imagedestroy($im);
exit(0);

?>