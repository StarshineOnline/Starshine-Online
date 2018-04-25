<?php
/**
* @file infogroupe.php
* Informations du groupe et accès au bataille pour le groupe
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();

$perso = joueur::get_perso();

// Cadre de la partie droite
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
$action = array_key_exists('action', $_GET) ? $_GET['action'] : 'infos';
$groupe = new groupe( $perso->get_id_groupe() );

switch( $action )
{
case 'quitter':
	degroup($perso->get_id(), $groupe->get_id());
	$interf_princ->recharger_interface();
	exit;
case 'expulser':
	$autre = new perso($_GET['id']);
	/// @todo loguer triche
	if( $autre->get_id_groupe() == $groupe->get_id() && $perso->get_id() == $groupe->get_id_leader() )
	{
		// On debloque l'achievement
		$autre->unlock_achiev('etre_expulse');
		$perso->unlock_achiev('expulser');
		degroup($_GET['id'], $groupe->get_id());
	}
	$interf_princ->maj_perso();
	exit;
case 'invite':
	$invite = new perso($_GET['id']);
	//Si il n'est pas groupé, création du groupe
	if( !$perso->is_groupe() )
	{
		if( !$perso->is_buff('debuff_groupe') )
		{
			$groupe = new groupe('', 'r', $perso->get_id(), 'groupe_'.$perso->get_id());
			$groupe->sauver();
			$perso->set_id_groupe($groupe->get_id());
			$perso->sauver();
			$groupe_joueur = new groupe_joueur('', $perso->get_id(), $groupe->get_id(), 'y');
			$groupe_joueur->sauver();
			interf_alerte::enregistre(interf_alerte::msg_succes, 'Groupe créé.');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous êtes trop déprimé pour créer un groupe. Pour le moment vous ne voulez parler à personne.');
	}
	else
	{
		$groupe = new groupe($perso->get_id_groupe());
		$groupe_joueur = new groupe_joueur($groupe->get_id(), $perso->get_id());
	}
	// Regarde si le personnage est le chef du groupe
	if( isset($groupe_joueur) && $groupe_joueur->is_leader() )
	{
		//Si il y a moins de X invitations déjà, faire une invitation
		if( $groupe->get_place_libre() > 0 )
		{
			//Si il est déjà groupé
			if( $invite->get_id_groupe() == 0 )
			{
				//Vérifie qu'il n'est pas déprimé
				if($invite->is_buff('debuff_groupe', true))
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Ce joueur est déprimé, il ne vous voit même pas !');
				else
				{
					//Regarde si vous l'avez déjà invité
					$invitations = invitation::create(array('inviteur', 'receveur'), array($perso->get_id(), $invite->get_id()));
	
					if(count($invitations) == 0)
					{
						$invit = new invitation(-1, $perso->get_id(), $invite->get_id(), time(), $perso->get_id_groupe());
						$invit->sauver();
						if($invit->get_id() >= 0)
						{
							interf_alerte::enregistre(interf_alerte::msg_erreur, 'Invitation bien envoyée !');
							
							// Augmentation du compteur de l'achievement
							$achiev = $perso->get_compteur('invitation_groupe');
							$achiev->set_compteur($achiev->get_compteur() + 1);
							$achiev->sauver();
						}
						else
							interf_alerte::enregistre(interf_alerte::msg_erreur, 'Veuillez renvoyer l\'invitation.');
					}
					else
						interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous avez déjà envoyé une invitation à cette personne.');
				}
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Ce joueur est déjà groupé.');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous avez déjà invité '.$G_nb_joueur_groupe.' autres joueurs !');
	}
	else
	{
		if( isset($groupe_joueur) )
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'êtes pas chef du groupe.');
	}
	break;
}

if( $ajax == 2 )
{
	switch($action)
	{
	case 'infos':
		$interf_princ->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
		exit;
	case 'batailles':
		$interf_princ->add( $G_interf->creer_batailles('batailles_groupe', $groupe) );
		exit;
	}
}
else if( $action == 'carte' )
{
	$bataille = new bataille(sSQL($_GET['bataille']));
	$interf_princ->set_dialogue( $G_interf->creer_carte_bataille($bataille) );
	if($ajax == 1)
	{
		$interf_princ->maj_tooltips();
		exit;
	}
	$action = 'batailles';
}

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Groupe') );
$onglets = $cadre->add( new interf_onglets('onglets_groupe', 'groupe') );
interf_alerte::aff_enregistres($cadre);

$onglets->add_onglet('Infos groupe', 'infogroupe.php?action=infos&ajax=2', 'ongl_infos', false, $action=='infos' || $action == 'modifier_infos' || $action == 'suppr_invit');
$onglets->add_onglet('Batailles', 'infogroupe.php?action=batailles&ajax=2', 'ongl_batailles', 'invent', $action=='batailles' || $action == 'accepter');

switch($action)
{
case 'modifier_infos':
	$groupe->set_nom(sSQL($_GET['nom']));
	$groupe->set_partage(sSQL($_GET['partage']));
	$groupe->sauver();
	/// @todo passer à l'objet
	$requete = "UPDATE groupe_joueur SET leader = 'n' WHERE id_groupe = ".sSQL($_GET['id']);
	$db->query($requete);
	$requete = "UPDATE groupe_joueur SET leader = 'y' WHERE id_joueur = ".sSQL($_GET['chef']);
	$db->query($requete);
case 'infos':
	$onglets->get_onglet('ongl_infos')->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
	break;
case 'suppr_invit':
	/// @todo passer à l'objet
	$requete = "DELETE FROM invitation WHERE ID = ".sSQL($_GET['id']);
	$db->query($requete);
	$onglets->get_onglet('ongl_infos')->add( $G_interf->creer_groupe('infos_groupe', $groupe) );
	break;
case 'accepter':
	$repere = new bataille_groupe_repere($_GET['id']);
	$repere->accepte();
	// Augmentation du compteur de l'achievement
	foreach($groupe->get_membre_joueur() as $membre)
	{
		$achiev = $membre->get_compteur('bataille');
		$achiev->set_compteur($achiev->get_compteur() + 1);
		$achiev->sauver();
	}
case 'batailles':
	$onglets->get_onglet('ongl_batailles')->add( $G_interf->creer_batailles('batailles_groupe', $groupe) );
	break;
}

$interf_princ->maj_tooltips();

