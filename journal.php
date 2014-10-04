<?php
/**
 * @file journal.php
 * Afficher le journal 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

$options = recup_option($_SESSION['ID']);

$mois =  array_key_exists('mois', $_GET) ? $_GET['mois'] : 'actuel';
$page =  array_key_exists('page', $_GET) ? $_GET['page'] : 1;

$interf_princ = $G_interf->creer_jeu();
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'options':
	/// @todo passer à l'objet
	foreach(interf_journal::$liste_options as $cle=>$texte)
	{
		$requete = false;
		if( array_key_exists($cle, $_POST) )
		{
			if( array_key_exists($cle, $options) )
				$requete = 'UPDATE options SET valeur = '.$_POST[$cle].' WHERE id_perso = '.$_SESSION['ID'].' AND nom = "'.$cle.'"';
			else
				$requete = 'INSERT INTO options(id_perso, nom, valeur) VALUES('.$_SESSION['ID'].', "'.$cle.'", '.$_POST[$cle].')';
			$options[$cle] = 1;
		}
		else if( array_key_exists($cle, $options) )
		{
			$requete = 'UPDATE options SET valeur = 0 WHERE id_perso = '.$_SESSION['ID'].' AND nom = "'.$cle.'"';
			$options[$cle] = 0;
		}
		if($requete)
			$db->query($requete);
	}
	if( array_key_exists('nbrLignesJournal', $_POST) )
	{
		/// @todo passer à l'objet
		if( array_key_exists('nbrLignesJournal', $options) )
			$requete = 'UPDATE options SET valeur = '.$_POST['nbrLignesJournal'].' WHERE id_perso = '.$_SESSION['ID'].' AND nom = "nbrLignesJournal"';
		else
			$requete = 'INSERT INTO options(id_perso, nom, valeur) VALUES('.$_SESSION['ID'].', "nbrLignesJournal", '.$_POST['nbrLignesJournal'].')';
		$db->query($requete);
	}
	break;
case 'combat':
	/// @todo passer à l'objet
	$req = $db->query('SELECT * FROM combats WHERE id_journal = '.$_GET['id']);
	$row = $db->read_assoc($req);
	$combat = new combat($row);
	$G_url->add('action', 'combat');
	$cadre = $combat->afficher_combat($interf_princ);
	exit;
}
$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Journal') );
$cadre->add( $G_interf->creer_journal(joueur::get_perso(), $options, $mois, $page) );

