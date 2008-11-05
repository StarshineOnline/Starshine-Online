<?php
include('haut.php');

//On sort de la bdd les coordonnés du joueur x,y
$viewer = recupperso($_SESSION['ID']);
$arene_masters = array('irulan', 'azeroth', 'mylok','minus');
if (in_array(strtolower($viewer['nom']), $arene_masters))
{
	$admin = true;
}
else $admin = false;

if(array_key_exists('arene', $_GET))
{
	$arenenom = $_GET['arene'];
}
else
{
	$arenenom = 'Glace';
}
if($_SESSION['ID'] == '') die('Il faut être connecté pour voir les arènes');
$requete = "SELECT id FROM arenes_joueurs WHERE id = ".$_SESSION['ID'];
$req = $db->query($requete);
if($db->num_rows($req) > 0) die('noob');


//Récupérations des informations pour l'affichage des joueurs dans l'arène ainsi que l'arène
$fichier_arene = new DomDocument();
$fichier_arene->load('./xml/arenes.xml');
$arene_xml = $fichier_arene->getElementsByTagName('viewarene');

//Choix de l'arène
foreach($arene_xml as $type_arene)
{
	if( !strcmp($type_arene->getAttribute('type'), $arenenom) )
	{
		$liste_joueurs = $type_arene->getElementsByTagName('joueur');
		$liste_cases = $type_arene->getElementsByTagName('case');
		$taille_arene = $type_arene->getElementsByTagName('taille')->item(0);
		$xmin = $taille_arene->getAttribute('xmin');
		$xmin = $taille_arene->getAttribute('xmax');
		$xmin = $taille_arene->getAttribute('ymin');
		$xmin = $taille_arene->getAttribute('xmax');
	}
}
$coord['x'] = $xmin + round(($xmax - $xmin) / 2);
$coord['y'] = $ymin + round(($ymax - $ymin) / 2);

?>
	<div id="carte" style='width:645px !important;'>
	<ul id='map_bord_haut'>
	<li id='map_bord_haut_gauche'>&nbsp;</li>
	<?php
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		if($coord['x'] == $i) $color = 'black; font-weight : bold';
		else $color = 'white';
		echo '<li id="bord_haut_x">'.$i.'</li>';
	}
	echo "</ul>";
	$x = 0;
	$y = 0;
	
	$row_map = array();
	$index = 0;
	foreach($liste_cases as $case)
	{
		$row_map[$index]['ID'] = $case->getAttribute('id');
		$row_map[$index]['decor'] = $case->getAttribute('decor');
		$index++;
	}
	
	
	$row_j = array();
	$index = 0;
	foreach($liste_joueur as $joueur)
	{
		$row_j[$index]['nom'] = $joueur->getAttribute('nom');
		$row_j[$index]['hp'] = $joueur->getAttribute('hp');
		$row_j[$index]['mp'] = $joueur->getAttribute('mp');
		$row_j[$index]['pa'] = $joueur->getAttribute('pa');
		$row_j[$index]['x'] = $joueur->getAttribute('x');
		$row_j[$index]['y'] = $joueur->getAttribute('y');
		$row_j[$index]['hpmax'] = $joueur->getAttribute('hpmax');
		$row_j[$index]['classe'] = $joueur->getAttribute('classe');
		$row_j[$index]['level'] = $joueur->getAttribute('lvl');
		$index++;
	}

	$index = 0;
	$index_m = 0;
	$x_joueurs = $row_j[$index]['x'];
	$y_joueurs = $row_j[$index]['y'];

	//Affichage de la map
	$roy = 0;
	$race = array();
	while( ($row = current($row_map)) !== false)
	{
		$coord = convert_in_coord($row['ID']);
		$rowid = $row['ID'];
		$W_terrain_case = $row['decor'];
		
		if (($coord['x'] != 0) AND ($coord['y'] != 0))
		{
			$case['information'] = '';
			
			$z = 0;
			//affichage des joueurs
			while(($x_joueurs == $coord['x']) AND ($y_joueurs == $coord['y']))
			{
				//$info[$rowid][$z]['ID'] = $row_j[$index]['ID'];
				$info[$rowid][$z]['nom'] = $row_j[$index]['nom'];
				$info[$rowid][$z]['race'] = $row_j[$index]['race'];
				$info[$rowid][$z]['classe'] = $row_j[$index]['classe'];
				$info[$rowid][$z]['hp'] = $row_j[$index]['hp'];
				$info[$rowid][$z]['mp'] = $row_j[$index]['mp'];
				$info[$rowid][$z]['pa'] = $row_j[$index]['pa'];
				$info[$rowid][$z]['level'] = $row_j[$index]['level'];
				$race[$row_j[$index]['race']]['hp'] += $row_j[$index]['hp'];
				$race[$row_j[$index]['race']]['hpmax'] += floor($row_j[$index]['hp_max']);
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
				if ($z > 0) $case['information'] .= '<br />';
				$case['information'] .= '<strong>'.htmlspecialchars($info[$rowid][$z]['nom']).'</strong> - '.$Gtrad[$info[$rowid][$z]['race']].' - Niv '.$info[$rowid][$z]['level'];
				if($admin) $case['information'] .= ' - HP : '.$info[$rowid][$z]['hp'].' - MP : '.$info[$rowid][$z]['mp'].' - PA : '.$info[$rowid][$z]['pa'];
		
				$z++;
			}

			$case['infoj'] = '';
			$case['info'] = '';
			$case['infoj'] = '';
			$case['infob'] = '';

			if (isset($info[$rowid]))
			{
				$case['info'] = $info[$rowid][0]['race'];
				$case['info_classe'] = $Tclasse[$info[$rowid][0]['classe']]['type'];
				$case['type'] = 'joueur';
			}
			if ($coord['y'] != $y)
			{
				echo '</ul>
				<ul class="map">
					<li id="bord_haut_y" class="map_bord_gauche">
						'.$coord['y'].'
					</li>';
					if ($case['information'] != '') $on = 'onmouseover="'.make_overlib($case['information']).'" onmouseout="nd();"';
					else $on = '';
					echo '<li '.$on.' class="decor tex'.$W_terrain_case.'">';
					if ($case['info'] != '')
					{
						$rep_type = 'personnage/'.$case['info'];
						$image = 'image/'.$rep_type.'/'.$case['info'];
						//Vérification image classe
						if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
						elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
						elseif (file_exists($image.'.png')) $image .= '.png';
						else $image .= '.gif';
						if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
						echo'<img src="'.$image.'" alt="'.$case['info'].'" style="vertical-align : middle; margin:10px;" '.$on.'/>';
					}

				$y = $coord['y'];
			}
			else
			{
				if ($case['information'] != '') $on = 'onmouseover="'.make_overlib($case['information']).'" onmouseout="nd();"';
				else $on = '';
				echo '<li '.$on.' class="decor tex'.$W_terrain_case.'">';

				if ($case['info'] != '')
				{
					$rep_type = 'personnage/'.$case['info'];
					$image = 'image/'.$rep_type.'/'.$case['info'];
					if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
					elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
					elseif (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
					if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<img src="'.$image.'" alt="'.$case['info'].'" style="vertical-align : middle; margin : 10px;" '.$on.'/>';
				}
			}
			?>
			</li>
			<?php
		}
		$roy++;
		next($row_map);
	}
	
	?>
	</ul>
	</div>
	<div id="classement" style="float : left;">
	<?php
	echo "<ul>";
	foreach($race as $nom => $r)
	{
		echo '<li><strong>'.$nom.'</strong> : '.(ceil(($r['hp'] / $r['hpmax']) * 10000) / 100).'%</li>';
	}
	echo "</ul>
	<div style='padding:6px;'>
	<strong>Switch arène :</strong><br />";
	$requete = "SELECT * FROM arenes";
	$req = $db->query($requete);
	if ($db->num_rows > 0) {
		while ($R_arene = $db->read_assoc($req)) {
			echo '<a href="viewarene.php?arene='.$R_arene['nom'].'">'.$R_arene['nom']."</a><br />";
		}
	}	
	echo "</div>";
	?>
	</div>
	<div id="pub" style="float:left;padding:10px;">
				<script type="text/javascript">
				google_ad_client = "pub-7541997421837440";
				google_ad_width = 120;
				google_ad_height = 600;
				google_ad_format = "120x600_as";
				google_ad_type = "text_image";
				google_ad_channel = "";
				google_color_border = "e4eaf2";
				google_color_bg = "e4eaf2";
				google_color_link = "0000FF";
				google_color_text = "000000";
				google_color_url = "008000";
				</script>

				<script type="text/javascript"
				  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
	</div>
