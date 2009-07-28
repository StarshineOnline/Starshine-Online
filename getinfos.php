<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
header('Content-Type: text/xml; charset=utf-8');
//header('Content-type: text/plain');

function cpyToAttr($node, $row, $attr) {
  if (is_array($attr)) {
    foreach ($attr as $tattr) {
      $node->setAttribute($tattr, $row[$tattr]);
    }
  }
  else {
    $node->setAttribute($attr, $row[$attr]);
  }
}

$doc = new DomDocument('1.0', 'utf-8');
if (isset($_REQUEST['xsl'])) {
  $doc->appendChild($doc->createProcessingInstruction('xml-stylesheet',
						      'href="'.
						      $_REQUEST['xsl'].
						      '" type="text/xsl"'));
}
// create root node
$root = $doc->createElement('infos');
$root = $doc->appendChild($root);

$requete = 'SELECT x, y, level FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($requete);
$row = $db->read_array($req);
//On sort de la bdd les coordonnés du joueur x,y
$coord['x'] = $row['x'];
$coord['y'] = $row['y'];
$coord_joueur['y'] = $row['y'];
$coord_joueur['x'] = $row['x'];
$xmin = $coord['x'] - 3;
if ($xmin < 1) $xmin = 1;
$xmax = $coord['x'] + 3;
if ($xmax > 999) $xmax = 999;
$ymin = $coord['y'] - 3;
if ($ymin < 1) $ymin = 1;
$ymax = $coord['y'] + 3;
if ($ymax > 1000) $ymax = 1000;

// Mise à jour des placemens
$now = time();
$rh = $time + (4 * 60);
// Armes de siège
$query = "INSERT INTO `construction` (
`id_batiment`, `x`, `y`, `royaume`, `hp`, `nom`, `type`, `rez`, `rechargement`
) SELECT
`id_batiment`,`x`,`y`,`royaume`,`hp`,`nom`,`type`,`rez`,'$rh' as `rechargement`
FROM `placement` WHERE type != 'drapeau' and `fin_placement` <= $now";

//Requète pour l'affichage de la map
$requete = 'SELECT * FROM map WHERE (((FLOOR(ID / '.$G_ligne.') >= '.$ymin.') AND (FLOOR(ID / '.$G_ligne.') <= '.$ymax.')) AND (((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) >= '.$xmin.') AND ((ID - (FLOOR(ID / '.$G_colonne.') * 1000)) <= '.$xmax.'))) ORDER BY ID';
$req = $db->query($requete);
//Requète pour l'affichage des joueurs dans le périmètre de vision
$requete_joueurs = 'SELECT ID, nom, level, race, x, y, classe, cache_classe, cache_niveau, hp, cache_stat, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, craft, honneur, exp FROM perso WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) AND statut = \'actif\' ORDER BY y ASC, x ASC, dernier_connexion DESC';
$req_joueurs = $db->query($requete_joueurs);
//Requète pour l'affichage des pnj dans le périmètre de vision
$requete_pnj = 'SELECT id, nom, image, x, y FROM pnj WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_pnj = $db->query($requete_pnj);
//Requète pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT id, x, y, nom, lib, COUNT(*) as tot FROM map_monstre WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) GROUP BY x, y, lib ORDER BY y ASC, x ASC, ABS(level - '.$row['level'].') ASC, level ASC, nom ASC, id ASC';
$req_monstres = $db->query($requete_monstres);
//Requète pour l'affichage des drapeaux dans le périmètre de vision
$requete_drapeaux = 'SELECT placement.x, placement.y, placement.type, placement.nom, placement.royaume, placement.debut_placement, placement.fin_placement, batiment.image FROM placement, batiment WHERE (((placement.x >= '.$xmin.') AND (placement.x <= '.$xmax.')) AND ((placement.y >= '.$ymin.') AND (placement.y <= '.$ymax.'))) AND batiment.id = placement.id_batiment ORDER BY placement.y ASC, placement.x ASC';
$req_drapeaux = $db->query($requete_drapeaux);
//Requète pour l'affichage des batiments dans le périmètre de vision
$requete_batiment = 'SELECT construction.x, construction.y, construction.hp, construction.royaume, construction.nom, construction.id_batiment, batiment.image FROM construction, batiment WHERE (((construction.x >= '.$xmin.') AND (construction.x <= '.$xmax.')) AND ((construction.y >= '.$ymin.') AND (construction.y <= '.$ymax.'))) AND batiment.id = construction.id_batiment ORDER BY construction.y ASC, construction.x ASC';
$req_batiment = $db->query($requete_batiment);

$joueur = recupperso($_SESSION['ID']);

while($row_joueurs = $db->read_assoc($req_joueurs))   $row_j[] = $row_joueurs;
while($row_p = $db->read_assoc($req_pnj))             $row_pnj[] = $row_p;
while($row_monstres = $db->read_assoc($req_monstres)) $row_m[] = $row_monstres;
while($row_drapeaux = $db->read_assoc($req_drapeaux)) $row_d[] = $row_drapeaux;
while($row_batiment = $db->read_assoc($req_batiment)) $row_b[] = $row_batiment;

while($row = $db->read_array($req)) {
  $square = $doc->createElement("square");
  $coord = convert_in_coord($row['ID']);
  // Square
  cpyToAttr($square, $coord, array('x', 'y'));
  cpyToAttr($square, $row, array('decor', 'royaume', 'type'));

  // Ourself
  if ($coord_joueur['x'] == $coord['x'] && $coord_joueur['y'] == $coord['y']) {
    for ($i = 0; $i < count($row_j); $i++) {
      if ($_SESSION['ID'] == $row_j[$i]['ID']) {
	$pc = $doc->createElement('pc');
        cpyToAttr($pc, $row_j[$i], 
		  array('nom', 'race', 'classe', 'level','melee', 'distance',
			'esquive', 'blocage', 'exp', 'incantation', 'sort_vie',
			'sort_element', 'sort_mort', 'craft', 'honneur'));
	$pc->setAttribute('image',$row_j[$i]['race'].'_'.
			  $Tclasse[$row_j[$i]['classe']]['type']);
        $pc->setAttribute('mort', (($row_j[$i]['hp'] < 0) ? 'true' : 'false'));
        $square->appendChild($pc);
      }
    }
  }

  // PNJ
  if (!isset($_REQUEST['npc']) || $_REQUEST['npc']) {
    for ($i = 0; $i < count($row_pnj); $i++) {
      if ($row_pnj[$i]['x'] == $coord['x'] && 
	  $row_pnj[$i]['y'] == $coord['y']) {
	$pnj = $doc->createElement('npc');
	cpyToAttr($pnj, $row_pnj[$i], array('nom', 'image'));
	$square->appendChild($pnj);
      }
    }
  }

  // Joueurs
  if (!isset($_REQUEST['pc']) || $_REQUEST['pc']) {
    for ($i = 0; $i < count($row_j); $i++) {
      if ($row_j[$i]['x'] == $coord['x'] &&
	  $row_j[$i]['y'] == $coord['y']) {
	if ($_SESSION['ID'] == $row_j[$i]['ID']) continue; // Ourself
	$pc = $doc->createElement('pc');
	cpyToAttr($pc, $row_j[$i], array('nom', 'race'));
	if (!check_affiche_bonus($row_j[$i]['cache_classe'], $row_j[$i],
				 $joueur)) {
	  $pc->setAttribute('image', $row_j[$i]['race'].'_combattant');
	  cpyToAttr($pc, $row_j[$i], 'classe');
	}
	else {
	  $pc->setAttribute('image', $row_j[$i]['race'].'_'.
			    $Tclasse[$row_j[$i]['classe']]['type']);
	}
	if (!check_affiche_bonus($row_j[$i]['cache_niveau'], $row_j[$i],
				 $joueur))
	  cpyToAttr($pc, $row_j[$i], 'level');
	if (!check_affiche_bonus($row_j[$i]['cache_stat'], $row_j[$i],
				 $joueur))
	  cpyToAttr($pc, $row_j[$i],
		    array('melee', 'distance', 'esquive', 'blocage',
			  'incantation', 'sort_vie', 'sort_element',
			  'sort_mort', 'craft', 'honneur', 'exp'));
	$pc->setAttribute('mort', (($row_j[$i]['hp'] < 0) ? 'true' : 'false'));
	$square->appendChild($pc);
      }
    }
  }

  // Batiments
  if (!isset($_REQUEST['buildings']) || $_REQUEST['buildings']) {
    for ($i = 0; $i < count($row_b); $i++) {
      if ($row_b[$i]['x'] == $coord['x'] && $row_b[$i]['y'] == $coord['y']) {
	$building = $doc->createElement('building');
	cpyToAttr($building, $row_b[$i], array('nom', 'royaume', 'hp'));
	$building->setAttribute('image', $row_b[$i]['image'].'_04');
	$square->appendChild($building);
      }
    }
  }

  // Drapeaux
  if (!isset($_REQUEST['flags']) || $_REQUEST['flags']) {
    for ($i = 0; $i < count($row_d); $i++) {
      if ($row_d[$i]['x'] == $coord['x'] && $row_d[$i]['y'] == $coord['y']) {
	$temps_passe = time() - $row_d[$i]['debut_placement'];
	$temps_total = $row_d[$i]['fin_placement'] -
	  $row_d[$i]['debut_placement'];
	$ratio_temps = ceil(3 * $temps_passe / $temps_total);
	if($row_d[$i]['type'] == 'drapeau') {
	  $row_d[$i]['image'] = 'drapeau_'.$row_d[$i]['royaume'];
	}
	else {
	  $row_d[$i]['image'] = $row_d[$i]['image'].'_0'.$ratio_temps;
	}
	$flag = $doc->createElement('flag');
	cpyToAttr($flag, $row_d[$i], array('type', 'nom', 'royaume', 'image',
					   'fin_placement'));
	$flag->setAttribute('restant',
			    strftime('%H:%M:%S',
				   $row_d[$i]['fin_placement'] - time()));
	$square->appendChild($flag);
      }
    }
  }

  // Monstres
  if (!isset($_REQUEST['monsters']) || $_REQUEST['monsters']) {
    for ($i = 0; $i < count($row_m); $i++) {
      if ($row_m[$i]['x'] == $coord['x'] && $row_m[$i]['y'] == $coord['y']) {
	$monster = $doc->createElement('monster');
	cpyToAttr($monster, $row_m[$i], array('nom', 'lib'));
	$square->appendChild($monster);
      }
    }
  }

  $root->appendChild($square);
}

// get completed xml document
echo $doc->saveXML();
?>