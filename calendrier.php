<?php // -*- mode: php; tab-width:2 -*-

if (file_exists('root.php'))
  include_once('root.php');

// Defined in inc/fp.php
date_default_timezone_set('Europe/Paris');

include_once(root.'class/db.class.php');
include_once(root.'connect.php');
// using mysqli for transactions

$mysqli = new mysqli($cfg["sql"]['host'], $cfg["sql"]['user'],
										 $cfg["sql"]['pass'], $cfg["sql"]['db']);

// Works as of PHP 5.2.9 and 5.3.0.
if ($mysqli->connect_error) {
  die('Erreur de connexion : ' . $mysqli->connect_error);
}

if ($_SERVER['argc'] > 1) {
  if (in_array('help', $_SERVER['argv']) ||
    in_array('-help', $_SERVER['argv'])) {
    die('usage: '.$_SERVER['argv'][0].' [help|runskip]'."\n");
  }
  if (in_array('runskip', $_SERVER['argv'])) {
    // run calendar skipping actions until all events are in the future
    $sql = 'update calendrier set `date` = '.
      'from_unixtime(unix_timestamp(`date`) + `nextu`) '.
      'where `date` < now() and `nextu` > 0 and `done` = 0';
    $done = false;
    $max = 0;
    $total = 0;
    $runs = 0;
    $mysqli->autocommit(false);
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) die($mysqli->error);
    do {
      $ret = $stmt->execute();
      if (!$ret) die($mysqli->error);
      if ($stmt->get_warnings()) die($stmt->get_warnings());
      if ($stmt->affected_rows > 0) {
        // Had an effect
        $total += $stmt->affected_rows;
        $runs++;
        if ($stmt->affected_rows > $max)
          $max = $stmt->affected_rows;
      }
      else {
        $done= true;
      }
      echo '.';
    } while (!$done);
    $stmt->close();
    $mysqli->commit();
    echo "Runskip done, executed $runs run(s), affecting $max different tasks for $total total modifications\n";
    exit (0);
  }
  die('usage: '.$_SERVER['argv'][0].' [help|runskip]'."\n".
      print_r($_SERVER['argv'], true)."\n");
}

function verif_queries($sql) {
	$sql2 = trim($sql); // trim spaces
	$sql3 = trim($sql2, ";"); // remove trailing ;
	return $sql3;
}

$mysqli->autocommit(false);

$sql = 'select * from `calendrier` where `date` <= NOW() and `done` = 0';
$req = $mysqli->query($sql.' ORDER BY `date`');
if ($req) {
	while ($row = $req->fetch_object()) {
		$sql = 'update calendrier set done = 1 where id = '.$row->id;
		if (!$mysqli->query($sql)) die($mysqli->error);

		if ($row->nextu && $row->nextu > 0) {
      /*
			$sql = 'update `calendrier` set `done` = 0, `date` = `date` + `next` '.
				'where id = '.$row->id;
      */
      $sql = 'update `calendrier` set `done` = 0, `date` = '.
        'from_unixtime(unix_timestamp(`date`) + `nextu`) '.
				'where id = '.$row->id;
			if (!$mysqli->query($sql)) die($mysqli->error);
		}

		if ($row->eval) {
			eval($row->eval);
		}

		if ($row->sql) {
			if (!$mysqli->multi_query(verif_queries($row->sql))) die($mysqli->error);
			while ($mysqli->next_result()); //flush
		}

		if ($row->script) {
			$ret = 0;
			passthru('php '.$row->script.' id_task='.$row->id, $ret);
			if ($ret) die('exec error');
		}
		
		$mysqli->commit();
		$mysqli->autocommit(false);
	}
}

$mysqli->commit();

$sql = "delete from `calendrier` where `date` < DATE_SUB(NOW(), interval 7 day) and `done` = 1";
$mysqli->autocommit(true);
$mysqli->query($sql);

$mysqli->close();

exit(0);
