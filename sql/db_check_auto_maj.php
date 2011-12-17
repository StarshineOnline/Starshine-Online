<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');

if (array_key_exists('HTTP_HOST', $_SERVER))
  security_block(URL_MANIPULATION);

// check if table is there
$req = $db->query("show tables like 'db_auto_maj'");
if ($db->num_rows($req) == 0)
  $db->query('source '.root.'sql/update_20111217.sql');

$loaded = array();
$req = $db->query('select loaded from db_auto_maj');
while ($row = $db->read_object($req)) {
  $loaded[] = $row->loaded;
}

$dh = opendir(root.'sql');
if (!$dh) die();

$files_to_load = array();
while (($file = readdir($dh)) !== false) {
  if (preg_match('/update_([0-9]{8}).sql/', $file, $regs)) {
    if ($regs[1] > 20111200 // Not before 2011-12-01
        && !in_array($file, $loaded)) {
      $files_to_load[] = $file;
    }
  }
}

// use mysqli for multi_query
$mysqli = new mysqli($cfg["sql"]['host'], $cfg["sql"]['user'],
										 $cfg["sql"]['pass'], $cfg["sql"]['db']);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

function verif_queries($sql) {
	$sql2 = trim($sql); // trim spaces
	$sql3 = trim($sql2, ";"); // remove trailing ;
	return $sql3;
}

sort($files_to_load, SORT_STRING);
foreach ($files_to_load as $file) {
  echo "Loading $file ";
  $ct = file_get_contents(root.'sql/'.$file);
  if (!$mysqli->multi_query(verif_queries($ct)))
    die("\nmulti_query failed: $mysqli->error \n");

  do {
    /* store first result set */
    if ($result = $mysqli->use_result()) {
      $result->close();
    }
    echo '.';
  } while ($mysqli->next_result());

  echo "\n";
  $sql = "insert into db_auto_maj values ('$file')";
  $db->query($sql);
}
