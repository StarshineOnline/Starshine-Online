<?php
if (file_exists('root.php'))
  include_once('root.php');

$api_login = true;
$check = false;

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

if ($check == false)
{
	header('HTTP/1.1 403 Forbidden');
	echo $erreur_login;
	exit (0);
}

header('Content-Type: text/xml; charset=utf-8');

$joueur = new perso($_SESSION['ID']);

if (map::is_masked_coordinates($joueur->get_x(), $joueur->get_y())) {
	header('HTTP/1.1 403 Forbidden');
	echo 'API usage is prohibited in masked zones';
	exit (0);  
}

function cpyToAttr($node, $row, $attr) {
	if( !is_array($attr) )
	{
		$attr = array($attr);
	}
	
	if( is_array($row) )
	{
		foreach ($attr as $tattr) {
			$node->setAttribute($tattr, $row[$tattr]);
		}
	}
	elseif( $row instanceof perso )
	{
		foreach ($attr as $tattr) {
			$getter = 'get_'.$tattr;
			$node->setAttribute($tattr, $row->$getter());
		}
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

$size_view = 3;
if (is_donjon($joueur->get_x(), $joueur->get_y())) {
  $size_view = 2; // Voir pour les arènes ?
}
$root->setAttribute('radius', $size_view);

$xmin = $joueur->get_x() - $size_view;
if ($xmin < 1) $xmin = 1;
$xmax = $joueur->get_x() + $size_view;
if ($xmax > 999) $xmax = 999;
$ymin = $joueur->get_y() - $size_view;
if ($ymin < 1) $ymin = 1;
$ymax = $joueur->get_y() + $size_view;
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
$requete = "SELECT * FROM map WHERE $xmin <= x AND x <= $xmax AND $ymin <= y AND y <= $ymax ORDER BY y, x";
$req = $db->query($requete);
//Requête pour l'affichage des joueurs dans le périmètre de vision
$where = "( (x >= $xmin) AND (x <= $xmax) AND (y >= $ymin) AND (y <= $ymax) ) AND statut = 'actif'";
$order = 'y ASC, x ASC, dernier_connexion DESC';
$persos = perso::create(null, null, $order, false, $where);
foreach($persos as $p){
	// Prend en compte les effets qui peuvent agir sur le perso, utile notamment pour l'effet "camouflage" qui peut modifier l'image du perso
	$p->check_specials();
}
//Requète pour l'affichage des pnj dans le périmètre de vision
$requete_pnj = 'SELECT id, nom, image, x, y FROM pnj WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_pnj = $db->query($requete_pnj);
//Requète pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT mm.id, mm.x, mm.y, m.nom, m.lib, COUNT(*) as tot FROM map_monstre mm, monstre m WHERE mm.type = m.id AND (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) GROUP BY x, y, lib ORDER BY y ASC, x ASC, ABS(CAST(level AS SIGNED) - '.$joueur->get_level().') ASC, level ASC, nom ASC, id ASC';
$req_monstres = $db->query($requete_monstres);
//Requète pour l'affichage des drapeaux dans le périmètre de vision
$requete_drapeaux = 'SELECT placement.x, placement.y, placement.type, placement.nom, placement.royaume, placement.debut_placement, placement.fin_placement, batiment.image FROM placement, batiment WHERE (((placement.x >= '.$xmin.') AND (placement.x <= '.$xmax.')) AND ((placement.y >= '.$ymin.') AND (placement.y <= '.$ymax.'))) AND batiment.id = placement.id_batiment ORDER BY placement.y ASC, placement.x ASC';
$req_drapeaux = $db->query($requete_drapeaux);
//Requète pour l'affichage des batiments dans le périmètre de vision
$requete_batiment = 'SELECT construction.x, construction.y, construction.hp, construction.royaume, construction.nom, construction.id_batiment, batiment.image FROM construction, batiment WHERE (((construction.x >= '.$xmin.') AND (construction.x <= '.$xmax.')) AND ((construction.y >= '.$ymin.') AND (construction.y <= '.$ymax.'))) AND batiment.id = construction.id_batiment ORDER BY construction.y ASC, construction.x ASC';
$req_batiment = $db->query($requete_batiment);


while($row_p = $db->read_assoc($req_pnj))             $row_pnj[] = $row_p;
while($row_monstres = $db->read_assoc($req_monstres)) $row_m[] = $row_monstres;
while($row_drapeaux = $db->read_assoc($req_drapeaux)) $row_d[] = $row_drapeaux;
while($row_batiment = $db->read_assoc($req_batiment)) $row_b[] = $row_batiment;

while($row = $db->read_array($req)) {
  $square = $doc->createElement("square");
  // Square
  cpyToAttr($square, $row, array('x', 'y', 'decor', 'royaume', 'type'));

	// Ourself
	if ($joueur->get_x() == $row['x'] && $joueur->get_y() == $row['y']) {
		for ($i = 0; $i < count($persos); $i++) {
			if ($joueur->get_id() == $persos[$i]->get_id()) {
				$pc = $doc->createElement('pc');
				cpyToAttr($pc, $persos[$i], array(
					'nom', 'race', 'classe', 'level','melee', 'distance',
					'esquive', 'blocage', 'exp', 'incantation', 'sort_vie',
					'sort_element', 'sort_mort', 'craft', 'honneur'
				));
				$urlImage = $persos[$i]->get_image();
				$image = pathinfo($urlImage, PATHINFO_FILENAME);
				$pc->setAttribute('image', $image);
				$pc->setAttribute('mort', (($joueur->est_mort()) ? 'true' : 'false'));
				$square->appendChild($pc);
			}
		}
	}

  // PNJ
  if (!isset($_REQUEST['npc']) || $_REQUEST['npc']) {
    for ($i = 0; $i < count($row_pnj); $i++) {
      if ($row_pnj[$i]['x'] == $row['x'] && 
          $row_pnj[$i]['y'] == $row['y']) {
        $pnj = $doc->createElement('npc');
        cpyToAttr($pnj, $row_pnj[$i], array('nom', 'image'));
        $square->appendChild($pnj);
      }
    }
  }

	// Autres Personnages
	if (!isset($_REQUEST['pc']) || $_REQUEST['pc']) {
		for ($i = 0; $i < count($persos); $i++) {
			if ($persos[$i]->get_x() == $row['x'] && $persos[$i]->get_y() == $row['y']) {
				if ($joueur->get_id() == $persos[$i]->get_id())
					continue; // Ourself
				$pc = $doc->createElement('pc');
				cpyToAttr($pc, $persos[$i], array('nom', 'race'));
				if( !$persos[$i]->est_cache_classe($joueur) )
				{
					$pc->setAttribute('classe', $persos[$i]->get_classe());
				}
				if( !$persos[$i]->est_cache_niveau($joueur) )
				{
					$pc->setAttribute('level', $persos[$i]->get_level());
				}
				if( !$persos[$i]->est_cache_stat($joueur) )
				{
					cpyToAttr($pc, $persos[$i], array(
						'melee', 'distance', 'esquive', 'blocage',
						'exp', 'incantation', 'sort_vie',
						'sort_element', 'sort_mort', 'craft', 'honneur'
					));
				}
				$urlImage = $persos[$i]->get_image('', 'high', $joueur);
				$image = pathinfo($urlImage, PATHINFO_FILENAME);
				$pc->setAttribute('image', $image);
				$pc->setAttribute('mort', (($persos[$i]->est_mort()) ? 'true' : 'false'));
				$square->appendChild($pc);
			}
		}
	}

  // Batiments
  if (!isset($_REQUEST['buildings']) || $_REQUEST['buildings']) {
    for ($i = 0; $i < count($row_b); $i++) {
      if ($row_b[$i]['x'] == $row['x'] && $row_b[$i]['y'] == $row['y']) {
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
      if ($row_d[$i]['x'] == $row['x'] && $row_d[$i]['y'] == $row['y']) {
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
      if ($row_m[$i]['x'] == $row['x'] && $row_m[$i]['y'] == $row['y']) {
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
