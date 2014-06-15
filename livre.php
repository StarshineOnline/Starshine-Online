<?php // -*- mode: php; tab-width:2 -*-
/**
* @file livre.php
* Permet l'affichage des sorts, compétences et recettes d'alchimie et de forge.
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$perso = joueur::get_perso();

if(  array_key_exists('type', $_GET) )
	$type = $_GET['type'];
else if( $perso->get_facteur_magie() == 1 )
	$type = 'sort_jeu';
else
	$type = 'comp_jeu';

if(  array_key_exists('categorie', $_GET) )
	$categorie = $_GET['categorie'];
else
{
	switch($type)
	{
	case 'sort_combat':
		$magies = array(	'sort_element'=>$perso->get_sort_element(),
											'sort_mort'=>$perso->get_sort_mort(),
											'sort_vie'=>$perso->get_sort_vie() );
		arsort($magies);
		$magies = array_keys($magies);
		$categorie = $magies[0];
		break;
	case 'comp_combat':
		$apt = array(	'melee'=>$perso->get_melee(),
									'distance'=>$perso->get_distance(),
									'esquive'=>$perso->get_esquive(),
									'blocage'=>$perso->get_blocage(),
									'dressage'=>$perso->get_dressage() );
		arsort($apt);
		$apt = array_keys($apt);
		$categorie = $apt[0];
		break;
	default:
		$categorie = 'favoris';
	}
}
	
if(  array_key_exists('action', $_GET) )
{
	switch( $_GET['action'] )
	{
	}
}
else
{
}

$interf_princ = $G_interf->creer_jeu();
// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Livres') );
// Onglets
$tabs = $cadre->add( new interf_onglets('tab_livres', 'cont_livre') );
// Si le perso a des sort hors combat affichage de l'onglet des sorts hors combat
if( $perso->get_sort_jeu() )
{
	$img = new interf_img('image/interface/livres/iconesorthorscombat.png', 'Sorts hors combat');
	$tabs->add_onglet($img, 'livre.php?type=sort_jeu', 'tab_sort_jeu', 'invent', $type == 'sort_jeu');
}
// Si le perso a des sort de combat affichage de l'onglet des sorts de combat
if( $perso->get_sort_combat() )
{
	$img = new interf_img('image/interface/livres/iconesortcombat.png', 'Sorts de combat');
	$tabs->add_onglet($img, 'livre.php?type=sort_combat&action=onglet', 'tab_sort_combat', 'invent', $type == 'sort_combat');
}
// Si le perso a des compétences hors combat affichage de l'onglet des compétences hors combat
if( $perso->get_comp_jeu() || true )
{
	$img = new interf_img('image/interface/livres/iconecompentecehorscombat.png', 'Compétences hors combat');
	$tabs->add_onglet($img, 'livre.php?type=comp_jeu&action=onglet', 'tab_comp_jeu', 'invent', $type == 'comp_jeu');
}
// Si le perso a des compétences de combat affichage de l'onglet des compétences de combat
if( $perso->get_comp_combat() )
{
	$img = new interf_img('image/interface/livres/iconecompentececombat.png', 'Compétences de combat');
	$tabs->add_onglet($img, 'livre.php?type=comp_combat&action=onglet', 'tab_comp_combat', 'invent', $type == 'comp_combat');
}
// Si le perso a des recettes d'alchimie, affichage de l'onglet correspondant
/// TODO: à améliorer
$requete = 'SELECT * FROM perso_recette WHERE id_perso = '.$perso->get_id().' LIMIT 0, 1';
$req = $db->query($requete);
if( $db->num_rows($req) )
{
	$img = new interf_img('image/interface/livres/iconealchimie.png', 'Alchimie');
	$tabs->add_onglet($img, 'livre.php?type=alchimie&action=onglet', 'tab_alchimie', 'invent', $type == 'alchimie');
}
//Si le perso a des recettes de forge, affichage de l'onglet correspondant
if( true )
{
	$img = new interf_img('image/interface/livres/iconeforge.png', 'Forge');
	$tabs->add_onglet($img, 'livre.php?type=forge&action=onglet', 'tab_forge', 'invent', $type == 'forge');
}

$tabs->get_onglet('tab_'.$type)->add( $G_interf->creer_livre_sortcomp($type, $perso, $categorie, !$perso->est_mort()) );
$interf_princ->code_js('maj_tooltips();');

