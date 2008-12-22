<?php
require('haut_roi.php');
include('../class/construction.class.php');
include('../class/bourg.class.php');
include('../class/mine.class.php');
include('../class/placement.class.php');

if(array_key_exists('id', $_GET))
{
	$bourg = new bourg($_GET['id']);
	$bourg->get_mines(true);
	$bourg->get_placements();
	$bourg->get_mine_total();
	$x = $bourg->x;
	$y = $bourg->y;
	?>
	<div id="map_mine">
	<?php
	//-- Champ de vision = 5 par défaut
	$champ_vision = 5;
	//-- Nombre de case affichées en longueur et largeur
	$case_affiche = ($champ_vision * 2) + 1;
	
	{//-- Sert ? calculer le point d'origine en haut a gauche pour la carte
		if($x < ($champ_vision + 1))			{ $x_min = 1;		$x_max = $x + ($case_affiche - ($x)); }
		elseif($x > (150 - $champ_vision))		{ $x_max = 150;		$x_min = $x - ($case_affiche - (150 - $x + 1)); }
		else								{ $x_min = $x - $champ_vision;	$x_max = $x + $champ_vision; };
		
		if($y < ($champ_vision + 1))		{ $y_min = 1;		$y_max = $y + ($case_affiche - ($y)); }
		elseif($y > (150 - $champ_vision))	{ $y_max = 150;		$y_min = $y - ($case_affiche - (150 - $y + 1)); }
		else								{ $y_min = $y - $champ_vision; 	$y_max = $y + $champ_vision; }
	}	
	
	//On va afficher la carte
	$RqMap = $db->query("SELECT * FROM map 
						 WHERE ( (FLOOR(ID / $G_ligne) >= $y_min) AND (FLOOR(ID / $G_ligne) <= $y_max) ) 
						 AND ( ((ID - (FLOOR(ID / $G_colonne) * 1000) ) >= $x_min) AND ((ID - (FLOOR(ID / $G_colonne) * 1000)) <= $x_max) ) 
						 ORDER BY ID;");
	$taille_px = 60;
						 
	echo '<div id="carte" style="width : 700px; height : 700px;">';
	{//-- Affichage du bord haut (bh) de la map
		echo "<ul id='map_bord_haut'>
				<li id='map_bord_haut_gauche' style='width : ".$taille_px."px; height : 20px;' onclick=\"switch_map();\">&nbsp;</li>";
		for ($bh = $x_min; $bh <= $x_max; $bh++)
		{
			if($bh == $x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
			echo "<li $class_x style='width : ".$taille_px."px; height : 20px;'>$bh</li>";
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
				echo "<ul class='map' style='height : ".$taille_px."px;'>
						<li $class_y style='width : 20px; height : ".$taille_px."px;'>".$coord['y']."</li>"; //-- Bord gauche de la map

				$y_BAK = $coord['y'];
			}
			$background = "";
			$overlib = "";
			
			//Si c'est là où est le bourg
			if($bourg->x == $coord['x'] && $bourg->y == $coord['y'])
			{
				$background = "background-image : url('../image/batiment/".$bourg->image."_04.png') !important;";
			}
			else
			{
				$check = false;
				foreach($bourg->mines as $mine)
				{
					if($mine->x == $coord['x'] && $mine->y == $coord['y'])
					{
						$background = "background-image : url('../image/batiment/".$mine->image."_04.png') !important; background-repeat : no-repeat;";
						$check = true;
					}
				}
				if(!$check)
				{
					foreach($bourg->placements as $placement)
					{
						if($placement->x == $coord['x'] && $placement->y == $coord['y'])
						{
							$background = "background-image : url('../image/batiment/mine_04.png') !important; background-repeat : no-repeat;";
						}
					}
				}
			}
			
			$border = "border:0px solid ".$Gcouleurs[$objMap->royaume].";";
			echo "<li class='$class_map' style='width : ".$taille_px."px; height : ".$taille_px."px;'>
					<div class='map_contenu' 
							id='marq$case' 
							style=\"".$background.$border."width : ".$taille_px."px; height : ".$taille_px."px;\" ";
			echo " 		onclick=\"envoiInfo('mine.php?case=".$objMap->ID."&amp;id_bourg=".$bourg->id."', 'info_mine');\" 
					>&nbsp;</div>
					</li>";	
			
			$case++;
		}
		echo "</ul>";
	}
	?>
	</div>
	<div id="infos">
		<div id="info_bourg">
			Type : <?php echo $bourg->nom; ?><br />
			X : <?php echo $bourg->x; ?><br />
			Y : <?php echo $bourg->y; ?><br />
			Mines : <?php echo $bourg->mine_total; ?> / <?php echo $bourg->mine_max; ?>
			<ul style="margin-left : 15px;">
			<?php
				foreach($bourg->mines as $mine)
				{
					$mine->get_evolution();
					$overlib = 'Pierre : '.$mine->ressources['Pierre'].'
					Bois : '.$mine->ressources['Bois'].'
					Eau : '.$mine->ressources['Eau'].'
					Sable : '.$mine->ressources['Sable'].'
					Nourriture : '.$mine->ressources['Nourriture'].'
					Charbon : '.$mine->ressources['Charbon'].'
					Essence Magique : '.$mine->ressources['Essence Magique'].'
					Star : '.$mine->ressources['Star'];
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						'.$mine->nom.' - X : '.$mine->x.' - Y : '.$mine->y.' - <a href="mine.php?mine='.$mine->id.'&amp;up" onclick="return envoiInfo(this.href, 'info_mine');">Evoluer ('.$mine->evolution['cout'].' stars)</a> - <a href="mine.php?mine='.$mine->id.'&amp;suppr" onclick="return envoiInfo(this.href, 'info_mine'>X</a>
					</li>';
				}
			?>
			</ul>
			En construction
			<ul style="margin-left : 15px;">
			<?php
				foreach($bourg->placements as $placement)
				{
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						'.$placement->nom.' - X : '.$placement->x.' - Y : '.$placement->y.' - fin dans '.transform_sec_temp($placement->fin_placement - time()).'
					</li>';
				}
			?>
			</ul>
		</div>
		<div id="info_mine">
		</div>
	</div>
	<?php
}
//Info d'une case
elseif(array_key_exists('case', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	check_case($coord);
	echo 'CASE : X : '.$coord['x'].' - Y : '.$coord['y'].'<br />';
	$bourg = new bourg($_GET['id_bourg']);
	$bourg->get_mines();
	$bourg->get_placements();
	$bourg->get_mine_total();
	if($bourg->mine_max > $this->mine_total)
	{
		//On vérifie qu'il y a pas déjà une construction sur cette case
		$requete = "SELECT id FROM construction WHERE x = ".$coord['x']." AND y = ".$coord['y'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			echo 'Construction impossible, il y a déjà un batiment';
		}
		else
		{
			//On vérifie qu'il y a pas déjà une construction sur cette case
			$requete = "SELECT id FROM placement WHERE x = ".$coord['x']." AND y = ".$coord['y'];
			$db->query($requete);
			if($db->num_rows > 0)
			{
				echo 'Construction impossible, il y a déjà un batiment en construction';
			}
			//On peut construire une mine
			else
			{
				$requete = "SELECT id, nom, cout, bonus1, bonus2 FROM batiment WHERE type = 'mine' AND cond1 = 0";
				$req = $db->query($requete);
				?>
				Quel mine voulait vous construire ?<br />
				<select name="type_mine" id="type_mine">
				<?php
				while($row = $db->read_assoc($req))
				{
					$description = '';
					if($row['bonus2'] != 0)
					{
						switch($row['bonus2'])
						{
							case 1 :
								$description = 'Pierre x'.$row['bonus1'];
							break;
							case 2 :
								$description = 'Bois x'.$row['bonus1'];
							break;
							case 3 :
								$description = 'Eau x'.$row['bonus1'];
							break;
							case 4 :
								$description = 'Sable x'.$row['bonus1'];
							break;
							case 5 :
								$description = 'Nourriture x'.$row['bonus1'];
							break;
							case 6 :
								$description = 'Star x'.$row['bonus1'];
							break;
							case 7 :
								$description = 'Charbon x'.$row['bonus1'];
							break;
							case 8 :
								$description = 'Essence Magique x'.$row['bonus1'];
							break;
						}
					}
					else $description = 'Toute ressources x'.$row['bonus1'];
					echo '<option value="'.$row['id'].'">'.$row['nom'].' - '.$row['cout'].' stars ('.$description.')</option>';
				}
				?>
				</select>
				<input type="button" onclick="envoiInfo('mine.php?bourg=<?php echo $_GET['id_bourg']; ?>&amp;x=<?php echo $coord['x']; ?>&amp;y=<?php echo $coord['y']; ?>&amp;add=' + $('type_mine').value, 'info_mine');" value="Valider" />
				<?php
			}
		}
	}
	else
	{
		echo 'Construction impossible, ce bourg ne peut plus avoir de mine associée';
	}
}
//Ajout d'une mine
elseif(array_key_exists('add', $_GET))
{
	$bourg = new bourg($_GET['bourg']);
	$bourg->get_mine_total();

	if($bourg->mine_total < $bourg->mine_max)
	{
		$requete = "SELECT nom, hp,temps_construction, cout FROM batiment WHERE id = ".$_GET['add'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);

		//On vérifie si on a assez de stars
		if($R['star'] >= $row['cout'])
		{
			$distance = calcul_distance(convert_in_pos($Trace[$R['race']]['spawn_x'], $Trace[$R['race']]['spawn_y']), convert_in_pos($_GET['x'], $_GET['y']));
			$time = time() + ($row['temps_construction'] * $distance);

			$placement = new placement();
			$placement->id_royaume = $R['ID'];
			$placement->id_batiment = $_GET['add'];
			$placement->x = $_GET['x'];
			$placement->y = $_GET['y'];
			$placement->hp = $row['hp'];
			$placement->nom = $row['nom'];
			$placement->rez = $_GET['bourg'];
			$placement->type = 'mine';
			$placement->fin_placement = $time;
			$placement->sauver();
			
			//On enlève les stars au royaume
			$requete = "UPDATE royaume SET star = star - ".$row['cout']." WHERE ID = ".$R['ID'];
			$db->query($requete);
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars';
		}
	}
}
//Ajout d'une mine
elseif(array_key_exists('up', $_GET))
{
	$mine = new mine($_GET['mine']);
	$mine->get_evolution();


	//On vérifie si on a assez de stars
	if($R['star'] >= $mine->evolution['cout'])
	{
		$mine->id_batiment = $mine->evolution['id'];
		$mine->hp = round(($mine->hp / $mine->get_hp_max()) * $mine->evolution['hp']);
		$mine->nom = $mine->evolution['nom'];
		$mine->sauver();
	}
	else
	{
		echo 'Vous n\'avez pas assez de stars';
	}
}
elseif(array_key_exists('suppr', $_GET))
{
	$mine = new mine($_GET['mine']);
	if($mine->id_royaume == $R['ID']) $mine->supprimer();
}
else
{
	$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE type = 'bourg' AND royaume = ".$R['ID'];
	$req = $db->query($requete);
	
	?>
	<ul>
	<?php
	while($row = $db->read_assoc($req))
	{
		$bourg = new bourg($row);
		$bourg->get_mines();
		$bourg->get_placements();
		$bourg->get_mine_total();
		echo '<li><a href="mine.php?id='.$bourg->id.'" onclick="return envoiInfo(this.href, \'conteneur\');">'.$bourg->nom.'</a> - X : '.$bourg->x.' - Y : '.$bourg->y.' - ('.$bourg->mine_total.' / '.$bourg->mine_max.')</li>';
		if(count($bourg->mines) > 0)
		{
		?>
		<ul style="margin-left : 15px;">
		<?php
			foreach($bourg->mines as $mine)
			{
				echo '<li>'.$mine->nom.' - X : '.$mine->x.' - Y : '.$mine->y.'</li>';
			}
		?>
		</ul>
		<ul style="margin-left : 15px;">
		<?php
			foreach($bourg->placements as $placement)
			{
				echo '<li>'.$placement->nom.' - X : '.$placement->x.' - Y : '.$placement->y.' - fin dans '.transform_sec_temp($placement->fin_placement - time()).'</li>';
			}
		?>
		</ul>
		<?php
		}
	}
	?>
	</ul>
<?php
}
?>