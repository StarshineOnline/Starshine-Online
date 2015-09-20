<?php
/// @deprecated (?)
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$possedeUnPerso = false;
if( isset($_SESSION['nom']) )
{
	$possedeUnPerso = true;
}
$estUnUtilisateur = false;
if( isset($_SESSION['pseudo']) )
{
	$estUnUtilisateur = true;
}

if(!$site && !$possedeUnPerso && !$estUnUtilisateur && !array_key_exists('log', $_POST) && strpos($_SERVER['SCRIPT_NAME'], '/index.php') === false) // === car 0 == false
{
	$s = strpos($_SERVER['SCRIPT_NAME'], '/index.php');
	header("X-strpos: $s");
	header("Location: index.php");
}

/// @var juste pour empêcher Doxygen de bugger

$identification = new identification();

$erreur_login = '';
//Déconnexion du joueur
if (isset($_GET['deco']) AND !isset($_POST['log']))
{
	$identification->deconnexion();
	$possedeUnPerso = false;
	$estUnUtilisateur = false;
}
//Connexion du joueur
elseif( (isset($_POST['log']) OR isset($_COOKIE['nom'])) AND !$possedeUnPerso )
{
	if(isset($_POST['log']))
	{
		$nom = $_POST['nom'];
		$password = md5($_POST['password']);
		$header = substr($_POST['header'], 1);
	}
	else
	{
		$nom = $_COOKIE['nom'];
		$password = $_COOKIE['password'];
		$header = '';
	}
	$autologin = isset($_POST['auto_login']) && $_POST['auto_login'] == 'Ok';
	$estConnexionReussie = $identification->connexion($nom, $password, $autologin, false, $header);
	if(isset($_SESSION['nom']))
	{
		$possedeUnPerso = true;
	}
	if( isset($_SESSION['pseudo']) )
	{
		$estUnUtilisateur = true;
	}
	if($estConnexionReussie)
	{
		?>
		<script language="javascript" type="text/javascript">
		<!--
		window.location.replace("interface.php");
		-->
		</script>
		<?php
	}
}
$journal = '';

if($possedeUnPerso)
	$joueur = new perso($_SESSION['ID']);
if(!isset($root))
	$root = '';

if( $interf_obj !== true )
{
  if (isset($site) && $site)
  {
  	print_head("css:./css/site.css~./css/jquery.lightbox-0.5.css~./css/jquery-ui-1.7.3.custom.css;script:./javascript/jquery/jquery-2.0.2.min.js~./css/jquery/jquery.ui.all.css~./javascript/jquery/jquery.lightbox-0.5.min.js~./javascript/jquery/jquery.dataTables.min.js~./javascript/fonction.js~./javascript/site.js;title:StarShine, le jeu qu'il tient ses plannings !");
  }
  else
  {
  	if ($interface_v2)
  	{
  		$html 			= new html();
  		$html->title	= "Starshine, un jeu de l'espace !";
  		$html->css		= array('./css/texture.css','./css/bootstrap.css','./css/bootstrap-theme.min.css','./css/texture_low.css','./css/interfacev3.css','./css/jquery.ui.all.css');
  		$html->scripts = array('./javascript/jquery/jquery-2.0.2.min.js','./javascript/bootstrap.js','./javascript/jquery/jquery-ui-1.10.3.custom.min.js','./javascript/jquery/jquery.dataTables.min.js','./javascript/fonction.js','./javascript/overlib/overlib.js','./javascript/jquery/jquery.hoverIntent.minified.js','./javascript/jquery/jquery.cluetip.min.js','./javascript/jquery/atooltip.min.jquery.js');
  		$html->header();
  	}
  	elseif($admin)
  	{
  		print_head("css:../css/texture.css~../css/texture_low.css~../css/interfacev2.css;script:../javascript/fonction.js~../javascript/overlib/overlib.js~../javascript/jquery/jquery-1.5.1.min.js~./javascript/jquery/jquery-ui-1.8.10.custom.min.js;title:StarShine, le jeu qu'il tient ses plannings !");
  	}
  }
}
?>
