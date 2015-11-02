<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

if( array_key_exists('categorie', $_GET) )
	$categorie = $_GET['categorie'];
else if( joueur::get_perso() )
	$categorie = 'perso';
else
	$categorie = 'joueur';
			

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

// action sans affichage
switch($action)
{
case 'jabber':
	/// @todo passer à l'objet
	$requete = 'REPLACE INTO options(id_perso, nom, valeur) VALUES('.$_SESSION['ID'].', "jabber_'.$_GET['option'].'", '.$_GET['valeur'].')';
	$db->query($requete);
	exit;
}

$interf_princ = $G_interf->creer_princ();
  

switch($action)
{
case 'titre':
	$titre_perso = new titre($_SESSION['ID']);
	$titre_perso->set_id_titre($_GET['titre']);
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre titre a bien été modifié. Pensez à réactualiser !');
	break;
case 'mdp':
	$joueur = joueur::factory();
	if( !array_key_exists('anc_mdp', $_POST) || !$joueur->test_mdp( md5($_POST['anc_mdp']) ) )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur, l\'ancien mot de passe n\'est pas le bon.');
		break;
	}
	if( !array_key_exists('nouv_mdp_1', $_POST) || !array_key_exists('nouv_mdp_2', $_POST) || $_POST['nouv_mdp_1'] != $_POST['nouv_mdp_2'] )
	{
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Erreur lors de la saisie du nouveau mot de passe.');
		break;
	}
	$perso = joueur::get_perso();
	$perso->set_password(md5($_POST['nouv_mdp_1']));
	$perso->sauver();
	
	if(array_key_exists('id_joueur', $_SESSION)) 
	{
		$joueur->set_mdp(md5($_POST['nouv_mdp_1']));
		$joueur->set_mdp_jabber(md5($_POST['nouv_mdp_1']));
		$joueur->set_mdp_forum(sha1($_POST['nouv_mdp_1']));
		$joueur->sauver();
	}

	require('connect_forum.php');
	if($db_forum)
	{
		$requete = "UPDATE punbbusers SET password = '".sha1($_POST['nouv_mdp_1'])."' WHERE username = '".sSQL($perso->get_nom())."'";
		$db_forum->query($requete);
	}
	interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre mot de passe a bien été modifié !');
	break;
case 'email' :
	if(array_key_exists('email', $_POST))
	{
		$email = sSQL($_POST['email']);
		$joueur = joueur::factory();
		$joueur->set_email($email);
		$joueur->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre email a bien été modifié !');
	}
	break;
case 'suppr':
	$perso = joueur::get_perso();
	/// @todo faire un statut 'suppr'
	$perso->set_statut('ban');
	$perso->set_fin_ban(time() + 3600 * 24 * 36500);
	$perso->sauver();
	$groupe = $perso->get_groupe();
	if($groupe != 0)
		degroup($perso->get_id(), $groupe);
	require_once('connect_forum.php');
	if($db_forum)
	{
		$requete = "INSERT INTO punbbbans VALUES(NULL, '".sSQL($perso->get_nom())."', NULL, NULL, NULL, NULL, 2)";
		$db_forum->query($requete);
	}
	unset($_COOKIE['nom']);
	unset($_SESSION['nom']);
	unset($_SESSION['ID']);
	$interf_princ->recharger_interface('index.php');
	exit;
case 'hibern':
	$perso = joueur::get_perso();
	$perso->set_statut('hibern');
	$perso->set_fin_ban(time() + 3600 * 24 * 14);
	$perso->sauver();
	unset($_COOKIE['nom']);
	unset($_SESSION['nom']);
	unset($_SESSION['ID']);
	$interf_princ->recharger_interface('index.php');
	exit;
case 'interface':
	$val = sSQL($_GET['valeur'], SSQL_INTEGER);
	$requete = "REPLACE INTO options(id_perso, nom, valeur) VALUES(".$_SESSION['ID'].", 'interface', $val)";
	$db->query($requete);
	$interf_princ->recharger_interface('index.php');
	exit;
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$G_url->add('categorie', $categorie);
	switch($categorie)
	{
	case 'perso':
		$interf_princ->add( $G_interf->creer_options_perso() );
		break;
	case 'joueur':
		$interf_princ->add( $G_interf->creer_options_joueur() );
		break;
	case 'affichage':
		$interf_princ->add( $G_interf->creer_options_affichage() );
		break;
	}
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Options', true, 'dlg_options') );
	$dlg->add( $G_interf->creer_options($categorie) );
}

