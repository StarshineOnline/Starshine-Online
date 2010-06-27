<?php
if (file_exists('root.php'))
  include_once('root.php');

	include_once(root.'inc/fp.php');
	//Tableau des classements
  include_once(root.'inc/classement_ajax_tab.php');

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

	if(array_key_exists('iDisplayStart', $_GET)) $i = $_GET['iDisplayStart'];
	else $i = 0;

	if(array_key_exists('iDisplayLength', $_GET)) $j = $_GET['iDisplayLength'];
	else $j = 25;

	if($race == 'race')
	{
		$where = "race = '".sSQL($joueur->get_race())."'";
	}
	else
	{
		$where = '1';
	}

$inf = intval($i);

	$ord = strcmp($tri, 'architecture, forge, alchimie') ? $tri : 'ROUND(SQRT((alchimie + forge + architecture) * 10))';
	$tri = strcmp($tri, 'architecture, forge, alchimie') ? $tri : 'ROUND(SQRT((alchimie + forge + architecture) * 10)) as craft';
	$requete = "SELECT @rownum:=@rownum+1 rank, id, nom, ".sSQL($tri).", level, race, classe, cache_stat, cache_classe FROM perso, (SELECT @rownum:=$inf) r WHERE statut = 'actif' AND ".$where." ORDER BY ".$ord." DESC, nom ASC";
	//echo $requete;
	//echo 'inf : '.$inf.' j : '.$j.' k : '.$k.' sup : '.$sup.' '.$requete.'<br />';

if (array_key_exists('sSearch', $_GET) && $_GET['sSearch'] != '')
{
	$where .= " AND nom like '".sSQL($_GET['sSearch'])."%'";
	$requete = "select * from ($requete) s where nom like '".
		sSQL($_GET['sSearch'])."%' LIMIT $inf, $j";
}
else
{
	$requete .= " LIMIT $inf, $j";
}

// Comptage
$requetecount = "SELECT COUNT(1) FROM perso WHERE statut = 'actif'";
$reqcount = $db->query($requetecount);
while ($row = $db->read_array($reqcount)) {
  $nbresults = $row[0];
}
// Comptage
$requetecount = "SELECT COUNT(1) FROM perso WHERE statut = 'actif' AND $where";
$reqcount = $db->query($requetecount);
while ($row = $db->read_array($reqcount)) {
  $nbdispresults = $row[0];
}

$req = $db->query($requete);
$tri = !strcmp('ROUND(SQRT((alchimie + forge + architecture) * 10)) as craft', $tri) ? 'craft' : $tri;

$z = 0;
$y = $inf;
/*
$result_json = array();
$result_json['sEcho'] = $_REQUEST['sEcho'];
$result_json['iTotalRecords'] = $nbresults;
$result_json['iTotalDisplayRecords'] = 25;
$result_json['aaData'] = array();
*/

$endl = "\n";

$sOutput = '{';
$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', '.$endl;
$sOutput .= '"iTotalRecords": '.$nbresults.', '.$endl;
$sOutput .= '"iTotalDisplayRecords": '.$nbdispresults.', '.$endl;

$sOutput .= '"sColumns": [ "#", "Nom"';
if ($tab_classement[$tri]['champ'] != '') {
	$sOutput .= ', "'.$tab_classement[$tri]['champ'].'"';
}
if ($tab_classement[$tri]['affiche_niveau'] != '') {
	$sOutput .= ', "Niveau"';
}
$sOutput .= '],'.$endl;

$sOutput .= '"aaData": [ '.$endl;

function get_cell($cell)
{
	$rcell = str_replace('\\', '\\\\', $cell);
	return str_replace('"', '\"', $rcell);
}

while($row = $db->read_array($req))
{
	//$jsrow = array();
	//$jsrow[] = $y + 1;
	if ($row['nom'] == $joueur->get_nom())
	{
		$bef = '<div class="table itsme" style="background-color: rgb(10, 167, 76);">';
		$aft = '</div>';
	}
	else
	{
		$bef = '';
		$aft = '';
	}
  
  $sOutput .= '[ "'.get_cell($bef.$row['rank'].$aft).'"';
	if (!check_affiche_bonus($row['cache_stat'], $joueur, $row))
	{
    /* $jsrow[] = '###'; */
    $sOutput .= ', "###"';
    if ($tab_classement[$tri]['affiche'])
      $sOutput .= ', "###"'; /* $jsrow[] = '###'; */
    if($tab_classement[$tri]['affiche_niveau'])
      $sOutput .= ', "###"'; /* $jsrow[] = '###'; */
  }
	else
  {
    $nom = $row['nom'];
    if((strtolower($row['nom']) != strtolower($_SESSION['nom'])) AND
    ($row['cache_classe'] > 1 OR
    ($row['cache_classe'] == 1 AND $row['race'] != $joueur->get_race())))
      $row['classe'] = 'combattant';
    $perso = '<img src="image/personnage/'.$row['race'].'/'.$row['race'].
      '_'.$Tclasse[$row['classe']]['type'].'.png" alt="'.
      $Gtrad[$row['race']].' - '.$row['classe'].'" title="'.
      $Gtrad[$row['race']].' - '.$row['classe'].'" style="width: 20px; '.
      'height: 20px; vertical-align: middle;"/> '.$nom;
    $cell = $bef.$perso.$aft;
    //$cell = $nom;
    /* $jsrow[] = $perso; */
    $cell = str_replace('\\', '\\\\', $cell);
    $cell = str_replace('"', '\"', $cell);
    $sOutput .= ', "'.$cell.'"';
    if($tab_classement[$tri]['affiche'])
      $sOutput .= ', "'.get_cell($bef.$row[$tri].$aft).'"';
      /* $jsrow[] = $row[$tri]; */
    if ($tab_classement[$tri]['affiche_niveau'])
      $sOutput .= ', "'.get_cell($bef.$row['level'].$aft).'"';
  }
	$y++;
  $sOutput .= "],".$endl;
  
	/* $result_json['aaData'][] = $jsrow; */
}

//echo '<pre>'; var_dump($result_json); echo '</pre>'; exit(0);

//echo json_encode($result_json, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
//echo Zend_Json::encode($result_json);

$sOutput = substr_replace( $sOutput, "", -1 );
$sOutput .= '] }';

header('Content-type: text/plain');
echo $sOutput;
?>