<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
$im = imagecreatefrompng('image/carte.png');
$requete = "SELECT x, y, count( * ) AS count FROM perso GROUP BY x, y";
$req = $db->query($requete);
$couleur1 = imagecolorallocate($im, 255, 0, 0);
$couleur2 = imagecolorallocate($im, 255, 100, 100);
$couleur3 = imagecolorallocate($im, 255, 150, 150);
$couleur4 = imagecolorallocate($im, 255, 200, 200);
$couleur5 = imagecolorallocate($im, 255, 255, 255);
while($row = $db->read_row($req))
{
	if($row[2] < 5) $couleur = $couleur1;
	elseif($row[2] < 10) $couleur = $couleur2;
	elseif($row[2] < 20) $couleur = $couleur3;
	elseif($row[2] < 50) $couleur = $couleur4;
	else $couleur = $couleur5;
	echo 'X : '.$row[0].' / Y : '.$row[1].' / Joueurs : '.$row[2].'<br />';
	$x = ($row[0] - 1) * 4;
	$y = ($row[1] - 1) * 4;
	$x_fin = (($row[0] - 1) * 4) + 3;
	$y_fin = (($row[1] - 1) * 4) + 3;
	imagefilledrectangle($im, $x, $y, $x_fin, $y_fin, $couleur);
}
imagepng ($im, 'image/carte_tous_perso.png');
?>
<a href="image/persos.png">Afficher l'image générée</a>