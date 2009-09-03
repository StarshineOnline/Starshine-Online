<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);;

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_req = $db->query('SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'');
$W_row = $db->read_array($W_req);
$R = new royaume($W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());

//Informations sur le batiment
$construction = new construction(sSQL($_GET['id_construction']));
$batiment = new batiment($construction->get_id_batiment());
?>
	<div id="carte">
		<h2><?php echo $batiment->get_nom(); ?></h2>
<?php

$W_distance = detection_distance($W_case, $_SESSION["position"]);

$W_coord = convert_in_coord($W_case);

if($W_coord->x == 0 AND $W_coord->y == 0)
{
	echo 'Position - X : '.$batiment->get_x().' - Y : '.$batiment->get_y().'<br />';
	echo 'Distance de vue : '.$batiment->get_bonus4().' cases.<br />';
	echo '<h3>Joueurs en visu</h3>';
	$joueurs = list_joueurs_visu($joueur, $batiment->get_bonus4());
	$constructions = list_construction_visu($joueur, $batiment->get_bonus4());
	echo '<div class="visu"><ul>';
	foreach ($joueurs as $v) {
		//echo '<li>'.$v['nom'].'</li>';
		$position = " -- X: $v[x] - Y: $v[y] - distance : ".$v['distance'];
		affiche_perso_visu($joueur, $v, $position);
	}
	echo '</ul><h3>Constructions en visu</h3><ul>';
	foreach ($constructions as $v) {
		//echo '<li>'.$v['nom'].'</li>';
		$position = " -- X: $v[x] - Y: $v[y] - distance : ".$v['distance'];
		affiche_construction_visu($joueur, $v, $position);
	}
	echo '</ul></div>';
}
?>
	</ul>
	</div>