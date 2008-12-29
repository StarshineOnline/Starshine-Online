<?php
require('haut_roi.php');
include('../class/bataille.class.php');
include('../class/bataille_royaume.class.php');
include('../class/bataille_repere.class.php');
include('../class/bataille_groupe.class.php');
include('../class/bataille_groupe_repere.class.php');
include('../class/bataille_repere_type.class.php');
include('../fonction/messagerie.inc.php');

//Nouvelle bataille
if(array_key_exists('new', $_GET))
{
	?>
	<h2>Création d'une bataille</h2>
	Nom : <input type="text" name="nom" id="nom" /><br />
	Description :<br />
	<textarea name="description" id="description"></textarea><br />
	x : <input type="text" name="x" id="x" /><br />
	y : <input type="text" name="y" id="y" /><br />
	<input type="button" onclick="description = $('description').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_bataille.php?nom=' + $('nom').value + '&amp;description=' + description + '&amp;x=' + $('x').value + '&amp;y=' + $('x').value + '&amp;new2', 'conteneur');" value="Créer cette bataille" />
	<?php
}
//Nouvelle bataille etape 2 => Création
elseif(array_key_exists('new2', $_GET))
{
	$bataille = new bataille();
	$bataille->nom = $_GET['nom'];
	$bataille->description = $_GET['description'];
	$bataille->id_royaume = $R['ID'];
	$bataille->etat = 0;
	$bataille->x = $_GET['x'];
	$bataille->y = $_GET['y'];
	$bataille->sauver();
	?>
	Bataille créée avec succès<br />
	<a href="gestion_bataille.php" onclick="return envoiInfo(this.href, 'conteneur');">Revenir à la liste des batailles</a>
	<?php
}
//Information et modification sur une bataille
elseif(array_key_exists('info_bataille', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$bataille->get_reperes();
	?>
	<h1>Bataille : <?php echo $bataille->nom; ?></h1>
	<?php echo transform_texte($bataille->description); ?><br />
	<?php
	print_r($bataille->reperes);
	include('map_strategique.php');
}
elseif(array_key_exists('info_case', $_GET) OR array_key_exists('type', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$case = $_GET['case'];
	$coord = convert_in_coord($case);
	if(array_key_exists('type', $_GET))
	{
		$repere = new bataille_repere();
		$repere->id_bataille = $_GET['id_bataille'];
		$repere->id_type = $_GET['id_type'];
		$repere->x = $coord['x'];
		$repere->y = $coord['y'];
		$repere->sauver();
	}
	$repere = $bataille->get_repere_by_coord($coord['x'], $coord['y']);
	//Si ya pas de repère
	if(!$repere)
	{
		?>
		Ajouter un nouveau repère ?<br />
		<select name="type" id="type">
		<?php
		$bataille_royaume = new bataille_royaume($R['ID']);
		$types = $bataille_royaume->get_all_repere_type();
		print_r($types);
		foreach($types as $type)
		{
			?>
			<option value="<?php echo $type->id; ?>"><?php echo $type->nom; ?> (<?php echo $type->description; ?>)</option>
			<?php
		}
		?>
		</select><input type="button" value="Ok" onclick="envoiInfoPost('gestion_bataille.php?id_type=' + $('type').value + '&amp;id_bataille=<?php echo $bataille->id; ?>&amp;case=<?php echo $case; ?>&amp;type', 'information');"/>
		<?php
	}
	else
	{
		$repere->get_type();
		echo $repere->type->nom;
	}
}
else
{
?>
	<h1>Gestion des batailles</h1>
	<?php
	print_r($_GET);
	$bataille_royaume = new bataille_royaume($R['ID']);
	$bataille_royaume->get_batailles();
	
	foreach($bataille_royaume->batailles as $bataille)
	{
		?>
		<div style="border : 1px solid black;">
			<?php echo $bataille->nom; ?><br />
			<?php echo transform_texte($bataille->description); ?><br />
			<?php echo $bataille->statut_texte(); ?><br />
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;info_bataille" onclick="return envoiInfo(this.href, 'conteneur');">modifier</a>
		</div>
		<?php
	}
	?>
	<a href="gestion_bataille.php?new" onclick="return envoiInfo(this.href, 'conteneur');">Nouvelle bataille</a>
	<?php
}
?>