<?php // -*- mode: php; tab-width:2 -*-                                                                                                                                                                            

if (file_exists('../root.php'))
  include_once('../root.php');
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'connect.php');

$found = false;
$stmt = $db->prepare('select info, type from map where x = ? and y = ?');
$x = 0;
$y = 0;
$stmt->bind_param('ii', $x, $y);
$stmt->bind_result($info, $type);

while (!$found) {
  $x = rand(1, 150);
  $y = rand(1, 150);
  $stmt->execute();
  $result = $stmt->store_result();
  if (!$result) die($stmt->error);
  while ($stmt->fetch()) {
    if ($info != 5 && $type == 0)
      $found = true;
  }
  $stmt->free_result();
}
$stmt->reset();

echo "0\n";
$ID = 'sworling_bis';
$req = $db->param_query('SELECT * FROM map_monstre mm, monstre m WHERE mm.type = m.id AND m.id_manuel = ?', array($ID), 's');
$sworling_present = $req && $req->num_rows;
$req->close();
if ($sworling_present) {
  $db->param_query('UPDATE map_monstre mm, monstre m SET x = ?, y = ? WHERE mm.type = m.id AND m.id_manuel = ?', array($x, $y, $ID), 'iis');
}
else {
  $db->param_query('INSERT INTO map_monstre (type,x,y,hp,mort_naturelle) SELECT id, ?, ?, hp, UNIX_TIMESTAMP(NOW()) + (3600 * 31) FROM monstre WHERE id_manuel = ?',
                   array($x, $y, $ID), 'iis');
}
