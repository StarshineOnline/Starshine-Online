<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis � jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu_admin.php');
	?>
	<div id="contenu">
	<div id="centre3">
	<div class="titre">
				Edition d'un monstre
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
			Dext�rit�
		</td>
		<td>
			Puissance
		</td>
		<td>
			Volont�
		</td>
		<td>
			M�l�e
		</td>
		<td>
			Esquive
		</td>
		<td>
			Terrain
		</td>
		<td>
			Action
		</td>
	</tr>

	<?php
	$i = 0;
	$tab = array('', 'Plaine', 'For�t', 'D�sert', 'Glace', 'Eau', 'Montagne', 'Marais', 'Route', '', '', 'Terre maudite');
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
		if (file_exists('image/monstre/'.$image.'.png')) $image .= '.png';
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
			'.$row['pp'].'<br /><span class="xsmall">('.(round((1 - calcul_pp($row['pp'])) * 100, 2)).'% de r�duction)</span>
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
			'.$type_terrain.'
		</td>
		<td>
			<a href="edit_monstre_action.php?id_monstre='.$row['id'].'">Modifier Script d\'action</a> / <a href="edit_monstre_drop.php?id_monstre='.$row['id'].'">Modifier drops</a>
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