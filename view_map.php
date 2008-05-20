<?php

include('haut.php');

if(array_key_exists('show', $_GET))
{
	$info = $_GET['show'];
}
else
{
	$info = 'map';
}

$im = @imagecreate (395, 395)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 255);

if($info == 'map')
{
	$color1 = imagecolorallocate($im, 0x66, 0xdd, 0x66);
	$color2 = imagecolorallocate($im, 0x00, 0x99, 0x00);
	$color3 = imagecolorallocate($im, 0xff, 0xff, 0x00);
	$color4 = imagecolorallocate($im, 0xff, 0xff, 0xff);
	$color5 = imagecolorallocate($im, 0x00, 0x00, 0xff);
	$color6 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
	$color7 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
	$show_info[0] = $color1;
	$show_info[1] = $color1;
	$show_info[2] = $color2;
	$show_info[3] = $color3;
	$show_info[4] = $color4;
	$show_info[5] = $color5;
	$show_info[6] = $color6;
	$show_info[7] = $color6;
	$show_info[8] = $color6;
	$show_info[9] = $color6;
	$show_info[10] = $color7;
	$col = 'info';
}

if($info == 'royaume')
{
	$color1 = imagecolorallocate($im, 0xaa, 0xaa, 0xaa);
	$color2 = imagecolorallocate($im, 0x00, 0x68, 0xff);
	$color3 = imagecolorallocate($im, 0x00, 0x99, 0x00);
	$color4 = imagecolorallocate($im, 0xff, 0x00, 0x00);
	$color5 = imagecolorallocate($im, 0xff, 0xff, 0x00);
	$color6 = imagecolorallocate($im, 0x00, 0x00, 0xff);
	$color7 = imagecolorallocate($im, 0xff, 0xcc, 0xcc);
	$color8 = imagecolorallocate($im, 0xff, 0xa5, 0x00);
	$color9 = imagecolorallocate($im, 0x5c, 0x1e, 0x00);
	$color10 = imagecolorallocate($im, 0x00, 0x00, 0x00);
	$color11 = imagecolorallocate($im, 0x00, 0x00, 0xff);
	$color12 = imagecolorallocate($im, 0xff, 0xff, 0xff);
	$color13 = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
	$show_info[0] = $color1;
	$show_info[1] = $color2;
	$show_info[2] = $color3;
	$show_info[3] = $color4;
	$show_info[4] = $color5;
	$show_info[5] = $color6;
	$show_info[6] = $color7;
	$show_info[7] = $color8;
	$show_info[8] = $color9;
	$show_info[9] = $color10;
	$show_info[10] = $color11;
	$show_info[11] = $color12;
	$show_info[12] = $color13;
	$col = 'royaume';
}

if($info == 'royaume')
{
	$carte = 'image/carte_royaume.png';
}
else
{
	$carte = 'image/carte.png';
}
//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map ORDER BY ID';
$req = $db->query($requete);

?>

	<div id="carte">
	<table cellpadding="0" cellspacing="0" border="0">
	<tr style="width : 5px; height : 5px; padding : 0px; margin : 0px;">
<?php

$i = 0;
while($row = $db->read_array($req))
{
	$coord = convert_in_coord($row['ID']);
	$rowid = $row['ID'];
	$W_terrain_case = $row['decor'];
	
	if (($coord['x'] != 0) AND ($coord['y'] != 0))
	{
		imagefilledrectangle($im, (($coord['x'] - 1) * 4), (($coord['y'] - 1) * 4), ((($coord['x'] - 1) * 4) + 3), ((($coord['y'] - 1) * 4) + 3), $show_info[$row[$col]]);
	}
	$i++;
}
imagepng ($im, $carte);
imagedestroy($im);
echo '<a href="'.$carte.'">Afficher la carte</a>';	
?>