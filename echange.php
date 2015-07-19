<?php
/**
 * @file echange.php
 * Gestion des échanges 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

$perso = joueur::get_perso();
$perso->restack_objet();

$interf_princ = $G_interf->creer_jeu();

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'creer':
	$interf_princ->verif_mort($perso);
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Effectuer un échange') );
	$cadre->add( $G_interf->creer_echange(0, $_GET['perso']) );
	exit;
case 'voir':
	$echange = recup_echange(sSQL($_GET['id'], SSQL_INTEGER));
	if( $echange['nouveau'] )
	{
		switch( $echange['statut'] )
		{
		case 'creation':
		case 'finalisation':
			$lu = $echange['id_j1'] == $perso->get_id();
			break;
		case 'proposition':
			$lu = $echange['id_j2'] == $perso->get_id();
			break;
		default:
			$lu = false;
		}
		if($lu)
		{
			$requete = 'UPDATE echange SET nouveau = FALSE WHERE id_echange = "'.$echange['id_echange'].'"';
			$db->query($requete);
		}
	}
	$interf_princ->verif_mort($perso);
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Effectuer un échange') );
	$cadre->add( $G_interf->creer_echange($_GET['id'], true) );
	$interf_princ->maj_perso(true);
	exit;
case 'modifier':
	//Si on commence un nouvel échange
	if( !$_GET['id'] )
	{
		$receveur = new perso($_GET['perso']);
		//On créé l'échange
		/// @todo passer à l'objet
		$requete = "INSERT INTO echange(id_j1, id_j2, statut, date_debut, date_fin, message_j1, message_j2) VALUES(".$perso->get_id().", ".$receveur->get_id().", 'creation', ".time().", ".(time() + 100000).", '', '')";
		$db->query($requete);
		$echange = recup_echange($db->last_insert_id());
	}
	else
		$echange = recup_echange(sSQL($_GET['id'], SSQL_INTEGER));
	//Vérification si le joueur fait parti du donneur ou receveur
	if($perso->get_id() != $echange['id_j1'] && $perso->get_id() != $echange['id_j2'])
		security_block(URL_MANIPULATION, 'Vous ne faîtes pas parti de cet échange');
	else
	{
		switch( $echange['statut'] )
		{
		case 'creation':
		case 'finalisation':
			if( $echange['id_j1'] != $perso->get_id() )
				security_block(URL_MANIPULATION, 'Ce n\'est pas à vous de faire une proposition');
			break;
		case 'proposition':
			if( $echange['id_j2'] != $perso->get_id() )
				security_block(URL_MANIPULATION, 'Ce n\'est pas à vous d\'agir');
			break;
		}
	}
	//Ajout des stars dans la bdd
	echange_objet_ajout(sSQL($_GET['stars'], SSQL_INTEGER), 'star', $echange['id_echange'], $perso->get_id());
	//Ajout des objets à l'échange en cours
	for($i=0; $i<count($_SESSION['objets']); $i++)
	{
		$cle = 'nbr_'.$i;
		if( array_key_exists($cle, $_GET) )
		{
			$obj = $_SESSION['objets'][$i];
			if( $_GET[$cle] > 1 )
				$obj .= 'x'.$_GET[$cle];
			echange_objet_ajout($obj, 'objet', $echange['id_echange'], $perso->get_id());
		}
	}
	if( !array_key_exists('bouton', $_GET) || $_GET['bouton'] == 'Valider' )
	{
		//On passe l'échange en mode proposition
		/// @todo passer à l'objet
		switch( $echange['statut'] )
		{
		case 'creation':
			$nouv_statut = 'proposition';
			break;
		case 'proposition':
			$nouv_statut = 'finalisation';
			break;
		default:
			/// @todo loguer l'erreur
			$nouv_statut = false;
		}
		if( $nouv_statut )
		{
			$requete = 'UPDATE echange SET statut = "'.$nouv_statut.'", nouveau = TRUE WHERE id_echange = "'.$echange['id_echange'].'"';
			$db->query($requete);
			interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre proposition a bien été envoyée.');
		}
	}
	break;
case 'annuler':
	/// @todo passer à l'objet
	$requete = "UPDATE echange SET statut = 'annule' WHERE id_echange = ".sSQL($_GET['id']);
	$db->query($requete);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'L\'échange a été supprimé.');
	break;
case 'valider':
	$echange = recup_echange(sSQL($_GET['id'], SSQL_INTEGER));
	$receveur = new perso($echange['id_j2']);
	//Finalisation de l'échange donc vérifications
	if( $echange['statut'] != 'finalisation' )
		security_block(URL_MANIPULATION, 'Cet échange n\'en est pas encore au stade de la finlisation.');
	if( $echange['id_j1'] != $perso->get_id() )
		security_block(URL_MANIPULATION, 'Ce n\'est pas à vous de finir cet échange.');
	//Les joueurs doivent être a moins de deux cases l'un de l'autre
	if( $perso->calcule_distance($receveur) > 1)
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes trop loin pour finaliser cet échange.');
		break;
	}
	//Vérification que les joueurs ont bien les objets dans leur inventaire
	if( !verif_echange($echange['id_echange'], $perso->get_id(), $receveur->get_id()) )
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il manque un ou plusieurs objets a un joueur pour finaliser l\'échange.');
	//On compte les objets de chaque personne
	$nb_objet['j1'] = 0;
	$nb_objet['j2'] = 0;
	$encombr1 = 0;
	$encombr2 = 0;
	my_dump($echange);
	foreach($echange['objet'] as $objet)
	{
		$obj = objet_invent::factory($objet['objet']);
		if($perso->get_id() == $objet['id_j'])
		{
			$nb_objet['j1']++;
			$encombr1 += $obj->get_encombrement();
		}
		else
		{
			$nb_objet['j2']++;
			$encombr2 += $obj->get_encombrement();
		}
	}
	
	//Vérification qu'ils ont bien assez de place
	if($perso->get_max_encombrement() - $perso->get_encombrement() < ($encombr2 - $encombr1))
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, $perso->get_nom().' n\'a pas assez de place dans son inventaire.');
		break;
	}
	if($perso->get_max_encombrement() - $receveur->get_encombrement() < ($encombr1 - $encombr2))
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, $receveur->get_nom().' n\'a pas assez de place dans son inventaire.');
		break;
	}
	//On transfère tous les objets
	foreach($echange['objet'] as $objet)
	{
		$o = explode('x', $objet['objet']);
		$n = count($o) > 1 ? intval($o[1]) : 1;
		if($perso->get_id() == $objet['id_j'])
		{
			$perso->supprime_objet($o[0], $n);
			$receveur->prend_objet($objet['objet']);
		}
		else
		{
			$receveur->supprime_objet($o[0], $n);
			$perso->prend_objet($objet['objet']);
		}
	}
	//On échange les stars
	/// @todo à améliorer
	$diff_stars = intval($echange['star'][$perso->get_id()]['objet']) - intval($echange['star'][$receveur->get_id()]['objet']);
	$perso->add_star( -$diff_stars );
	$receveur->add_star( $diff_stars );
	$perso->sauver();
	$receveur->sauver();
	//On met a jour le statut de l'échange
	//On passe l'échange en mode fini
	$requete = "UPDATE echange SET statut = 'fini' WHERE id_echange = '".$echange['id_echange']."'";
	$db->query($requete);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'L\'échange s\'est déroulé avec succès.');
	
	// Augmentation du compteur de l'achievement
	$achiev = $perso->get_compteur('objets_echanges');
	$achiev->set_compteur($achiev->get_compteur() + $nb_objet['j1']);
	$achiev->sauver();
	
	// Augmentation du compteur de l'achievement
	$achiev = $receveur->get_compteur('objets_echanges');
	$achiev->set_compteur($achiev->get_compteur() + $nb_objet['j2']);
	$achiev->sauver();
	
	if($nb_objet['j2'] == 0 AND $echange['star'][$perso->get_id()]['objet'] > 0)
	{
		// Augmentation du compteur de l'achievement
		$achiev = $perso->get_compteur('donner_stars');
		$achiev->set_compteur($achiev->get_compteur() + $echange['star'][$perso->get_id()]['objet']);
		$achiev->sauver();
	}
	if($nb_objet['j1'] == 0 AND $echange['star'][$receveur->get_id()]['objet'] > 0)
	{
		// Augmentation du compteur de l'achievement
		$achiev = $receveur->get_compteur('donner_stars');
		$achiev->set_compteur($achiev->get_compteur() + $echange['star'][$receveur->get_id()]['objet']);
		$achiev->sauver();
	}
	$interf_princ->maj_perso();
}
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Liste de vos échanges') );
interf_alerte::aff_enregistres($cadre);
$cadre->add( $G_interf->creer_echanges($perso, !$perso->est_mort()) );
