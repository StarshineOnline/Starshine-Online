<?php

$requete = 'SELECT x, y FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($requete);
$row = $db->read_array($req);
//On sort de la bdd les coordonnés du joueur x,y
$coord['x'] = $row['x'];
$coord['y'] = $row['y'];
$coord_joueur['y'] = $row['y'];
$coord_joueur['x'] = $row['x'];

//echo 'x : '.$coord['x'].' y : '.$coord['y'];
//Sert à calculer le point d'origine en haut a gauche pour la carte
$xmin = $coord['x'] - 2;
if ($xmin < 1) $xmin = 1;
$xmax = $coord['x'] + 2;
$ymin = $coord['y'] - 2;
if ($ymin < 1) $ymin = 1;
$ymax = $coord['y'] + 2;

$map = array();
//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne_donjon.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne_donjon.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne_donjon.') * '.$G_colonne_donjon.')) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne_donjon.') * '.$G_colonne_donjon.')) <= '.$xmax.'))) ORDER BY ID';
$req = $db->query($requete);
//On met les valeurs dans les cases
while($row = $db->read_array($req))
{
	$coord_map = convertd_in_coord($row['ID']);
	$map[$coord_map['x']][$coord_map['y']] = $row;
}
//Requète pour l'affichage des joueurs dans le périmètre de vision
$requete_joueurs = 'SELECT ID, nom, level, race, x, y, classe FROM perso WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) AND statut = \'actif\' ORDER BY y ASC, x ASC, dernier_connexion DESC';
$req_joueurs = $db->query($requete_joueurs);
//Requète pour l'affichage des pnj dans le périmètre de vision
$requete_pnj = 'SELECT id, nom, image, x, y FROM pnj WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_pnj = $db->query($requete_pnj);
//Requète pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT id, x, y, nom, lib FROM map_monstre WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC, nom ASC, id ASC';
$req_monstres = $db->query($requete_monstres);
//Requète pour l'affichage des drapeaux dans le périmètre de vision
$requete_drapeaux = 'SELECT x, y, type, nom FROM placement WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_drapeaux = $db->query($requete_drapeaux);
?>
	<div id="carte">
	<table cellpadding="0" cellspacing="0">
	<tr class="tabnoir">
		<td>
			<a title="Afficher / Masquer les royaumes" onclick="switch_map();" style="cursor : pointer;">R</a>
		</td>

	<?php
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		if($coord['x'] == $i) $color = 'red';
		else $color = 'white';
		echo '<td style="text-align : center; color : '.$color.';">'.$i.'</td>';
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

	$index = 0;
	$index_pnj = 0;
	$index_m = 0;
	$index_d = 0;
	$x_joueurs = $row_j[$index]['x'];
	$y_joueurs = $row_j[$index]['y'];
	
	$x_pnj = $row_pnj[$index]['x'];
	$y_pnj = $row_pnj[$index]['y'];
	
	$x_monstres = $row_m[$index]['x'];
	$y_monstres = $row_m[$index]['y'];
	
	$x_drapeaux = $row_d[$index]['x'];
	$y_drapeaux = $row_d[$index]['y'];

	//Affichage de la map
	$roy = 0;
	$y_curseur = $ymin;
	while($y_curseur <= $ymax)
	{
		$x_curseur = $xmin;
		while($x_curseur <= $xmax)
		{
			$rowid = $y_curseur * 1000 + $x_curseur;
			if(is_array($map[$x_curseur][$y_curseur])) $W_terrain_case = $map[$x_curseur][$y_curseur]['decor'];
			else $W_terrain_case = 'black';
			
			$case['information'] = '';
			
			$z_pnj = 0;
			//Affichage des pnjs
			while(($x_pnj == $x_curseur) AND ($y_pnj == $y_curseur))
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
       	
			$z = 0;
			//affichage des joueurs
			while(($x_joueurs == $x_curseur) AND ($y_joueurs == $y_curseur))
			{
				//echo $rowid.' '.$z.'<br />';
				$info[$rowid][$z]['ID'] = $row_j[$index]['ID'];
				$info[$rowid][$z]['nom'] = $row_j[$index]['nom'];
				$info[$rowid][$z]['race'] = $row_j[$index]['race'];
				$info[$rowid][$z]['classe'] = $row_j[$index]['classe'];
				$info[$rowid][$z]['level'] = $row_j[$index]['level'];
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
				if ($z > 0 OR $z_pnj > 0) $case['information'] .= '<br />';
				$case['information'] .= '<strong>'.$info[$rowid][$z]['nom'].'</strong> - '.$Gtrad[$info[$rowid][$z]['race']].' - Niv '.$info[$rowid][$z]['level'];
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
			while(($x_monstres == $x_curseur) AND ($y_monstres == $y_curseur))
			{
				$info_m[$rowid][$z_m]['type'] = $row_m[$index_m]['type'];
				$info_m[$rowid][$z_m]['nom'] = $row_m[$index_m]['nom'];
				$info_m[$rowid][$z_m]['lib'] = $row_m[$index_m]['lib'];
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
				if (($z_m > 0) OR ($z > 0)) $case['information'] .= '<br />';
				$case['information'] .= '<strong>Monstre</strong> - '.$info_m[$rowid][$z_m]['nom'];
				$z_m++;
			}		
       	
			$z_d = 0;
			//Affichage des drapeaux
			while(($x_drapeaux == $x_curseur) AND ($y_drapeaux == $y_curseur))
			{
				$info_d[$rowid][$z_d]['type'] = $row_d[$index_d]['type'];
				$info_d[$rowid][$z_d]['nom'] = $row_d[$index_d]['nom'];
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
				if (($z_d > 0) OR ($z > 0) OR ($z_m > 0)) $case['information'] .= '<br />';
				$case['information'] .= '<strong>'.ucwords($info_d[$rowid][$z_d]['nom']).'</strong>';
				$z_d++;
			}
       	
			$case['info'] = '';
			$case['infopnj'] = '';
			$case['infor'] = '';
			$case['infoj'] = '';
			//Joueur toujours en haut
			if($info[$rowid][0]['ID'] == $_SESSION['ID'])
			{
				$case['info'] = $info[$rowid][0]['race'];
				$case['info_classe'] = $Tclasse[$info[$rowid][0]['classe']]['type'];
			}
			else
			{
				if (isset($info_m[$rowid]))
				{
					$case['info'] = $info_m[$rowid][0]['lib'];
					$case['info_classe'] = 'hop';
				}
				if (isset($info[$rowid]))
				{
					$case['info'] = $info[$rowid][0]['race'];
					$case['info_classe'] = $Tclasse[$info[$rowid][0]['classe']]['type'];
				}
				if (isset($info_pnj[$rowid]))
				{
					$case['info'] = $info_pnj[$rowid][0]['image'];
					$case['info_classe'] = 'pnj';
				}
				if (isset($info_d[$rowid]))
				{
					$case['infor'] = 'drapeau';
				}
			}
			if($x_curseur == $coord_joueur['x'] AND $y_curseur == $coord_joueur['y'])
			{
				$case['infoj'] = $joueur['race'];
				$case['infoj_classe'] = $Tclasse[$joueur['classe']]['type'];
			}
			if($x_curseur == $xmin)
			{
				if($y_curseur == $coord_joueur['y']) $color = 'red';
				else $color = 'white';
				echo '</tr>
				<tr>
					<td class="tabnoir" style="color : '.$color.';">
						'.$y_curseur.'
					</td>';
					if ($case['information'] != '') $on = 'onmousemove="afficheInfo(\'info_'.$rowid.'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$rowid.'\', \'none\', event, \'centre\');"';
					else $on = '';
					echo '<td '.$on.' class="decor tex'.$W_terrain_case.'" onclick="envoiInfo(\'informationcase.php?case='.$rowid.'\', \'information\')">';
					echo '<div class="boite_royaume" id="roy'.$roy.'"><div id="marq'.$roy.'" class="marque_royaume" style="border : 0px solid '.$Gcouleurs[$row['royaume']].';">';
					if ($case['infor'] != '')
					{
						$image = 'image/'.$case['infor'];
						if (file_exists($image.'.png')) $image .= '.png';
						else $image .= '.gif';
						if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
						echo'<div id="drap'.$roy.'"><img src="'.$image.'" alt="'.$case['infor'].'" '.$on.'/></div>';
					}
					if ($case['infoj'] != '')
					{
						$image = 'image/'.$case['infoj'];
						if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
						elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
						elseif (file_exists($image.'.png')) $image .= '.png';
						else $image .= '.gif';
							if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
						echo'<div style="height : 100%; vertical-align : center;"><table style="height : 100%; width : 100%;"><tr><td><img src="'.$image.'" alt="'.$case['infoj'].'" style="vertical-align : middle;" '.$on.'/></td></tr></table></div>';
					}
					elseif ($case['info'] != '')
					{
						$image = 'image/'.$case['info'];
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
				if ($case['infor'] != '')
				{
					$image = 'image/'.$case['infor'];
					if (file_exists($image.'.png')) $image .= '.png';
					else $image .= '.gif';
						if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div id="drap'.$roy.'"><img src="'.$image.'" alt="'.$case['infor'].'" '.$on.'/></div>';
				}
				if ($case['infoj'] != '')
				{
					$image = 'image/'.$case['infoj'];
						if (file_exists($image.'_'.$case['info_classe'].'.png')) $image .= '_'.$case['info_classe'].'.png';
						elseif(file_exists($image.'_'.$case['info_classe'].'.gif')) $image .= '_'.$case['info_classe'].'.gif';
						elseif (file_exists($image.'.png')) $image .= '.png';
						else $image .= '.gif';
						if (!file_exists($image)) $on = 'style="width : 50px; height : 50px;"';
					echo'<div style="height : 100%; vertical-align : center;"><table style="height : 100%; width : 100%;"><tr><td><img src="'.$image.'" alt="'.$case['infoj'].'" style="vertical-align : middle;" '.$on.'/></td></tr></table></div>';
				}
				elseif ($case['info'] != '')
				{
					$image = 'image/'.$case['info'];
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
				<div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_<?php echo $rowid ?>">
				<?php
				echo $case['information'];
				?>
				</div>
				<?php
			}
			?>
			</td>
			<?php
			$roy++;
			$x_curseur++;
		}
		$y_curseur++;
	}
	
	?>
	</tr>
	</table>
	</div>