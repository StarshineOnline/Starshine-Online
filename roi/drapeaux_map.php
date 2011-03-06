<?php // -*- tab-width:2; mode: php -*-  
if (file_exists('../root.php')) {
  include_once('../root.php');
}

session_start();

if (array_key_exists('img', $_GET) && 
    array_key_exists('map_drap_key', $_SESSION) &&
    $_GET['img'] == $_SESSION['map_drap_key']) {
  header('Content-Type: image/png');
}
else {
  include_once('haut_roi.php');
  // PAS BÔ !!
}
