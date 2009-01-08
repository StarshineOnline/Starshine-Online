<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);

//Informations sur le batiment
$requete = "SELECT * FROM construction WHERE id = ".sSQL($_GET['id_construction']);
$req = $db->query($requete);
$row_c = $db->read_assoc($req);
$requete = "SELECT * FROM batiment WHERE id = ".$row_c['id_batiment'];
$req = $db->query($requete);
$row_b = $db->read_assoc($req);
?>
	<div id="carte">
		<h2><?php echo $row_b['nom']; ?></h2>
<?php

$W_distance = detection_distance($W_case, $_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
	echo 'Position - X : '.$row_c['x'].' - Y : '.$row_c['y'].'<br />';
	echo 'Distance de tir : '.$row_b['bonus4'].' case.<br />';
	echo 'Temps avant de pouvoir tirer : ';
	$pat = false;
	if($row_c['rechargement'] > time()) echo transform_sec_temp($row_c['rechargement'] - time());
	else
	{
		echo 'Pret à tirer !';
		$pat = true;
	}
	echo '<br />';
	echo '<h3>Batîments à portée </h3>';
	$x_min = $joueur['x'] - $row_b['bonus4'];
	$x_max = $joueur['x'] + $row_b['bonus4'];
	$y_min = $joueur['y'] - $row_b['bonus4'];
	$y_max = $joueur['y'] + $row_b['bonus4'];
	$requete = "SELECT * FROM construction WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	echo '<ul>';
	while($row_bp = $db->read_assoc($req_bp))
	{
		if($row_bp['royaume'] != $Trace[$joueur['race']]['numrace'] && $row_bp['id'] != $row_c['id'])
		{
			echo '<li>'.$row_bp['nom'].' - X : '.$row_bp['x'].' - Y : '.$row_bp['y'];
			if($pat && $joueur['rang_grade'] >= $row_b['bonus6']) echo ' - <a href="attaque_arme_de_siege.php?poscase='.$W_case.'&amp;table=construction&amp;type=arme_de_siege&amp;id_arme_de_siege='.$row_c['id'].'&amp;id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a></li>';
		}
	}
	echo '</ul>';
	echo '<h3>Batîments en construction à portée</h3>';
	$x_min = $joueur['x'] - $row_b['bonus4'];
	$x_max = $joueur['x'] + $row_b['bonus4'];
	$y_min = $joueur['y'] - $row_b['bonus4'];
	$y_max = $joueur['y'] + $row_b['bonus4'];
	$requete = "SELECT * FROM placement WHERE x >= ".$x_min." AND x <= ".$x_max." AND y >= ".$y_min." AND y <= ".$y_max;
	$req_bp = $db->query($requete);
	echo '<ul>';
	while($row_bp = $db->read_assoc($req_bp))
	{
		if($row_bp['royaume'] != $Trace[$joueur['race']]['numrace'] && $row_bp['id'] != $row_c['id'])
		{
			echo '<li>'.$row_bp['nom'].' - X : '.$row_bp['x'].' - Y : '.$row_bp['y'];
			if($pat && $joueur['rang_grade'] >= $row_b['bonus6']) echo ' - <a href="attaque_arme_de_siege.php?poscase='.$W_case.'&amp;table=placement&amp;type=arme_de_siege&amp;id_arme_de_siege='.$row_c['id'].'&amp;id_batiment='.$row_bp['id'].'" onclick="return envoiInfo(this.href, \'information\');">Attaquer avec l\'arme de siège</a></li>';
		}
	}
	echo '</ul>';
}
?>
	</ul>
	</div>