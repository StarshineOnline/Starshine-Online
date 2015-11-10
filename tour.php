<?php
/**
 * @file tour.php
 * Utilisation des tours 
 */ 
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
$tour->check_buff();

if($perso->get_x() == $tour->get_x() && $perso->get_y() == $tour->get_y() && $perso->get_race() == $R->get_race())
{
	// Cadre de la partie gauche
	$cadre = $interf_princ->set_gauche( $G_interf->creer_tour($tour) );
	$interf_princ->maj_tooltips();
}
else
{
	/// @todo faire quelque chose
}