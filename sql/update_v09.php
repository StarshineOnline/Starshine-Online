<?php
if (file_exists('../root.php'))
  include_once('../root.php');

function __autoload($class_name)
{
	$file = root.'class/'.$class_name.'.class.php';
	require_once($file);
}
include_once(root."connect.php");

$requete = 'SELECT id, inventaire_slot, quete FROM perso WHERE inventaire NOT LIKE ""';
$req = $db->query($requete);
while( $row = $db->read_assoc($req) )
{
	// encombrement
	$inventaire = unserialize($row['inventaire_slot']);
	$encombrement = 0;
	foreach($inventaire as $o)
	{
		$obj = objet_invent::factory($o);
		$encombrement += $obj->get_encombrement();
	}
	$requete = 'UPDATE perso SET encombrement = '.$encombrement.' WHERE id = '.$row['id'];
	$db->query($requete);
	// quêtes
	$quetes = unserialize($row['quete']);
	foreach($quetes as $q)
	{
		$id = $q['id_quete'];
		switch($id)
		{
		case 49:
			$id = 53;
			$etape = 1;
			break;
		case 50:
			$id = 53;
			$etape = 2;
			break;
		case 52:
			$id = 53;
			$etape = 3;
			break;
		case 53:
			$etape = 4;
			break;
		case 86:
			$id = 87;
			$etape = 1;
			break;
		case 87:
			$etape = 2;
			break;
		case 151:
			$id = 87;
			$etape = 3;
			break;
		default:
			$etape = 1;
		}
		$etape = 1;
		$obj = array();
		foreach($q['objectif'] as $o)
		{
			$obj[] = $o->cible.':'.($o->nombre?$o->nombre:'0');
		}
		$objectif = implode(';', $obj);
		$requete = "INSERT INTO quete_perso (id_perso, id_quete, id_etape, avancement) VALUES (".$row['id'].", $id, $etape, '$objectif')";
		$db->query($requete);
	}
}




?>