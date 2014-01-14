<?php //	 -*- tab-width:	2; mode: php -*-
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
  $interf->creer_infos_objet($_GET['id']);
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
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_equip($perso, $_GET['page'], !$visu) );
  exit;
case 'sac':
  $princ = new interf_princ_cont();
  $princ->add( $interf->creer_invent_sac($perso, $_GET['slot'], !$visu) );
  exit;
case 'hotel_vente':
  $princ = $interf->creer_vente_hotel($perso, $_GET['objet']);
  exit;
case 'gemme':
  $princ = $interf->creer_enchasser($perso, $_GET['objet']);
  exit;
}

//Filtres
$page = array_key_exists('page', $_GET) ? $_GET['page'] : 'perso';
$slot = array_key_exists('slot', $_GET) ? $_GET['slot'] : 'utile';

$princ = $interf->creer_princ_droit('Inventaire du Personnage');
//Switch des actions
if( !$visu && $action )
{
  $obj = $perso->get_inventaire_slot_partie($_GET['objet']);
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
      echo 'objet à équiper : '.$_GET['objet'].' -> '.$obj.'<br>';
			if($perso->equip_objet($obj))
			{
				//On supprime l'objet de l'inventaire
				$perso->supprime_objet($obj, 1);
				$perso->sauver();
			}
			else
				$princ->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible d\'équiper cet objet.');
      break;
	  case 'desequip' :
			if(!$perso->desequip($_GET['objet']))
        $princ->add( new interf_alerte('danger') )->add_message($G_erreur?$G_erreur:'Impossible de deséquiper cet objet.');
      break;
	  case 'utilise' :
      $objet = objet_invent::factory( $obj );
      $objet->utiliser($perso, $princ);
      break;
	  case 'depot' :
      $objet = objet_invent::factory( $obj );
      $objet->deposer($perso, $princ);
      break;
	  case 'slot_1' :
	  case 'slot_2' :
	  case 'slot_3' :
      $objet = objet_invent::factory( $obj );
      if( $objet->mettre_slot($perso, $princ, $action[5]) )
      {
        $perso->set_inventaire_slot_partie($objet->get_texte(), $_GET['objet']);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->sauver();
      }
      break;
		case 'vente_hotel':
      $objet = objet_invent::factory( $obj );
      $objet->vendre_hdv($perso, $princ, $_GET['prix']);
		  break;
		case 'vente':
      $objet = objet_invent::factory( $obj );
      $objet->vendre_marchand($perso, $princ);
		  break;
		case 'enchasse':
      $objet = objet_invent::factory( $obj );
      $gemme = objet_invent::factory( $perso->get_inventaire_slot_partie($_GET['gemme']) );
      if( $objet->enchasser($perso, $princ, $gemme) )
      {
        $perso->set_inventaire_slot_partie($objet->get_texte(), $_GET['objet']);
  		  $perso->set_inventaire_slot( serialize($perso->get_inventaire_slot_partie(false, true)) );
        $perso->sauver();
      }
		  break;
		case 'recup_gemme':
      $objet = objet_invent::factory( $obj );
      $objet->recup_gemme($perso, $princ);
		  break;
	}
	refresh_perso();
}

$princ->add( $interf->creer_inventaire($perso, $page, $slot, !$visu) );

// Augmentation du compteur de l'achievement
$achiev = $perso->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
