<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis ? jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
		Cr?ation acc?s au serveur test
	</div>
	<?php
	$cfg_apache["sql"]['host'] = "localhost";
	$cfg_apache["sql"]['user'] = "apache_auth";
	$cfg_apache["sql"]['pass'] = "7qG2sEr9qEAQ5sQq";
	$cfg_apache["sql"]['db'] = "apache_auth";
	$cfg_apache["sql"]['encoding'] = "utf8";
		
	$db_apache = new db($cfg_apache);
	
	if(array_key_exists('pseudo', $_POST))
	{
		echo 'Création en cours...<br />';
		$requete = "INSERT INTO clients(username, password, groups) VALUES('".$_POST['pseudo']."', ENCRYPT('".$_POST['pass']."'), 'dev');";
		$db_apache->query($requete);
		echo 'Création réussie<br />';
	}
	
	?>
	<form action="" method="post">
		Login : <input type="text" value="" id="pseudo" name="pseudo" /><br />
		Pass : <input type="text" value="" id="pass" name="pass" /><br />
		<input type="submit" value="Créer" />
	</form>
	<?php
	include('bas.php');
}
?>