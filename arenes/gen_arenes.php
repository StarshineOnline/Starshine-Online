<?php // -*- mode: php -*-

if (file_exists('../root.php')) {
  include_once('../root.php');
}

include_once(root.'class/db.class.php');
include_once(root.'inc/traduction.inc.php');
include_once(root.'inc/classe.inc.php');
include_once(root.'connect.php');

global $Tclasse2;
$Tclasse2 = $Tclasse;

function make_attr(&$xml, &$element, $name, $value)
{
	$attr = $xml->createAttribute($name);
	$attr->appendChild($xml->createTextNode($value));
	$element->appendChild($attr);
}

function gen_arene($x, $y, $size, $nom)
{
	global $db;
	global $Tclasse2;

	$xml = new DOMDocument("1.0", "UTF-8");
	$xml->formatOutput = true;
	$xslt = $xml->createProcessingInstruction('xml-stylesheet',
																						'type="text/xsl" href="arene.xsl"');
	$xml->appendChild($xslt);
	$root = $xml->createElement('arene');
	$xml-> appendChild($root);
	$root->appendChild($xml->createElement('name', $nom));
	$origin = $xml->createElement('origin');
	make_attr($xml, $origin, 'x', $x);
	make_attr($xml, $origin, 'y', $y);
	$root->appendChild($origin);
	$root->appendChild($xml->createElement('base',
																				 'http://www.starshine-online.com/'));
	$cases = $xml->createElement('cases');
	$root->appendChild($cases);

	$q = "select decor, FLOOR(id / 1000) y, (id - (FLOOR(id / 1000) * 1000)) x".
		" from map where ((FLOOR(id / 1000) >= $y) AND ".
		"(FLOOR(id / 1000) < ($y + $size))) AND ".
		"(((id - (FLOOR(id / 1000) * 1000)) >= $x) AND ".
		"((id - (FLOOR(id / 1000) * 1000)) <= ($x + $size))) ORDER BY id;";
	$req = $db->query($q);
	while ($row = $db->read_assoc($req)) {
		$case = $xml->createElement('case');
		make_attr($xml, $case, 'type', $row['decor']);
		make_attr($xml, $case, 'x', $row['x']);
		make_attr($xml, $case, 'y', $row['y']);
		$cases->appendChild($case);
	}

	$joueurs = $xml->createElement('joueurs');
	$root->appendChild($joueurs);

	$q = "select * from perso where x >= $x and x < ($x + $size) and y >= $y ".
		"and y < ($y + $size)";
	$req = $db->query($q);
	while ($row = $db->read_assoc($req)) {
		$joueur = $xml->createElement('joueur');
		make_attr($xml, $joueur, 'id', $row['id']);
		make_attr($xml, $joueur, 'x', $row['x']);
		make_attr($xml, $joueur, 'y', $row['y']);
		make_attr($xml, $joueur, 'nom', $row['nom']);
		make_attr($xml, $joueur, 'race', $row['race']);
		make_attr($xml, $joueur, 'lvl', $row['level']);
		make_attr($xml, $joueur, 'classe', $row['classe']);
		$image = $row[race].'_'.$Tclasse2[$row['classe']]['type'];
		make_attr($xml, $joueur, 'image', $image);
		$joueurs->appendChild($joueur);
	}


	return $xml->saveXML();
}

echo gen_arene(120, 15, 6, 'Test');
