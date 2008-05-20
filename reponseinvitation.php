<?php
include('inc/fp.php');
//L'ID de l'invitation
$W_ID = sSQL($_GET['ID']);
//L'id du groupe
$W_groupe = sSQL($_GET['groupe']);
//La réponse oui ou non
$W_reponse = $_GET['reponse'];

if ($W_reponse == 'non')
{
	$W_requete = 'DELETE FROM invitation WHERE ID = '.$W_ID;
	$W_req = $db->query($W_requete);
}
elseif ($W_reponse == 'oui')
{
	$groupe = recupgroupe($W_groupe, '');
	//Vérifie avant si l'utilisateur n'a pas déjà de groupe (problème rencontré si la personne clic très rapidement sur le lien)
	$W_requete = 'SELECT groupe FROM perso WHERE ID = '.$_SESSION['ID'];
	$W_req = $db->query($W_requete);
	$W_row = $db->read_array($W_req);
	if ($W_row['groupe'] > 0)
	{
		echo 'vous êtes déjà groupé.';
	}
	elseif($groupe['nombre_joueur'] >= 5)
	{
		echo 'Le groupe a atteind son maximum de membres.';
	}
	else
	{
		//Ajoute le membre au groupe
		$W_requete = "INSERT INTO groupe_joueur VALUES('', ".$_SESSION['ID'].", ".$W_groupe.", 'n')";
		$W_req = $db->query($W_requete);
		$W_requete = 'DELETE FROM invitation WHERE ID = '.$W_ID;
		$W_req = $db->query($W_requete);
		$W_requete = "UPDATE perso SET groupe = ".$W_groupe." WHERE ID = ".$_SESSION['ID'];
		$W_req = $db->query($W_requete);
		echo 'Vous êtes maintenant membre du groupe !';
	}
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />