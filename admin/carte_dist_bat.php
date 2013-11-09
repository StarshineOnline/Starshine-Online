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
$eau = imagecolorallocate($im, 0xcc, 0xff, 0xff);
imagefill($im, 0, 0, $bg);

$R = new royaume($race);

// Cases d'eau
$requete = 'select x, y from map where info = 5 or type in (1, 4)';
$req = $db->query($requete);
while( $row = $db->read_array($req) )
{
  $x = $row['x'];
  $y = $row['y'];
  imagefilledrectangle($im, $x*3, $y*3, ($x+1)*3-1, ($y+1)*3-1, $eau);
}

// capitales
$dist = $R->get_dist_bourg_capitale();
foreach($Trace as $r)
{
  $x = $r['spawn_x'];
  $y = $r['spawn_y'];
	imagefilledrectangle($im, ($x-6)*$dist, ($y-6)*$dist, ($x+7)*$dist-1, ($y+6)*$dist-1, $couleur[$r['numrace']]);
}


// bourgs
$dist1 = $R->get_dist_bourgs();
$dist2 = $R->get_dist_bourgs(true);
$requete = 'select x, y, royaume from construction where type = "'.$type.'"';
$req = $db->query($requete);
while( $row = $db->read_array($req) )
{
  $x = $row['x'];
  $y = $row['y'];
  $d = $row['royaume']==$race ? $dist1 : $dist2;
	imagefilledrectangle($im, ($x-$d)*3, ($y-$d)*3, ($x+$d+1)*3-1, ($y+$d+1)*3-1, $couleur[$row['royaume']]);
}

header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>
