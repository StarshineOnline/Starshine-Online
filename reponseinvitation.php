<?php

if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();


//L'ID de l'invitation
$W_ID = sSQL($_GET['id']);
//L'id du groupe
$W_groupe = sSQL($_GET['groupe']);
//La réponse oui ou non
$W_reponse = $_GET['reponse'];

$joueur = new perso($_SESSION['ID']);
$invitation = new invitation($W_ID);
if ($W_reponse == 'non')
	$invitation->supprimer();
elseif ($W_reponse == 'oui')
{
	$groupe = new groupe($W_groupe);

	//Vérifie avant si l'utilisateur n'a pas déjà de groupe (problème rencontré si la personne clic très rapidement sur le lien)
	if ($joueur->get_groupe() > 0)
		 $interf_princ->set_dialogue( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes déjà groupé.');
	elseif(count($groupe->get_membre()) >= 5)
		$interf_princ->set_dialogue( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Le groupe a atteint son maximum de membres.');
	else
	{
		if(!$joueur->is_buff('debuff_groupe'))
		{
			//Ajoute le membre au groupe
			$groupe_joueur = new groupe_joueur(-1, $joueur->get_id(), $groupe->get_id(), 'n');
			//echo $groupe_joueur;
			$joueur->set_groupe($groupe->get_id());
			
			$invitation->supprimer();
			$groupe_joueur->sauver();
			$joueur->sauver();
			$interf_princ->set_dialogue( new interf_alerte(interf_alerte::msg_succes, false, false, 'Vous êtes maintenant membre du groupe !');
			$interf_princ->maj_perso();
			
			// On debloque l'achievement
			$joueur->unlock_achiev('rejoindre_groupe');
		}
		else
			$interf_princ->set_dialogue( new interf_alerte(interf_alerte::msg_erreur, false, false, "Vous êtes trop déprimé pour rejoindre un groupe. Pour le moment vous ne voulez parler à personne.");
	}
}
?>