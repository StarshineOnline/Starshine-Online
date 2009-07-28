<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$race = $_GET['race'];
$classe = $_GET['classe'];
$image = 'image/'.$race;
if (file_exists($image.'_'.$classe.'.png')) $image .= '_'.$classe.'.png';
elseif(file_exists($image.'_'.$classe.'.gif')) $image .= '_'.$classe.'.gif';
elseif (file_exists($image.'.png')) $image .= '.png';
else $image .= '.gif';
echo '<img src="'.$image.'" style="vertical-align : middle;" />';
?>