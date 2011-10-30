<?php // -*- mode: php; tab-width:2 -*-

if (file_exists('../root.php'))
  include_once('../root.php');
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'connect.php');

$pos = array();
for ($x = 66; $x <= 70; $x++) {
  for ($y = 295; $y <= 299; $y++) {
    $pos[] = array('x' => $x, 'y' => $y);
  }
}

shuffle($pos);

$id_boss = 187;
$id_illu = 189;
$nb_illu = 10;

$req = $db->query("select mort_naturelle from map_monstre where type = $id_boss");
if ($req && ($row = $db->read_object($req))) {
  $mort = $row->mort_naturelle;
  $pop_boss = 0;
}
else {
  if (date('H') < 5 && date('j') == 1) {
    $pop_boss = 1;
  }
  else {
    // Pas de pop du boss, et il est mort, donc kill des illu
    $req = $db->query("delete from map_monstre where type = $id_illu");
    exit (0);
  }
}

if ($pop_boss)
{
  $mort = time() + 86400 * 30;
  $db->query("insert into map_monstre(type, x, y, hp, mort_naturelle) select ".
             "id, 1000, 1000, hp, $mort from monstre where id = $id_boss");
}

$req = $db->query("select id from map_monstre where type = $id_illu");
$pop_illu = $nb_illu - ($req ? $db->num_rows($req) : 0);

while ($pop_illu--)
{
  $db->query("insert into map_monstre(type, x, y, hp, mort_naturelle) values ".
             "($id_illu, 1000, 1000, 100, $mort)");
}

$p = array_shift($pos);

$sql = "update map_monstre set x = $p[x], y = $p[y] where type = $id_boss";
$db->query($sql);

$sql = "select id from map_monstre where type = $id_illu";
$req = $db->query($sql);
while ($req && ($row = $db->read_object($req))) {
  $p = array_shift($pos);
  array_push($pos, $p);
  $sql = "update map_monstre set x = $p[x], y = $p[y] where id = $row->id";
  $db->query($sql);
}

$db->query("update map_monstre mm, monstre m set mm.hp = m.hp where mm.type = m.id AND m.id = $id_illu");
