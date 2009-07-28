<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

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
	echo 'Distance de vue : '.$row_b['bonus4'].' cases.<br />';
	echo '<h3>Joueurs en visu</h3>';
	$joueurs = list_joueurs_visu($joueur, $row_b['bonus4']);
	echo '<div class="visu"><ul>';
	foreach ($joueurs as $v) {
		//echo '<li>'.$v['nom'].'</li>';
		$position = " -- X: $v[x] - Y: $v[y] - distance : ".$v['distance'];
		affiche_perso_visu($joueur, $v, $position);
	}
	echo '</ul></div>';
}
?>
	</ul>
	</div>