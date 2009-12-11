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

function gen_arene($x, $y, $size, $nom, $import = false, $make_import = false)
{
	global $db;

	$xml = new DOMDocument("1.0", "UTF-8");
	//$xml->formatOutput = true;
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

	$joueurs = $xml->createElement('joueurs');
	$root->appendChild($joueurs);

	$q = "select * from perso where x >= $x and x < ($x + $size) and y >= $y ".
		"and y < ($y + $size)";
	$req = $db->query($q);
	while ($row = $db->read_assoc($req)) {
		$joueur = create_joueur($xml, $row);
		$joueurs->appendChild($joueur);
	}

	/*
	$mirwen = array('nom' => 'Mirwen', 'race' => 'mortvivant', 'hp' => 0,
									'x' => $x, 'y' => $y, 'level' => 8, 'classe' => 'clerc');
	$punching_ball = create_joueur($xml, $mirwen);
	$joueurs->appendChild($punching_ball);
	*/
	
	return $xml->saveXML();
}

function gen_all() {
	global $db;
	$q = "select * from arenes";
	$req = $db->query($q);
	while ($arene = $db->read_object($req)) {
		$arene_xml = gen_arene($arene->x, $arene->y, $arene->size, $arene->nom);
		$arene_file = fopen(root.'arenes/'.$arene->file.'tmp', 'w+');
		fwrite($arene_file, $arene_xml);
		fclose($arene_file);
		rename(root.'arenes/'.$arene->file.'tmp', root.'arenes/'.$arene->file);
	}
}

//echo gen_arene(117, 15, 10, 'Test-donjon');
