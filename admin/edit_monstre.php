<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

$admin = true;
$textures = false;
include_once(root.'admin/admin_haut.php');
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
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
     Edition d&apos;un monstre
	</div>
	<?php
$requete = "SELECT * FROM monstre ORDER BY level ASC, xp ASC";
$req = $db->query($requete);

?>
	<table cellspacing="0" style="margin-top : 10px; border : 1px solid grey;">
	<tr class="tabMonstre">
		<td>
		</td>
		<td>
			Nom
		</td>
		<td>
			Niveau
		</td>
		<td>
			Type
		</td>
		<td>
			HP
		</td>
		<td>
			PP
		</td>
		<td>
			PM
		</td>
		<td>
			Force
		</td>
		<td>
			Dextérité
		</td>
		<td>
			Puissance
		</td>
		<td>
			Volonté
		</td>
		<td>
			Mélée
		</td>
		<td>
			Esquive
		</td>
		<td>
			Incantation
		</td>
		<td>
			Terrain
		</td>
		<td>
			ID
		</td>
		<td>
			Action
		</td>
	</tr>

	<?php
	$i = 0;
	$tab = array('', 'Plaine', 'Forêt', 'Désert', 'Glace', 'Eau', 'Montagne', 'Marais', 'Route', '', '', 'Terre maudite');
	while($row = $db->read_array($req))
	{
		$image = $row['lib'];
		$terrain = explode(';', $row['terrain']);
		$type_terrain = array();
		foreach($terrain as $t)
		{
			$type_terrain[] = $tab[$t];
		}
		$type_terrain = implode(', ', $type_terrain);
		if (file_exists(root.'image/monstre/'.$image.'.png')) $image .= '.png';
		else $image .= '.gif';
		echo '
	<tr class="tabMonstre">
		<td>
			<img src="image/monstre/'.$image.'" />
		</td>
		<td>
			'.$row['nom'].'
		</td>
		<td>
			'.$row['level'].'
		</td>
		<td>
			'.$row['type'].'
		</td>
		<td>
			'.$row['hp'].'
		</td>
		<td>
			'.$row['pp'].'<br /><span class="xsmall">('.(round((1 - calcul_pp($row['pp'])) * 100, 2)).'% de réduction)</span>
		</td>
		<td>
			'.$row['pm'].'
		</td>
		<td>
			'.$row['forcex'].'
		</td>
		<td>
			'.$row['dexterite'].'
		</td>
		<td>
			'.$row['puissance'].'
		</td>
		<td>
			'.$row['volonte'].'
		</td>
		<td>
			'.$row['melee'].'
		</td>
		<td>
			'.$row['esquive'].'
		</td>
		<td>
			'.$row['incantation'].'
		</td>
		<td>
			'.$type_terrain.'
		</td>
		<td>
			'.$row['id'].($row['id_manuel'] ? (' (' . $row['id_manuel'] . ')') : '').'
		</td>
		<td>
			<a href="edit_monstre_action.php?id_monstre='.$row['id'].'">Modifier Script d\'action</a> / <a href="edit_monstre_drop.php?id_monstre='.$row['id'].'">Modifier drops</a> / <a href="edit_monstre_pop.php?id_monstre='.$row['id'].'">Modifier spawn</a> / <a href="edit_monstre_desc.php?id_monstre='.$row['id'].'">Modifier description</a> / <a href="edit_monstre_sort.php?id_monstre='.$row['id'].'">Modifier sort</a> / <a href="edit_monstre_arme.php?id_monstre='.$row['id'].'">Modifier arme</a>
		</td>
	</tr>';
		$i++;
	}
	?>
	</table>
	</div>
	<?php
}
?>
