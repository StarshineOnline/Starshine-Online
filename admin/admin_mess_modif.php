<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$textures = false;
include_once(root.'../haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'../haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'menu_admin.php');

	if (isset($_GET['direction'])) $direction = $_GET['direction'];
	elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
	if (!isset($direction))
	{
		$requete = 'SELECT * FROM motd ORDER BY DESC'; 
		$req = mysql_query($requete);
		while($row = mysql_fetch_array($req))
		{
		
			$motd_id = $row['motd_id'];
			$motd_titre = $row['motd_titre'];
			$motd_publi = $row['motd_publi'];
			$motd_resume = $row['motd_resume'];
		
			echo '<form action="admin_mess_modif.php" method=POST>';
			echo '<table width=800px>';
			echo '<tr>';
			echo '<td width="50%">'.$motd_titre.' - '.$motd_publi.'</td>';
			echo '</tr><input type=hidden name="cherche_id" value="'.$motd_id.'" />';
			echo '<tr>';
			echo '<td><input type=hidden name="direction" value=phase2 /> <input type=submit value=Validez /></td>';
			echo '</tr>';
			echo '</table>';
		
		}
		echo '<p>Oki</p>';
	}
	elseif ($direction == 'phase2')
	{
		$requete = 'SELECT * FROM motd WHERE motd_id = "'.$motd_id.'"';
		$req = mysql_query($requete);
		while($row = mysql_fetch_array($req))
		{
			$motd_id = $row['motd_id'];
			$motd_titre = $row['motd_titre'];
			$motd_publi = $row['motd_publi'];
			$motd_resume = $row['motd_resume'];
		}
		echo '<h2>Info du monde</h2>';
		echo '<form action="admin_mess.php" enctype="multipart/form-data" method="POST"><table>';
		echo '<tr><td width="50%">Titre :</td><td width="50%"><input type="text" name="titre" size="20" value="'.$motd_titre.'" maxlength="255"></td></tr>';
		echo '<tr><td width="50%">Publication : </td><td width="50%"><SELECT name="publi" size="1">';
		if ($motd_publi = 'nonpublie')
		{
			echo '<OPTION value="nonpublie"  selected="selected">Non publié<OPTION value="publie">Publié</SELECT>';
		}
		else 
		{
			echo '<OPTION value="nonpublie"  selected="selected">Non publié<OPTION value="publie">Publié</SELECT>';
		}
		echo'</td></tr>';
		echo '<tr><td width="50%">Résumé : </td><td width="50%"><textarea name="resume" value="'.$motd_resume.'" rows="7" cols="40" ></textarea></tr>';
		echo '<tr><td><input type="hidden" name="direction" value="phase3" /><input type=hidden name="motd" value="'.$motd_id.'" /><input type="submit" value="Valider" /></td></tr>';
		echo '</table></form>';
	}
	elseif ($direction == 'phase3')
	{
		// Vérification des champs vide
		if (($_POST['titre'] == '') OR ($_POST['publi'] == '') OR ($_POST['resume'] == ''))
		{
			echo 'Erreur dans votre saisie';
		}
		else
		{ 	
			$motd_id = $_POST['motd'];
			$motd_titre = $_POST['titre'];
			$motd_publi = $_POST['publi'];
			$motd_resume = $_POST['resume'];
			$requete = 'UPDATE film SET motd_titre = "'.$motd_titre.'", motd_publi = "'.$motd_publi.'", motd_resume= "'.$motd_resume.'" WHERE motd_id = "'.$motd_id.'"';
			mysql_query($requete) or die('Erreur SQL !<br />'.$requete.'<br />'.mysql_error());
			echo 'Modification effectué';
		}
	}
}
?>