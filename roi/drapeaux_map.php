<?php // -*- tab-width:2; mode: php -*-  
if (file_exists('../root.php')) {
  include_once('../root.php');
}

include_once(root.'haut_ajax.php');

$echelle = 3; // La carte est 3x plus grande

if (array_key_exists('img', $_GET) && 
    array_key_exists('map_drap_key', $_SESSION) &&
    $_GET['img'] == $_SESSION['map_drap_key']) {

	define('MAP_WIDTH', 190);
	define('MAP_HEIGHT', 190);
	define('CARTE3D_WIDTH', MAP_WIDTH * $echelle); // Echelle
	define('CARTE3D_HEIGHT', MAP_HEIGHT * $echelle); // Echelle

	$joueur = new perso($_SESSION['ID']);
	$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);
	$roy_id = $royaume->get_id();
	if ($joueur->get_rang_royaume() != 6) {
		security_block(URL_MANIPULATION);
	}

  header('Content-Type: image/png');

	$src = root.'image/carte-pose-drapeaux.png';
	$im3d = imagecreatefrompng($src);

	$color = imagecolorallocate($im3d, 255, 0, 255);
	$color_pose = imagecolorallocate($im3d, 255, 255, 0);
	$imalpha = imagecreatetruecolor(CARTE3D_WIDTH, CARTE3D_HEIGHT);
	$noir = imagecolorallocate($imalpha, 170, 170, 170);
	imagefill($imalpha, 0, 0, $noir);
	imagecolortransparent($imalpha, $noir);

	make_tmp_adj_tables($roy_id);
	$req4 = "select * from tmp_adj_lib";
	$req = $db->query($req4);

	while($row = $db->read_array($req)) {
		if ($row['x'] > 0 AND $row['x'] <= MAP_WIDTH AND
				$row['y'] > 0 AND $row['y'] <= MAP_HEIGHT) {
			imagerectangle($imalpha,
										 (($row['x'] - 1) * $echelle), (($row['y'] - 1) * $echelle),
										 ((($row['x'] - 1) * $echelle) + 3),
										 ((($row['y'] - 1) * $echelle) + 3),
										 $color);
		}
	}

	$req5 = "create temporary table tmp_adj_pose as select * from tmp_adj m where  exists (select x, y from placement p where p.x = m.x and p.y = m.y)";
	$db->query($req5); // Les cases adjacentes avec un placement
	$req6 = "select * from tmp_adj_pose";
	$req = $db->query($req6);
	
	while($row = $db->read_array($req)) {
		if ($row['x'] > 0 AND $row['x'] <= MAP_WIDTH AND
				$row['y'] > 0 AND $row['y'] <= MAP_HEIGHT) {
			imagerectangle($imalpha,
										 (($row['x'] - 1) * $echelle), (($row['y'] - 1) * $echelle),
										 ((($row['x'] - 1) * $echelle) + 3),
										 ((($row['y'] - 1) * $echelle) + 3),
										 $color_pose);
		}
	}
	
	imagecopymerge($im3d, $imalpha, 0, 0, 0, 0, CARTE3D_WIDTH, CARTE3D_HEIGHT, 80);
	imagepng($im3d);
	imagedestroy($im3d);
	imagedestroy($imalpha);

}
else {
  security_block(URL_MANIPULATION);
}
