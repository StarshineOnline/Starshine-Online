<?php

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'admin/admin_haut.php');
setlocale(LC_ALL, 'fr_FR');
// include_once(root.'haut_site.php');

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

  echo '<br/><a href="view_donjon_full.php">Afficher tout</a><br/>';
	
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
	<div class="mapedit">
		<div class="rosedesvents">
			<a id='rose_div_hg' href="?xmin=<?php echo ($xmin - 4); ?>&ymin=<?php echo ($ymin - 4); ?>&xmax=<?php echo ($xmax - 4); ?>&ymax=<?php echo ($ymax - 4); ?>"></a>
			<a id='rose_div_h' href="?xmin=<?php echo $xmin; ?>&ymin=<?php echo ($ymin - 4); ?>&xmax=<?php echo $xmax; ?>&ymax=<?php echo ($ymax - 4); ?>"></a>
			<a id='rose_div_hd' href="?xmin=<?php echo ($xmin + 4); ?>&ymin=<?php echo ($ymin - 4); ?>&xmax=<?php echo ($xmax + 4); ?>&ymax=<?php echo ($ymax - 4); ?>"></a>
			<a id='rose_div_cg'href="?xmin=<?php echo ($xmin - 4); ?>&ymin=<?php echo $ymin; ?>&xmax=<?php echo ($xmax - 4); ?>&ymax=<?php echo $ymax; ?>"></a>
			<a id='rose_div_c'></a>
			<a id='rose_div_cd' href="?xmin=<?php echo ($xmin + 4); ?>&ymin=<?php echo $ymin; ?>&xmax=<?php echo ($xmax + 4); ?>&ymax=<?php echo $ymax; ?>"></a>
			<a id='rose_div_bg' href="?xmin=<?php echo ($xmin - 4); ?>&ymin=<?php echo ($ymin + 4); ?>&xmax=<?php echo ($xmax - 4); ?>&ymax=<?php echo ($ymax + 4); ?>"></a>
			<a id='rose_div_b' href="?xmin=<?php echo $xmin; ?>&ymin=<?php echo ($ymin + 4); ?>&xmax=<?php echo $xmax; ?>&ymax=<?php echo ($ymax + 4); ?>"></a>
			<a id='rose_div_bd' href="?xmin=<?php echo ($xmin + 4); ?>&ymin=<?php echo ($ymin + 4); ?>&xmax=<?php echo ($xmax + 4); ?>&ymax=<?php echo ($ymax + 4); ?>"></a>
		</div>
		<?php
		$xCentre = floor(($xmin+$xmax)/2);
		$yCentre = floor(($ymin+$ymax)/2);
		$champVision = floor(($xmax-$xmin)/2);
		$map = new map($xCentre, $yCentre, $champVision, '', true);
		$map->onclick = '';
		$map->donjon = false;
		$map->xmin = $xmin;
		$map->xmax = $xmax;
		$map->ymin = $ymin;
		$map->ymax = $ymax;
		
		$map->affiche();
		?>
	</div>
</div>
</body>
</html>
