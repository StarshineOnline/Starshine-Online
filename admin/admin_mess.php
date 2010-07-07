<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
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

	if (isset($_GET['direction'])) $direction = $_GET['direction'];
	elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
	// Formulaire de création du message
	if (true)
	{
		echo '<h2>Info du monde</h2>';
		echo '<form action="admin_mess.php" enctype="multipart/form-data" method="POST"><table>';
		echo '<tr><td width="50%">Titre :</td><td width="50%"><input type="text" name="titre" size="20" value="" maxlength="255"></td></tr>';
		echo '<tr><td width="50%">Publication : </td><td width="50%"><SELECT name="publi" size="1"><OPTION value="0">Non publié<OPTION value="1">Publié</SELECT></td></tr>';
		echo '<tr><td width="50%">Résumé : </td><td width="50%"><textarea name="resume" value="" rows="7" cols="40" ></textarea></tr>';
		echo '<tr><td><input type="hidden" name="direction" value="phase2" /><input type="submit" value="Ajouter le motd" /></td></tr>';
		echo '</table></form>';
	}
	// Enregistrement du film dans la base de donnée
	if ($direction == 'phase2')
	{
		// Vérification des champs vide
		if (($_POST['titre'] == '') OR ($_POST['publi'] == '') OR ($_POST['resume'] == ''))
		{
			echo 'Erreur dans votre saisie';
		}
		else
		{ 	
			$motd_titre = $_POST['titre'];
			$motd_publi = $_POST['publi'];
			$motd_resume = $_POST['resume'];
			$requete = "INSERT INTO motd (`titre`,`publie`,`text`)
			VALUES ('$motd_titre','$motd_publi','$motd_resume')";
			$req = $db->query($requete);
			echo 'Ajout réussi';
		}
	}
	elseif ($direction == 'supp') {
		$req = $db->query('delete from motd where id_motd = \''.
											sSQL($_REQUEST['id'], INTEGER).'\'');
	}
	elseif ($direction == 'hide') {
		$req = $db->query('update motd set publie = 0 where id_motd = \''.
											sSQL($_REQUEST['id'], INTEGER).'\'');
	}
	elseif ($direction == 'pub') {
		$req = $db->query('update motd set publie = 1 where id_motd = \''.
											sSQL($_REQUEST['id'], INTEGER).'\'');
	}
}

?>
<table>
 <thead>
  <tr><th>Titre</th><th>Publié</th><th>Message</th></tr>
 </thead>
 <tbody>
<?php
$requete = "SELECT * FROM motd order by publie desc";
$req_motd = $db->query($requete);
if ($db->num_rows > 0)
{
	echo '<h3>Informations du monde</h3>';
	while ($row_motd = $db->read_assoc($req_motd)) { 
		$pub = $row_motd['publie'] ? 'oui' : 'non';
		$actb = '<a href="admin_mess.php?id='.$row_motd['id_motd'].
			'&amp;direction=';
		$action = $actb.'supp">Supprimer</a> ';
		if ($row_motd['publie'])
			$action .= $actb.'hide">Cacher</a> ';
		else
			$action .= $actb.'pub">Publier</a> ';
		echo '<tr><td>'.$row_motd['titre'].'</td><td>'.$pub.'</td><td>'.
			$row_motd['text'].'</td><td>'.$action.'</td></tr>';
	}
}

?>
 </tbody>
</table>

<?php

include_once(root.'admin/admin_bas.php');