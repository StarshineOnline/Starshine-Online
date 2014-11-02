<?php
/// @deprecated

/*
 * Pour les informations envoyées au serveur par jQuery DataTables
 * et les informations à renvoyer à jQuery DataTables par le serveur
 * voir l'adresse : http://datatables.net/usage/server-side
 */

if (file_exists('root.php')) {
	include_once('root.php');
}

include_once(root.'inc/fp.php');
//Tableau des classements
include_once(root.'inc/classement_ajax_tab.php');

$perso = new perso($_SESSION['ID']);

/*************************************************************************/
/************** Récupération des paramètres passés au script *************/
/*************************************************************************/

$typeClassement = 'honneur';
if( isset($_GET['classement']) )
	$typeClassement = $_GET['classement'];
$raceClassement = 'tous';
if( isset($_GET['race']) )
	$raceClassement = $_GET['race'];

$sEcho = '';
if( isset($_GET['sEcho']) )
	$sEcho = $_GET['sEcho'];
$iDisplayStart = 0;
if( isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart']) )
	$iDisplayStart = intval($_GET['iDisplayStart']);
$iDisplayLength = 25;
if( isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength']) )
	$iDisplayLength = intval($_GET['iDisplayLength']);
$iSortingCols = 0;
if( isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols']) )
	$iSortingCols = intval($_GET['iSortingCols']);
$orders = array();
for($i=0; $i<$iSortingCols; $i++)
{
	if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		$orders[intval($_GET['iSortCol_'.$i])] = ($_GET['sSortDir_'.$i] === 'asc' ? 'ASC' : 'DESC');
} 
$sSearch = '';
if( isset($_GET['sSearch']) )
	$sSearch = $_GET['sSearch'];
$iColumns = 0;
if( isset($_GET['iColumns']) && is_numeric($_GET['iColumns']) )
	$iColumns = intval($_GET['iColumns']);
$searchColumns = array();
for($i=0; $i<$iColumns; $i++)
{
	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
		$searchColumns[] = $i;
}
$searchStringByColumns = array();
foreach($searchColumns as $idCol){
	if($_GET['sSearch_'.$idCol] != '')
		$searchStringByColumns[$idCol] = $_GET['sSearch_'.$idCol];
}

// bRegex et bRegex_(int) non utilisés
// ...

/*************************************************************************/
/******************** Définition d'autres paramètres *********************/
/*************************************************************************/

// Array of database columns which should be read and sent back to DataTables.
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

// Indexed column (used for fast and accurate table cardinality)
$sIndexColumn = "id";

// DB table to use
$sTable = "perso";

// Array of database columns, pour faire des vérifications sur les persos pendant la création de l'affichage
$aColumnsTest = array('id', 'cache_classe', 'cache_stat', 'cache_niveau', 'race', 'classe');

/*************************************************************************/
/********************** Création de la requête SQL ***********************/
/*************************************************************************/

// Paging
$sLimit = "";
if ( $iDisplayLength >= 0 )
{
	$sLimit = "LIMIT $iDisplayStart, $iDisplayLength";
}

// Ordering
$sOrder = "";
if(!empty($orders))
{
	$sOrder .= 'ORDER BY'.' ';
	$first = true;
	foreach($orders as $idCol => $order){
		if($first) $first = false;
		else $sOrder .= ', ';
		$sOrder .= $aColumns[$idCol]." $order";
	}
}

// Filtering
$sWhereStart = 'WHERE 1'.' ';
$sWhere = $sWhereStart;
if( !empty($searchColumns) && $sSearch != '' )
{
	$sWhere .= 'AND (';
	$first = true;
	foreach($searchColumns as $idCol){
		if($first) $first = false;
		else $sWhere .= ' OR ';
		$sWhere .= $aColumns[$idCol]." LIKE '".$db->escape($sSearch)."%'";
	}
	$sWhere .= ')';
}
// Individual column filtering
foreach($searchStringByColumns as $idCol => $sSearchString){
	$sWhere .= " AND ";
	$sWhere .= $aColumns[$idCol]." LIKE '".$db->escape($sSearchString)."%'";
}
// Les données sont-elles effectivement filtrées ?
$isDataFiltered = false;
if( $sWhere != $sWhereStart)
	$isDataFiltered = true;

// Filtre pour le calcul du rang et les persos à prendre en compte dans le classement
$sWhereRank = "WHERE $sTable.statut = 'actif' AND $sTable.level > 0";
if( $raceClassement != 'tous' )
{
	$raceSQL = $raceClassement;
	if( $raceSQL == 'race' )
		$raceSQL = $perso->get_race();
	$sWhereRank .= " AND $sTable.race = '".$db->escape($raceSQL)."'";
}

// Toutes les colonnes à récupérer
$aAllColumns = array_unique(array_merge($aColumns, $aColumnsTest));
// Toutes les colonnes non spéciales
$aNormalColumns = array_diff($aAllColumns, array($sIndexColumn, 'craft', 'rank', 'achiev'));
// La chaîne de caractères MySQL de toutes les colonnes non spéciales
$sChampsNormaux = implode(", ", $aNormalColumns);
if($sChampsNormaux != '') $sChampsNormaux = ", ".$sChampsNormaux;
// Sous-requête principale
$sTableTemp = "(
	SELECT 	$sIndexColumn $sChampsNormaux"
			.($typeClassement=='craft' ? ", ROUND(SQRT((alchimie + forge + architecture) * 10)) as craft" : '')."
			, @curRank := @curRank + 1 AS rank
	FROM $sTable, (SELECT @curRank := 0) r
	$sWhereRank
	ORDER BY ".$db->escape($typeClassement)." DESC, nom ASC
) AS tab";
if($typeClassement == 'achiev')
{
	$sTableTemp = "(
		SELECT 	$sIndexColumn $sChampsNormaux"
				.", achiev"."
				, @curRank := @curRank + 1 AS rank
		FROM
		(SELECT @curRank := 0) r
		, (
			SELECT	$sTable.$sIndexColumn ".str_replace(', ', ", $sTable.", $sChampsNormaux)."
					, COUNT(a.id_perso) achiev
			FROM	$sTable
					LEFT JOIN achievement a ON a.id_perso = $sTable.id
			$sWhereRank
			GROUP BY $sTable.id
		) AS tabGroup
		ORDER BY ".$db->escape($typeClassement)." DESC, nom ASC
	) AS tab";
}
$sTable = $sTableTemp;

// Si les données ne sont pas filtrées par l'utilisateur, on utilisera toutes les données pour l'affichage
if( !$isDataFiltered )
{
	// Requête pour l'affichage du classement
	$sQuery = "
		SELECT	SQL_CALC_FOUND_ROWS
				".implode(", ", $aAllColumns)."
		FROM $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	$rResult = $db->query($sQuery);

	// La requête SELECT FOUND_ROWS() doit s'effectuer juste après celle contenant SQL_CALC_FOUND_ROWS
	// Data set length after filtering
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $db->query($sQuery);
	$aResultFilterTotal = $db->read_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];

	// Position du $perso dans le classement
	$sQuery = "
		SELECT position
		FROM
		(
			SELECT id, @curRank := @curRank + 1 AS position
			FROM $sTable, (SELECT @curRank := 0) r
			$sWhere
			$sOrder
		) as tab
		WHERE id = ".$perso->get_id()."
	";
	$rPositionMonPerso = $db->query($sQuery);
	$aPositionMonPerso = $db->read_array($rPositionMonPerso);
	$iPositionMonPerso = $aPositionMonPerso[0];
	$iMonPersoPageNumber = 0;
	if($iDisplayLength > 0)
		$iMonPersoPageNumber = floor( ($iPositionMonPerso-1) / $iDisplayLength);

	// Récupération des persos pour l'affichage du classement
	$aResults = array();
	$persoResults = array();
	while($row = $db->read_assoc($rResult)){
		$aResults[] = $row;
		$persoResults[] = new perso($row);
	}
}
// Si les données sont filtrées par l'utilisateur, on utilisera des données restreintes pour l'affichage
// pour éviter que l'utilisateur ne déduise de son filtre le nom des persos du classement qui ont cachés leurs stats ou leur niveau
else
{
	// Requête pour l'affichage du classement, avant restriction
	$sQuery = "
		SELECT ".implode(", ", $aAllColumns)."
		FROM $sTable
		$sWhere
		$sOrder
	";
	$rResult = $db->query($sQuery);
	
	// Récupération des persos et restriction des données avant affichage
	// En même temps, définition de la position du $perso dans le classement restreint
	$aResultsBeforePaging = array();
	$persosBeforePaging = array();
	$count = 0;
	while($row = $db->read_assoc($rResult)){
		$p = new perso($row);
		if( (!$p->est_cache_stat($perso) || $typeClassement == 'exp') && (!$p->est_cache_niveau($perso) || $typeClassement != 'exp') ){
			$aResultsBeforePaging[] = $row;
			$persosBeforePaging[] = $p;
			
			if($p->get_id() == $perso->get_id())
				$iPositionMonPerso = $count+1;
			
			$count++;
		}
	}
	
	// Data set length after filtering
	$iFilteredTotal = count($persosBeforePaging);
	
	// Application du Paging
	$aResults = $aResultsBeforePaging;
	$persoResults = $persosBeforePaging;
	if($iDisplayLength >= 0){
		$aResults = array_slice($aResultsBeforePaging, $iDisplayStart, $iDisplayLength);
		$persoResults = array_slice($persosBeforePaging, $iDisplayStart, $iDisplayLength);
	}
	
	// Position du $perso dans le classement restreint
	$iMonPersoPageNumber = 0;
	if($iDisplayLength > 0)
		$iMonPersoPageNumber = floor( ($iPositionMonPerso-1) / $iDisplayLength);
}

// Total data set length
$sQuery = "
	SELECT COUNT(`$sIndexColumn`)
	FROM   $sTable
";
$rResultTotal = $db->query($sQuery);
$aResultTotal = $db->read_array($rResultTotal);
$iTotal = $aResultTotal[0];

/*************************************************************************/
/******************************** Output *********************************/
/*************************************************************************/

$output = array(
	"sEcho" => $sEcho
	, "iTotalRecords" => $iTotal
	, "iTotalDisplayRecords" => $iFilteredTotal
	, "aaData" => array()
	, "extra" => array('monPersoPageNumber' => $iMonPersoPageNumber)
);

foreach($aResults as $k => $aRow)
{
	$p = $persoResults[$k];
	
	$cacheStat = false;
	if($p->est_cache_stat($perso))
		$cacheStat = true;
	$cacheNiveau = false;
	if($p->est_cache_niveau($perso))
		$cacheNiveau = true;
	
	$row = array();

	// Ajout d'une classe CSS pour la ligne du tableau qui concerne le personnage du joueur
	if($p->get_id() == $perso->get_id()){
		$row['DT_RowClass'] = 'moi';
	}
	
	// Ajout des valeurs au tableau
	for( $i=0; $i<count($aColumns); $i++ )
	{
		// General Output
		$data = $aRow[ $aColumns[$i] ];
		
		// Particular Output
		if( $aColumns[$i] == 'rank' ){
			$data = $aRow[ $aColumns[$i] ];
		}
		elseif( $aColumns[$i] == 'level' || $aColumns[$i] == 'exp' ){
			if($cacheNiveau){
				$data = '###';
			}
		}
		elseif( $typeClassement == 'exp' && $cacheNiveau ){
			$data = '###';
		}
		else{
			if( $cacheStat && $typeClassement != 'exp' ){
				$data = '###';
			}
			elseif( $aColumns[$i] == 'nom' ){
				$urlImage = $p->get_image('', 'high', $perso);
				$classe = $p->get_classe($perso);
				$data = '<img src="'.$urlImage.'" alt="'.$Gtrad[$aRow['race']].' - '.$classe.'" title="'.$Gtrad[$aRow['race']].' - '.$classe.'" /> '.$aRow['nom'];
			}
		}
		
		$row[] = $data;
	}
	
	$output['aaData'][] = $row;
}


header('Content-Type: application/json');

$jsonOutput = json_encode($output);
if($jsonOutput === false){
	echo 'PHP erreur JSON : '.json_last_error();
}
elseif($jsonOutput === 'null' || $jsonOutput == ''){
	echo 'vide';
}
else
	echo $jsonOutput;
