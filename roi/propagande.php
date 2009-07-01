<?php
require('haut_roi.php');
include('../fonction/messagerie.inc.php');

if($joueur['rang_royaume'] != 6)
	echo '<p>Cheater</p>';
else if(array_key_exists('direction', $_GET) && $_GET['direction'] == 'propagande')
{
	$message = addslashes($_POST['message']);
	if ($message != '')
	{
		$requete = "UPDATE motk SET propagande = '".$message."' WHERE id_royaume = ".$R['ID'];
		if($req = $db->query($requete)) 
		{
			echo '<h6>Propagande bien modifiée !</h6>';
		}
		else echo('<h5>Erreur lors de l\'envoi du message</h5>');
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de message</h5>';
	}
}
else
{
	//Message actuel
	$requete = "SELECT propagande FROM motk WHERE id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$message = transform_texte($row[0]);
	echo "<fieldset>";
	echo "<legend>Propagande actuelle</legend>
	<div id='message_propagande' onclick=\"$('message_propagande_edit').show();$('message_propagande').hide();\">
	".$message."
	</div>";
	?>
	<div id='message_propagande_edit' style='display:none;'>
		<form method="post" action="propagande.php?direction=propagande" id="formPropagande">
		<textarea name="message" id="message" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes($row[0])); ?></textarea><br />
		<input type="button" name="btnSubmit" value="Envoyer" onclick="envoiFormulaire('formPropagande', 'conteneur');" />
	</form>
	</div>
	
	</fieldset>
	
<?php
}
?>