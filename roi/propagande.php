<?php
require('haut_roi.php');
include('../fonction/messagerie.inc.php');

if($joueur['rang_royaume'] != 6)
	echo '<p>Cheater</p>';
else
{
	echo "<div id='propagande'>";
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
		<textarea name="message" id="message" cols="90" rows="12"><?php echo htmlspecialchars(stripslashes($row[0])); ?></textarea><br />
		<input type="button" value="Envoyer" onclick="texte_update($('message').value, 'update_propagande');" />
	</div>
	
	</fieldset>
	</div>
<?php
}
?>