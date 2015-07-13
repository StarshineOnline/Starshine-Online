<?php //	 -*- tab-width:	2; mode: php -*-
/**
 * @ inventaire.php
 * Gestion de l'inventaire 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

// Infos sur un objet
if( $action == 'infos' )
{
  $G_interf->creer_infos_objet($_GET['id']);
  exit;
}

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
    $perso = new perso($_GET['id_perso']);
	}
	else exit();
}
else
{
  $perso = joueur::get_perso();
	$visu = $perso->est_mort();
}

switch( $action )
{
case 'princ':
  $cadre = new interf_princ_ob();
  $cadre->add( $G_interf->creer_invent_equip($perso, $_GET['page'], !$visu) );
  exit;
case 'sac':
  $cadre = new interf_princ_ob();
  $cadre->add( $G_interf->creer_invent_sac($perso, $_GET['slot'], !$visu) );
  exit;
case 'hotel_vente':
	$interf_princ = $G_interf->creer_jeu();
  $interf_princ->set_dialogue( $G_interf->creer_vente_hotel($perso, $_GET['objet'], $_GET['nombre']) );
  exit;
case 'gemme':
	$interf_princ = $G_interf->creer_jeu();
  $interf_princ->set_dialogue( $G_interf->creer_enchasser($perso, $_GET['objet']) );
  exit;
}
$interf_princ = $G_interf->creer_jeu();

//Filtres
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 'perso';
$slot = array_key_exists('slot', $_GET) ? $_GET['slot'] : 'utile';

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Inventaire du Personnage') );
//Switch des actions
if( !$visu && $action )
{
  if( array_key_exists('objet', $_GET) )
    $obj = $_GET['objet'];// = $perso->get_inventaire_slot_partie($_GET['objet']);
	switch($action)
	{
    /// TODO : faire plus de vérifications
    case 'grand_accessoire':
    case 'tete':
    case 'cou':
    case 'main_droite':
    case 'torse':
    case 'main_gauche':
    case 'main':
    case 'ceinture':
    case 'doigt':
    case 'moyen_accessoire':
    case 'jambe':
    case 'dos':
    case 'petit_accessoire_1':
    case 'chaussure':
    case 'petit_accessoire_2':
			if( $perso->equip_objet($obj, false, $action) )
			{
				//On supprime l'objet de l'inventaire
				$perso->supprime_objet($obj, 1);
				$perso->sauver();
			}
			else
				$cadre->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible d\'équiper cet objet.');
      break;
	  case 'desequip':
			if(!$perso->desequip($_GET['zone'], $page=='pet'))
        $cadre->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible de deséquiper cet objet.');
      break;
	  case 'utiliser':
      $objet = objet_invent::factory( $obj );
      $objet->utiliser($perso, $cadre);
      interf_alerte::aff_enregistres($cadre);
      break;
	  case 'depot':
      $objet = objet_invent::factory( $obj );
      $objet->deposer($perso, $cadre);
			journal_royaume::ecrire_perso('pose_depot', null, $objet->get_nom(), 1);
      break;
	  case 'slot_1':
	  case 'slot_2':
	  case 'slot_3':
      $objet = objet_invent::factory( $obj );
      if( $objet->mettre_slot($perso, $cadre, $action[5]) )
      {
      	$ind = $perso->recherche_objet($obj);
        $perso->set_inventaire_slot_partie($objet->get_texte(), $ind[1]);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->sauver();
      }
      break;
		case 'vente_hotel':
      $objet = objet_invent::factory( $obj );
      $nombre = array_key_exists('nombre', $_GET) ? $_GET['nombre'] : 1;
      for($i=0; $i<$nombre; $i++)
      {
      	$objet->vendre_hdv($perso, $cadre, $_GET['prix']);
			}
		  break;
		case 'vente':
      $objets = explode('-', $_GET['objets']);
      $stars = 0;
      foreach($objets as $objet)
      {
        $obj = explode('x', $objet);
        $objet_rech = $perso->recherche_objet($objet);
        if( !$objet )
        	log_admin::log('erreur', 'Objet non trouvé dans l\'inventaire : '.$obj[0]);
        $objet = objet_invent::factory( $perso->get_inventaire_slot_partie($objet_rech[1]) );
        if( $objet_rech[0] >= $obj[1] )
          $stars += $objet->vendre_marchand($perso, $cadre, $obj[1]);
        else
          $cadre->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas assez d\'exemplaires de '.$objet->get_nom().' !');
        
      }
      if( $stars )
        $cadre->add( new interf_alerte('success') )->add_message('Objet(s) vendu(s) pour '.$stars.' stars.');
		  break;
		case 'enchasse':
			///@todo vérifier présence objet et loguer triche
      $objet = objet_invent::factory( $obj );
      $gemme = objet_invent::factory( $_GET['gemme'] );
      if( $objet->enchasser($perso, $cadre, $gemme) )
      {
      	$ind = $perso->recherche_objet($obj);
        $perso->set_inventaire_slot_partie($objet->get_texte(), $ind[1]);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->supprime_objet($gemme->get_texte(), 1);
      }
      $perso->sauver();
		  break;
		case 'recup_gemme':
      $objet = objet_invent::factory( $obj );
      $objet->recup_gemme($perso, $cadre);
		  break;
		case 'identifier':
      $objet = objet_invent::factory( $perso->get_inventaire_slot_partie($obj) );
      $objet->identifier($perso, $cadre, $_GET['objet']);
		  break;
	}
	//refresh_perso();
	$interf_princ->maj_perso();
}

$cadre->add( $G_interf->creer_inventaire($perso, $page, $slot, !$visu) );

// Augmentation du compteur de l'achievement
$achiev = $perso->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
