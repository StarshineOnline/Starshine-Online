<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$constr = new construction($batiment['id_batiment'])
		$lieu = $bourg->has_bonus('royaume') && !$constr->is_buff('assaut');
	}
}
if( $perso->get_rang() != 6 && $perso->get_rang() != 1 )
{
	/// @todo logguer triche
	exit;
}
$change = ($perso->get_rang() == 6 || $royaume->get_ministre_militaire() == $perso->get_id()) && $lieu && $perso->get_hp() > 0 && !$royaume->is_raz();

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;


$cadre = $G_interf->creer_royaume();

if($change)
{
	switch($action)
	{
	case 'modifier':
	  include_once(root.'interface/interf_gest_diplo.class.php');
		$cadre->set_dialogue( new interf_choix_diplo($royaume, $_GET['id']) );
		if( array_key_exists('ajax', $_GET) )
			exit;
		break;
	case 'monter':
		$diplo = unserialize($royaume->get_diplo_time());
		$race = $Trace['liste'][$_GET['id']];
		if( $diplo[$race] > time() )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas changer votre diplomatie avec ce royaume avant '.transform_sec_temp($diplo[$race] - time()));
			break;
		}
		// On vérifie que c'est autorisé
		/// @todo centramiser
		///@todo passer à l'objet
		$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$royaume->get_race()."'");
		$row = $db->read_assoc($req);
		$req2 = $db->query("SELECT * FROM diplomatie WHERE race = '".$race."'");
		$row2 = $db->read_assoc($req2);
		$local_diplo=array_count_values($new_diplo);
		$away_diplo=array_count_values($new_diplo2);
		$diplo_req = $row[$race] - 1;
		if( $row[$race] == 1 && $local_diplo[0] >= 1 )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il ne peut y avoir qu\'un seul allié fraternel.');
			break;
		}
		else if( $row[$race] == 2 && ($local_diplo[0] + $local_diplo[1]) >= 3 )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il ne peut y avoir que 3 alliés en tout (allié fraternel compris).');
			break;
		}
		$requete = 'SELECT * FROM diplomatie_demande WHERE royaume_demande = \''.$royaume->get_race().'\' AND royaume_recois = \''.$race.'\'';
		$db->query($requete);
		if( $db->num_rows > 0 )
		{
			
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Une demande au royaume '.$Gtrad[$race].' pour passer en diplomatie '.$Gtrad['diplo'.$diplo_req].' est déjà en cours.');
			break;
		}
		$star = min($_GET['stars'], $royaume->get_star());
		//Suppression des stars
		$royaume->set_star( $royaume->get_star() - $star );
		$royaume->sauver();
		//Envoi de la demande
		$db->query("INSERT INTO diplomatie_demande VALUES(NULL, ".$diplo_req.", '".$royaume->get_race()."', '".$race."',  ".$star.")");
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Une demande au royaume '.$Gtrad[$race].' pour passer en diplomatie <strong>'.$Gtrad['diplo'.$diplo_req].'</strong> en échange de <em>'.$star.'</em> stars a été envoyée.');
		journal_royaume::ecrire_perso('monte_diplo', new royaume($_GET['id']), $Gtrad['diplo'.$diplo_req], $star);
		break;
	case 'baisser':
		$diplo = unserialize($royaume->get_diplo_time());
		$race = $Trace['liste'][$_GET['id']];
		if( $diplo[$_GET['race']] > time() )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas changer votre diplomatie avec ce royaume avant '.transform_sec_temp($diplo[$race] - time()));
			break;
		}
		// On vérifie que c'est autorisé
		/// @todo centramiser
		///@todo passer à l'objet
		$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$royaume->get_race()."'");
		$row = $db->read_assoc($req);
		$req2 = $db->query("SELECT * FROM diplomatie WHERE race = '".$race."'");
		$row2 = $db->read_assoc($req2);
		$local_diplo=array_count_values($new_diplo);
		$away_diplo=array_count_values($new_diplo2);
		if( $row[$race] == 9 && $local_diplo[10] >= 1 )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il ne peut y avoir qu\'un seul ennemi éternel.');
			break;
		}
		else if( $row[$race] == 8 && ($local_diplo[10] + $local_diplo[9]) >= 3 )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il ne peut y avoir que 3 ennemis en tout (ennemi éternel compris).');
			break;
		}
		$diplo_req = $row[$race] + 1;
		$duree = (pow(2, abs(5 - $diplo_req)) * 60 * 60 * 24);
		$prochain_changement = time() + $duree;
		//Requète de changement pour ce royaume
		$requete = "UPDATE diplomatie SET ".$race." = ".$diplo_req." WHERE race = '".$royaume->get_race()."'";
		$db->query($requete);
		//Requète de changement pour l'autre royaume
		$requete = "UPDATE diplomatie SET ".$royaume->get_race()." = ".$diplo_req." WHERE race = '".$race."'";
		$db->query($requete);
		$requete = "SELECT diplo_time FROM royaume WHERE race = '".sSQL($_GET['race'])."'";
		$req = $db->query($requete);
		$row2 = $db->read_assoc($req);
		$row2['diplo_time'] = unserialize($row2['diplo_time']);
		$row2['diplo_time'][$royaume->get_race()] = $prochain_changement;
		$row2['diplo_time'] = serialize($row2['diplo_time']);
		$diplo[$race] = $prochain_changement;
		$diplo = serialize($diplo);
		$requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".$race."'";
		$db->query($requete);
		$requete = "UPDATE royaume SET diplo_time = '".$diplo."' WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous êtes maintenant en <strong>'.$Gtrad['diplo'.$diplo_req].'</strong> avec les '.$Gtrad[$race]);
		/// Entrées dans les journaux
		journal_royaume::ecrire_perso('baisse_diplo', new royaume($_GET['id']), $Gtrad['diplo'.$diplo_req]);
		journal_royaume::ecrire('baisse_diplo_autre', $_GET['id'], $royaume->get_id(), $royaume->get_nom(), 0, '', $Gtrad['diplo'.$diplo_req]);
		break;
	case 'refuser':
    /// @todo passer à l'objet
    //Recherche de la demande
    $requete = "SELECT * FROM diplomatie_demande WHERE id = ".sSQL($_GET['id']);
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    //Suppression de la demande
    $requete = "DELETE FROM diplomatie_demande WHERE id = ".sSQL($_GET['id']);
    $db->query($requete);
	  //On redonne les stars
    $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_demande']."'";
    $db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Demande refusée');
		/// Entrées dans les journaux
		$R_demande = royaume::create('race', $row['royaume_demande'])[0];
		journal_royaume::ecrire_perso('refus_diplo', $R_demande, $Gtrad['diplo'.$diplo_req]);
		journal_royaume::ecrire('refus_diplo_autre', $R_demande->get_id(), $royaume->get_id(), $royaume->get_nom(), 0, '', $Gtrad['diplo'.$row['diplo']], $row['stars']);
		break;
	case 'accepter':
    /// @todo passer à l'objet
    //Recherche de la demande
    $requete = "SELECT * FROM diplomatie_demande WHERE id = ".sSQL($_GET['id']);
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    //Suppression de la demande
    $requete = "DELETE FROM diplomatie_demande WHERE id = ".sSQL($_GET['id']);
    $db->query($requete);
    $diplo = $row['diplo'];
    $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
    $prochain_changement = time() + $duree;
    //Requète de changement pour ce royaume
    $requete = "UPDATE diplomatie SET ".$row['royaume_demande']." = ".$diplo." WHERE race = '".$royaume->get_race()."'";
    $db->query($requete);
    //On donne les stars au royaume qui recoit
    $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_recois']."'";
    $db->query($requete);
    //Requète de changement pour l'autre royaume
    $requete = "UPDATE diplomatie SET ".$royaume->get_race()." = ".$diplo." WHERE race = '".$row['royaume_demande']."'";
    $db->query($requete);
    $requete = "SELECT diplo_time FROM royaume WHERE race = '".$row['royaume_demande']."'";
    $req = $db->query($requete);
    $row2 = $db->read_assoc($req);
    $row2['diplo_time'] = unserialize($row2['diplo_time']);
    $row2['diplo_time'][$royaume->get_race()] = $prochain_changement;
    $row2['diplo_time'] = serialize($row2['diplo_time']);
    $row3['diplo_time'] = unserialize($royaume->get_diplo_time());
    $row3['diplo_time'][$row['royaume_demande']] = $prochain_changement;
    $row3['diplo_time'] = serialize($row3['diplo_time']);
    $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".$row['royaume_demande']."'";
    $db->query($requete);
    $requete = "UPDATE royaume SET diplo_time = '".$row3['diplo_time']."' WHERE race = '".$royaume->get_race()."'";
    $db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$row['royaume_demande']]);
		/// Entrées dans les journaux
		$R_demande = royaume::create('race', $row['royaume_demande'])[0];
		journal_royaume::ecrire_perso('accepte_diplo', $R_demande, $Gtrad['diplo'.$diplo_req]);
		journal_royaume::ecrire('accepte_diplo_autre', $R_demande->get_id(), $royaume->get_id(), $royaume->get_nom(), 0, '', $Gtrad['diplo'.$row['diplo']], $row['stars']);
		break;
	}
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_gest_diplo($royaume, $change) );
if( $change )
	$cont->add( $G_interf->creer_demande_diplo($royaume) );
$cadre->maj_tooltips();


?>