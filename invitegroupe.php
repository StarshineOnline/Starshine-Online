<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//L'id du joueur que l'ont veut inviter
$W_ID = $_GET['ID'];

//On sort de la bdd l'ID du groupe du joueur
$W_requete = 'SELECT * FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($W_requete);
$row = $db->read_array($req);
$joueur = new perso($_SESSION['ID']);
$perso = new perso($W_ID);
//Si il n'est pas groupé, création du groupe
if (empty($row['groupe']))
{
	if($joueur->is_debuff('Dépression'))
	{
		
	}
	
	if(!$joueur->is_debuff('Dépression'))
	{
		$groupe = new groupe('', 'r', $joueur->get_id(), 'groupe_'.$joueur->get_id());
		$groupe->sauver();
		$joueur->set_groupe($groupe->get_id());
		$joueur->sauver();
		$groupe_joueur = new groupe_joueur('', $joueur->get_id(), $groupe->get_id(), 'y');
		$groupe_joueur->sauver();
		echo 'Groupe créé<br />';
	}
	else
		echo 'Vous êtes trop déprimé pour créer un groupe, pour le moment vous ne voulez parler à personne.';
}
else
{
	$groupe = new groupe($joueur->get_groupe());
	$groupe_joueur = new groupe_joueur($groupe->get_id(), $joueur->get_id());
}

//Regarde si le joueur est leader du groupe
if (isset($groupe_joueur) && $groupe_joueur->is_leader())
{
	//Si il y a moins de 4 invitations déjà, faire une invitation
	if ($groupe->get_place_libre() > 0)
	{
		//Si il y est déjà groupé
		if ($perso->get_groupe() == 0)
		{
			//Regarde si vous l'avez déjà invité
			$W_requete = 'SELECT COUNT(inviteur), groupe FROM invitation WHERE inviteur = '.$joueur->get_id().' AND receveur = '.$perso->get_id().' GROUP BY groupe';
			$W_query = $db->query($W_requete);
			$W_row = $db->read_array($W_query);
			if ($W_row['COUNT(inviteur)'] == 0)
			{
				$W_requete = "INSERT INTO `invitation` ( `ID` , `inviteur` , `receveur` , `time`, `groupe` ) VALUES ('', '".$_SESSION['ID']."', '".$W_ID."', '".time()."','".$W_ID_groupe."')";
				if ($db->query($W_requete))
				{
					echo 'Invitation bien envoyée!<br />';
				}
				else
				{
					echo 'Veuillez renvoyer l\'invitation.';
				}
			}
			else
			{
				echo 'Vous avez déjà envoyé une invitation à cette personne.';
			}
		}
		else
		{
			echo 'Ce joueur est déjà groupé.<br />';
		}
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