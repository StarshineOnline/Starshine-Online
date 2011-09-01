<?php // -*- mode: php; tab-width:2 -*-

if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'connect.php');
// using mysqli for transactions

$mysqli = new mysqli($cfg["sql"]['host'], $cfg["sql"]['user'],
										 $cfg["sql"]['pass'], $cfg["sql"]['db']);

// old-style connexio-check: php < 5.2.9 in prod
if (mysqli_connect_error()) {
    die('Erreur de connexion (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

function verif_queries($sql) {
	$sql2 = trim($sql); // trim spaces
	$sql3 = trim($sql2, ";"); // remove trailing ;
	return $sql3;
}

$mysqli->autocommit(false);

$sql = 'select * from `calendrier` where `date` < NOW() and `done` = 0';
$req = $mysqli->query($sql.' ORDER BY `date`');
if ($req) {
	while ($row = $req->fetch_object()) {
		$sql = 'update calendrier set done = 1 where id = '.$row->id;
		if (!$mysqli->query($sql)) die($mysqli->error);

		if ($row->eval) {
			eval($row->eval);
		}

		if ($row->sql) {
			if (!$mysqli->multi_query(verif_queries($row->sql))) die($mysqli->error);
			while ($mysqli->next_result()); //flush
		}

		if ($row->script) {
			$ret = 0;
			passthru('php '.$row->script.' id_task='.$this->id, $ret);
			if ($ret) die('exec error');
		}

		if ($row->next) {
			$sql = 'update `calendrier` set `done` = 0, `date` = `date` + `next` '.
				'where id = '.$row->id;
			if (!$mysqli->query($sql)) die($mysqli->error);
		}
		
		$mysqli->commit();
		$mysqli->autocommit(false);
	}
}

$mysqli->commit();
$mysqli->close();

exit(0);
