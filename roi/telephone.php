<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');
if($joueur->get_rang_royaume() != 6  AND $joueur->get_id() != $royaume->get_ministre_economie() AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cette page vous est interdit</p>';
else if(array_key_exists('message', $_GET))
{
	$erreur = false;
	$titre = addslashes($_GET['titre']);
	$message = addslashes($_GET['message']);
	if (empty($titre)){$titre = 'Sans titre';}
	if($titre != '')
	{
		if ($_GET['id_destinataire'] == 0)
		{
			echo '<h5>Vous n\'avez pas saisi de destinataire</h5>';
		}
		elseif ($message != '')
		{
			$id_groupe = 0;
			$id_thread = 0;
			$id_dest = $_GET['id_destinataire'];
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
			echo '<h6>Message transmis avec succés</h6>';
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
}
elseif(array_key_exists('id_dest', $_GET))
{
?>
<fieldset>
	<legend>Envoi d'un message</legend>
	<div class="information_case">
	<form method="post" action="telephone.php?id_type=<?php echo $id_type; ?>" id="formMessage">
		Titre du message :<br />
		<input type="text" name="titre" id="titre" size="30" value=""/><br />
		Message :<br />
		<textarea name="message" id="message" cols="45" rows="12"></textarea><br />
		<br />
		<input type="hidden" name="id_destinataire" id="id_destinataire" value="<?php echo $_GET['id_dest']; ?>" />
		<input type="button" onclick="envoiInfo('telephone.php?message=' + $('#message').val() + '&amp;titre=' + $('#titre').val() + '&amp;id_destinataire=' + $('#id_destinataire').val(), 'message_confirm');fermePopUp();" name="btnSubmit" value="Envoyer" />
	</form>
</fieldset>
<?php
}
?>
