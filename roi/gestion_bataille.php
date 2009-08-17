<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
require('haut_roi.php');
include_once(root.'fonction/messagerie.inc.php');

function affiche_bataille($bataille)
{
	?>
	<div style="clear : both;"></div>
	<div id="bataille_<?php echo $bataille->id; ?>" style="margin : 10px;">
		<fieldset style="float : left; height : 50px; padding : 5px;">
			<legend><?php echo ucwords($bataille->etat_texte()); ?></legend>
			<a href="#" onclick="affiche_bataille('gestion_bataille.php','id_bataille=<?php echo $bataille->id; ?>&amp;info_bataille');">Gérer</a><br />
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
		</fieldset>
		<fieldset style="padding : 5px; width : 500px; float : left; min-height : 50px;">
			<legend><?php echo $bataille->nom; ?></legend>
			<?php echo transform_texte($bataille->description); ?><br />
		</fieldset>
	</div>
	<?php
}

function affiche_map($bataille)
{
	global $db;
	$map = new map($x, $y, 5, '../', false, 'low');
	print_r($bataille->reperes);
	if(array_key_exists('action', $bataille->reperes)) $map->set_repere($bataille->reperes['action']);
	if(array_key_exists('batiment', $bataille->reperes)) $map->set_batiment_ennemi($bataille->reperes['batiment']);
	$map->set_onclick("affichePopUp('gestion_bataille.php?id_bataille=".$bataille->id."&amp;case=%%ID%%&amp;info_case');");
	$map->quadrillage = true;
	$map->affiche();
}

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
//Nouvelle bataille
else if(array_key_exists('new', $_GET))
{
	include_once(root.'roi/gestion_bataille_menu.php');
	?>
	<div style='clear:both'>
	<div style='float:left;'>
	Nom : <input type="text" name="nom" id="nom" /><br />
	Description :<br />
	<textarea name="description" id="description"></textarea><br />
	</div>
	<?php
$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".$joueur->get_race()."'";
$req = $db->query($requete);
echo "<ul>";
while($row = $db->read_assoc($req))
{
	if($row['groupenom'] == '') $row['groupenom'] = '-----';
	?>
	<li id="groupe_<?php echo $row['groupeid']; ?>" onclick="refresh('infos_groupe.php?id_groupe=<?php echo $row['groupeid']; ?>', 'infos_groupe');"><?php echo $row['groupeid'].' - '.$row['groupenom']; ?></li>
	<?php
}
?>
</ul>
	
	<?php
	$x = $Trace[$royaume->get_race()]['spawn_x'];
	$y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%ID%%', 'valide_choix_bataille');");
	$map->quadrillage = true;
	echo "<div id='choix_bataille' style='float : right;'>";
	?>
		<div id="move_map_menu">
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y - 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Haut</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y + 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Bas</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x - 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Gauche</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x + 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Droite</a><br />
		X : <input type="text" id="go_x" style="width : 30px;" /> / Y : <input type="text" id="go_y" style="width : 30px;" /> <input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');" value="Go !" /><br />
		<div id="valide_choix_bataille"></div>
		</div>
	<?php	
	$map->affiche();
	?>
	</div>
	</div>
	<div style="clear : both;"></div>
	<input type="button" onclick="description = $('description').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_bataille.php?nom=' + $('nom').value + '&amp;description=' + description + '&amp;x=' + $('x').value + '&amp;y=' + $('y').value + '&amp;new2', 'conteneur');" value="Créer cette bataille" />
	</div>
	<?php
}
elseif(array_key_exists('move_map', $_GET))
{
	if(array_key_exists('x', $_GET)) $x = $_GET['x'];
	else $x = $Trace[$royaume->get_race()]['spawn_x'];
	if(array_key_exists('y', $_GET)) $y = $_GET['y'];
	else $y = $Trace[$royaume->get_race()]['spawn_y'];
	$map = new map($x, $y, 8, '../', false, 'low');
	$map->set_onclick("envoiInfo('gestion_bataille.php?valide_choix_bataille&amp;case=%%ID%%', 'valide_choix_bataille');");
	$map->quadrillage = true;
	?>
		<div id="move_map_menu">
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y - 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Haut</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo $x; ?>&y=<?php echo ($y + 10); ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Bas</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x - 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Gauche</a>
		<a href="gestion_bataille.php?move_map&x=<?php echo ($x + 10); ?>&y=<?php echo $y; ?>" onclick="return envoiInfo(this.href, 'choix_bataille');">Droite</a><br />
		X : <input type="text" id="go_x" style="width : 30px;" /> / Y : <input type="text" id="go_y" style="width : 30px;" /> <input type="button" onclick="envoiInfo('gestion_bataille.php?move_map&x=' + $('go_x').value + '&y=' + $('go_y').value, 'choix_bataille');" value="Go !" /><br />
		<div id="valide_choix_bataille"></div>
		</div>
	
	<?php
	$map->affiche();
}
elseif(array_key_exists('valide_choix_bataille', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	?>
	Vous avez séléctionné X : <?php echo $coord['x']; ?> / Y : <?php echo $coord['y']; ?> comme centre de la bataille.
	<input type="hidden" name="x" id="x" value="<?php echo $coord['x']; ?>" />
	<input type="hidden" name="y" id="y" value="<?php echo $coord['y']; ?>" />
	<?php
}
//Nouvelle bataille etape 2 => Création
elseif(array_key_exists('new2', $_GET))
{
	include_once(root.'roi/gestion_bataille_menu.php');
	$bataille = new bataille();
	$bataille->nom = $_GET['nom'];
	$bataille->description = $_GET['description'];
	$bataille->id_royaume = $royaume->get_id();
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
	include_once(root.'roi/gestion_bataille_menu.php');
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
	$requete = "SELECT hp, nom FROM construction WHERE royaume = ".$royaume->get_id()." AND x = ".$coord['x']." AND y = ".$coord['y'];
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
			$bataille_royaume = new bataille_royaume($royaume->get_id());
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
	include_once(root.'roi/gestion_bataille_menu.php');
	$bataille_royaume = new bataille_royaume($royaume->get_id());
	$bataille_royaume->get_batailles();
	
	foreach($bataille_royaume->batailles as $bataille)
	{
		affiche_bataille($bataille);
	}
}
?>
