<?php
	include('inc/fp.php');
	$joueur = recupperso($_SESSION['ID']);
	//Affichage du chat
	if(array_key_exists('nick', $_GET))
	{
	?>
	<applet codebase="/pjirc/" code=IRCApplet.class archive="irc.jar,pixx.jar" width=640 height=400>
	<param name="CABINETS" value="irc.cab, securedirc.cab, pixx.cab">
	<param name="nick" value="<?php echo $_GET['nick']; ?>">
	<param name="name" value="Starshine User">
	<param name="host" value="irc.quakenet.org">
	<param name="gui" value="pixx">
	<param name="pixx:language" value="pixx-french">
	<param name="pixx:timestamp" value="true">
	<param name="highlight" value="true">
	<param name="pixx:highlightnick" value="true">
	<param name="style:smileys" value="true">
	<param name="command1" value="/join #starshine-online">
	</applet>
	<?php
	}
	//Formulaire chat
	else
	{
	?>
	<form method="post" action="acces_chat.php">
		Quel sera votre pseudo : <input type="text" name="nick" id="nick" value="<?php echo $joueur['nom']; ?>" />
		<input type="button" value="Se connecter" name="chat" onclick="envoiInfo('acces_chat.php?nick=' + $('nick').value, 'popup_content');" />
	</form>
	<?php
	}
?>