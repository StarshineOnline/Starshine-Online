<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $perso->get_rang() != 1 )
{
	/// @todo logguer triche
	exit;
}

$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
switch($action)
{
case 'modifier':
	if($_POST['texte'])
	{
		$royaume->set_propagande(sSQL($_POST['texte']));
		interf_alerte::enregistre(interf_alerte::msg_succes, 'Propagande bien modifiée !');
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas saisi de message !');
	break;
}

$cadre = $G_interf->creer_royaume();



$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( new interf_bal_smpl('h4', 'Message du roi') );
$editeur = $cont->add( new interf_editeur('mot_roi', $G_url->get('action', 'modifier'), false, 'editeur', interf_editeur::mot_roi) );
$mot = $royaume->get_motk();
if( $mot )
{
	$texte = new texte($mot->get_propagande(), texte::msg_propagande);
	$editeur->set_texte( $texte->parse() );
}
$cadre->maj_tooltips();



/*
	//$message = $amessage.$message;
	$message = preg_replace("`\[img\]([^[]*)\[/img\]`i", '<img src=\\1 title="\\1">`', $message );
	$message = preg_replace("`\[b\]([^[]*)\[/b\]`i", '<strong>\\1</strong>`', $message );
	$message = preg_replace("`\[i\]([^[]*)\[/i\]`i", '<i>\\1</i>`', $message );
	$message = preg_replace("`\[url\]([^[]*)\[/url\]`i", '<a href="\\1">\\1</a>', $message );
	$message = str_ireplace("[/color]", "</span>", $message);
	$regCouleur = "`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`i";
	$message = preg_replace($regCouleur, "<span style=\"color: \\1\">", $message);
*/

?>