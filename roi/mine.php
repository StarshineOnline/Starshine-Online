<?php
require('haut_roi.php');
include('../class/bourg.class.php');
include('../class/mine.class.php');

if(array_key_exists('id', $_GET))
{
	$bourg = new bourg($_GET['id']);
	$bourg->get_mines();
	$x = $bourg->x;
	$y = $bourg->y;
	?>
	<div id="map_mine">
	<?php
	//-- Champ de vision = 5 par d?faut
	$champ_vision = 5;
	//-- Nombre de case affich?es en longueur et largeur
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
						 
	echo '<div id="carte" style="width : 605px; height : 610px;">';
	{//-- Affichage du bord haut (bh) de la map
		echo "<ul id='map_bord_haut'>
				<li id='map_bord_haut_gauche' style='width : 20px; height : 20px;' onclick=\"switch_map();\">&nbsp;</li>";
		for ($bh = $x_min; $bh <= $x_max; $bh++)
		{
			if($bh == $x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
			echo "<li $class_x style='width : 20px; height : 20px;'>$bh</li>";
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
			$class_map = "decor texl".$objMap->decor;	//-- Nom de la classe "terrain" contenu dans texture.css
			
			if($coord['y'] != $y_BAK)
			{//-- On passe a la ligne
				if($Once) { echo "</ul>"; } else { $Once = true; };
				if($coord['y'] == $y) { $class_y = "id='bord_haut_y' "; } else { $class_y = ""; }; //-- Pour mettre en valeur la position Y ou se trouve le joueur
				echo "<ul class='map' style='height : 20px;'>
						<li $class_y style='width : 20px; height : 20px;'>".$coord['y']."</li>"; //-- Bord gauche de la map

				$y_BAK = $coord['y'];
			}
			$background = "";
			$overlib = "";
			
			$border = "border:0px solid ".$Gcouleurs[$objMap->royaume].";";
			echo "<li class='$class_map' style='width : 20px; height : 20px;'>
					<div class='map_contenu' 
							id='marq$case' 
							style=\"".$background.$border."width : 20px; height : 20px;\" ";
			echo " 		onclick=\"envoiInfo('mine.php?case=".$objMap->ID."&amp;id_bourg=".$bourg->id_bourg."', 'info_mine');\" 
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
			Mines : <?php echo count($bourg->mines); ?> / <?php echo $bourg->mine_max; ?>
		</div>
		<div id="info_mine">
		</div>
	</div>
	<?php
}
elseif(array_key_exists('case', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	echo 'CASE : X : '.$coord['x'].' - Y : '.$coord['y'].'<br />';
	$bourg = new bourg($_GET['id_bourg']);
	$bourg->get_mines();
	if($bourg->mine_max > count($bourg->mines))
	{
		//On vérifie qu'il y a pas déjà une construction sur cette case
		$requete = "SELECT * FROM construction WHERE x = ".$coord['x']." AND y = ".$coord['y'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			echo 'Construction impossible, il y a déjà un batiment';
		}
		//On peut construire une mine
		else
		{
			?>
			Quel mine voulait vous construire ?<br />
			<select name="type_mine" id="type_mine">
				<option value="">TEST</option>
			</select>
			<?php
		}
	}
	else
	{
		echo 'Construction impossible, ce bourg ne pas plus avoir de mine associée';
	}
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
		echo '<li><a href="mine.php?id='.$bourg->id_bourg.'" onclick="return envoiInfo(this.href, \'conteneur\');">'.$bourg->nom.'</a> - X : '.$bourg->x.' - Y : '.$bourg->y.'</li>';
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
		<?php
		}
	}
	?>
	</ul>
<?php
}
?>