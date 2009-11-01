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
	
	
	if(array_key_exists('debuff', $_GET))
	{
		$requete = 'SELECT race FROM royaume WHERE race != "Neutre"';
		$req = $db->query($requete);
		while($race = $db->read_array($req))
		{
			$requete = 'SELECT id FROM perso WHERE race = "'.$race['race'].'"';
			$req2 = $db->query($requete);
			while($row = $db->read_array($req2))
			{
				switch($_GET['debuff'])
				{
					case 'cacophonie':
						lance_buff('cacophonie', $row['id'], '10', '0', 2678400, 'Cacophonie', 'Que de bruit! Vous gagnez 10% d\'honneur en moins.', 'perso', 1, 0, 0, 0);
					default:
						$buff = buff::create('id', $GET['debuff']);
						lance_buff($buff->get_type(), $row['id'], $buff->get_effet(), $buff->get_effet2(), $buff->get_duree(), $buff->get_nom(), description($buff->get_description(), $buff), 'perso', $buff->get_debuff(), 0, 0, 0);
				}
			}
		}
		echo 'Buff bien lancé.';
	}
	else
	{?>
	<form action="#" method="get" name="buff">
		<select name="debuff" size="3">
			<?php
				$requete = 'SELECT id, nom FROM sort_jeu ORDER BY nom';
				$req = $db->query($requete);
				while($row = $db->read_array($req))
					echo '<option value="',$row['id'],'">',$row['nom'],'</option>';

				//debuffs spéciaux
				echo '<option value="cacophonie">Cacophonie</option>';
			?>
		</select>
		<input type="submit" name="subm" value="Lancer" />
	</form>
		
	<?php
	}
}
?>