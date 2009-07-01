<?php
$root = '../';
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include($root.'inc/fp.php');

$joueur = recupperso($_SESSION['ID']);
if($joueur['grade'] == 'Roi' OR strtolower($joueur['nom']) == 'r')
{
	$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));
	$requete = "SELECT food, nombre_joueur FROM stat_jeu ORDER BY date DESC";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($row['nombre_joueur'] != 0) $food_necessaire = $row['food'] / $row['nombre_joueur'];
	else $food_necessaire = 0;
	
	$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);
	
	//Vérifie si le perso est mort
	verif_mort($joueur, 1);
	
	check_perso($joueur);
	
	$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
	$check = false;
	if(verif_ville($joueur['x'], $joueur['y']))
	{
		$check = true;
	}
	elseif($batiment = verif_batiment($joueur['x'], $joueur['y'], $R))
	{
		if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg') $check = true;
	}
	
	if($check)
	{
?>
<html>
<head>
	<title>Starshine-Online / Gestion du royaume</title>
	<link href="../css/texture.css" rel="stylesheet" type="text/css" />
	<link href="../css/texture_low.css" rel="stylesheet" type="text/css" />
	<link href="../css/interfacev2.css" rel="stylesheet" type="text/css" />
	<link href="../css/prototip.css" rel="stylesheet" type="text/css" />
	<link href="css/roi.css" rel="stylesheet" type="text/css" />
	<script src="../javascript/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="../javascript/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="../javascript/scriptaculous/prototip.js" type="text/javascript"></script>
	<script src="../javascript/fonction.js" type="text/javascript"></script>
	<script src="../javascript/overlib/overlib.js" type="text/javascript"></script>
	<script src="javascript/menu.js" type="text/javascript"></script>
	<meta http-equiv='content-type' content='text/html; charset=utf-8' />	
	<script type="text/javascript">
	window.onload = function()
	{
		new Draggable('popup', {handle: 'popup_menu'});
	}
	</script>
</head>
<body>
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

		<div id="perso_contenu">
		<?php include('perso_contenu.php'); ?>
		</div>
 

		<div id='perso_menu'>
			<ul>
				<li id='diplomatie' class='menu' onclick="menu_change('diplomatie');">Diplomatie</li>
				<li id='militaire' class='menu' onclick="menu_change('militaire');">Militaire</li>
				<li id='economie' class='menu' onclick="menu_change('economie');">Economie</li>
				<li id='divers' class='menu' onclick="menu_change('divers');">Divers</li>
			</ul>
			
		</div>
		
	</div>	
	



	<div id='menu'>
	<input type='hidden' id='menu_encours' value='diplomatie' />
	<div id='menu_details'>
		<div id='diplomatie_menu' style='display:none;'><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=diplomatie');">Diplomatie</span><span class='menu' onclick="affiche_page('telephone.php');">Téléphone rouge</span></div>
		
		
		<div id='militaire_menu' style='display:none;'><span class='menu' onclick="affiche_page('construction.php');">Drapeaux & batiments</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=carte');">Carte</span><span class='menu' onclick="affiche_page('gestion_groupe.php');">Gestion des groupes</span><span class='menu' onclick="affiche_page('gestion_bataille.php');">Gestion des batailles</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=boutique');">Boutique Militaire</span></div>
		
		
		<div id='economie_menu' style='display:none;'><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=bourse');">Bourse Inter Royaume</span><span class='menu'  onclick="affiche_page('gestion_royaume.php?direction=construction');">Construction de la ville</span><span class='menu' onclick="affiche_page('entretien.php');">Entretien</span><span class='menu' onclick="affiche_page('ressources.php');">Ressources</span><span class='menu' onclick="affiche_page('quete.php');">Gestion des quètes</span><span class='menu' onclick="affiche_page('taxe.php');">Gestion des taxes</span><span class='menu' onclick="affiche_page('mine.php');">Gestion des mines</span></div>
		
		<div id='divers_menu' style='display:none;'><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=criminel');">Criminels</span><span class='menu' onclick="affiche_page('motk.php');">Message du roi</span><span class='menu' onclick="affiche_page('propagande.php');">Propagande</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=stats');">Statistiques</span></div>
	</div>
	</div>
</div>
<div id='contenu_back'>
		<div id='message_confirm'></div>
	<div id="contenu_jeu">
&nbsp;
</div>
</div>
<?php
//Inclusion du bas de la page
		include($root.'bas.php');
	}
	else echo 'INTERDIT';
}
else
echo 'INTERDIT';
?>