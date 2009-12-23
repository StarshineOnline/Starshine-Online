<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;

if (array_key_exists('nom', $_GET)) {
	//Inclusion du haut du document html
	include_once(root.'haut_ajax.php');

	$requete = "select nom,file from arenes where open = 1 and nom = '".
		sSQL($_GET[nom]).'\'';
	$req = $db->query($requete);
	if ($arene = $db->read_object($req)) {
		session_start();
		$_SESSION['arene_nom'] = $arene->nom;
		$_SESSION['arene_file'] = $arene->file;
		$_SESSION['arene_error'] = false;
	}
	else {
		$_SESSION['arene_error'] = "Arène invalide";
	}
		header('Location: show_arenes2.php');
}

//Inclusion du haut du document html
include_once(root.'haut.php');

if (array_key_exists('arene_error', $_SESSION) && $_SESSION['arene_error']) {
	echo "<h5>$_SESSION[arene_error]</h5>";
	exit(0);
}
if (array_key_exists('arene_nom', $_SESSION)) $nom = $_SESSION['arene_nom'];
if (array_key_exists('arene_file', $_SESSION)) $file = $_SESSION['arene_file'];

if (!isset($nom) || !isset($file)) {
	echo "<h5>Paramètres invalides !</h5>";
	var_dump($_SESSION);
	exit(0);	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
<title><?php echo $nom; ?></title>
<style type="text/css">
html {

	margin: 0;
	padding: 0;
	overflow-x: hidden;
	overflow-y: hidden;
	border: 0 none;
}

body {
 margin: 0;
 padding: 0;
 border: 0 none;
 overflow-x: hidden;
 overflow-y: hidden;
 height: 100%;
}

object {
 width: 100%;
 height: 800px;
}
</style>
</head>
<body>
<object data="arenes/<?php echo $file; ?>" type="text/xml" />
</body>
</html>