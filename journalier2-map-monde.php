<?php
if (file_exists('root.php'))
  include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

echo "Création de l'image de la carte du monde\n";

/**
 * 
 * @return true si la case affichable: colonisée ou adjacente à une case à une case colonisée
 */
function isAffichable($tab, $x, $y)
{
	// Vérif des cases adjacentes
	for ($i = $x - 1; $i <= $x + 1; $i++) {
		for ($j = $y - 1; $j <= $y + 1; $j++) {
			// case colonisée
			if(isset($tab[$i][$j]) && $tab[$i][$j] == 1){
				return true;
			}
		}
	}
	return false;
}

function do_map($file, $fog = true) {
	global $db;
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
	$colorHidden = imagecolorallocate($im, 0xe2, 0xd4, 0x9e); ///< couleur case masquée
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
	$show_info[108] = $color6;
	$show_info[109] = $color6;
	$show_info[10] = $color7;
	$show_info[11] = $color11;
	$show_info[16] = $color6; // donjon ?
	$show_info[95] = $color6; // donjon ?
	$col = 'info';
	$carte = $file;

	if ($fog) {
		// Récupération des cases colonisées
		$requete = 'SELECT map.*
	FROM `map` map
	WHERE map.x BETWEEN 1 AND '.MAP_WIDTH
			.' AND map.y BETWEEN 1 AND '.MAP_HEIGHT
			.' AND royaume > 0';
		$req = $db->query($requete);
		
		// Mémorisation des cases colonisées
		$casesColonisees = array(array());
		while($row = $db->read_array($req)){
			$casesColonisees[$row['x']][$row['y']] = 1;
		}
	}

	// Récupération de toutes les cases
	$requete = 'SELECT map.*
	FROM `map` map
	WHERE map.x BETWEEN 1 AND '.MAP_WIDTH
		.' AND map.y BETWEEN 1 AND '.MAP_HEIGHT;
	$req = $db->query($requete);
	
	while($row = $db->read_array($req))
	{
		// case affichable
		if (!$fog || isAffichable($casesColonisees, $row['x'], $row['y'])) {
			imagefilledrectangle($im, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
													 ((($row['x'] - 1) * 3) + 2),
													 ((($row['y'] - 1) * 3) + 2),
													 $show_info[$row[$col]]);
		}
		else {
			imagefilledrectangle($im, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
													 ((($row['x'] - 1) * 3) + 2),
													 ((($row['y'] - 1) * 3) + 2),
													 $colorHidden);
		}
	}
	imagepng ($im, $carte);
	imagedestroy($im);
}

do_map('image/carte.png', true);
do_map('admin/images/carte.png', false);

?>