<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

//On regarde si sworling est là
$requete = "SELECT id FROM map_monstre WHERE type = 56";
$req = $db->query($requete);
$x = ceil(rand(0, 150));
$y = ceil(rand(0, 150));
//Il est déjà là, on le téléport
if($db->num_rows > 0)
{
	$row = $db->read_assoc($req);
	$requete = "UPDATE map_monstre SET x = ".$x.", y = ".$y." WHERE id = ".$row['id']; 
}
//Il n'existe pas, on le recréé
else
{
	$requete = "INSERT INTO map_monstre VALUES('','56','".$x."','".$y."','1',"
    .(time() + 360000).")";
}
$db->query($requete);

//Squelette pour la quête myriandre
$requete = "SELECT id FROM map_monstre WHERE type = 57";
$req = $db->query($requete);
$x = ceil(rand(0, 150));
$y = ceil(rand(0, 150));
//Il est déjà là, il se passe rien
if($db->num_rows > 0)
{
}
//Il n'existe pas, on le recréé
else
{ // En SQL c'est mieux et on aura pas d'erreur
	$mort = time() + 360000;
	$requete = "insert into map_monstre (type, x, y, hp, mort_naturelle) select".
		" m.id, d.x, d.y, m.hp, $mort from monstre m, donjon d".
    " where m.id = 57 and d.nom = 'Myriandre'";
	$db->query($requete);
}

//Création de la carte des conflits
$total = 0;
$conflits = array();

for($i = 1; $i <= 150; $i++)
{
	for($j = 1; $j <= 150; $j++)
	{
		$conflits[$i][$j] = 0;
	}
}

$requete = "SELECT COUNT(*) AS tot, ceil(x/10) as cx, ceil(y/10) as cy FROM journal WHERE DAY(time )=DAY(CURDATE())AND MONTH(time )=MONTH(CURDATE())AND YEAR(time )=YEAR(CURDATE())AND action ='attaque' AND x <> 0 AND y <> 0 GROUP BY cx, cy ORDER BY tot DESC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$total += $row['tot'];
	$conflits[$row['cx']][$row['cy']] = $row['tot'];
}

echo 'Création de la carte des conflits<br />';

$im = imagecreate (600, 600)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

$color1 = imagecolorallocate($im, 0xff, 0xff, 0xff);
$color2 = imagecolorallocate($im, 0xff, 0x00, 0x00);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
$carte = 'image/carte_conflit.png';

$i = 0;
while($i <= 20)
{
	$j = 0;
	while($j <= 20)
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
imagecopymerge($im2, $im, 0, 0, 0, 0, 600, 600, 30);
imagepng ($im2, $carte);
imagedestroy($im2);
imagedestroy($im);

?>