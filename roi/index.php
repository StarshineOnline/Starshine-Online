<?php
$root = '../';
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include($root.'inc/fp.php');

$joueur = recupperso($_SESSION['ID']);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

check_perso($joueur);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
<html>
<head>
	<link href="../css/texture.css" rel="stylesheet" type="text/css" />
	<link href="../css/interface.css" rel="stylesheet" type="text/css" />
	<script src="../javascript/scriptaculous/prototype.js" type="text/javascript"></script>
	<script src="../javascript/scriptaculous/scriptaculous.js" type="text/javascript"></script>
	<script src="javascript/menu.js" type="text/javascript"></script>
</head>
<body>
<div id="menu">
		<dl id="menu">
			<dt class="smenu" id="a1"><span>Diplomatie</span></dt>
			<dd id="smenu1">
				<ul>
					<li><a href="gestion_royaume.php?direction=diplomatie" onclick="new Ajax.Updater('conteneur', this.href); return false;">Diplomatie</a>
					<li><a href="gestion_royaume.php?direction=telephone" onclick="new Ajax.Updater('conteneur', this.href); return false;">Téléphone rouge</a>
				</ul>
			</dd>
			<dt class="smenu" id="a2"><span>Militaire</span></dt>
			<dd id="smenu2">
				<ul>
					<li><a href="gestion_royaume.php?direction=drapeau" onclick="new Ajax.Updater('conteneur', this.href); return false;">Drapeaux & batiments</a>
					<li><a href="gestion_royaume.php?direction=carte" onclick="new Ajax.Updater('conteneur', this.href); return false;">Carte des constructions et habitants</a>
					<li><a href="index.php" onclick="new Ajax.Updater('conteneur', this.href);">Gestion des groupes</a></li>
				</ul>
			</dd>	
			<dt class="smenu" id="a3"><span>Economie</span></dt>
			<dd id="smenu3">
				<ul>
					<li><a href="gestion_royaume.php?direction=construction" onclick="new Ajax.Updater('conteneur', this.href); return false;">Construction de la ville</a>
					<li><a href="gestion_royaume.php?direction=entretien" onclick="new Ajax.Updater('conteneur', this.href); return false;">Entretien</a>
					<li><a href="gestion_royaume.php?direction=quete" onclick="new Ajax.Updater('conteneur', this.href); return false;">Gestion des quètes</a>
					<li><a href="gestion_royaume.php?direction=taxe" onclick="new Ajax.Updater('conteneur', this.href); return false;">Gestion des taxes</a>
				</ul>
			</dd>
			<dt class="smenu" id="a4"><span>Divers</span></dt>
			<dd id="smenu4">
				<ul>
					<li><a href="gestion_royaume.php?direction=criminel" onclick="new Ajax.Updater('conteneur', this.href); return false;">Criminels</a>
					<li><a href="gestion_royaume.php?direction=motk" onclick="new Ajax.Updater('conteneur', this.href); return false;">Message du roi</a>
					<li><a href="gestion_royaume.php?direction=propagande" onclick="new Ajax.Updater('conteneur', this.href); return false;">Propagande</a>
					<li><a href="gestion_royaume.php?direction=stats" onclick="new Ajax.Updater('conteneur', this.href); return false;">Statistiques</a>
				</ul>
			</dd>
		</dl>
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
		<li id="groupe_<?php echo $row['groupeid']; ?>" onclick="new Ajax.Updater('infos_groupe', 'infos_groupe.php?id_groupe=<?php echo $row['groupeid']; ?>');"><?php echo $row['groupeid'].' - '.$row['groupenom']; ?></li>
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
?>