<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
//L'id du joueur dont on veut l'info
$W_ID = $_GET['id_objet'];
echo description_objet($W_ID);
?>