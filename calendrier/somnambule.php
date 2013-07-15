<?php // -*- mode: php; tab-width:2 -*-

if (file_exists('../root.php'))
  include_once('../root.php');
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'class/db.class.php');
include_once(root.'class/pnjutils.class.php');
include_once(root.'connect.php');

list($x, $y) = pnjutils::get_gob_loc();
if (moment_jour() == 'Nuit') {
  if ($x == 35 AND $y == 276)
    pnjutils::move_gob(46, 376); // Arrivée dans le donjon aquatique
  elseif ($x == 46 AND $y == 376)
    pnjutils::move_gob(45, 378);
  elseif ($x == 45 AND $y == 378)
    pnjutils::move_gob(43, 378);
  elseif ($x == 43 AND $y == 378)
    pnjutils::move_gob(41, 377);
  elseif ($x == 41 AND $y == 377)
    pnjutils::move_gob(40, 378);
  elseif ($x == 40 AND $y == 378)
    pnjutils::move_gob(39, 378); 
  elseif ($x == 39 AND $y == 378)
    pnjutils::move_gob(38, 379); 
  elseif ($x == 38 AND $y == 379)
    pnjutils::move_gob(38, 380); 
  elseif ($x == 38 AND $y == 380)
    pnjutils::move_gob(38, 381); 
  elseif ($x == 38 AND $y == 381)
    pnjutils::move_gob(38, 382); 
  elseif ($x == 38 AND $y == 382)
    pnjutils::move_gob(37, 383); 
  elseif ($x == 37 AND $y == 383)
    pnjutils::move_gob(43, 382); // Position statique de nuit
}
else {
  if (!($x == 35 AND $y == 276))
    pnjutils::move_gob(35, 276); // Position statique de jour
}