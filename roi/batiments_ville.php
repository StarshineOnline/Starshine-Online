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

$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
/// @todo à améliorer
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$constr = new construction($batiment['id_batiment']);
		$lieu = $bourg->has_bonus('royaume') && !$constr->is_buff('assaut');
	}
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

$cadre = $G_interf->creer_royaume();

if( $action && $lieu && $perso->get_hp()>0 )
{
	/// @todo verifier temps
	switch($action)
	{
	case 'reactif':
    $id_batiment = $_GET['id'];
    $requete = "SELECT * FROM construction_ville WHERE id = ".$id_batiment;
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    if($royaume->get_star() >= $row['dette'])
    {
      $requete = "UPDATE construction_ville SET statut = 'actif', dette = 0 WHERE id = ".$id_batiment;
      $db->query($requete);
      $requete = "UPDATE royaume SET star = star - ".$row['dette']." WHERE ID = ".$royaume->get_id();
      if( $db->query($requete) )
      {
      	interf_alerte::enregistre(interf_alerte::msg_succes, 'Batiment bien réactivé.');
				journal_royaume::ecrire_perso('reactive_ville', new batiment_ville($row['id_batiment']), '', $row['dette']);
			}
    }
    else
    	interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars pour réactiver cette construction !');
  	break;
  case 'ameliore':
    $constr = new construction_ville($_GET['id']);
    $batiment = new batiment_ville( $constr->get_id_batiment() );
    $requete = "SELECT * FROM batiment_ville WHERE level = ".$batiment->get_level()."+1 AND type = '".$batiment->get_type()."'";
    $req = $db->query($requete);
    if( $constr->get_hp() == $batiment->get_hp() && $row = $db->read_assoc($req) )
    {
    	$nouv_batiment = new batiment_ville($row);
	    //Si le royaume a assez de stars on achète le batiment
	    if( $royaume->get_star() >= $nouv_batiment->get_cout())
	    {
	      //On paye
	      $royaume->set_star($royaume->get_star() - $row['cout']);
	      $royaume->sauver();
	      //On remplace le batiment
	      $constr->set_id_batiment( $nouv_batiment->get_id() );
	      $constr->set_hp( $nouv_batiment->get_hp() );
	      $constr->set_date( time() );
				$constr->sauver();
      	interf_alerte::enregistre(interf_alerte::msg_succes, 'Batiment bien amélioré.');
				journal_royaume::ecrire_perso('ameliore_ville', $nouv_batiment, '', $batiment->get_cout());
	    }
	    else
	  		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume ne possède pas assez de stars !');
		}
  	break;
  case 'reduit':
    // On récupère le nouveau bâtiment
    $constr = new construction_ville($_GET['id']);
    $batiment = new batiment_ville( $constr->get_id_batiment() );
    $requete = "SELECT * FROM batiment_ville WHERE level = ".$batiment->get_level()."-1 AND type = '".$batiment->get_type()."'";
    $req = $db->query($requete);
    if( $row = $db->read_assoc($req) )
    {
    	$nouv_batiment = new batiment_ville($row);
      //On remplace le batiment
      $constr->set_id_batiment( $nouv_batiment->get_id() );
      $constr->set_hp( min($nouv_batiment->get_hp(), $constr->get_hp()) );
      $constr->set_date( time() );
			$constr->sauver();
      interf_alerte::enregistre(interf_alerte::msg_succes, 'Retour à '.$nouv_batiment->get_nom().' effectué.');
			journal_royaume::ecrire_perso('reduit_ville', $nouv_batiment);
		}
		break;      
  case 'reparation':
    // Récupération de informations sur le bâtiment
    $constr = new construction_ville($_GET['id']);
    $batiment = new batiment_ville( $constr->get_id_batiment() );
    // On vérifie qu'il a assez de stars
    if($royaume->get_star() >= $batiment->get_cout())
    {
      // On répare
      $constr->set_hp( $batiment->get_hp() );
			$constr->sauver();
      //On paye
      $royaume->set_star($royaume->get_star() - $batiment->get_cout());
      $royaume->sauver();
    	interf_alerte::enregistre(interf_alerte::msg_succes, 'Réparation effectuée.');
			journal_royaume::ecrire_perso('repare_ville', $batiment, '', $batiment->get_cout());
    }
    else
  		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume ne possède pas assez de stars !');
    break;
	}
}

$cadre->set_gestion( $G_interf->creer_batiments_ville($royaume, $lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();
     
          
?>