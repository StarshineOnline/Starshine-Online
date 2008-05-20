<?php
include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//R�cup�re le timestamp en milliseconde de d�but de cr�ation de la page
$debut = getmicrotime();

//R�cup�ration des variables de connexion � la base et connexion � cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include('inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include('inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include('fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de g�rer les qu�tes
include('fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de g�rer l'�quipement
include('fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include('class/inventaire.class.php');

//On regarde si sworling est l�
$requete = "SELECT id FROM map_monstre WHERE lib = 'sworling'";
$req = $db->query($requete);
$x = ceil(rand(0, 150));
$y = ceil(rand(0, 150));
//Il est d�j� l�, on le t�l�port
if($db->num_rows > 0)
{
	$row = $db->read_assoc($req);
	$requete = "UPDATE map_monstre SET x = ".$x.", y = ".$y." WHERE id = ".$row['id']; 
}
//Il n'existe pas, on le recr��
else
{
	$requete = "INSERT INTO map_monstre VALUES('','56','".$x."','".$y."','1', 6, '".addslashes('Sworling le t�l�porteur')."','sworling', ".(time() + 360000).")";
}
$db->query($requete);

//Squelette pour la qu�te myriandre
$requete = "SELECT id FROM map_monstre WHERE type = 57";
$req = $db->query($requete);
$x = ceil(rand(0, 150));
$y = ceil(rand(0, 150));
//Il est d�j� l�, il se passe rien
if($db->num_rows > 0)
{
}
//Il n'existe pas, on le recr��
else
{
	$requete = "INSERT INTO map_monstre VALUES('','57','86','71','300', 5, '".addslashes('Squelette de Myriandre')."','protecteur_defunt', ".(time() + 360000).")"; 
	$db->query($requete);
}

//Cr�ation de la carte des conflits
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

echo 'Cr�ation de la carte des conflits<br />';

$im = imagecreate (450, 450)
   or die ("Impossible d'initialiser la biblioth�que GD");
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