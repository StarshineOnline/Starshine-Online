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
var_dump($loaded);

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

sort($files_to_load, SORT_STRING);
foreach ($files_to_load as $file) {
  $sql = 'source '.root.'sql/'.$file;
  echo "Loading $file";
  echo "$sql \n";
  $sql = "insert into table db_auto_maj values ('$file')";
  echo "$sql \n";
}