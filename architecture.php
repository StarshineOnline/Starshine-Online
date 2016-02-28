<?php //	 -*- tab-width:	2; mode: php -*-
/**
* @file architecture.php
* Réparation des bâtiments et accélération de leur construction
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);

// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Architecture') );

if( $perso->is_buff('debuff_rvr') )
{
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'RvR impossible pendant la trêve') );
	exit;
}
//Si le personnage a assez de PA
if( $perso->get_pa() < 30 )
{
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous n\'avez pas assez de PA') );
	exit;
}

$type = $_GET['type'];
$entite = entitenj_constr::factory($type, $_GET['id']);
if( !$entite )
	security_block(URL_MANIPULATION, 'Mauvais type');
// Saboté ? 
if( $entite->get_buff_actif('sabotage') )
{
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Ce bâtiment est saboté') );
	exit;
}
//Calcul de la distance entre le joueur et le placement
if( $perso->calcule_distance($entite) )
	security_block(BAD_ENTRY, 'Construction trop loin');
$batiment = $entite->get_batiment();
$agit = false;
$continue = true;

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'construit':
	// Déjà fini ?
	if( $entite->get_fin_placement() < time() )
		security_block(BAD_ENTRY, 'Construction déjà finie !');
	if( $entite->get_debut_placement() == 0 )
		security_block(BAD_ENTRY, 'Erreur de paramètre');
	/// @todo  vérifir que c'est bien un placement
		
		
  $fin_min = $entite->get_debut_placement() + $batiment->get_temps_construction_min();
  if( $fin_min >= $entite->get_fin_placement() )
  {
		$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'La construction ne peux plus être accélérée: il reste moins de '.transform_sec_temp($batiment->get_temps_construction_min())) );
		break;
	}
	//Seconde supprimées du décompte
	$seconde_prct = floor( ($entite->get_fin_placement() - $entite->get_debut_placement()) * sqrt($perso->get_architecture()) / 100 );
	$secondes_max = min(round(15000 * sqrt($perso->get_architecture())), $seconde_prct);
	$secondes_min = min(250 * $perso->get_architecture(), round($seconde_prct / 2));
	// Gemme de fabrique : augmente de effet % l'accélération
	if( $perso->is_enchantement('forge') )
	{
		$secondes_max += floor($joueur->get_enchantement('forge', 'effet') / 100 * $secondes_max);
	}
  if( $entite->get_fin_placement() < $fin_min )
    $secondes = 0;
  else
  	$secondes = rand($secondes_min, $secondes_max);
	if( $perso->is_buff('convalescence') )
	{
		$cadre->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'La convalescence réduit l\'efficacité de l\'accélération') );
		$secondes = floor($secondes / 2);
	}
	$fin = $entite->get_fin_placement() - $secondes;
	if( $fin < $fin_min )
	{
		$fin = $fin_min;
		$secondes = $entite->get_fin_placement() - $fin_min;
		$continue = false;
	}
	
	if( $secondes > 0 )
	{
		$entite->set_fin_placement($fin);
		
		$cadre->add( new interf_alerte(interf_alerte::msg_succes, false, false, 'La construction a été accélérée de '.transform_sec_temp($secondes)) );
		$agit = true;
	}
	else
		$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'La construction ne peux plus être accélérée: il reste moins de '.transform_sec_temp($batiment->get_temps_construction_min())) );
	break;
	
case 'repare':
	// On vérifie qu'il y a quelque chose à réparer
	if( $entite->get_hp() == $batiment->get_hp() )
		security_block(BAD_ENTRY, 'Rien à réparer !');
	// on vérifie que c'est bien un bâtiment construit
	if( $type != 'construction' )
		security_block(URL_MANIPULATION, 'Mauvais type');
	
	//HP redonnés
	$hp_repare_max = ceil(1000 * (1 - 50/($perso->get_architecture()+50)));
	$hp_repare_min = $perso->get_architecture();
	$hp_repare = rand($hp_repare_min, $hp_repare_max);
	if( $perso->is_buff('convalescence') )
	{
		$cadre->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'La convalescence réduit l\'efficacité de la répération') );
		$hp_repare = floor($hp_repare/2);
	}
	// Gemme de fabrique : augmente de effet % l'accélération
	if ($perso->is_enchantement('forge'))
	{
		$hp_repare += floor($perso->get_enchantement('forge', 'effet') / 100 * $hp_repare);
	}
	$hp = $entite->get_hp() + $hp_repare;
	if($hp > $batiment->get_hp())
	{
		$hp = $batiment->get_hp();
		$hp_repare = $batiment->get_hp() - $entite->get_hp();
		$continue = false;
	}
	//On vérifie qu'il y a bien eu réparation du batiment
	if($hp_repare > 0)
	{
		$entite->set_hp($hp);
		$cadre->add( new interf_alerte(interf_alerte::msg_succes, false, false, 'La construction a été répérée de '.$hp_repare.' HP') );
		$agit = true;
	}
	else
		$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'La construction est déjà totalement réparée') );
}

if( $agit )
{
	$entite->sauver();
	//On supprime les PA du joueurs
	$perso->set_pa($perso->get_pa() - 30);
	//Augmentation de la compétence d'architecture
	$lim = $batiment->get_bonus('lim_montee');
	if( !$lim or $perso->get_architecture() < $lim )
	{
		$augmentation = augmentation_competence('architecture', $perso, 1);
		if ($augmentation[1] == 1)
		{
			$perso->set_architecture($augmentation[0]);
			$perso->recalcule_avancement();
			$perso->sauver();
		}
	}
	$interf_princ->maj_perso();
}
interf_debug::aff_enregistres($cadre);
$btns = $cadre->add( new interf_bal_cont('div', false, 'btn-group') );
if( $agit && $continue )
{
	$G_url->add('type', $type);
	$G_url->add('action', $action);
	$G_url->add('id', $_GET['id']);
	$btn = $btns->add( new interf_lien_cont($G_url->get(), false, 'btn btn-default') );
	$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-architecture') );
	$btn->add( new interf_bal_smpl('span', 'Continuer') );
}
if( interf_debug::doit_aff_bouton() )
{
	$btn = $btns->add( new interf_bal_cont('button', false, 'btn btn-default') );
	$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-debug') );
	$btn->add( new interf_bal_smpl('span', 'Débogage') );
	$btn->set_attribut('onclick', 'return debugs();');
}
$url = 'informationcase.php?case='.convert_in_pos($entite->get_x(), $entite->get_y());
$btn = $btns->add( new interf_lien_cont($url, false, 'btn btn-default') );
$btn->add( new interf_bal_smpl('span', '', false, 'icone icone-retour') );
$btn->add( new interf_bal_smpl('span', 'Revenir à la case') );

