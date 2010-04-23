<?php

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'haut.php');
setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');


if (!array_key_exists('xmin', $_GET) &&
		!array_key_exists('ymin', $_GET))
{
	
	echo '<h1>Donjons</h1>';

	$requete = "SELECT * FROM donjon";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
		{
			echo '<a href="?xmin='.$row['x_donjon'].'&amp;ymin='.$row['y_donjon'].'">'.$row['nom'].'</a><br />';
		}
	
	echo '<h1>Arènes</h1>';
	
	$requete = "SELECT * FROM arenes";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
		{
			echo '<a href="?xmin='.($row['x'] - 1).'&amp;ymin='.($row['y'] - 1).'&amp;xmax='.($row['x'] + $row['size'] + 1).'&amp;ymax='.($row['y'] + $row['size'] + 1).'&amp;arene='.$row['nom'].'">'.$row['nom'].'</a><br />';
		}
	echo "\n</div></body></html>\n";
	exit(0);
}

if (isset($_GET['xmin'])) $xmin = $_GET['xmin'];
elseif (isset($_POST['xmin'])) $xmin = $_POST['xmin'];
else $xmin = 1;
if (isset($_GET['ymin'])) $ymin = $_GET['ymin'];
elseif (isset($_POST['ymin'])) $ymin = $_POST['ymin'];
else $ymin = 1;

$xmax = $xmin + 15;
$ymax = $ymin + 15;

if (array_key_exists('xmax', $_GET)) $xmax = $_GET['xmax'];
if (array_key_exists('ymax', $_GET)) $ymax = $_GET['ymax'];

?>

<div id='rosedesvents'>
	   <a id='rose_div_hg'></a>
	   <a id='rose_div_h' href="?ymin=<?php echo ($ymin - 4); ?>&xmin=<?php echo $xmin; ?>"></a>
	   <a id='rose_div_hd'></a>
	   <a id='rose_div_cg'href="?xmin=<?php echo ($xmin - 4); ?>&ymin=<?php echo $ymin; ?>"></a>
	   <a id='rose_div_c'></a>
	   <a id='rose_div_cd' href="?xmin=<?php echo ($xmin + 4); ?>&ymin=<?php echo $ymin; ?>"></a>
	   <a id='rose_div_bg'></a>
	   <a id='rose_div_b' href="?ymin=<?php echo ($ymin + 4); ?>&xmin=<?php echo $xmin; ?>"></a>
	   <a id='rose_div_bd'></a>
</div>

	<div class="mapedit">
	<table cellpadding="0" cellspacing="0">
	<tr class="tabnoir">
		<td>
		</td>
<?php
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		echo '<td style="text-align : center;">'.$i.'</td>';
	}
	
	$x = 0;
	$y = 0;
	
	$j = 0;
	
	$index = 0;
	
	//Affichage de la map
	$y_map = $ymin;
	while($y_map < $ymax)
	{
		$x_map = $xmin;
		while($x_map < $xmax)
		{
			$positioncase = convert_in_pos($x_map, $y_map);
			$requete = "SELECT * FROM map WHERE ID = ".$positioncase;
			$req = $db->query($requete);
			if ($x_map == $xmin)
			{
				echo '</tr>
				<tr>
					<td class="tabnoir">
						'.$y_map.'
					</td>';
			}
			if($db->num_rows > 0)
			{
				$row = $db->read_assoc($req);
				//Affichage de la case
				$coord = convert_in_coord($row['ID']);
				$rowid = $row['ID'];
				$W_terrain_case = $row['decor'];				
				echo '
					<td class="decor tex'.$W_terrain_case.'" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
						<input type="hidden" name="hidden'.$positioncase.'" value="'.$W_terrain_case.'" id="input'.$positioncase.'" />
					</td>';
			}
			else
			{
				//affichage case noire
					echo '
						<td class="decor texblack" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
							<input type="hidden" name="hidden'.$positioncase.'" value="" id="input'.$positioncase.'" />
						</td>';
			}
			$x_map++;
		}
		$y_map++;
	}
	?>
	</tr>
	</table>
	</div>

</div>
</body>
</html>
