<?php
/**
 * @file info_monstre.php
 * Information sur un monstre
 */
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);

$map_monstre = new map_monstre($_GET['id']);
$perso = joueur::get_perso();
//Calcul de la distance qui sépare le joueur du monstre
$distance = $perso->calcule_distance($map_monstre);
/// @todo à améliorer
if( $distance > 3 || ($distance == 3 &&  $perso->get_y() > 190) )
	exit();
	
// @todo monstre spécifiques à une quête
		
// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite($map_monstre->get_nom()) );
$cadre->add( $G_interf->creer_monstre($map_monstre, true) );
$interf_princ->maj_tooltips();
