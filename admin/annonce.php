<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');

if( array_key_exists('message', $_POST) )
{
	$forum = array_key_exists('forum', $_POST) && $_POST['forum'];
	annonce::envoyer($_POST['message'], $forum);
	echo '<p><b>Annonce enregistrée.</b></p>';
}

?>
<h4> Message de l'annonce</h4>
<i>Pas de balise, 140 caractère max.</i>
<form action="annonce.php" method="POST">
	<textarea name="message" maxlength="140" required="required" cols="50" rows="4"></textarea><br/>
	<input type="checkbox" name="forum" checked="checked" />Publier aussi dans le forum (sujet des annonces courtes)<br/>
	<input type="submit" value="Envoyer"/>
</form>