<?php
session_start();
header ("Content-type: image/png");
include('class/db.class.php');
include('connect.php');
include('fonction/time.inc.php');
include('fonction/equipement.inc.php');
include('fonction/groupe.inc.php');
include('fonction/base.inc.php');
$im = imagecreatefrompng('image/carte.png');
$rouge = imagecolorallocate($im, 255, 0, 0);
$violet = imagecolorallocate($im, 102, 0, 153);
//Positionnement du perso sur la carte
$joueur = recupperso($_SESSION['ID']);
$x = ($joueur['x'] - 1) * 3;
$y = ($joueur['y'] - 1) * 3;
$x_fin = (($joueur['x'] - 1) * 3) + 2;
$y_fin = (($joueur['y'] - 1) * 3) + 2;
imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $rouge);
//Positionnement des membres du groupe
if($joueur['groupe'] > 0)
{
	$groupe = recupgroupe($joueur['groupe'], $joueur['x'].'-'.$joueur['y']);
	foreach($groupe['membre'] as $membre)
	{
		if($membre['id_joueur'] != $_SESSION['ID'])
		{
			$x = ($membre['x'] - 1) * 3;
			$y = ($membre['y'] - 1) * 3;
			$x_fin = (($membre['x'] - 1) * 3) + 2;
			$y_fin = (($membre['y'] - 1) * 3) + 2;
			imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $violet);
		}
	}
}
imagepng ($im);
imagedestroy($im);
?>