<?php

include('haut.php');
$cfg["sql"]['host'] = "localhost";
$cfg["sql"]['user'] = "root";
$cfg["sql"]['pass'] = "ilove50";
$cfg["sql"]['db'] = "starshine";
$db = new db();

$tabcolor = array();
$tabcolor[0] = '#aaaaaa';
$tabcolor[1] = '#0068ff';
$tabcolor[2] = '#009900';
$tabcolor[3] = '#ff0000';
$tabcolor[4] = '#ffff00';
$tabcolor[6] = '#ffcccc';
$tabcolor[7] = '#ffa500';
$tabcolor[8] = '#5c1e00';
$tabcolor[9] = '#000000';
$tabcolor[10] = '#0000ff';
$tabcolor[11] = '#ffffff';
$tabcolor[12] = '#cccccc';


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
if($ymax > 150) $ymax = 150;
if($xmin < 1) $xmin = 1;
if($xmax > 150) $xmax = 150;
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
			$requete = "UPDATE map SET royaume = $decor WHERE ID = $temp";
			//echo $requete.'<br />';
			$req = $db->query($requete);
		}
	}
}

?>

<form action="edit_map4.php" name="formulaire" method="POST">
<?php
echo 'xmin : '.$xmin.' xmax : '.$xmax.' ymin : '.$ymin.' ymax : '.$ymax;



//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) <= '.$xmax.'))) ORDER BY ID';
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
	
	//Affichage de la map
	while($row = $db->read_array($req))
	{
		$coord = convert_in_coord($row['ID']);
		$rowid = $row['ID'];
		$W_terrain_case = $row['decor'];
		
		if (($coord['x'] != 0) AND ($coord['y'] != 0))
		{
			$z = 0;
			$case['info']='';
			$positioncase = convert_in_pos($coord['x'],$coord['y']);
			$color = $tabcolor[$row['royaume']];
			if (isset($info[$rowid])) $case['info'] = $info[$rowid][0]['race'];
			if ($coord['y'] != $y)
			{
				echo '</tr>
				<tr>
					<td class="tabnoir">
						'.$coord['y'].'
					</td>
					<td class="decor" style="background-color : '.$color.'" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
						<input type="hidden" name="hidden'.$positioncase.'" value="'.$row['royaume'].'" id="input'.$positioncase.'" />
					</td>';
				$y = $coord['y'];
			}
			else
			{
				echo '
					<td class="decor" style="background-color : '.$color.'" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
						<input type="hidden" name="hidden'.$positioncase.'" value="'.$row['royaume'].'" id="input'.$positioncase.'" />
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
				<optgroup label="Plaine">
					<option value="1" style="background-color : <?php echo $tabcolor[1]; ?>;" class="baseRow1">Barbare</option>
					<option value="2" style="background-color : <?php echo $tabcolor[2]; ?>;" class="baseRow1">Elfe des bois</option>
					<option value="3" style="background-color : <?php echo $tabcolor[3]; ?>;" class="baseRow1">Troll</option>
					<option value="4" style="background-color : <?php echo $tabcolor[4]; ?>;" class="baseRow1">Scavenger</option>
					<option value="6" style="background-color : <?php echo $tabcolor[6]; ?>;" class="baseRow1">Orc</option>
					<option value="7" style="background-color : <?php echo $tabcolor[7]; ?>;" class="baseRow1">Nain</option>
					<option value="8" style="background-color : <?php echo $tabcolor[8]; ?>;" class="baseRow1">Mort-Vivant</option>
					<option value="9" style="background-color : <?php echo $tabcolor[9]; ?>;" class="baseRow1">Corrompu</option>
					<option value="10" style="background-color : <?php echo $tabcolor[10]; ?>;" class="baseRow1">Humain</option>
					<option value="11" style="background-color : <?php echo $tabcolor[11]; ?>;" class="baseRow1">Haut elfe</option>
					<option value="12" style="background-color : <?php echo $tabcolor[12]; ?>;" class="baseRow1">Vampire</option>
					<option value="0" style="background-color : <?php echo $tabcolor[0]; ?>;" class="baseRow1">Neutre</option>
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
		<a href="edit_map4.php?ymin=<?php echo ($ymin - 5); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_nord.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_nordest.png" alt="" />
		</td>
	</tr>
	<tr>
		<td class="rose">
		<a href="edit_map4.php?xmin=<?php echo ($xmin - 5); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_ouest.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_centre.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_map4.php?xmin=<?php echo ($xmax - 4); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_est.png" alt="" /></a>
		</td>
	</tr>
	<tr>
		<td class="rose">
		<img src="image/rdv_sudouest.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_map4.php?ymin=<?php echo ($ymax - 4); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_sud.png" alt="" /></a>
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