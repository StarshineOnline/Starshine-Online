<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

echo "Création de l'image de la carte du monde\n";
$im = imagecreate (CARTE_WIDTH, CARTE_HEIGHT)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

$color1 = imagecolorallocate($im, 0x66, 0xdd, 0x66);  ///< couleur de la plaine
$color2 = imagecolorallocate($im, 0x00, 0x99, 0x00);  ///< couleur de la forêt
$color3 = imagecolorallocate($im, 0xff, 0xff, 0x00);  ///< couleur du désert
$color4 = imagecolorallocate($im, 0xff, 0xff, 0xff);  ///< couleur de la banquise
$color5 = imagecolorallocate($im, 0x00, 0x00, 0xff);  ///< couleur de l'eau
$color6 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);  ///< couleur de la route
$color7 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);  ///< couleur des capitales
$color8 = imagecolorallocate($im, 0x5d, 0x43, 0x00);  ///< couleur de la montagne
$color9 = imagecolorallocate($im, 0x00, 0x00, 0x00);  ///< couleur des marais
$color11 = imagecolorallocate($im, 0x41, 0x35, 0x3e); ///< couleur de la terre maudite
$colorHidden = imagecolorallocate($im, 0xe3, 0xe3, 0xe3); ///< couleur case masquée
$show_info[0] = $color1;
$show_info[1] = $color1;
$show_info[22] = $color1;
$show_info[2] = $color2;
$show_info[3] = $color3;
$show_info[106] = $color3;
$show_info[4] = $color4;
$show_info[5] = $color5;
$show_info[85] = $color5;
$show_info[6] = $color8;
$show_info[23] = $color8;
$show_info[7] = $color9;
$show_info[8] = $color6;
$show_info[9] = $color6;
$show_info[10] = $color7;
$show_info[11] = $color11;
$col = 'info';
$carte = 'image/carte.png';

//Requète pour l'affichage de la map
$requete = 'SELECT map.*,
(select count(1) FROM `map` map2 WHERE map.y BETWEEN (map2.y -1) AND (map2.y +1) AND map.x BETWEEN (map2.x -1) AND (map2.x +1) AND map2.royaume > 0 AND map2.x <= 190 AND map2.y <= 190) as afficher
FROM `map` map
WHERE map.x <= 190
AND map.y <= 190';
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	if ($row['x'] > 0 AND $row['y'] > 0 &&
			$row['x'] <= MAP_WIDTH && $row['y'] <= MAP_HEIGHT)
	{
		// case affichable
		if($row['afficher'] > 0){
			imagefilledrectangle($im, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
												 ((($row['x'] - 1) * 3) + 2),
												 ((($row['y'] - 1) * 3) + 2),
												 $show_info[$row[$col]]);
		}
		else{
			imagefilledrectangle($im, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
										((($row['x'] - 1) * 3) + 2),
										((($row['y'] - 1) * 3) + 2),
										$colorHidden);
		}
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);

?>