<?php
$textures = false;
include('../haut.php');
setlocale(LC_ALL, 'fr_FR');
include('../haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');

	if (isset($_GET['direction'])) $direction = $_GET['direction'];
	elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
	// Formulaire de création du film
	if (!isset($direction))
	{
		echo '<h2>Info du monde</h2>';
		echo '<form action="admin_mess.php" enctype="multipart/form-data" method="POST"><table>';
		echo '<tr><td width="50%">Titre :</td><td width="50%"><input type="text" name="titre" size="20" value="" maxlength="255"></td></tr>';
		echo '<tr><td width="50%">Publication : </td><td width="50%"><SELECT name="publi" size="1"><OPTION value="nonpublie">Non publié<OPTION value="publie">Publié</SELECT></td></tr>';
		echo '<tr><td width="50%">Résumé : </td><td width="50%"><textarea name="resume" value="" rows="7" cols="40" ></textarea></tr>';
		echo '<tr><td><input type="hidden" name="direction" value="phase2" /><input type="submit" value="Ajouter le motd" /></td></tr>';
		echo '</table></form>';
	}
	// Enregistrement du film dans la base de donnée

	elseif ($direction == 'phase2')
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
			$requete = "INSERT INTO motd (`motd_id`,`motd_titre`,`motd_publi`,`motd_resume`)
			VALUES ('','$motd_titre','$motd_publi','$motd_resume')";
			mysql_query($requete) or die('Erreur SQL !<br />'.$requete.'<br />'.mysql_error());			
			echo 'Ajout réussi';

	
		}
	}

}
?>