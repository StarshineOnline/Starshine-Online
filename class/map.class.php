<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class map
{
	public $x;
	public $y;
	public $champ_vision;
	public $root;
	public $xmin;
	public $xmax;
	public $ymin;
	public $ymax;
	public $resolution;
	public $troisd;
	public $onclick;
	public $quadrillage;
	public $cache_monstre;
	public $show_only;
	public $onclick_status;
	public $show_royaume_button;
	public $affiche_terrain;
	public $arene = false;
	private $affiche_royaume;

	function __construct($x, $y, $champ_vision = 3, $root = '', $donjon = false, $resolution = 'high', $troisd = false)
	{
		$this->x = $x;
		$this->y = $y;
		$this->champ_vision = $champ_vision;
		$this->root = $root;
		$this->resolution = $resolution;
		$this->donjon = $donjon;
		$this->onclick = "envoiInfo('informationcase.php?case=%%id%%', 'information');";
		$this->onclick_status = false;
		$this->cache_monstre = false;
		$this->affiche_royaume = false;
		$this->case_affiche = ($this->champ_vision * 2) + 1;
		$this->troisd = $troisd;
		$this->affiche_terrain = false;

		//$this->show_royaume_button = "javascript:affiche_royaume=!affiche_royaume;deplacement('centre', cache_monstre, affiche_royaume, show_only);";
		$this->show_royaume_button = '';

		if(!$this->donjon)
		{
			$limite_x = 190;
			$limite_y = 190;
		}
		else
		{
			$limite_x = 300;
			$limite_y = 300;
		}

		if($this->x < ($this->champ_vision + 1))			{ $this->xmin = 1;		$this->xmax = $this->x + ($this->case_affiche - ($this->x)); }
		elseif($this->x > ($limite_x - $this->champ_vision))		{ $this->xmax = $limite_x;		$this->xmin = $this->x - ($this->case_affiche - ($limite_x - $this->x + 1)); }
		else												{ $this->xmin = $this->x - $this->champ_vision;	$this->xmax = $this->x + $this->champ_vision; };
		
		if($this->y < ($this->champ_vision + 1))		{ $this->ymin = 1;		$this->ymax = $this->y + ($this->case_affiche - ($this->y)); }
		elseif($this->y > ($limite_y - $this->champ_vision))	{ $this->ymax = $limite_y;		$this->ymin = $this->y - ($this->case_affiche - ($limite_y - $this->y + 1)); }
		else											{ $this->ymin = $this->y - $this->champ_vision; 	$this->ymax = $this->y + $this->champ_vision; }

		$this->map = array();
	}

	function affiche()
	{
		global $db;
		global $Gcouleurs;
		if($this->donjon && !$this->arene)
		{
			$xmin = $this->xmin + 1;
			$xmax = $this->xmax - 1;
			$ymin = $this->ymin + 1;
			$ymax = $this->ymax - 1;
		}
		else
		{
			$xmin = $this->xmin;
			$xmax = $this->xmax;
			$ymin = $this->ymin;
			$ymax = $this->ymax;
		}
		$total_cases = ($this->xmax - $this->xmin + 1) * ($this->ymax - $this->ymin + 1);
		$this->nb_cases = $total_cases;
		$RqMapTxt = "SELECT x,y,decor,royaume,info FROM map 
						 WHERE y >= $ymin AND y <= $ymax 
						 AND x >= $xmin AND x <= $xmax
						 ORDER BY y,x;";
		$RqMap = $db->query($RqMapTxt);
		while($objMap = $db->read_object($RqMap))
		{
			//$coord = convert_in_coord($objMap->ID);
			$MAPTAB[$objMap->x][$objMap->y]["id"] = convert_in_pos($objMap->x, $objMap->y);
			$MAPTAB[$objMap->x][$objMap->y]["decor"] = $objMap->decor;
			$MAPTAB[$objMap->x][$objMap->y]["royaume"] = $objMap->royaume;
			$MAPTAB[$objMap->x][$objMap->y]["type"] = $objMap->info;
		}
		$classe_css = array();
		if(!$this->donjon)
		{
			$classe_css['map_bord_haut'] = 'map_bord_haut';
			$classe_css['map_bord_haut_gauche'] = 'map_bord_haut_gauche';
			$classe_css['map_bord_gauche'] = 'map_bord_gauche';
		}
		else
		{
			$classe_css['map_bord_haut'] = 'map_bord_haut2';
			$classe_css['map_bord_haut_gauche'] = 'map_bord_haut_gauche2';
			$classe_css['map_bord_gauche'] = 'map_bord_gauche2';
		}
		if($this->resolution == 'low')
		{
			$class_css['resolution'] = 'low_resolution';
			$class_css['resolution_map'] = 'mapl';
			$taille_cellule = 20.75;
		}
		else
		{
			$class_css['resolution'] = '';
			$class_css['resolution_map'] = 'map';
			$taille_cellule = 60.75;
		}
		if ($this->troisd)
		{
			echo "<div id=\"carte_3D\">";
			//echo "<div><b>Debug :</b><br/>x : $x, y : $y<br/>x_min : $x_min, x_max : $x_max<br/>y_min : $y_min, y_max : $y_max<br/></div>";
			{//-- MAP
				$_NB_CASE = 7;
	
				$w_box = 100;
				$h_box = 50;
				
				$x_init = 1;
				$y_init = floor(($h_box / 2) * ($_NB_CASE / 2));
				
				$G_ligne = 1000;
				$G_colonne = 1000;

				$x_pos = $x_init;
				$y_pos = $y_init;
				$z_index = 200;
				$case = 0;
				for($y_map = $this->ymin; $y_map <= $this->ymax; $y_map++)
				{
					if( ($y_map % 2) == 0) { $moins = 1; } else { $moins = 0; };
					echo "<ul>
						   <li class=\"bord_bas\" style=\"top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;\">$y_map<br/>Y</li>";
					$z_index --;
					$x_pos += floor($w_box / 2);
					$y_pos -= floor($h_box / 2);
					
					for($x_map = $this->xmin; $x_map <= $this->xmax; $x_map++)
					{
						if( ($x_map == $this->x) && ($y_map == $this->y) && is_array($this->map[$x_map][$y_map]["Joueurs"]))
						{
							if(!empty($this->map[$x_map][$y_map]["Joueurs"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Joueurs"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["PNJ"]))
						{//-- Affichage des PNJ ---------------------------------------//
							if(!empty($this->map[$x_map][$y_map]["PNJ"][0]["image"])) 		{ $background = "background-image : url(".$this->map[$x_map][$y_map]["PNJ"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Drapeaux"]))
						{//-- Affichage des Drapeaux ----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Drapeaux"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Drapeaux"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Batiments"]))
						{//-- Affichage des Batiments ---------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Batiments"][0]["image"])) { $background = "background-image : url(".$this->map[$x_map][$y_map]["Batiments"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Batiments_ennemi"]))
						{//-- Affichage des Batiments Ennemis---------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Batiments_ennemi"][0]["image"])) { $background = "background-image : url(".$this->map[$x_map][$y_map]["Batiments_ennemi"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Joueurs"]))
						{//-- Affichage des Joueurs -----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Joueurs"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Joueurs"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Monstres"]) && !$this->cache_monstre)
						{//-- Affichage des Monstres ----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Monstres"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Monstres"][0]["image"].") !important;"; };
						}
						else { $background = ""; }
						switch(calcul_distance_pytagore(convert_in_pos($this->x, $this->y), convert_in_pos($x_map, $y_map)))
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
							if(   (count($this->map[$x_map][$y_map]["Batiments"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Batiments_ennemi"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Reperes"]) > 0)
							|| (count($this->map[$x_map][$y_map]["PNJ"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Joueurs"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Monstres"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Drapeaux"]) > 0)
							|| $this->affiche_terrain)
							{
							$overlib = "<ul>";
	
							if ($this->affiche_terrain)
							{
								$type_terrain = type_terrain($MAPTAB[$x_map][$y_map]["type"]);
								$ressources_terrain = '';
								$ressource_array = ressource_terrain($type_terrain[1]);
								//my_dump($ressource_array);
								if (is_array($ressource_array))
									foreach ($ressource_array as $ress => $val)
										if ($val > 0) {
											if (strlen($ressources_terrain))
												$ressources_terrain .= ', ';
											else 
												$ressources_terrain = '<br />';
											$ressources_terrain .= "$ress:&nbsp;$val";
										}
								$overlib .= "<li class='overlib_batiments'><span>Terrain</span>&nbsp;-&nbsp;$type_terrain[1]$ressources_terrain</li>";
							}
	
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Reperes"]); $i++) 			{ $overlib .= "<li class='overlib_batiments'><span>Mission</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Reperes"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Batiments"]); $i++)			{ $overlib .= "<li class='overlib_batiments'><span>Batiment</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Batiments"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Batiments_ennemi"]); $i++) 	{ $overlib .= "<li class='overlib_batiments'><span>Batiment ennemi</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Batiments_ennemi"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["PNJ"]); $i++)				{ $overlib .= "<li class='overlib_batiments'><span>PNJ</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["PNJ"][$i]["nom"])."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Joueurs"]); $i++)
							{
								if(array_key_exists('hp', $this->map[$x_map][$y_map]["Joueurs"][$i]))
								{
									$all = ' HP : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["hp"].' / '.$this->map[$x_map][$y_map]["Joueurs"][$i]["hp_max"].' - MP : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["mp"].' / '.$this->map[$x_map][$y_map]["Joueurs"][$i]["mp_max"].' - PA : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["pa"];
	
								}
								else $all = '';
								$overlib .= "<li class='overlib_joueurs'><span>".$this->map[$x_map][$y_map]["Joueurs"][$i]["nom"]."</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["Joueurs"][$i]["race"])." - Niv.".$this->map[$x_map][$y_map]["Joueurs"][$i]["level"].$all."</li>";
							}
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Monstres"]); $i++)
							{
								if(array_key_exists('hp', $this->map[$x_map][$y_map]["Monstres"][$i])) $hp = ' - HP : '.$this->map[$x_map][$y_map]["Monstres"][$i]['hp'];
								else $hp = '';
								$overlib .= "<li class='overlib_monstres'><span>Monstre</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Monstres"][$i]["nom"]." x".$this->map[$x_map][$y_map]["Monstres"][$i]["tot"].$hp."</li>";
							}
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Drapeaux"]); $i++)			{ $overlib .= "<li class='overlib_batiments'><span>Construction</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["Drapeaux"][$i]["nom"])."</li>"; }
	
							$overlib .= "</ul>";
							$overlib = str_replace("'", "\'", trim($overlib));
						}
						else { $overlib = ""; }
					
						if(is_array($MAPTAB[$x_map][$y_map])) { $class_map = "decor tex".$MAPTAB[$x_map][$y_map]["decor"]; } else { $class_map = "decor texblack"; };
						
						echo "<li class='".$class_map."' style='top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;'>";
						echo " <div class='map_contenu' 
									id=\"marq$case\" 
									style=\"".$background.$border.$opacity."margin-top:-15px;width:100px;\" ";
						if(!empty($overlib))
						{
							echo "	onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\" 
							   		onmouseout=\"return nd();\" ";
						}
						if($this->onclick_status)
						{
							$onclick = str_replace('%%id%%', $MAPTAB[$x_map][$y_map]['id'], $this->onclick);
						}
						else $onclick = $this->onclick;
						echo " 		onclick=\"".$onclick."\" 
							   ><span id=\"pos_".$MAPTAB[$x_map][$y_map]["id"]."\">".$repere."</span></div>
							  </li>";	
						
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
					   <li id=\"bord_gauche\" style=\"top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;\"></li>";
				$z_index --;
				$x_pos += floor($w_box / 2);
				$y_pos -= floor($h_box / 2);
				for($x_map = $this->xmin; $x_map <= $this->xmax; $x_map++)
				{
					echo " <li class=\"bord_haut\" style=\"top:".$y_pos."px;left:".$x_pos."px;z-index:$z_index;\">$x_map<br/>X</li>";
					$z_index --;
					$x_pos += floor($w_box / 2);
					$y_pos -= floor($h_box / 2);
				}
				echo "</ul>";
			}
			echo "  </div>";
		
		}
		else
		{
			echo '<div class="div_map" style="width : '.round(20 + ($taille_cellule * $this->case_affiche)).'px;height:'.round(20 + ($taille_cellule * $this->case_affiche)).'px;">';
			{//-- Affichage du bord haut (bh) de la map
				echo "<ul id=\"".$classe_css['map_bord_haut']."\">
					   <li id=\"".$classe_css['map_bord_haut_gauche']."\" rel=\"option_map.php\" ";if (!empty($class_css['resolution'])) {echo "class=\"".$class_css['resolution']."\" ";} echo "onclick=\"$this->show_royaume_button\">&nbsp;</li>";
				for ($bh = $this->xmin; $bh <= $this->xmax; $bh++)
				{
					if($bh == $this->x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
					echo "<li $class_x ";if (!empty($class_css['resolution'])) {echo "class='".$class_css['resolution']."'";} echo ">$bh</li>";
				}
				echo "</ul>";
			}
			{//-- Affichage du reste de la map
				$y_BAK = 0;
				$Once = false;
				$case = 0;
				
				for($y_map = $this->ymin; $y_map <= $this->ymax; $y_map++)
				{
					for($x_map = $this->xmin; $x_map <= $this->xmax; $x_map++)
					{
						if($x_map == $this->xmin)
						{
							if($Once) { echo "</ul>"; } else { $Once = true; };
							if($y_map == $this->y) { $class_y = "id='bord_haut_y' "; } else { $class_y = ""; }; //-- Pour mettre en valeur la position Y ou se trouve le joueur
							echo "<ul class='".$class_css['resolution_map']."'>
						 		   <li $class_y "; if((!empty($class_css['resolution'])) OR (!empty($classe_css['map_bord_gauche']))) { echo "class='".$classe_css['map_bord_gauche']." ".$class_css['resolution']."'";} echo ">".$y_map."</li>"; //-- Bord gauche de la map
						}
						if( ($x_map == $this->x) && ($y_map == $this->y) && is_array($this->map[$x_map][$y_map]["Joueurs"]))
						{
							if(!empty($this->map[$x_map][$y_map]["Joueurs"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Joueurs"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["PNJ"]))
						{//-- Affichage des PNJ ---------------------------------------//
							if(!empty($this->map[$x_map][$y_map]["PNJ"][0]["image"])) 		{ $background = "background-image : url(".$this->map[$x_map][$y_map]["PNJ"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Drapeaux"]))
						{//-- Affichage des Drapeaux ----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Drapeaux"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Drapeaux"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Batiments"]))
						{//-- Affichage des Batiments ---------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Batiments"][0]["image"])) { $background = "background-image : url(".$this->map[$x_map][$y_map]["Batiments"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Batiments_ennemi"]))
						{//-- Affichage des Batiments Ennemis---------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Batiments_ennemi"][0]["image"])) { $background = "background-image : url(".$this->map[$x_map][$y_map]["Batiments_ennemi"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Joueurs"]))
						{//-- Affichage des Joueurs -----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Joueurs"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Joueurs"][0]["image"].") !important;"; };
						}
						elseif(is_array($this->map[$x_map][$y_map]["Monstres"]) && !$this->cache_monstre)
						{//-- Affichage des Monstres ----------------------------------//
							if(!empty($this->map[$x_map][$y_map]["Monstres"][0]["image"])) 	{ $background = "background-image : url(".$this->map[$x_map][$y_map]["Monstres"][0]["image"].") !important;"; };
						}
						else { $background = ""; }
	
						if(   (count($this->map[$x_map][$y_map]["Batiments"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Batiments_ennemi"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Reperes"]) > 0)
							|| (count($this->map[$x_map][$y_map]["PNJ"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Joueurs"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Monstres"]) > 0)
							|| (count($this->map[$x_map][$y_map]["Drapeaux"]) > 0)
							|| $this->affiche_terrain)
						{
							$overlib = "<ul>";
	
							if ($this->affiche_terrain)
							{
								$type_terrain = type_terrain($MAPTAB[$x_map][$y_map]["type"]);
								$ressources_terrain = '';
								$ressource_array = ressource_terrain($type_terrain[1]);
								//my_dump($ressource_array);
								if (is_array($ressource_array))
									foreach ($ressource_array as $ress => $val)
										if ($val > 0) {
											if (strlen($ressources_terrain))
												$ressources_terrain .= ', ';
											else 
												$ressources_terrain = '<br />';
											$ressources_terrain .= "$ress:&nbsp;$val";
										}
								$overlib .= "<li class='overlib_batiments'><span>Terrain</span>&nbsp;-&nbsp;$type_terrain[1]$ressources_terrain</li>";
							}
	
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Reperes"]); $i++) 			{ $overlib .= "<li class='overlib_batiments'><span>Mission</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Reperes"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Batiments"]); $i++)			{ $overlib .= "<li class='overlib_batiments'><span>Batiment</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Batiments"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Batiments_ennemi"]); $i++) 	{ $overlib .= "<li class='overlib_batiments'><span>Batiment ennemi</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Batiments_ennemi"][$i]["nom"]."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["PNJ"]); $i++)				{ $overlib .= "<li class='overlib_batiments'><span>PNJ</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["PNJ"][$i]["nom"])."</li>"; }
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Joueurs"]); $i++)
							{
								if(array_key_exists('hp', $this->map[$x_map][$y_map]["Joueurs"][$i]))
								{
									$all = ' HP : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["hp"].' / '.$this->map[$x_map][$y_map]["Joueurs"][$i]["hp_max"].' - MP : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["mp"].' / '.$this->map[$x_map][$y_map]["Joueurs"][$i]["mp_max"].' - PA : '.$this->map[$x_map][$y_map]["Joueurs"][$i]["pa"];
	
								}
								else $all = '';
								$overlib .= "<li class='overlib_joueurs'><span>".$this->map[$x_map][$y_map]["Joueurs"][$i]["nom"]."</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["Joueurs"][$i]["race"])." - Niv.".$this->map[$x_map][$y_map]["Joueurs"][$i]["level"].$all."</li>";
							}
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Monstres"]); $i++)
							{
								if(array_key_exists('hp', $this->map[$x_map][$y_map]["Monstres"][$i])) $hp = ' - HP : '.$this->map[$x_map][$y_map]["Monstres"][$i]['hp'];
								else $hp = '';
								$overlib .= "<li class='overlib_monstres'><span>Monstre</span>&nbsp;-&nbsp;".$this->map[$x_map][$y_map]["Monstres"][$i]["nom"]." x".$this->map[$x_map][$y_map]["Monstres"][$i]["tot"].$hp."</li>";
							}
							for($i = 0; $i < count($this->map[$x_map][$y_map]["Drapeaux"]); $i++)			{ $overlib .= "<li class='overlib_batiments'><span>Construction</span>&nbsp;-&nbsp;".ucwords($this->map[$x_map][$y_map]["Drapeaux"][$i]["nom"])."</li>"; }
	
							$overlib .= "</ul>";
							$overlib = str_replace("'", "\'", trim($overlib));
						}
						else { $overlib = ""; }
	
						//Repere
						if(is_array($this->map[$x_map][$y_map]["Reperes"])) $repere = $this->map[$x_map][$y_map]["Reperes"][0]['nom'][0];
						else $repere = '&nbsp;';
	
						if($this->resolution == 'low') $tex_resolution = 'l';
						else $tex_resolution = '';
						if(is_array($MAPTAB[$x_map][$y_map])) { $class_map = "decor tex".$tex_resolution.$MAPTAB[$x_map][$y_map]["decor"]; } else { $class_map = "decor texblack"; };
	
						if($this->affiche_royaume) $taille_border = 1;
						else $taille_border = 0;
						$border = "border:".$taille_border."px solid ".$Gcouleurs[$MAPTAB[$x_map][$y_map]['royaume']].";";
						echo "<li class='$class_map ".$class_css['resolution']."'>\n";
						echo "<div class='map_contenu ".$class_css['resolution']."' 
							   		id='marq$case' 
							   		style='".$background.$border."' ";
						if(!empty($overlib))
						{
							echo "	onmouseover=\"return overlib('$overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '');\" 
							   		onmouseout=\"return nd();\" ";
						}
						if($this->onclick_status)
						{
							$onclick = str_replace('%%id%%', $MAPTAB[$x_map][$y_map]['id'], $this->onclick);
						}
						else $onclick = $this->onclick;
						echo " 		onclick=\"".$onclick."\"\n>";
						
						if ($this->atmosphere_type != false)
						{
							$this->atmosphere_layer($this->atmosphere_type, $x_map, $y_map);
						} elseif (isset($this->map[$x_map][$y_map]['calque'])) {
							$this->atmosphere_layer($this->map[$x_map][$y_map]['calque'],
																			$x_map, $y_map,
																			$this->map[$x_map][$y_map]['calque_dx'],
																			$this->map[$x_map][$y_map]['calque_dy']);
						}
						echo "<span id='pos_".$MAPTAB[$x_map][$y_map]["id"]."'>".$repere."</span></div>";
						if ($this->atmosphere_type != false || 
								isset($this->map[$x_map][$y_map]['calque']))
							echo '</div>';
						echo "\n</li>";	
						
						$case++;
					}
				}
				echo "</ul>";
			}
			?>
			</div>
			<script type="text/javascript">
			// <![CDATA[
				$('#map_bord_haut_gauche').cluetip({activation: 'click', ajaxCache: false, width: '500px', cluetipClass: 'meteo', showTitle: false, closeText: 'Fermer', dropShadow: false, sticky: true, leftOffset: -5});
			// ]]>
			</script>
			<?php
			;
		}
	}

	function atmosphere_layer($atmosphere_type, $x, $y, $cdx = 0, $cdy = 0)
	{
		echo '<div style="background-attachment: scroll; '.
			'background-image: url(image/interface/calque-atmosphere-'.
			$atmosphere_type.'.png); ';
		$dx = (-$x * 60) + $cdx;
		$dy = (-$y * 60) + $cdy;
		if ($this->atmosphere_decal != false) {
			$dx += $this->atmosphere_decal['x'];
			$dy += $this->atmosphere_decal['y'];
		}
		echo "background-position: ${dx}px ${dy}px; ";
		echo 'margin-top: -2px; margin-bottom: -2px; margin-left: -2px;'.
			' height: 62px; width: 60px; background-repeat: repeat;">';
	}

	function get_pnj()
	{
		global $db;
		if($this->donjon)
		{
			$xmin = $this->xmin + 1;
			$xmax = $this->xmax - 1;
			$ymin = $this->ymin + 1;
			$ymax = $this->ymax - 1;
		}
		else
		{
			$xmin = $this->xmin;
			$xmax = $this->xmax;
			$ymin = $this->ymin;
			$ymax = $this->ymax;
		}
		$RqPNJ = $db->query("SELECT id, nom, image, x, y FROM pnj 
							 WHERE ( (x >= ".$xmin.") AND (x <= ".$xmax.") ) 
							 AND ( (y >= ".$ymin.") AND (y <= ".$ymax.") )  
							 ORDER BY y ASC, x ASC;");
		if($db->num_rows($RqPNJ) > 0)
		{
			$pnj = 0;
			while($objPNJ = $db->read_object($RqPNJ))
			{
				$pnj = count($this->map[$objPNJ->x][$objPNJ->y]["PNJ"]);
				$this->map[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["id"] = $objPNJ->id;
				$this->map[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["nom"] = $objPNJ->nom;
				{//-- v?rification que l'image du PNJ existe
					$image = $this->root."image/pnj/";
					if(file_exists($image.$objPNJ->image.".png")) 		{ $image .= $objPNJ->image.".png"; }
					elseif(file_exists($image.$objPNJ->image.".gif")) 	{ $image .= $objPNJ->image.".gif"; }
					else 												{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
				}
				$this->map[$objPNJ->x][$objPNJ->y]["PNJ"][$pnj]["image"] = $image;
			}
		}
	}

	function get_joueur($race = 'neutre', $all = false)
	{
		global $db;
		global $Tclasse;
		global $Gtrad;

		if($this->donjon)
		{
			$xmin = $this->xmin + 1;
			$xmax = $this->xmax - 1;
			$ymin = $this->ymin + 1;
			$ymax = $this->ymax - 1;
		}
		else
		{
			$xmin = $this->xmin;
			$xmax = $this->xmax;
			$ymin = $this->ymin;
			$ymax = $this->ymax;
		}
		if($all) $champs .= ', hp, hp_max, mp, mp_max, pa ';
		else $champs = '';
		$requete = "SELECT id, nom, level, race, x, y, classe, cache_classe, cache_niveau".$champs."
								 FROM perso 
								 WHERE (( (x >= ".$xmin.") AND (x <= ".$xmax.") ) 
								 AND ( (y >= ".$ymin.") AND (y <= ".$ymax.") ))  
								 AND statut='actif' 
								 ORDER BY y ASC, x ASC, dernier_connexion DESC;";
		$RqJoueurs = $db->query($requete);
		if($db->num_rows($RqJoueurs) > 0)
		{
			$joueurs = 0;
			while($objJoueurs = $db->read_object($RqJoueurs))
			{
				$joueurs = count($this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"]);

				$image = "";
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["id"] = $objJoueurs->id;
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["nom"] = htmlspecialchars($objJoueurs->nom);
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = $objJoueurs->level;
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["race"] = $Gtrad[$objJoueurs->race];
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = $objJoueurs->classe;
				if($all)
				{
					$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["hp"] = $objJoueurs->hp;
					$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["hp_max"] = floor($objJoueurs->hp_max);
					$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["mp"] = $objJoueurs->mp;
					$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["mp_max"] = floor($objJoueurs->mp_max);
					$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["pa"] = $objJoueurs->pa;
				}
				{//-- Vérification des bonus liés au points shine
					//Si c'est pas lui même
					if($objJoueurs->id != $_SESSION['id'])
					{
						if($objJoueurs->cache_classe == 2)	{ $this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = "combattant"; }
						elseif($objJoueurs->cache_classe == 1 && $objJoueurs->race != $race) { $this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"] = "combattant"; }
						if($objJoueurs->cache_niveau == 2)	{ $this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = "xxx"; }
						elseif($objJoueurs->cache_niveau == 1 && $objJoueurs->race != $race) { $this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["level"] = "xxx"; }
					}
				}
				{//-- Vérification que l'image de classe existe ($Tclasse est contenue dans ./inc/classe.inc.php)
					$classe = $this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["classe"];
					if($this->resolution == 'low')
					{
						$image = $this->root."image/personnage_low/".$objJoueurs->race."/".$objJoueurs->race;
					}
					else
					{
						$image = $this->root."image/personnage/".$objJoueurs->race."/".$objJoueurs->race;
					}
					if(file_exists($image."_".$Tclasse[$classe]["type"].".png")) 		{ $image .= "_".$Tclasse[$classe]["type"].".png"; }
					elseif(file_exists($image."_".$Tclasse[$classe]["type"].".gif")) 	{ $image .= "_".$Tclasse[$classe]["type"].".gif"; }
					elseif(file_exists($image.".png")) 									{ $image .= ".png"; }
					elseif(file_exists($image.".gif"))  								{ $image .= ".gif"; }
					else 																{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
				}			
				$this->map[$objJoueurs->x][$objJoueurs->y]["Joueurs"][$joueurs]["image"] = $image;
			}
		}
	}

	function get_drapeau($royaume='')
	{
		global $db;
		if (!empty($royaume)){$filter = 'AND map.royaume = '.$royaume; }
		$RqDrapeaux = $db->query("SELECT placement.x, placement.y, placement.type, placement.nom, placement.royaume, royaume.race, placement.debut_placement, placement.fin_placement, batiment.image 
							      FROM placement, batiment, royaume, map
							      WHERE ( ( (placement.x >= ".$this->xmin.") AND (placement.x <= ".$this->xmax.") ) AND ( (placement.y >= ".$this->ymin.") AND (placement.y <= ".$this->ymax.") ) ) 
							      AND batiment.id = placement.id_batiment 
							      AND royaume.id=placement.royaume
								  AND map.y = placement.y AND map.x = placement.x
								  $filter
							      ORDER BY placement.y ASC, placement.x ASC;");
		if($db->num_rows($RqDrapeaux) > 0)
		{
			$drapal = 0;
			while($objDrapeaux = $db->read_object($RqDrapeaux))
			{
				$drapal = count($this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"]);
				
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["type"] = $objDrapeaux->type;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["nom"] = $objDrapeaux->nom;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["royaume"] = $objDrapeaux->royaume;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["race"] = $objDrapeaux->race;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["debut_placement"] = $objDrapeaux->debut_placement;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["fin_placement"] = $objDrapeaux->fin_placement;
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["image"] = $objDrapeaux->image;
				{//-- v?rification que l'image du drapeau existe
					if($this->resolution == 'low')
					{					
						$image = $this->root."image/drapeaux_low/";
						$image2 = $this->root."image/batiment_low/";
					}
					else
					{
						$image = $this->root."image/drapeaux/";
						$image2 = $this->root."image/batiment/";
					}
					$ratio_temps = ceil(3 * (time() - $objDrapeaux->debut_placement) / ($objDrapeaux->fin_placement - $objDrapeaux->debut_placement) );
					
					if(file_exists($image.$objDrapeaux->image."_".$objDrapeaux->royaume.".png")) 		{ $image = $image.$objDrapeaux->image."_".$objDrapeaux->royaume.".png"; }
					elseif(file_exists($image.$objDrapeaux->image."_".$objDrapeaux->royaume.".gif")) 	{ $image = $image.$objDrapeaux->image."_".$objDrapeaux->royaume.".gif"; }
					elseif(file_exists($image2.$objDrapeaux->image."_0".$ratio_temps.".png")) 				{ $image = $image2.$objDrapeaux->image."_0".$ratio_temps.".png"; }
					elseif(file_exists($image2.$objDrapeaux->image."_0".$ratio_temps.".gif")) 			{ $image = $image2.$objDrapeaux->image."_0".$ratio_temps.".gif"; }
					else 																				{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
				}
				$this->map[$objDrapeaux->x][$objDrapeaux->y]["Drapeaux"][$drapal]["image"] = $image;
			}
		}
	}

	function get_batiment($royaume='')
	{
		global $db;
		if (!empty($royaume)){$filter = 'AND map.royaume = '.$royaume; }
		$RqBatiments = $db->query("SELECT construction.x, construction.y, construction.hp, construction.royaume, construction.nom, construction.id_batiment, batiment.image 
							FROM construction, batiment, map 
							WHERE ( ( (construction.x >= ".$this->xmin.") AND (construction.x <= ".$this->xmax.") ) AND ( (construction.y >= ".$this->ymin.") AND (construction.y <= ".$this->ymax.") ) ) 
							AND batiment.id = construction.id_batiment 
							AND map.y = construction.y AND map.x = construction.x
							$filter
							ORDER BY construction.y ASC, construction.x ASC;");
		if($db->num_rows($RqBatiments) > 0)
		{
			$batimat = 0;
			while($objBatiments = $db->read_object($RqBatiments))
			{
				$batimat = count($this->map[$objBatiments->x][$objBatiments->y]["Batiments"]);
				
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["id_batiment"] = $objBatiments->id_batiment;
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["hp"] = $objBatiments->hp;
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["nom"] = $objBatiments->nom;
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["royaume"] = $objBatiments->royaume;
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["image"] = $objBatiments->image;

				{//-- vérification que l'image du batiment existe
					if($this->resolution == 'low')
					{					
						$image = $this->root."image/batiment_low/";
					}
					else
					{
						$image = $this->root."image/batiment/";
					}
					
					if(file_exists($image.$objBatiments->image."_04.png")) 		{ $image .= $objBatiments->image."_04.png"; }
					elseif(file_exists($image.$objBatiments->image."_04.gif")) 	{ $image .= $objBatiments->image."_04.gif"; }
					else 														{ $image = $objBatiments->image."_introuvable.png"; } //-- Si aucun des fichiers n'existe autant rien mettre...
				}
				$this->map[$objBatiments->x][$objBatiments->y]["Batiments"][$batimat]["image"] = $image;
			}
		}
	}

	function get_monstre($level = 0, $groupe = true)
	{
		global $db;
		if($this->donjon)
		{
			$xmin = $this->xmin + 1;
			$xmax = $this->xmax - 1;
			$ymin = $this->ymin + 1;
			$ymax = $this->ymax - 1;
		}
		else
		{
			$xmin = $this->xmin;
			$xmax = $this->xmax;
			$ymin = $this->ymin;
			$ymax = $this->ymax;
		}
		if($groupe)
		{
			$group = ' GROUP BY x, y, lib';
			$champs = ', COUNT(1) as tot';
		}
		else
		{
			$group = '';
			$champs = ', hp';
		}
		//On affiche que les monstres d'un certain type
		if($this->show_only != '')
		{
			$show_only = " AND type IN (".$this->show_only.")";
		}
		else $show_only = '';
		$query = "SELECT mm.id, mm.x, mm.y, m.nom, m.lib ".$champs."
								  FROM map_monstre mm, monstre m
								  WHERE mm.type = m.id AND ( ( x BETWEEN ".$xmin." AND ".$xmax." ) AND ( y BETWEEN ".$ymin." AND ".$ymax." ) ) ".$show_only."
								  ".$group." ORDER BY y ASC, x ASC, ABS(mm.level - $level) ASC, mm.level ASC, nom ASC, id ASC;";
		$RqMonstres = $db->query($query);
		if($db->num_rows($RqMonstres) > 0)
		{
			$monster = 0;
			while($objMonstres = $db->read_object($RqMonstres))
			{
				$monster = count($this->map[$objMonstres->x][$objMonstres->y]["Monstres"]);
				
				$this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["id"] = $objMonstres->id;
				$this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["nom"] = $objMonstres->nom;
				$this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["lib"] = $objMonstres->lib;
				$this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["tot"] = $objMonstres->tot;
				if(!$groupe) $this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["hp"] = $objMonstres->hp;

				{//-- v?rification que l'image du PNJ existe
					$image = $this->root."image/monstre/";
					if(file_exists($image.$objMonstres->lib.".png")) 		{ $image .= $objMonstres->lib.".png"; }
					elseif(file_exists($image.$objMonstres->lib.".gif")) 	{ $image .= $objMonstres->lib.".gif"; }
					else 													{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
				}
				$this->map[$objMonstres->x][$objMonstres->y]["Monstres"][$monster]["image"] = $image;
			}
		}
	}

	//On passe en argumant un tableau contenant la liste des batiments
	function set_batiment($batiments)
	{
		$batimat = 0;
		foreach($batiments as $batiment)
		{
			$batimat = count($this->map[$batiment['x']][$batiment['y']]["Batiments"]);
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["id_batiment"] = $batiment['id'];
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["hp"] = $batiment['hp'];
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["nom"] = $batiment['nom'];
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["royaume"] = $batiment['royaume'];
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["image"] = $batiment['image'];

			{//-- vérification que l'image du PNJ existe
				if($this->resolution != 'high') $image = $this->root."image/batiment_low/";
				else $image = $this->root."image/batiment/";
				
				if(file_exists($image.$batiment['image']."_04.png")) 		{ $image .= $batiment['image']."_04.png"; }
				elseif(file_exists($image.$batiment['image']."_04.gif")) 	{ $image .= $batiment['image']."_04.gif"; }
				else 														{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$this->map[$batiment['x']][$batiment['y']]["Batiments"][$batimat]["image"] = $image;
		}
	}

	//On passe en argumant un tableau contenant la liste des batiments sous forme d'objet
	function set_batiment_objet($batiments)
	{
		$batimat = 0;
		foreach($batiments as $batiment)
		{
			$batimat = count($this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"]);
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["id_batiment"] = $batiment->get_id();
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["hp"] = $batiment->get_hp();
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["nom"] = $batiment->get_nom();
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["royaume"] = $batiment->get_royaume();
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["image"] = $batiment->get_image();

			{//-- vérification que l'image du PNJ existe
				$image = $this->root."image/batiment/";
				
				if(file_exists($image.$batiment->get_image()."_04.png")) 		{ $image .= $batiment->get_image()."_04.png"; }
				elseif(file_exists($image.$batiment->get_image()."_04.gif")) 	{ $image .= $batiment->get_image()."_04.gif"; }
				else { $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
			}
			$this->map[$batiment->get_x()][$batiment->get_y()]["Batiments"][$batimat]["image"] = $image;
		}
	}

	function set_repere($reperes)
	{
		$rep = 0;
		foreach($reperes as $repere)
		{
			$rep = count($this->map[$repere->x][$repere->y]["Reperes"]);
			$repere->get_type();
			$this->map[$repere->x][$repere->y]["Reperes"][$rep]["id_repere"] = $repere->id;
			$this->map[$repere->x][$repere->y]["Reperes"][$rep]["nom"] = $repere->repere_type->nom;
			$this->map[$repere->x][$repere->y]["Reperes"][$rep]["id_type"] = $repere->id_type;
		}
	}

	function set_batiment_ennemi($reperes)
	{
		$rep = 0;
		foreach($reperes as $repere)
		{
			$rep = count($this->map[$repere->x][$repere->y]["Batiments_ennemi"]);
			$repere->get_type();
			$this->map[$repere->x][$repere->y]["Batiments_ennemi"][$rep]["id_batiment"] = $repere->id_batiment;
			$this->map[$repere->x][$repere->y]["Batiments_ennemi"][$rep]["nom"] = $repere->repere_type->nom;
			$this->map[$repere->x][$repere->y]["Batiments_ennemi"][$rep]["image"] = $repere->repere_type->get_image($this->root, $this->resolution);
		}
	}

	function set_onclick($onclick)
	{
		$this->onclick = $onclick;
	}
	
	function set_arene($arene)
	{
		$this->arene = $arene;
	}
	
	function change_cache_monstre()
	{
		$this->cache_monstre = !$this->cache_monstre;
	}
	
	function change_affiche_royaume()
	{
		global $Gcouleurs;
		
		$this->affiche_royaume = !$this->affiche_royaume;
	}

	function change_show_only($show_only)
	{
		$this->show_only = $show_only;
	}

	function set_affiche_royaume($val)
	{
		$this->affiche_royaume = $val;
	}

	private $atmosphere_type = false;
	function set_atmosphere($type)
	{
		$this->atmosphere_type = $type;
	}

	private $atmosphere_decal = false;
	function set_atmosphere_decal($x, $y)
	{
		if ($x == 0 && $y == 0)
			$this->atmosphere_decal = false;
		else
			$this->atmosphere_decal = array('x' => $x, 'y' => $y);
			//$this->atmosphere_decal = (-$x * 60).'px '.(-$y * 60).'px';
	}

	function compute_atmosphere()
	{
		global $db;
		$xmin = $this->xmin;
		$xmax = $this->xmax;
		$ymin = $this->ymin;
		$ymax = $this->ymax;
		$atmosphere_moment = strtolower(moment_jour());
		for ($x = $xmin; $x <= $xmax; $x++) {
			for ($y = $ymin; $y <= $ymax; $y++) {
				$this->map[$x][$y]['calque'] = 'vide-'.$atmosphere_moment;
				$this->map[$x][$y]['calque_dx'] = 0;
				$this->map[$x][$y]['calque_dy'] = 0;
			}
		}
		$q = "select x, y, type, dx, dy from map_zone, (select x, y from map where $xmin <= x AND x <= $xmax AND $ymin <= y AND y <= $ymax) points where x1 <= x and x <= x2 and y1 <= y and y <= y2";
		$time_start = microtime(true);
		$req = $db->query($q);
		while ($row = $db->read_object($req)) {
			$this->map[$row->x][$row->y]['calque'] = $row->type.'-'.$atmosphere_moment;
			$this->map[$row->x][$row->y]['calque_dx'] = $row->dx;
			$this->map[$row->x][$row->y]['calque_dy'] = $row->dy;
			//echo "dx: $row->dx, dy: $row->dy";
		}
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		//my_dump($this->map);
		echo "<!-- Requete calques en $time secondes --> \n";
		//echo $q;
	}
}
?>
