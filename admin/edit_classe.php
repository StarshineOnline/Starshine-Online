<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
$admin = true;

$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mise à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include_once(root.'admin/menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<?php
	//Requis
	if(array_key_exists('requis', $_GET))
	{
		$classe = new classe($_GET['id_classe']);
		echo '<h1>'.$classe->get_nom().'</h1>';
		if(array_key_exists('comp', $_POST))
		{
			$classe_requis = new classe_requis();
			$classe_requis->set_competence($_POST['comp']);
			$classe_requis->set_id_classe($classe->get_id());
			$classe_requis->set_requis($_POST['requis']);
			$classe_requis->sauver(false, true);
		}
		$requis = classe_requis::create('id_classe', $classe->get_id());
		foreach($requis as $comp)
		{
			?>
			<li><?php echo $comp->get_competence(); ?> : <?php echo $comp->get_requis(); ?></li>
			<?php
		}
		?>
			<form action="edit_classe.php?id_classe=<?php echo $classe->get_id(); ?>&requis" method="post">
				<select name="comp" id="comp">
					<option value="classe">Classe</option>
					<option value="honneur">Honneur</option>
					<option value="incantation">Incantation</option>
					<option value="sort_vie">Magie de la vie</option>
					<option value="sort_element">Magie élémentaire</option>
					<option value="sort_mort">Nécromancie</option>
					<option value="dressage">Dressage</option>
					<option value="distance">Tir à distance</option>
					<option value="melee">Mélée</option>
					<option value="esquive">Esquive</option>
				</select>
				<input type="text" name="requis" />
				<input type="submit" value="Ajouter" />
			</form>
		<?php
	}
	//permet
	if(array_key_exists('comp', $_GET))
	{
		$classe = new classe($_GET['id_classe']);
		echo '<h1>'.$classe->get_nom().'</h1>';
		if(array_key_exists('comp', $_POST))
		{
			$classe_permet = new classe_permet();
			$classe_permet->set_competence($_POST['comp']);
			$classe_permet->set_id_classe($classe->get_id());
			$classe_permet->set_permet($_POST['permet']);
			$classe_permet->set_new($_POST['new']);
			$classe_permet->sauver(false, true);
		}
		$permet = classe_permet::create('id_classe', $classe->get_id());
		foreach($permet as $comp)
		{
			?>
			<li><?php echo $comp->get_competence(); ?> : <?php echo $comp->get_permet(); ?> (<?php echo $comp->get_new(); ?>)</li>
			<?php
		}
		?>
			<form action="edit_classe.php?id_classe=<?php echo $classe->get_id(); ?>&comp" method="post">
				<select name="comp" id="comp">
					<option value="incantation">Incantation</option>
					<option value="sort_vie">Magie de la vie</option>
					<option value="sort_element">Magie élémentaire</option>
					<option value="sort_mort">Nécromancie</option>
					<option value="dressage">Dressage</option>
					<option value="distance">Tir à distance</option>
					<option value="melee">Mélée</option>
					<option value="esquive">Esquive</option>
					<option value="blocage">Blocage</option>
					<option value="survie_magique">Survie magique</option>
					<option value="survie_bete">Survie bête</option>
					<option value="survie_humanoide">Survie Humanoïdes</option>
					<option value="facteur_magie">Facteur magie</option>
					<option value="maitrise_dague">Maitrise dague</option>
					<option value="maitrise_arc">Maitrise Arc</option>
					<option value="maitrise_critique">Maitrise critique</option>
					<option value="art_critique">Art du critique</option>
					<option value="max_pet">Maximum créatures</option>
				</select>
				<input type="text" name="permet" />
				<select name="new" id="new">
					<option value="no">no</option>
					<option value="yes">yes</option>
				</select>
				<input type="submit" value="Ajouter" />
			</form>
		<?php
	}
	//Requis
	if(array_key_exists('capa', $_GET))
	{
		$classe = new classe($_GET['id_classe']);
		echo '<h1>'.$classe->get_nom().'</h1>';
		if(array_key_exists('competence', $_POST))
		{
			$classe_comp_permet = new classe_comp_permet();
			$classe_comp_permet->set_competence($_POST['competence']);
			$classe_comp_permet->set_id_classe($classe->get_id());
			$classe_comp_permet->set_type($_POST['type']);
			$classe_comp_permet->sauver(false, true);
		}
		$requis = classe_comp_permet::create('id_classe', $classe->get_id());
		foreach($requis as $comp)
		{
			$requete = "SELECT nom FROM ".$comp->get_type()." WHERE id = ".$comp->get_competence();
			$req = $db->query($requete);
			$capa = $db->read_assoc($req);
			?>
			<li><?php echo $capa['nom']; ?> (<?php echo $comp->get_competence(); ?>) : <?php echo $comp->get_type(); ?></li>
			<?php
		}
		?>
			<form action="edit_classe.php?id_classe=<?php echo $classe->get_id(); ?>&capa" method="post">
				<select name="type" id="type">
					<option value="comp_combat">Compétence Combat</option>
					<option value="comp_jeu">Compétence jeu</option>
				</select>
				<input type="text" name="competence" />
				<input type="submit" value="Ajouter" />
			</form>
		<?php
	}
	?>
	<h1>Gestion des classes</h1>
	<form action="edit_classe.php?new" method="post">
		<input type="text" name="nom" />
		<select name="rang">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
		</select>
		<select name="type">
			<option value="mage">Mage</option>
			<option value="guerrier">guerrier</option>
		</select>
		<input type="submit" value="Créer cette classe" />
	</form>
	<?php
	if(array_key_exists('new', $_GET))
	{
		$classe = new classe();
		$classe->set_nom($_POST['nom']);
		$classe->set_rang($_POST['rang']);
		$classe->set_type($_POST['type']);
		$classe->sauver(false, true);
	}
	$classes = classe::create(0, 0, "rang ASC");
	?>
	<table cellspacing="0" style="margin-top : 10px; border : 1px solid grey;">
	<tr class="tabMonstre">
		<td>
			id
		</td>
		<td>
			Nom
		</td>
		<td>
			Rang
		</td>
		<td>
			Type
		</td>
		<td>
			
		</td>
	</tr>

	<?php
	foreach($classes as $classe)
	{
		echo '
	<tr class="tabMonstre">
		<td>
			'.$classe->get_id().'
		</td>
		<td>
			'.$classe->get_nom().'
		</td>
		<td>
			'.$classe->get_rang().'
		</td>
		<td>
			'.$classe->get_type().'
		</td>
		<td>
			<a href="edit_classe.php?capa&id_classe='.$classe->get_id().'">Capacité permet</a> / <a href="edit_classe.php?comp&id_classe='.$classe->get_id().'">Comp permet</a> / <a href="edit_classe.php?requis&id_classe='.$classe->get_id().'">Requis</a>
		</td>
	</tr>';
	}
	?>
	</table>
	</div>
	<?php
}
?>