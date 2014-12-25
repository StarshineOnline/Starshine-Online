<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( ($perso->get_rang() != 6 && $royaume->get_ministre_economie() != $perso->get_id()) || $royaume->is_raz() )
{
	/// @todo logguer triche
	exit;
}
$onglet = array_key_exists('onglet', $_GET) ? $_GET['onglet'] : 'achat';
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$G_url->add('onglet', $onglet);

$cadre = $G_interf->creer_royaume();
if( $perso->get_hp() > 0 || $action == 'cours' )
{
	switch($action)
	{
	case 'vente':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_vente() );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'offre_achat':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_achat() );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'cours':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_cours() );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'vendre':
		if($_GET['nombre'] <= 0 || $_GET['prix'] <= 0)
		{
			/// @todo loguer triche
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les valeurs doivent être strictement positives !');
			break;
		}
		if($_GET['nombre'] > get_ressource_max($_GET['ressource']))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de '.$Gtrad[$_GET['ressource']].' !');
			break;
		}
		$enchere = new bourse_royaume();
		$enchere->id_royaume = $royaume->get_id();
		$enchere->ressource = $_GET['ressource'];
		$enchere->nombre = $_GET['nombre'];
		$enchere->prix = $_GET['prix'];
		$enchere->type = 'vente';
		$enchere->sauver();
		/// @todo passer à l'objet
		$requete = "UPDATE royaume SET ".$_GET['ressource']." = ".$_GET['ressource']." - ".$_GET['nombre']." WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre ressource a bien été mise en vente.');
		$cadre->maj_royaume();
		break;
	case 'acheter':
		if($_GET['nombre'] <= 0)
		{
			/// @todo loguer triche
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les valeurs doivent être strictement positives !');
			break;
		}
		$enchere = new bourse_royaume();
		$enchere->id_royaume = $royaume->get_id();
		$enchere->ressource = $_GET['ressource'];
		$enchere->nombre = $_GET['nombre'];
		// proposition du jeu
		/// @todo passer à l'objet
		$requete = 'select '.$enchere->ressource.' from royaume where id = 0';
    $req = $db->query($requete);
    $row = $db->read_array($req);
    if( $row[0] >= $enchere->nombre )
    {
    	$requete = 'select sum('.$enchere->ressource.') as total from royaume';
    	$req = $db->query($requete);
    	$row = $db->read_array($req);
	    $total = $row['total'];
	    $requete = 'select sum(nombre) as total from bourse_royaume where actif = 1 and ressource = "'.$enchere->ressource.'"';
	    $req = $db->query($requete);
	    $row = $db->read_array($req);
	    $total += $row['total'];
	    $enchere->prix = $enchere->nombre * (5 + 50 / sqrt(1 + $total/100000));
		}
		else
			$enchere->prix = 0;
		$enchere->type = 'achat';
	   $enchere->id_royaume_acheteur = 0;
		$enchere->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre offre d\'achat a bien été enregistrée.');
		break;
	case 'annuler':
		$enchere = new bourse_royaume($_GET['id']);
		/// @todo passer à l'objet
		if( $enchere->prix > 0 )
		{
			$requete = "UPDATE royaume SET ".$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->nombre.' WHERE ID = '.$enchere->id_royaume_acheteur;
			$db->query($requete);
		}
		$enchere->supprimer();
		break;
	case 'achat':
		$enchere = new bourse_royaume($_GET['id']);
		if($royaume->get_id() == $enchere->id_royaume || ( (1 << ($royaume->get_id()-1)) & $enchere->id_royaume_acheteur ))
		{
			/// @todo loguer triche
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas placer l\'enchère.');
			break;
		}
		if($royaume->get_star() < $enchere->prix)
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars pour enchérir !');
			break;
		}
		//On prend les stars de notre royaume
		/// @todo passer à l'objet
		$requete = "UPDATE royaume SET star = star - ".$enchere->prix." WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		//On met à jour l'enchère
		$enchere->id_royaume_acheteur |= 1 << ($royaume->get_id()-1);
		$enchere->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Enchère prise en compte !');
		$cadre->maj_royaume();
		break;
	case 'achat_offre':
		$enchere = new bourse_royaume($_GET['id']);
		if($royaume->get_star() < $enchere->prix)
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars pour acheter !');
			break;
		}
		/// @todo passer à l'objet
		$requete = "UPDATE royaume SET star = star - ".$enchere->prix.', '.$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->nombre.' WHERE ID = '.$royaume->get_id();
		$db->query($requete);
		$requete = "UPDATE royaume SET star = star + ".$enchere->prix.' WHERE ID = '.$enchere->id_royaume_acheteur;
		$db->query($requete);
		$enchere->actif = 0;
		$enchere->sauver();
		$cadre->maj_royaume();
		break;
	case 'offre_vente':
		include_once(root.'interface/interf_bourse.class.php');
		$enchere = new bourse_royaume($_GET['id']);
		$cadre->set_dialogue( new interf_dlg_bourse_offre($enchere) );
		if( array_key_exists('ajax', $_GET) )
		 exit;
		break;
	case 'offre_vente2':
		$enchere = new bourse_royaume($_GET['id']);
		if( $_GET['prix'] <= 0 )
		{
			/// @todo loguer triche
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les valeurs doivent être strictement positives !');
			break;
		}
		if( $royaume->get_id() == $enchere->id_royaume || $enchere->id_royaume_acheteur == $royaume->get_id() )
		{
			/// @todo loguer triche
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas placer d\'offre.');
			break;
		}
		if($enchere->nombre > get_ressource_max($enchere->ressource))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de '.$Gtrad[$enchere->ressource].' !');
			break;
		}
		/// @todo passer à l'objet
		if( $enchere->prix > 0 )
		{
			$requete = "UPDATE royaume SET ".$enchere->ressource.' = '.$enchere->ressource.' + '.$enchere->nombre.' WHERE ID = '.$enchere->id_royaume_acheteur;
			$db->query($requete);
		}
		$requete = "UPDATE royaume SET ".$enchere->ressource.' = '.$enchere->ressource.' - '.$enchere->nombre.' WHERE ID = '.$royaume->get_id();
		$db->query($requete);
		$enchere->prix = $_GET['prix'];
		$enchere->id_royaume_acheteur = $royaume->get_id();
		$enchere->sauver();
		$cadre->maj_royaume();
		break;
	}
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
		switch($onglet)
		{
		case 'achat':
			$cadre->add( $G_interf->creer_bourse_achat() );
			break;
		case 'vente':
			$cadre->add( $G_interf->creer_bourse_vente() );
			break;
		case 'hist_achat':
			$cadre->add( $G_interf->creer_bourse_hist_achat() );
			break;
		case 'hist_vente':
			$cadre->add( $G_interf->creer_bourse_hist_vente() );
			break;
		}
}
else
{
	$cont = $cadre->set_gestion( new interf_bal_cont('div') );
	interf_alerte::aff_enregistres($cont);
	$cont->add( $G_interf->creer_bourse($onglet) );
	if( $perso->get_hp() > 0 )
	{
		$cont->add( new interf_lien('Vendre des ressources', 'bourse.php?action=vente', false, 'btn btn-default') );
		$cont->add( new interf_lien('Déposer une offre d\'achat', 'bourse.php?action=offre_achat', false, 'btn btn-default') );
	}
	$cont->add( new interf_lien('Voir le cours des ressources', 'bourse.php?action=cours', false, 'btn btn-default') );
	$cadre->maj_tooltips();
}


function get_ressource_max($ressource)
{
	global $royaume;
	switch ($ressource)
	{
	case 'bois' :
		return $royaume->get_bois();
	case 'eau' :
		return $royaume->get_eau();
	case 'essence' :
		return $royaume->get_essence();
	case 'pierre' :
		return $royaume->get_pierre();
	case 'sable' :
		return $royaume->get_sable();
	case 'charbon' :
		return $royaume->get_charbon();
	case 'food' :
		return $royaume->get_food();
	default:
	 return null;
	}
}
?>