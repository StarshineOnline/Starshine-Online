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
	$tab_classement['star']['champ'] = 'Stars';
	$tab_classement['star']['affiche'] = false;
	$tab_classement['star']['affiche_niveau'] = false;
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
	$tab_classement['hp_max']['nom'] = 'TANKS';
	$tab_classement['hp_max']['champ'] = 'HP';
	$tab_classement['hp_max']['affiche'] = true;
	$tab_classement['hp_max']['affiche_niveau'] = false;
	$tab_classement['mp_max']['nom'] = 'BRAINS';
	$tab_classement['mp_max']['champ'] = 'MP';
	$tab_classement['mp_max']['affiche'] = true;
	$tab_classement['mp_max']['affiche_niveau'] = false;
?>
		<div id="table_classement">
<?php
	$joueur = new perso($_SESSION['ID']);
	if(!array_key_exists('tri', $_GET)) $tri = 'honneur';
	else
	{
		$tri = sSQL($_GET['tri']);
		if(!strcmp($tri, 'craft'))
			$tri = 'architecture, forge, alchimie';
	}
	$race = 'tous';
	$i = 0;
	$where = '1';
	$inf = $i;
	$sup = $inf + 26;
	$k = $inf - 25;
	if($k < 0) $k = 0;
	$j = 26;

	$tri = strcmp($tri, 'architecture, forge, alchimie') ? 	$tri = 'perso.'.sSQL($tri) : 'ROUND(SQRT((perso.alchimie + perso.forge + perso.architecture) * 10))';
	$requete = "SELECT groupe.id AS groupe_id, groupe.nom AS groupe_nom, SUM(".$tri.") as somme, COUNT(*) as tot FROM `groupe` LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE perso.statut = 'actif' GROUP BY groupe.id ORDER BY somme DESC LIMIT $inf, $j";
	//echo 'inf : '.$inf.' j : '.$j.' k : '.$k.' sup : '.$sup.' '.$requete.'<br />';
	$req = $db->query($requete);

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
			<td>
				<?php echo $tab_classement[$tri]['champ']; ?>
			</td>
			<td>
				Moyenne
			</td>
		</tr>

		<?php
			$z = 0;
			$y = $inf;
			while($row = $db->read_array($req))
			{
				if($z < 25)
				{
						$nom = $row['groupe_nom'];
						if($row['groupe_id'] == $joueur->get_groupe())
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
								'.$nom.'
							</td>';
						?>
							<td>
								<?php echo number_format($row['somme'], 0, ',', ' '); ?>
							</td>
							<td>
								<?php echo number_format(($row['somme'] / $row['tot']), 0, ',', ' '); ?>
							</td>
						</tr>
						<?php
					$y++;
				}
				$z++;
			}
		
		?>
		</table>
	</td>
</tr>
</table>