<?php 
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
if (isset($_SESSION['nom']))
{
	if (isset($api_login) && $api_login) { $check = true; }
}
elseif($connexion)
{
	header("Location: index.php");
}

$identification = new identification();

$erreur_login = '';
//Connexion du joueur
if((isset($_POST['log']) OR isset($_COOKIE['nom'])) AND !array_key_exists('nom', $_SESSION))
{
	if(isset($_POST['log']))
	{
		$nom = $_POST['nom'];
		$password = md5($_POST['password']);
	}
	else
	{
		$nom = $_COOKIE['nom'];
		$password = $_COOKIE['password'];
	}
	if($_POST['auto_login'] == 'Ok') $autologin = true; else $autologin = false;
	$check = $identification->connexion($nom, $password, $autologin);
}
// Login API -- seules les pages définissant $api_login à true le permettent
if (isset($api_login) && $api_login && !array_key_exists('nom', $_SESSION) && 
		array_key_exists('login', $_REQUEST) && array_key_exists('key', $_REQUEST))
{
	$check = $identification->connexion($_REQUEST['login'], $_REQUEST['key'],
																			false, true);
}

if(!isset($root)) $root = '';
if ($check) check_undead_players();
?>
