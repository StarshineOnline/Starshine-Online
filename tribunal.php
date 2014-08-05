<?php
/**
* @file qg.php
* Quartier général
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
///TODO: à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// TODO: logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// TODO: logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// TODO: logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
switch($action)
{
case 'prime':
	/// TODO: passer à l'objet
	$id = sSQL($_GET['id']);
	$requete = 'SELECT a.id_joueur, nom from amende AS a INNER JOIN perso AS p ON p.id = a.id_joueur WHERE a.id = '.$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if( array_key_exists('star', $_GET) )
	{
		$prime = sSQL($_GET['star']);
		if($prime <= $perso->get_star())
		{
			if($prime > 0)
			{
				/// TODO: passer à l'objet
				$amende = recup_amende($row['id_joueur']);
				//On supprime les stars au joueur
				$requete = "UPDATE perso SET star = star - ".$prime." WHERE ID = ".$perso->get_id();
				$db->query($requete);
				//On ajoute la prime dans la liste des primes
				$requete = "INSERT INTO prime_criminel VALUES('', ".$row['id_joueur'].", ".$perso->get_id().", ".$id.", ".$prime.")";
				$db->query($requete);
				//On totalise la prime avec les autres
				$requete = "UPDATE amende SET prime = prime + ".$prime." WHERE id = ".$id;
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous avez bien mis une prime sur la tête du criminel !');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur de saisi des stars!');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars.');
	}
	else
	{
		$interf_princ->set_dialogue( $G_interf->creer_prime($_GET['id'], $row['nom']) );
		exit;
	}
	break;
}
$interf_princ->set_gauche( $G_interf->creer_tribunal($R) );

?>