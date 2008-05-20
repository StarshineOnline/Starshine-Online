<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte complète, l\'expérience acquise grâce à l\'alpha m\'a permis de voir les gros problèmes qui pourraient se poser.<br />
	Je vais donc travailler sur la béta.<br />';
}
else
{
	include('menu.php');

$requete = 'SELECT * FROM monstre ORDER BY level ASC, xp ASC';
$req = $db->query($requete);

?>
<div id="contenu">
	<div id="centre2">
	<div class="titre">
		Tchat de Starshine-Online
	</div>
			<?php
		//Affichage du chat
		if(array_key_exists('chat', $_POST))
		{
		?>
		<applet codebase="/pjirc/" code=IRCApplet.class archive="irc.jar,pixx.jar" width=640 height=400>
		<param name="CABINETS" value="irc.cab, securedirc.cab, pixx.cab">
		<param name="nick" value="<?php echo $_POST['nick']; ?>">
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
			Quel sera votre pseudo : <input type="text" name="nick" value="" />
			<input type="submit" value="Se connecter" name="chat" />
		</form>
		<?php
		}
		?>
	</div>
	<?php
	include('menu_d.php');
	?>
</div>
</div>
<?php
}
include('bas.php');
?>