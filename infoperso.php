<?php // -*- tab-width:2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
  

if( array_key_exists('action', $_GET) && $_GET['action'] == 'infos_rez' )
{
	$princ = new interf_princ_ob();
	$liste = $princ->add( new interf_bal_cont('ul', false, 'list-group') );
	/// TODO: passer à l'objet
	$requete = 'SELECT * FROM rez WHERE id_perso = '.$_GET['id'];
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$li = $liste->add( new interf_bal_cont('li', false, 'list-group-item') );
		$li->add( new interf_jauge_bulle(false, $row['pourcent'], 100, false, 'mp', false, 'jauge_groupe') )->set_tooltip('MP : '.$row['pourcent'].'%');
		$li->add( new interf_jauge_bulle(false, $row['pourcent'], 100, false, 'hp', false, 'jauge_groupe') )->set_tooltip('HP : '.$row['pourcent'].'%');
		$li->add( new interf_bal_smpl('span', $row['nom_rez']) );
		interf_base::code_js('maj_tooltips();');
	}
	exit;
}

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$interf_princ->verif_mort($perso);

$perso = joueur::get_perso();
$pj = new perso($_GET['id']);
$pj->check_materiel();
//Calcul de la distance qui sépare le joueur du monstre
$distance = $perso->calcule_distance($pj);
/// TODO: à améliorer
if( ($perso->get_groupe() == 0 || $perso->get_groupe() != $pj->get_groupe() ) && ($distance > 3 || ($distance == 3 &&  $perso->get_y() > 190)) )
	exit();

// nom
$titre_perso = new titre($_GET['id']);
$bonus = recup_bonus($_GET['id']);
$titre = $titre_perso->get_titre_perso($bonus);
$nom = $titre[0].' '.ucwords($pj->get_nom()).' '.$titre[1];

// Cadre de la partie droite
$cadre = $interf_princ->set_droite( $G_interf->creer_droite($nom) );
$cadre->add( $G_interf->creer_info_perso($pj, true) );
$interf_princ->maj_tooltips();



?>
