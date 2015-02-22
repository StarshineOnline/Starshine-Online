<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $royaume->get_ministre_militaire() != $perso->get_id() )
{
	/// @todo logguer triche
	exit;
}

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$cadre = $G_interf->creer_royaume();

switch($action)
{
case 'carte':
	$cadre->add_section('gest_bat_carte', new interf_carte($_GET['x'], $_GET['y'], interf_carte::aff_gest_batailles, 8, 'carte'));
	exit;
case 'suppr':  // Suppression d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille->supprimer(true);
	break;
case 'debut':  // Début d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille->set_etat(1);
	$bataille->sauver();
	// Envoie des messages aux groupes participants
	/// @todo à modifier quand on aura le nouveau type de messagerie
	$groupes = $bataille->get_groupes();
	foreach($groupes as $groupe)
	{
		$titre = 'Mission pour la bataille : '.$bataille->get_nom();
		$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->get_nom().'[br]
		[bataille:'.$bataille->get_nom().']';
		//Si le groupe n'a pas deja son thread pour cette bataille
		if($groupe->get_id_thread() == 0)
		{
			$thread = new messagerie_thread(0, $groupe->get_id_groupe(), 0, $perso->get_id(), 1, null, $titre);
			$thread->sauver();
			$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
			$messagerie->envoi_message($thread->id_thread, 0, $titre, $message, $groupe->get_id_groupe(), 1);
			$groupe->set_id_thread($thread->id_thread);
			$groupe->sauver();
		}
		else
		{
			$messagerie = new messagerie($joueur->get_id(), $joueur->get_groupe());
			$messagerie->envoi_message($groupe->get_id_thread(), 0, $titre, $message, $groupe->get_id_groupe(), 1);
		}
	}
	break;
case 'fermer':  // Fin d'une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$bataille->set_etat(2);
	$bataille->sauver();
	break;
case 'modifier': // Modifier une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$cadre->set_gestion( $G_interf->creer_modif_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'nouveau': // Nouvelle bataille
	$bataille = new bataille();
	$cadre->set_gestion( $G_interf->creer_modif_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'creer': // validation de la création ou modification
	$bataille = array_key_exists('id', $_GET) ? new bataille(sSQL($_GET['id'])) : new bataille(0, $royaume->get_id());
	$bataille->set_nom( $_POST['nom'] );
	$bataille->set_description( $_POST['texte'] );
	$bataille->set_x( $_POST['x'] );
	$bataille->set_y( $_POST['y'] );
	$bataille->sauver();
	// Groupes
	/// @todo passer à l'objet
	$requete = "SELECT groupe.id as groupeid, groupe.nom as groupenom, groupe_joueur.id_joueur, perso.nom, perso.race FROM groupe LEFT JOIN groupe_joueur ON groupe.id = groupe_joueur.id_groupe LEFT JOIN perso ON groupe_joueur.id_joueur = perso.ID WHERE groupe_joueur.leader = 'y' AND perso.race = '".joueur::get_perso()->get_race()."'";
	$req = $db->query($requete);
	// On regarde tous les groupes possibles
	while($row = $db->read_assoc($req))
	{
		$bat_groupe = new bataille_groupe(0,0,$row['groupeid']);
		if( $bat_groupe->is_bataille() )
		{
			// on regarde si le groupe a été retiré de la bataille
			if( $bat_groupe->get_id_bataille() == $bataille->get_id() && !in_array($row['groupeid'], $_POST['groupes']) )
			{
				// On supprime les reperes associés au groupe, puis le groupe
				$bat_groupe->get_reperes();
				foreach ($bat_groupe->reperes as $repere)
					$repere->supprimer();
				$bat_groupe->supprimer();
			}
		}
		else if( in_array($row['groupeid'], $_POST['groupes']) )
		{
			$bataille_groupe = new bataille_groupe(0, $bataille->get_id(), $row['groupeid']);
			$bataille_groupe->sauver();
			//unset($bataille_groupe);  // <- utile ?
		}
	}
	break;
case 'gerer': // Gérer une bataille
	$bataille = new bataille(sSQL($_GET['id']));
	$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'suppr_mission';
	$bataille = new bataille(sSQL($_GET['bataille']));
	$mission = new bataille_groupe_repere(sSQL($_GET['id']));
	$mission->supprimer();
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Mission supprimée avec succès');
	$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'mission';
	$bataille = new bataille(sSQL($_GET['bataille']));
	if( !$_GET['groupe'] || !$_GET['mission'])
		break;
	$mission = new bataille_groupe_repere();
	$mission->set_id_repere(sSQL($_GET['mission']));
	$mission->set_id_groupe(sSQL($_GET['groupe']));
	$mission->accepter = 0;
	$mission->sauver();
	//Si la bataille est déjà lancée
	if ($bataille->get_etat() == 1)
	{
		//On envoi un message au groupe
		$groupe = new bataille_groupe($mission->get_id_groupe());
		$titre = 'Mission pour la bataille : '.$bataille->get_nom();
		$message = 'Votre groupe a été affecté à une mission concernant la bataille : '.$bataille->get_nom().'[br]
		[bataille:'.$bataille->get_nom().'][br][br]';
		// Si le groupe n'a pas deja son thread pour cette bataille
		if($groupe->get_id_thread() == 0)
		{
			$thread = new messagerie_thread(0, $groupe->get_id_groupe(), 0, $perso->get_id(), 1, null, $titre);
			$thread->sauver();
			$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
			$messagerie->envoi_message($thread->id_thread, 0, $titre, $message, $groupe->get_id_groupe(), 1);
			$groupe->set_id_thread($thread->id_thread);
			$groupe->sauver();
		}
		else
		{
			$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
			$messagerie->envoi_message($groupe->get_id_thread(), 0, $titre, $message, $groupe->get_id_groupe(), 1);
		}
	}
	$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'suppr_repere':  // Suppression d'un repère
	$repere = new bataille_repere($_GET['id_repere']);
	$repere->supprimer(true);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Repère supprimé avec succès');
	break;
case 'case_repere':
	$bataille = new bataille(sSQL($_GET['bataille']));
	$cadre->set_dialogue( $G_interf->creer_reperes($bataille, sSQL($_GET['x']), sSQL($_GET['y']), $royaume) );
	if( !array_key_exists('ajax', $_GET) )
		$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
case 'nouv_repere':
	$bataille = new bataille(sSQL($_GET['bataille']));
	$type = $_GET['repere'];
	$repere = new bataille_repere();
	switch($type[0])
	{
	case 'a' :
		$repere->set_type('action');
		break;
	case 'b' :
		$repere->set_type('batiment');
		break;
	}
	$repere->set_id_bataille( $bataille->get_id() );
	$repere->set_id_type(substr($type, 1, strlen($type)));
	$repere->set_x(sSQL($_GET['x']));
	$repere->set_y(sSQL($_GET['y']));
	$repere->sauver();
	$cadre->set_gestion( $G_interf->creer_gerer_bataille($bataille) );
	$cadre->maj_tooltips();
	exit;
}

$cadre->set_gestion( $G_interf->creer_gest_batailles($royaume) );
$cadre->maj_tooltips();
