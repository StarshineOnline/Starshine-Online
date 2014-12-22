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

$cadre = $G_interf->creer_royaume();
if( $perso->get_hp() > 0 || $action == 'cours' )
{
	switch($action)
	{
	case 'vente':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_vente() );
		exit;
	case 'offre_achat':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_achat() );
		exit;
	case 'cours':
		include_once(root.'interface/interf_bourse.class.php');
		$cadre->set_dialogue( new interf_dlg_bourse_cours() );
		exit;
	case 'vendre':
		if($_GET['nombre'] <= 0 || $prix <= 0)
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Les valeurs doivent être stgrictement positives !');
			break;
		}
		if($_GET['nombre'] > get_ressource_max($_GET['ressource']))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de '.$Gtrad[$_GET['ressource']].' !');
			break;
		}
		$enchere =  = new bourse_royaume();
		$enchere->id_royaume = $royaume->get_id();
		$enchere->ressource = $_GET['ressource'];
		$enchere->nombre = $nombre;
		$enchere->prix = $prix;
		$enchere->type = 'vente';
		$enchere->sauver();
		$requete = "UPDATE royaume SET ".$_GET['ressource']." = ".$_GET['ressource']." - ".$nombre." WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre ressource a bien été mise en vente.');
		break;
	case 'acheter':
		break;
	case 'annuler':
		break;
	case 'achat':
		break;
	case 'achat_offre':
		break;
	case 'offre_vente':
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



exit;


//case 'bourse_enchere':
		if ($RAZ_ROYAUME) { echo '<h5>Gestion impossible quand la capitale est mise à sac</h5>'; break; }
  	if( $economie )
  	{
  		require_once(root.'class/bourse_royaume.class.php');
  		require_once(root.'class/bourse.class.php');
  		$enchere = new bourse_royaume($_GET['id_enchere']);
  		//On vérifie que c'est un royaume possible
  		if($royaume->get_id() != $enchere->id_royaume AND !( (1 << ($royaume->get_id()-1)) & $enchere->id_royaume_acheteur ))
  		{
  			//On vérifie que le royaume a assez de stars
  			if($royaume->get_star() >= $enchere->prix)
  			{
  				//On prend les stars de notre royaume
  				$requete = "UPDATE royaume SET star = star - ".$enchere->prix." WHERE ID = ".$royaume->get_id();
  				$db->query($requete);
  				//On met à jour l'enchère
  				$enchere->id_royaume_acheteur |= 1 << ($royaume->get_id()-1);
  				$enchere->sauver();
  				?>
  				<h6>Enchère prise en compte !</h6>
  				<?php
  			}
  			else
  			{
  				?>
  				<h5>Vous n'avez pas assez de stars pour enchérir !</h5>
  				<?php
  			}
  		}
      else
      {
        echo '<h5>Vous ne pouvez pas placer l\'enchère</h5>';
      }
  	}
  	Else 
  	{
		echo '<p>Cette page vous est interdite</p>';
	}
?>