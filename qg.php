<?php // -*- tab-width:2; mode: php -*- 
/**
* @file qg.php
* Quartier général
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

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

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
switch($action)
{
case 'prendre':
  if ($perso->is_buff('debuff_rvr'))
  {
  	interf_alerte::enregistre(interf_alerte::msg_erreur, 'RvR impossible pendant la trêve.');
    break;
  }
  foreach( $_GET as $cle=>$nombre )
  {
	  if( substr($cle, 0, 3) == 'nbr' && $nombre > 0)
	  {
	  	$id_objet = substr($cle, 3);
	    $i = 0;
	    if ($nombre > ($G_place_inventaire - count($perso->get_inventaire_slot_partie())))
	    {
	      $reste = $G_place_inventaire - count($perso->get_inventaire_slot_partie()) ;
	      if($reste != 0)
	      {
	  			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il ne vous reste que '.$reste.' places dans votre inventaire.');
	      }
	      else
	      {
	  			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Plus de place dans votre inventaire.');
	      }
	    }
	    else
	    {
	      while($i < $nombre)
	      {
	      	/// TODO: passer par des objets
	        $requete = "SELECT objet_royaume.nom, depot_royaume.*, depot_royaume.id AS id_depot FROM depot_royaume, grade, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_royaume = ".$R->get_id()." AND id_objet = '".sSQL($id_objet)."' AND objet_royaume.grade <= grade.rang  AND grade.id = ".$perso->get_rang_royaume();
	        $req = $db->query($requete);
	        $row = $db->read_array($req);
	        if($db->num_rows > 0)
	        {
	          if($perso->prend_objet('r'.$_GET['id_objet']))
	          {
	          	$requete2 = "DELETE FROM depot_royaume WHERE id = ".$row['id_depot'];
	            $db->query($requete2);
	          }
	          else
	          {
	  					interf_alerte::enregistre(interf_alerte::msg_erreur, $G_erreur);
	          }
	        }
	        $i++;
	      }
	      $perso->sauver();
	      $nom = $row['nom'] ;
	      if($nombre > 1)
				{
					/// TODO: à améliorer 
	          $tab = array("Drapeau"=>"Drapeaux","Poste avancé"=>"Postes avancés", "Fortin"=>"Fortins", "Fort"=>"Forts", "Forteresse"=>"Forteresses", "Tour de guet"=>"Tours de guet", "Tour de garde"=>"Tours de garde", "Tour de mages"=>"Tours de mages", "Tour d archers"=>"Tours d'archers", "Bourgade"=>"Bourgades", "Palissade"=>"Palissades", "Mur"=>"Murs", "Muraille"=>"Murailles", "Grande muraille"=>"Grandes murailles", "Bélier"=>"Béliers", "Catapulte"=>"Catapultes", "Trébuchet"=>"Trébuchets", "Baliste"=>"Balistes", "Grand drapeau"=>"Grands drapeaux", "Étendard"=>"Étendards", "Grand étendard"=>"Grands étendards", "Petit drapeau"=>"Petits drapeaux") ; 
	          if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
	          {
	  					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$row['nom']].' bien prises au dépôt du royaume.');
	          }
	          else
	          {
	  					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$row['nom']].' bien pris au dépôt du royaume.');
	          }
	                                           
	      }
	      else
	      {
	          if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
	          {
	  					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$row['nom'].' bien prise au dépôt du royaume.');
	          }
	          else
	          {
	  					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$row['nom'].' bien pris au dépôt du royaume.');
	          }
	      }
	    }
	  }
	}
  break;
}
$interf_princ->set_gauche( $G_interf->creer_qg($R) );
	
	
	

?>