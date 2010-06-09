<?php
if (file_exists('root.php'))
  include_once('root.php');

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
	$tab_classement['reputation']['nom'] = 'Les plus réputés';
	$tab_classement['reputation']['champ'] = 'Réputation';
	$tab_classement['reputation']['affiche'] = true;
	$tab_classement['reputation']['affiche_niveau'] = false;
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
	$tab_classement['dressage']['nom'] = 'Dresseur';
	$tab_classement['dressage']['champ'] = 'Dressage';
	$tab_classement['dressage']['affiche'] = true;
	$tab_classement['dressage']['affiche_niveau'] = false;


if(!array_key_exists('tri', $_GET)) $tri = 'honneur';
else $tri = $_GET['tri'];
if (array_key_exists('race', $_GET)) {
  $target = 'classement_ajax_json.php?race='.$_GET['race'].'&tri='.$tri;
} else {
  $target = 'classement_ajax_json.php?tri='.$tri;
}
?>
		<div id="table_classement" class="display">
<?php
	$tri = !strcmp('ROUND(SQRT((alchimie + forge + architecture) * 10)) as craft', $tri) ? 'craft' : $tri;
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
		<table cellspacing="0" style="width : 100%;"  id="classement_table">
		<thead>
		<tr class="table">
			<th>
				#
			</th>
			<th>
				Nom
			</th>
			<?php
			if($tab_classement[$tri]['affiche'])
			{
			?>
			<th>
				<?php echo $tab_classement[$tri]['champ']; ?>
			</th>
			<?php
			}
			if($tab_classement[$tri]['affiche_niveau'])
			{
			?>
			<th>
				Niveau
			</th>
			<?php
			}
			?>
		</tr>
		</thead>
		<tbody>
			</tbody>
		</table>
	</td>
</tr>
</table>
<script type="text/javascript">
		$('#classement_table').dataTable({
			"sAjaxSource": "<?php echo $target ?>",		
				"bProcessing": true,
				"bServerSide": true,
				"sPaginationType": "full_numbers",
				"oLanguage": {
				  "sInfo": "_START_ à _END_ sur _TOTAL_",
					"sInfoEmpty": "Pas de résultats",
					"sInfoFiltered": " (nombre total : _MAX_)",
					"sLengthMenu": "Affiche _MENU_ éléments",
					"sProcessing": "Calcul en cours ...",
					"sSearch": "Filtrer sur :",
					"sZeroRecords": "Pas de résultat",
					"oPaginate": {
						"sFirst": "Début", "sLast": "Fin", "sNext": "Suivant", "sPrevious": "Précédent"
					}
	  		}
		});
</script>
