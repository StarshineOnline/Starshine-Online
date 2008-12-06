<?php
include('inc/fp.php');
include('fonction/messagerie.inc.php');

$id_thread = $_GET['id_thread'];
$thread = new messagerie_thread($id_thread);
if(array_key_exists('important', $_GET))
{
	$thread->important = $_GET['important'];
}
elseif(array_key_exists('suppr', $_GET) && $_GET['suppr'] == 1)
{
	$thread->supprimer(true);
}
?>