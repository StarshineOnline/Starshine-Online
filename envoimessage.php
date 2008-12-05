<?php
include('inc/fp.php');

if(array_key_exists('id_type', $_GET)) $id_type = $_GET['id_type'];
else echo 'ERREUR';
$type = $id_type[0];
$id = intval(mb_substr($id_type, 1, strlen($id_type)));
$joueur = recupperso($_SESSION['ID']);
if($joueur['groupe'] != 0) $groupe_joueur = recupgroupe($joueur['groupe'], ''); else $groupe_joueur = false;

normalize_entry_charset(array('titre', 'message'));

//Envoi du message
if(isset($_GET['titre']))
{
	$titre = addslashes($_GET['titre']);
	$message = addslashes($_GET['message']);
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
				if($thread->id_dest == $joueur['ID']) $id_dest = $thread->id_auteur;
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
			$messagerie = new messagerie($joueur['ID']);
			$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
			echo '<h6>Message transmis avec succès</h6>';
		}
		else
		{
			echo '<h5>Vous n\'avez pas saisi de message</h5>';
		}
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de titre</h5>';
	}
	echo '<a href="'.$_SERVER["HTTP_REFERER"].'">Retour au jeu</a><br />';
}
else
{
?>
<h2>Envoi d'un message</h2>
<div class="information_case">
<form method="post" action="javascript:message = document.getElementById('message').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('envoimessage.php?id_type=<?php echo $id_type; ?>&amp;titre=' + document.getElementById('titre').value + '&amp;message=' + message, 'information');">
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
	<input type="submit" name="btnSubmit" value="Envoyer" />
</form>
</div>
<?php

}
?>
