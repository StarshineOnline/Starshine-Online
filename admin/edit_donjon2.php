<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;

$textures = false;
include_once(root.'inc/fp.php');
setlocale(LC_ALL, 'fr_FR');
add_data_to_head('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />');
include_once(root.'admin/admin_haut.php');

// include_once(root.'haut_site.php');


if (isset($_GET['direction'])) $direction = $_GET['direction'];
elseif (isset($_POST['direction'])) $direction = $_POST['direction'];
if (isset($_GET['xmin'])) $xmin = $_GET['xmin'];
elseif (isset($_POST['xmin'])) $xmin = $_POST['xmin'];
else $xmin = 1;
if (isset($_GET['ymin'])) $ymin = $_GET['ymin'];
elseif (isset($_POST['ymin'])) $ymin = $_POST['ymin'];
else $ymin = 1;

$xmax = $xmin + 30;
$ymax = $ymin + 30;

if($ymin < 1) $ymin = 1;
//if($ymax > 99) $ymax = 99;
if($xmin < 1) $xmin = 1;
//if($xmax > 99) $xmax = 99;

if (array_key_exists('xmax', $_REQUEST)) $xmax = $_REQUEST['xmax'];
if (array_key_exists('ymax', $_REQUEST)) $ymax = $_REQUEST['ymax'];


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
	for($c = $ymin; $c <= $ymax; $c++)
	{
		for($d = $xmin; $d <= $xmax; $d++)
		{
			//$temp = $d + $c;
			$clause = "x = $d and y = $c";
			$temp = $d + $c * 1000;
			$decor = $_POST['hidden'.$temp];
			if($decor != '')
			{
				$info = floor($decor / 100);
				$requete = "SELECT x, y FROM map WHERE $clause";
				$req = $db->query($requete);
				if($db->num_rows > 0)
				{
					$requete = "UPDATE map SET decor = $decor, info = $info, type = 2 WHERE $clause";
				}
				else
				{
					$requete = "INSERT INTO map (x, y, info, decor, royaume, type) VALUES ($d, $c, $info, $decor, 0, 2)";
				}
				$req = $db->query($requete);
				//echo $requete. "<br />\n";
			}
		}
	}
	if (array_key_exists('arene', $_GET)) 
	{
		require_once(root.'arenes/gen_arenes.php');
		$requete = "SELECT * from arenes where nom = '$_GET[arene]'";
		$req = $db->query($requete);
		if ($arene = $db->read_object($req)) {
			$arene_xml = gen_arene($arene->x, $arene->y, $arene->size, $arene->nom);
			$arene_file = fopen(root.'arenes/'.$arene->file.'tmp', 'w+');
			fwrite($arene_file, $arene_xml);
			fclose($arene_file);
			rename(root.'arenes/'.$arene->file.'tmp', root.'arenes/'.$arene->file);
		}
	}
}

$size_tab = 'min-width: '.(($xmax - $xmin) * 60 + 45).'px; min-height: '.
(($ymax - $ymin) * 60 + 20).'px;';

?>

<form action="edit_donjon2.php<?php if(array_key_exists('arene', $_GET)) echo '?arene='.$_GET['arene'] ?>" name="formulaire" method="POST" id="theform">
		<input type="hidden" name="direction" value="phase2" />
		<input type="hidden" name="xmin" value="<?php echo $xmin; ?>" />
		<input type="hidden" name="ymin" value="<?php echo $ymin; ?>" />
		<input type="hidden" name="xmax" value="<?php echo $xmax; ?>" />
		<input type="hidden" name="ymax" value="<?php echo $ymax; ?>" />
<?php
echo 'xmin : '.$xmin.' xmax : '.$xmax.' ymin : '.$ymin.' ymax : '.$ymax;
//Requète pour l'affichage de la map
?>
	<div class="mapedit">
	<table cellpadding="0" cellspacing="0" style="<?php echo $size_tab ?>">
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
			//$positioncase = convert_in_pos($x_map, $y_map);
			$requete = "SELECT * FROM map WHERE x = $x_map and y = $y_map";
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
				$coord = array($row['x'], $row['y']);
				$rowid = convert_in_pos($row['x'], $row['y']);
				$W_terrain_case = $row['decor'];				
				echo '
					<td class="decor tex'.$W_terrain_case.'" id="case'.$rowid.'" onClick="clickTexture('.$rowid.')">
						<input type="hidden" name="hidden'.$rowid.'" value="'.$W_terrain_case.'" id="input'.$rowid.'" />
					</td>';
			}
			else
			{
				//affichage case noire
				$rowid = convert_in_pos($x_map, $y_map);
					echo '
						<td class="decor texblack" id="case'.$rowid.'" onClick="clickTexture('.$rowid.')">
							<input type="hidden" name="hidden'.$rowid.'" value="" id="input'.$rowid.'" />
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
	<div class="selecteur" id="selecteur" title="Palette">
		<table>
		<tr>
			<td class="decor" id="texturePreview">
			</td>
			<td><input type="submit" value="ok" onClick="javascript:doPost()" /></td>
		</tr>
		</table>
		<select name="<?php echo $positioncase;?>" size="15" class="baseJumpbox" id="selectText" onChange="changeTexture('texturePreview')">

<?php
	include_once('donjon.inc.html');
  if (array_key_exists('arene', $_GET)) 
		 include_once('terrain.inc.html');
 ?>

		</select>
		</form>
  	<div>
		Starshine Editeur v2.2
  	</div>
	</div>
	<a href="view_map2.php">Map Globale</a>

<script type="text/javascript">
function doPost() {
  $('#theform').submit();
}


	$(function() {
		$("#selecteur").dialog({ position: ['right','top'] });
	});
</script>

</body>
</html>