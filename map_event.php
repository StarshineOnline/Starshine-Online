<?php // -*- php -*-
if (file_exists('root.php'))
  include_once('root.php');

/**
* 
* Effectue les actions spéciales des cases
* 
*/
include_once(root.'inc/fp.php');
include_once(root.'fonction/mini_jeux.php');
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

function giveRecette(&$joueur, $id_recette, $quiet = false)
{
  global $dontrefresh;
	$perso = new perso_recette();
	$perso_recette = $perso->recov($joueur->get_id(), $id_recette);
	if(!$perso_recette)
	{
		$perso_recette = new perso_recette();
		$perso_recette->id_perso = $joueur->get_id();
		$perso_recette->id_recette =$id_recette;
		$perso_recette->sauver();
		if ($quiet) {
			echo '&nbsp;<small>Vous avez acquis une nouvelle recette !</small>';
		} else {
			echo '<h6>Vous avez acquis une nouvelle recette !</h6>';
		}
		$dontrefresh = true;
	}
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

function checkOpenJailGate(&$joueur)
{
	global $db;
	$achiev = $joueur->get_compteur('abomination_mark');
	if ($achiev->get_compteur() > 0) {
		showMessage('Vous ressentez comme une peur émannant de la porte, qui s\'actionne',
								'Vous touchez la porte');
		$req_gate = "update map set decor = if(decor = 1691,1598,1691), ".
			"info = floor(decor/100) where x = 25 and y = 288";
		$req_player = "update perso set hp = 0, y = y + 1 where x = 25 and y = 288";
		$db->query($req_gate);
		$db->query($req_player);
		return true;
	}
	else {
		showMessage('La porte reste de marbre, semblant ne pas vous prendre au sérieux',
								'Vous touchez la porte');
		return false;
	}
}

class MapChanges
{
  private $x;
  private $y;
  private $field;
  private $from;
  private $to;
  function __construct($x, $y, $field, $from, $to) {
    $this->x = $x;
    $this->y = $y;
    $this->field = $field;
    $this->from = $from;
    $this->to = $to;
  }

  function apply() {
    global $db;
    $sql = "update map set $this->field = ".
      "if ( $this->field = $this->from, $this->to, $this->from ), ".
      "info = floor(decor/100) where x = $this->x and y = $this->y";
    $db->query($sql);
  }
}

function trafiquerCanalisation(&$joueur, $item, $x, $y, $decor, $changes)
{
	global $db;

  if ($decor != null)
  {
    $req = "select decor from map where x = $x and y = $y";
    $res = $db->query($req);
    $row = $db->read_array($res);
    if ($row['decor'] != $decor)
    {
      showMessage('La canalisation est déjà réparée !', 'Trafiquer');
      return;
    }
  }
  if ($item == null OR $joueur->recherche_objet($item)) // check inventaire
  {
    foreach ($changes as $c)
      $c->apply();
    showMessage('Vous réparez la canalisation ...', 'Trafiquer');
    if ($item != null)
    {
      $joueur->supprime_objet($item, 1);
      $joueur->sauver();
    }
  }
  else
    showMessage('Vous n\'avez pas de matériel !', 'Trafiquer');
}

function checkChacalGate(&$joueur, $changes)
{
	$armor = decompose_objet($joueur->get_inventaire_partie('torse'));
	if ($armor['id_objet'] != 127)
	{
    showMessage('La porte vous est interdite', 'porte');
	}
	else
		foreach ($changes as $c)
			$c->apply();
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
