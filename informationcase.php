<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
/**
* 
* Permet l'affichage des informations d'une case en fonction du joueur.
* 
*/
include_once(root.'inc/fp.php');
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);
if ($joueur->is_buff('buff_rapidite')) $reduction_pa = $joueur->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
if ($joueur->is_debuff('debuff_ralentissement')) $reduction_pa -= $joueur->get_debuff('debuff_ralentissement', 'effet');

//Case et coordonnées de la case
$W_case = $_GET['case'];

//Vérifie si il y a eu des modifications sur la case (fin de batiments drapeaux et autres)
$case = new map_case($W_case);
$case->check_case();

//Calcul de la distance qui sépare le joueur de la case en question
$W_distance = detection_distance($W_case, convert_in_pos($joueur->get_x(), $joueur->get_y()));
if($W_distance < 4)
{
	?>
	<fieldset>
	<legend>Informations Case - X : <?php echo $case->get_x(); ?> | Y : <?php echo $case->get_y(); ?><a href="carte_perso_affiche.php" onclick="affichePopUp(this.href); return false;"> <img src="image/icone/oujesuis.png" alt="Où je suis ?" title="Où je suis ?" style="vertical-align : middle;height:20px;" /></a> </legend>
	<div id='info_case'>
	<?php
	$R = new royaume($case->get_royaume());
	$type_terrain = type_terrain($case->get_info());
	$coutpa = cout_pa($type_terrain[0], $joueur->get_race());
	$coutpa_base = $coutpa;
	$coutpa_diagonale = cout_pa2($coutpa, $joueur, $case, true);
	$coutpa = cout_pa2($coutpa, $joueur, $case, false);
	if ($coutpa_base > 49) $coutpa = 'Infranchissable';
	
	//Si c'est la capitale
	if($case->get_x() == $Trace[$R->get_race()]['spawn_x'] AND $case->get_y() == $Trace[$R->get_race()]['spawn_y'])
	{
		echo '<h3>Capitale '.$R->get_capitale().'</h3>';
	}
	?>
	
	<h4><span class='titre_info'><?php echo $R->get_nom(); ?></span></h4>
	<strong><?php echo $Gtrad[$R->get_race()]; ?></strong> - <?php echo $Gtrad['diplo'.$R->get_diplo($joueur->get_race())]; ?> - Taxe : <?php echo $R->get_taxe(); ?>%<br />
	<strong><?php echo $type_terrain[1]; ?></strong> - <?php echo $coutpa; ?> PA de déplacement <span class="xsmall">(en diagonale = <?php echo $coutpa_diagonale; ?> PA)</span>
	
	<?php
	//Recherche des joueurs sur la case
	$W_requete = 'SELECT perso.id, perso.nom, race, classe, hp, rang_royaume, grade.nom as gnom FROM perso LEFT JOIN grade ON perso.rang_royaume = grade.id WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().') AND statut = \'actif\'';
	$W_query = $db->query($W_requete);
	
	//Affichage des infos des joueurs
	if($db->num_rows > 0)
	{
		echo '	
		<h4><span class="titre_info">Joueurs</span></h4>
		<ul>';
		while ($W_row = $db->read_array($W_query))
		{
			affiche_perso_visu($joueur, $W_row);
		}
		echo '</ul>';
	}
	
		
	//Affichage des PNJ
	$W_requete = 'SELECT * FROM pnj WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().')';
	$W_query = $db->query($W_requete);
	
	$num_rows = $db->num_rows;
	if($num_rows > 0)
	{
		echo '
	
		<h4><span class="titre_info">PNJ</span></h4>';
		while($W_row = $db->read_array($W_query))
		{
			echo  '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
			if($W_row['x'] == $joueur->get_x() AND $W_row['y'] == $joueur->get_y()) echo ' <a href="pnj.php?id='.$W_row['id'].'" onclick="return envoiInfo(this.href, \'information\')">Parler...</a>';
			echo '<br />';
		}
	}
	
	//Affichage des Donjons
	$RqDonjon = 'SELECT * FROM donjon WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().')';
	
	$W_query = $db->query($RqDonjon);
	
	$num_rows = $db->num_rows;
	if($num_rows > 0)
	{
		echo '
		<h4><span class="titre_info">Donjons</span></h4>';
		while($W_row = $db->read_array($W_query))
		{
			echo '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
			//Entrée du donjon
			$quete_fini = explode(';', $joueur->get_quete_fini());
			$unlock = false;
			//Myriandre locké si pas fini la quête
			if($W_row['nom'] == 'Myriandre')
			{
				if(in_array('53', $quete_fini)) $unlock = true;
			}
			elseif($W_row['nom'] == 'Teleport')
			{
				$requete_dragon = 'SELECT id FROM map_monstre WHERE type = 125 OR type = 126';
				$req_dragon = $db->query($requete_dragon);
				
				$num_rows = $db->num_rows;
				//Si les 2 dragons sont morts alors on ouvre
				if($num_rows == 0) $unlock = true;
			}
			else $unlock = true;
			if($W_row['x'] == $joueur->get_x() AND $W_row['y'] == $joueur->get_y() AND $unlock) echo ' <a href="jeu2.php?donjon_id='.$W_row['id'].'">Entrer dans le donjon</a>';
			echo '<br />';
		}
	}
	
	//Affichage des sorties de Donjons
	$W_requete = 'SELECT * FROM donjon WHERE (x_donjon = '.$case->get_x().') AND (y_donjon = '.$case->get_y().')';
	$W_query = $db->query($W_requete);
	
	$num_rows = $db->num_rows;
	if($num_rows > 0)
	{
		echo '
		<h4><span class="titre_info">Donjons</span></h4>';
		while($W_row = $db->read_array($W_query))
		{
			echo  '&nbsp;&nbsp;&nbsp;'.$W_row['nom'];
			//Sortie du donjon
			if($W_row['x_donjon'] == $joueur->get_x() AND $W_row['y_donjon'] == $joueur->get_y()) echo ' <a href="jeu2.php?donjon_id='.$W_row['id'].'&amp;type=sortie">Sortir du donjon</a>';
			echo '<br />';
		}
	}
	
	//Affichage des placements
	$W_requete = 'SELECT * FROM placement WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().')';
	$W_query = $db->query($W_requete);
	
	$num_rows = $db->num_rows;
	if($num_rows > 0)
	{
		echo '
		<h4><span class="titre_info">En construction</span></h4>';
		while($W_row = $db->read_array($W_query))
		{
			//Recherche du batiment
			$requete = "SELECT * FROM batiment WHERE id = ".$W_row['id_batiment'];
			$req = $db->query($requete);
			$row_b = $db->read_assoc($req);
			$Royaume = new royaume($W_row['royaume']);
			echo '<span onmousemove="return '.make_overlib(transform_sec_temp($W_row['fin_placement'] - time()).' avant fin de construction').'" onmouseout="return nd();">'.$row_b['nom'].' '.$Gtrad[$Royaume->get_race()].'</span> - HP : '.$W_row['hp'];
			if($joueur->get_race() != $Royaume->get_race())
			{
				if(!array_key_exists('repos_sage', $joueur->get_debuff()))
				{
					echo ' <a href="attaque.php?id_batiment='.$W_row['id'].'&amp;type=batiment&amp;table=placement" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> Attaquer <span class="xsmall">('.($G_PA_attaque_batiment - $reduction_pa).' PA)</a>';
				}
			}
			else
			{
					echo ' <a href="archi_accelere_construction.php?id_construction='.$W_row['id'].'" onclick="return envoiInfo(this.href, \'information\')">Accélérer <span class="xsmall">(30 PA)</a>';
			}
			echo '<br />';
		}
	}
	
	//Affichage des batiments
	$W_requete = 'SELECT * FROM construction WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().')';
	$W_query = $db->query($W_requete);
	
	$num_rows = $db->num_rows;
	if($num_rows > 0)
	{
		echo '
		<h4><span class="titre_info">Batiments</span></h4>';
		while($W_row = $db->read_array($W_query))
		{
			//Recherche du batiment
			$requete = "SELECT * FROM batiment WHERE id = ".$W_row['id_batiment'];
			$req = $db->query($requete);
			$row_b = $db->read_assoc($req);
			$Royaume = new royaume($W_row['royaume']);
			echo '<span onmousemove="return '.make_overlib($row_b['description']).'" onmouseout="return nd();"><image src="image/mini_'.$row_b['type'].'.png" style="vertical-align : top;" title="'.$row_b['nom'].'" alt="'.$row_b['nom'].'" />';
			$nom = $row_b['nom'];
			if($row_b['type'] == 'fort' AND $joueur->get_race() == $Royaume->get_race()) $nom = '<a href="fort.php?id_batiment='.$row_b['id'].'" onclick="return envoiInfo(this.href, \'centre\');">'.$row_b['nom'].'</a>';
			if($row_b['type'] == 'bourg' AND $joueur->get_race() == $Royaume->get_race()) $nom = '<a href="bourg.php?id_batiment='.$row_b['id'].'" onclick="return envoiInfo(this.href, \'centre\');">'.$row_b['nom'].'</a>';
			if($row_b['type'] == 'arme_de_siege' AND $joueur->get_race() == $Royaume->get_race()) $nom = '<a href="arme_de_siege.php?id_construction='.$W_row['id'].'" onclick="return envoiInfo(this.href, \'centre\');">'.$row_b['nom'].'</a>';
			if($row_b['type'] == 'tour' AND $joueur->get_race() == $Royaume->get_race()) $nom = '<a href="tour.php?id_construction='.$W_row['id'].'" onclick="return envoiInfo(this.href, \'centre\');">'.$row_b['nom'].'</a>';
			echo ' '.$nom;
			echo ' '.$Gtrad[$Royaume->get_race()].' - HP : '.$W_row['hp'].' / '.$row_b['hp'];
			echo '</span>';
			if($joueur->get_race() != $Royaume->get_race() && $row_b['type'] != 'bourg') echo  ' <a href="attaque.php?id_batiment='.$W_row['id'].'&amp;type=batiment&amp;table=construction" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer '.($G_PA_attaque_batiment - $reduction_pa).' PA" style="vertical-align : middle;" /> Attaquer</a>';
			elseif($W_row['hp'] < $row_b['hp'])
			{
				echo ' <a href="archi_soigne_construction.php?id_construction='.$W_row['id'].'" onclick="return envoiInfo(this.href, \'information\')">Réparer <span class="xsmall">(30 PA)</a>';
			}
			echo '<br />';
		}
	}
	
	$pa_attaque = $G_PA_attaque_monstre;
	if(array_key_exists('cout_attaque', $joueur->get_debuff())) $pa_attaque = ceil($pa_attaque / $joueur->get_debuff('cout_attaque', 'effet'));
	if(array_key_exists('plus_cout_attaque', $joueur->get_debuff())) $pa_attaque = $pa_attaque * $joueur->get_debuff('plus_cout_attaque', 'effet');
	
	$W_requete = 'SELECT id, nom, type, hp, level FROM map_monstre WHERE (x = '.$case->get_x().') AND (y = '.$case->get_y().') ORDER BY level ASC, nom ASC, id ASC';
	$W_query = $db->query($W_requete);
	
	//Affichage des infos des monstres
	if($db->num_rows > 0)
	{
		echo '
	<h4><span class="titre_info">Monstres</span></h4>
	<ul>';
		while($W_row = $db->read_array($W_query))
		{
			$W_nom = $W_row['nom'];
			$W_type = $W_row['type'];
			$W_ID = $W_row['id'];
			//Recherche des capas du mob
			$W2_requete = 'SELECT * FROM monstre WHERE id = '.$W_type.' ORDER BY level ASC';
			$W2_query = $db->query($W2_requete);
			$W2_row = $db->read_array($W2_query);
			$W_hp = $W_row['hp'];
			$diff_level = ($W2_row['level'] - $joueur->get_level());
			if($diff_level > 5) $diff_level = 5;
			elseif($diff_level < -5) $diff_level = -5;
			//echo $diff_level;
			$color = $G_consider[$diff_level];
			if($diff_level > 0) $strong = 'bold'; else $strong = 'normal';
			// on envois dans infojoueur.php -> ID du joueur et La position de la case ou il se trouve
			$image = $W2_row['lib'];
			if (file_exists('image/monstre/'.$image.'.png')) $image .= '.png';
			else $image .= '.gif';
			echo '
			<li style="clear:both;"><img src="image/monstre/'.$image.'" alt="'.$W2_row['nom'].'" style="vertical-align : middle;height:21px;float:left;width:21px;" /><span style="color : '.$color.'; font-weight : '.$strong.';float:left;width:325px;margin-left:15px;">'.$W_nom.'</span>
			
				<span style="float:left;">';
				if(!array_key_exists('repos_sage', $joueur->get_debuff()) OR !array_key_exists('bloque_attaque', $joueur->get_debuff())) echo '
				<a href="attaque.php?id_monstre='.$W_ID.'&type=monstre" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquez ce monstez ('.($pa_attaque - $reduction_pa).' PA)" style="vertical-align : middle;" /></a>';
				echo ' <a href="info_monstre.php?ID='.$W_ID.'&poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/icone/mobinfo.png" alt="Voir informations sur le monstre" title="Voir informations sur le monstre" style="vertical-align : middle;" /></a>';
				if($joueur->get_sort_jeu() != '') echo ' <a href="sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$W_ID.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/sort_hc_icone.png" title="Lancer un sort" alt="Lancer un sort" style="vertical-align : middle;" /></a>';
			echo '</span>
				</li>';
		}
		echo '</ul>';
	}
	echo "</div></fieldset>";
}
?>

