<?php
require('haut_roi.php');

if(array_key_exists('direction', $_GET) && $_GET['direction'] == 'motk2')
{
	$message = addslashes($_GET['message']);
	if ($message != '')
	{
		$requete = "UPDATE motk SET message = '".$message."', date = ".time()." WHERE id_royaume = ".$R['ID'];
		if($req = $db->query($requete)) 
		{
			echo 'Message du roi bien modifié !<br />';
		}
		else echo('Erreur lors de l\'envoi du message');
	}
	else
	{
		echo 'Vous n\'avez pas saisi de message';
	}
}
else
{
	//Message actuel
	$requete = "SELECT message FROM motk WHERE id_royaume = ".$R['ID'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$message = htmlspecialchars(stripslashes($row[0]));
	$message1 = str_replace('[br]', '<br />', $message);
	$message2 = str_replace('[br]', "\n", $message);
	echo '<h3>Message du roi actuel</h3>
	'.$message1.'<br />
	<h3>Modifier</h3>';
	?>
	<form method="post" action="javascript:message = document.getElementById('message').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('motk.php?poscase=<?php echo $W_case; ?>&amp;direction=motk2&amp;message=' + message, 'carte');">
	<?php
	echo '
	    <textarea name="message" id="message" cols="45" rows="12">'.$message2.'</textarea><br />
		<input type="submit" name="btnSubmit" value="Envoyer" />
	</form>';
}
?>