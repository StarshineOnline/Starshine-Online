<?php

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
$interface_v2 = true;
include('haut.php');
?>
<script type="text/javascript">
window.onload = function()
{
	new Draggable('popup', {handle: 'popup_menu'});
	<?php if($_COOKIE['dernier_affichage_popup'] < (time() - 3600)) echo 'affichePopUp(\'message_accueil.php\');'; ?>
}
</script>
<?php
$joueur = recupperso($_SESSION['ID']);

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
		if($joueur['x'] == $row['x_donjon'] AND $joueur['y'] == $row['y_donjon'])
		{
			$requete = "UPDATE perso SET x = ".$row['x'].", y = ".$row['y']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
	//Entrée
	else
	{
		if($joueur['x'] == $row['x'] AND $joueur['y'] == $row['y'])
		{
			$requete = "UPDATE perso SET x = ".$row['x_donjon'].", y = ".$row['y_donjon']." WHERE ID = ".$_SESSION['ID'];
			$db->query($requete);
		}
	}
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

check_perso($joueur);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>

<div id="conteneur_back">
<div id="conteneur">

<div id="mask" style='display:none;'></div>
<div id="popup" style='display:none;'><div id="popup_menu"><span class='fermer' title='Fermer le popup' onclick="fermePopUp(); return false;"></span></div><div id="popup_marge"><div id="popup_content"></div></div></div>
<div id="loading" style='display:none'></div>
<div id="loading_information" style='display:none'></div>
	<div id="perso">
		
		<?php
		require_once('infoperso.php');
		?>
		
	</div>
	<div id='menu'>
		<?php echo "<div id='menu_date'><img src='image/interface/".moment_jour().".png' alt='".moment_jour()."' title='".moment_jour()."' >".moment_jour();?>
	</div>

	<div id='menu_details'>
		<div id='lejeu_menu' style='display:none;'><span class='menu' onclick="affichePopUp('diplomatie.php');">Diplomatie</span><span class='menu' onclick="affichePopUp('classement.php');">Classement</span><span class='menu' onclick="affichePopUp('stats2.php?historique=y&annee=2009&mois=01&jour=14&image=carte_densite_mob.png');">Statistiques</span><span class='menu' onclick="affichePopUp('message_accueil.php');">Message d'Accueil</span><span class='menu' onclick="affichePopUp('repartir_craft.php');">Craft</span><span class='menu' onclick="affichePopUp('beta_test.php');">Beta Test</span></div>
		<div id='starshine_menu' style='display:none;'><span class='menu' onclick="affichePopUp('liste_monstre.php');">Bestiaire</span><span class='menu' onclick="affichePopUp('background.php');">Background</span><span class='menu' onclick="affichePopUp('royaume.php');">Carte</span></div>
		<div id='communaute_menu' style='display:none;'><span class='menu'><a href="http://forum.starshinebox.info">Forum</a></span><span class='menu'><a href="http://wiki.starshine-online.com/">Wiki</a></span><span class='menu' onclick="affichePopUp('acces_chat.php');">Tchat</span></div>
	</div>
	<div id='menu_deco'>
		<span class="fermer" title='Se déconnecter' onclick="if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };"></span>
	</div>
</div>
<div id='contenu_back'>
	<div id="contenu_jeu">
		<div id="centre">
		<?php
		//Génération de la carte apparaissant au centre.
		//Si coordonées supérieur à 100 alors c'est un donjon
		if(is_donjon($joueur['x'], $joueur['y']))
		{
			include('donjon.php');
		}
		else include('map2.php');
		?>
		</div>
		<?php include('menu_carte.php');?>
		<div id="information">
				<h2>Information</h2>
		<?php
		
		$case = convert_in_pos($joueur['x'], $joueur['y']);
		if(array_key_exists('page_info', $_GET)) $page_info = $_GET['page_info']; else $page_info = 'informationcase.php';
		?>
		<img src="image/pixel.gif" onLoad="envoiInfo('<?php echo $page_info; ?>?case=<?php echo $case; ?>', 'information');" />
		</div>
	</div>
	
</div>
</div>
</div>
<?php
//Inclusion du bas de la page
include('bas.php');
?>