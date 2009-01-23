<?php
require('haut_roi.php');
include('../fonction/messagerie.inc.php');

function affiche_bataille($bataille)
{
	?>
		<div style="float : right;">
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;info_bataille" onclick="return envoiInfo(this.href, 'conteneur');">modifier</a><br />
		<?php
		if($bataille->etat == 0)
		{
			?>
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;debut_bataille" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->id; ?>');">Debuter</a><br />
			<?php
		}
		elseif($bataille->etat == 1)
		{
			?>
			<a href="gestion_bataille.php?id_bataille=<?php echo $bataille->id; ?>&amp;fin_bataille" onclick="return envoiInfo(this.href, 'bataille_<?php echo $bataille->id; ?>');">Fermer</a><br />
			<?php
		}
		?>
		</div>
		<?php echo $bataille->nom; ?><br />
		<?php echo transform_texte($bataille->description); ?><br />
		<?php echo $bataille->etat_texte(); ?><br />
	<?php
}

function affiche_map($bataille)
{
	global $db, $R;
	$bataille->get_reperes('tri_type');
	//print_r($bataille->reperes);
	$batiments = array();
	$dimensions = dimension_map($bataille->x, $bataille->y, 11);
	$requete = "SELECT x, y, hp, nom, type, image FROM construction WHERE royaume = ".$R['ID']." AND x >= ".$dimensions['xmin']." AND x <= ".$dimensions['xmax']." AND y >= ".$dimensions['ymin']." AND y <= ".$dimensions['ymax'];
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$batiments[convert_in_pos($row['x'], $row['y'])] = $row;
	}
	$x = $bataille->x;
	$y = $bataille->y;
	
	$map = new map($x, $y, 12, '../', false, 'low');
	$map->set_batiment($batiments);
	if(array_key_exists('action', $bataille->reperes)) $map->set_repere($bataille->reperes['action']);
	if(array_key_exists('batiment', $bataille->reperes)) $map->set_batiment_ennemi($bataille->reperes['batiment']);
	$map->set_onclick("affichePopUp('gestion_bataille.php?id_bataille=".$bataille->id."&amp;case=%%ID%%&amp;info_case');");
	$map->quadrillage = true;
	$map->affiche();
}

//Nouvelle bataille
if(array_key_exists('new', $_GET))
{
	include('gestion_bataille_menu.php');
	?>
	<h2>Création d'une bataille</h2>
	Nom : <input type="text" name="nom" id="nom" /><br />
	Description :<br />
	<textarea name="description" id="description"></textarea><br />
	x : <input type="text" name="x" id="x" /><br />
	y : <input type="text" name="y" id="y" /><br />
	<input type="button" onclick="description = $('description').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_bataille.php?nom=' + $('nom').value + '&amp;description=' + description + '&amp;x=' + $('x').value + '&amp;y=' + $('y').value + '&amp;new2', 'conteneur');" value="Créer cette bataille" />
	<?php
}
//Nouvelle bataille etape 2 => Création
elseif(array_key_exists('new2', $_GET))
{
	include('gestion_bataille_menu.php');
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
	<?php
}
//Refresh de la carte de la bataille
elseif(array_key_exists('refresh_bataille', $_GET))
{
	$bataille = new bataille($_GET['refresh_bataille']);
	affiche_map($bataille);
}
//Information et modification sur une bataille
elseif(array_key_exists('info_bataille', $_GET))
{
	include('gestion_bataille_menu.php');
	$bataille = new bataille($_GET['id_bataille']);
	?>
	<div id="map" style="float : left;">
	<?php
	affiche_map($bataille);
	?>
	</div>
	<div id="info_bataille" style="float : left;">
		<h1>Bataille : <?php echo $bataille->nom; ?> <a href="gestion_bataille.php?refresh_bataille=<?php echo $bataille->id; ?>" onclick="return envoiInfo(this.href, 'map');">R</a></h1>
		<?php echo transform_texte($bataille->description); ?><br />
		<div id="information_modif"></div>
		<div id="menu_bataille">
			<ul>
				<li><a href="gestion_bataille_groupe.php?id_bataille=<?php echo $bataille->id; ?>" onclick="return envoiInfo(this.href, 'information_onglet_bataille');">Groupes</a></li>
			</ul>
		</div>
		<div id="information_onglet_bataille">
			INFOS
		</div>
	</div>
	<?php
}
//Début d'une bataille
elseif(array_key_exists('debut_bataille', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$bataille->etat = 1;
	$bataille->sauver();
	affiche_bataille($bataille);
}
//Fin d'une bataille
elseif(array_key_exists('fin_bataille', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$bataille->etat = 2;
	$bataille->sauver();
	affiche_bataille($bataille);
}
//Information sur une case d'une bataille
elseif(array_key_exists('info_case', $_GET) OR array_key_exists('type', $_GET))
{
	$bataille = new bataille($_GET['id_bataille']);
	$case = $_GET['case'];
	$coord = convert_in_coord($case);
	if(array_key_exists('type', $_GET))
	{
		$type  = $_GET['id_type'][0];
		$repere = new bataille_repere();
		switch($type)
		{
			case 'a' :
				$repere->type = 'action';
			break;
			case 'b' :
				$repere->type = 'batiment';
			break;
		}
		$repere->id_bataille = $_GET['id_bataille'];
		$repere->id_type = substr($_GET['id_type'], 1, strlen($_GET['id_type']));
		$repere->x = $coord['x'];
		$repere->y = $coord['y'];
		$repere->sauver();
	}
	$reperes = $bataille->get_repere_by_coord($coord['x'], $coord['y']);
	?>
	<h4>Case <?php echo $coord['x']; ?> / <?php echo $coord['y']; ?></h4>
	<?php
	$batiment = false;
	$requete = "SELECT hp, nom FROM construction WHERE royaume = ".$R['ID']." AND x = ".$coord['x']." AND y = ".$coord['y'];
	$req = $db->query($requete);
	if($db->num_rows($req) > 0)
	{
		$batiment = $db->read_assoc($req);
		echo '<div>'.$batiment['nom'].' - '.$batiment['hp'].' HP</div>';
	}
	$type_reperes = array();
	foreach($reperes as $repere)
	{
		$repere->get_type();
		$type_reperes[] = $repere->type;
		switch($repere->type)
		{
			case 'action' :
				$type = 'Mission';
				$repere->get_groupes();
			break;
			case 'batiment' :
				$type = 'Batiment Ennemi';
			break;
		}
		echo '
		<div id="repere'.$repere->id.'">
			'.$type.' : '.$repere->repere_type->nom.' <a href="gestion_bataille.php?id_repere='.$repere->id.'&del_repere" onclick="javascript: if(confirm(\'Voulez vous vraiment supprimer ce repère ?\')) { $(\'repere'.$repere->id.'\').remove(); return envoiInfo(this.href, \'information_modif\'); } else return false;">X</a><br />';
		if($repere->type == 'action') echo '&nbsp;&nbsp;&nbsp;<i>'.count($repere->groupes).' groupe</i>';
		echo '
		</div>';
	}
	//Si ya moins de 2 repères
	if(count($reperes) < 2)
	{
		?>
		Ajouter un nouveau repère ?<br />
		<select name="type" id="type">
		<?php
		if(!in_array('action', $type_reperes))
		{
			$bataille_royaume = new bataille_royaume($R['ID']);
			$types = $bataille_royaume->get_all_repere_type();
			foreach($types as $type)
			{
				?>
				<option value="a<?php echo $type->id; ?>">Mission : <?php echo $type->nom; ?> (<?php echo $type->description; ?>)</option>
				<?php
			}
		}
		if(!in_array('batiment', $type_reperes))
		{
			if(!$batiment)
			{
				$requete = "SELECT id, nom FROM batiment";
				$req = $db->query($requete);
				while($row = $db->read_assoc($req))
				{
					?>
					<option value="b<?php echo $row['id']; ?>">Batiment ennemi : <?php echo $row['nom']; ?></option>
					<?php
				}
			}
		}
		?>
		</select><input type="button" value="Ok" onclick="envoiInfo('gestion_bataille.php?id_type=' + $('type').value + '&amp;id_bataille=<?php echo $bataille->id; ?>&amp;case=<?php echo $case; ?>&amp;type', 'popup_content');"/>
		<?php
	}
}
//Suppression d'un repère
elseif(array_key_exists('del_repere', $_GET))
{
	$repere = new bataille_repere($_GET['id_repere']);
	$repere->supprimer(true);
	echo 'Repère supprimé avec succès';
}
else
{
	include('gestion_bataille_menu.php');
	$bataille_royaume = new bataille_royaume($R['ID']);
	$bataille_royaume->get_batailles();
	
	foreach($bataille_royaume->batailles as $bataille)
	{
		?>
		<div style="border : 1px solid black;" id="bataille_<?php echo $bataille->id; ?>">
		<?php
		affiche_bataille($bataille);
		?>
		</div>
		<?php
	}
}
?>