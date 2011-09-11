<?php
if (file_exists('root.php'))
  include_once('root.php');

//session_start();
header ("Content-type: image/png");
include_once(root.'inc/fp.php');
$im = imagecreatefrompng('image/carte.png');
$rouge = imagecolorallocate($im, 255, 0, 0);
$orange = imagecolorallocate($im, 255, 140, 0);
//Positionnement du perso sur la carte
$joueur = new perso($_SESSION['ID']);
$x = ($joueur->get_x() - 1) * 3;
$y = ($joueur->get_y() - 1) * 3;
$x_fin = (($joueur->get_x() - 1) * 3) + 2;
$y_fin = (($joueur->get_y() - 1) * 3) + 2;
imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $rouge);
//Positionnement des membres du groupe
if($joueur->get_groupe() > 0)
{
	$groupe = new groupe($joueur->get_groupe());
	$groupe->get_membre_joueur();
	foreach($groupe->membre_joueur as $membre)
	{
		if($membre->get_id() != $_SESSION['ID'])
		{
			$x = ($membre->get_x() - 1) * 3;
			$y = ($membre->get_y() - 1) * 3;
			$x_fin = (($membre->get_x() - 1) * 3) + 2;
			$y_fin = (($membre->get_y() - 1) * 3) + 2;
			imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $orange);
		}
	}
}
imagepng ($im);
imagedestroy($im);
?>
