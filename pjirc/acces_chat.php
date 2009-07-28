<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$root = '../';
include_once(root.'../haut.php');
include_once(root.'../haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte complète, l\'expérience acquise grâce à l\'alpha m\'a permis de voir les gros problèmes qui pourraient se poser.<br />
	Je vais donc travailler sur la béta.<br />';
}
else
{
	include_once(root.'../menu.php');

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
		<applet code=IRCApplet.class archive="irc.jar,pixx.jar" width=640 height=400>
		<param name="CABINETS" value="irc.cab, securedirc.cab, pixx.cab">
		<param name="nick" value="<?php echo $_POST['nick']; ?>">
		<param name="name" value="Starshine User">
		<param name="host" value="irc.quakenet.org">
		<param name="gui" value="pixx">
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
	include_once(root.'../menu_d.php');
	?>
</div>
</div>
<?php
}
include_once(root.'../bas.php');
?>