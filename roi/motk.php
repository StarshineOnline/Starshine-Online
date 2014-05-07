<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require_once('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');


if($joueur->get_rang_royaume() != 6)
	echo '<p>Cette page vous est interdite</p>';
	elseif(array_key_exists('message', $_GET))
{
	if ($_GET['message'] != '')
	{
		$royaume->set_motk(sSQL($_GET['message']));
		echo '<h6>Message du roi bien modifié !</h6>';
	}
	else
	{
		echo '<h5>Vous n\'avez pas saisi de message</h5>';
	}
}
if($joueur->get_rang_royaume() != 6)
	echo '<p>Cette page vous est interdite</p>';
	else
	{
	echo "<div id='propagande'>";
	//Message actuel
	$royaume->get_motk();
	$message = transform_texte($royaume->motk->get_message());
	$message = str_replace('[br]', '<br />', $message);
	//$message = $amessage.$message;
	$message = preg_replace("`\[img\]([^[]*)\[/img\]`i", '<img src=\\1 title="\\1">', $message );
	$message = preg_replace("`\[b\]([^[]*)\[/b\]`i", '<strong>\\1</strong>', $message );
	$message = preg_replace("`\[i\]([^[]*)\[/i\]`i", '<i>\\1</i>', $message );
	$message = preg_replace("`\[url\]([^[]*)\[/url\]`i", '<a href="\\1">\\1</a>', $message );
	$message = str_ireplace("[/color]", "</span>", $message);
	$regCouleur = "`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`i";
	$message = preg_replace($regCouleur, "<span style=\"color: \\1\">", $message);
	
	if (empty($message)){$message = "Aucun message du roi pour l'instant";}
	echo "<fieldset>";
	echo "<legend>Message du roi actuel</legend>
	<div id='message_roi' onclick=\"$('#message_roi_edit').show();$('#message_roi').hide();\">
	".$message."
	</div>";
	?>
	<div id='message_roi_edit' style='display:none;'>
		<textarea name="message" id="messageid" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes(($royaume->motk->get_message()))); ?></textarea><br />
		<input type="button" value="Envoyer" onclick="envoiInfo('motk.php?message=' + encodeURIComponent($('#messageid').val()), 'contenu_jeu');" />
	</div>
	
	</fieldset>
	</div>
<? } ?>
