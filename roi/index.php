<?php
$root = '../';
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include($root.'inc/fp.php');

$joueur = recupperso($_SESSION['ID']);
if($joueur['grade'] == 'Roi' OR $joueur['nom'] == 'Mylok' OR strtolower($joueur['nom']) == 'minus')
{

$R = get_royaume_info($joueur['race'], $Trace[$joueur['race']]['numrace']);

//V�rifie si le perso est mort
verif_mort($joueur, 1);

check_perso($joueur);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
<html>
<head>
	<title>Starshine-Online / Gestion du royaume</title>
	<link href="../css/texture.css" rel="stylesheet" type="text/css" />
	<link href="../css/interface.css" rel="stylesheet" type="text/css" />
	<link href="../css/prototip.css" rel="stylesheet" type="text/css" />
	<link href="css/roi.css" rel="stylesheet" type="text/css" />
	<script src="../javascript/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="../javascript/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="../javascript/scriptaculous/prototip.js" type="text/javascript"></script>
	<script src="../javascript/fonction.js" type="text/javascript"></script>
	<script src="javascript/menu.js" type="text/javascript"></script>
</head>
<body>
<div id="top" style="width : 100%;">
		<ul id="menu">
			<li>
				<a href="#" onclick="showMenu(1)">Diplomatie</a>
				<ul id="smenu1" style="display : none">
					<li><a href="gestion_royaume.php?direction=diplomatie" onclick="return clickMenu(this);">Diplomatie</a>
					<li><a href="gestion_royaume.php?direction=telephone" onclick="return clickMenu(this);">T�l�phone rouge</a>
				</ul>
			</li>
			<li>
				<a href="#" onclick="showMenu(2)">Militaire</a>
				<ul id="smenu2" style="display : none">
					<li><a href="gestion_royaume.php?direction=drapeau" onclick="return clickMenu(this);">Drapeaux & batiments</a>
					<li><a href="gestion_royaume.php?direction=carte" onclick="return clickMenu(this);">Carte des constructions et habitants</a>
					<li><a href="index.php" onclick="refresh(this.href, 'conteneur');">Gestion des groupes</a></li>
					<li><a href="carte_strategique.php" onclick="return clickMenu(this);">Carte Strat�gique</a></li>
				</ul>
			</li>
			<li>
				<a href="#" onclick="showMenu(3)">Economie</a>
				<ul id="smenu3" style="display : none">
					<li><a href="gestion_royaume.php?direction=construction" onclick="return clickMenu(this);">Construction de la ville</a>
					<li><a href="gestion_royaume.php?direction=entretien" onclick="return clickMenu(this);">Entretien</a>
					<li><a href="gestion_royaume.php?direction=quete" onclick="return clickMenu(this);">Gestion des qu�tes</a>
					<li><a href="gestion_royaume.php?direction=taxe" onclick="return clickMenu(this);">Gestion des taxes</a>
				</ul>
			</li>
			<li>
				<a href="#" onclick="showMenu(4)">Divers</a>
				<ul id="smenu4" style="display : none">
					<li><a href="gestion_royaume.php?direction=criminel" onclick="return clickMenu(this);">Criminels</a>
					<li><a href="gestion_royaume.php?direction=motk" onclick="return clickMenu(this);">Message du roi</a>
					<li><a href="gestion_royaume.php?direction=propagande" onclick="return clickMenu(this);">Propagande</a>
					<li><a href="gestion_royaume.php?direction=stats" onclick="return clickMenu(this);">Statistiques</a>
				</ul>
			</li>
		</ul>
		<div id="loading" style="display : none;"> </div>
		<div id="infos">
	<?php
	$W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND statut = 'actif'";
	$W_req = $db->query($W_requete);
	$W_row = $db->read_row($W_req);
	$h = $W_row[0];
	$semaine = time() - (3600 * 24 * 7);
	$W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND level > 3 AND dernier_connexion > ".$semaine." AND statut = 'actif'";
	$W_req = $db->query($W_requete);
	$W_row = $db->read_row($W_req);
	$hta = $W_row[0];
	?>
			<strong>Stars du royaume : </strong><?php echo $R['star']; ?> / <strong>Taux de taxe</strong> : <?php echo $R['taxe_base']; ?>% / <strong>Habitants</strong> : <?php echo $h; ?> / <strong>Habitants tr�s actifs</strong> : <?php echo $hta; ?>
		</div>
</div>
<div style="clear : both; width : 100%;">
	<hr />
</div>
<div id="conteneur">
	<ul style="float :left;">
	<?php
	$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur['race']."'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		if($row['groupenom'] == '') $row['groupenom'] = '-----';
		?>
		<li id="groupe_<?php echo $row['groupeid']; ?>" onclick="refresh('infos_groupe.php?id_groupe=<?php echo $row['groupeid']; ?>', 'infos_groupe');"><?php echo $row['groupeid'].' - '.$row['groupenom']; ?></li>
		<?php
	}
	?>
	</ul>
	<div id="infos_groupe" style="float : right;">
		Cliquez sur un groupe pour obtenir des informations
	</div>
</div>
<?php
//Inclusion du bas de la page
include($root.'bas.php');
}
else
echo 'INTERDIT';
?>