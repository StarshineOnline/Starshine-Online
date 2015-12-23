<?php // -*- mode: php; tab-width: 2 -*-

if (file_exists('root.php'))
  include_once('root.php');

/**
* 
* Effectue les actions spéciales des cases
* 
*/
include_once(root.'inc/fp.php');
include_once(root.'fonction/mini_jeux.php');


$interf_princ = $G_interf->creer_jeu();
//Récupération des informations du personnage
$joueur = new perso($_SESSION['ID']);

$W_case = $_GET['poscase'];

if ($W_case != $joueur->get_poscase()) {
	security_block(URL_MANIPULATION, 'Event pas sur la même case');
}

function showMessage($msg, $titre = null)
{
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
  /*echo '<fieldset><legend>'.$titre.'</legend>';
  echo '<div id="info_case">';
  echo $msg;
  echo '</div>';*/
  $cont = $interf_princ->set_droite( new interf_cont() );
  if( $titre )
  	$cont->add( new interf_bal_smpl('h4', $titre) );
  $cont->add( new interf_bal_smpl('div', $msg, 'info_case') );
}

function showImage($url, $titre = null)
{
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
  /*echo '<fieldset><legend>'.$titre.'</legend>';
  echo '<div id="info_case">';
  echo '<img alt="'.$title.'" title="'.$title.'" src="'.$url.'" />';
  echo '</div>';*/
  $cont = $interf_princ->set_droite( new interf_cont() );
  if( $titre )
  	$cont->add( new interf_bal_smpl('h4', $titre) );
  $div = $cont->add( new interf_bal_cont('div', $msg, 'info_case') );
  $img = $div->add( new interf_img($url, $title) );
  $img->set_attribut('title', $title);
}

function showParchemin($texte, $titre = 'Une page de parchemin',
											 $image = 'pagenoir.png', $x = 416, $y = 575)
{
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
  /*echo '<fieldset><legend>'.$titre.'</legend>';
  echo '<div id="info_case" style="background: url(\'image/'.$image
		.'\'); width: '.($x - 30).'px; height: '.($y - 30).
		'px; padding: 15px;"><div class="parchemin_texte">'.$texte.'</div></div>';*/
  $cont = $interf_princ->set_droite( new interf_cont() );
  if( $titre )
  	$cont->add( new interf_bal_smpl('h4', $titre) );
  $div = $cont->add( new interf_bal_cont('div', 'info_case') );
  $div->set_attribut('style', 'background: url(\'image/'.$image.'\'); width: '.($x - 30).'px; height: '.($y - 30).'px; padding: 15px;');
  $div->add( new interf_bal_smpl('div', $texte, null, 'parchemin_texte') );
}

function giveRecette(&$joueur, $id_recette, $quiet = false)
{
  global $dontrefresh, $interf_princ;
	$perso = new perso_recette();
	$perso_recette = $perso->recov($joueur->get_id(), $id_recette);
	if(!$perso_recette)
	{
		$perso_recette = new perso_recette();
		$perso_recette->id_perso = $joueur->get_id();
		$perso_recette->id_recette =$id_recette;
		$perso_recette->sauver();
		if ($quiet) {
			$interf_princ->set_droite( new interf_bal_smpl('small', 'Vous avez acquis une nouvelle recette !') );
			//echo '&nbsp;<small>Vous avez acquis une nouvelle recette !</small>';
		} else {
			$interf_princ->set_droite( new interf_alerte(interf_alerte::msg_succes, false, false, 'Vous avez acquis une nouvelle recette !') );
			//echo '<h6>Vous avez acquis une nouvelle recette !</h6>';
		}
		$dontrefresh = true;
	}
}

function checkTpCacheChache(&$joueur)
{
  global $dontrefresh;
  $dontrefresh = true;
	$quetes = $joueur->get_quete_fini();
	$found = false;
  // TODO ...
}

/// @todo remplacer par texte avec balises
function checkTpAbo(&$perso)
{
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
	$quete = quete_perso::create(array('id_perso', 'id_etape'), array($perso->get_id(), 123));
	if( $quete )
	{
	  $cont = $interf_princ->set_droite( new interf_cont() );
	  $cont->add( new interf_bal_smpl('h4', 'Descente vers les profondeurs') );
	  $cont->add( new interf_bal_smpl('div', 'Comme vous l\'avait demandé le gobelin, vous descendez explorer les profondeurs. Qui sait ce que vous allez y trouver ?', 'info_case') );
		$quete->get_etape()->fin($perso);
	}
	else
	{
	
		$quetes_fini = explode(';', $perso->get_quete_fini());
		$found = false;
		foreach ($quetes_fini as $qf)
		{
			if ($qf == 86)
			{
				$found = true;
				showMessage('Vous descendez à nouveau dans les profondeurs', 'Descente vers les profondeurs');
			}
		}
		if (!$found)
		{
			showMessage('Ce puis vous inquiète trop, vous ne voulez pas y entrer', 'Descente vers les profondeurs');
			return;
		}
	}
	$perso->set_x(20);
	$perso->set_y(304);
	$perso->sauver();
}

function checkTpValidQuest(&$joueur, $queteId, $x, $y, $allowNotQuest = false)
{
  global $db;
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
	$quetes = $joueur->get_liste_quete();
	$found = false;
	foreach ($quetes as $id => $q) {
		if ($q->get_id() == $queteId) {
			$found = true;
      $qd = $db->query_get_object("select * from quete where id = $queteId");
      /*echo '<fieldset><legend>'.$qd->nom.'</legend><div id="info_case">';
      echo 'Comme on vous l\'avait demandé, vous empruntez le passage. '.
        'Qui sait ce que vous allez y trouver ?<br/>';*/
		  $cont = $interf_princ->set_droite( new interf_cont() );
		  $cont->add( new interf_bal_smpl('h4', '$qd->nom') );
		  $cont->add( new interf_bal_smpl('div', 'Comme on vous l\'avait demandé, vous empruntez le passage. Qui sait ce que vous allez y trouver ?', 'info_case') );
     $q->get_etape()->fin($joueur);
      echo '</div>';
		}
	}
	if (!$found) {
		$quetes_fini = explode(';', $joueur->get_quete_fini());
		foreach ($quetes_fini as $qf) {
			if ($qf == $queteId) {
				$found = true;
				showMessage('Vous empruntez à nouveau le passage',
										'Passage inquiétant');
			}
		}
	}
	if (!$found && !$allowNotQuest) {
		showMessage('Ce passage vous inquiète trop, vous ne voulez pas y entrer',
								'Passage inquiétant');
		return;
	}
	$joueur->set_x($x);
	$joueur->set_y($y);
	$joueur->sauver();
}

function usePute(&$joueur, $stars, $honneur, $effet, $virtuose)
{
  global $dontrefresh, $interf_princ;
  $dontrefresh = true;
  if ($joueur->get_star() < $star || $joueur->get_honneur() < $honneur) {
    showMessage('<h5>Vous n\'avez pas les moyens !</h5>', 'Prostitution');
    return;
  }
  if ($joueur->get_pa() < 12) {
    showMessage('<h5>Vous n\'avez pas assez de PA !</h5>', 'Prostitution');
    return;
  }
  $joueur->add_star($stars * -1);
  $joueur->add_honneur($honneur * -1);
  $joueur->add_hp($effet);
  $joueur->add_mp($effet);
  $joueur->add_pa(-12);
  $joueur->sauver();
  
  $spe = array('virtuose_sexe' => array('type' => 'virtuose_sexe',
                                        'nom' => 'Virtuose du sexe',
                                        'description' => 'La joie décuple vos facultés d´esquive',
                                        'effet' => 10000,
                                        'effet2' => 0,
                                        'duree' => 86400 * 3));

  echo '<fieldset><legend>Prostitution</legend><div id="info_case">';
  if ($virtuose)
    $res = pute_effets($joueur, $honneur, array_keys($spe), $spe);
  else
    $res = pute_effets($joueur, $honneur);
  echo '<h6>'.$res.'</h6>Vous gagnez '.$effet.' PV/PM</div>';
  $cont = $interf_princ->set_droite( new interf_cont() );
  $cont->add( new interf_bal_smpl('h4', 'Prostitution') );
	$cont->add( new interf_bal_smpl('div', $res, 'info_case') );
  $cont->add( new interf_alerte(interf_alerte::msg_succes, false, false, 'Vous gagnez '.$effet.' PV/PM') );
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

include_once(root.'class/map_changes.php');

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
		$sch = serialize($changes);
		$code = 'include_once(root."class/map_changes.php"); $cs = unserialize(\''.
			$sch.'\'); foreach ($cs as $c)  $c->apply();';
		$scode = sSQL($code);
		$date = 'DATE_ADD(NOW(), interval 3 day)';
		$res = $db->query("insert into calendrier (date, eval) values ($date, '$scode')");
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

function checkChacalTP(&$joueur, $x, $y)
{
	$armor = decompose_objet($joueur->get_inventaire_partie('torse'));
	if ($armor['id_objet'] != 127)
	{
    showMessage('La porte vous est interdite', 'porte');
	}
	else
	{
    showMessage('On vous laisse entrer', 'porte');
    $joueur->set_x($x);
    $joueur->set_y($y);
    $joueur->sauver();
	}
}

function checkDaemonTP(&$joueur, $x, $y)
{
	if ($joueur->is_buff('debuff_forme_demon'))
	{
    showMessage('Ce passage n\'est pas pour les mortels', 'portail');
	}
	else
	{
    showMessage('Le portail s\'active, et vous êtes transporté', 'portail');
    $joueur->set_x($x);
    $joueur->set_y($y);
    $joueur->sauver();
	}
}

function checkTeleportHydraulique(&$joueur)
{
  global $db;
  $sql = 'select * from map where (x = 27 and y = 407 and type = 158) or '.
    '(x = 18 and y = 409 and type = 158) or (x = 6 and y = 426 and type = 158)';
  $res = $db->query($sql);
  if ($res && $db->num_rows($res) == 3) {
    $joueur->set_x(14);
    $joueur->set_y(401);
    $joueur->sauver();
		showMessage('Le calligramme disparaît et une brise légère vous transporte instantanément autre-part.');
  }
	else
		showMessage('Le calligramme luit faiblement sous vos pieds mais rien ne se passe...');
}
function updatetuto($etape, &$joueur)
{
	global $db;
	$sql ='update perso SET tuto ='.++$etape.' WHERE id = '.$joueur->get_id();
	$db->query($sql);
}
function tpLyly(&$joueur)
{
  switch ($joueur->get_x()) {
    case 39: // sortie
      showMessage('Vous traversez le conduit, et retournez au couloir');
      $joueur->set_x(44);
      $joueur->set_y(327);
      $joueur->sauver();
      break;
    case 44: // entrée
      $qf = explode(';', $joueur->get_quete_fini());
      if (array_search(90, $qf)) {
        showMessage('Vous tirez le troisième barreau, et la grille s\'efface dans le sol, libérant le passage que vous empruntez aussitôt');
      $joueur->set_x(39);
      $joueur->set_y(326);
      $joueur->sauver();
      }
      else {
        showMessage('La grille a l\'air d\'être trop bien scellée pour être ouverte');
      }
      break;
    default:
      security_block(URL_MANIPULATION, 'Pas sur le TP');
  }
}

/**
 * Pour gérer les ponts "temporaires", variante à coût fixe
 * @param @joueur le joueur qui passe le pont
 * @param x l'autre coté du pont (x)
 * @param y l'autre coté du pont (y)
 * @param cout le cout en PA pour passer
 * @param jours le pont est praticable tous les x jours
 */
function checkPontTemporaireFixeTP(&$joueur, $x, $y, $cout = 10, $jours = 2)
{
	if ($joueur->get_pa() < $cout) {
		showMessage('Vous ne vous sentez pas la force de passer ce pont', 'Pas assez de PA');
		return;
	}
	if (((int)date('d')) % max(2, ((int)$jours) % 31) && $jours != 1)
	{
    showMessage('Le tronc de l\'arbre est trop instable pour servir à franchir la rivière, rééssayez demain',
								'L\'arbre ne peut servir de pont.');
	}
	else
	{
    showMessage('Vous réussissez à traverser la rivière en jouant l\'équilibriste sur les branche de l\'arbre.<br/>Ça vous a coûté '
								.$cout.' PA', 'Passage précaire');
    $joueur->set_x($x);
    $joueur->set_y($y);
		$joueur->add_pa($cout * -1);
    $joueur->sauver();
	}
}

/**
 * Pour gérer les ponts "temporaires", variante à coût aléatoire
 * Si le joueur n'a pas assez de PA, mais en aurait eu assez pour
 * passer si le coût avait été minimal, il perd 2/3 des PA au delà
 * du minimum. Ceci pour éviter qu'il ré-éssaye X fois jusqu'à tirer
 * le cout minimum pour optimiser la traversée ;)
 * @param @joueur le joueur qui passe le pont
 * @param x l'autre coté du pont (x)
 * @param y l'autre coté du pont (y)
 * @param cout_min le cout minimum en PA pour passer
 * @param cout_max le cout maximum en PA pour passer
 * @param jours le pont est praticable tous les x jours
 */
function checkPontTemporaireRandTP(&$joueur, $x, $y, $cout_min = 6, $cout_max = 20, $jours = 2)
{
	$cout = rand($cout_min, $cout_max);
	if ($joueur->get_pa() < $cout) {
		if ($joueur->get_pa() > $cout_min) {
			$cout = floor(($cout - $cout_min) / 3 * 2);
			$joueur->add_pa($cout * -1);
			$joueur->sauver();
			$msg = ', vos tentatives infructueuses vous ont néanmoins coûté '.
				$cout.' PA.';
		} else $msg = '.';
		showMessage('Vous ne vous sentez pas la force de passer ce pont'.$msg,
								'Pas assez de PA');
		return;
	}
	if (((int)date('d')) % max(2, ((int)$jours) % 31) && $jours != 1)
	{
    showMessage('Le tronc de l\'arbre est trop instable pour servir à franchir la rivière, rééssayez demain',
								'L\'arbre ne peut servir de pont.');
	}
	else
	{
		if ($cout < ($cout_min + ($cout_max - $cout_min) / 3)) {
			$msg = 'Vous glissez littéralement sur les branches de l\'arbre et passez de l\'autre coté avec une facilité déconcertante.';
		}
		elseif ($cout > ($cout_min + ($cout_max - $cout_min) / 3 * 2)) {
			$msg = 'Ça a été dur, mais vous avez réussi à traverser en rampant sur le tronc d\'arbre.';
		} else {
			$msg = 'Vous réussissez à traverser la rivière en jouant l\'équilibriste sur les branche de l\'arbre.';
		}
    showMessage($msg.'<br/>Ça vous a coûté '.$cout.' PA', 'Passage précaire');
		$joueur->add_pa($cout * -1);
    $joueur->set_x($x);
    $joueur->set_y($y);
    $joueur->sauver();
	}
}

function checkTpSomnambule(&$joueur, $x, $y)
{
  list($posx, $posy) = pnjutils::get_gob_loc();
	if ($posx == 43 AND $posy == 382)
	{
    showMessage('Peut être dans le but de maîtriser les errances du somnambule, une corde semble le ceinturer à la taille avant d\'aller se perdre dans quelques crevasses obscures. C\'est avec une délicatesse de chat que vous réussissez à vous en servir sans réveiller Gafolin. Vous vous hissez dans les hauteurs et vous arrivez dans le coin d\'une grande salle', 'passage escarpé');
    $joueur->set_x($x);
    $joueur->set_y($y);
    $joueur->sauver();
	}
	else
	{
    showMessage('Les morceaux de roches composant une faille située en hauteur scintillent ici et là de milles brins de pailles broyés. Malheureusement, vous avez beau sauter, le passage reste hors d\'atteinte. Attendre est sans doute la plus sage des décisions si vous désirez absolument emprunter ce passage car vous en êtes presque certains : quelqu\'un ou quelque chose passe régulièrement par cette gorge.', 'passage escarpé');
	}
}

global $dontrefresh;
global $dontrefreshmap;

$S_requete = 'SELECT * from map_event WHERE x = '.$joueur->get_x().
' AND y = '.$joueur->get_y();

$S_query = $db->query($S_requete);
if ($db->num_rows > 0)
{
	$dontrefresh = true;
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
