<?php // -*- mode: php -*-

if (file_exists('../root.php')) {
  include_once('../root.php');
}

include_once(root.'class/db.class.php');
include_once(root.'inc/traduction.inc.php');
include_once(root.'inc/classe.inc.php');
include_once(root.'connect.php');

// defines BASE
include_once(root.'inc/variable.inc.php');

function make_attr(&$xml, &$element, $name, $value)
{
	$attr = $xml->createAttribute($name);
	$attr->appendChild($xml->createTextNode($value));
	$element->appendChild($attr);
}

function create_joueur(&$xml, $row)
{
	global $Tclasse;

	$joueur = $xml->createElement('joueur');
	make_attr($xml, $joueur, 'id', $row['id']);
	make_attr($xml, $joueur, 'x', $row['x']);
	make_attr($xml, $joueur, 'y', $row['y']);
	make_attr($xml, $joueur, 'nom', $row['nom']);
	make_attr($xml, $joueur, 'race', $row['race']);
	make_attr($xml, $joueur, 'lvl', $row['level']);
	make_attr($xml, $joueur, 'classe', $row['classe']);
	$image = $row['race'].'_'.$Tclasse[$row['classe']]['type'];
	make_attr($xml, $joueur, 'image', $image);
	if ($row['hp'] <= 0) 	make_attr($xml, $joueur, 'mort', 1);
	return $joueur;
}

function create_joueur_adm(&$xml, $row)
{
	global $Tclasse;

	$joueur = create_joueur($xml, $row);
	make_attr($xml, $joueur, 'hp', $row['hp']);
	make_attr($xml, $joueur, 'mp', $row['mp']);
	make_attr($xml, $joueur, 'hp_max', $row['hp_max']);
	make_attr($xml, $joueur, 'mp_max', $row['mp_max']);
	make_attr($xml, $joueur, 'pa', $row['pa']);
	return $joueur;
}

function journal(&$xml, $joueurs_id, $x, $y, $size)
{
	global $db;
	
	$journal = $xml->createElement('journal');
	if (count($joueurs_id) > 0) {
		$q = "select * from journal where x >= $x and x < $x + $size ".
      "and y >= $y and y < $y + $size ";
		$q .= 'and time >= (select min(time) from journal where id_perso in ('.
			implode(",", $joueurs_id).") and action = 'teleport') ";
		$q .= "and action in ('attaque', 'soin', 'gsoin', 'buff', 'debuff', ".
			"'teleport', 'recup', 'tue', 'f_quete', 'loot', 'rgbuff') ";
		$q .= 'order by time desc';
		$req = $db->query($q);
		while ($row = $db->read_assoc($req)) {
			$entry = $xml->createElement('log');
			$journal->appendChild($entry);
			make_attr($xml, $entry, 'action', $row['action']);
			make_attr($xml, $entry, 'actif', $row['actif']);
			make_attr($xml, $entry, 'passif', $row['passif']);
			make_attr($xml, $entry, 'time', $row['time']);
			make_attr($xml, $entry, 'valeur', $row['valeur']);
			make_attr($xml, $entry, 'valeur2', $row['valeur2']);
			switch($row['action']) {
			case 'attaque' :
			case 'defense' :
				make_attr($xml, $entry, 'class', 'jdegat');
				break;
			case 'tue' :
			case 'mort' :
			case 'teleport' :
				make_attr($xml, $entry, 'class', 'jkill');
				break;
			case 'soin' :
			case 'rsoin' :
			case 'gsoin' :
			case 'rgsoin' :
				make_attr($xml, $entry, 'class', 'jgsoin');
				break;
			case 'loot' :
				make_attr($xml, $entry, 'class', 'jloot');
				break;
			case 'buff' :
			case 'rbuff' :
				make_attr($xml, $entry, 'class', 'jbuff');
				break;
			case 'gbuff' :
			case 'rgbuff' :
				make_attr($xml, $entry, 'class', 'jgbuff');
				break;
			case 'debuff' :
			case 'rdebuff' :
				make_attr($xml, $entry, 'class', 'jdebuff');
				break;
			}
		}
	}
	return $journal;
}

function gen_arene($x, $y, $size, $nom, $import = false, $make_import = false)
{
	global $db;

	$xml = new DOMDocument("1.0", "UTF-8");
	$xml_adm = new DOMDocument("1.0", "UTF-8");
	//$xml->formatOutput = true;
	$xml_adm->formatOutput = true;
	$xslt = $xml->createProcessingInstruction('xml-stylesheet',
																						'type="text/xsl" href="arene.xsl"');
	$xml->appendChild($xslt);
	$root = $xml->createElement('arene');
	$xml->appendChild($root);
	$root->appendChild($xml->createElement('name', $nom));
	$origin = $xml->createElement('origin');
	make_attr($xml, $origin, 'x', $x);
	make_attr($xml, $origin, 'y', $y);
	make_attr($xml, $origin, 'size', $size);
	$root->appendChild($origin);
	$root->appendChild($xml->createElement('base', BASE));
	$root->appendChild($xml->createElement('date', date(DATE_RFC1123)));

	if ($import !== false && $make_import == false) {
		$xml2 = new DOMDocument("1.0", "UTF-8");
		$xml2->load($import);

		$copy = $xml2->getElementsByTagName('cases')->item(0);
		$cases = $xml->importNode($copy, true);
		$root->appendChild($cases);
	}
	if ($import === false || $make_import == true) {
		$cases = $xml->createElement('cases');
		$root->appendChild($cases);
		
		$q = "select decor, FLOOR(id / 1000) y, (id - (FLOOR(id / 1000) * 1000))".
			" x from map where ((FLOOR(id / 1000) >= $y) AND ".
			"(FLOOR(id / 1000) < ($y + $size))) AND ".
			"(((id - (FLOOR(id / 1000) * 1000)) >= $x) AND ".
			"((id - (FLOOR(id / 1000) * 1000)) < ($x + $size))) ORDER BY id;";
		$req = $db->query($q);
		while ($row = $db->read_assoc($req)) {
			$case = $xml->createElement('case');
			make_attr($xml, $case, 'type', $row['decor']);
			//make_attr($xml, $case, 'type', 1501);
			make_attr($xml, $case, 'x', $row['x']);
			make_attr($xml, $case, 'y', $row['y']);
			$cases->appendChild($case);
		}

		if ($make_import) {
			$xml2 = new DOMDocument("1.0", "UTF-8");
			$copy = $xml2->importNode($cases, true);
			$xml2->appendChild($copy);

			$file = fopen($import, "w+");
			fwrite($file, $xml2->saveXML());
			fclose($file);
		}
	}

	// Copy current arene to $xml_adm
	$xslt_adm = $xml_adm->createProcessingInstruction('xml-stylesheet',
																	'type="text/xsl" href="arene.xsl"');
	$xml_adm->appendChild($xslt_adm);
	$root_adm = $xml_adm->importNode($root, true);
	$xml_adm->appendChild($root_adm);
	

	$joueurs = $xml->createElement('joueurs');
	$root->appendChild($joueurs);
	$joueurs_adm = $xml_adm->createElement('joueurs');
	$root_adm->appendChild($joueurs_adm);

	$q = "select * from perso where x >= $x and x < ($x + $size) and y >= $y ".
		"and y < ($y + $size)";
	$req = $db->query($q);
	$joueurs_id = array();
	while ($row = $db->read_assoc($req)) {
		$joueur = create_joueur($xml, $row);
		$joueurs->appendChild($joueur);

		$joueur_adm = create_joueur_adm($xml_adm, $row);
		$joueurs_adm->appendChild($joueur_adm);
		$joueurs_id[] = $row['id'] + $row['ID']; /* hack débile ! */
	}

	$journal = journal($xml_adm, $joueurs_id, $x, $y, $size);
	$joueurs_adm->appendChild($journal);

	/*
	$mirwen = array('nom' => 'Mirwen', 'race' => 'mortvivant', 'hp' => 0,
									'x' => $x, 'y' => $y, 'level' => 8, 'classe' => 'clerc');
	$punching_ball = create_joueur($xml, $mirwen);
	$joueurs->appendChild($punching_ball);
	*/
	
	return array($xml->saveXML(), $xml_adm->saveXML());
}

function gen_all() {
	global $db;
	$q = "select * from arenes where open = 1";
	$req = $db->query($q);
	while ($arene = $db->read_object($req)) {
		$arene_xml = gen_arene($arene->x, $arene->y, $arene->size, $arene->nom);
		$arene_file = fopen(root.'arenes/'.$arene->file.'tmp', 'w+');
		fwrite($arene_file, $arene_xml[0]);
		fclose($arene_file);
		rename(root.'arenes/'.$arene->file.'tmp', root.'arenes/'.$arene->file);
		$arene_file = fopen(root.'arenes/admin/'.$arene->file.'tmp', 'w+');
		fwrite($arene_file, $arene_xml[1]);
		fclose($arene_file);
		rename(root.'arenes/admin/'.$arene->file.'tmp', root.'arenes/admin/'.$arene->file);
	}
}

//echo gen_arene(117, 15, 10, 'Test-donjon');
