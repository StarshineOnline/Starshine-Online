<?php // -*- php -*-

$cfg_log["sql"]['host'] = "localhost";
$cfg_log["sql"]['user'] = "starshine";
$cfg_log["sql"]['pass'] = "";
$cfg_log["sql"]['db'] = "starshine_log";
$cfg_log["sql"]['encoding'] = "utf8";

// Paramètres locaux, à ne pas mettre dans le SVN
//if (file_exists('connect.local.php')) include_once('connect.local.php');

$db_log = new db($cfg_log);

?>