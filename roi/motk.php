<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
elseif(array_key_exists('message', $_GET))
{
	if ($_GET['message'] != '')
	{
		if($royaume->set_motk(sSQL($_GET['message'])))
		{
			echo '<h6>Message du roi bien modifiée !</h6>';
		}
		else
		{
			echo('<h5>Erreur lors de l\'envoi du message</h5>');
		}
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de message</h5>';
	}
}
else
{
	echo "<div id='propagande'>";
	//Message actuel
	$message = transform_texte($royaume->get_motk());
	if (empty($message)){$message = "Aucun message du roi pour l'instant";}
	echo "<fieldset>";
	echo "<legend>Propagande actuelle</legend>
	<div id='message_propagande' onclick=\"$('message_propagande_edit').show();$('message_propagande').hide();\">
	".$message."
	</div>";
	?>
	<div id='message_propagande_edit' style='display:none;'>
		<textarea name="message" id="messageid" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes($royaume->get_motk())); ?></textarea><br />
		<input type="button" value="Envoyer" onclick="envoiInfo('motk.php?message=' + $('messageid').value, 'message_confirm');envoiInfo('motk.php', 'contenu_jeu');" />
	</div>
	
	</fieldset>
	</div>
<?php
}
?>
