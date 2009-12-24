<?php

define('MAX_GRIMOIRE', 63);

if (isset($_POST['inv'])) {

  //var_dump(stripslashes($_POST['inv']));

  $inv = unserialize(stripslashes($_POST['inv']));

  //var_dump($inv);

  $newinv = array();

  foreach ($inv as $item) {
    $type = mb_substr($item, 0, 1);
    switch ($type) {
      case 'l':
        $id = mb_substr($item, 1);
        if ($id <= MAX_GRIMOIRE)
          $newinv[] = $item;
        break;
      default:
        $newinv[] = $item;
    }
  }

  echo 'Inventaire corrigé: \''.serialize($newinv).'\'<br/>';

}
else {
?>
<form method="post"><label>Joueur a debugguer : <input type="text" name="inv" /></label><input type="submit"/></form>
<?php
}

?>