<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
function db_connect($hote, $compte, $password, $database)
{
	global $db;
	return mysql_connect($hote, $compte, $password, $database);
}

function db_query($dab, $requete)
{
	global $db;
	return mysql_query($requete, $db);
}

function db_fetch_array($req)
{
	global $db;
	return mysql_fetch_array($req, $db);
}
?>