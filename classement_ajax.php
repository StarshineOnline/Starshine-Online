<?php
/// @deprecated
if (file_exists('root.php'))
	include_once('root.php');

if(array_key_exists('ajax', $_GET))
	include_once(root.'inc/fp.php');
//Tableau des classements
include_once(root.'inc/classement_ajax_tab.php');

// Récupération des paramètres utilisateurs
$typeClassement = 'honneur';
if( isset($_GET['classement']) )
	$typeClassement = $_GET['classement'];
$raceClassement = 'tous';
if( isset($_GET['race']) )
	$raceClassement = $_GET['race'];
$sSearch = '';
if( isset($_GET['sSearch']) )
	$sSearch = $_GET['sSearch'];
$iDisplayLength = 10;
if( isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength']) )
	$iDisplayLength = (int) $_GET['iDisplayLength'];

// Définition d'autres paramètres
$aColumns = array('rank', 'nom');
$aColumnsDisplay = array('#', 'Nom');
if($tab_classement[$typeClassement]['affiche']){
	$aColumns[] = $typeClassement;
	$aColumnsDisplay[] = $tab_classement[$typeClassement]['champ'];
}
if($tab_classement[$typeClassement]['affiche_niveau']){
	$aColumns[] = 'level';
	$aColumnsDisplay[] = 'Niveau';
}

$aColumnDefs = array();
if( ($idCol = array_search('rank', $aColumns)) !== false )
{
	// Les colonnes qui ne sont pas 'sortable'
	$columnDefs[] = array('bSortable' => false, 'aTargets' => array($idCol));
}
if( ($idCol = array_search('nom', $aColumns)) !== false )
{
	// Les colonnes avec une largeur imposée
	$columnDefs[] = array('sWidth' => '230px', 'aTargets' => array($idCol));
}
$notSearchableColumns = array();
foreach($aColumns as $k => $c){
	if($c != 'nom')
		$notSearchableColumns[] = $k;
}
if( !empty($notSearchableColumns) )
{
	// Les colonnes qui ne sont pas 'searchable'
	$columnDefs[] = array('bSearchable' => false, 'aTargets' => $notSearchableColumns);
}

$target = 'classement_ajax_json.php'.'?'.'race='.urlencode($raceClassement).'&'.'classement='.urlencode($typeClassement);

?>
<div id="table_classement" class="display">
	<input type="hidden" id="classement" value="<?php echo htmlspecialchars($typeClassement); ?>" />
	<input type="hidden" id="race" value="<?php echo htmlspecialchars($raceClassement); ?>" />
	<table style="font-size : 1.1em; width : 100%;" cellspacing="0">
		<tr class="table">
			<td>
				<div class="titre">
					<?php echo htmlspecialchars($tab_classement[$typeClassement]['nom']); ?>
				</div>
				<table cellspacing="0" style="width : 100%;"  id="classement_table">
				<thead>
					<tr class="table">
						<?php foreach($aColumnsDisplay as $display): ?>
							<th><?php echo htmlspecialchars($display); ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
				</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
var oTable = $('#classement_table').dataTable({
	"sAjaxSource": <?php echo json_encode($target); ?>,
	"bAutoWidth": false,
	"bProcessing": true,
	"bServerSide": true,
	"bStateSave": false,
	"sPaginationType": "input",
	"iDisplayLength": <?php echo $iDisplayLength; ?>,
	"oSearch": {"sSearch": <?php echo json_encode($sSearch); ?>},
	"bSort": false,
	"aaSorting": [[ 2, "desc" ], [ 1, "asc" ]],
	"aoColumnDefs": <?php echo json_encode($columnDefs); ?>,
	"oLanguage": {
		"sInfo": "_START_ à _END_ sur _TOTAL_",
		"sInfoEmpty": "Pas de résultat",
		"sInfoFiltered": " (nombre total : _MAX_)",
		"sLengthMenu": "Affiche _MENU_ éléments",
		"sProcessing": "Calcul en cours...",
		"sSearch": "Filtrer sur :",
		"sZeroRecords": "Pas de résultat",
		"oPaginate": {
			"sFirst": "Début", "sLast": "Fin", "sNext": "Suivant", "sPrevious": "Précédent"
		}
	},
	"fnServerData": function ( sUrl, aoData, fnCallback, oSettings ) {
		oSettings.jqXHR = $.ajax( {
			"url":  sUrl,
			"data": aoData,
			"dataType": "json",
			"type": oSettings.sServerMethod,
			"cache": false,
			"success": function (data, textStatus, jqXHR) {
				$(oSettings.oInstance).trigger('xhr', [oSettings, data]);
				fnCallback( data );
				
				// Gestion du lien pour afficher 'mon' perso dans le classement
				var monPersoPageNumber = parseInt( data.extra['monPersoPageNumber'] );
				$('#classement_table_mon_perso').data('pagingAction', monPersoPageNumber);
				if(monPersoPageNumber >= 0){
					$('#classement_table_mon_perso').attr('href', '#jquery-datatables-paging-action_'+monPersoPageNumber);
					$('#classement_table_mon_perso').data('isDisabledPagingAction', false);
				}
				else{
					$('#classement_table_mon_perso').removeAttr('href');
					$('#classement_table_mon_perso').data('isDisabledPagingAction', true);
				}
			},
			"error": function (jqXHR, textStatus, errorThrown) {
				if ( textStatus == "parsererror" ) {
					oSettings.oApi._fnLog( oSettings, 0, 'JSON data from server could not be parsed. This is caused by a JSON formatting error. ( '+errorThrown+' )' );
				}
				else {
					oSettings.oApi._fnLog( oSettings, 1, 'Error from server (TextStatus: "'+textStatus+'" - ErrorThrown: "'+errorThrown+'").' );
				}
				oSettings.oApi._fnProcessingDisplay(oSettings, false);
			}
		} );
	}
});
</script>
