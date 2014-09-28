<?php // -*- mode: php; tab-width:2 -*-
/**
* @file universite.php
* Université
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
switch($action)
{
case 'prendre':
	$fin = false;
	/// @todo  passer par les objets
	$requete = "SELECT * FROM classe_requis WHERE id_classe = '".sSQL($_GET['id'])."'";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		/// @todo loguer triche
		if($row['new'] == 'yes') $new[] = $row['competence'];
		if($row['competence'] == 'classe')
		{
			$requete = "SELECT * FROM classe WHERE id = ".$row['requis'];
			$req_classe = $db->query($requete);
			$row_classe = $db->read_array($req_classe);
			if(mb_strtolower($row_classe['nom']) != mb_strtolower($perso->get_classe()))
			{
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut être un '.$row_classe['nom']);
				$fin = true;
			}
		}
		else
		{
			$get = 'get_'.$row['competence'];
			if( (method_exists($perso, $get)  && $perso->$get(true) < $row['requis'])
					|| (!method_exists($perso, $get) && $perso->get_competence($row['competence']) < $row['requis']) )
			{
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez en : '.ucwords($row['competence']));
				$fin = true;
			}
		}
	}
	//Le joueur rempli les conditions
	if(!$fin)
	{
		$and = '';
		/// @todo  passer par les objets
		$requete = "SELECT * FROM classe_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
		$req = $db->query($requete);
		$new = array();
		while($row = $db->read_array($req))
		{
			if($row['new'] == 'yes') $new[] = $row['competence'];
			if($row['competence'] == 'facteur_magie')
				$joueur->set_facteur_magie($row['permet']);
			if($row['competence'] == 'sort_vie+')
				$joueur->set_sort_vie($joueur->get_sort_vie() + $row['permet']);
			if($row['competence'] == 'max_pet')
				$joueur->set_max_pet($row['permet']);
		}
		$newi = 0;
		while($newi < count($new))
		{
			$requete = "INSERT INTO comp_perso VALUES(null, '1', '".$new[$newi]."', 1, ".$_SESSION['ID'].")";
			$req = $db->query($requete);
			$newi++;
		}
		$comp_combat = explode(';', $perso->get_comp_combat());
		if($comp_combat[0] == '') $comp_combat = array();
		$comp_jeu = explode(';', $perso->get_comp_jeu());
		if($comp_jeu[0] == '') $comp_jeu = array();
		$sort_jeu = explode(';', $perso->get_sort_jeu());
		if($sort_jeu[0] == '') $sort_jeu = array();
		$sort_combat = explode(';', $perso->get_sort_combat());
		if($sort_combat[0] == '') $sort_combat = array();
		$requete = "SELECT * FROM classe_comp_permet WHERE id_classe = '".sSQL($_GET['id'])."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			if($row['type'] == 'comp_combat') $comp_combat[] = $row['competence'];
			if($row['type'] == 'comp_jeu') $comp_jeu[] = $row['competence'];
			if($row['type'] == 'sort_jeu') $sort_jeu[] = $row['competence'];
			if($row['type'] == 'sort_combat') $sort_combat[] = $row['competence'];
		}
		$perso->set_comp_combat(implode(';', $comp_combat));
		$perso->set_comp_jeu(implode(';', $comp_jeu));
		$perso->set_sort_jeu(implode(';', $sort_jeu));
		$perso->set_sort_combat(implode(';', $sort_combat));
		$perso->set_classe_id($_GET['id']);
		$perso->set_classe(mb_strtolower($nom, 'UTF-8'));
		$perso->sauver();
		$perso->unlock_achiev("rang_$rang");
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Félicitations vous suivez maintenant la voie du '.$nom);
		$log = new log_admin();
		$log->send($perso->get_id(), 'rang', mb_strtolower($nom, 'UTF-8'));
	}
case 'description':
	$interf_princ->set_gauche( $G_interf->creer_descr_classe($R, $_GET['id']) );
	break;
case 'quete':
	$interf_princ->set_gauche( $G_interf->creer_bibliotheque($R, $_GET['id']) );
	break;
default:
	$interf_princ->set_gauche( $G_interf->creer_universite($R) );
}