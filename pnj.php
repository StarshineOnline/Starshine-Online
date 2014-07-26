<?php // -*- mode: php; tab-width:2 -*-
/**
 * @file pnj.php
 * Script gérant les intéractions avec les PNJ. 
 */

/*
 * Aide pour les discours des PNJ :
 * Les différents discours sont séparés pas '*****', leur id correspond à leur ordre d'apparition en commençant par 0.
 * Plusieur balises "pseudo-bbscript" existent :
 * - ID : crée un lien vers la partie suivant du discours, ex [ID:1]suite[/ID:1]
 * - quete_finie / QUETEFINI
 * - non_quete
 * - ISQUETE
 * - retour : affiche un lien pour retourner à la description de la case
 * - quete : validation de la quête
 * - quetegroupe : validation de la quête de groupe
 * - prendquete : prise d'une quête
 * - donneitem : donne un item
 * - vendsitem : vends un item
 * - run : lancement fonction personalisée (cf. fonction/pnj.inc.php)
 * - if : IF fonction personalisée (cf. fonction/pnj.inc.php)
 * - ifnot : IFNOT fonction personalisée (cf. fonction/pnj.inc.php)
 * - verifinventaire : validation inventaire
 */
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

$case = $perso->get_poscase();

if(array_key_exists('reponse', $_GET)) $reponse = $_GET['reponse']; else $reponse = 0;

$id = $_GET['id'];
$pnj = new pnj($id);


if( $pnj->get_x() != $perso->get_x() || $pnj->get_y() != $perso->get_y() )
	security_block(URL_MANIPULATION, 'PNJ pas sur la même case');


$cadre = $interf_princ->set_droite( $G_interf->creer_droite($pnj->get_nom()) );



$texte = new texte($pnj->get_texte(), texte::pnj);
$texte->set_liens('pnj.php?id='.$id.'&amp;poscase='.$case, $case, true);
$texte->set_perso($perso);
$texte->set_id_objet('P'.$id);

$cadre->add( new interf_txt($texte->parse($reponse)) );
?>
