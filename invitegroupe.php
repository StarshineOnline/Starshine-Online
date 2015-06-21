<?php
/// @deprecated
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//On sort de la bdd l'ID du groupe du joueur
$perso = new perso($_SESSION['ID']);
$invite = new perso($_GET['id']);
//Si il n'est pas groupé, création du groupe
if(!$perso->is_groupe())
{
	if(!$perso->is_buff('debuff_groupe'))
	{
		$groupe = new groupe('', 'r', $perso->get_id(), 'groupe_'.$perso->get_id());
		$groupe->sauver();
		$perso->set_groupe($groupe->get_id());
		$perso->sauver();
		$groupe_joueur = new groupe_joueur('', $perso->get_id(), $groupe->get_id(), 'y');
		$groupe_joueur->sauver();
		echo 'Groupe créé<br />';
	}
	else
		echo 'Vous êtes trop déprimé pour créer un groupe. Pour le moment vous ne voulez parler à personne.';
}
else
{
	$groupe = new groupe($perso->get_groupe());
	$groupe_joueur = new groupe_joueur($groupe->get_id(), $perso->get_id());
}

//Regarde si le joueur est leader du groupe
if (isset($groupe_joueur) && $groupe_joueur->is_leader())
{
	//Si il y a moins de X invitations déjà, faire une invitation
	if ($groupe->get_place_libre() > 0)
	{
		//Si il est déjà groupé
		if ($invite->get_groupe() == 0)
		{
			//Vérifie qu'il n'est pas déprimé
			if($invite->is_buff('debuff_groupe', true))
			{
				echo 'Ce joueur est déprimé, il ne vous voit même pas!';
			}
			else
			{
				//Regarde si vous l'avez déjà invité
				$invitations = invitation::create(array('inviteur', 'receveur'), array($perso->get_id(), $invite->get_id()));

				if(count($invitations) == 0)
				{
					$invit = new invitation(-1, $perso->get_id(), $invite->get_id(), time(), $perso->get_groupe());
					$invit->sauver();
					if($invit->get_id() >= 0)
					{
						echo 'Invitation bien envoyée!<br />';
						
						// Augmentation du compteur de l'achievement
						$achiev = $perso->get_compteur('invitation_groupe');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
					else
						echo 'Veuillez renvoyer l\'invitation.';
				}
				else
					echo 'Vous avez déjà envoyé une invitation à cette personne.';
			}
		}
		else
			echo 'Ce joueur est déjà groupé.<br />';
	}
	else
	{
		echo 'Vous avez déjà invité '.$G_nb_joueur_groupe.' autres joueurs!<br />';
	}
}
else
{
	if(isset($groupe_joueur))
		echo 'Vous n\'êtes pas leader du groupe.';
}
?>