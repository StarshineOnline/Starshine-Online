<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

if(array_key_exists('id_type', $_GET)) $id_type = $_GET['id_type'];
else echo 'ERREUR';
$type = $id_type[0];
$id = intval(mb_substr($id_type, 1, strlen($id_type)));
$joueur = new perso($_SESSION['ID']);
if($joueur->get_groupe() != 0) $groupe_joueur = recupgroupe($joueur->get_groupe(), ''); else $groupe_joueur = false;

normalize_entry_charset(array('titre', 'message'));

//Envoi du message
if(isset($_POST['message']))
{
	$erreur = false;
	$titre = addslashes($_POST['titre']);
	$message = addslashes($_POST['message']);
	if (empty($titre)){$titre = 'Sans titre';}
	if($titre != '')
	{
		if ($message != '')
		{
			$id_groupe = 0;
			$id_dest = 0;
			$id_thread = 0;
			if($type == 'r')
			{
				$thread = new messagerie_thread($id);
				$id_groupe = $thread->id_groupe;
				if($thread->id_dest == $joueur->get_id()) $id_dest = $thread->id_auteur;
				else $id_dest = $thread->id_dest;
				$id_thread = $thread->id_thread;
			}
			elseif($type == 'g')
			{
				$id_groupe = $id;
			}
			else
			{
				$id_dest = $id;
			}
			$messagerie = new messagerie($joueur->get_id());
			$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
			if($type == 'r')
			{
				?>
				<img src="image/pixel.gif" onload="envoiInfo('messagerie.php?id_thread=<?php echo $thread->id_thread; ?>', 'information');" />
				<?php
			}
			else echo '<h6>Message transmis avec succès</h6>';
		}
		else
		{
			echo '<h5>Vous n\'avez pas saisi de message</h5>';
			$erreur = true;
		}
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de titre</h5>';
		$erreur = true;
	}
	if($type != 'r') echo '<a href="messagerie.php" onclick="return envoiInfo(this.href, \'information\');">Retour à la messagerie</a><br />';
}
else
{
?>
<fieldset>
	<legend>Envoi d'un message</legend>
	<div class="information_case">
	<form method="post" action="envoimessage.php?id_type=<?php echo $id_type; ?>" id="formMessage">
		<?php
		if ($type == 'r')
		{
			$thread = new messagerie_thread($id);
			$thread->get_messages(1, 'ASC');
			$titre_message = stripslashes($thread->messages[0]->titre);
		}
		?>
		Titre du message :<br />
		<input type="text" name="titre" id="titre" size="30" value="<?php echo $titre_message;?>"/><br />
		<?// print_messbar() ?>
		Message :<br />
		<textarea name="message" id="message" cols="45" rows="12"></textarea><br />
		<br />
		<input type="button" onclick="envoiFormulaire('formMessage', 'information');" name="btnSubmit" value="Envoyer" />
	</form>
</fieldset>
<?php

}
?>
