<?php //  -*- tab-width:2; mode: php  -*-

if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');

$size_x = 190;
$size_y = 450;

$im = imagecreatefrompng(root.'admin/images/carte.png');
imagealphablending($im, false);
imagesavealpha($im, true);
$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);

$im2 = imagecreatetruecolor($size_x * 3, $size_y * 3);
$black = imagecolorallocate($im2, 0, 0, 0);
imagefilledrectangle($im2, 0, 0, $size_x * 3, $size_y * 3, $black);

$background_color = imagecolorallocatealpha($im2, 250, 250, 250, 80);
imagecopy($im2, $im, 0, 0, 0, 0, 190 * 3, 190 * 3);
$donjon = imagecolorallocate($im2, 164, 127, 78);
$mur = imagecolorallocate($im2, 71, 43, 6);
//$murs = imagecolorallocate($im2, 201, 243, 216);

// trace les donjons
$req = $db->query("select x, y from map where (y > 190 and x < 190) and (info % 10 = 6 and info >= 16)");
while($row = $db->read_object($req))
{
	$x = ($row->x - 1) * 3;
	$y = ($row->y - 1) * 3;
	$x_fin = $x + 2;
	$y_fin = $y + 2;
	imagefilledrectangle($im2, $x, $y, $x_fin, $y_fin, $mur);
}
$req = $db->query("select x, y from map where (y > 190 and x < 190) and (info > 99 or (info % 10 = 5 and info >= 15))");
while($row = $db->read_object($req))
{
	$x = ($row->x - 1) * 3;
	$y = ($row->y - 1) * 3;
	$x_fin = $x + 2;
	$y_fin = $y + 2;
	imagefilledrectangle($im2, $x, $y, $x_fin, $y_fin, $donjon);
}


// L'ordre est inversé ici, car le premier est écrasé par le second
$req = $db->query("select * from map_sound_zone order by ordre asc, type");
while($row = $db->read_object($req))
{
	$x = ($row->x1 - 1) * 3;
	$y = ($row->y1 - 1) * 3;
	$x_fin = (($row->x2 - 1) * 3) + 2;
	$y_fin = (($row->y2 - 1) * 3) + 2;
	imagefilledrectangle($im2, $x, $y, $x_fin, $y_fin, $background_color);
}

header ("Content-type: image/png");
imagepng ($im2);
imagedestroy($im2);
exit(0);

?>
