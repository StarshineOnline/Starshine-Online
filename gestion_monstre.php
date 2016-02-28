<?php // -*- php -*-
/**
*	@file gestion_monstre.php
* Gestion des crétures dressées et dressables
*/

if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


//Récupération des informations du personnage
$perso = joueur::get_perso();
$perso->check_perso();

$interf_princ = $G_interf->creer_jeu();
$lance = null;

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'principale':
	$perso->set_pet_principale($_GET['id']);
	break;
case 'supprimer':
	$pet = new pet($_GET['id']);
	$pet->supprimer();
	//Si c'est la créature principale que l'on supprime
	if($pet->get_principale() == 1)
	{
		$pets = $perso->get_pets();
		$perso->set_pet_principale($pets[0]->get_id());
	}
	break;
case 'annuler':
	$buff = $perso->get_buff('dressage');
	$buff->supprimer();
	$perso->get_buff();
	$interf_princ->maj_perso();
	break;
case 'soin':
	$pet = new pet($_GET['id']);
	$pet->get_monstre();
	//Si on a assez de PV
	/// @todo centraliser cout
	$cout = ceil($perso->get_hp_max() / 10);
	if($perso->get_hp() <= $cout )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de HP.');
		break;
	}
	if($perso->get_pa() < 1)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA.');
		break;
	}
	if($pet->get_hp() >= $pet->monstre->get_hp() && $pet->get_mp() >= $pet->get_mp_max() )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre créature a toute sa vie et sa mana.');
		break;
	}
	if($pet->get_hp() <= 0)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre créature est morte.');
		break;
	}
	$perso->set_hp($perso->get_hp() - ceil($perso->get_hp_max() / 10));
	$perso->set_pa($perso->get_pa() - 1);
	$perso->sauver();
	$des = de_soin(0, $perso->soin_pet());
	$msg = '';
	foreach($des as $de)
	{
		$rnd = rand(1, $de);
		$heal += $rnd;
		if ($msg == '') $msg = 'Soin: jet de ';
		else $msg .= ' + ';
		$msg .= "d${de}($rnd)";
	}
	interf_debug::enregistre($msg);
	//Heal MP
	$heal_mp = floor($heal / 3);
	if($heal > ($pet->monstre->get_hp() - $pet->get_hp())) $heal = $pet->monstre->get_hp() - $pet->get_hp();
	if($heal_mp > ($pet->get_mp_max() - $pet->get_mp())) $heal_mp = $pet->get_mp_max() - $pet->get_mp();
	$pet->set_hp($pet->get_hp() + $heal);
	$pet->set_mp($pet->get_mp() + $heal_mp);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous soignez '.$pet->get_nom().' de '.$heal.' HP et '.$heal_mp.' MP.');
	$augmentation = augmentation_competence('dressage', $perso, 5);
	if ($augmentation[1] == 1)
	{
		$perso->set_dressage($augmentation[0]);
		$perso->recalcule_avancement();
	}
	$perso->sauver();
	$pet->sauver();
	$interf_princ->maj_perso();
	break;
case 'modifier':
	$nom = $_GET['nom'];
	if( !$nom)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Nom de la créature incorrect.');
		break;
	}
	$pet = new pet($_GET['id']);
	$pet->set_nom($nom);
	$pet->sauver();
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Nom de la créature modifié avec succès.');
	break;
case 'infos':
	$pet = new pet($_GET['id']);
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite( $pet->get_nom() ) );
	$cadre->add( $G_interf->creer_monstre($pet, false) );
	$interf_princ->maj_tooltips();
	exit;
case 'lancer_groupe':
	$groupe = true;
	// Vérification que le joueur a le droit aux sorts de groupe
	if( $_GET['buff'][0] == 'S' )
  {
  	///TODO : à refaire ?
  	security_block(URL_MANIPULATION, 'Sort de groupe non autorisé');
	}
case 'lancer':
	$buff = $_GET['buff'];
	if( $buff[0] == 's' || $buff[0] == 'S' )
		$sortcomp = sort_jeu::factory( substr($buff, 1) );
	else if( $buff[0] == 'c' )
		$sortcomp = comp_jeu::factory( substr($buff, 1) );
	// On vérifie que c'est connu par la créature
	$pet = new pet($_GET['id']);
	$monstre = $pet->get_def();
	$buffs = explode(';', $monstre->get_sort_dressage());
	///@todo loguer ça ?
	if( !in_array($buff, $buffs) )
  	break;
	// On vérifie que l'on peut lancer un sort / une compétence
	/// @todo centraliser ça
	if($perso->is_buff('petrifie'))
  {
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes pétrifié, vous ne pouvez pas rien faire.');
		break;
  }
	if( ($buff[0] == 's' || $buff[0] == 'S') && $perso->is_buff('bloque_sort'))
  {
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes sous vunérabilité, vous ne pouvez plus lancer de sorts hors combat.');
		break;
  }
  if($perso->get_hp() <= 0)
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes mort !');
		break;
	}
  if($pet->get_hp() <= 0)
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre créature est morte !');
		break;
	}
	$cout_mp = $sortcomp->get_mp();
	$cout_pa = $sortcomp->get_pa();
	if($perso->is_buff('buff_concentration', true))
		$cout_mp = ceil($cout_mp * (1 - ($perso->get_buff('buff_concentration','effet') / 100)));
	if( $groupe )
		$cout_mp = round($cout_mp * 1.5);
  if($perso->get_pa() < $cout_pa)
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Pas assez de PA !');
		break;
	}
  if($pet->get_mp() < $cout_mp)
	{
    interf_alerte::enregistre(interf_alerte::msg_erreur, 'Pas assez de mana !');
		break;
	}
	// On lance
	$lance = new interf_alerte(interf_alerte::msg_info);
	interf_base::set_courrent($lance);
	/// BUG: clone nécessaire pour ne pas faire buguer si lancé en groupe
	if( $sortcomp->lance($perso, clone $perso, $groupe, '', 'perso') )
	{
    $perso->set_pa($perso->get_pa() - $cout_pa);
    $pet->set_mp($pet->get_mp() - $cout_mp);
    $perso->sauver();
    $pet->sauver();
		$interf_princ->maj_perso();
	}
	break;
}

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Créatures '.$perso->nb_pet().'/'.$perso->get_max_pet()) );
interf_alerte::aff_enregistres($cadre);
if($lance)
	$cadre->add( $lance );
interf_debug::aff_enregistres($cadre);
/// @todo remettre bouton debug
	
$cadre->add( $G_interf->creer_dressage($perso, !$perso->est_mort()) );
$interf_princ->maj_tooltips();


?>
