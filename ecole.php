<?php // -*- mode: php; tab-width:2 -*-
/**
* @file ecole.php
* Écoles de magie & de combat
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
if( $action == 'infos' )
{
	$comp_sort = comp_sort::factory_gen($_GET['type'], $_GET['id']);
	///@todo passer par $G_interf
  new interf_infos_popover($comp_sort->get_noms_infos(), $comp_sort->get_valeurs_infos());
  exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///@todo à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// @todo logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	
$type = $_GET['type'];

if( $action == 'achat' )
{
	$comp_sort = comp_sort::factory_gen($type, $_GET['id']);
	///@todo loguer triche
	$taxe = ceil($comp_sort->get_prix() * $R->get_taxe_diplo($perso->get_race()) / 100);
	$prix = $comp_sort->get_prix() + $taxe;
	if( $comp_sort->est_connu($perso) )
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous connaissez déjà '.$comp_sort->get_nom());
	else if( $perso->get_star() < $prix )
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$prix.' stars.');
	else if( $comp_sort->verif_prerequis($perso, true) )
	{
		include_once(root.'fonction/competence.inc.php');
    switch($type)
    {
    case 'comp_jeu':
    case 'comp_combat':
      $appris = apprend_competence($type, $comp_sort->get_id(), $perso, null, true);
      break;
    case 'sort_jeu':
    case 'sort_combat':
      $appris = apprend_sort($type, $comp_sort->get_id(), $perso, null, true);
      break;
    }
    if( $appris )
    {
    	$perso->add_star( -$prix );
			//Récupération de la taxe
			if($taxe > 0)
			{
				$R->add_star_taxe($taxe, $type);
				$R->sauver();
			}
    	$perso->sauver();
    	$interf_princ->maj_perso();
		}
	}
	$type = substr($type, 0, 4);
}
	
// Type d'école
switch( $type )
{
case 'sort':
	$interf_princ->set_gauche( $G_interf->creer_ecole_magie($R, 'sort_jeu') );
	break;
case 'sort_jeu':
	$interf_princ->add( $G_interf->creer_achat_sort_jeu($R) );
	break;
case 'sort_combat':
	$interf_princ->add( $G_interf->creer_achat_sort_combat($R) );
	break;
case 'comp':
	$interf_princ->set_gauche( $G_interf->creer_ecole_combat($R, 'comp_jeu') );
	break;
case 'comp_jeu':
	$interf_princ->add( $G_interf->creer_achat_comp_jeu($R) );
	break;
case 'comp_combat':
	$interf_princ->add( $G_interf->creer_achat_comp_combat($R) );
	break;
}
$interf_princ->maj_tooltips();
?>
