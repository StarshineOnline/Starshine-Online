<?php
/**
* @file vente_terrain.php
* Vente de terrain
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///@todo à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// @todo logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
/// @todo ajouter des vérifications et des messages
switch($action)
{
case 'encherir' :
	$vente_terrain = new vente_terrain($_GET['id']);
	$verif = $vente_terrain->verif_joueur($perso);
	if($verif)
	{
		$vente_terrain->enchere($perso->get_id());
		$interf_princ->maj_perso();
	}
	else
	{
		switch($vente_terrain->erreur)
		{
			case 'star' :
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars.');
			break;
			case 'royaume' :
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne faites pas partie de ce royaume.');
			break;
			case 'terrain' :
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous possédez déjà un terrain.');
			break;
			case 'enchere' :
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous avez déjà une enchère en cours.');
			break;
			case 'date_fin' :
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette enchère est terminée.');
			break;
		}
	}
	break;
}
$interf_princ->set_gauche( $G_interf->creer_vente_terrain($R) );


?>