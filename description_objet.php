<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
//L'id du joueur dont on veut l'info
$W_ID = $_GET['id_objet'];
echo description_objet($W_ID);
?>