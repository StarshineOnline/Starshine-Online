<?php
include('haut.php');
include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte compl�te, l\'exp�rience acquise gr�ce � l\'alpha m\'a permis de voir les gros probl�mes qui pourraient se poser.<br />
	Je vais donc travailler sur la b�ta.<br />';
}
else
{
	include('menu.php');

$requete = "SELECT * FROM monstre WHERE affiche = 'y' ORDER BY level ASC, xp ASC";
$req = $db->query($requete);

?>
<div id="contenu">
	<div id="centre2">
	<div class="titre">
		Liste des monstres communs
	</div>
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
	</tr>';
		$i++;
	}


	?>
	</table>
	</div>
	<?php
	include('menu_d.php');
	?>
</div>
</div>
<?php
}
include('bas.php');
?>