<?php
require('haut_roi.php');
include('../fonction/messagerie.inc.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
else
{
	echo "<div id='motk'>";
	//Message actuel
	$requete = "SELECT message FROM motk WHERE id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$message = transform_texte($row[0]);
	echo "<fieldset>
	<legend>Message du roi actuel</legend>
		<div id='message_motk' onclick=\"$('message_motk_edit').show();$('message_motk').hide();\">
	".$message."</div>";
	
	?>
	<div id='message_motk_edit' style='display:none;'>
		<textarea name="message" id="message" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes($row[0])); ?></textarea><br />
		<input type="button" value="Envoyer" onclick="texte_update($('message').value, 'update_motk');" />
	</div>
	
	</fieldset>
	</div>
<?php
}
?>
