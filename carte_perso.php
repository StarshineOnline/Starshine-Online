<?php
if (file_exists('root.php'))
  include_once('root.php');

//session_start();
header ("Content-type: image/png");
include_once(root.'inc/fp.php');

$im = imagecreatefrompng('image/carte.png');


//Positionnement du perso sur la carte
$perso = joueur::get_perso();
$perso_x = ($perso->get_x() - 1) * 3;
$perso_y = ($perso->get_y() - 1) * 3;

if( array_key_exists('vue', $_GET) )
{	
	$vue =  $_GET['vue']* 3;
  $taille = $vue * 2 + 3;
  $img2 = imagecreatetruecolor($taille, $taille);
	$colorHidden = imagecolorallocate($img2, 0xe2, 0xd4, 0x9e); ///< couleur case masquée
  imagefill($img2, 0, 0, $colorHidden);
  if( $perso->get_x() <= $G_max_x && $perso->get_y() <= $G_max_y )
  {
	  $pos_x = min(max($perso_x - $vue, 0), $G_max_x*3 - $taille);
	  $pos_y = min(max($perso_y - $vue, 0), $G_max_x*3 - $taille);
	  imagecopy($img2, $im, 0, 0, $pos_x, $pos_y, $taille, $taille);
	}
	else
	{
	  $pos_x = $perso_x - $vue;
	  $pos_y = $perso_y - $vue;
	}
}
else
{
  $img2 = $im;
	$pos_x = 0;
	$pos_y = 0;
}

$rouge = imagecolorallocate($img2, 255, 0, 0);
$orange = imagecolorallocate($img2, 255, 140, 0);

$x_fin = $perso_x + 2;
$y_fin = $perso_y + 2;
imagefilledrectangle($img2, $perso_x-$pos_x, $perso_y-$pos_y, $x_fin-$pos_x, $y_fin-$pos_y, $rouge);
//Positionnement des membres du groupe
if($perso->get_groupe() > 0)
{
	$groupe = new groupe($perso->get_groupe());
	$groupe->get_membre_joueur();
	foreach($groupe->membre_joueur as $membre)
	{
		if($membre->get_id() != $_SESSION['ID'])
		{
			$x = ($membre->get_x() - 1) * 3;
			$y = ($membre->get_y() - 1) * 3;
			$x_fin = (($membre->get_x() - 1) * 3) + 2;
			$y_fin = (($membre->get_y() - 1) * 3) + 2;
			imagefilledrectangle($img2, $x-$pos_x, $y-$pos_y, $x_fin-$pos_x, $y_fin-$pos_y, $orange);
		}
	}
}

imagepng($img2);
imagedestroy($img2);
imagedestroy($im);
?>
