<?php
{//-- Initialisation
	$MAP = Array();
}
{//-- Récupération de la position X, Y du joueur et de son level pour la detection des monstres.
	$RqXY = $db->query("SELECT x, y, level FROM perso WHERE ID=".$_SESSION["ID"].";");
	$objXY = $db->read_object($RqXY);
	$x = $objXY->x;
	$y = $objXY->y;
	$level = $objXY->level;
}

//-- Champ de vision = 3 par défaut
$champ_vision = 3;
//-- Nombre de case affichées en longueur et largeur
$case_affiche = ($champ_vision * 2) + 1;

{//-- Sert à calculer le point d'origine en haut a gauche pour la carte
	if($x < ($champ_vision))			{ $x_min = 1;		$x_max = $x + ($case_affiche - ($x)); }
	elseif($x > (150 - $champ_vision))		{ $x_max = 150;		$x_min = $x - ($case_affiche - (150 - $x + 1)); }
	else								{ $x_min = $x - $champ_vision;	$x_max = $x + $champ_vision; };
	
	if($y < ($champ_vision + 3))		{ $y_min = 1;		$y_max = $y + ($case_affiche - ($y)); }
	elseif($y > (150 - $champ_vision))	{ $y_max = 150;		$y_min = $y - ($case_affiche - (150 - $y + 1)); }
	else								{ $y_min = $y - $champ_vision; 	$y_max = $y + $champ_vision; }
}	
{//-- Requête pour l'affichage des PNJ dans le périmètre de vision
	$RqPNJ = $db->query("SELECT id, nom, image, x, y FROM pnj 
						 WHERE ( (x >= $x_min) AND (x <= $x_max) ) 
						 AND ( (y >= $y_min) AND (y <= $y_max) )  
						 ORDER BY y ASC, x ASC;");
	if($db->num_rows($RqPNJ) > 0)
	{
		$pnj = 0;
		while($objPNJ = $db->read_object($RqPNJ))
		{
			$pnj = count($MAP[$objPNJ->x][$objPNJ->y]["PNJ"]);
			
			$MAP[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["id"] = $objPNJ->id;
			$MAP[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["nom"] = $objPNJ->nom;
			{//-- vérification que l'image du PNJ existe
				$image = "image/pnj/";
				if(file_exists($image.$objPNJ->image.".png")) 		{ $image .= $objPNJ->image.".png"; }
				elseif(file_exists($image.$objPNJ->image.".gif")) 	{ $image .= $objPNJ->image.".gif"; }
				else 												{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$MAP[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["image"] = $image;
		}
	}
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
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["race"] = $Gtrad[$objJoueurs->race];
			$MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = $objJoueurs->classe;
			{//-- Vérification des bonus liés au points shine
				if( ($objJoueurs->cache_classe == 1) && ($objJoueurs->ID != $_SESSION["ID"]) ) { $MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = "combattant"; }
				if( ($objJoueurs->cache_niveau == 1) && ($objJoueurs->ID != $_SESSION["ID"]) ) { $MAP[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = "xxx"; }
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
{//-- Requète pour l'affichage des Drapeaux dans le périmètre de vision
	$RqDrapeaux = $db->query("SELECT placement.x, placement.y, placement.type, placement.nom, placement.royaume, royaume.race, placement.debut_placement, placement.fin_placement, batiment.image 
						      FROM placement, batiment, royaume
						      WHERE ( ( (placement.x >= $x_min) AND (placement.x <= $x_max) ) AND ( (placement.y >= $y_min) AND (placement.y <= $y_max) ) ) 
						      AND batiment.id = placement.id_batiment 
						      AND royaume.ID=placement.royaume
						      ORDER BY placement.y ASC, placement.x ASC;");
	if($db->num_rows($RqDrapeaux) > 0)
	{
		$drapal = 0;
		while($objDrapeaux = $db->read_object($RqDrapeaux))
		{
			$drapal = count($MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"]);
			
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["type"] = $objDrapeaux->type;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["nom"] = $objDrapeaux->nom;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["royaume"] = $objDrapeaux->royaume;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["race"] = $objDrapeaux->race;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["debut_placement"] = $objDrapeaux->debut_placement;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["fin_placement"] = $objDrapeaux->fin_placement;
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["image"] = $objDrapeaux->image;
			{//-- vérification que l'image du drapeau existe
				$image = "image/drapeaux/";
				$image2 = "image/batiment/";
				$ratio_temps = ceil(3 * (time() - $objDrapeaux->debut_placement) / ($objDrapeaux->fin_placement - $objDrapeaux->debut_placement) );
				
				if(file_exists($image.$objDrapeaux->image."_".$objDrapeaux->royaume.".png")) 		{ $image = $image.$objDrapeaux->image."_".$objDrapeaux->royaume.".png"; }
				elseif(file_exists($image.$objDrapeaux->image."_".$objDrapeaux->royaume.".gif")) 	{ $image = $image.$objDrapeaux->image."_".$objDrapeaux->royaume.".gif"; }
				elseif(file_exists($image2.$objDrapeaux->image."_0".$ratio_temps.".png")) 				{ $image = $image2.$objDrapeaux->image."_0".$ratio_temps.".png"; }
				elseif(file_exists($image2.$objDrapeaux->image."_0".$ratio_temps.".gif")) 			{ $image = $image2.$objDrapeaux->image."_0".$ratio_temps.".gif"; }
				else 																				{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$MAP[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["image"] = $image;
		}
	}
}
{//-- Requète pour l'affichage des batiments dans le périmètre de vision
	$RqBatiments = $db->query("SELECT construction.x, construction.y, construction.hp, construction.royaume, construction.nom, construction.id_batiment, batiment.image 
							   FROM construction, batiment 
							   WHERE ( ( (construction.x >= $x_min) AND (construction.x <= $x_max) ) AND ( (construction.y >= $y_min) AND (construction.y <= $y_max) ) ) 
							   AND batiment.id = construction.id_batiment 
							   ORDER BY construction.y ASC, construction.x ASC;");
	if($db->num_rows($RqBatiments) > 0)
	{
		$batimat = 0;
		while($objBatiments = $db->read_object($RqBatiments))
		{
			$batimat = count($MAP[$objBatiments->x][$objBatiments->y]["Batiments"]);
			
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["id_batiment"] = $objBatiments->id_batiment;
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["hp"] = $objBatiments->hp;
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["nom"] = $objBatiments->nom;
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["royaume"] = $objBatiments->royaume;
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["image"] = $objBatiments->image;

			{//-- vérification que l'image du PNJ existe
				$image = "image/batiment/";
				
				if(file_exists($image.$objBatiments->image."_04.png")) 		{ $image .= $objBatiments->image."_04.png"; }
				elseif(file_exists($image.$objBatiments->image."_04.gif")) 	{ $image .= $objBatiments->image."_04.gif"; }
				else 														{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$MAP[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["image"] = $image;
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
						 AND ( ((ID - (FLOOR(ID / $G_colonne) * 1000) ) >= $x_min) AND ((ID - (FLOOR(ID / $G_colonne) * 1000)) <= $x_max) ) 
						 ORDER BY ID;");
}
/* =========================== MAP V.3 ================================*/
echo "<div id='carte'>";
{//-- Affichage du bord haut (bh) de la map
	echo "<ul id='map_bord_haut'>
		   <li id='map_bord_haut_gauche' onclick=\"switch_map();\">&nbsp;</li>";
	for ($bh = $x_min; $bh <= $x_max; $bh++)
	{
		if($bh == $x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
		echo "<li $class_x>$bh</li>";
	}
	echo "</ul>";
}
{//-- Affichage du reste de la map
	$y_BAK = 0;
	$Once = false;
	$case = 0;
	while($objMap = $db->read_object($RqMap))
	{
		$coord = convert_in_coord($objMap->ID);
		$class_map = "decor tex".$objMap->decor;	//-- Nom de la classe "terrain" contenu dans texture.css
		
		if($coord['y'] != $y_BAK)
		{//-- On passe a la ligne
			if($Once) { echo "</ul>"; } else { $Once = true; };
			if($coord['y'] == $y) { $class_y = "id='bord_haut_y' "; } else { $class_y = ""; }; //-- Pour mettre en valeur la position Y ou se trouve le joueur
			echo "<ul class='map'>
			 	   <li $class_y class='map_bord_gauche'>".$coord['y']."</li>"; //-- Bord gauche de la map
			 
			$y_BAK = $coord['y'];
		}
		if( ($coord['x'] == $x) && ($coord['y'] == $y) )
		{
			if(!empty($MAP[$coord['x']][$coord['y']]["Joueurs"][0]["image"])) 	{ $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["Joueurs"][0]["image"].") center no-repeat;"; };
		}
		elseif(is_array($MAP[$coord['x']][$coord['y']]["PNJ"]))
		{//-- Affichage des PNJ ---------------------------------------//
			if(!empty($MAP[$coord['x']][$coord['y']]["PNJ"][0]["image"])) 		{ $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["PNJ"][0]["image"].") center no-repeat;"; };
		}
		elseif(is_array($MAP[$coord['x']][$coord['y']]["Drapeaux"]))
		{//-- Affichage des Drapeaux ----------------------------------//
			if(!empty($MAP[$coord['x']][$coord['y']]["Drapeaux"][0]["image"])) 	{ $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["Drapeaux"][0]["image"].") center no-repeat;"; };
		}
		elseif(is_array($MAP[$coord['x']][$coord['y']]["Batiments"]))
		{//-- Affichage des Batiments ---------------------------------//
			if(!empty($MAP[$coord['x']][$coord['y']]["Batiments"][0]["image"])) { $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["Batiments"][0]["image"].") center no-repeat;"; };
		}
		elseif(is_array($MAP[$coord['x']][$coord['y']]["Joueurs"]))
		{//-- Affichage des Joueurs -----------------------------------//
			if(!empty($MAP[$coord['x']][$coord['y']]["Joueurs"][0]["image"])) 	{ $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["Joueurs"][0]["image"].") center no-repeat;"; };
		}
		elseif(is_array($MAP[$coord['x']][$coord['y']]["Monstres"]))
		{//-- Affichage des Monstres ----------------------------------//
			if(!empty($MAP[$coord['x']][$coord['y']]["Monstres"][0]["image"])) 	{ $background = "background:transparent url(".$MAP[$coord['x']][$coord['y']]["Monstres"][0]["image"].") center no-repeat;"; };
		}
		else { $background = ""; }
		
		if(   (count($MAP[$coord['x']][$coord['y']]["Batiments"]) > 0)
		   || (count($MAP[$coord['x']][$coord['y']]["PNJ"]) > 0)
		   || (count($MAP[$coord['x']][$coord['y']]["Joueurs"]) > 0)
		   || (count($MAP[$coord['x']][$coord['y']]["Monstres"]) > 0)
		   || (count($MAP[$coord['x']][$coord['y']]["Drapeaux"]) > 0) )
		{
			$overlib = "<ul>";
			for($i = 0; $i < count($MAP[$coord['x']][$coord['y']]["Batiments"]); $i++) 	{ $overlib .= "<li class='overlib_batiments'><span>Batiment</span>&nbsp;-&nbsp;".$MAP[$coord['x']][$coord['y']]["Batiments"][$i]["nom"]."</li>"; }
			for($i = 0; $i < count($MAP[$coord['x']][$coord['y']]["PNJ"]); $i++)		{ $overlib .= "<li class='overlib_batiments'><span>PNJ</span>&nbsp;-&nbsp;".ucwords($MAP[$coord['x']][$coord['y']]["PNJ"][$i]["nom"])."</li>"; }
			for($i = 0; $i < count($MAP[$coord['x']][$coord['y']]["Joueurs"]); $i++)	{ $overlib .= "<li class='overlib_joueurs'><span>".$MAP[$coord['x']][$coord['y']]["Joueurs"][$i]["nom"]."</span>&nbsp;-&nbsp;".ucwords($MAP[$coord['x']][$coord['y']]["Joueurs"][$i]["race"])." - Niv.".$MAP[$coord['x']][$coord['y']]["Joueurs"][$i]["level"]."</li>"; }
			for($i = 0; $i < count($MAP[$coord['x']][$coord['y']]["Monstres"]); $i++)	{ $overlib .= "<li class='overlib_monstres'><span>Monstre</span>&nbsp;-&nbsp;".$MAP[$coord['x']][$coord['y']]["Monstres"][$i]["nom"]." x".$MAP[$coord['x']][$coord['y']]["Monstres"][$i]["tot"]."</li>"; }
			for($i = 0; $i < count($MAP[$coord['x']][$coord['y']]["Drapeaux"]); $i++)	{ $overlib .= "<li class='overlib_batiments'><span>Drapeau</span>&nbsp;-&nbsp;".ucwords($MAP[$coord['x']][$coord['y']]["Drapeaux"][$i]["race"])."</li>"; }
			$overlib .= "</ul>";
			$overlib = str_replace("'", "\'", trim($overlib));
		}
		else { $overlib = ""; }
		
		$border = "border:0px solid ".$Gcouleurs[$objMap->royaume].";";
		echo "<li class='$class_map'>
			   <div class='map_contenu' 
			   		id='marq$case' 
			   		style=\"".$background.$border."\" ";
		if(!empty($overlib))
		{
			echo "	onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\" 
			   		onmouseout=\"return nd();\" ";
		}
		echo " 		onclick=\"envoiInfo('informationcase.php?case=".$objMap->ID."', 'information');\" 
			   >&nbsp;</div>
			  </li>";	
		
		$case++;
	}
	echo "</ul>";
}
echo "</div>"; 
/* ========================= FIN MAP V.3 ==============================*/
?>
