<?php
if (file_exists('root.php'))
	include_once('root.php');

include_once(root.'inc/fp.php');

/******************************************/
/* Récupération des variables utilisateur */
/******************************************/

// L'ID de l'invitation
$W_ID = isset($_GET['id']) ? $_GET['id'] : null;
// La réponse oui ou non
$W_reponse = isset($_GET['reponse']) ? $_GET['reponse'] : null;

/******************************************/
/* Gestion de l'invitation dans un groupe */
/******************************************/

if( !is_null($W_ID) && !is_null($W_reponse) )
{
	$interf_princ = $G_interf->creer_jeu();
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Invitation', true) );
	$dlg->ajout_btn('Ok', 'fermer');
	
	$perso = new perso($_SESSION['ID']);
	$invitation = invitation::findOneById($W_ID);
	// Test existence de l'invitation
	if( !$invitation )
	{
		$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Invitation introuvable.') );
	}
	else
	{
		// Test si l'invitation concerne bien le $perso
		if( $perso->get_id() != $invitation->get_receveur() )
		{
			$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Cette invitation n\'est pas pour vous.') );
		}
		// Refus de l'invitation
		elseif ($W_reponse == 'non')
		{
			$invitation->supprimer();
			$interf_princ->maj_perso();
			$dlg->add( new interf_alerte(interf_alerte::msg_succes, false, false, 'Invitation refusée.') );
		}
		// Acceptation de l'invitation
		elseif ($W_reponse == 'oui')
		{
			$groupe = new groupe($invitation->get_groupe());
			
			// Test si le $perso n'est pas déjà dans un groupe
			if( $perso->is_groupe() )
			{
				$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes déjà dans un groupe.') );
			}
			// Test si le $perso est déprimé
			elseif( $perso->is_buff('debuff_groupe') )
			{
				$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes trop déprimé pour rejoindre un groupe. Pour le moment vous ne voulez parler à personne.') );
			}
			// Test si ya encore de la place dans le $groupe
			elseif( count($groupe->get_membre()) >= 5 )
			{
				$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Le groupe a atteint son maximum de membres.') );
			}
			else
			{
				// Ajoute le membre au groupe
				$groupe_joueur = new groupe_joueur(-1, $perso->get_id(), $groupe->get_id(), 'n');
				$perso->set_id_groupe($groupe->get_id());
				
				$invitation->supprimer();
				$groupe_joueur->sauver();
				$perso->sauver();
				$dlg->add( new interf_alerte(interf_alerte::msg_succes, false, false, 'Vous êtes maintenant membre du groupe !') );
				$interf_princ->maj_perso();
				
				// On débloque l'achievement
				$perso->unlock_achiev('rejoindre_groupe');
			}
		}
	}
}
