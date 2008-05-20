<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	if(array_key_exists('etape3', $_POST))
	{
		$id = $_POST['id'];
		if(array_key_exists('enchantement_type', $_POST))
		{
			$requete = "SELECT enchantement_type, enchantement_effet FROM gemme WHERE id = ".$id;
			$req = $db->query($requete);
			$row = $db->read_row($req);
			if($row[0] != '')
			{
				$parties = explode(';', $row[0]);
				$enchants = explode(';', $row[1]);
			}
			else
			{
				$parties = array();
				$enchants = array();
			}
			$parties[] = $_POST['enchantement_type'];
			$partie = implode(';', $parties);
			$enchants[] = $_POST['enchantement_effet'];
			$enchant = implode(';', $enchants);
			$requete = "UPDATE gemme SET enchantement_type = '".$partie."', enchantement_effet = '".$enchant."' WHERE id = ".$id;
			$req = $db->query($requete);
			$i = 0;
			while($i < count($parties))
			{
				echo $parties[$i].' '.$enchants[$i].'<br />';
				$i++;
			}
		}
		?>
		Ajouter un effet :
		<form action="create_gemme.php" method="post">
		Type : <select name="enchantement_type">
				<option value="hp">+X HP</option>
				<option value="mp">+X MP</option>
				<option value="reserve">+X réserve de mana</option>
				<option value="blocage">+X% chance de bloquer</option>
				<option value="degat">+X facteur de dégat</option>
				<option value="portee">+X portée de l'arme</option>
				<option value="star">+X% stars trouvé sur les mobs</option>
				<option value="pa_sort">-X% PA pour les sorts</option>
				<option value="round_plus">+X round de combat</option>
				<option value="round_moins">-X round de combat</option>
				<option value="regen_mp">+X% Regen MP</option>
				<option value="regen_hp">+X% Régen HP</option>
				<option value="competence">+X% augmentation de compétences</option>
				<option value="critique">+X% de faire un coup critique</option>
				<option value="deplacement">-X au couts de déplacement</option>
			</select>
		Effet : <input type="text" name="enchantement_effet" />
		<input type="submit" name="etape3" value="Valider cet effet" /><br />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="submit" name="etape4" value="Finir cette gemme" />
		</form>
		<?php
	}
	elseif(array_key_exists('etape2', $_POST))
	{
		if(!array_key_exists('id', $_POST))
		{
			//Création de la gemme
			$requete = "INSERT INTO gemme VALUES ('', '".$_POST['nom_gemme']."', '".$_POST['type']."', ".$_POST['niveau'].", '', '".$_POST['enchantement']."', '".$_POST['description']."', '', '')";
			$db->query($requete);
			$id = $db->last_insert_id();
		}
		else
		{
			$id = $_POST['id'];
			$requete = "SELECT partie FROM gemme WHERE id = ".$id;
			$req = $db->query($requete);
			$row = $db->read_row($req);
			if($row[0] != '') $parties = explode(';', $row[0]); else $parties = array();
			$parties[] = $_POST['partie'];
			$partie = implode(';', $parties);
			$requete = "UPDATE gemme SET partie = '".$partie."' WHERE id = ".$id;
			$req = $db->query($requete);
			$i = 0;
			while($i < count($parties))
			{
				echo $parties[$i].'<br />';
				$i++;
			}
		}
		?>
		Ajouter une partie :
		<form action="create_gemme.php" method="post">
		<select name="partie">
			<option value="epee">Epée</option>
			<option value="dague">Dague</option>
			<option value="hache">Hache</option>
			<option value="arc">Arc</option>
			<option value="baton">Baton</option>
			<option value="bouclier">Bouclier</option>
			<option value="chaussure">Chaussure</option>
			<option value="ceinture">ceinture</option>
			<option value="main">Mains</option>
			<option value="jambe">Jambe</option>
			<option value="tete">Tête</option>
			<option value="torse">Torse</option>
			<option value="cou">Cou</option>
			<option value="dos">Dos</option>
			<option value="doigt">Doigt</option>
		</select>
		<input type="submit" name="etape2" value="Valider cette partie" /><br />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="submit" name="etape3" value="Passer à l'étape suivante -->" />
		</form>
		<?php
	}
	else
	{
?>
<h2>Création d'une gemme</h2>
<form action="create_gemme.php" method="post">
<table>
<tr>
	<td>
		Nom de la gèmme
	</td>
	<td>
		: <input type="text" name="nom_gemme" />
	</td>
	<td>
		Niveau
	</td>
	<td>
		: <input type="text" name="niveau" />
	</td>
</tr>
<tr>
	<td>
		Type
	</td>
	<td>
		: <select name="type">
			<option value="arme">Arme</option>
			<option value="armure">Armure</option>
		</select>
	</td>
</tr>
<tr>
	<td>
		Nom Enchantement
	</td>
	<td>
		: <input type="text" name="enchantement" />
	</td>
</tr>
<tr>
	<td>
		Description
	</td>
</tr>
<tr>
	<td colspan="4">
		<textarea name="description" cols="60" rows="15"></textarea>
	</td>
</tr>
</table>
	<input type="submit" name="etape2" value="Passer à l'étape suivante -->" />
</form>
<?php
	}
}
?>