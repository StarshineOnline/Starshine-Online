<?php
/**
 * @file messagerie.php
 * Gestion de la messagerie 
 */ 
if (file_exists('root.php'))
	include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


if(!array_key_exists('ID', $_SESSION) || empty($_SESSION['ID']))
{
	$G_interf->creer_jeu()->recharger_interface('index.php');
	exit();
}

$perso = joueur::get_perso();

$interf_princ = $G_interf->creer_jeu();
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
$type = array_key_exists('type', $_GET) ? $_GET['type'] : null;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
$sujet = false;
$page = null;

switch($action)
{
case 'ecrire':
	$messagerie = new messagerie($perso);
	$msg = $_POST['texte'];
	$sujet = $_GET['sujet'];
	$sujet_obj = new messagerie_thread($sujet);
	$id_dest = $sujet_obj->get_id_dest() == $perso->get_id() ? $sujet_obj->get_id_auteur() : $sujet_obj->get_id_dest();
	$messagerie->envoi_message($sujet, $id_dest, '', $msg, $sujet_obj->get_id_groupe(), 0, $sujet_obj->get_categorie());
	$page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
	break;
case 'suppr_msg':
	$message = new messagerie_message($_GET['msg']);
	if( joueur::is_granted('suppr', $message) )
	{
		$message->supprimer();
	}
case 'lire':
	$messagerie = new messagerie($perso);
	$sujet = $_GET['sujet'];
	$page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
	$messagerie->set_thread_lu($sujet);
	$interf_princ->maj_perso(true);
	break;
case 'nouveau':
	$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Messagerie') );
	$cadre->add( $G_interf->creer_nouveau_message($type, $id) );
	exit;
case 'nouveau_sujet':
	if( !array_key_exists('titre', $_POST) || !$_POST['titre'] )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saisi de titre.');
		break;
	}
	if( !array_key_exists('texte', $_POST) || !$_POST['texte'] )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saisi de message.');
		break;
	}
	switch( $_GET['type'] )
	{
	case 'perso':
		/// @todo passer à l'objet
		$requete = 'SELECT id FROM perso WHERE nom = "'.$_POST['destinataire'].'"';
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if( !$row )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saisi de destinataire.');
			break;
		}
		$id_dest = $row[0];
		$id_groupe = 0;
		break;
	case 'groupe':
		$id_dest = 0;
		$id_groupe = $perso->get_id_groupe();
		break;
	case 'royaume':
		/// @todo vérifier que le groupe existe
		$id_chef = $perso->get_groupe()->get_id_leader();
		$chef = new perso( $id_chef );
		$id_dest = $Trace[ $chef->get_race() ]['numrace'];
		$id_groupe = $_POST['destinataire'];
		break;
	case 'diplomatie':
		/// @todo vérifier que le royaume existe
		$id_dest = $_POST['destinataire'];
		$id_groupe = 0;
		break;
	}
	$messagerie = new messagerie($perso);
	$sujet = $messagerie->envoi_message(0, $id_dest, $_POST['titre'], $_POST['texte'], /*$_GET['type']*/$id_groupe, 0, $_GET['type']);
	$page = 1;
	break;
case 'suppr_sujet':
	$thread = new messagerie_thread($_GET['sujet']);
	if( joueur::is_granted('suppr', $thread) )
	{
		$thread->supprimer();
	}
	break;
case 'masquer_sujet':
	$messagerie = new messagerie($perso);
	$messagerie->set_thread_masque($_GET['sujet']);
	break;
case 'tous_lu':
	$messagerie = new messagerie($perso);
	$messagerie->set_thread_lu_all();
	break;
}

if( $ajax == 2 )
{
	$lst = $interf_princ->add( $G_interf->creer_liste_messages($perso, $type) );
}
else
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Messagerie') );
	interf_alerte::aff_enregistres($cadre);
	$cadre->add( $G_interf->creer_messagerie($perso, $type, $sujet, $page) );
}
$interf_princ->maj_tooltips();
