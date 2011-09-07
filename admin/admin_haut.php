<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

include_once(root.'inc/fp.php');
if(isset($_SESSION['nom']) || $admin)
{
}
elseif(!array_key_exists('log', $_POST) && strpos($_SERVER['SCRIPT_NAME'], '/index.php') === false) // === car 0 == false
{
  $s = strpos($_SERVER['SCRIPT_NAME'], '/index.php');
  header("X-strpos: $s");
	header("Location: index.php");
}

/// @var juste pour empêcher Doxygen de bugger

$identification = new identification();

$erreur_login = '';
//Connexion du joueur
if((isset($_POST['log']) OR isset($_COOKIE['nom'])) AND !array_key_exists('nom', $_SESSION))
{
	if(isset($_POST['log']))
	{
		$nom = $_POST['nom'];
		$password = md5($_POST['password']);
		$_SESSION['password'] = $_POST['password'];
	}
	else
	{
		$nom = $_COOKIE['nom'];
		$password = $_COOKIE['password'];
		if (!isset($_SESSION['password'])) $_SESSION['password'] = '';
	}
	if(isset($_POST['auto_login']) && $_POST['auto_login'] == 'Ok') $autologin = true; else $autologin = false;
	$check = $identification->connexion($nom, $password, $autologin);
}
//Déconnexion du joueur
if (isset($_GET['deco']) AND !isset($_POST['log']))
{
	$identification->deconnexion();
}
$journal = '';

if(array_key_exists('nom', $_SESSION)) $joueur = new perso($_SESSION['ID']);
if(!isset($root)) $root = '';
//check_undead_players();
if (isset($site) && $site)
{
	print_head("css:./css/site.css;script:./javascript/jquery/jquery-1.3.2.min.js~./javascript/jquery/jquery-ui-1.7.2.custom.min.js~./javascript/fonction.js~./javascript/site.js;title:StarShine, le jeu qu'il tient ses plannings !");
}
else
{
	if (isset($interface_v2) && $interface_v2)
	{
		print_head("css:./css/texture.css~./css/texture_low.css~./css/interfacev2.css;script:./javascript/jquery/jquery-1.3.2.min.js~./javascript/jquery/jquery-ui-1.7.2.custom.min.js~./javascript/fonction.js~./javascript/overlib/overlib.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
	elseif (isset($interface_3D) && $interface_3D)
	{
		print_head("css:./css/texture3D.css~./css/interface3D.css~./css/prototip.css~./css/site.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
	elseif (isset($old_admin) && $old_admin)
  { // Le mappeur marche pas avec jquery 1.4
		print_head("css:../css/texture.css~../css/texture_low.css~../css/interfacev2.css~../css/admin.css~../css/prototip.css;script:../javascript/fonction.js~../javascript/overlib/overlib.js~../javascript/scriptaculous/prototype.js~../javascript/scriptaculous/scriptaculous.js~../javascript/scriptaculous/prototip.js;title:StarShine Admin");
	}
	elseif (isset($admin) && $admin)
	{
		if (!isset($ajax))
      print_head("css:../css/texture.css~../css/texture_low.css~../css/interfacev2.css~../css/admin.css~../css/prototip.css~../css/jquery-ui-1.7.3.custom.css;script:../javascript/jquery/jquery-1.4.2.min.js~../javascript/jquery/jquery-ui-1.7.2.custom.min.js~../javascript/fonction.js~../javascript/jquery/jquery.cluetip.min.js~../javascript/jquery/jquery.dataTables.min.js~admin.js~../javascript/jquery/jquery.ui.datepicker-fr.js;title:StarShine Admin");
	}
}
$fin = getmicrotime();

//echo 'TEMPS : '.($fin - $debut);
?>
