<?php // -*- php -*-
/**
* @file informationcase.php
* Permet l'affichage des informations d'une case en fonction du joueur.
* 
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);


$reponse = array_key_exists('reponse', $_GET) ? $_GET['reponse'] : 0;
// case
$id_case = $_GET['case'];
// position relative
if( !is_numeric($W_case) )
{
  if( preg_match('/rel_(-?[0-9]+)_(-?[0-9]+)/', $id_case, $regs) )
    $id_case = convert_in_pos($perso->get_x() + /*(int)*/$regs[1], $perso->get_y() + /*(int)*/$regs[2]);
}
$case = new map_case( $id_case );
if( !$case )
	exit();
//Calcul de la distance qui sépare le joueur de la case en question
$distance = $perso->calcule_distance($case);//detection_distance($case, convert_in_pos($perso->get_x(), $perso->get_y()));
/// @todo à améliorer
if( $distance > 3 || ($distance == 3 &&  $perso->get_y() > 190) )
	exit();
$coord_x = $case->get_x();
$coord_y = $case->get_y();
//Vérifie si il y a eu des modifications sur la case (fin de batiments drapeaux et autres)
$case->check_case();

// est-ce qu'onaffiche les coordonées ?
if( map::is_masked_coordinates($case->get_x(), $case->get_y()) )
{
  $coord_x = '*';
  if ($case->get_x() < $perso->get_x())
    $coord_x .= ' - '.abs($case->get_x() - $perso->get_x());
  if ($case->get_x() > $perso->get_x())
    $coord_x .= ' + '.abs($case->get_x() - $perso->get_x());
  $coord_y = '*';
  if ($case->get_y() < $perso->get_y())
    $coord_y .= ' - '.abs($case->get_y() - $perso->get_y());
  if ($case->get_y() > $perso->get_y())
    $coord_y .= ' + '.abs($case->get_y() - $perso->get_y());
}
// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('X : '.$coord_x.' | Y : '.$coord_y) );
$cadre->add( $G_interf->creer_infos_case($case, $reponse) );
$interf_princ->code_js('maj_tooltips();');

check_son_ambiance();
