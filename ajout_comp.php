<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis � jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Ajout des comp�tences de la 0.5
	</div>
	<?php
	$requete = "SELECT ID, nom, classe FROM perso";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		switch($row['classe'])
		{
			case 'archer' : case 'archer d �lite' :
				$requete = "INSERT INTO comp_perso VALUES('', 1, 'survie_bete', 1, ".$row['ID'].")";
				$db->query($requete);
			break;
			case 'voleur' : case 'assassin' :
				$requete = "INSERT INTO comp_perso VALUES('', 1, 'survie_humanoide', 1, ".$row['ID'].")";
				$db->query($requete);
				$requete = "UPDATE perso SET comp_jeu = CONCAT(comp_jeu, ';48') WHERE ID = ".$row['ID'];
				$db->query($requete);
			break;
			case 'sorcier' : case 'grand sorcier' : case 'n�cromancien' : case 'grand n�cromancien' :
				$requete = "INSERT INTO comp_perso VALUES('', 1, 'survie_magique', 1, ".$row['ID'].")";
				$db->query($requete);
			break;
		}
	}
}
?>