<?php
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
$interface_v2 = true;
include_once(root.'haut.php');
?>
<script type="text/javascript">
window.onload = function()
{
	<?php if($_COOKIE['dernier_affichage_popup'] < (time() - 3600)) echo 'affichePopUp(\'message_accueil.php\');'; ?>
}
</script>
<?php
if(array_key_exists('ID', $_SESSION) && !empty($_SESSION['ID']))
	$joueur = new perso($_SESSION['ID']);
else
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	exit();
}

//Si c'est pour entrer dans un donjon
if(array_key_exists('donjon_id', $_GET))
{
	$id = $_GET['donjon_id'];
	
	$requete = "SELECT x, y, x_donjon, y_donjon FROM donjon WHERE id = ".$id;
	$req = $db->query($requete);
	
	$row = $db->read_assoc($req);
	//sortie
	if(array_key_exists('type', $_GET))
	{
		if($joueur->get_x() == $row['x_donjon'] AND $joueur->get_y() == $row['y_donjon'])
		{
			$requete = "UPDATE perso SET x = ".$row['x'].", y = ".$row['y']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
	//Entrée
	else
	{
		if($joueur->get_x() == $row['x'] AND $joueur->get_y() == $row['y'])
		{
			$requete = "UPDATE perso SET x = ".$row['x_donjon'].", y = ".$row['y_donjon']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
	$joueur = new perso($_SESSION['ID']);
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$joueur->check_perso();

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
?>

<div id="conteneur_back">
<div id="conteneur">

<div id="mask" style='display:none;'></div>
<div id="popup" style='display:none;'>
	<div id="popup_menu"><span class='fermer' title='Fermer le popup' onclick="fermePopUp(); return false;">&nbsp;</span></div>
	<div id="popup_marge">
		<div id="popup_content"></div>
	</div>
</div>
<div id="loading" style='display:none'></div>
<div id="loading_information" style='display:none'></div>
	<div id="perso">
		
		<?php
		require_once('infoperso.php');
		?>
		
	</div>
	<div id='menu'>
		<?php
$arene = $joueur->in_arene();
$time = time();
if ($arene) $time += $arene->decal;
echo "<div id='menu_date'><img src='image/interface/".moment_jour().
  ".png' alt='".moment_jour()."' title='".moment_jour()." - ".date_sso($time).
  "' />".moment_jour();?>
	</div>

	<input type="hidden" id="menu_encours" value="lejeu" />
	<div id='menu_details'>
		<div id='lejeu_menu' style='display:none;'><span class='menu' onclick="affichePopUp('diplomatie.php');">Diplomatie</span><span class='menu' onclick="affichePopUp('classement.php');">Classement</span><span class='menu' onclick="affichePopUp('stats2.php?graph=carte_royaume');">Statistiques</span><span class='menu' onclick="affichePopUp('message_accueil.php?affiche=all');">Message d'Accueil</span><span class='menu' onclick="affichePopUp('option.php');">Options</span></div>
		<div id='starshine_menu' style='display:none;'><span class='menu' onclick="affichePopUp('liste_monstre.php');">Bestiaire</span><span class='menu' onclick="affichePopUp('background.php');">Background</span><span class='menu' onclick="affichePopUp('royaume.php');">Carte</span>
		<?php //echo "<span class='menu' onclick=\"affichePopUp('beta_test.php');\">Beta</span>"; ?>
		</div>
		<div id='communaute_menu' style='display:none;'><span class='menu'><a href="http://forum.starshine-online.com">Forum</a></span><span class='menu'><a href="http://wiki.starshine-online.com/">Wiki</a></span><span class='menu'><a href="http://bug.starshine-online.com/">Signaler un bug</a></span><span class='menu' onclick="affichePopUp('acces_chat.php');">Tchat</span><span class='menu' onclick="affichePopUp('boutique_sso.php');">Boutique SSO</span></div>
	</div>
	<div id='menu_deco'>
		<span class="fermer" title='Se déconnecter' onclick="if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };">&nbsp;</span>
	</div>
</div>
<div id='contenu_back'>
	<div id="contenu_jeu">
		<div id="centre">
		<?php
		//Génération de la carte apparaissant au centre.
		//Si coordonées supérieur à 100 alors c'est un donjon
		if(is_donjon($joueur->get_x(), $joueur->get_y()))
		{
			include_once(root.'donjon.php');
		}
		else include_once(root.'map2.php');
		?>
		</div>
		<?php include_once(root.'menu_carte.php');?>
		<div id="information">
				<h2>Information</h2>
		<?php
		
		$case = convert_in_pos($joueur->get_x(), $joueur->get_y());
		if(array_key_exists('page_info', $_GET)) $page_info = $_GET['page_info']; else $page_info = 'informationcase.php';
		{//-- Javascript
			echo "<script type='text/javascript'>
					// <![CDATA[\n";
			{//-- envoiInfo
				echo "envoiInfo('".$page_info."?case=".$case."', 'information');";
			}
			echo "	// ]]>
				  </script>";
		}

		echo "</div>
	</div>
	
</div>
</div>";
if (file_exists(root.'revision.inc')) {
	echo "\n<div style=\"font-size: 0.5em; text-align: right; padding-right: 15px\">";
	include_once(root.'revision.inc');
	echo "</div>\n";
}

//Inclusion du bas de la page
include_once(root.'bas.php');
?>
