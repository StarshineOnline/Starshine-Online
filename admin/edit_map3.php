<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;


include_once(root.'haut.php');
include_once(root.'connect.php');

if (isset($_GET['direction'])) $direction = $_GET['direction'];
elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
if (isset($_GET['xmin'])) $xmin = $_GET['xmin'];
elseif (isset($_POST['xmin'])) $xmin = $_POST['xmin'];
else $xmin = 1;
if (isset($_GET['ymin'])) $ymin = $_GET['ymin'];
elseif (isset($_POST['ymin'])) $ymin = $_POST['ymin'];
else $ymin = 1;

$xmax = $xmin + 15;
$ymax = $ymin + 15;

if($ymin < 1) $ymin = 1;
if($xmin < 1) $xmin = 1;
if ($xmin >= 200) {}
elseif($ymin <= 190) {
  if($ymax > 190) $ymax = 190;
  if($xmax > 190) $xmax = 190;
} elseif ($ymin >= 300 && $xmin >= 300) {
  // Nothing
} else {
  if ($ymin < 200)
    $ymin = 200;
  if ($ymin > 225 && $ymin < 250)
    $ymin = 250;
  if ($ymin > 285)
    $ymin = 285;
     if($xmax > 50) $xmax = 50;
}

?>

<script language="javascript">

//Fonction permettant de changer la texture de preview
function changeTexture(title)
{
	a = eval('$("' + title + '")');
	b = eval('$("selectText")');
	texture = b.options[b.selectedIndex].value;
	nomclass = 'decor tex' + texture;
	a.className = nomclass;
}


//Fonction permettant de modifier la texture de numerocase et de changer le formulaire hidden
function clickTexture(numeroCase)
{
	Case = eval('$("case' + numeroCase + '")');
	Selecteur = eval('$("selectText")');
	Input = eval('$("input' + numeroCase + '")');
	texture = Selecteur.options[Selecteur.selectedIndex].value;
	nomclass = 'decor tex' + texture;
	Case.className = nomclass;
	Input.value = texture;
}

</script>

<?php

if ($direction == 'phase2')
{
	$posymax = $ymax * 1000;
	$posymin = $ymin * 1000;
	for($c = $posymin;$c <= $posymax;$c = $c + 1000)
	{
		for($d = $xmin; $d <= $xmax; $d++)
		{
			$temp = $d + $c;
			$decor = $_POST['hidden'.$temp];
			$info = floor($decor / 100);
			$requete = "UPDATE map SET decor = $decor WHERE id = $temp";
			$req = $db->query($requete);
			$requete = "UPDATE map SET info = $info WHERE id = $temp";
			$req = $db->query($requete);
			$ville = array(1001, 1002, 1003, 1004, 1005, 1006, 1007);
			if (in_array($decor, $ville))
			{
				$requete = "UPDATE map SET type = 1 WHERE id = $temp";
				$req = $db->query($requete);
			}
		}
	}
}

?>

<form action="edit_map3.php" name="formulaire" method="POST">
<?php
echo 'xmin : '.$xmin.' xmax : '.$xmax.' ymin : '.$ymin.' ymax : '.$ymax;



//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(id / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(id / '.$G_ligne.') <= '.$ymax.')) AND (((id - (FLOOR(id / '.$G_colonne.') * 1000)) >= '.$xmin.') AND ((id - (FLOOR(id / '.$G_colonne.') * 1000)) <= '.$xmax.'))) ORDER BY id';
$req = $db->query($requete);
?>
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
	while($row = $db->read_array($req))
	{
		$coord = convert_in_coord($row['id']);
		$rowid = $row['id'];
		$W_terrain_case = $row['decor'];
		
		if (($coord['x'] != 0) AND ($coord['y'] != 0))
		{
			$z = 0;
			$case['info']='';
			$positioncase = convert_in_pos($coord['x'],$coord['y']);
			if (isset($info[$rowid])) $case['info'] = $info[$rowid][0]['race'];
			if ($coord['y'] != $y)
			{
				echo '</tr>
				<tr>
					<td class="tabnoir">
						'.$coord['y'].'
					</td>
					<td class="decor tex'.$W_terrain_case.'" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
						<input type="hidden" name="hidden'.$positioncase.'" value="'.$W_terrain_case.'" id="input'.$positioncase.'" />
					</td>';
				$y = $coord['y'];
			}
			else
			{
				echo '
					<td class="decor tex'.$W_terrain_case.'" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
						<input type="hidden" name="hidden'.$positioncase.'" value="'.$W_terrain_case.'" id="input'.$positioncase.'" />
					</td>';
			}
		}
	}
	
	?>
	</tr>
	</table>
	</div>
	<div class="selecteur">
		<select name="<?php echo $positioncase;?>" size="15" class="baseJumpbox" id="selectText" onChange="changeTexture('texturePreview')">

<?php include_once('terrain.inc.html'); ?>

		</select>
		<table>
		<tr>
			<td class="decor" id="texturePreview">
			</td>
		</tr>
		</table>
		<input type="hidden" name="direction" value="phase2" />
		<input type="hidden" name="xmin" value="<?php echo $xmin; ?>" />
		<input type="hidden" name="ymin" value="<?php echo $ymin; ?>" />
		<input type="submit" value="ok" /><br />
		</form>
	</div>
	<br />
	<div>
<div id='rosedesvents'>
	   <a id='rose_div_hg'></a>
	   <a id='rose_div_h' href="edit_map3.php?ymin=<?php echo ($ymin - 4); ?>&xmin=<?php echo $xmin; ?>"></a>
	   <a id='rose_div_hd'></a>
	   <a id='rose_div_cg'href="edit_map3.php?xmin=<?php echo ($xmin - 4); ?>&ymin=<?php echo $ymin; ?>"></a>
	   <a id='rose_div_c'></a>
	   <a id='rose_div_cd' href="edit_map3.php?xmin=<?php echo ($xmax + 4); ?>&ymin=<?php echo $ymin; ?>"></a>
	   <a id='rose_div_bg'></a>
	   <a id='rose_div_b' href="edit_map3.php?ymin=<?php echo ($ymax - 4); ?>&xmin=<?php echo $xmin; ?>"></a>
	   <a id='rose_div_bd'></a>
</div>	
	</div>
	<a href="view_map.php">Map Globale</a>
	<div style="margin-top : 650px; text-align : center;">
		Starshine Editeur v2.1
	</div>
</body>
</html>