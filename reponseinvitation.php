<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
//L'ID de l'invitation
$W_ID = sSQL($_GET['ID']);
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
		echo 'vous êtes déjà groupé.';
	elseif(count($groupe->get_membre()) >= 5)
		echo 'Le groupe a atteind son maximum de membres.';
	else
	{
		//Ajoute le membre au groupe
		$groupe_joueur = new groupe_joueur(-1, $joueur->get_id(), $groupe->get_id(), 'n');
		//echo $groupe_joueur;
		$joueur->set_groupe($groupe->get_id());
		
		$invitation->supprimer();
		$groupe_joueur->sauver();
		$joueur->sauver();
		echo 'Vous êtes maintenant membre du groupe !';
	}
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />