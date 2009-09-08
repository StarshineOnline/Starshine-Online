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
	echo '
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Création d\'un monstre
	</div>				
	';


	//Insertion du monstre dans la bdd
	if(array_key_exists('submit', $_POST))
	{
		
		$requete = "INSERT INTO monstre(lib, nom, type, hp, pp, pm, forcex, dexterite, puissance, volonte, melee, esquive, incantation, sort_vie, sort_mort, sort_element, level, xp, star, terrain) VALUES('".$_POST['lib']."', '".$_POST['nom']."', '".$_POST['type']."', ".$_POST['hp'].", ".$_POST['pp'].", ".$_POST['pm'].", ".$_POST['forcex'].", ".$_POST['dexterite'].", ".$_POST['puissance'].", ".$_POST['volonte'].", ".$_POST['melee'].", ".$_POST['esquive'].", ".$_POST['incantation'].", ".$_POST['sort_vie'].", ".$_POST['sort_mort'].", ".$_POST['sort_element'].", ".$_POST['level'].", ".$_POST['xp'].", ".$_POST['star'].", '".$_POST['terrain']."')";
		$db->query($requete);
		
	}
	?>
<form action="create_monstre.php" method="post">
<table class="admin">
<tr>
	<td>
		Libellé
	</td>
	<td>
		: <input type="text" name="lib" />
	</td>
	<td>
		Nom
	</td>
	<td>
		<input type="text" name="nom" />
	</td>
	<td>
		Type
	</td>
	<td>
		<select name="type">
			<option value="bete">Bête</option>
			<option value="humanoide">Humanoïde</option>
			<option value="magique">Créature magique</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		HP
	</td>
	<td>
		<input type="text" name="hp" />
	</td>
	<td>
		PP
	</td>
	<td>
		<input type="text" name="pp" />
	</td>
	<td>
		PM
	</td>
	<td>
		<input type="text" name="pm" />
	</td>
</tr>
<tr>
	<td>
		Force
	</td>
	<td>
		<input type="text" name="forcex" />
	</td>
	<td>
		Dextérité
	</td>
	<td>
		<input type="text" name="dexterite" />
	</td>
	<td>
		
	</td>
	<td>
	
	</td>
</tr>
<tr>
	<td>
		Puissance
	</td>
	<td>
		<input type="text" name="puissance" />
	</td>
	<td>
		Volonté
	</td>
	<td>
		<input type="text" name="volonte" />
	</td>
	<td>
		
	</td>
	<td>
	
	</td>
</tr>
<tr>
	<td>
		Melee
	</td>
	<td>
		<input type="text" name="melee" />
	</td>
	<td>
		Esquive
	</td>
	<td>
		<input type="text" name="esquive" />
	</td>
	<td>
		Incantation
	</td>
	<td>
		<input type="text" name="incantation" />
	</td>
</tr>
<tr>
	<td>
		Magie de la vie
	</td>
	<td>
		<input type="text" name="sort_vie" />
	</td>
	<td>
		Magie de la mort
	</td>
	<td>
		<input type="text" name="sort_mort" />
	</td>
	<td>
		Magie élémentaire
	</td>
	<td>
		<input type="text" name="sort_element" />
	</td>
</tr>
<tr>
	<td>
		Niveau
	</td>
	<td>
		<input type="text" name="level" />
	</td>
	<td>
		XP
	</td>
	<td>
		<input type="text" name="xp" />
	</td>
	<td>
		Stars
	</td>
	<td>
		<input type="text" name="star" />
	</td>
</tr>
<tr>
	<td>
		Terrain
	</td>
	<td>
		<select name="terrain">
			<option value="1">Plaine</option>
			<option value="2">Forêt</option>
			<option value="3">Désert</option>
			<option value="4">Glace</option>
			<option value="6">Montagne</option>
			<option value="7;11">Marais / Terre Maudite</option>
			<option value="8">Route</option>
		</select>
	</td>
	<td>
	</td>
	<td>
		
	</td>
	<td>
	</td>
	<td>
	
	</td>
</tr>
</table>
<input type="submit" name="submit" value="Créer le monstre" />
<form>
		<?php
}
?>