<?php
if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');


if( !array_key_exists('droits', $_SESSION) or !($_SESSION['droits'] & joueur::droit_interf_admin) )
{
  header("HTTP/1.1 403 Forbidden");
  die('<h1>Forbidden</h1>');
}

if( array_key_exists('type', $_GET) )
  $type = $_GET['type'];
else
  $type = 'bourg';
if( array_key_exists('race', $_GET) )
  $race = $_GET['race'];
else
  exit();
$im = imagecreate($G_max_x*3, $G_max_y*3) or die ("Impossible d'initialiser la bibliothèque GD");

$bg = imagecolorallocate ($im, 0xe2, 0xd4, 0x9e);
$couleur[0] = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
$couleur[1] = imagecolorallocate($im, 0x00, 0x68, 0xff);
$couleur[2] = imagecolorallocate($im, 0x00, 0x99, 0x00);
$couleur[3] = imagecolorallocate($im, 0xff, 0x00, 0x00);
$couleur[4] = imagecolorallocate($im, 0xff, 0xff, 0x00);
$couleur[6] = imagecolorallocate($im, 0xff, 0xcc, 0xcc);
$couleur[7] = imagecolorallocate($im, 0xff, 0xa5, 0x00);
$couleur[8] = imagecolorallocate($im, 0x5c, 0x1e, 0x00);
$couleur[9] = imagecolorallocate($im, 0x00, 0x00, 0x00);
$couleur[10] = imagecolorallocate($im, 0x00, 0x00, 0xff);
$couleur[11] = imagecolorallocate($im, 0xff, 0xff, 0xff);
$couleur[12] = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
imagefill($im, 0, 0, $bg);

// capitales
foreach($Trace as $r)
{
  $x = $r['spawn_x'];
  $y = $r['spawn_y'];
	imagefilledrectangle($im, ($x-6)*3, ($y-6)*3, ($x+6)*3, ($y+5)*3, $couleur[$r['numrace']]);
}

$requete = 'select facteur_entretien from royaume where id = '.$race;
$req = $db->query($requete);
$facteur = array();
$row = $db->read_array($req);
$dist = floor(7*$row['facteur_entretien']);

// bourgs
$requete = 'select x, y, royaume from construction where type';
$req = $db->query($requete);
while( $row = $db->read_array($req) )
{
  $x = $row['x'];
  $y = $row['y'];
	imagefilledrectangle($im, ($x-$dist)*3, ($y-$dist)*3, ($x+$dist)*3, ($y+$dist)*3, $couleur[$row['royaume']]);
}

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>