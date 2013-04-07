<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

//Création de la carte de densité des mobs
$requete = "SELECT x, y, COUNT( * ) AS tot FROM `map_monstre` GROUP BY x, y ORDER BY tot DESC";
$req = $db->query($requete);
$check = false;
while($row = $db->read_assoc($req))
{
	if(!$check)
	{
		$max = $row['tot'];
		$check = true;
	}
	$map_monstre[$row['x']][$row['y']] = $row['tot'];
}

$im = imagecreate (CARTE_WIDTH, CARTE_HEIGHT)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);
$carte = 'image/carte_densite_mob.png';
$part = round($max / 6);

$color0 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color1 = imagecolorallocate($im, 0x66, 0xff, 0x33);
$color2 = imagecolorallocate($im, 0xe6, 0xfa, 0x37);
$color3 = imagecolorallocate($im, 0xe6, 0xba, 0x04);
$color4 = imagecolorallocate($im, 0xe6, 0x76, 0x04);
$color5 = imagecolorallocate($im, 0xe6, 0x3b, 0x07);
$color6 = imagecolorallocate($im, 0xff, 0x00, 0x00);
$show_info[0] = $color0;
$show_info[1] = $color1;
$show_info[2] = $color2;
$show_info[3] = $color3;
$show_info[4] = $color4;
$show_info[5] = $color5;
$show_info[6] = $color6;

//Création de la map
for($i = 1; $i <= $G_max_x; $i++)
{
	for($j = 1; $j <= $G_max_y; $j++)
	{
		if(!isset($map_monstre[$i][$j])) $densite = 0;
		else
		{
			if($map_monstre[$i][$j] < $part) $densite = 1;
			elseif($map_monstre[$i][$j] < $part * 2) $densite = 2;
			elseif($map_monstre[$i][$j] < $part * 3) $densite = 3;
			elseif($map_monstre[$i][$j] < $part * 4) $densite = 4;
			elseif($map_monstre[$i][$j] < $part * 5) $densite = 5;
			else $densite = 6;	
		}
		imagefilledrectangle($im, (($i - 1) * 3), (($j - 1) * 3), ((($i - 1) * 3) + 2), ((($j - 1) * 3) + 2), $show_info[$densite]);
	}
}
imagepng ($im, $carte);
imagedestroy($im);

?>