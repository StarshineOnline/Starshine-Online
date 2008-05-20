<?php
$requete = 'SELECT x, y, level FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($requete);
$row = $db->read_array($req);
//On sort de la bdd les coordonnés du joueur x,y
$coord['x'] = $row['x'];
$coord['y'] = $row['y'];
$coord_joueur['y'] = $row['y'];
$coord_joueur['x'] = $row['x'];

//echo 'x : '.$coord['x'].' y : '.$coord['y'];
//Sert à calculer le point d'origine en haut a gauche pour la carte
$xmin = $coord['x'] - 3;
if ($xmin < 1) $xmin = 1;
$xmax = $coord['x'] + 3;
if ($xmax > 999) $xmax = 999;
$ymin = $coord['y'] - 3;
if ($ymin < 1) $ymin = 1;
$ymax = $coord['y'] + 3;
if ($ymax > 1000) $ymax = 1000;

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) <= '.$xmax.'))) ORDER BY ID';
$req = $db->query($requete);
//Requète pour l'affichage des joueurs dans le périmètre de vision
$requete_joueurs = 'SELECT ID, nom, level, race, x, y, classe, cache_classe, cache_niveau FROM perso WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) AND statut = \'actif\' ORDER BY y ASC, x ASC, dernier_connexion DESC';
$req_joueurs = $db->query($requete_joueurs);
//Requète pour l'affichage des pnj dans le périmètre de vision
$requete_pnj = 'SELECT id, nom, image, x, y FROM pnj WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_pnj = $db->query($requete_pnj);
//Requète pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT id, x, y, nom, lib, COUNT(*) as tot FROM map_monstre WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) GROUP BY x, y, lib ORDER BY y ASC, x ASC, ABS(level - '.$row['level'].') ASC, level ASC, nom ASC, id ASC';
$req_monstres = $db->query($requete_monstres);
//Requète pour l'affichage des drapeaux dans le périmètre de vision
$requete_drapeaux = 'SELECT placement.x, placement.y, placement.type, placement.nom, placement.royaume, placement.debut_placement, placement.fin_placement, batiment.image FROM placement, batiment WHERE (((placement.x >= '.$xmin.') AND (placement.x <= '.$xmax.')) AND ((placement.y >= '.$ymin.') AND (placement.y <= '.$ymax.'))) AND batiment.id = placement.id_batiment ORDER BY placement.y ASC, placement.x ASC';
$req_drapeaux = $db->query($requete_drapeaux);
//Requète pour l'affichage des batiments dans le périmètre de vision
$requete_batiment = 'SELECT construction.x, construction.y, construction.hp, construction.royaume, construction.nom, construction.id_batiment, batiment.image FROM construction, batiment WHERE (((construction.x >= '.$xmin.') AND (construction.x <= '.$xmax.')) AND ((construction.y >= '.$ymin.') AND (construction.y <= '.$ymax.'))) AND batiment.id = construction.id_batiment ORDER BY construction.y ASC, construction.x ASC';
$req_batiment = $db->query($requete_batiment);
?>
	<div id="carte">
	<table cellpadding="0" cellspacing="0" style="margin: 0 auto;">
	<tr>
		<td class="bord_carte_haut_gauche">
			<a title="Afficher / Masquer les royaumes" onclick="switch_map();" style="cursor : pointer;"><img src="image/icone/royaume_icone.png" alt="Royaume" title="Royaume" style="vertical-align : middle;" /></a>
		</td>

	<?php

	for ($i = $xmin; $i <= $xmax; $i++)
	{
		if($coord['x'] == $i) $color = 'black; font-weight : bold';
		else $color = 'white';
		echo '<td class="bord_carte_haut" style="color : '.$color.';">'.$i.'</td>';
	}

	$x = 0;
	$y = 0;

	while($row_joueurs = $db->read_assoc($req_joueurs))
	{
		$row_j[] = $row_joueurs;
	}

	while($row_p = $db->read_assoc($req_pnj))
	{
		$row_pnj[] = $row_p;
	}

	while($row_monstres = $db->read_assoc($req_monstres))
	{
		$row_m[] = $row_monstres;
	}

	while($row_drapeaux = $db->read_assoc($req_drapeaux))
	{
		$row_d[] = $row_drapeaux;
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

	$x_pnj = $row_pnj[$index]['x'];
	$y_pnj = $row_pnj[$index]['y'];

	$x_monstres = $row_m[$index]['x'];
	$y_monstres = $row_m[$index]['y'];

	$x_drapeaux = $row_d[$index]['x'];
	$y_drapeaux = $row_d[$index]['y'];

	$x_batiment = $row_b[$index]['x'];
	$y_batiment = $row_b[$index]['y'];

	//Affichage de la map
	$roy = 0;
	while($row = $db->read_array($req))
	{
		$coord = convert_in_coord($row['ID']);
		$rowid = $row['ID'];
		$W_terrain_case = $row['decor'];

		if (($coord['x'] != 0) AND ($coord['y'] != 0))
		{
			$case['information'] = '';

			$z_pnj = 0;
			//Affichage des pnjs
			while(($x_pnj == $coord['x']) AND ($y_pnj == $coord['y']))
			{
				$info_pnj[$rowid][$z_pnj]['nom'] = $row_pnj[$index_pnj]['nom'];
				$info_pnj[$rowid][$z_pnj]['image'] = $row_pnj[$index_pnj]['image'];
				$index_pnj++;
				if (isset($row_pnj[$index_pnj]['x']))
				{
					$x_pnj = $row_pnj[$index_pnj]['x'];
					$y_pnj = $row_pnj[$index_pnj]['y'];
				}
				else
				{
					$x_pnj = 0;
					$y_pnj = 0;
				}
				if ($z_pnj > 0) $case['information'] .= '<br />';
				$case['information'] .= '<strong>PNJ</strong> - '.$info_pnj[$rowid][$z_pnj]['nom'];
				$z_pnj++;
			}

			$z_b = 0;
			//Affichage des batiments
			while(($x_batiment == $coord['x']) AND ($y_batiment == $coord['y']))
			{
				$info_b[$rowid][$z_b]['nom'] = $row_b[$index_b]['nom'];
				$info_b[$rowid][$z_b]['image'] = $row_b[$index_b]['image'];
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
				if ($z_b > 0 OR $z_pnj > 0) $case['information'] .= '<br />';
				$case['information'] .= '<strong>Batiment</strong> - '.$info_b[$rowid][$z_b]['nom'];
				$z_b++;
			}

			$z = 0;
			//affichage des joueurs
			$id_du_joueur = false;
			while(($x_joueurs == $coord['x']) AND ($y_joueurs == $coord['y']))
			{
				$info[$rowid][$z]['ID'] = $row_j[$index]['ID'];
				$info[$rowid][$z]['nom'] = $row_j[$index]['nom'];
				$info[$rowid][$z]['race'] = $row_j[$index]['race'];
				if(!check_affiche_bonus($row_j[$index]['cache_classe'], $joueur, $row_j[$index])) $info[$rowid][$z]['classe'] = 'combattant';
				else $info[$rowid][$z]['classe'] = $row_j[$index]['classe'];
				if(!check_affiche_bonus($row_j[$index]['cache_niveau'], $joueur, $row_j[$index])) $info[$rowid][$z]['level'] = 'xxx';
				else $info[$rowid][$z]['level'] = $row_j[$index]['level'];
				$info[$rowid][$z]['level'] = $row_j[$index]['level'];
				//On check si c'est le joueur si c'est cas on init la variable id_joueur
				if($row_j[$index]['ID'] == $_SESSION['ID']) $id_du_joueur = $z;
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
				if ($z > 0 OR $z_pnj > 0 OR $z_b > 0) $case['information'] .= '<br />';
				$case['information'] .= '<span class="info_joueur">'.$info[$rowid][$z]['nom'].'</span> - '.$Gtrad[$info[$rowid][$z]['race']].' - Niv '.$info[$rowid][$z]['level'];
				//Si c'est le joueur on le met en haut
				if($row_j[$index]['ID'] == $_SESSION['ID'])
				{
					$temp = $info[$rowid][0];
					$info[$rowid][0] = $info[$rowid][$z];
					$info[$rowid][$z] = $temp;
				}
				$z++;
			}

			$z_m = 0;
			//Affichage des monstres
			while(($x_monstres == $coord['x']) AND ($y_monstres == $coord['y']))
			{
				$info_m[$rowid][$z_m]['type'] = $row_m[$index_m]['type'];
				$info_m[$rowid][$z_m]['nom'] = $row_m[$index_m]['nom'];
				$info_m[$rowid][$z_m]['lib'] = $row_m[$index_m]['lib'];
				$info_m[$rowid][$z_m]['tot'] = $row_m[$index_m]['tot'];
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
				if($z_m > 0 OR ($z > 0) OR $z_pnj > 0 OR $z_b > 0) $case['information'] .= '<br />';
				$case['information'] .= '<span class="info_monstre">Monstre</span> - '.$info_m[$rowid][$z_m]['nom'].' x'.$info_m[$rowid][$z_m]['tot'];
				$z_m++;
			}

			$z_d = 0;
			//Affichage des drapeaux
			while(($x_drapeaux == $coord['x']) AND ($y_drapeaux == $coord['y']))
			{
				$info_d[$rowid][$z_d]['type'] = $row_d[$index_d]['type'];
				$info_d[$rowid][$z_d]['nom'] = $row_d[$index_d]['nom'];
				$info_d[$rowid][$z_d]['image'] = $row_d[$index_d]['image'];
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
				if (($z_d > 0) OR ($z > 0) OR ($z_m > 0) OR $z_pnj > 0 OR $z_b > 0) $case['information'] .= '<br />';
				$case['information'] .= '<strong>'.ucwords($info_d[$rowid][$z_d]['nom']).'</strong>';
				$z_d++;
			}

			$case['info'] = '';
			$case['infopnj'] = '';
			$case['infor'] = '';
			$case['infoj'] = '';
			$case['infob'] = '';

			//Joueur toujours en haut
			if($id_du_joueur !== false)
			{
				$case['info'] = $info[$rowid][$id_du_joueur]['race'];
				$case['info_classe'] = $Tclasse[$info[$rowid][$id_du_joueur]['classe']]['type'];
			}
			else
			{
				if (isset($info_m[$rowid]))
				{
					$case['info'] = $info_m[$rowid][0]['lib'];
					$case['info_classe'] = 'hop';
					$case['type'] = 'monstre';
				}
				if (isset($info[$rowid]))
				{
					$case['info'] = $info[$rowid][0]['race'];
					$case['info_classe'] = $Tclasse[$info[$rowid][0]['classe']]['type'];
					$case['type'] = 'joueur';
				}
				if (isset($info_pnj[$rowid]))
				{
					$case['info'] = $info_pnj[$rowid][0]['image'];
					$case['info_classe'] = 'pnj';
					$case['type'] = 'pnj';
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
						$case['infor'] = $info_d[$rowid][0]['image'].'_0'.$info_d[$rowid][0]['ratio_temps'];
						$case['type'] = 'batiment';
					}
				}
				if (isset($info_b[$rowid]))
				{
					$case['info'] = $info_b[$rowid][0]['image'].'_04';
					$case['info_classe'] = 'hop';
					$case['type'] = 'batiment';
				}
			}
			if($coord['x'] == $coord_joueur['x'] AND $coord['y'] == $coord_joueur['y'])
			{
				$case['infoj'] = $joueur['race'];
				$case['infoj_classe'] = $Tclasse[$joueur['classe']]['type'];
			}
			if ($coord['y'] != $y)
			{
				if($coord['y'] == $coord_joueur['y']) $color = 'black; font-weight : bold;';
				else $color = 'white';
				echo '</tr>
				<tr>
					<td style="color : '.$color.';" class="bord_carte_gauche">
						'.$coord['y'].'
					</td>';
					if ($case['information'] != '') $on = 'onmousemove="afficheInfo(\'info_'.$rowid.'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$rowid.'\', \'none\', event, \'centre\');"';
					else $on = '';
					echo '<td '.$on.' class="decor tex'.$W_terrain_case.'" onclick="envoiInfo(\'informationcase.php?case='.$rowid.'\', \'information\')">';
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
				$y = $coord['y'];
			}
			else
			{
				if ($case['information'] != '') $on = 'onmousemove="afficheInfo(\'info_'.$rowid.'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$rowid.'\', \'none\', event, \'centre\');"';
				else $on = '';
				echo '<td '.$on.' class="decor tex'.$W_terrain_case.'" onclick="envoiInfo(\'informationcase.php?case='.$rowid.'\', \'information\')">';
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
					/*
					error_log("test: ".$image.'_'.$case['info_classe'].'.png'.
							  " - ".$image.'_'.$case['info_classe'].'.gif'.
							  " - ".$image.'.png');
							  */
					if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
					elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
					elseif (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
					if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div id="inf'.$roy.'" style="height : 100%; display : block;"><table style="height : 100%; width : 100%; margin : 0px;padding : 0px;" cellpadding="0" cellspacing="0"><tr><td><img src="'.$image.'" alt="'.$case['info'].'" style="vertical-align : middle; margin : 0px;padding : 0px;" '.$on.'/></td></tr></table></div>';
				}
				echo '</div>';
			}
			echo '</div>';
			if ($case['information'] != '')
			{
				?>
				<div class="jsmap" id="info_<?php echo $rowid ?>">
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
		$roy++;
	}

	?>

	</tr>

	</table>

	</div>