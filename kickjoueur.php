<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);

//L'ID du joueur a kicker
$W_ID = $_GET['ID'];
$joueur_kicked = new perso($W_ID);

//L'ID du groupe du joueur a kicker
$W_groupe = $_GET['groupe'];

degroup($W_ID, $W_groupe);

// On debloque l'achievement
$joueur_kicked->unlock_achiev('etre_expulse');
$joueur->unlock_achiev('expulser');

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />