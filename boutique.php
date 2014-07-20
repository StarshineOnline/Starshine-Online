<?php // -*- mode: php; tab-width:2 -*-
/**
* @file boutiques.php
* Boutiques en ville : forgeron, armurerie, dresseur, enchanteur.
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
if( $action == 'infos' )
{
	$objet = objet_invent::factory($_GET['id'], $_GET['type']);
	///TODO: passer par $G_interf
  new interf_infos_popover($objet->get_noms_infos(), $objet->get_valeurs_infos());
  exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///TODO: à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// TODO: logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// TODO: logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// TODO: logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	
$type = $_GET['type'];
$tab = array_key_exists('categorie', $_GET);
$categorie = $tab ? $_GET['categorie'] : false;

if( $action == 'achat' )
{
	$objet = objet_invent::factory($_GET['id'], $_GET['type']);
	if( $R->get_niveau_batiment($type) < $objet->get_lvl_batiment() )
		security_block(URL_MANIPULATION, 'Batiment non disponible dans cette ville');
	if( $objet->peut_utiliser($perso) )
	{
		$taxe = ceil($objet->get_prix() * $R->get_taxe_diplo($perso->get_race()) / 100);
		$prix = $objet->get_prix() + $taxe;
		if( $perso->get_star() >= $objet->get_prix() )
		{
			if( ($categorie == 'dressage' && $perso->prend_objet_pet('d'.$objet->get_id()) ) || $perso->prend_objet($objet->get_texte()) )
			{
				$perso->add_star( -$objet->get_prix() );
									
				//Récupération de la taxe
				if($taxe > 0)
				{
					$R->add_star_taxe($taxe, $type);
					$R->sauver();
				}
				$perso->sauver();
				interf_alerte::enregistre(interf_alerte::msg_succes, $objet->get_nom().' acheté.');
    		$interf_princ->maj_perso();
			}
			else /// TODO: à améliorer
				interf_alerte::enregistre(interf_alerte::msg_erreur, $G_erreur);
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$prix.' stars.');
	}
	$tab = false;
}
	
// Type de boutique
switch( $type )
{
case 'arme':
	if(!$categorie)
		$categorie = 'epee';
	if( $tab )
		$interf_princ->add( $G_interf->creer_achat_arme($R, $categorie) );
	else
		$interf_princ->set_gauche( $G_interf->creer_forgeron($R, $categorie) );
	break;
case 'armure':
	if(!$categorie)
		$categorie = 'torse';
	if( $tab )
		$interf_princ->add( $G_interf->creer_achat_armure($R, $categorie) );
	else
		$interf_princ->set_gauche( $G_interf->creer_armurerie($R, $categorie) );
	break;
case 'accessoire':
	if(!$categorie)
		$categorie = 'grand';
	if( $tab )
		$interf_princ->add( $G_interf->creer_achat_accessoire($R, $categorie) );
	else
		$interf_princ->set_gauche( $G_interf->creer_enchanteur($R, $categorie) );
	break;
case 'dressage':
	if(!$categorie)
		$categorie = 'cou';
	if( $tab )
		$interf_princ->add( $G_interf->creer_achat_dressage($R, $categorie) );
	else
		$interf_princ->set_gauche( $G_interf->creer_dresseur($R, $categorie) );
	break;
}
$interf_princ->maj_tooltips();

?>
