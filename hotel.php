<?php // -*- mode: php; tab-width:2 -*-
/**
* @file hotel.php
* Hotel des ventes
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
if( $action == 'infos' )
{
	/// TODO: passer par un objet
	$requete = 'SELECT objet FROM hotel WHERE id='.$_GET['id'];
	$req = $db->query($requete);
	if( $res = $db->read_array($req) )
	{
		$objet = objet_invent::factory($res[0]);
		///TODO: passer par $G_interf
		new interf_infos_popover($objet->get_noms_infos(), $objet->get_valeurs_infos());
	}
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
	
$type = array_key_exists('type', $_GET) ? $_GET['type'] : 'achat';
$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 'arme';

if( $action == 'achat' && $categorie == 'perso' )
	$action = 'suppr';

switch( $action )
{
case 'achat':
	/// TODO: passer par un objet
	$requete = 'SELECT * FROM hotel WHERE type = "vente" AND id='.$_GET['id'];
	$req = $db->query($requete);
	if( $res = $db->read_assoc($req) )
	{
		if( $perso->get_star() >= $res['prix'] )
		{
			if( ($categorie == 'objet_pet' && $perso->prend_objet_pet($res['objet']) ) || $perso->prend_objet($res['objet']) )
			{
				$obj = objet_invent::factory($res['objet']);
				$perso->add_star( -$res['prix'] );
				$perso->sauver();
				interf_alerte::enregistre(interf_alerte::msg_succes,  $obj->get_nom().' acheté.');
    		$interf_princ->maj_perso();
    		$requete = 'DELETE FROM hotel WHERE id='.$_GET['id'];
    		$db->query($requete);
    		// Vendeur
				$vendeur = new perso($_GET["id_vendeur"]);
				/// TODO: vérifier s'il n'y a pas un problème avec la taxe
				$vendeur->add_star( $res['prix'] );
				$vendeur->sauver();
    		// journal
    		$db->query('INSERT INTO journal VALUES(NULL, '.$vendeur->get_id().', "vend", "", "", NOW(), "'.addslashes(nom_objet($res['objet'])).'", "'.$res['prix'].'", 0, 0)');
				// Augmentation du compteur de l'achievement
				$achiev = $vendeur->get_compteur('objets_vendus');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			else /// TODO: à améliorer
				interf_alerte::enregistre(interf_alerte::msg_erreur, $G_erreur);
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$res['prix'].' stars.');
	}
	break;
case 'vente':
	/// TODO: passer par un objet
	$requete = 'SELECT * FROM hotel WHERE type = "achat" AND id='.$_GET['id'];
	$req = $db->query($requete);
	if( $res = $db->read_assoc($req) )
	{
		$acheteur = new perso($res['id_vendeur']);
		/// TODO: logguer triche
		$obj_perso = $perso->recherche_objet($res['objet']);
		if( !$obj_perso )
			security_block(URL_MANIPULATION, 'Objet non disponible');
		$obj = objet_invent::factory($res['objet']);
		$nbr_achat = $obj->get_nombre();
		if( !array_key_exists('nombre', $_GET) && $nbr_achat > 1 )
		{
		  $interf_princ->set_dialogue( $G_interf->creer_vente_objets($res, $categorie, $obj_perso) );
		  exit;
		}
		else
		{ // Un seul exemplaire on vend directement
			/// TODO: si on ne peut pas le mettre dans l'inventaire, essayer un terrain en ville
			$nombre = array_key_exists('nombre', $_GET) ? $_GET['nombre'] : 1;
			if( $nombre > $nbr_achat || $nombre > $obj_perso[0] )
				security_block(URL_MANIPULATION, 'Vous essayez de vendre trop d\'objets');
			$obj->set_nombre($nombre);
			$obj->recompose_texte();
			///TODO: ajouter directement la possibilité de prendre plusieurs objets
			for($n=0; $n<$nombre; $n++)
			{
				if( $categorie )
				{
					if( !$acheteur->prend_objet_pet($obj->get_texte()) )
						break;
				}
				else
				{
					if( !$acheteur->prend_objet($obj->get_texte()) )
						break;
				}
			}
			if( $n )
			{
				$perso->supprime_objet($obj->get_texte(), $n);
				$perso->add_star( $res['prix']*$n );
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('objets_vendus');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
				$perso->sauver();
	  		$interf_princ->maj_perso();
	  		if( $nombre == $nbr_achat )
    			$requete = 'DELETE FROM hotel WHERE id='.$_GET['id'];
    		else
    		{
    			$obj->set_nombre($nbr_achat - $n);
    			$obj->recompose_texte();
    			$requete = 'UPDATE hotel SET objet="'.$obj->get_texte().'" WHERE id='.$_GET['id'];
				}
    		$db->query($requete);
				/// TODO: péciser le nombre
				interf_alerte::enregistre(interf_alerte::msg_succes, $obj->get_nom().' vendu.');
    		$db->query('INSERT INTO journal VALUES(NULL, '.$res["id_vendeur"].', "achete", "", "", NOW(), "'.addslashes($obj->get_nom()).'", "'.$res['prix'].'", 0, 0)');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'L\'acheteur ne peut pas prendre l\'objet, réessayez plus tard.');
		}
	}
	break;
case 'offre':
	$taxe = $R->get_taxe_diplo($perso->get_race()) / 100;
	if( array_key_exists('objet', $_GET) )
	{
		$taxe = round($_GET['prix'] * $taxe);
		$cout = ($_GET['prix'] + $taxe) * $_GET['nombre'];
		if( $perso->get_star() >= $cout )
		{
			/// TODO: passer par un objet
			$obj = $_GET['objet'];
			if( $_GET['nombre'] > 1 )
				$obj .= 'x'.$_GET['nombre'];
			$requete = 'INSERT INTO hotel (objet, id_vendeur, type, prix, race, time) VALUES ("'.$obj.'", '.$perso->get_id().', "achat", '.$_GET['prix'].', "'.$perso->get_race().'", '.time().')';
			$db->query($requete);
			$perso->add_star( -$cout );
			$perso->sauver();
			//Récupération de la taxe
			if($taxe > 0)
			{
				$R->add_star_taxe($taxe, $type);
				$R->sauver();
			}
			interf_alerte::enregistre(interf_alerte::msg_succes, 'Offre déposée.');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$res['prix'].' stars.');
	}
	else
	{
		  $interf_princ->set_dialogue( $G_interf->creer_offre_achat($categorie, $taxe) );
		  exit;
	}
	break;
case 'suppr':
	/// TODO: passer par un objet
	$requete = 'SELECT * FROM hotel WHERE type = "vente" AND id='.$_GET['id'];
	$req = $db->query($requete);
	if( $res = $db->read_assoc($req) )
	{
		if($res['id_vendeur'] != $perso->get_id())
			security_block(URL_MANIPULATION, 'Cet '.($type='vente'?'objet n\'est pas à':'offre n\'est pas de').' vous !');
		switch($type)
		{
		case 'vente':
			if($joueur->prend_objet($objObjetHotel->objet))
			{
				$db->query('DELETE FROM hotel WHERE id = '.$_GET["id"]);
				$db->query('INSERT INTO journal VALUES(NULL, '.$joueur->get_id().', "recup", "", "", NOW(), "'.sSQL(nom_objet($res['objet'])).'", 0, 0, 0)');
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous avez bien récupéré votre objet.');
			}
			else /// TODO: à améliorer
				interf_alerte::enregistre(interf_alerte::msg_erreur, $G_erreur);
			break;
		case 'achat':
				$db->query('DELETE FROM hotel WHERE id = '.$_GET["id"]);
				$perso->add_star( $res['prix'] * $res['nombre'] );
				$perso->sauver();
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous avez bien retiré votre offre.');
			break;
		}
	}
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	if( $type == 'vente' )
		$interf_princ->add( $G_interf->creer_vente_hdv($R, $categorie) );
	else
		$interf_princ->add( $G_interf->creer_achat_hdv($R, $categorie) );
}
else
	$interf_princ->set_gauche( $G_interf->creer_hotel_vente($R, $type, $categorie) );
