<?php
//Inclusion des fonctions permettant de gérer le temps
if (file_exists('root.php'))
  include_once('root.php');
/*
function convert_in_coord($pos)
{
	$coord['y'] = floor($pos / 1000);
	$coord['x'] = $pos - ($coord['y'] * 1000);
	return $coord;
}

// Convertion des coordonnés en un chiffre
function convert_in_pos($x, $y)
{
	$pos = $y * 1000 + $x;
	return $pos;
}
function calcul_distance_pytagore($posjoueur1, $posjoueur2)
{
	$W_coord_joueur1 = convert_in_coord($posjoueur1);
	$W_coord_joueur2 = convert_in_coord($posjoueur2);
	$R_distance = ceil(sqrt(pow(abs($W_coord_joueur1['x'] - $W_coord_joueur2['x']), 2) + pow(abs($W_coord_joueur1['y'] - $W_coord_joueur2['y']), 2)));
	return $R_distance;
}
*/
$Tclasse['combattant']['type'] = 'guerrier';
$Tclasse['magicien']['type'] = 'mage';
$Tclasse['voleur']['type'] = 'voleur';
$Tclasse['guerrier']['type'] = 'guerrier';
$Tclasse['archer']['type'] = 'archer';
$Tclasse['sorcier']['type'] = 'mage';
$Tclasse['clerc']['type'] = 'mage';
$Tclasse['nécromancien']['type'] = 'mage';
$Tclasse['assassin']['type'] = 'voleur';
$Tclasse['champion']['type'] = 'champion';
$Tclasse['paladin']['type'] = 'champion';
$Tclasse['archer d élite']['type'] = 'archer';
$Tclasse['grand sorcier']['type'] = 'archimage';
$Tclasse['prètre']['type'] = 'archimage';
$Tclasse['prêtre']['type'] = 'archimage'; // Bastien: ben oui, c'est un accent circonflexe
$Tclasse['grand nécromancien']['type'] = 'archimage';

{//-- Initialisation
	$MAP = Array();

	$_NB_CASE = 7;
	
	$w_box = 100;
	$h_box = 50;
	
	$x_init = 1;
	$y_init = floor(($h_box / 2) * ($_NB_CASE / 2));
	
	$G_ligne = 1000;
	$G_colonne = 1000;
}
{//-- Récupération de la position X, Y du joueur et de son level pour la detection des monstres.
	$RqXY = $db->query("SELECT x, y, level FROM perso WHERE ID=".$joueur->get_id().";");
	$objXY = $db->read_object($RqXY);
	$x = $objXY->x;
	$y = $objXY->y;
	$level = $objXY->level;
}
{//-- Sert à  calculer le point d'origine en haut a gauche pour la carte
	if($x < 4)		{ $x_min = 1;							$x_max = $x + ($_NB_CASE - ($x)); }
	elseif($x > 197){ $x_max = 200;							$x_min = $x - ($_NB_CASE - (200 - $x + 1)); }
	else			{ $x_min = $x - floor($_NB_CASE / 2);	$x_max = $x + floor($_NB_CASE / 2); };
	
	if($y < 4)		{ $y_min = 1;							$y_max = $y + ($_NB_CASE - ($y)); }
	elseif($y > 197){ $y_max = 200;							$y_min = $y - ($_NB_CASE - (200 - $y + 1)); }
	else			{ $y_min = $y - floor($_NB_CASE / 2); 	$y_max = $y + floor($_NB_CASE / 2); }
}
{//-- Requète pour l'affichage des joueurs dans le périmètre de vision
	$RqJoueurs = $db->query("SELECT ID, nom, level, race, x, y, classe, cache_classe, cache_niveau 
							 FROM perso 
							 WHERE ( ( (x >= $x_min) AND (x <= $x_max) ) AND ( (y >= $y_min) AND (y <= $y_max) ) ) 
							 AND statut='actif' 
							 ORDER BY y ASC, x ASC, dernier_connexion DESC;");
	if($db->num_rows($RqJoueurs) > 0)
	{
		$joueurs = 0;
		while($objJoueurs = $db->read_object($RqJoueurs))
		{
			$image = "";
			$joueurs = count($MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"]);
			
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][]["ID"] = $objJoueurs->ID;
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["nom"] = $objJoueurs->nom;
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = $objJoueurs->level;
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["race"] = $objJoueurs->race;
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = $objJoueurs->classe;
			{//-- Vérification des bonus liés au points shine
				if( ($objJoueurs->cache_classe == 1) && ($objJoueurs->ID != 2008) ) { $MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = "combattant"; }
				if( ($objJoueurs->cache_niveau == 1) && ($objJoueurs->ID != 2008) ) { $MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = "xxx"; }
			}
			{//-- Vérification que l'image de classe existe ($Tclasse est contenue dans ./inc/classe.inc.php)
				$classe = $MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"];
				
				$image = "image/personnage/".$objJoueurs->race."/".$objJoueurs->race;
				if(file_exists($image."_".$Tclasse[$classe]["type"].".png")) 		{ $image .= "_".$Tclasse[$classe]["type"].".png"; }
				elseif(file_exists($image."_".$Tclasse[$classe]["type"].".gif")) 	{ $image .= "_".$Tclasse[$classe]["type"].".gif"; }
				elseif(file_exists($image.".png")) 									{ $image .= ".png"; }
				elseif(file_exists($image.".gif"))  								{ $image .= ".gif"; }
				else 																{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}			
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["image"] = $image;
		}
	}
}
{//-- Requète pour l'affichage des monstres dans le périmètre de vision
	$RqMonstres = $db->query("SELECT id, x, y, nom, lib, COUNT(*) as tot 
							  FROM map_monstre 
							  WHERE ( ( (x >= $x_min) AND (x <= $x_max) ) AND ( (y >= $y_min) AND (y <= $y_max) ) ) 
							  GROUP BY x, y, lib ORDER BY y ASC, x ASC, ABS(level - $level) ASC, level ASC, nom ASC, id ASC;");
	if($db->num_rows($RqMonstres) > 0)
	{
		$monster = 0;
		while($objMonstres = $db->read_object($RqMonstres))
		{
			$monster = count($MAP[$objMonstres->x][$objMonstres->y]["Monstres"]);
			
			$MAP[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["id"] = $objMonstres->id;
			$MAP[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["nom"] = $objMonstres->nom;
			$MAP[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["lib"] = $objMonstres->lib;
			$MAP[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["tot"] = $objMonstres->tot;

			{//-- vérification que l'image du PNJ existe
				$image = "image/monstre/";
				if(file_exists($image.$objMonstres->lib.".png")) 		{ $image .= $objMonstres->lib.".png"; }
				elseif(file_exists($image.$objMonstres->lib.".gif")) 	{ $image .= $objMonstres->lib.".gif"; }
				else 													{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$MAP[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["image"] = $image;
		}
	}
}
{//-- Requète pour l'affichage de la map
	$RqMap = $db->query("SELECT * FROM map 
					     WHERE ( (FLOOR(ID / $G_ligne) >= $y_min) AND (FLOOR(ID / $G_ligne) <= $y_max) ) 
					     AND ( ( (ID - (FLOOR(ID / $G_colonne) * $G_colonne)) >= $x_min) AND ( (ID - (FLOOR(ID / $G_colonne) * $G_colonne) ) <= $x_max) )  
					     ORDER BY ID;");
	while($objMap = $db->read_object($RqMap))
	{
		$coord = convert_in_coord($objMap->ID);
		$MAPTAB[$coord['x']][$coord['y']]["ID"] = $objMap->ID;
		$MAPTAB[$coord['x']][$coord['y']]["decor"] = "decor tex".$objMap->decor;
	}
}
echo "<div id='carte_3D'>";
//echo "<div><b>Debug :</b><br/>x : $x, y : $y<br/>x_min : $x_min, x_max : $x_max<br/>y_min : $y_min, y_max : $y_max<br/></div>";
{//-- MAP
	$x_pos = $x_init;
	$y_pos = $y_init;
	$z_index = 200;
	$case = 0;
	for($y_map = $y_min; $y_map <= $y_max; $y_map++)
	{
		if( ($y_map % 2) == 0) { $moins = 1; } else { $moins = 0; };
		echo "<ul>
			   <li class='bord_bas' style='top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;'>$y_map<br/>Y</li>";
		$z_index --;
		$x_pos += floor($w_box / 2);
		$y_pos -= floor($h_box / 2);
		
		for($x_map =  $x_min; $x_map <= $x_max; $x_map++)
		{
			if(is_array($MAP[$x_map][$y_map]["Joueurs"]))
			{//-- Affichage des Joueurs -----------------------------------//
				if(!empty($MAP[$x_map][$y_map]["Joueurs"][0]["image"])) 		{ $background = "background:transparent url(".$MAP[$x_map][$y_map]["Joueurs"][0]["image"].") center no-repeat;"; };
			}
			elseif(is_array($MAP[$x_map][$y_map]["Monstres"]))
			{//-- Affichage des Monstres ----------------------------------//
				if(!empty($MAP[$x_map][$y_map]["Monstres"][0]["image"])) 		
				{ 
					$background = "background:transparent url(".$MAP[$x_map][$y_map]["Monstres"][0]["image"].") center no-repeat;"; 
				};
			}
			else { $background = ""; };
			switch(calcul_distance_pytagore(convert_in_pos($x, $y), convert_in_pos($x_map, $y_map)))
			{
				case 0 : $opacity = ""; break;
				case 1 : $opacity = ""; break;
				case 2 : $opacity = "opacity:0.9;"; break;
				case 3 : $opacity = "opacity:0.8;"; break;
				case 4 : $opacity = "opacity:0.7;"; break;
				case 5 : $opacity = "opacity:0.5;"; break;
				case 6 : $opacity = "opacity:0.3;"; break;
				case 7 : $opacity = "opacity:0.2;"; break;
				case 8 : $opacity = "opacity:0.1;"; break;
				default : $opacity = "opacity:0.1;"; break;
			}
			if(is_array($MAPTAB[$x_map][$y_map])) 
			{ 
				$class_map = $MAPTAB[$x_map][$y_map]["decor"];
			} 
			else 
			{ 
				$class_map = "texblack"; 
				$style_map = "";
			};
			
			echo "<li class='".$class_map."' style='top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;'>";
			echo " <div class='map_contenu' 
						id='marq$case' 
						style=\"".$background.$border.$opacity."margin-top:-15px;width:100px;\" ";
			if(!empty($overlib))
			{
				echo "	onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\" 
						onmouseout=\"return nd();\" ";
			}
			echo " 		onclick=\"envoiInfo('informationcase.php?case=".$MAPTAB[$x_map][$y_map]["ID"]."', 'information');\" 
				   >&nbsp;</div>";
			
			echo "</li>";
			$z_index --;
			$x_pos += floor($w_box / 2);
			$y_pos -= floor($h_box / 2);
			$case ++;
		}
		$z_index = 200;
		$x_init += floor($w_box / 2);	$x_pos = $x_init;
		$y_init += floor($h_box / 2);	$y_pos = $y_init;
		echo "</ul>";
	}	
	echo "<ul>
		   <li id='bord_gauche' style='top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;'></li>";
	$z_index --;
	$x_pos += floor($w_box / 2);
	$y_pos -= floor($h_box / 2);
	for($x_map = $x_min; $x_map <= $x_max; $x_map++)
	{
		echo " <li class='bord_haut' style='top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;'>$x_map<br/>X</li>";
		$z_index --;
		$x_pos += floor($w_box / 2);
		$y_pos -= floor($h_box / 2);
	}
	echo "</ul>";
}
echo "  </div>";


?>
