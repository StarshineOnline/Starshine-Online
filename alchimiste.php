<?php // -*- mode: php; tab-width:2 -*-
/**
* @file boutiques.php
* Boutiques en ville : forgeron, armurerie, dresseur, enchanteur.
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
$onglet = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 'recherche';

if( $action == 'infos' )
{
	switch( $onglet )
	{
	case 'objet':
		$objet = new objet($_GET['id']);
		///@todo passer par $G_interf
	  new interf_infos_popover($objet->get_noms_infos(), $objet->get_valeurs_infos());
	  break;
	case 'recette':
		$recette = new alchimie_recette($_GET['id']);
		///@todo passer par $G_interf
	  new interf_infos_popover($recette->get_noms_infos(), $recette->get_valeurs_infos());
	  break;
	}
  exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume & case
/// @todo logguer triche
$case = new map_case($perso->get_pos());
$R = new royaume($case->get_royaume());
if( !$case->is_ville(true, 'alchimiste') )
	exit();
$ville = $case->is_ville(true);


// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	

switch( $action )
{
case 'recherche':
	if($perso->get_pa() >= 10)
	{
		// De combien il augmente la recherche ?
		$recherche = rand(1, $perso->get_alchimie());
		$R->set_alchimie($R->get_alchimie() + $recherche);
		$R->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous augmentez la recherche de votre royaume en alchimie de '.$recherche.' points');
		$perso->set_pa($perso->get_pa() - 10);
		//Augmentation de la compétence d'architecture
		$augmentation = augmentation_competence('alchimie', $perso, 2);
		if ($augmentation[1] == 1)
		{
			$perso->set_alchimie($augmentation[0]);
		}
		$perso->sauver();
		$interf_princ->maj_perso();
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA');
	break;
case 'achat':
	switch($onglet)
	{
	case 'objet':
		$achat = new objet($_GET['id']);
		break;
	case 'recette':
		$achat = new alchimie_recette($_GET['id']);
		if( $R->get_alchimie() < $achat->get_royaume_alchimie() )
			security_block(URL_MANIPULATION, 'Recette non disponible');
		break;
	}
	$taxe = ceil($achat->get_prix() * $R->get_taxe_diplo($perso->get_race()) / 100);
	$prix = $achat->get_prix() + $taxe;
	if( $perso->get_star() >= $prix )
	{
		switch($onglet)
		{
		case 'objet':
			$ok = $perso->prend_objet_pet( 'd'.$objet->get_id() );
			break;
		case 'recette':
			$ok = !perso_recette::recov($perso->get_id(), $_GET['id']);
			if($ok)
			{
				$perso_recette = new perso_recette(0, $perso->get_id(), $_GET['id']);
				$perso_recette->sauver();
			}
			break;
		default:
			$ok = false;
		}
		if( $ok )
		{
			$perso->add_star( -$prix );
			//Récupération de la taxe
			if($taxe > 0)
			{
				$R->add_star_taxe($taxe, $type);
				$R->sauver();
			}
			$perso->sauver();
			interf_alerte::enregistre(interf_alerte::msg_succes, $achat->get_nom().' acheté.');
  		$interf_princ->maj_perso();
		}
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$prix.' stars.');
	break;
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	switch($onglet)
	{
	case 'objet':
		$interf_princ->add( $G_interf->creer_achat_alchimie($R, $onglet) );
		break;
	case 'recette':
		$interf_princ->add( $G_interf->creer_achat_recette($R, $onglet) );
		break;
	case 'quetes':
		$interf_princ->add( $G_interf->creer_tbl_quetes($R, 'alchimiste') );
		break;
	}
}
else
	$interf_princ->set_gauche( $G_interf->creer_alchimiste($R, $case, $onglet) );