<?php

include('haut.php');
$cfg["sql"]['host'] = "localhost";
$cfg["sql"]['user'] = "starshine";
$cfg["sql"]['pass'] = "ilove50";
$cfg["sql"]['db'] = "starshine";
$db = new db();

if (isset($_GET['direction'])) $direction = $_GET['direction'];
elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
if (isset($_GET['xmin'])) $xmin = $_GET['xmin'];
elseif (isset($_POST['xmin'])) $xmin = $_POST['xmin'];
else $xmin = 1;
if (isset($_GET['ymin'])) $ymin = $_GET['ymin'];
elseif (isset($_POST['ymin'])) $ymin = $_POST['ymin'];
else $ymin = 1;

$xmax = $xmin + 9;
$ymax = $ymin + 9;

if($ymin < 1) $ymin = 1;
//if($ymax > 99) $ymax = 99;
if($xmin < 1) $xmin = 1;
//if($xmax > 99) $xmax = 99;
?>

<script language="javascript">

//Fonction permettant de changer la texture de preview
function changeTexture(title)
{
	a = eval('document.getElementById("' + title + '")');
	b = eval('document.getElementById("selectText")');
	texture = b.options[b.selectedIndex].value;
	nomclass = 'decor tex' + texture;
	a.className = nomclass;
}


//Fonction permettant de modifier la texture de numerocase et de changer le formulaire hidden
function clickTexture(numeroCase)
{
	Case = eval('document.getElementById("case' + numeroCase + '")');
	Selecteur = eval('document.getElementById("selectText")');
	Input = eval('document.getElementById("input' + numeroCase + '")');
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
			if($decor != '')
			{
				$info = floor($decor / 100);
				$requete = "SELECT ID FROM map WHERE ID = $temp";
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
					$requete = "UPDATE map SET decor = $decor, info = $info, type = 2 WHERE ID = $temp";
				}
				else
				{
					$requete = "INSERT INTO map VALUES ($temp, $info, $decor, 0, 2)";
				}
				$req = $db->query($requete);
				//echo $requete.'<br />';
			}
		}
	}
}

?>

<form action="edit_donjon2.php" name="formulaire" method="POST">
<?php
echo 'xmin : '.$xmin.' xmax : '.$xmax.' ymin : '.$ymin.' ymax : '.$ymax;
//Requ�te pour l'affichage de la map
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
	<div class="selecteur">
		<select name="<?php echo $positioncase;?>" size="15" class="baseJumpbox" id="selectText" onChange="changeTexture('texturePreview')">
				<optgroup label="Donjon">
					<option value="" class="baseRow1">Case noire</option>
					<option value="1501" class="baseRow1">Donjon 1</option>
					<option value="1502" class="baseRow1">Donjon 2</option>
					<option value="1503" class="baseRow1">Donjon 3</option>
					<option value="1504" class="baseRow1">Donjon 4</option>
					<option value="1505" class="baseRow1">Donjon 5</option>
					<option value="1506" class="baseRow1">Donjon 6</option>
					<option value="1507" class="baseRow1">Donjon 7</option>
					<option value="1508" class="baseRow1">Donjon 8</option>
					<option value="1509" class="baseRow1">Donjon 9</option>
					<option value="1510" class="baseRow1">Donjon 10</option>
					<option value="1511" class="baseRow1">Donjon 11</option>
					<option value="1512" class="baseRow1">Donjon 12</option>
					<option value="1513" class="baseRow1">Donjon 13</option>
				</optgroup>
				<optgroup label="Mur Donjon">
					<option value="1601" class="baseRow1">Mur Donjon 1</option>
					<option value="1602" class="baseRow1">Mur Donjon 2</option>
					<option value="1603" class="baseRow1">Mur Donjon 3</option>
					<option value="1604" class="baseRow1">Mur Donjon 4</option>
					<option value="1605" class="baseRow1">Mur Donjon 5</option>
					<option value="1606" class="baseRow1">Mur Donjon 6</option>
					<option value="1607" class="baseRow1">Mur Donjon 7</option>
					<option value="1608" class="baseRow1">Mur Donjon 8</option>
					<option value="1609" class="baseRow1">Mur Donjon 9</option>
					<option value="1610" class="baseRow1">Mur Donjon 10</option>
					<option value="1611" class="baseRow1">Mur Donjon 11</option>
					<option value="1612" class="baseRow1">Mur Donjon 12</option>
					<option value="1613" class="baseRow1">Mur Donjon 13</option>
				</optgroup>
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
	<div class="deplacement">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="rose">
		<img src="image/rdv_nordouest.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_donjon2.php?ymin=<?php echo ($ymin - 5); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_nord.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_nordest.png" alt="" />
		</td>
	</tr>
	<tr>
		<td class="rose">
		<a href="edit_donjon2.php?xmin=<?php echo ($xmin - 5); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_ouest.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_centre.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_donjon2.php?xmin=<?php echo ($xmax - 4); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_est.png" alt="" /></a>
		</td>
	</tr>
	<tr>
		<td class="rose">
		<img src="image/rdv_sudouest.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_donjon2.php?ymin=<?php echo ($ymax - 4); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_sud.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_sudest.png" alt="" />
		</td>
	</tr>
	</table>
	</div>
	<a href="view_map2.php">Map Globale</a>
	<div style="margin-top : 650px; text-align : center;">
		Starshine Editeur v2.1
	</div>
</body>
</html>