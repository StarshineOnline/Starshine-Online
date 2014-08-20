<?php
if (file_exists('root.php'))
	include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');


if(!array_key_exists('ID', $_SESSION) || empty($_SESSION['ID']))
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
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
	$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
	$msg = htmlspecialchars(addslashes($_POST['texte']));
	$sujet = $_GET['sujet'];
	$sujet = new messagerie_thread($sujet);
	$id_dest = $sujet->id_dest == $perso->get_id() ? $sujet->id_auteur : $sujet->id_dest;
	$messagerie->envoi_message($sujet, $id_dest, '', $msg, $sujet->id_groupe);
	$page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
	break;
case 'suppr_msg':
	$message = new messagerie_message($_GET['msg']);
	$message->supprimer();
case 'lire':
	$sujet = $_GET['sujet'];
	$page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
	break;
case 'nouveau':
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Messagerie') );
	$cadre->add( $G_interf->creer_nouveau_message($type) );
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
	if( array_key_exists('destinataire', $_POST) )
	{
		/// TODO: passer à l'objet
		$requete = 'SELECT id FROM perso WHERE nom = "'.$_POST['destinataire'].'"';
		$req = $db->query($requete);
		$row = $db->read_row($req);
		if( !$row )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saisi de message.');
			break;
		}
		$id_dest = $row[0];
		$id_groupe = 0;
	}
	else
	{
		$id_dest = 0;
		$id_groupe = $perso->get_groupe();
	}
	$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
	$sujet = $messagerie->envoi_message(0, $id_dest, $_POST['titre'], $_POST['texte'], $id_groupe);
	$page = 1;
	break;
case 'suppr_sujet':
	$thread = new messagerie_thread($_GET['sujet']);
	$thread->supprimer(true);
	break;
case 'masquer_sujet':
	$messagerie = new messagerie($perso->get_id(), $perso->get_groupe());
	$messagerie->set_thread_masque($_GET['sujet']);
	break;
}

if( $ajax == 2 )
{
	$interf_princ->add( $G_interf->creer_liste_messages($perso, $type) );
}
else
{
	$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Messagerie') );
	interf_alerte::aff_enregistres($cadre);
	$cadre->add( $G_interf->creer_messagerie($perso, $type, $sujet, $page) );
}
$interf_princ->maj_tooltips();
