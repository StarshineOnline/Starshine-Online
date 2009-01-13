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
<div id="popup" style='display:none;'><div id="popup_menu"><a href="" onclick="fermePopUp(); return false;">Fermer</a></div><div id="popup_content"></div></div>
<div id="loading" style='display:none'></div>
<div id="loading_information" style='display:none'></div>
	<div id="perso">
		<div id="perso_contenu">
		<?php
		require_once('infoperso.php');
		?>
		</div>
		<div id='perso_menu'>
			<ul>
				<li id='lejeu' class='menu' onclick="menu_change('lejeu');">Le jeu</li>
				<li id='communaute' class='menu' onclick="menu_change('communaute');">Communauté</li>
				<li id='starshine' class='menu' onclick="menu_change('starshine');">Starshine</li>
			</ul>
			<input type='hidden' id='menu_encours' />
		</div>
	</div>
<div id='menu'>
	<div id='menu_details'>
		<div id='lejeu_menu' style='display:none;'><span class='menu' onclick="affichePopUp('diplomatie.php');">Diplomatie</span><span class='menu'>Classement</span><span class='menu'>Statistique</span></div>
		<div id='starshine_menu' style='display:none;'><span class='menu'>Bestiaire</span><span class='menu'>Background</span><span class='menu'>Carte</span></div>
		<div id='communaute_menu' style='display:none;'><span class='menu'>Forum</span><span class='menu'>Wiki</span><span class='menu'>Tchat</span></div>
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