<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$image = imagecreate(100, 4);
$rouge = imagecolorallocate($image, 0xFF, 0x00, 0x00);
$blanc = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
imagefill($image, 0, 0, $blanc);
imagefilledRectangle($image, 0, 0, $_GET['longueur'], 4, $rouge);

header("Content-Type: image/png");
imagepng($image);
?>