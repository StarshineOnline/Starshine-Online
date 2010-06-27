<?php
if (file_exists('root.php'))
  include_once('root.php');

	if(array_key_exists('javascript', $_GET)) include_once(root.'inc/fp.php');
	//Tableau des classements
  include_once(root.'inc/classement_ajax_tab.php');

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
        "bStateSave": true,
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
