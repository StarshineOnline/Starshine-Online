<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
if(isset($_SESSION['nom']))
{
}
elseif(!array_key_exists('log', $_POST) && !strpos($_SERVER['SCRIPT_NAME'], '/index.php'))
{
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
	if($check)
	{
		?>
		<script language="javascript" type="text/javascript">
		<!--
		window.location.replace("jeu2.php");
		-->
		</script>
		<?php
	}
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
	print_head("css:./css/site.css~./css/lightbox.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js?load=effects,builder~./javascript/scriptaculous/lightbox.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
}
else
{
	if ($interface_v2)
	{
		print_head("css:./css/texture.css~./css/texture_low.css~./css/interfacev2.css~./css/prototip.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js?load=effects,builder,dragdrop~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
	elseif($interface_3D)
	{
		print_head("css:./css/texture3D.css~./css/interface3D.css~./css/prototip.css~./css/site.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
	else
	{
		print_head("css:./css/texture.css~./css/interface.css~./css/prototip.css~./css/site.css;script:./javascript/fonction.js~./javascript/overlib/overlib.js~./javascript/scriptaculous/prototype.js~./javascript/scriptaculous/scriptaculous.js~./javascript/scriptaculous/prototip.js;title:StarShine, le jeu qu'il tient ses plannings !");
	}
}
$fin = getmicrotime();

//echo 'TEMPS : '.($fin - $debut);
?>