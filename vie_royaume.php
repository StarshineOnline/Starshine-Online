<?php
/**
 * @file vie_royaume.php
 * Gestionn des élections & révolutions 
 */
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();
$perso = joueur::get_perso();

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
/// TODO: vérifier les accès et loguer la triche
switch($action)
{
case 'candidature':
	$interf_princ->set_gauche( $G_interf->creer_candidature() );
	break;
case 'vote':
	$interf_princ->set_gauche( $G_interf->creer_vote() );
	break;
case 'revolution':
	$id_royaume = $Trace[$perso->get_race()]['numrace'];
	$is_revolution = revolution::is_mois_revolution($id_royaume);
	if($is_revolution)
		$revolution = revolution::get_prochain_revolution($id_royaume)[0];
	else
	{
		$revolution = new revolution();
		$revolution->set_id_royaume($id_royaume);
		$revolution->set_date(date('Y-m-d', mktime(0, 0, 0, date("m") + 1 , 1, date("Y"))));
		$revolution->set_id_perso($perso->get_id());
		$revolution->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous avez déclanché une révolution, toutefois il faudra qu\'une autre personne vous suive pour qu\'elle prenne forme.');
	}
	$votes = vote_revolution::create(array('id_perso', 'id_revolution'), array($perso->get_id(), $revolution->get_id()));
	if( count($votes) )
		$vote = $votes[0];
	else
	{
		$vote = new vote_revolution();
		$vote->set_id_revolution($revolution->get_id());
		$vote->set_id_perso($perso->get_id());
	}
	$vote->set_pour( array_key_exists('vote', $_GET) ? $_GET['vote'] : 1 );
	$vote->set_poid_vote($perso->get_level());
	my_dump($vote);
	$vote->sauver();
	$interf_princ->set_gauche( $G_interf->creer_vie_royaume() );
	break;
case 'candidature2':
	$id_royaume = $Trace[$perso->get_race()]['numrace'];
	$election = elections::get_prochain_election($id_royaume, true)[0];
	$candidats = candidat::create(array('id_election', 'id_perso'), array($election->get_id(), $perso->get_id()));
	if( count($candidats) )
		$candidat = $candidats[0];
	else
	{
		$candidat = new candidat(0, $perso->get_id());
		$candidat->set_royaume($id_royaume);
		$candidat->set_id_election($election->get_id());
	}
	$candidat->set_duree(sSQL($_POST['duree']));
	$candidat->set_type(sSQL($_POST['type']));
	$candidat->set_programme(sSQL($_POST['programme']));
	if($_POST['ministre_economie'] != '')
	{
		$economie = perso::create(array('nom', 'race'), array(sSQL($_POST['ministre_economie']), $perso->get_race()));
		if(count($economie) == 1)
			$candidat->set_id_ministre_economie($economie[0]->get_id());
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Ministre de l\'économie introuvable !');
	}
	if($_POST['ministre_militaire'] != '')
	{
		$militaire = perso::create(array('nom', 'race'), array(sSQL($_POST['ministre_militaire']), $perso->get_race()));
		if(count($militaire) == 1)
			$candidat->set_id_ministre_militaire($militaire[0]->get_id());
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Ministre militaire introuvable !');
	}
	$candidat->sauver();
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Candidature '.(count($candidats)?'modifiée':'acceptée'));
	$interf_princ->set_gauche( $G_interf->creer_vie_royaume() );
	break;
case 'retirer_candidature':
	$id_royaume = $Trace[$perso->get_race()]['numrace'];
	$election = elections::get_prochain_election($id_royaume, true)[0];
	$candidats = candidat::create(array('id_election', 'id_perso'), array($election->get_id(), $perso->get_id()));
	if( $candidats )
	{
		$candidats[0]->supprimer();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Candidature retirée');
	}
	else /// TODO: loguer triche
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Candidature introuvable');
	$interf_princ->set_gauche( $G_interf->creer_vie_royaume() );
	break;
case 'vote2':
	$id_royaume = $Trace[$perso->get_race()]['numrace'];
	$elections = elections::get_prochain_election($id_royaume, $perso->get_grade()->get_id() == 6);
	/// TODO: passer à l'objet
	$requete = 'SELECT id FROM vote WHERE id_election = '.$elections[0]->get_id().' AND id_perso = '.$perso->get_id();
	$req = $db->query($requete);
	$row = $db->read_array($req);
	if( $elections[0]->get_type() == 'universel' )
	{
		$nom = 'vote';
		$fin = '';
	}
	else
	{
		$nom = 'nomination';
		$fin = 'e';
	}
	if( $row )
	{
		$requete = 'UPDATE vote SET id_candidat = '.$_GET['candidat'].' WHERE id = '.$row[0];
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre '.$nom.' a bien été modifié'.$fin.'.');
	}
	else
	{
		$requete = 'INSERT INTO vote (id_perso, id_candidat, id_election) VALUES ('.$perso->get_id().', '.$_GET['candidat'].', '.$elections[0]->get_id().')';
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre '.$nom.' a bien été pris'.$fin.' en compte.');
	}
	$db->query($requete);
	$interf_princ->set_gauche( $G_interf->creer_vie_royaume() );
	break;
default:
	$interf_princ->set_gauche( $G_interf->creer_vie_royaume() );
}

?>
