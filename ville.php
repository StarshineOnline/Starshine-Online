<?php
/**
* @file ville.php
* Accès à la ville
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
//$perso->check_perso();
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
/// @todo logguer triche ?
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Gestion des amandes
///@todo passer à l'objet
$amende = recup_amende($perso->get_id());
if($_GET['action'] == 'paye_amende')
{
	if($amende['montant'] > $perso->get_star())
	{
		$interf_princ->set_gauche( $G_interf->creer_ville_amende($R, $amende, true) );
		exit;
	}
	else
	{
		//On supprime l'amende du joueur
		$requete = 'UPDATE perso SET star = star - '.floor($amende['montant']).', crime = 0, amende = 0 WHERE ID = '.$perso->get_id();
		$db->query($requete);
		$requete = 'DELETE FROM amende WHERE id = '.$amende['id'];
		$db->query($requete);
		//On partage l'amende a tous les joueurs du royaume
		$requete = 'SELECT * FROM perso WHERE race = "'.$perso->get_race().'" AND statut = "actif" AND ID <> '.$perso->get_id();
		$req = $db->query($requete);
		$persos = array();
		while($row = $db->read_assoc($req))
		{
			$persos[] = $row;
		}
		$tot_joueurs = count($persos);
		$star_joueur = floor(floor($amende['montant']) / $tot_joueurs);
		$star_royaume = floor($amende['montant']) % $tot_joueurs;
		if($star_joueur > 0)
		{
			$requete = 'UPDATE perso SET star = star + '.$star_joueur.' WHERE race  = "'.$perso->get_race().'" AND statut = "actif" AND ID <> '.$perso->get_id();
			$db->query($requete);
			foreach($persos as $p)
			{
				//Inscription dans son journal de l'amende
				$requete = 'INSERT INTO journal VALUES("", '.$p['ID'].', "r_amende", "'.$p['nom'].'", "'.$perso->get_nom().'", NOW(), "'.$star_joueur.'", 0, 0, 0)';
				$db->query($requete);
			}
		}
		if($star_royaume > 0)
		{
			$requete = 'UPDATE royaume SET star = star + '.$star_royaume.' WHERE race = "'.$perso->get_race().'"';
			$db->query($requete);
		}
		//Si le joueur avait des primes sur la tête, elles sont effacées
		if($amende['prime'] > 0)
		{
			$requete = 'SELECT * FROM prime_criminel WHERE id_criminel = '.$perso->get_id();
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$requete = 'UPDATE perso SET star = star + '.$row['montant'].' WHERE ID = '.$row['id_joueur'];
				$db->query($requete);
			}
			$requete = 'DELETE FROM prime_criminel WHERE id_criminel = '.$perso->get_id();
			$db->query($requete);
		}
		$amende = recup_amende($perso->get_id());
	}
}
	
if($amende)
	$interf_princ->set_gauche( $G_interf->creer_ville_amende($R, $amende) );
else
	$interf_princ->set_gauche( $G_interf->creer_ville_entree($R) );

?>