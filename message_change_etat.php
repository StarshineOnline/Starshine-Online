<?php
include('inc/fp.php');
include('fonction/messagerie.inc.php');

$id_message = $_GET['id_message'];
$etat = $_GET['etat'];
if($etat == 'del')
{
	$message = new messagerie_message($id_message);
	$message->supprimer();
	echo '';
}
else
{
	$joueur = recupperso($_SESSION['ID']);
	$messagerie = new messagerie($_SESSION['ID']);
	$messagerie->set_etat($id_message, $etat);
	$messagerie->get_message($id_message);
	$message_affiche = message_affiche($messagerie->message, $joueur);
	echo $message_affiche;
}
?>