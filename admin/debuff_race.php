<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');

include_once(root.'admin/menu_admin.php');
	
	
	if(array_key_exists('debuff', $_GET))
	{
		$buff = sort_jeu::create(array('id'), array($_GET['debuff']));
		$buff = $buff[0];
		$requete = 'SELECT id FROM perso WHERE race = "'.$_GET['race'].'"';
		$req2 = $db->query($requete);
		while($row = $db->read_array($req2))
		{
			switch($_GET['debuff'])
			{
			case 'cacophonie':
				lance_buff('cacophonie', $row['id'], '10', '0', 2678400, 'Cacophonie', 'Que de bruit! Vous gagnez 10% d\'honneur en moins.', 'perso', 1, 0, 0, 0);
				break;
			default:
				lance_buff($buff->get_type(), $row['id'], $buff->get_effet(), $buff->get_effet2(), $buff->get_duree(), $buff->get_nom(), description($buff->get_description(), $buff), 'perso', 1, 0, 0, 0);
				}
		}
		echo 'Buff bien lancé.';
	}
	else
	{?>
	<form action="#" method="get" name="buff">
		<select name="race">
			<option value="barbare">barbare</option>
			<option value="elfebois">elfebois</option>
			<option value="elfehaut">elfehaut</option>
			<option value="humain">humain</option>
			<option value="humainnoir">humainnoir</option>
			<option value="mortvivant">mortvivant</option>
			<option value="nain">nain</option>
			<option value="orc">orc</option>
			<option value="scavenger">scavenger</option>
			<option value="troll">troll</option>
			<option value="vampire">vampire</option>
    </select>
		<select name="debuff" size="30">
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

?>