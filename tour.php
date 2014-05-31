<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);



$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

$tour = new construction(sSQL($_GET['id_construction']));

if($perso->get_x() == $tour->get_x() && $perso->get_y() == $tour->get_y() && $perso->get_race() == $R->get_race())
{
	// Cadre de la partie gauche
	$cadre = $interf_princ->set_gauche( $G_interf->creer_tour($tour) );
	$interf_princ->code_js('maj_tooltips();');
}
else
{
	/// TODO: faire quelque chose
}

exit;

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);;

$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

//Informations sur le batiment
$batiment = new batiment($construction->get_id_batiment());
?>
	<div id="carte">
	<fieldset>
		<legend><?php echo $batiment->get_nom(); ?></legend>
<?php

if($joueur->get_x() == $construction->get_x() AND $joueur->get_y() == $construction->get_y() AND $joueur->get_race() == $R->get_race())
{
	echo 'Position - X : '.$construction->get_x().' - Y : '.$construction->get_y().'<br />';
	echo 'Distance de vue : '.$batiment->get_bonus('batiment_vue').' cases.<br />';
	echo '<h3>Joueurs en visu</h3>';
	$joueurs = list_joueurs_visu($joueur, $batiment->get_bonus('batiment_vue'));
	$constructions = list_construction_visu($joueur, $batiment->get_bonus('batiment_vue'));
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