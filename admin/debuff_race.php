<?php
if (file_exists('../root.php'))
	include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	include_once(root.'fonction/base.inc.php');
	
	$requete = 'SELECT race FROM royaume WHERE race != "Neutre"';
	$req = $db->query($requete);
	while($race = $db->read_array($req))
	{
		$requete = 'SELECT id FROM perso WHERE race = "'.$race['race'].'"';
		$req2 = $db->query($requete);
		while($row = $db->read_array($req2))
			lance_buff('cacophonie', $row['id'], '0', '0', 2678400, 'Cacophonie', 'Que de bruit! Vous gagnez 10% d\'honneur en moins.', 'perso', 1, 0, 0, 0);
	}
}
?>