<?php
include('inc/fp.php');
//L'id du joueur que l'ont veut inviter
$W_ID = $_GET['ID'];

//On sort de la bdd l'ID du groupe du joueur
$W_requete = 'SELECT * FROM perso WHERE ID = \''.$_SESSION['ID'].'\'';
$req = $db->query($W_requete);
$row = $db->read_array($req);

//Si il n'est pas group�, cr�ation du groupe
if ($row['groupe'] == 0)
{
	$W_requete = "INSERT INTO `groupe` VALUES ('', 'r', ".$_SESSION['ID'].")";
	if($db->query($W_requete))
	{
		$W_ID_groupe = $db->last_insert_id();
		$W_requete = 'UPDATE perso SET groupe = '.$W_ID_groupe.' WHERE ID = '.$_SESSION['ID'];
		$W_req = $db->query($W_requete);
		$W_requete = "INSERT INTO groupe_joueur VALUES('', ".$_SESSION['ID'].", ".$W_ID_groupe.", 'y')";
		$W_req = $db->query($W_requete);
		echo 'Groupe cr��<br />';
	}
}
else
{
	$W_ID_groupe = $row['groupe'];
}
$groupe = recupgroupe($W_ID_groupe, '');

//Regarde si le joueur est leader du groupe
if ($groupe['id_leader'] == $_SESSION['ID'])
{
	//Requ�te pour regarder le nombre d'invitation
	$W_requete = 'SELECT COUNT(inviteur) as count FROM invitation WHERE inviteur = '.$_SESSION['ID'];
	$W_query = $db->query($W_requete);
	$W_row = $db->read_array($W_query);
	
	//Si il y a moins de 4 invitations d�j�, faire une invitation
	if ($W_row['count'] < $G_nb_joueur_groupe - ($nb_perso_groupe))
	{
		//Si il y est d�j� group�
		$W_requete = 'SELECT groupe FROM perso WHERE ID = '.$W_ID;
		$W_query = $db->query($W_requete);
		$W_row = $db->read_array($W_query);
		$W_groupe = $W_row['groupe'];
		if ($W_groupe == 0)
		{
			//Regarde si vous l'avez d�j� invit�
			$W_requete = 'SELECT COUNT(inviteur), groupe FROM invitation WHERE inviteur = '.$_SESSION['ID'].' AND receveur = '.$W_ID.' GROUP BY groupe';
			$W_query = $db->query($W_requete);
			$W_row = $db->read_array($W_query);
			if ($W_row['COUNT(inviteur)'] == 0)
			{
				$W_requete = "INSERT INTO `invitation` ( `ID` , `inviteur` , `receveur` , `time`, `groupe` ) VALUES ('', '".$_SESSION['ID']."', '".$W_ID."', '".time()."','".$W_ID_groupe."')";
				if ($db->query($W_requete))
				{
					echo 'Invitation bien envoy�e !<br />';
				}
				else
				{
				}
			}
			else
			{
				echo 'Vous avez d�j� envoy� une invitation � cette personne';
			}
		}
		else
		{
			echo 'Ce joueur est d�j� group�<br />';
		}
	}
	else
	{
		echo 'Vous avez d�j� invit� 4 autres joueurs !<br />';
	}
}
else
{
	echo 'Vous n\'�tes pas leader du groupe';
}
?>