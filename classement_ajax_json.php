<?php
if (file_exists('root.php'))
  include_once('root.php');

	include_once(root.'inc/fp.php');
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

$sOutput .= '] }';

header('Content-type: text/plain');
echo $sOutput;
?>