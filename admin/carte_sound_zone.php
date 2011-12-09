<?php //  -*- tab-width:2; mode: php  -*-

if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');

$im = imagecreatefrompng(root.'image/carte.png');
imagealphablending($im, false);
imagesavealpha($im, true);
$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);

$background_color = imagecolorallocatealpha($im, 0, 0, 0, 127);

// L'ordre est inversé ici, car le premier est écrasé par le second
$req = $db->query("select * from map_sound_zone order by ordre asc, type");
while($row = $db->read_object($req))
{
	$x = ($row->x1 - 1) * 3;
	$y = ($row->y1 - 1) * 3;
	$x_fin = (($row->x2 - 1) * 3) + 2 - $x;
	$y_fin = (($row->y2 - 1) * 3) + 2 - $y;
	imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $background_color);
}

header ("Content-type: image/png");
imagepng ($im);
imagedestroy($im);
exit(0);

?>
