<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
	if(array_key_exists('javascript', $_GET)) include_once(root.'inc/fp.php');
	//Tableau des classements
	$tab_classement = array();
	$tab_classement['exp']['nom'] = 'Les plus expérimentés';
	$tab_classement['exp']['champ'] = 'Expérience';
	$tab_classement['exp']['affiche'] = true;
	$tab_classement['exp']['affiche_niveau'] = true;
	$tab_classement['honneur']['nom'] = 'Les plus honorés';
	$tab_classement['honneur']['champ'] = 'Honneur';
	$tab_classement['honneur']['affiche'] = true;
	$tab_classement['honneur']['affiche_niveau'] = false;
	$tab_classement['star']['nom'] = 'Les plus fortunés';
	$tab_classement['star']['champ'] = '';
	$tab_classement['star']['affiche'] = false;
	$tab_classement['star']['affiche_niveau'] = false;
	$tab_classement['melee']['nom'] = 'Les meilleurs combattants';
	$tab_classement['melee']['champ'] = 'Mêlée';
	$tab_classement['melee']['affiche'] = true;
	$tab_classement['melee']['affiche_niveau'] = false;
	$tab_classement['esquive']['nom'] = 'Les meilleurs esquiveurs';
	$tab_classement['esquive']['champ'] = 'Esquive';
	$tab_classement['esquive']['affiche'] = true;
	$tab_classement['esquive']['affiche_niveau'] = false;
	$tab_classement['blocaga']['nom'] = 'Les meilleurs bloqueurs';
	$tab_classement['blocage']['champ'] = 'Blocage';
	$tab_classement['blocage']['affiche'] = true;
	$tab_classement['blocage']['affiche_niveau'] = false;
	$tab_classement['distance']['nom'] = 'Les meilleurs archers';
	$tab_classement['distance']['champ'] = 'Tir à Distance';
	$tab_classement['distance']['affiche'] = true;
	$tab_classement['distance']['affiche_niveau'] = false;
	$tab_classement['incantation']['nom'] = 'Les meilleurs magiciens';
	$tab_classement['incantation']['champ'] = 'Incantation';
	$tab_classement['incantation']['affiche'] = true;
	$tab_classement['incantation']['affiche_niveau'] = false;
	$tab_classement['sort_element']['nom'] = 'Les meilleurs sorciers';
	$tab_classement['sort_element']['champ'] = 'Magie élémentaire';
	$tab_classement['sort_element']['affiche'] = true;
	$tab_classement['sort_element']['affiche_niveau'] = false;
	$tab_classement['sort_mort']['nom'] = 'Les meilleurs nécromants';
	$tab_classement['sort_mort']['champ'] = 'Nécromancie';
	$tab_classement['sort_mort']['affiche'] = true;
	$tab_classement['sort_mort']['affiche_niveau'] = false;
	$tab_classement['sort_vie']['nom'] = 'Les meilleurs clercs';
	$tab_classement['sort_vie']['champ'] = 'Magie de la vie';
	$tab_classement['sort_vie']['affiche'] = true;
	$tab_classement['sort_vie']['affiche_niveau'] = false;
	$tab_classement['craft']['nom'] = 'Les meilleurs artisans';
	$tab_classement['craft']['champ'] = 'Fabrication';
	$tab_classement['craft']['affiche'] = true;
	$tab_classement['craft']['affiche_niveau'] = false;
	$tab_classement['frag']['nom'] = 'Les meilleurs en PvP';
	$tab_classement['frag']['champ'] = 'Kill';
	$tab_classement['frag']['affiche'] = true;
	$tab_classement['frag']['affiche_niveau'] = false;
	$tab_classement['mort']['nom'] = 'Ceux qui n\'ont pas peur de la mort';
	$tab_classement['mort']['champ'] = 'Mort';
	$tab_classement['mort']['affiche'] = true;
	$tab_classement['mort']['affiche_niveau'] = false;
	$tab_classement['crime']['nom'] = 'Points de crimes';
	$tab_classement['crime']['champ'] = 'Crime';
	$tab_classement['crime']['affiche'] = true;
	$tab_classement['crime']['affiche_niveau'] = false;
	$tab_classement['survie']['nom'] = 'Survivor';
	$tab_classement['survie']['champ'] = 'Survie';
	$tab_classement['survie']['affiche'] = true;
	$tab_classement['survie']['affiche_niveau'] = false;
?>
		<div id="table_classement">
<?php
	$joueur = new perso($_SESSION['ID']);
	if(!array_key_exists('tri', $_GET)) $tri = 'honneur';
	else
	{
		$tri = mysql_escape_string($_GET['tri']);
		if(!strcmp($tri, 'craft'))
		{
			$tri = 'architecture, forge, alchimie';
		}
		$i = 0;
	}
	if(array_key_exists('race', $_GET))
	{
		$race = $_GET['race'];
	}
	else
	{
		$race = 'tous';
	}
	if(array_key_exists('i', $_GET)) $i = $_GET['i'];
	else
	{
		$i = 0;
	}
	if($race == 'race')
	{
		$where = "race = '".sSQL($joueur->get_race())."'";
	}
	else
	{
		$where = '1';
	}
	if($i === 'moi')
	{
		if(!strcmp($tri, 'architecture, forge, alchimie'))
			$requete = "SELECT COUNT(*) FROM perso WHERE ROUND(SQRT(alchimie + forge + architecture)) > ".$joueur->get_artisanat()." AND statut = 'actif' AND ".$where;
		else
			$requete = "SELECT COUNT(*) FROM perso WHERE ".sSQL($tri)." > ".$joueur->get_{$tri}." AND statut = 'actif' AND ".$where;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$sup = $row[0] + 15;
		$inf = $row[0] - 10;
		if($inf < 0)
		{
			$inf = 0;
			$sup = 26;
		}
		$k = $inf - 25;
	}
	else
	{
		$inf = $i;
		$sup = $inf + 26;
		$k = $inf - 25;
	}
	if($k < 0) $k = 0;
	$j = 26;
	$ord = strcmp($tri, 'architecture, forge, alchimie') ? $tri : 'ROUND(SQRT(alchimie + forge + architecture))';
	$tri = strcmp($tri, 'architecture, forge, alchimie') ? $tri : 'ROUND(SQRT(alchimie + forge + architecture) * 10) as craft';
	$requete = "SELECT ID, nom, ".sSQL($tri).", level, race, classe, cache_stat, cache_classe FROM perso WHERE statut = 'actif' AND ".$where." ORDER BY ".$ord." DESC, nom ASC LIMIT $inf, $j";
	//echo $requete;
	//echo 'inf : '.$inf.' j : '.$j.' k : '.$k.' sup : '.$sup.' '.$requete.'<br />';
	$req = $db->query($requete);
	$tri = !strcmp('architecture, forge, alchimie', $tri) ? 'craft' : $tri;
?>
		<input type="hidden" id="tri" value="<?php echo $tri; ?>" />
		<input type="hidden" id="i" value="<?php echo $i; ?>" />
		<input type="hidden" id="race" value="<?php echo $race; ?>" />
<table style="font-size : 1.1em; width : 100%;" cellspacing="0">
<tr class="table">
	<td>
		<div class="titre">
			<?php echo $tab_classement[$tri]['nom']; ?>
		</div>
		<table cellspacing="0" style="width : 100%;">
		<tr class="table">
			<td>
				#
			</td>
			<td>
				Nom
			</td>
			<?php
			if($tab_classement[$tri]['affiche'])
			{
			?>
			<td>
				<?php echo $tab_classement[$tri]['champ']; ?>
			</td>
			<?php
			}
			if($tab_classement[$tri]['affiche_niveau'])
			{
			?>
			<td>
				Niveau
			</td>
			<?php
			}
			?>
		</tr>

		<?php
			$z = 0;
			$y = $inf;
			while($row = $db->read_array($req))
			{
				if($z < 25)
				{
					if(!check_affiche_bonus($row['cache_stat'], $joueur, $row))
					{
						?>
						<tr class="table">
							<td>
								<?php echo ($y + 1); ?>
							</td>
							<td>
								###
							</td>
							<?php
							if($tab_classement[$tri]['affiche'])
							{
							?>
								<td>
									###
								</td>
							<?php
							}
							if($tab_classement[$tri]['affiche_niveau'])
							{
							?>
								<td>
									###
								</td>
							</tr>
							<?php
							}
					}
					else
					{
						$nom = $row['nom'];
						if((strtolower($row['nom']) != strtolower($_SESSION['nom'])) AND
							 ($row['cache_classe'] > 1 OR
								($row['cache_classe'] == 1 AND $row['race'] != $joueur->get_race())))
							$row['classe'] = 'combattant';
						if(strtolower($nom) == strtolower($_SESSION['nom']))
						{
							$style = 'background-color : #0aa74c;';
						}
						else $style = '';
						echo '
						<tr style="'.$style.'" class="table">
							<td>
								'.($y + 1).'
							</td>
							<td>
								<img src="image/personnage/'.$row['race'].'/'.$row['race'].'_'.$Tclasse[$row['classe']]['type'].'.png" alt="'.$Gtrad[$row['race']].' - '.$row['classe'].'" title="'.$Gtrad[$row['race']].' - '.$row['classe'].'"  style="width : 20px; height : 20px;vertical-align : middle;" /> '.$nom.'
							</td>';
						if($tab_classement[$tri]['affiche'])
						{
						?>
							<td>
								<?php echo $row[$tri]; ?>
							</td>
						<?php
						}
						if($tab_classement[$tri]['affiche_niveau'])
						{
						?>
							<td>
								<?php echo $row['level']; ?>
							</td>
						</tr>
						<?php
						}
					}
					$y++;
				}
				$z++;
			}
		
		?>
		</table>
	</td>
</tr>
</table>
		<?php
		if($inf > 0)
		{
		?>
			<a href="javascript:adresse('', '<?php echo $k; ?>', '')"><<- Précédent</a>
		<?php
		}
		else
		{
		?>
		<<- Précédent
		<?php
		}
		if($z > 25)
		{
		?>
			<a href="javascript:adresse('', '<?php echo ($sup - 1); ?>', '')">Suivant ->></a>
		<?php
		}
		?>
