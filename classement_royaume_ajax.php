<?php
if (file_exists('root.php'))
  include_once('root.php');
  
if(array_key_exists('javascript', $_GET)) include_once(root.'inc/fp.php');
	//Tableau des classements
	$tab_classement = array();
	$tab_classement['victoire']['nom'] = 'Les plus victorieux';
	$tab_classement['victoire']['champ'] = 'Points de victoire';
	$tab_classement['victoire']['affiche'] = true;
	$tab_classement['victoire']['affiche_niveau'] = true;
	$tab_classement['cases']['nom'] = 'Les plus expentionnistes';
	$tab_classement['cases']['champ'] = 'Cases';
	$tab_classement['cases']['affiche'] = false;
	$tab_classement['cases']['affiche_niveau'] = false;
	$tab_classement['level']['nom'] = 'Les meilleurs soldats';
	$tab_classement['level']['champ'] = 'Niveau';
	$tab_classement['level']['affiche'] = true;
	$tab_classement['level']['affiche_niveau'] = false;
	$tab_classement['honneur']['nom'] = 'Les plus honorés';
	$tab_classement['honneur']['champ'] = 'Honneur';
	$tab_classement['honneur']['affiche'] = true;
	$tab_classement['honneur']['affiche_niveau'] = false;
	$tab_classement['frag']['nom'] = 'Les plus meurtiers';
	$tab_classement['frag']['champ'] = 'Kill';
	$tab_classement['frag']['affiche'] = true;
	$tab_classement['frag']['affiche_niveau'] = false;
	$tab_classement['mort']['nom'] = 'Ceux qui ont le plus grand cimetiére';
	$tab_classement['mort']['champ'] = 'Mort';
	$tab_classement['mort']['affiche'] = true;
	$tab_classement['mort']['affiche_niveau'] = false;
	$tab_classement['crime']['nom'] = 'Ceux où il y a le plus d\'insécurité';
	$tab_classement['crime']['champ'] = 'Crime';
	$tab_classement['crime']['affiche'] = true;
	$tab_classement['crime']['affiche_niveau'] = false;
?>
		<div id="table_classement">
<?php
  $pop = array();

  $requete = "SELECT COUNT(*) as tot, race FROM perso WHERE statut = 'actif' AND level > 0 GROUP BY race";
  $req = $db->query($requete);
  while($row = $db->read_assoc($req))
  {
  	$pop[$row['race']] = $row['tot']>0 ? $row['tot'] : 1;
  }

	$joueur = new perso($_SESSION['ID']);
  if(!array_key_exists('tri', $_GET)) $tri = 'victoire';
  else $tri = $_GET['tri'];
	switch($tri)
	{
  case 'victoire':
    $requete = "SELECT race, point_victoire_total as tot FROM `royaume` WHERE ID <> 0 ORDER BY star DESC";
    break;
  case 'cases':
    $requete = "SELECT COUNT(*) as tot, race FROM `map` LEFT JOIN royaume ON royaume.id = map.royaume WHERE royaume <> 0 AND x <= 190 AND y <= 190 GROUP BY royaume ORDER BY tot DESC";
    break;
  default:
    $requete = "SELECT SUM($tri) as tot, race FROM perso WHERE statut = 'actif' and level > 0 GROUP BY race ORDER BY tot DESC";
    break;
  }
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
			$z = 1;
			while($row = $db->read_array($req))
			{
				if($row['race'] == $joueur->get_race())
				{
					$style = 'background-color : #0aa74c;';
				}
				else $style = '';
				echo '
				<tr style="'.$style.'" class="table">
					<td>
						'.$z.'
					</td>
					<td>
						'.$Gtrad[$row['race']].'
					</td>';
				?>
					<td>
						<?php echo number_format($row['tot'], 0, ',', ' '); ?>
					</td>
					<td>
						<?php echo number_format(($row['tot'] / $pop[$row['race']]), 0, ',', ' '); ?>
					</td>
				</tr>
				<?php
				$z++;
			}

		?>
		</table>
	</td>
</tr>
</table>
</div>