<?php // -*- php -*-
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

function showMessage($msg, $titre = null)
{
  global $dontrefresh;
  $dontrefresh = true;
  echo '<fieldset><legend>'.$titre.'</legend>';
  echo '<div id="info_case">';
  echo $msg;
  echo '</div>';
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

function showParchemin($texte, $titre = 'Une page de parchemin',
											 $image = 'pagenoir.png', $x = 416, $y = 575)
{
  global $dontrefresh;
  $dontrefresh = true;
  echo '<fieldset><legend>'.$titre.'</legend>';
  echo '<div id="info_case" style="background: url(\'image/'.$image
		.'\'); width: '.($x - 30).'px; height: '.($y - 30).
		'px; padding: 15px;"><div class="parchemin_texte">'.$texte.'</div></div>';
}

function checkTpAbo(&$joueur)
{
  global $dontrefresh;
  $dontrefresh = true;
	$quetes = $joueur->get_liste_quete();
	$found = false;
	foreach ($quetes as $id => $q) {
		if ($q['id_quete'] == 86) {
			$found = true;
				echo '<fieldset><legend>Descente vers les profondeurs</legend>'.
					'<div id="info_case">';
				echo 'Comme vous l\'avait demandé le gobelin, vous descendez explorer'.
					' les profondeurs. Qui sait ce que vous allez y trouver ?<br/>';
				fin_quete($joueur, $id, $q['id_quete']);
				echo '</div>';
		}
	}
	if (!$found) {
		$quetes_fini = explode(';', $joueur->get_quete_fini());
		foreach ($quetes_fini as $qf) {
			if ($qf == 86) {
				$found = true;
				showMessage('Vous descendez à nouveau dans les profondeurs',
										'Descente vers les profondeurs');
			}
		}
	}
	if (!$found) {
		showMessage('Ce puis vous inquiète trop, vous ne voulez pas y entrer',
								'Descente vers les profondeurs');
		return;
	}
	$joueur->set_x(20);
	$joueur->set_y(304);
	$joueur->sauver();
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
