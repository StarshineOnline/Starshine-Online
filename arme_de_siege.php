<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->get_grade();

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);

//Informations sur le batiment
$arme = new construction($_GET['id_construction']);
$requete = "SELECT * FROM batiment WHERE id = ".$arme->get_id_batiment();
$req = $db->query($requete);
$row_b = $db->read_assoc($req);
?>
	<div id="carte">
		<h2><?php echo $row_b['nom']; ?></h2>
<?php

$W_distance = detection_distance(convert_in_pos($arme->get_x(), $arme->get_y()), $joueur->get_pos());

if($W_distance == 0)
{
	echo 'Position - X : '.$arme->get_x().' - Y : '.$arme->get_y().'<br />';
	echo 'Distance de tir : '.$row_b['bonus4'].' case.<br />';
	echo 'Temps avant de pouvoir tirer : ';
	$pat = false;
	if($arme->get_rechargement() > time()) echo transform_sec_temp($arme->get_rechargement() - time());
	else
	{
		echo 'Pret à tirer !';
		$pat = true;
	}
	echo '<br />';
	echo '<h3>Batîments à portée </h3>';
	$x_min = $joueur->get_x() - $row_b['bonus4'];
	$x_max = $joueur->get_x() + $row_b['bonus4'];
	$y_min = $joueur->get_y() - $row_b['bonus4'];
	$y_max = $joueur->get_y() + $row_b['bonus4'];
	$requete = "SELECT * FROM construction WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	echo '<ul>';
	while($row_bp = $db->read_assoc($req_bp))
	{
		if($row_bp['royaume'] != $Trace[$joueur->get_race()]['numrace'] && $row_bp['id'] != $arme->get_id())
		{
			echo '<li>'.$row_bp['nom'].' - X : '.$row_bp['x'].' - Y : '.$row_bp['y'];
			if($pat && $joueur->grade->get_rang() >= $row_b['bonus6'] && $joueur->get_pa() >= 10) echo ' - <a href="attaque.php?type=siege&table=construction&id_arme_de_siege='.$arme->get_id().'&id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège (10 PA)</a></li>';
		}
	}
	echo '</ul>';
	echo '<h3>Batîments en construction à portée</h3>';
	$requete = "SELECT * FROM placement WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	echo '<ul>';
	while($row_bp = $db->read_assoc($req_bp))
	{
		if($row_bp['royaume'] != $Trace[$joueur->get_race()]['numrace'] && $row_bp['id'] != $arme->get_id())
		{
			echo '<li>'.$row_bp['nom'].' - X : '.$row_bp['x'].' - Y : '.$row_bp['y'];
			if($pat && $joueur->grade->get_rang() >= $row_b['bonus6']) echo ' - <a href="attaque.php?table=placement&amp;type=siege&amp;id_arme_de_siege='.$arme->get_id().'&amp;id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a></li>';
		}
	}
	echo '</ul>';
	echo '<h3>Ville à portée</h3>';
	$requete = "SELECT map.id as id, nom FROM map LEFT JOIN royaume ON map.royaume = royaume.id WHERE (map.id - (floor(map.id / 1000)) * 1000) >= ".$x_min." AND (map.id - (floor(map.id / 1000)) * 1000) <= ".$x_max." AND floor(map.id / 1000) >= ".$y_min." AND floor(map.id / 1000) <= ".$y_max." AND type = 1";
	$req_v = $db->query($requete);
	$row_v = $db->read_assoc($req_v);
	if($pat && $joueur->grade->get_rang() >= $row_b['bonus6']) echo $row_v['nom'].' - <a href="attaque.php?type=ville&amp;id_arme_de_siege='.$arme->get_id().'&id_ville='.$row_v['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a>';
}
?>
	</div>
