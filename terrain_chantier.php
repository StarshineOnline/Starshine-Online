<?php // -*- tab-width:2; mode: php -*- 
/**
* @file terrain_chantier.php
* Ecuries
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
/// TODO: ajouter des vérifications et des messages
switch($action)
{
case 'construire':
	$chantier = new terrain_chantier($_GET['id']);
	$batiment = $chantier->get_batiment();
	if ($chantier->star_point == 0)
	{
		send_forbidden('Gros malin, va ! ');
	}
	//Il faut qu'il ai 10 PA
	else if($perso->get_pa() >= 10)
	{
		//dé d'Architecture
		$de_architecture = rand(1, $perso->get_architecture());
		$taxe = floor(($chantier->star_point * $de_architecture) * $R->get_taxe_diplo($perso->get_race()) / 100);
		$stars = ($chantier->star_point * $de_architecture) - $taxe;
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous aidez à construire le batiment pour '.$de_architecture.' points de structure.<br />Et vous recevez '.$stars.' stars');
		//Augmentation de la compétence d'architecture
		$augmentation = augmentation_competence('architecture', $perso, 2);
		if ($augmentation[1] == 1)
		{
			$perso->set_architecture($augmentation[0]);
		}
		//Gain de stars et Suppression des PA
    $perso->set_star($perso->get_star() + $stars);
    $perso->set_pa($perso->get_pa() - 10);
    $perso->sauver();
		$delete = false;
		//Fin de la construction ?
		if(($chantier->point + $de_architecture) >= $batiment->point_structure)
		{
			//Si c'est un aggrandissement
			if($batiment->type == 'agrandissement')
			{
				$terrain = new terrain($chantier->id_terrain);
				$terrain->nb_case = $batiment->effet;
				$terrain->sauver();
			}
			//Sinon on fait la construction
			else
			{
				if($chantier->upgrade_id_construction == 0)
				{
					$construction = new terrain_construction();
					$construction->set_id_terrain($chantier->id_terrain);
					$construction->set_id_batiment($chantier->id_batiment);
					$construction->sauver();
				}
				else
				{
					$construction = new terrain_construction($chantier->upgrade_id_construction);
					$construction->set_id_batiment($chantier->get_id_batiment());
					$construction->sauver();
				}
			}
			//On supprime le chantier
			$chantier->supprimer();
			$delete = true;
		}
		//Avancée de la construction
		else
		{
			$chantier->point += $de_architecture;
			$chantier->sauver();
		}
		$interf_princ->maj_tooltips();
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de PA.');
	break;
}
$interf_princ->set_gauche( $G_interf->creer_terrain_chantier($R) );


?>
