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

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

//Informations sur le batiment
$construction = new construction(sSQL($_GET['id_construction']));
$batiment = new batiment($construction->get_id_batiment());
?>
	<div id="carte">
	<fieldset>
		<legend><?php echo $batiment->get_nom(); ?></legend>
<?php

if($joueur->get_x() == $construction->get_x() AND $joueur->get_y() == $construction->get_y() AND $joueur->get_race() == $R->get_race())
{
	echo 'Position - X : '.$construction->get_x().' - Y : '.$construction->get_y().'<br />';
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
	</fieldset>
	</div>