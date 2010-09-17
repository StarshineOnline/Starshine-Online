<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

echo 'Création de la carte des royaumes<br />';
$show_info = array();
$im = imagecreate (CARTE_WIDTH, CARTE_HEIGHT)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

function create_roy_colors(&$im, &$show_info)
{
	$color1 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
	$color2 = imagecolorallocate($im, 0x00, 0x68, 0xff);
	$color3 = imagecolorallocate($im, 0x00, 0x99, 0x00);
	$color4 = imagecolorallocate($im, 0xff, 0x00, 0x00);
	$color5 = imagecolorallocate($im, 0xff, 0xff, 0x00);
	$color6 = imagecolorallocate($im, 0x00, 0x00, 0xff);
	$color7 = imagecolorallocate($im, 0xff, 0xcc, 0xcc);
	$color8 = imagecolorallocate($im, 0xff, 0xa5, 0x00);
	$color9 = imagecolorallocate($im, 0x5c, 0x1e, 0x00);
	$color10 = imagecolorallocate($im, 0x00, 0x00, 0x00);
	$color11 = imagecolorallocate($im, 0x00, 0x00, 0xff);
	$color12 = imagecolorallocate($im, 0xff, 0xff, 0xff);
	$color13 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
	$show_info[0] = $color1;
	$show_info[1] = $color2;
	$show_info[2] = $color3;
	$show_info[3] = $color4;
	$show_info[4] = $color5;
	$show_info[5] = $color6;
	$show_info[6] = $color7;
	$show_info[7] = $color8;
	$show_info[8] = $color9;
	$show_info[9] = $color10;
	$show_info[10] = $color11;
	$show_info[11] = $color12;
	$show_info[12] = $color13;
}
create_roy_colors($im, $show_info);
$col = 'royaume';
$carte = 'image/carte_royaume.png';

// Carte 3d transparente
$im3d = imagecreatefrompng('image/carte3d-4.png');
if (!$im3d) {
	die ("Impossible d'initialiser la bibliothèque GD");
}
$show_info3d = array();
// prépare le calque transparent
$imalpha = imagecreatetruecolor(CARTE3D_WIDTH, CARTE3D_HEIGHT);
$noir = imagecolorallocate($imalpha, 170, 170, 170);
imagefill($imalpha, 0, 0, $noir);
imagecolortransparent($imalpha, $noir);
create_roy_colors($imalpha, $show_info_3d);

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map ORDER BY x,y';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{	
	if ($row['x'] > 0 AND $row['x'] <= MAP_WIDTH AND
			$row['y'] > 0 AND $row['y'] <= MAP_HEIGHT)
	{
		imagefilledrectangle($im, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
												 ((($row['x'] - 1) * 3) + 2),
												 ((($row['y'] - 1) * 3) + 2),
												 $show_info[$row[$col]]);

		// Carte 3d transparente
		imagefilledrectangle($imalpha,
												 (($row['x'] - 1) * 4), (($row['y'] - 1) * 4),
												 ((($row['x'] - 1) * 4) + 3),
												 ((($row['y'] - 1) * 4) + 3),
												 $show_info_3d[$row[$col]]);
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);

// Carte 3d transparente
// ajoute le calque
imagecopymerge($im3d, $imalpha, 0, 0, 0, 0, CARTE3D_WIDTH, CARTE3D_HEIGHT, 80);
imagepng($im3d, 'image/carte3d-royaumes.png');
imagedestroy($im3d);
imagedestroy($imalpha);

?>