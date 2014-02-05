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

$perso = new perso($_SESSION['ID']);

if (map::is_masked_coordinates($perso->get_x(), $perso->get_y())) {
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

// Récupération des paramètres passés au script
$cacheMonPerso = false;
if( isset($_GET['cache_moi']) )
	$cacheMonPerso = true;
$cacheAutrePerso = false;
if( isset($_GET['cache_autre_perso']) )
	$cacheAutrePerso = true;
$cachePNJ = false;
if( isset($_GET['cache_pnj']) )
	$cachePNJ = true;
$cacheMonstre = false;
if( isset($_GET['cache_monstre']) )
	$cacheMonstre = true;
$cacheBatimentEnConstruction = false;
if( isset($_GET['cache_batiment_en_construction']) )
	$cacheBatimentEnConstruction = true;
$cacheBatiment = false;
if( isset($_GET['cache_batiment']) )
	$cacheBatiment = true;
$xslUrl = '';
if( isset($_GET['xsl']) )
	$xslUrl = trim($_GET['xsl']);

// Définition de la zone de vision
$sizeView = 3; // Distance de Tchebychev
$buildingSizeView = 0; // Distance euclidienne
if (is_donjon($perso->get_x(), $perso->get_y())) {
  $sizeView = 2;
   // Voir pour les arènes ?
}
else
{
	$constructions = construction::findBy(array('x' => $perso->get_x(), 'y' => $perso->get_y()));
	foreach($constructions as $construction)
	{
		if( $construction->get_royaume() == $Trace[$perso->get_race()]['numrace'] )
		{
			$batiment = new batiment($construction->get_id_batiment());
			if($batiment->has_bonus('batiment_vue'))
			{
				$buildingSizeView = max($buildingSizeView, $batiment->get_bonus('batiment_vue'));
			}
		}
	}
}

// On utilise $maxView comme une distance de Tchebychev permettant d'englober (trop largement) $sizeView et $buildingSizeView
$maxView = max($sizeView, $buildingSizeView);
// Définition de xmin, xmax, ymin, ymax, utilisés pour récupérer les données dans la BDD
// On va parfois récupérer un peu plus de données que nécessaires mais on affichera que celles qui respectent $sizeView OU $buildingSizeView
$xmin = $perso->get_x() - $maxView;
$xmax = $perso->get_x() + $maxView;
$ymin = $perso->get_y() - $maxView;
$ymax = $perso->get_y() + $maxView;

// Vérification des modifications à effectuer sur les cases avant d'en récupérer le contenu
// Notamment, transformation des 'placement' en 'construction' si nécessaire
$casePerso = new map_case($perso->get_x(), $perso->get_y());
$casePerso->check_case($maxView);

//Requête pour l'affichage de la map
$requete = "SELECT * FROM map WHERE $xmin <= x AND x <= $xmax AND $ymin <= y AND y <= $ymax ORDER BY y, x";
$req = $db->query($requete);
// Requête pour l'affichage des joueurs dans le périmètre de vision
$where = "( (x >= $xmin) AND (x <= $xmax) AND (y >= $ymin) AND (y <= $ymax) ) AND statut = 'actif'";
$order = 'y ASC, x ASC, dernier_connexion DESC';
$persos = perso::create(null, null, $order, false, $where);
foreach($persos as $p){
	// Prend en compte les effets qui peuvent agir sur le perso, utile notamment pour l'effet "camouflage" qui peut modifier l'image du perso
	$p->check_specials();
}
//Requête pour l'affichage des pnj dans le périmètre de vision
$requete_pnjs = 'SELECT id, nom, image, x, y FROM pnj WHERE (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) ORDER BY y ASC, x ASC';
$req_pnjs = $db->query($requete_pnjs);
//Requête pour l'affichage des monstres dans le périmètre de vision
$requete_monstres = 'SELECT mm.id, mm.x, mm.y, m.nom, m.lib, COUNT(*) as tot FROM map_monstre mm, monstre m WHERE mm.type = m.id AND (((x >= '.$xmin.') AND (x <= '.$xmax.')) AND ((y >= '.$ymin.') AND (y <= '.$ymax.'))) GROUP BY x, y, lib ORDER BY y ASC, x ASC, ABS(CAST(level AS SIGNED) - '.$perso->get_level().') ASC, level ASC, nom ASC, id ASC';
$req_monstres = $db->query($requete_monstres);
// Requête pour l'affichage des "placements" (bâtiments, drapeaux... en construction) dans le périmètre de vision
// Différents types à l'heure actuelle : 'drapeau' 'arme_de_siege' 'mur' 'mine' 'bourg' 'fort' 'tour'
$requete_placements = "
	SELECT p.x, p.y, p.type, p.nom, p.hp, p.royaume, p.debut_placement, p.fin_placement, b.image, b.hp hp_max
	FROM
		placement p
		INNER JOIN batiment b ON p.id_batiment = b.id
	WHERE
		p.x >= $xmin AND p.x <= $xmax
		AND p.y >= $ymin AND p.y <= $ymax
	ORDER BY p.y ASC, p.x ASC
";
$req_placements = $db->query($requete_placements);
// Requête pour l'affichage des bâtiments dans le périmètre de vision
// Différents types à l'heure actuelle : 'arme_de_siege' 'mur' 'mine' 'bourg' 'fort' 'tour'
// Les drapeaux ne font pas partie des bâtiments construits car une fois construits ils disparaissent et modifie le royaume de la case sur laquelle il se trouvait.
$requete_batiments = "
	SELECT c.x, c.y, c.type, c.nom, c.hp, c.royaume, b.image, b.hp hp_max
	FROM
		construction c
		INNER JOIN batiment b ON c.id_batiment = b.id
	WHERE
		c.x >= $xmin AND c.x <= $xmax
		AND c.y >= $ymin AND c.y <= $ymax
	ORDER BY c.y ASC, c.x ASC
";
$req_batiments = $db->query($requete_batiments);

while($row_p = $db->read_assoc($req_pnjs))
	$row_pnjs[] = $row_p;
while($row_monstres = $db->read_assoc($req_monstres))
	$row_m[] = $row_monstres;
while($row_placements = $db->read_assoc($req_placements))
	$row_pl[] = $row_placements;
while($row_batiments = $db->read_assoc($req_batiments))
	$row_b[] = $row_batiments;
// On ne garde que les cases qui respectent la distance $sizeView OU la distance $buildingSizeView
$casesVisibles = array();
$xminView = $perso->get_x() - $sizeView; $xmaxView = $perso->get_x() + $sizeView;
$yminView = $perso->get_y() - $sizeView; $ymaxView = $perso->get_y() + $sizeView;
$posBuildingView = convert_in_pos($perso->get_x(), $perso->get_y());
while($case = $db->read_array($req))
{
	$currentPosBuildingView = convert_in_pos($case['x'], $case['y']);
	$currentBuildingViewDistance = calcul_distance_pytagore($posBuildingView, $currentPosBuildingView);
	// $sizeView distance de Tchebychev et $buildingSizeView distance euclidienne
	if(
		// Vérification pour $sizeView
		$case['x'] >= $xminView && $case['x'] <= $xmaxView && $case['y'] >= $yminView && $case['y'] <= $ymaxView
		||
		// Vérification pour $buildingSizeView
		$currentBuildingViewDistance <= $buildingSizeView
	)
	{
		$casesVisibles[] = $case;
	}
}


/********************************************************************************************************/
/*************************************** Création du document XML ***************************************/
/********************************************************************************************************/
$doc = new DomDocument('1.0', 'utf-8');
if( $xslUrl != '' ){
	$doc->appendChild($doc->createProcessingInstruction('xml-stylesheet', 'href="'.$xslUrl.'" type="text/xsl"'));
}
// Create root node
$root = $doc->createElement('infos');
$root = $doc->appendChild($root);

$root->setAttribute('radius', $sizeView);
$root->setAttribute('building_radius', $buildingSizeView);
$root->setAttribute('timezone', date_default_timezone_get());

foreach($casesVisibles as $case) {
	$square = $doc->createElement("square");
	// Square
	cpyToAttr($square, $case, array('x', 'y', 'decor', 'royaume', 'type'));
	$typeTerrainArray = type_terrain($case['info']);
	$typeTerrain = $typeTerrainArray[0];
	$square->setAttribute('type_terrain', $typeTerrain);
	
	// Mon perso
	if (!$cacheMonPerso) {
		if ($perso->get_x() == $case['x'] && $perso->get_y() == $case['y']) {
			for ($i = 0; $i < count($persos); $i++) {
				if ($perso->get_id() == $persos[$i]->get_id()) {
					$pc = $doc->createElement('pc');
					cpyToAttr($pc, $persos[$i], array(
						'nom', 'race', 'classe', 'level','melee', 'distance',
						'esquive', 'blocage', 'exp', 'incantation', 'sort_vie',
						'sort_element', 'sort_mort', 'craft', 'honneur'
					));
					$urlImage = $persos[$i]->get_image();
					$image = pathinfo($urlImage, PATHINFO_FILENAME);
					$pc->setAttribute('image', $image);
					$pc->setAttribute('mort', (($perso->est_mort()) ? 'true' : 'false'));
					$square->appendChild($pc);
				}
			}
		}
	}

  // PNJs
  if (!$cachePNJ) {
    for ($i = 0; $i < count($row_pnjs); $i++) {
      if ($row_pnjs[$i]['x'] == $case['x'] && 
          $row_pnjs[$i]['y'] == $case['y']) {
        $pnj = $doc->createElement('npc');
        cpyToAttr($pnj, $row_pnjs[$i], array('nom', 'image'));
        $square->appendChild($pnj);
      }
    }
  }

	// Autres Personnages
	if (!$cacheAutrePerso) {
		for ($i = 0; $i < count($persos); $i++) {
			if ($persos[$i]->get_x() == $case['x'] && $persos[$i]->get_y() == $case['y']) {
				if ($perso->get_id() == $persos[$i]->get_id())
					continue; // Ourself
				$pc = $doc->createElement('pc');
				cpyToAttr($pc, $persos[$i], array('nom', 'race'));
				if( !$persos[$i]->est_cache_classe($perso) )
				{
					$pc->setAttribute('classe', $persos[$i]->get_classe());
				}
				if( !$persos[$i]->est_cache_niveau($perso) )
				{
					$pc->setAttribute('level', $persos[$i]->get_level());
				}
				if( !$persos[$i]->est_cache_stat($perso) )
				{
					cpyToAttr($pc, $persos[$i], array(
						'melee', 'distance', 'esquive', 'blocage',
						'exp', 'incantation', 'sort_vie',
						'sort_element', 'sort_mort', 'craft', 'honneur'
					));
				}
				$urlImage = $persos[$i]->get_image('', 'high', $perso);
				$image = pathinfo($urlImage, PATHINFO_FILENAME);
				$pc->setAttribute('image', $image);
				$pc->setAttribute('mort', (($persos[$i]->est_mort()) ? 'true' : 'false'));
				$square->appendChild($pc);
			}
		}
	}

	// Bâtiments
	// Différents types à l'heure actuelle : 'arme_de_siege' 'mur' 'mine' 'bourg' 'fort' 'tour'
	if (!$cacheBatiment) {
		for ($i = 0; $i < count($row_b); $i++) {
			if ($row_b[$i]['x'] == $case['x'] && $row_b[$i]['y'] == $case['y']) {
				$building = $doc->createElement('building');
				cpyToAttr($building, $row_b[$i], array('type', 'nom', 'royaume', 'hp', 'hp_max'));
				$building->setAttribute('image', $row_b[$i]['image'].'_04');
				$square->appendChild($building);
			}
		}
	}

	// Placements (bâtiments, drapeaux... en construction)
	// Différents types à l'heure actuelle : 'drapeau' 'arme_de_siege' 'mur' 'mine' 'bourg' 'fort' 'tour'
	if (!$cacheBatimentEnConstruction) {
		for ($i = 0; $i < count($row_pl); $i++) {
			if ($row_pl[$i]['x'] == $case['x'] && $row_pl[$i]['y'] == $case['y']) {
				$placement = $doc->createElement('construction');
				cpyToAttr($placement, $row_pl[$i], array('type', 'nom', 'royaume', 'hp', 'hp_max', 'debut_placement', 'fin_placement'));
				$image = '';
				if($row_pl[$i]['type'] == 'drapeau') {
					$image = 'drapeau_'.$row_pl[$i]['royaume'];
				}
				else {
					$temps_passe = time() - $row_pl[$i]['debut_placement'];
					$temps_total = $row_pl[$i]['fin_placement'] - $row_pl[$i]['debut_placement'];
					$ratio_temps = ceil(3 * $temps_passe / $temps_total);
					$image = $row_pl[$i]['image'].'_0'.$ratio_temps;
				}				
				$placement->setAttribute('image', $image);
				$square->appendChild($placement);
			}
		}
	}

  // Monstres
  if (!$cacheMonstre) {
    for ($i = 0; $i < count($row_m); $i++) {
      if ($row_m[$i]['x'] == $case['x'] && $row_m[$i]['y'] == $case['y']) {
        $monster = $doc->createElement('monster');
        cpyToAttr($monster, $row_m[$i], array('nom', 'lib'));
        $square->appendChild($monster);
      }
    }
  }

  $root->appendChild($square);
}

// Get the XML document
echo $doc->saveXML();
