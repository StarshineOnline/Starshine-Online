<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
{
	echo '<p>Cette page vous est interdit</p>';
	exit();
}
elseif(array_key_exists('message', $_GET))
{
	if ($_GET['message'] != '')
	{
		$royaume->set_propagande(sSQL($_GET['message']));
		echo '<h6>Propagande bien modifiée !</h6>';
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de message</h5>';
	}
}
	echo "<div id='propagande'>";
	//Message actuel
	$royaume->get_motk();
	$message = transform_texte($royaume->motk->get_propagande());

	$message = str_replace('[br]', '<br />', $message);
	//$message = $amessage.$message;
	$message = preg_replace("`\[img\]([^[]*)\[/img\]", '<img src=\\1 title="\\1">`', $message );
	$message = preg_replace("`\[b\]([^[]*)\[/b\]", '<strong>\\1</strong>`', $message );
	$message = preg_replace("`\[i\]([^[]*)\[/i\]", '<i>\\1</i>`', $message );
	$message = preg_replace("`\[url\]([^[]*)\[/url\]`", '<a href="\\1">\\1</a>', $message );
	$message = str_replace("[/color]", "</span>", $message);
	$regCouleur = "`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`";
	$message = preg_replace($regCouleur, "<span style=\"color: \\1\">", $message);

	if (empty($message)){$message = "Aucune propagande pour l'instant";}
	echo "<fieldset>";
	echo "<legend>Propagande actuelle</legend>
	<div id='message_propagande' onclick=\"$('#message_propagande_edit').show();$('#message_propagande').hide();\">
	".$message."
	</div>";
	?>
	<div id='message_propagande_edit' style='display:none;'>
        <form method="post" action="propagande.php?direction=propagande" id="formPropagande">
			<textarea name="message" id="message" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes($royaume->motk->get_propagande())); ?></textarea><br />
			<input type="button" value="Envoyer" onclick="envoiInfo('propagande.php?message='+encodeURIComponent($('#message').val()), 'contenu_jeu');" />
		</form>
	</div>
	
	</fieldset>
	</div>

