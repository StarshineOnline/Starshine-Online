<?php
if (file_exists('../root.php'))
  include_once('../root.php');


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
if($ymin <= 190) {
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
				<optgroup label="Plaine">
					<option value="101" class="baseRow1">Herbe</option>
					<option value="102" class="baseRow1">Herbe fleur</option>
					<option value="103" class="baseRow1">Buisson</option>		
					<option value="104" class="baseRow1">Arbre 1</option>
					<option value="105" class="baseRow1">Arbre 2</option>
					<option value="106" class="baseRow1">Herbe Haute Bas Gauche</option>
					<option value="107" class="baseRow1">Herbe Haute Bas Milieu</option>
					<option value="108" class="baseRow1">Herbe Haute Bas Droite</option>		
					<option value="109" class="baseRow1">Herbe Haute Haut Droite</option>
					<option value="110" class="baseRow1">Herbe Haute Haut Milieu</option>
					<option value="111" class="baseRow1">Herbe Haute Haut Gauche</option>
					<option value="116" class="baseRow1">Herbe Haute Diag Bas Droit</option>
					<option value="117" class="baseRow1">Herbe Haute Diag Bas Gauche</option>
					<option value="118" class="baseRow1">Herbe Haute Diag Haut Droit</option>
					<option value="119" class="baseRow1">Herbe Haute Diag Haut Gauche</option>
					<option value="120" class="baseRow1">Herbe Haute Centre</option>
					<option value="121" class="baseRow1">hautherbe_jonction_horid</option>
					<option value="122" class="baseRow1">hautherbe_jonction_horig</option>
					<option value="123" class="baseRow1">hautherbe_jonction_vertib</option>
					<option value="124" class="baseRow1">hautherbe_jonction_vertih</option>
					<option value="125" class="baseRow1">Arbre 2</option>
					<option value="126" class="baseRow1">Arbre 2</option>
					<option value="127" class="baseRow1">Arbre 2</option>
					<option value="128" class="baseRow1">Arbre 2</option>
					<option value="129" class="baseRow1">Arbre 2</option>
					<option value="130" class="baseRow1">Arbre 2</option>
					<option value="131" class="baseRow1">Arbre 2</option>
					<option value="132" class="baseRow1">Arbre 2</option>
					<option value="133" class="baseRow1">Arbre 2</option>
					<option value="134" class="baseRow1">Arbre 2</option>
					<option value="135" class="baseRow1">Arbre 2</option>
					<option value="136" class="baseRow1">Arbre 2</option>
					<option value="137" class="baseRow1">Arbre 2</option>
					<option value="138" class="baseRow1">Arbre 2</option>
					<option value="139" class="baseRow1">Arbre 2</option>
					<option value="140" class="baseRow1">Arbre 2</option>
					<option value="141" class="baseRow1">Arbre 2</option>
					<option value="142" class="baseRow1">Arbre 2</option>
					<option value="143" class="baseRow1">Arbre 2</option>
					<option value="144" class="baseRow1">Arbre 2</option>
					<option value="145" class="baseRow1">Arbre 2</option>
					<option value="146" class="baseRow1">Arbre 2</option>
					<option value="147" class="baseRow1">Arbre 2</option>
					<option value="148" class="baseRow1">Arbre 2</option>
					<option value="149" class="baseRow1">Arbre 2</option>
					<option value="150" class="baseRow1">Arbre 2</option>
					<option value="151" class="baseRow1">Arbre 2</option>
					<option value="152" class="baseRow1">Arbre 2</option>
					<option value="153" class="baseRow1">Arbre 2</option>
					<option value="154" class="baseRow1">Arbre 2</option>
					<option value="155" class="baseRow1">Arbre 2</option>
					<option value="156" class="baseRow1">Arbre 2</option>
					<option value="157" class="baseRow1">Arbre 2</option>
					<option value="158" class="baseRow1">Arbre 2</option>
					<option value="159" class="baseRow1">Arbre 2</option>
					<option value="160" class="baseRow1">Arbre 2</option>
					<option value="161" class="baseRow1">Arbre 2</option>
					<option value="162" class="baseRow1">Arbre 2</option>
					<option value="163" class="baseRow1">Arbre 2</option>
					<option value="164" class="baseRow1">Arbre 2</option>
					<option value="165" class="baseRow1">Arbre 2</option>
					<option value="166" class="baseRow1">Arbre 2</option>
					<option value="167" class="baseRow1">Arbre 2</option>
					<option value="168" class="baseRow1">Arbre 2</option>
					<option value="169" class="baseRow1">Arbre 2</option>
					<option value="170" class="baseRow1">Arbre 2</option>
					<option value="171" class="baseRow1">Arbre 2</option>
					<option value="172" class="baseRow1">Arbre 2</option>
					<option value="173" class="baseRow1">Arbre 2</option>
					<option value="174" class="baseRow1">Arbre 2</option>
					<option value="175" class="baseRow1">Arbre 2</option>
					<option value="176" class="baseRow1">Arbre 2</option>
					<option value="177" class="baseRow1">Arbre 2</option>
					<option value="178" class="baseRow1">Arbre 2</option>
					<option value="179" class="baseRow1">Arbre 2</option>
					<option value="180" class="baseRow1">Arbre 2</option>
					<option value="181" class="baseRow1">Arbre 2</option>
					<option value="182" class="baseRow1">Arbre 2</option>
					<option value="183" class="baseRow1">Arbre 2</option>
					<option value="184" class="baseRow1">Arbre 2</option>
					
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
					<option value="211" class="baseRow2">Coin haut gauche</option>
					<option value="212" class="baseRow1">Coin bas gauche</option>
					<option value="213" class="baseRow2">Coin bas droite</option>
					<option value="214" class="baseRow1">Coin haut droite</option>
					<option value="215" class="baseRow2">Coin bas droite</option>
					<option value="216" class="baseRow1">Coin haut droite</option>
					<option value="217" class="baseRow2">Coin bas droite</option>
					<option value="218" class="baseRow1">Coin haut droite</option>

					
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
					<option value="310" class="baseRow2">Coin haut gauche</option>
					<option value="311" class="baseRow1">Coin bas gauche</option>
					<option value="312" class="baseRow2">Coin bas droite</option>
					<option value="313" class="baseRow1">Coin haut droite</option>
					<option value="314" class="baseRow2">cactus</option>
					<option value="315" class="baseRow1">Oasis is good</option>		
					<option value="316" class="baseRow2">cactus</option>
					<option value="317" class="baseRow1">Oasis is good</option>					
					<option value="318" class="baseRow2">cactus</option>
					<option value="319" class="baseRow1">Oasis is good</option>					
					<option value="320" class="baseRow2">cactus</option>
					<option value="321" class="baseRow1">Oasis is good</option>					
					<option value="322" class="baseRow2">cactus</option>
					<option value="323" class="baseRow1">Oasis is good</option>					
					<option value="324" class="baseRow2">cactus</option>
					<option value="325" class="baseRow1">Oasis is good</option>					
					<option value="326" class="baseRow2">cactus</option>
					<option value="327" class="baseRow1">Oasis is good</option>					
					<option value="328" class="baseRow2">cactus</option>
					<option value="329" class="baseRow1">Oasis is good</option>					
					<option value="330" class="baseRow2">cactus</option>
					<option value="331" class="baseRow1">Oasis is good</option>					
					<option value="332" class="baseRow2">cactus</option>
					<option value="333" class="baseRow1">Oasis is good</option>					
				</optgroup>
				<optgroup label="Neige">

					<option value="401" class="baseRow2">tex_neige1</option>
					<option value="402" class="baseRow1">tex_banquiseno</option>
					<option value="403" class="baseRow2">tex_banquiseo</option>
					<option value="404" class="baseRow1">tex_banquiseso</option>
					<option value="405" class="baseRow2">tex_banquises</option>
					<option value="406" class="baseRow1">tex_banquisese</option>
					<option value="407" class="baseRow2">tex_banquisee</option>
					<option value="408" class="baseRow1">tex_banquisene</option>
					<option value="409" class="baseRow2">tex_banquisen</option>
					<option value="410" class="baseRow1">tex_banquiseint1</option>
					<option value="411" class="baseRow2">tex_banquiseint2</option>
					<option value="412" class="baseRow1">tex_banquiseint3</option>
					<option value="413" class="baseRow2">tex_banquiseint4</option>
					<option value="414" class="baseRow1">tex_neigeherbecoinne</option>					
					<option value="415" class="baseRow2">tex_neigeherbecoinno</option>
					<option value="416" class="baseRow1">tex_neigeherbecoinse</option>
					<option value="417" class="baseRow2">tex_neigeherbecoinso</option>
					<option value="418" class="baseRow1">tex_neigeherbee</option>
					<option value="419" class="baseRow2">tex_neigeherben</option>
					<option value="420" class="baseRow1">tex_neigeherbeo</option>
					<option value="421" class="baseRow2">tex_neigeherbes</option>
					<option value="422" class="baseRow1">tex_neigeherbene</option>										
					<option value="423" class="baseRow2">tex_neigeherbeno</option>
					<option value="424" class="baseRow1">tex_neigeherbese</option>
					<option value="425" class="baseRow2">tex_neigeherbesoinso</option>
					<option value="426" class="baseRow1">textbanquisegele_bm</option>	
					<option value="427" class="baseRow2">textbanquisegele_bd</option>	
					<option value="428" class="baseRow1">textbanquisegele_hg</option>
					<option value="429" class="baseRow2">textbanquisegele_hm</option>
					<option value="430" class="baseRow1">textbanquisegele_hd</option>	
					<option value="431" class="baseRow2">textbanquisegele_md</option>						
					<option value="432" class="baseRow1">textbanquisegele_mg</option>
					<option value="433" class="baseRow2">textbanquisegele_mg2</option>
					<option value="434" class="baseRow1">textbanquisegele_centre</option>	
					<option value="435" class="baseRow2">textgelebanquise_bd</option>	
					<option value="436" class="baseRow1">textgelebanquise_bg</option>
					<option value="437" class="baseRow2">textgelebanquise_hd</option>
					<option value="438" class="baseRow1">textgelebanquise_hg</option>	
					<option value="439" class="baseRow2">jointgelebanqvertidroit</option>	
					<option value="440" class="baseRow1">jointgelebanqvertigauche</option>
					<option value="441" class="baseRow2">jointgelebanqhorizdroit</option>
					<option value="442" class="baseRow1">jointgelebanqhorizgauche</option>	
					<option value="443" class="baseRow2">textbanquisegele_diago_bd</option>
					
					<option value="444" class="baseRow1">textbanquisegele_diago_bg</option>
					<option value="445" class="baseRow2">banqgelefissure1</option>
					<option value="446" class="baseRow1">banqgelefissure2</option>	
					<option value="447" class="baseRow2">banqgele_centre1</option>	
					<option value="448" class="baseRow1">banqgele_centre2</option>
					<option value="449" class="baseRow2">banqgele_centre3</option>
					<option value="450" class="baseRow1">banqgeleperso</option>	
										
					<option value="451" class="baseRow2">textbanquisegele_bg2</option>	
					<option value="452" class="baseRow1">textbanquisegele_bm2</option>
					<option value="453" class="baseRow2">textbanquisegele_bd2</option>
					<option value="454" class="baseRow1">textbanquisegele_hg2</option>	
					<option value="455" class="baseRow2">textbanquisegele_hm2</option>	
					<option value="456" class="baseRow1">textbanquisegele_hd2</option>
					<option value="457" class="baseRow2">textbanquisegele_md2</option>
					
					<option value="458" class="baseRow1">textbanquisegele_centre2</option>	
					<option value="459" class="baseRow2">textgelebanquise_bd2</option>	
					<option value="460" class="baseRow1">textgelebanquise_bg2</option>
					<option value="461" class="baseRow2">textgelebanquise_hd2</option>
					<option value="462" class="baseRow1">textgelebanquise_hg2</option>	
					<option value="463" class="baseRow2">jointgelebanqvertidroit2</option>	
					
					<option value="464" class="baseRow1">jointgelebanqvertigauche2</option>
					<option value="465" class="baseRow2">jointgelebanqhorizdroit2</option>
					<option value="466" class="baseRow1">jointgelebanqhorizgauche2</option>	
					<option value="467" class="baseRow2">textbanquisegele_diago bd2</option>	
					<option value="468" class="baseRow1">textbanquisegele_diago bg2</option>
					<option value="469" class="baseRow2">tex_montneige3</option>
					<option value="470" class="baseRow1">tex_montneige9</option>	
					<option value="471" class="baseRow2">tex_montneige5</option>	
					<option value="472" class="baseRow1">tex_montneige6</option>
					<option value="473" class="baseRow2">tex_montneige7</option>
					<option value="474" class="baseRow1">tex_montneige8</option>	
					<option value="475" class="baseRow2">tex_montneige2</option>
					<option value="476" class="baseRow1">tex_montneige10</option>
					<option value="477" class="baseRow2">tex_montneige11</option>
					<option value="478" class="baseRow1">tex_montneige3</option>	
					<option value="479" class="baseRow2">tex_montneige4</option>	
					
				</optgroup>
				
				<optgroup label="Terre Maudite">
					<option value="1101" class="baseRow2">Centre</option>
					<option value="1102" class="baseRow1">Haut gauche</option>
					<option value="1103" class="baseRow2">Gauche</option>
					<option value="1104" class="baseRow1">Bas gauche</option>
					<option value="1105" class="baseRow2">Bas</option>
					<option value="1106" class="baseRow1">Bas droite</option>
					<option value="1107" class="baseRow2">Droite</option>
					<option value="1108" class="baseRow2">Haut droite</option>
					<option value="1109" class="baseRow1">Haut</option>
					<option value="1110" class="baseRow1">Coin haut gauche</option>
					<option value="1111" class="baseRow1">Coin bas gauche</option>
					<option value="1112" class="baseRow1">Coin bas droite</option>
					<option value="1113" class="baseRow1">Coin haut droite</option>
				</optgroup>
				<optgroup label="Terre Maudite forêt">
				<option value="1114" class="baseRow2">simple</option>
				<option value="1115" class="baseRow2">Centre touffu</option>
				<option value="1116" class="baseRow2">haut-gauche</option>
				<option value="1117" class="baseRow2">gauche</option>
				<option value="1118" class="baseRow2">bas-gauche</option>
				<option value="1119" class="baseRow2">Bas</option>
				<option value="1120" class="baseRow2">bas-droit</option>
				<option value="1121" class="baseRow2">droite</option>
				<option value="1122" class="baseRow2">haut-droite</option>
				<option value="1123" class="baseRow2">haut</option>
				<option value="1124" class="baseRow2">coin haut-gauche</option>
				<option value="1125" class="baseRow2">coin bas-gauche</option>
				<option value="1126" class="baseRow2">coin bas-droit</option>
				<option value="1127" class="baseRow2">doin haut-droit</option>
				<option value="1128" class="baseRow2">grave</option>
				<option value="1129" class="baseRow2">blood</option>
										    
				</optgroup>				
				
				<optgroup label="Montagne Neige">
					<option value="426" class="baseRow2">Centre</option>
					<option value="427" class="baseRow1">Haut gauche</option>
					<option value="428" class="baseRow2">Gauche</option>
					<option value="429" class="baseRow1">Bas gauche</option>
					<option value="430" class="baseRow2">Bas</option>
					<option value="431" class="baseRow1">Bas droite</option>
					<option value="432" class="baseRow1">Droite</option>
					<option value="433" class="baseRow1">Haut droite</option>
					<option value="434" class="baseRow1">Haut</option>
					<option value="435" class="baseRow1">Gauche droite</option>
					<option value="436" class="baseRow1">Haut bas</option>
					
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
					<option value="524" class="baseRow1">embouchure haut</option>
					<option value="525" class="baseRow1">embouchure bas</option>
					<option value="526" class="baseRow1">embouchure haut</option>
					<option value="527" class="baseRow1">embouchure bas</option>
					
				</optgroup>
				<optgroup label="Montagne">
					<option value="601" class="baseRow2">Centre1</option>
					<option value="602" class="baseRow2">Centre2</option>
					<option value="603" class="baseRow2">Centre3</option>
					<option value="604" class="baseRow2">Centre4</option>
					<option value="605" class="baseRow2">Centre5</option>
					<option value="606" class="baseRow2">Centre6</option>
					<option value="607" class="baseRow2">Centre7</option>
					<option value="608" class="baseRow2">Centre8</option>
					<option value="609" class="baseRow2">Centre9</option>
					<option value="610" class="baseRow2">Centre10</option>
					<option value="611" class="baseRow2">Centre11</option>
					<option value="612" class="baseRow1">Centre12</option>
					<option value="613" class="baseRow2">Centre</option>
					<option value="614" class="baseRow1">Centre12</option>
					<option value="615" class="baseRow2">Centre</option>
					<option value="616" class="baseRow1">Centre12</option>
					<option value="617" class="baseRow2">Centre</option>
					<option value="618" class="baseRow1">Centre12</option>
					<option value="619" class="baseRow2">Centre</option>
					<option value="620" class="baseRow1">Centre12</option>
					<option value="621" class="baseRow2">Centre</option>
					<option value="622" class="baseRow1">Centre12</option>
					
				</optgroup>
				
				<optgroup label="Montagne eau">
					<option value="640" class="baseRow2">Centre1</option>
					<option value="641" class="baseRow2">Centre2</option>
					<option value="642" class="baseRow2">Centre3</option>
					<option value="643" class="baseRow2">Centre4</option>
					<option value="644" class="baseRow2">Centre5</option>
					<option value="645" class="baseRow2">Centre6</option>
					<option value="646" class="baseRow2">Centre7</option>
					<option value="647" class="baseRow2">Centre8</option>
					<option value="648" class="baseRow2">Centre9</option>
					<option value="649" class="baseRow2">Centre10</option>
					<option value="650" class="baseRow2">Centre11</option>
					<option value="651" class="baseRow2">Centre12</option>
					<option value="652" class="baseRow2">Centre13</option>
					<option value="653" class="baseRow2">Centre14</option>
					<option value="654" class="baseRow2">Centre12</option>
					<option value="655" class="baseRow2">Centre13</option>
					<option value="656" class="baseRow2">Centre14</option>					
					
					
				</optgroup>
				

				<optgroup label="Marais">
					<option value="701" class="baseRow2">Centre</option>
					<option value="702" class="baseRow2">Haut gauche</option>
					<option value="703" class="baseRow2">Gauche</option>
					<option value="704" class="baseRow2">Bas gauche</option>
					<option value="705" class="baseRow2">Bas</option>
					<option value="706" class="baseRow2">Bas droite</option>
					<option value="707" class="baseRow2">Droite</option>
					<option value="708" class="baseRow2">Haut</option>
					<option value="709" class="baseRow2">Haut droite</option>
					<option value="710" class="baseRow2">Nenu</option>
					<option value="711" class="baseRow2">Nenu 2</option>
					<option value="712" class="baseRow2">Coin bas gauche</option>
					<option value="713" class="baseRow2">Coin bas droite</option>
					<option value="714" class="baseRow1">Coin haut gauche</option>
					<option value="715" class="baseRow2">Coin haut droite</option>
					<option value="716" class="baseRow1">Coin haut gauche</option>
					<option value="717" class="baseRow2">Coin haut droite</option>
					<option value="718" class="baseRow1">Coin haut gauche</option>
					<option value="719" class="baseRow2">Coin haut droite</option>
					<option value="720" class="baseRow1">Coin haut gauche</option>
					<option value="721" class="baseRow2">Coin haut droite</option>
					<option value="722" class="baseRow1">Coin haut gauche</option>
					<option value="723" class="baseRow2">Coin haut droite</option>
					<option value="724" class="baseRow1">Coin haut gauche</option>
					<option value="725" class="baseRow2">Coin haut droite</option>
					<option value="726" class="baseRow1">Coin haut gauche</option>
					<option value="727" class="baseRow2">Coin haut droite</option>
					<option value="728" class="baseRow1">Coin haut gauche</option>
					<option value="729" class="baseRow2">Coin haut droite</option>
					<option value="730" class="baseRow1">Coin haut gauche</option>
					<option value="731" class="baseRow2">Coin haut droite</option>
					<option value="732" class="baseRow1">Coin haut gauche</option>
					<option value="733" class="baseRow2">Coin haut droite</option>
					<option value="734" class="baseRow1">Coin haut gauche</option>
					<option value="735" class="baseRow2">Coin haut droite</option>
					<option value="736" class="baseRow1">Coin haut gauche</option>
					<option value="737" class="baseRow2">Coin haut droite</option>
					<option value="738" class="baseRow1">Coin haut gauche</option>
					<option value="739" class="baseRow2">Coin haut droite</option>
					
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
					<option value="829" class="baseRow1">Neige verticale</option>
					<option value="830" class="baseRow1">Neige horizontale</option>
					<option value="831" class="baseRow1">Neige carrefour</option>
					<option value="832" class="baseRow1">Neige croisement bas</option>
					<option value="833" class="baseRow1">Neige croisement droite</option>
					<option value="834" class="baseRow1">Neige croisement gauche</option>
					<option value="835" class="baseRow1">Neige croisement haut</option>
					<option value="836" class="baseRow1">Neige tournant n-e</option>
					<option value="837" class="baseRow1">Neige tournant n-o</option>
					<option value="838" class="baseRow1">Neige tournant s-e</option>
					<option value="839" class="baseRow1">Neige tournant s-o</option>
					<option value="849" class="baseRow1">Liaison neige herbe Route ns</option>	
					<option value="839" class="baseRow1">Neige tournant s-o</option>
					<option value="840" class="baseRow1">Pont neige gauche</option>
					<option value="841" class="baseRow1">Pont neige droite</option>
					<option value="842" class="baseRow1">Pont neige haut</option>
					<option value="843" class="baseRow1">Pont neige bas</option>
					<option value="844" class="baseRow1">Liaison Pont neige droite</option>
					<option value="845" class="baseRow1">Liaison pont neige droite</option>
					<option value="846" class="baseRow1">Liaison pont neige haut</option>
					<option value="847" class="baseRow1">Liaison pont neige bas</option>
					<option value="860" class="baseRow1">Route neige herbe</option>		
					<option value="848" class="baseRow1">Marais verticale</option>
					<option value="849" class="baseRow1">Marais horizontale</option>
					<option value="850" class="baseRow1">Marais carrefour</option>
					<option value="851" class="baseRow1">Marais croisement bas</option>
					<option value="852" class="baseRow1">Marais croisement droite</option>
					<option value="853" class="baseRow1">Marais croisement gauche</option>
					<option value="854" class="baseRow1">Marais croisement haut</option>
					<option value="855" class="baseRow1">Marais tournant n-e</option>
					<option value="856" class="baseRow1">Marais tournant n-o</option>
					<option value="857" class="baseRow1">Marais tournant s-e</option>
					<option value="858" class="baseRow1">Marais tournant s-o</option>					
					<option value="859" class="baseRow1">Marais Herbe</option>		
					<option value="861" class="baseRow1">Maudites carrefour</option>					
					<option value="862" class="baseRow1">Maudites tournant n-e</option>
					<option value="863" class="baseRow1">Maudites tournant n-o</option>
					<option value="864" class="baseRow1">Maudites tournant s-e</option>
					<option value="865" class="baseRow1">Maudites tournant s-o</option>		
					<option value="866" class="baseRow1">Maudites horizon</option>							
					<option value="867" class="baseRow1">Maudites verticale</option>	
					<option value="868" class="baseRow1">Liaison desert-route pavéeNS</option>
					<option value="869" class="baseRow1">Liaison Chemin-route pavée</option>
					<option value="870" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="871" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="872" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="873" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="874" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="875" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="876" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="877" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="878" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="879" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="880" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="881" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="882" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="883" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="884" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="885" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="886" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="887" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="888" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="889" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="890" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="891" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="892" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="893" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="894" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="895" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="896" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="897" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="898" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="899" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="900" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="901" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="902" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="903" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="904" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="905" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="906" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="907" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="908" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="909" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="910" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					<option value="911" class="baseRow2">Liaison desert-route pavéeEO </option>					
					<option value="912" class="baseRow1">Liaison Route pavée Herbe-TM</option>	
					
					
				</optgroup>
				<optgroup label="Capitale">
					<option value="1001" class="baseRow1">Ville humaine</option>
					<option value="1002" class="baseRow1">capitale defaut haut gauche</option>
					<option value="1003" class="baseRow1">capitale defaut haut centre</option>
					<option value="1004" class="baseRow1">capitale defaut haut droit</option>
					<option value="1005" class="baseRow1">capitale defaut bas gauche</option>
					<option value="1006" class="baseRow1">capitale defaut entrée</option>
					<option value="1007" class="baseRow1">capitale defaut bas droite</option>
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
					<option value="1021" class="baseRow1">capitale barbare haut gauche</option>
					<option value="1022" class="baseRow1">capitale barbare haut centre</option>
					<option value="1023" class="baseRow1">capitale barbare haut droit</option>
					<option value="1024" class="baseRow1">capitale barbare bas gauche</option>
					<option value="1025" class="baseRow1">capitale barbare entrée</option>
					<option value="1026" class="baseRow1">capitale barbare bas droite</option>
					<option value="1027" class="baseRow1">capitale Undead haut gauche</option>
					<option value="1028" class="baseRow1">capitale Undead haut centre</option>
					<option value="1029" class="baseRow1">capitale Undead haut droit</option>
					<option value="1030" class="baseRow1">capitale Undead bas gauche</option>
					<option value="1031" class="baseRow1">capitale Undead entrée</option>
					<option value="1032" class="baseRow1">capitale Undead bas droite</option>
					<option value="1033" class="baseRow1">capitale Haut elfe</option>
					<option value="1034" class="baseRow1">capitale Haut elfe</option>
					<option value="1035" class="baseRow1">capitale Haut elfe</option>
					<option value="1036" class="baseRow1">capitale Haut elfe</option>
					<option value="1037" class="baseRow1">capitale Haut elfe</option>
					<option value="1038" class="baseRow1">capitale Haut elfe</option>
					<option value="1039" class="baseRow1">capitale Troll</option>
					<option value="1040" class="baseRow1">capitale Troll</option>
					<option value="1041" class="baseRow1">capitale Troll</option>
					<option value="1042" class="baseRow1">capitale Troll</option>
					<option value="1043" class="baseRow1">capitale Troll</option>
					<option value="1044" class="baseRow1">capitale Troll</option>
					<option value="1045" class="baseRow1">capitale Orc</option>
					<option value="1046" class="baseRow1">capitale Orc</option>
					<option value="1047" class="baseRow1">capitale Orc</option>
					<option value="1048" class="baseRow1">capitale Orc</option>
					<option value="1049" class="baseRow1">capitale Orc</option>
					<option value="1050" class="baseRow1">capitale Orc</option>
					<option value="1051" class="baseRow1">capitale Edb</option>
					<option value="1052" class="baseRow1">capitale Edb</option>
					<option value="1053" class="baseRow1">capitale Edb</option>
					<option value="1054" class="baseRow1">capitale Edb</option>
					<option value="1055" class="baseRow1">capitale Edb</option>
					<option value="1056" class="baseRow1">capitale Edb</option>
					<option value="1057" class="baseRow1">capitale Corrompu</option>
					<option value="1058" class="baseRow1">capitale Corrompu</option>
					<option value="1059" class="baseRow1">capitale Corrompu</option>
					<option value="1060" class="baseRow1">capitale Corrompu</option>
					<option value="1061" class="baseRow1">capitale Corrompu</option>
					<option value="1062" class="baseRow1">capitale Corrompu</option>
					<option value="1063" class="baseRow1">capitale Humain</option>
					<option value="1064" class="baseRow1">capitale Humain</option>
					<option value="1065" class="baseRow1">capitale Humain</option>
					<option value="1066" class="baseRow1">capitale Humain</option>
					<option value="1067" class="baseRow1">capitale Humain</option>
					<option value="1068" class="baseRow1">capitale Humain</option>
				</optgroup>
				<optgroup label="Donjon">
					<option value="1601" class="baseRow1">Mur</option>
					<option value="1501" class="baseRow2">Sol</option>
					<option value="1510" class="baseRow1">Sol panneau</option>
					<option value="1511" class="baseRow2">Sol grande flaque sang</option>
					<option value="1512" class="baseRow1">Sol petite flaque sang</option>
					<option value="1513" class="baseRow2">Sol squelette</option>
				</optgroup>
				<optgroup label="Donjon Gobelin">
					<option value="1516" class="baseRow1">Sol</option>
					<option value="1517" class="baseRow2">Mur N</option>
					<option value="1518" class="baseRow1">Mur S</option>
					<option value="1519" class="baseRow2">Mur O</option>
					<option value="1520" class="baseRow1">Mur E</option>
					<option value="1521" class="baseRow2">Sol flaque sang</option>
					<option value="1522" class="baseRow2">Sol squelette</option>
					<option value="1523" class="baseRow1">Mur SO</option>
					<option value="1524" class="baseRow2">Mur NE</option>
					<option value="1525" class="baseRow1">Mur SE</option>
					<option value="1526" class="baseRow2">Mur NO</option>
					<option value="1618" class="baseRow1">Mur plein</option>
					<option value="1617" class="baseRow2">Mur prison</option>
					<option value="1630" class="baseRow1">Rivière</option>
					<option value="872" class="baseRow2">Pont</option>
					<option value="1640" class="baseRow1">Prison - H</option>
					<option value="1641" class="baseRow2">Prison - V</option>
					<option value="1642" class="baseRow1">Prison - Angle</option>
				</optgroup>
				<optgroup lavel="Jungle">
					<option value="215" class="baseRow1">Jungle 1</option>
					<option value="216" class="baseRow2">Jungle 2</option>
					<option value="217" class="baseRow1">Jungle 3</option>
					<option value="218" class="baseRow2">Jungle 4</option>
					<option value="219" class="baseRow1">Jungle 5</option>
					<option value="220" class="baseRow2">Jungle 6</option>
					<option value="221" class="baseRow1">Jungle 7</option>
					<option value="222" class="baseRow2">Jungle 8</option>
					<option value="223" class="baseRow2">Jungle 9</option>
					<option value="224" class="baseRow1">Jungle 10</option>
					<option value="225" class="baseRow1">Jungle 11</option>
					<option value="226" class="baseRow1">Jungle 12</option>
					<option value="227" class="baseRow1">Jungle 13</option>
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