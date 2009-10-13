<?php
if (file_exists('../root.php'))
  include_once('../root.php');

require('haut_roi.php');
include_once(root.'class/construction.class.php');
include_once(root.'class/bourg.class.php');
include_once(root.'class/mine.class.php');
include_once(root.'class/placement.class.php');

if($joueur->get_rang_royaume() != 6)
	echo '<p>Cheater</p>';
else if(array_key_exists('id', $_GET))
{
	$bourg = new bourg($_GET['id']);
	$bourg->get_mine_max();
	$bourg->get_mines(true);
	$bourg->get_placements();
	$bourg->get_mine_total();
	$x = $bourg->get_x();
	$y = $bourg->get_y();
	//echo '<pre>';
	//print_r($bourg->mines);
	$batiments = array_merge($bourg->mines, $bourg->placements);
	//echo "<pre>";
	//print_R($batiments);
	//echo "</pre>";
	$batiments[] = $bourg;
	?>
	<div id="map_mine" style='float:left;'>
	<?php
	$map = new map($x, $y, 5, '../', false, 'high');
	$map->quadrillage = true;
	$map->set_batiment_objet($batiments);
	$map->onclick_status = true;
	$map->affiche_terrain = true;
	$map->set_affiche_royaume($_GET['R'] == 1);
	if ($_GET['R'] == 1)
		$royaume = '&R=0';
	else
		$royaume = '&R=1';
	$map->show_royaume_button = "envoiInfo('mine.php?id=$_GET[id]$royaume', 'contenu_jeu');";
	$map->set_onclick("affichePopUp('mine.php?case=%%id%%&amp;id_bourg=".$bourg->get_id()."');");
	$map->affiche();
	?>
	</div>
	<div id="infos" style='margin-left:5px;float:left;width:275px;'>
		<fieldset>
			<legend><?php echo $bourg->get_nom(); ?> en <?php echo $bourg->get_x(); ?> / <?php echo $bourg->get_y(); ?> - Mines : <?php echo $bourg->mine_total; ?> / <?php echo $bourg->mine_max; ?></legend>
			
			<ul>
			<?php
				foreach($bourg->mines as $mine)
				{
					$overlib = '';
					$mine->get_evolution();
					if(!empty($mine->ressources['Pierre'])) 		{$overlib .= 'Pierre : '.$mine->ressources['Pierre'].'<br />';}
					if(!empty($mine->ressources['Bois'])) 			{$overlib .= 'Bois : '.$mine->ressources['Bois'].'<br />';}
					if(!empty($mine->ressources['Eau'])) 			{$overlib .= 'Eau : '.$mine->ressources['Eau'].'<br />';}
					if(!empty($mine->ressources['Sable'])) 			{$overlib .= 'Sable : '.$mine->ressources['Sable'].'<br />';}
					if(!empty($mine->ressources['Nourriture'])) 	{$overlib .= 'Nourriture : '.$mine->ressources['Nourriture'].'<br />';}
					if(!empty($mine->ressources['Charbon'])) 		{$overlib .= 'Charbon : '.$mine->ressources['Charbon'].'<br />';}
					if(!empty($mine->ressources['Essence Magique'])){$overlib .= 'Essence Magique : '.$mine->ressources['Essence Magique'].'<br />';}
					if(!empty($mine->ressources['Star'])) 			{$overlib .= 'Star : '.$mine->ressources['Star'].'<br />';}
					if($mine->evolution['cout'] != '') $evolution = ' <a onclick="envoiInfo(\'mine.php?mine='.$mine->get_id().'&amp;up\', \'info_mine\');$(\'info_mine\').show();" title="Evoluer ('.$mine->evolution['cout'].' stars)">Up</a>';
					else $evolution = '';
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						<span class="nom">'.$mine->get_nom().'</span><span class="position">'.$mine->get_x().' / '.$mine->get_y().$evolution.'</span><span class="supprimer"><a href="mine.php?mine='.$mine->get_id().'&amp;bourg='.$bourg->get_id().'&amp;suppr" onclick="if(confirm(\'Voulez vous supprimer cette mine ?\')) {return envoiInfo(this.href, \'info_mine\');envoiInfo(\'mine.php?id='.$bourg->get_id().'\', \'contenu_jeu\');} else {return false;}">X</a></span>
					</li>';					
				}
			?>
			</ul>
			<?php
			if (count($bourg->placements)>0)
			{
			?>
			<strong>En construction</strong>
			<ul>
			<?php
				$bourg->placements;
				foreach($bourg->placements as $placement)
				{
					$overlib = "Fin de construction dans ".transform_sec_temp($placement->get_fin_placement() - time());
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						<span class="nom">'.$placement->get_nom().'</span><span class="position">'.$placement->get_x().' / '.$placement->get_y().'</span>
					</li>';
				}
			}
			?>
			</ul>
		</fieldset>
		<fieldset>
			<legend>Info</legend>
			<p>Pour mettre un extracteur, cliquez sur une case vous appartenant sur la map.</p>
		</fieldset>
		
		<fieldset id="info_mine">
		</fieldset>
	</div>
	<?php
}
//Info d'une case
elseif(array_key_exists('case', $_GET))
{
	$case = new map_case($_GET['case']);
	$case->check_case();
	$coord = convertd_in_coord($case->get_id());
	$bourg = new bourg($_GET['id_bourg']);
	$bourg->get_mines();
	$bourg->get_mine_max();
	$bourg->get_placements();
	$bourg->get_mine_total();
	if($bourg->mine_max > $bourg->mine_total)
	{
		//On vérifie que la case appartient bien au royaume
		$requete = "SELECT ID, type FROM map WHERE ID = ".sSQL($_GET['case'])." AND royaume = ".$royaume->get_id();
		$db->query($requete);
		if($db->num_rows == 0)
		{
			echo '<h5>Construction impossible, ce terrain ne vous appartient pas</h5>';
		}
		else
		{
			$row = $db->read_assoc($req);
			if($row['type'] == 0)
			{
				//On vérifie qu'il y a pas déjà une construction sur cette case
				$requete = "SELECT id FROM construction WHERE x = ".$coord['x']." AND y = ".$coord['y'];
				$db->query($requete);
				if($db->num_rows > 0)
				{
					echo '<h5>Construction impossible, il y a déjà un batiment</h5>';
				}
				else
				{
					//On vérifie qu'il y a pas déjà une construction sur cette case
					$requete = "SELECT id FROM placement WHERE x = ".$coord['x']." AND y = ".$coord['y'];
					$db->query($requete);
					if($db->num_rows > 0)
					{
						echo '<h5>Construction impossible, il y a déjà un batiment en construction</h5>';
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
						<input type="button" onclick="envoiInfo('mine.php?bourg=<?php echo $_GET['id_bourg']; ?>&amp;x=<?php echo $coord['x']; ?>&amp;y=<?php echo $coord['y']; ?>&amp;add=' + $('#type_mine').val(), 'popup_content');refresh('mine.php?id=<?php echo $bourg->get_id();?>','contenu_jeu');$('#popup').hide();" value="Valider" />
						<?php
					}
				}
			}
			else echo '<h5>Vous ne pouvez pas construire sur ce type de terrain</h5>';
		}
	}
	else
	{
		echo '<h5>Construction impossible, ce bourg ne peut plus avoir de mine associée</h5>';
	}
}
//Ajout d'une mine
elseif(array_key_exists('add', $_GET))
{
	$bourg = new bourg($_GET['bourg']);
	$bourg->get_mine_total();
	$bourg->get_mine_max();

	if($bourg->mine_total < $bourg->mine_max)
	{
		$requete = "SELECT nom, hp,temps_construction, cout FROM batiment WHERE id = ".sSQL($_GET['add']);
		$req = $db->query($requete);
		$row = $db->read_assoc($req);

		//On vérifie si on a assez de stars
		if($royaume->get_star() >= $row['cout'])
		{
			$distance = calcul_distance(convert_in_pos($Trace[$royaume->get_race()]['spawn_x'], $Trace[$royaume->get_race()]['spawn_y']), convert_in_pos($_GET['x'], $_GET['y']));
			$time = time() + ($row['temps_construction'] * $distance);

			$placement = new placement();
			$placement->set_royaume($royaume->get_id());
			$placement->set_id_batiment($_GET['add']);
			$placement->set_x($_GET['x']);
			$placement->set_y($_GET['y']);
			$placement->set_hp($row['hp']);
			$placement->set_nom($row['nom']);
			$placement->set_rez($_GET['bourg']);
			$placement->set_type('mine');
			$placement->set_debut_placement(time()); // Sans ća l'acceleration est trop forte
			$placement->set_fin_placement($time);
			$placement->sauver();
			
			//On enlève les stars au royaume
			$requete = "UPDATE royaume SET star = star - ".$row['cout']." WHERE ID = ".$royaume->get_id();
			$db->query($requete);
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars';
		}
	}
}
//Upgrade d'une mine
elseif(array_key_exists('up', $_GET))
{
	$mine = new mine($_GET['mine']);
	$mine->get_evolution();
	echo $mine->evolution['cond1']."<br />";
	echo (time() - $mine->get_date_construction());
	//On vérifie si on a assez de stars
/*	if(($royaume->get_star() >= $mine->evolution['cout']) AND ($mine->evolution['cond1'] < (time() - $mine->get_date_construction())) )
	{
		$mine->hp = round(($mine->hp / $mine->get_hp_max()) * $mine->evolution['hp']);
		$mine->id_batiment = $mine->evolution['id'];
		$mine->set_nom($mine->evolution['nom']);
		$mine->sauver();

		//On enlève les stars au royaume
		$requete = "UPDATE royaume SET star = star - ".$mine->evolution['cout']." WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		echo "Vous venez de faire évoluer votre ".$mine->get_nom();
	}
	else
	{
		echo 'Vous n\'avez pas assez de stars';
	}*/
}
elseif(array_key_exists('suppr', $_GET))
{
	$mine = new mine($_GET['mine']);
	
	if($mine->get_royaume() == $royaume->get_id())
	{
		$mine->supprimer();
	}
}
else
{
	$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE type = 'bourg' AND royaume = ".$royaume->get_id();
	$req = $db->query($requete);
	
	?>
	<div id='mine'>
	<?php
	while($row = $db->read_assoc($req))
	{
		$bourg = new bourg($row);
		$bourg->get_mines();
		$bourg->get_placements();
		$bourg->get_mine_total();
		$bourg->get_mine_max();
		echo '<fieldset onclick="envoiInfo(\'mine.php?id='.$bourg->get_id().'\', \'contenu_jeu\');">
		<legend>'.$bourg->get_nom().'</a> - '.$bourg->get_x().' / '.$bourg->get_y().' - ('.$bourg->mine_total.' / '.$bourg->mine_max.')
		</legend>';
		if(count($bourg->mines) > 0)
		{
		?>
		<ul>
		<?php
			foreach($bourg->mines as $mine)
			{
				echo '<li>'.$mine->get_nom().'</li>';
			}
		?>
		</ul>
		<?php
		}
		if(count($bourg->placements) > 0)
		{
		?>
		<strong>Construction</strong>
		<ul>
		<?php
			foreach($bourg->placements as $placement)
			{
				echo '<li>'.$placement->get_nom().'  - fin dans  '.transform_sec_temp($placement->get_fin_placement() - time()).'</li>';
			}
		?>
		</ul>
		<?php
		}
		echo '</fieldset>';
	}
	?>
	</div>
<?php
}
?>
