<?php
include('inc/fp.php');
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
			Terrain
		</td>
	</tr>

	<?php
	$i = 0;
	$tab = array('', 'Plaine', 'Forêt', 'Désert', 'Glace', 'Eau', 'Montagne', 'Marais', 'Route', '', '', 'Terre maudite');
	$requete = "SELECT * FROM monstre WHERE affiche = 'y' ORDER BY level ASC, xp ASC";
	$req = $db->query($requete);

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
			'.$type_terrain.'
		</td>
	</tr>';
		$i++;
	}


	?>
	</table>