<?php // -*- mode: php; tab-width:2 -*-

if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'connect.php');

function maree_haute($zone)
{
  global $db;

  // type = maree.type : calque marée
  $sql = "update map m, maree me set m.type = me.type, m.info = 101 ".
    "where m.x = me.x and m.y = me.y and me.zone = $zone";
  $db->query($sql);

  // cas particuliers
  switch ($zone)
  {
    case 3:
      $sql = "update map set decor = 3636, info = 36 where x = 28 and y = 412";
      $db->query($sql);
      $sql = "update map set decor = 4539, info = 45 where x = 28 and y = 411";
      $db->query($sql);
      break;
    case 4:
      $sql = "update map set decor = 3636, info = 36 where x = 32 and y = 445";
      $db->query($sql);
      $sql = "update map set decor = 4539, info = 45 where x = 32 and y = 444";
      $db->query($sql);
      break;
  }
}

function maree_basse($zone)
{
  global $db;

  // type = 2 (donjon) pour enlever le calque marée
  $sql = "update map m, maree me set m.type = 2, m.info = floor(m.decor/100) ".
    "where m.x = me.x and m.y = me.y and me.zone = $zone";
  $db->query($sql);

  // cas particuliers
  switch ($zone)
  {
    case 3:
      $sql = "update map set decor = 4661, info = 46 where x = 28 and y = 412";
      $db->query($sql);
      $sql = "update map set decor = 5536, info = 55 where x = 28 and y = 411";
      $db->query($sql);
      break;
    case 4:
      $sql = "update map set decor = 4661, info = 46 where x = 32 and y = 445";
      $db->query($sql);
      $sql = "update map set decor = 5536, info = 55 where x = 32 and y = 444";
      $db->query($sql);
      break;
  }
}

function noyade($zone = null)
{
  global $db;
  $sql = "update perso p, maree me, map m ".
    "set hp = hp - (370 - sqrt(vie * vie * vie)) ".
    "where me.x and m.y = me.y and p.x = me.x and p.y = me.y and m.type != 2";
  if ($zone != null)
    $sql .= " and me.zone = $zone";
  $db->query($sql);
}
