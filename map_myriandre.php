<?php
include('haut.php');
include('inc/verif_log_admin.inc.php');
?>
<a href="admin_2.php">Revenir à l'administration</a>
<?php
//On sort de la bdd les coordonnés du joueur x,y
$coord['x'] = '1';
$coord['y'] = '1';
$coord_joueur['y'] = '50';
$coord_joueur['x'] = '50';

//echo 'x : '.$coord['x'].' y : '.$coord['y'];
//Sert à calculer le point d'origine en haut a gauche pour la carte
$xmin = 1;
$xmax = 25;
$ymin = 196;
$ymax = 222;

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= 196) AND (FLOOR(ID / '.$G_ligne.') <= 222)) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) >= 1) AND ((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) <= 25))) ORDER BY ID ASC';
$req = $db->query($requete);
//Requète pour l'affichage des joueurs dans le périmètre de vision
$requete_joueurs = 'SELECT ID, nom, level, race, x, y, classe, hp FROM perso WHERE statut = \'actif\' AND (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.')))ORDER BY y ASC, x ASC, dernier_connexion DESC';
$req_joueurs = $db->query($requete_joueurs);
//Requète pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT id, x, y, nom, lib, hp FROM map_monstre WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) GROUP BY x, y, lib ORDER BY y ASC, x ASC';
echo $requete_monstres;
$req_monstres = $db->query($requete_monstres);
//Requète pour l'affichage des drapeaux dans le périmètre de vision
$requete_drapeaux = 'SELECT x, y, type, nom, royaume, debut_placement, fin_placement FROM placement WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_drapeaux = $db->query($requete_drapeaux);
//Requète pour l'affichage des batiments dans le périmètre de vision
$requete_batiment = 'SELECT x, y, royaume, nom, id_batiment, image, hp FROM construction WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_batiment = $db->query($requete_batiment);
?>
	<div id="carte">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td style="background : url('image/coin_map.png'); height : 18px;">
		</td>

	<?php
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		if($coord['x'] == $i) $color = 'black; font-weight : bold';
		else $color = 'white';
		echo '<td style="text-align : center; color : '.$color.'; background : url(\'image/bord_map.png\');">'.$i.'</td>';
	}
	
	$x = 0;
	$y = 0;
	
	while($row_joueurs = $db->read_assoc($req_joueurs))
	{
		$row_j[] = $row_joueurs;
	}
	while($row_drapeaux = $db->read_assoc($req_drapeaux))
	{
		$row_d[] = $row_drapeaux;
	}
	while($row_monstres = $db->read_assoc($req_monstres))
	{
		$row_m[] = $row_monstres;
	}

	while($row_batiment = $db->read_assoc($req_batiment))
	{
		$row_b[] = $row_batiment;
	}

	$index = 0;
	$index_pnj = 0;
	$index_m = 0;
	$index_d = 0;
	$index_b = 0;
	$x_joueurs = $row_j[$index]['x'];
	$y_joueurs = $row_j[$index]['y'];

	$x_monstres = $row_m[$index]['x'];
	$y_monstres = $row_m[$index]['y'];
	
	$x_drapeaux = $row_d[$index]['x'];
	$y_drapeaux = $row_d[$index]['y'];

	$x_batiment = $row_b[$index]['x'];
	$y_batiment = $row_b[$index]['y'];

	//Affichage de la map
	$roy = 0;
	$maxx = 0;
	$maxy = 0;
	while($row = $db->read_array($req))
	{
		$coord = convert_in_coord($row['ID']);
		$donjon[$coord['x']][$coord['y']]['decor'] = $row['decor'];
		$donjon[$coord['x']][$coord['y']]['id'] = $row['ID'];
		if($coord['x'] > $maxx) $maxx = $coord['x'];
		if($coord['y'] > $maxy) $maxy = $coord['y'];
	}
	for($y = 196; $y < $maxy; $y++)
	{
		for($x = 1; $x < $maxx; $x++)
		{
			if ($x == 1)
			{
				echo '</tr>
				<tr>
					<td style="color : '.$color.';background : url(\'image/bord2_map.png\'); width : 16px; text-align : center;">
						'.$y.'
					</td>';
			}
			//Case vide
			if($donjon[$x][$y] == '')
			{
				//affichage case noire
					echo '
						<td class="decor texblack" id="case'.$positioncase.'" onClick="clickTexture('.$positioncase.')">
							<input type="hidden" name="hidden'.$positioncase.'" value="" id="input'.$positioncase.'" />
						</td>';
			}
			else
			{
				$rowid = $donjon[$x][$y]['id'];
				$case['information'] = '';
				
				$z_b = 0;
				//Affichage des batiments
				while(($x_batiment == $x) AND ($y_batiment == $y))
				{
					$info_b[$rowid][$z_b]['nom'] = $row_b[$index_b]['nom'];
					$info_b[$rowid][$z_b]['image'] = $row_b[$index_b]['image'];
					$info_b[$rowid][$z_b]['royaume'] = $row_b[$index_b]['royaume'];
					$info_b[$rowid][$z_b]['hp'] = $row_b[$index_b]['hp'];
					$index_b++;
					if (isset($row_b[$index_b]['x']))
					{
						$x_batiment = $row_b[$index_b]['x'];
						$y_batiment = $row_b[$index_b]['y'];
					}
					else
					{
						$x_batiment = 0;
						$y_batiment = 0;
					}
					if ($z_b > 0) $case['information'] .= '<br />';
					$case['information'] .= '<strong>Batiment</strong> - '.$info_b[$rowid][$z_b]['nom'].' '.$info_b[$rowid][$z_b]['royaume'].' '.$info_b[$rowid][$z_b]['hp'].' HP';
					$z_b++;
				}
				$z = 0;
				//affichage des joueurs
				while(($x_joueurs == $x) AND ($y_joueurs == $y))
				{
					$info[$rowid][$z]['ID'] = $row_j[$index]['ID'];
					$info[$rowid][$z]['nom'] = $row_j[$index]['nom'];
					$info[$rowid][$z]['race'] = $row_j[$index]['race'];
					$info[$rowid][$z]['classe'] = $row_j[$index]['classe'];
					$info[$rowid][$z]['level'] = $row_j[$index]['level'];
					$info[$rowid][$z]['hp'] = $row_j[$index]['hp'];
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
					if ($z > 0 OR $z_b > 0) $case['information'] .= '<br />';
					$case['information'] .= '<strong>'.$info[$rowid][$z]['nom'].'</strong> - '.$info[$rowid][$z]['hp'].' HP - '.$Gtrad[$info[$rowid][$z]['race']].' - Niv '.$info[$rowid][$z]['level'];
					$z++;
				}
        		
				$z_m = 0;
				//Affichage des monstres
				while(($x_monstres == $x) AND ($y_monstres == $y))
				{
					$info_m[$rowid][$z_m]['type'] = $row_m[$index_m]['type'];
					$info_m[$rowid][$z_m]['nom'] = $row_m[$index_m]['nom'];
					$info_m[$rowid][$z_m]['lib'] = $row_m[$index_m]['lib'];
					$info_m[$rowid][$z_m]['hp'] = $row_m[$index_m]['hp'];
					$index_m++;
					if (isset($row_m[$index_m]['x']))
					{
						$x_monstres = $row_m[$index_m]['x'];
						$y_monstres = $row_m[$index_m]['y'];
					}
					else
					{
						$x_monstres = 0;
						$y_monstres = 0;
					}
					if($z_m > 0 OR ($z > 0) OR $z_b > 0) $case['information'] .= '<br />';
					$case['information'] .= '<span class="info_monstre">Monstre</span> - '.$info_m[$rowid][$z_m]['nom'].' HP : '.$info_m[$rowid][$z_m]['hp'];
					$z_m++;
				}	

				$z_d = 0;
				//Affichage des drapeaux
				while(($x_drapeaux == $x) AND ($y_drapeaux == $y))
				{
					$info_d[$rowid][$z_d]['type'] = $row_d[$index_d]['type'];
					$info_d[$rowid][$z_d]['nom'] = $row_d[$index_d]['nom'];
					$info_d[$rowid][$z_d]['royaume'] = $row_d[$index_d]['royaume'];
					$temps_passe = time() - $row_d[$index_d]['debut_placement'];
					$temps_total = $row_d[$index_d]['fin_placement'] - $row_d[$index_d]['debut_placement'];
					$info_d[$rowid][$z_d]['ratio_temps'] = ceil(3 * $temps_passe / $temps_total);
					$index_d++;
					if (isset($row_d[$index_d]['x']))
					{
						$x_drapeaux = $row_d[$index_d]['x'];
						$y_drapeaux = $row_d[$index_d]['y'];
					}
					else
					{
						$x_drapeaux = 0;
						$y_drapeaux = 0;
					}
					if ($z_d > 0 OR $z > 0 OR $z_b > 0) $case['information'] .= '<br />';
					$case['information'] .= '<strong>'.ucwords($info_d[$rowid][$z_d]['nom']).'</strong>';
					$z_d++;
				}
        		
				$case['infoj'] = '';
				$case['info'] = '';
				$case['infor'] = '';
				$case['infoj'] = '';
				$case['infob'] = '';
				$case['infom'] = '';
        		
				if (isset($info[$rowid]))
				{
					$case['info'] = $info[$rowid][0]['race'];
					$case['info_classe'] = $Tclasse[$info[$rowid][0]['classe']]['type'];
					$case['type'] = 'joueur';
				}
				if (isset($info_m[$rowid]))
				{
					$case['info'] = $info_m[$rowid][0]['lib'];
					$case['info_classe'] = 'hop';
					$case['type'] = 'monstre';
				}
				if (isset($info_d[$rowid]))
				{
					if($info_d[$rowid][0]['type'] == 'drapeau')
					{
						$case['infor'] = 'drapeau_'.$info_d[$rowid][0]['royaume'];
						$case['type'] = 'drapeaux';
					}
					else
					{
						$case['infor'] = $Gtrad[$info_d[$rowid][0]['nom']].'_0'.$info_d[$rowid][0]['ratio_temps'];
						$case['type'] = 'batiment';
					}
				}
				if (isset($info_b[$rowid]))
				{
					$case['info'] = $info_b[$rowid][0]['image'].'_04';
					$case['info_classe'] = 'hop';
					$case['type'] = 'batiment';
				}
				if($x == $coord_joueur['x'] AND $y == $coord_joueur['y'])
				{
					$case['infoj'] = $joueur['race'];
					$case['infoj_classe'] = $Tclasse[$joueur['classe']]['type'];
				}
				if ($case['information'] != '') $on = 'onmousemove="afficheInfo(\'info_'.$rowid.'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$rowid.'\', \'none\', event, \'centre\');"';
				else $on = '';
				echo '<td '.$on.' class="decor tex'.$donjon[$x][$y]['decor'].'" onclick="envoiInfo(\'informationcase.php?case='.$rowid.'\', \'information\')">';
				echo '<div class="boite_royaume" id="roy'.$roy.'"><div id="marq'.$roy.'" class="marque_royaume" style="border : 0px solid '.$Gcouleurs[$row['royaume']].';">';
				if ($case['infoj'] != '')
				{
					$image = 'image/personnage/'.$case['infoj'].'/'.$case['infoj'];
					if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
					elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
					elseif (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
						if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div style="height : 100%; vertical-align : center;"><table style="height : 100%; width : 100%;"><tr><td><img src="'.$image.'" alt="'.$case['infoj'].'" style="vertical-align : middle;" '.$on.'/></td></tr></table></div>';
				}
				elseif ($case['infor'] != '')
				{
					if($case['type'] == 'monstre') $rep_type = 'monstre';
					elseif($case['type'] == 'pnj') $rep_type = 'pnj';
					elseif($case['type'] == 'drapeaux') $rep_type = 'drapeaux';
					elseif($case['type'] == 'batiment') $rep_type = 'batiment';
					else $rep_type = 'personnage/'.$case['infor'];
					$image = 'image/'.$rep_type.'/'.$case['infor'];
					if (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
					if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div id="drap'.$roy.'"><img src="'.$image.'" alt="'.$case['infor'].'" '.$on.'/></div>';
				}
				elseif ($case['info'] != '')
				{
					if($case['type'] == 'monstre') $rep_type = 'monstre';
					elseif($case['type'] == 'pnj') $rep_type = 'pnj';
					elseif($case['type'] == 'drapeaux') $rep_type = 'drapeaux';
					elseif($case['type'] == 'batiment') $rep_type = 'batiment';
					else $rep_type = 'personnage/'.$case['info'];
					$image = 'image/'.$rep_type.'/'.$case['info'];
					//Vérification image classe
					if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
					elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
					elseif (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
					if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div id="inf'.$roy.'" style="height : 100%; display : block;"><table style="height : 100%; width : 100%; margin : 0px;padding : 0px;"><tr><td><img src="'.$image.'" alt="'.$case['info'].'" style="vertical-align : middle; margin : 0px;padding : 0px;" '.$on.'/></td></tr></table></div>';
				}
				echo '</div>';
				if ($case['information'] != '')
				{
					?>
					<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 350px; padding: 5px;" id="info_<?php echo $rowid ?>">
					<?php
					echo $case['information'];
					?>
					</div>
					<?php
				}
				?>
				</td>
				<?php
			}
		}
	}
	
	?>
	</tr>
	</table>
	</div>