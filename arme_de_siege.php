<?php
/**
 * @file arme_de_siege.php
 * Utilisation des armes de sièges
 */  
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);



/*$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);*/

$ads = new construction(sSQL($_GET['id_construction']));
$ads->check_buff();
$R = new royaume( $ads->get_royaume() );

$distance = $perso->calcule_distance($ads->get_x(), $ads->get_y());

if($perso->get_race() == $R->get_race() && $distance <= 3 && !$ads->is_buff('assaut'))
{
	// Cadre de la partie gauche
	//$cadre = $interf_princ->set_gauche( $G_interf->creer_arme_siege($ads) );
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite($ads->get_nom()) );
	$cadre->add( $G_interf->creer_arme_siege($ads) );
	$interf_princ->maj_tooltips();
}
else
{
	/// @todo faire quelque chose
}



?>
