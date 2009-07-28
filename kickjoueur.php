<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//L'ID du joueur a kicker
$W_ID = $_GET['ID'];

//L'ID du groupe du joueur a kicker
$W_groupe = $_GET['groupe'];

degroup($W_ID, $W_groupe)

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />