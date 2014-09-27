<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
//L'ID du groupe du joueur
$W_ID = $_GET['ID'];

degroup($_SESSION['ID'], $W_ID)
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />