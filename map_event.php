<?php
if (file_exists('root.php'))
  include_once('root.php');

/**
* 
* Effectue les actions spéciales des cases
* 
*/
include_once(root.'inc/fp.php');
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);

$W_case = $_GET['poscase'];

if ($W_case != $joueur->get_poscase()) {
	security_block(URL_MANIPULATION, 'Event pas sur la même case');
}

function showImage($url, $titre = null)
{
	global $dontrefresh;
	$dontrefresh = true;
	echo '<fieldset><legend>'.$titre.'</legend>';
	echo '<div id="info_case">';
	echo '<img alt="'.$title.'" title="'.$title.'" src="'.$url.'" />';
	echo '</div>';
}

global $dontrefresh;
global $dontrefreshmap;

$S_requete = 'SELECT * from map_event WHERE x = '.$joueur->get_x().
' AND y = '.$joueur->get_y();
$S_query = $db->query($S_requete);
if ($db->num_rows > 0)
{
	$dontrefresh = false;
	$dontrefreshmap = false;
	$S_row = $db->read_array($S_query);
	if ($S_row['code'] != '')
	{
		$code = $S_row['code'];
		eval($code);
	}
	if ($S_row['sql'] != '')
	{
		foreach (explode(';', $S_row['sql']) as $sql)
			$db->query($sql);
	}
	if (!$dontrefreshmap)
		print_reload_area('deplacement.php?deplacement=centre', 'centre');
	if (!$dontrefresh)
		print_reload_area('informationcase.php?case='.$W_case, 'information');
}
else
{
	echo '<h6>Pas d\'event sur cette case</h6>';
}
