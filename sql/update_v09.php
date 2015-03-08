<?php
if (file_exists('../root.php'))
  include_once('../root.php');
/*include_once(root."class/db.class.php");
include_once(root."class/table.class.php");
include_once(root."class/objet_invent.class.php");
include_once(root."class/objet_equip.class.php");
include_once(root."class/accessoire.class.php");
include_once(root."class/arme.class.php");
include_once(root."class/armure.class.php");
include_once(root."class/grimoire.class.php");
include_once(root."class/objet.class.php");
include_once(root."class/objet_pet.class.php");
include_once(root."class/gemme.class.php");
include_once(root."class/objet_royaume.class.php");*/
function __autoload($class_name)
{
	$file = root.'class/'.$class_name.'.class.php';
	require_once($file);
}
include_once(root."connect.php");

$requete = 'SELECT id, nom, inventaire_slot FROM perso WHERE inventaire NOT LIKE ""';
$req = $db->query($requete);
while( $row = $db->read_assoc($req) )
{
	echo $row['nom'].' (#'.$row['id'].') : '.$row['inventaire_slot']."\n";
	$tbl = unserialize($row['inventaire_slot']);
	$encombrement = 0;
	foreach($tbl as $o)
	{
		$obj = objet_invent::factory($o);
		$encombrement += $obj->get_encombrement();
		echo "\t$o -> $encombrement\n";
	}
	$requete = 'UPDATE perso SET encombrement = '.$encombrement.' WHERE id = '.$row['id'];
	$db->query($requete);
}




?>