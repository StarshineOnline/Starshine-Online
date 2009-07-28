<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//L'ID du buff supprimer
$W_ID = sSQL($_GET['id']);

$requete = "DELETE FROM buff WHERE id = ".$W_ID;
$db->query($requete);

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />