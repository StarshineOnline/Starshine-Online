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
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$lieu = $bourg->has_bonus('royaume');
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
    $id_batiment = $_GET['id'];
    $batiment = new batiment_ville($id_batiment);
    //Si le royaume a assez de stars on achète le batiment
    if($royaume->get_star() >= $batiment->get_cout())
    {
      //On paye
      $royaume->set_star($royaume->get_star() - $row['cout']);
      $royaume->sauver();
      //On remplace le batiment
      $requete = 'UPDATE construction_ville SET id_batiment = '.$id_batiment.', hp = '.$batiment->get_hp().', date = '.time().' WHERE id = '.$id_batiment_ville;
      if($db->query($requete))
      {
      	interf_alerte::enregistre(interf_alerte::msg_succes, 'Batiment bien réactivé.');
				journal_royaume::ecrire_perso('ameliore_ville', $batiment, '', $batiment->get_cout());
			}
    }
    else
  		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume ne possède pas assez de stars !');
  	break;
  case 'reduit':
    // On récupère le nouveau bâtiment
    $id_batiment = $_GET['id'];
    $batiment = new batiment_ville($id_batiment);
    // On récupère les HP de l'ancien bâtiment
    $requete = "SELECT hp FROM construction_ville WHERE id = ".$id_batiment_ville;
    $req = $db->query($requete);
    $row2 = $db->read_assoc($req);
    // Calcul des HP
    $hp = min($row['hp'], $row2['hp']);
    //On remplace le batiment
    $requete = 'UPDATE construction_ville SET id_batiment = '.$id_batiment.', hp = '.$batiment->get_hp().', date = '.time().' WHERE id = '.$id_batiment_ville;
    if($db->query($requete))
    {
      interf_alerte::enregistre(interf_alerte::msg_succes, 'Retour à '.$row['nom'].' effectué.');
			journal_royaume::ecrire_perso('reduit_ville', $batiment);
		}
		break;      
  case 'reparation':
    // Récupération de informations sur le bâtiment
    $id_batiment = $_GET['id'];
    $batiment = new batiment_ville($id_batiment);
    // On vérifie qu'il a assez de stars
    if($royaume->get_star() >= $batiment->get_cout())
    {
      // On répare
      $requete = 'UPDATE construction_ville SET hp = '.$row['max_hp'].', date = '.time().' WHERE id = '.$id_batiment;
      if( $db->query($requete) )
      {
        //On paye
        $royaume->set_star($royaume->get_star() - $batiment->get_cout());
        $royaume->sauver();
      	interf_alerte::enregistre(interf_alerte::msg_succes, 'Réparation effectuée.');
				journal_royaume::ecrire_perso('repare_ville', $batiment, '', $batiment->get_cout());
      }
    }
    else
  		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume ne possède pas assez de stars !');
    break;
	}
}

$cadre->set_gestion( $G_interf->creer_batiments_ville($royaume, $lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();
     
          
?>