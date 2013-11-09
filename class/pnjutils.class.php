<?php

class pnjutils {
  static function get_gob_loc() {
    global $db;

    $sql = "select x, y from pnj where nom = 'Gafolin le somnambule'";
    $req = $db->query($sql);
    if ($req && ($row = $db->read_object($req))) {
      return array($row->x, $row->y);
    }
    die('Erreur SQL dans pnjutils::get_gob_loc()');
  }

  static function move_gob($x, $y) {
    global $db;
    $db->query('update pnj set x = '.((int)$x).', y = '.((int)$y).
               ' where nom = \'Gafolin le somnambule\'');
  }
}
