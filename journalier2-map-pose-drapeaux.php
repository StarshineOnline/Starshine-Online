<?php
if (file_exists('root.php'))
	include_once('root.php');
if (isset($_SERVER['REMOTE_ADDR'])) die('Forbidden connection from '.$_SERVER['REMOTE_ADDR']);

include_once('journalier2-head.php');

echo "\nCréation de l'image de la carte de pose de drapeaux\n";
$im = imagecreatefrompng('image/carte.png');
if (!$im) {
	die ("Impossible d'initialiser la bibliothèque GD");
}
$carte = 'image/carte-pose-drapeaux.png';


// prépare le calque transparent
$imalpha = imagecreatetruecolor(CARTE_WIDTH, CARTE_HEIGHT);
$noir = imagecolorallocate($imalpha, 0x00, 0x00, 0x00);
imagefill($imalpha, 0, 0, $noir);
imagecolortransparent($imalpha, $noir);

$colorColonisee = imagecolorallocate($imalpha, 0xff, 0xff, 0xff);

//Requète pour l'affichage de la map
$requete = 'SELECT map.*
		FROM `map` map
		WHERE map.x <= '.$G_max_x.'
		AND map.y <= '.$G_max_y;
$req = $db->query($requete);

$i = 0;
while($row = $db->read_array($req))
{
	if ($row['x'] > 0 AND $row['y'] > 0 &&
	$row['x'] <= MAP_WIDTH && $row['y'] <= MAP_HEIGHT)
	{
		$currentRoyaume = $row['royaume'];
		// case colonisée -> on éclaircit la texture
		if($currentRoyaume > 0){
			imagefilledrectangle($imalpha, (($row['x'] - 1) * 3), (($row['y'] - 1) * 3),
											((($row['x'] - 1) * 3) + 2),
											((($row['y'] - 1) * 3) + 2),
											$colorColonisee);
		}
	}
	$i++;
}

// merge du calque sur l'image
imagecopymerge($im, $imalpha, 0, 0, 0, 0, CARTE_WIDTH, CARTE_HEIGHT, 80);
imagepng ($im, $carte);
imagedestroy($im);
imagedestroy($imalpha);

?>