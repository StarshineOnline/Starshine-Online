<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');

$joueur = new perso($_SESSION['ID']);
if($joueur->get_grade()->get_id() == 6)
{
	$date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));
	$requete = "SELECT food, nombre_joueur FROM stat_jeu ORDER BY date DESC";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($row['nombre_joueur'] != 0) $food_necessaire = $row['food'] / $row['nombre_joueur'];
	else $food_necessaire = 0;
	
	$royaume = new royaume($Trace[$joueur->get_race()]['numrace']);
	
	//Vérifie si le perso est mort
	verif_mort($joueur, 1);
	$joueur->check_perso();
	
	$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
	$check = false;
	if(verif_ville($joueur->get_x(), $joueur->get_y()))
	{
		$check = true;
	}
	elseif($batiment = verif_batiment($joueur->get_x(), $joueur->get_y(), $royaume->get_id()))
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
	<?php
	echo "<script type='text/javascript'>
			// <![CDATA[\n";
	{ // Validation d'une bataille
	echo "		
	function validation_bataille()
	{		
		data = 'nom=' + $('nom').value + '&description=' + $('description').value + '&x=' + $('x').value + '&y=' + $('y').value + '&new2' ";
		$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			echo "
			if ($('groupe_".$row['groupeid']."').value == 1)
			{
				data = data+'&groupe_".$row['groupeid']."=1'
			}
			
			";
		}
		 
		echo "envoiInfo('gestion_bataille_new.php?'+data, 'message_confirm');
	}
	";
	}
	echo "	// ]]>
		  </script>";
?>
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
		<?php include_once(root.'roi/perso_contenu.php'); ?>
		</div>
 

		<div id='perso_menu'>
			<ul>
				<li id='ressource' class='menu' onclick="menu_change('ressource');">Ressources</li>
				<li id='economie' class='menu' onclick="menu_change('economie');">Economie</li>
				<li id='militaire' class='menu' onclick="menu_change('militaire');">Militaire</li>
				<li id='communication' class='menu' onclick="menu_change('communication');">Communication</li>
				<li id='divers' class='menu' onclick="menu_change('divers');">Divers</li>
			</ul>
			
		</div>
		
	</div>	
	



	<div id='menu'>
	<input type='hidden' id='menu_encours' value='ressource' />
	<div id='menu_details'>
		<div id='ressource_menu' style='display:none;'><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=bourse');">Bourse Inter Royaume</span><span class='menu' onclick="affiche_page('ressources.php');">Ressources</span><span class='menu' onclick="affiche_page('mine.php');">Gestion des mines</span></div>
		<div id='economie_menu' style='display:none;'><span class='menu'  onclick="affiche_page('gestion_royaume.php?direction=construction');">Construction de la ville</span><span class='menu' onclick="affiche_page('entretien.php');">Entretien</span><span class='menu' onclick="affiche_page('taxe.php');">Gestion des taxes</span></div>
		<div id='militaire_menu' style='display:none;'><span class='menu' onclick="affiche_page('construction.php');">Drapeaux & batiments</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=carte');">Carte</span><span class='menu' onclick="affiche_page('gestion_groupe.php');">Gestion des groupes</span><span class='menu' onclick="affiche_page('gestion_bataille.php');">Gestion des batailles</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=boutique');">Boutique Militaire</span></div>
		<div id='communication_menu' style='display:none;'><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=diplomatie');">Diplomatie</span><span class='menu' onclick="affiche_page('motk.php');">Message du roi</span><span class='menu' onclick="affiche_page('propagande.php');">Propagande</span></div>
		<div id='divers_menu' style='display:none;'><span class='menu' onclick="affiche_page('quete.php');">Gestion des quètes</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=criminel');">Criminels</span><span class='menu' onclick="affiche_page('gestion_royaume.php?direction=stats');">Statistiques</span><span class='menu' onclick="affiche_page('point_victoire.php');">Points de victoires</span></div>
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
		include_once(root.$root.'bas.php');
	}
	else echo 'INTERDIT';
}
else
echo 'INTERDIT';
?>
