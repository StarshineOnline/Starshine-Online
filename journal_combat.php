<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$options = recup_option($_SESSION['ID']);

if(array_key_exists('id', $_GET))
{
	$id = (int) $_GET['id'];
	$req = $db->query("SELECT * FROM combats WHERE id_journal = ".$id);
	$row = $db->read_assoc($req);
	$combat = new combat($row);
	$combat->afficher_combat();
}

?>