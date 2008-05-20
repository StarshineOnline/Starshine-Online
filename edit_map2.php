<?php

include('haut.php');

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
	$posymax = $ymax * 100;
	$posymin = $ymin * 100;
	for($c = $posymin;$c <= $posymax;$c = $c + 100)
	{
		for($d = $xmin; $d <= $xmax; $d++)
		{
			$temp = $d + $c;
			$decor = $_POST['hidden'.$temp];
			$info = floor($decor / 100);
			$requete = "UPDATE map SET decor = $decor WHERE ID = $temp";
			$req = $db->query($requete);
			$requete = "UPDATE map SET info = $info WHERE ID = $temp";
			$req = $db->query($requete);
			$ville = array(1001, 1002, 1003, 1004, 1005, 1006, 1007);
			if (in_array($decor, $ville))
			{
				$requete = "UPDATE map SET type = 1 WHERE ID = $temp";
				$req = $db->query($requete);
			}
		}
	}
}

?>

<form action="edit_map2.php" name="formulaire" method="POST">
<?php
echo 'xmin : '.$xmin.' xmax : '.$xmax.' ymin : '.$ymin.' ymax : '.$ymax;



//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 100)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne.') * 100)) <= '.$xmax.'))) ORDER BY ID';
$req = $db->query($requete);
$requete_joueurs = 'SELECT * FROM perso WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_joueurs = $db->query($requete_joueurs);
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
	// $db->read_array nous retourne ID, 0, nom, 1 etc ... 
	// Nous recuperons grace a cette boucle juste les informations qui nous interesse ID,nom , etc ...
	// EN CLAIR : Evite la redondance des données
	while($row_joueurs = $db->read_array($req_joueurs))
	{
		$i = 0;
		$arraykeys = array_keys($row_joueurs);
		foreach($row_joueurs as $value)
		{
			if (fmod($i, 2) != 0)
			{
				$nomchamp = $arraykeys[$i];
				$row_j[$j][$nomchamp] = $row_joueurs[$nomchamp];
			}
			$i++;
		}
		$j++;
	}
	
	$index = 0;
	$x_joueurs = $row_j[$index]['x'];
	$y_joueurs = $row_j[$index]['y'];
	
	//Affichage de la map
	while($row = $db->read_array($req))
	{
		$coord = convert_in_coord($row['ID']);
		$rowid = $row['ID'];
		$W_terrain_case = $row['decor'];
		
		if (($coord['x'] != 0) AND ($coord['y'] != 0))
		{
			$z = 0;
			while(($x_joueurs == $coord['x']) AND ($y_joueurs == $coord['y']))
			{
				$index++;
				if (isset($row_j[$index]['x']))
				{
					$x_joueurs = $row_j[$index]['x'];
					$y_joueurs = $row_j[$index]['y'];
				}
				else
				{
					$x_joueurs = 0;
					$y_joueurs = 0;
				}
				$z++;
			}		
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
				<optgroup label="Plaine">
					<option value="<?php echo $W_terrain_case; ?>" class="baseRow1"><?php echo $W_terrain_case; ?></option>
					<option value="101" class="baseRow1">1 : Herbe</option>
				</optgroup>
				<optgroup label="Foret">
					<option value="201" class="baseRow1">Simple</option>
					<option value="202" class="baseRow2">Touffu</option>
					<option value="203" class="baseRow1">Haut gauche</option>
					<option value="204" class="baseRow2">Gauche</option>
					<option value="205" class="baseRow1">Bas gauche</option>
					<option value="206" class="baseRow2">Bas</option>
					<option value="207" class="baseRow1">Bas droite</option>
					<option value="208" class="baseRow2">Droite</option>
					<option value="209" class="baseRow2">Haut droite</option>
					<option value="210" class="baseRow1">Haut</option>
					<option value="211" class="baseRow1">Coin haut gauche</option>
					<option value="212" class="baseRow1">Coin bas gauche</option>
					<option value="213" class="baseRow1">Coin bas droite</option>
					<option value="214" class="baseRow1">Coin haut droite</option>
				</optgroup>
				<optgroup label="Desert">
					<option value="301" class="baseRow2">Centre</option>
					<option value="302" class="baseRow1">Haut gauche</option>
					<option value="303" class="baseRow2">Gauche</option>
					<option value="304" class="baseRow1">Bas gauche</option>
					<option value="305" class="baseRow2">Bas</option>
					<option value="306" class="baseRow1">Bas droite</option>
					<option value="307" class="baseRow2">Droite</option>
					<option value="308" class="baseRow2">Haut droite</option>
					<option value="309" class="baseRow1">Haut</option>
					<option value="310" class="baseRow1">Coin haut gauche</option>
					<option value="311" class="baseRow1">Coin bas gauche</option>
					<option value="312" class="baseRow1">Coin bas droite</option>
					<option value="313" class="baseRow1">Coin haut droite</option>
					<option value="314" class="baseRow1">cactus</option>
				</optgroup>
				<optgroup label="Neige">
					<option value="401" class="baseRow2">Centre</option>
					<option value="402" class="baseRow1">Haut gauche</option>
					<option value="403" class="baseRow2">Gauche</option>
					<option value="404" class="baseRow1">Bas gauche</option>
					<option value="405" class="baseRow2">Bas</option>
					<option value="406" class="baseRow1">Bas droite</option>
					<option value="407" class="baseRow1">Droite</option>
					<option value="408" class="baseRow1">Haut droite</option>
					<option value="409" class="baseRow1">Haut</option>
				</optgroup>
				<optgroup label="Eau">
					<option value="501" class="baseRow1">Eau</option>
					<option value="502" class="baseRow1">riviere horizontale</option>
					<option value="503" class="baseRow1">riviere verticale</option>
					<option value="504" class="baseRow1">riviere bas gauche</option>
					<option value="505" class="baseRow1">riviere bas droite</option>
					<option value="506" class="baseRow1">riviere haut gauche</option>
					<option value="507" class="baseRow1">riviere haut droite</option>
					<option value="508" class="baseRow1">lac droite</option>
					<option value="509" class="baseRow1">lac gauche</option>
					<option value="510" class="baseRow1">lac haut</option>
					<option value="511" class="baseRow1">lac bas</option>
					<option value="512" class="baseRow1">lac haut gauche</option>
					<option value="513" class="baseRow1">lac haut droit</option>
					<option value="514" class="baseRow1">lac bas gauche</option>
					<option value="515" class="baseRow1">lac bas droit</option>
					<option value="516" class="baseRow1">lac coin haut gauche</option>
					<option value="517" class="baseRow1">lac coin bas gauche</option>
					<option value="518" class="baseRow1">lac coin bas droite</option>
					<option value="519" class="baseRow1">lac coin haut droite</option>
					<option value="520" class="baseRow1">embouchure gauche</option>
					<option value="521" class="baseRow1">embouchure droite</option>
					<option value="522" class="baseRow1">embouchure haut</option>
					<option value="523" class="baseRow1">embouchure bas</option>
				</optgroup>
				<optgroup label="route">
					<option value="801" class="baseRow1">pavée verticale</option>
					<option value="802" class="baseRow1">pavée horizontale</option>
					<option value="803" class="baseRow1">pavée carrefour</option>
					<option value="804" class="baseRow1">pavée croisement bas</option>
					<option value="805" class="baseRow1">pavée croisement droite</option>
					<option value="806" class="baseRow1">pavée croisement gauche</option>
					<option value="807" class="baseRow1">pavée croisement haut</option>
					<option value="808" class="baseRow1">pavée tournant n-e</option>
					<option value="809" class="baseRow1">pavée tournant n-o</option>
					<option value="810" class="baseRow1">pavée tournant s-e</option>
					<option value="811" class="baseRow1">pavée tournant s-o</option>
					<option value="812" class="baseRow1">pont pavé horizontal</option>
					<option value="813" class="baseRow1">pont pavé vertical</option>
					<option value="814" class="baseRow1">chemin vertical</option>
					<option value="815" class="baseRow1">chemin horizontal</option>
					<option value="816" class="baseRow1">pont bois vertical</option>
					<option value="817" class="baseRow1">pont bois horizontal</option>
					<option value="818" class="baseRow1">Désert verticale</option>
					<option value="819" class="baseRow1">Désert horizontale</option>
					<option value="820" class="baseRow1">Désert carrefour</option>
					<option value="821" class="baseRow1">Désert croisement bas</option>
					<option value="822" class="baseRow1">Désert croisement droite</option>
					<option value="823" class="baseRow1">Désert croisement gauche</option>
					<option value="824" class="baseRow1">Désert croisement haut</option>
					<option value="825" class="baseRow1">Désert tournant n-e</option>
					<option value="826" class="baseRow1">Désert tournant n-o</option>
					<option value="827" class="baseRow1">Désert tournant s-e</option>
					<option value="828" class="baseRow1">Désert tournant s-o</option>
				</optgroup>
				<optgroup label="Ville">
					<option value="1001" class="baseRow1">Ville humaine</option>
					<option value="1002" class="baseRow1">capitale humaine haut gauche</option>
					<option value="1003" class="baseRow1">capitale humaine haut centre</option>
					<option value="1004" class="baseRow1">capitale humaine haut droit</option>
					<option value="1005" class="baseRow1">capitale humaine bas gauche</option>
					<option value="1006" class="baseRow1">capitale humaine entrée</option>
					<option value="1007" class="baseRow1">capitale humaine bas droite</option>
					<option value="1008" class="baseRow1">cimetiere</option>
					<option value="1009" class="baseRow1">capitale vampire haut gauche</option>
					<option value="1010" class="baseRow1">capitale vampire haut centre</option>
					<option value="1011" class="baseRow1">capitale vampire haut droit</option>
					<option value="1012" class="baseRow1">capitale vampire bas gauche</option>
					<option value="1013" class="baseRow1">capitale vampire entrée</option>
					<option value="1014" class="baseRow1">capitale vampire bas droite</option>
					<option value="1015" class="baseRow1">capitale scavenger haut gauche</option>
					<option value="1016" class="baseRow1">capitale scavenger haut centre</option>
					<option value="1017" class="baseRow1">capitale scavenger haut droit</option>
					<option value="1018" class="baseRow1">capitale scavenger bas gauche</option>
					<option value="1019" class="baseRow1">capitale scavenger entrée</option>
					<option value="1020" class="baseRow1">capitale scavenger bas droite</option>
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
		<a href="edit_map2.php?ymin=<?php echo ($ymin - 10); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_nord.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_nordest.png" alt="" />
		</td>
	</tr>
	<tr>
		<td class="rose">
		<a href="edit_map2.php?xmin=<?php echo ($xmin - 10); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_ouest.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_centre.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_map2.php?xmin=<?php echo ($xmax + 1); ?>&ymin=<?php echo $ymin; ?>"><img src="image/rdv_est.png" alt="" /></a>
		</td>
	</tr>
	<tr>
		<td class="rose">
		<img src="image/rdv_sudouest.png" alt="" />
		</td>
		<td class="rose">
		<a href="edit_map2.php?ymin=<?php echo ($ymax + 1); ?>&xmin=<?php echo $xmin; ?>"><img src="image/rdv_sud.png" alt="" /></a>
		</td>
		<td class="rose">
		<img src="image/rdv_sudest.png" alt="" />
		</td>
	</tr>
	</table>
	</div>
	<a href="view_map.php">Map Globale</a>
	<div style="margin-top : 650px; text-align : center;">
		Starshine Editeur v2.0
	</div>
</body>
</html>