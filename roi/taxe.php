<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');
$duree = (60 * 60 * 24) * 7;


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 && $royaume->get_ministre_economie() != $perso->get_id() )
{
	/// @todo logguer triche
	exit;
}

$cadre = $G_interf->creer_royaume();

if((time() - $duree) < $royaume->get_taxe_time())
{
	$cadre->set_gestion( new interf_bal_smpl('span', 'Vous avez déjà modifié le taux de taxe récemment. Vous pourrez le modifier dans '.transform_sec_temp(($royaume->get_taxe_time() + $duree) - time())) );
}
else if($_GET['action'] == 'modifier')
{
	$requete = "UPDATE royaume SET taxe = ".sSQL($_GET['taux']).", taxe_time = ".time()." WHERE id = ".$royaume->get_id();
	if($db->query($requete))
		$cadre->set_gestion( new interf_alerte(interf_alerte::msg_succes, false, false, 'Taux de taxe modifié !') );
}
else
{
	$form = $cadre->set_gestion( new interf_form('taxe.php?action=modifier', 'modif_taxe', 'get', 'input-group') );
	$form->add( new interf_bal_smpl('span', 'Modifier le taux de taxe pour', false, 'input-group-addon') );
	$sel = $form->add( new interf_select_form('taxe', false, false, 'form-control') );
	$debut = max($royaume->get_taxe() - 3, 0);
	$fin = $royaume->get_taxe() + 3;
	for($i = $debut; $i <= $fin; $i++)
	{
		$sel->add_option($i.' %', $i, $i==$royaume->get_taxe());
	}
	$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
	$btn = $btns->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
	$btn->set_attribut('onclick', 'return charger_formulaire(\'modif_taxe\');');
}


?>
