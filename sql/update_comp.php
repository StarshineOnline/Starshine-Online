<?php
if (file_exists('../root.php'))
  include_once('../root.php');


global $db;

include_once(root."../class/db.class.php");
if (file_exists('../connect.local.php')) {
	include_once(root."../connect.local.php");
	$db = new db();
}
else {
	include_once(root."../connect.php");
}

function id_of($type, $lvl, $table = 'comp_combat') {
	global $db;
	$requete = "SELECT id FROM $table WHERE type = '$type' and level = $lvl";
	echo "$requete \n";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	return $row['id'];
}

function depends_of($type, $lvl, $depends, $table = 'comp_combat') {
	global $db;
	$requis="";
	foreach ($depends as $dep) {
		if ($requis != "") $requis .= ";";
		$requis .= id_of($dep[0], $dep[1]);
	}
	$requete = "UPDATE $table SET requis='$requis' WHERE type = '$type' and level = $lvl";
  $req = $db->query($requete);
	echo "$requete \n";
}

function sub_new_botte($nom, $desc, $type, $comp, $comp_lvl, $effet, $effet2, $effet3, $duree, $level, $mp, $prix, $lvl_batiment) {
	global $db;

	$requete = "INSERT INTO `comp_combat` ( `id` , `nom` , `description` , `mp` , `type` , `comp_assoc` , `carac_assoc` , `carac_requis` , `comp_requis` , `arme_requis` , `effet` , `effet2` , `effet3` , `duree` , `cible` , `requis` , `prix` , `lvl_batiment` , `level` ) VALUES ( NULL , '$nom', '$desc', '$mp', '$type', '$comp', 'dexterite', '0', '$comp_lvl', 'dague', '$effet', '$effet2', '$effet3', '$duree', '4', '999', '$prix', '$lvl_batiment', '$level')";
	echo "$requete \n";
  $req = $db->query($requete);	
}

function new_botte($nom, $desc, $type, $comp, $comp_lvl, $effet, $effet2 = array(), $effet3 = array(), $duree = array()) {
	sub_new_botte($nom, $desc, $type, $comp, $comp_lvl[0], $effet[0], $effet2[0], $effet3[0], $duree[0], 1, 4, 3600, 4);
	sub_new_botte("$nom 2", $desc, $type, $comp, $comp_lvl[1], $effet[0], $effet2[1], $effet3[1], $duree[1], 2, 4, 6400, 5);
	sub_new_botte("$nom 3", $desc, $type, $comp, $comp_lvl[2], $effet[0], $effet2[2], $effet3[2], $duree[2], 3, 3, 10000, 6);
}

depends_of('posture_esquive', '2', array(array('posture_esquive', '1')));
depends_of('posture_esquive', '3', array(array('posture_esquive', '2')));
depends_of('posture_esquive', '4', array(array('posture_esquive', '3')));

new_botte('Botte du scorpion', 'Après une esquive, augmente les chances de critique (effet %effet%)', 'botte_scorpion', 'esquive', array(350, 400, 450), array(30, 40, 40));

depends_of('botte_scorpion', '1', array(array('posture_esquive', '2')));
depends_of('botte_scorpion', '2', array(array('posture_esquive', '3'),
																		array('botte_scorpion', '1')));
depends_of('botte_scorpion', '3', array(array('posture_esquive', '4'),
																		array('botte_scorpion', '1')));

new_botte('Botte de l\'\'aigle', 'Après une esquive, augmente les chances de toucher (effet %effet%)', 'botte_aigle', 'esquive', array(350, 400, 450), array(30, 40, 40));

depends_of('botte_aigle', '1', array(array('posture_esquive', '2')));
depends_of('botte_aigle', '2', array(array('posture_esquive', '3'),
																		array('botte_aigle', '1')));
depends_of('botte_aigle', '3', array(array('posture_esquive', '4'),
																		array('botte_aigle', '1')));

new_botte('Botte du crabe', 'Après une esquive, donne une chance de désarmer l\'\'adversaire (effet %effet%)', 'botte_crabe', 'esquive', array(350, 400, 450), array(10, 20, 30));

depends_of('botte_crabe', '1', array(array('posture_esquive', '2')));
depends_of('botte_crabe', '2', array(array('posture_esquive', '3'),
																		array('botte_crabe', '1')));
depends_of('botte_crabe', '3', array(array('posture_esquive', '4'),
																		array('botte_crabe', '1')));

depends_of('posture_critique', '3', array(array('posture_critique', '2')));
depends_of('posture_critique', '4', array(array('posture_critique', '3')));
depends_of('posture_critique', '5', array(array('posture_critique', '4')));

new_botte('Botte du chat', 'Après un critique, augmente les chances d\'\'esquiver (effet %effet%)', 'botte_chat', 'maitrise_critique', array(50, 100, 150), array(30, 40, 40));

depends_of('botte_ours', '1', array(array('posture_critique', '3')));
depends_of('botte_ours', '2', array(array('posture_critique', '4'),
																		array('botte_ours', '1')));
depends_of('botte_ours', '3', array(array('posture_critique', '5'),
																		array('botte_ours', '1')));

new_botte('Botte de l\'\'ours', 'Après un critique, onne une chance d\'\'étourdir l\'\'adversaire (effet %effet%)', 'botte_ours', 'maitrise_critique', array(50, 100, 150), array(30, 40, 40));

depends_of('botte_chat', '1', array(array('posture_critique', '3')));
depends_of('botte_chat', '2', array(array('posture_critique', '4'),
																		array('botte_chat', '1')));
depends_of('botte_chat', '3', array(array('posture_critique', '5'),
																		array('botte_chat', '1')));

new_botte('Botte du tigre', 'Après un critique, augmente les chances de critique (effet %effet%)', 'botte_tigre', 'maitrise_critique', array(100, 150, 200), array(50, 50, 50), array(3, 4, 5));

depends_of('botte_tigre', '1', array(array('posture_critique', '3')));
depends_of('botte_tigre', '2', array(array('posture_critique', '4'),
																		array('botte_tigre', '1')));
depends_of('botte_tigre', '3', array(array('posture_critique', '5'),
																		array('botte_tigre', '1')));

?>