<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');


$total = 0;
$conflits = array();

for($i = 1; $i <= 150; $i++)
{
	for($j = 1; $j <= 150; $j++)
	{
		$conflits[$i][$j] = 0;
	}
}

$requete = "SELECT COUNT(*)AS tot, ceil(x/10) as cx, ceil(y/10) as cy FROM journal WHERE DAY(time )=DAY(CURDATE())AND MONTH(time )=MONTH(CURDATE())AND YEAR(time )=YEAR(CURDATE())AND action ='attaque' AND x <> 0 AND y <> 0 GROUP BY cx, cy ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$total += $row['tot'];
	$conflits[$row['cx']][$row['cy']] = $row['tot'];
}

echo 'Création de la carte des conflits<br />';

$im = imagecreate (450, 450)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

$color1 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color2 = imagecolorallocate($im, 0xff, 0x00, 0x00);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$carte = 'image/carte_conflit.png';

$i = 0;
while($i <= 15)
{
	$j = 0;
	while($j <= 15)
	{
		if($conflits[$i][$j] > ($total / 20))
		{
			imagefilledrectangle($im, ((($i - 1) * 30)), ((($j - 1) * 30)), ($i * 30), ($j * 30), $color2);
			imagerectangle($im, ((($i - 1) * 30) + 1), ((($j - 1) * 30)), ($i * 30), ($j * 30), $black);
		}
		else
		{
			imagefilledrectangle($im, ((($i - 1) * 30)), ((($j - 1) * 30)), ($i * 30), ($j * 30), $color1);
		}
		$j++;
	}
	$i++;
}
$im2 = imagecreatefrompng('image/carte.png');
imagegammacorrect($im2,1,0.6);
imagecopymerge($im2, $im, 0, 0, 0, 0, 450, 450, 30);
imagepng ($im2, $carte);
imagedestroy($im2);
imagedestroy($im);

?>