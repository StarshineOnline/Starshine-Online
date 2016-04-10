<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( ($perso->get_rang() != 6 && $royaume->get_ministre_economie() != $perso->get_id()) || $royaume->is_raz() )
{
	/// @todo logguer triche
	exit;
}

$lieu = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id());
if( !$lieu && $batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
{
	if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
	{
		$bourg = new batiment($batiment['id_batiment']);
		$constr = new construction($batiment['id_batiment']);
		$lieu = $bourg->has_bonus('royaume') && !$constr->is_buff('assaut');
	}
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;
//$carte = array_key_exists('carte', $_GET) ? $_GET['carte'] : null;
$ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
$carte = array_key_exists('bourg', $_GET) ? $_GET['bourg'] : null;

$cadre = $G_interf->creer_royaume();

if( $action && $lieu && $perso->get_hp()>0 )
{
	switch($action)
	{
	case 'renommer':
		$constr = new construction($_GET['id']);
	  include_once(root.'interface/interf_bat_drap.class.php');
		$cadre->set_dialogue( new interf_batiment_nom($constr) );
		if( $ajax )
			exit;
		break;
	case 'modif_nom':
		$constr = new construction($_GET['id']);
		$constr->set_nom( sSQL($_GET['nom'], SSQL_STRING) );
		$constr->sauver();
		break;
	case 'suppr_mine':
		$constr = new construction($_GET['id']);
		if( !$constr->get_buff('assiege') )
		{
			$constr->supprimer();
			journal_royaume::ecrire_perso('suppr_batiment', $constr->get_def(), $constr->get_nom(), $constr->get_id(), $constr->get_x(), $constr->get_y());
		}
		break;
	case 'suppr_constr':
		$constr = new placement($_GET['id']);
		if( !$constr->get_buff('assiege') )
		{
			$constr->supprimer();
			journal_royaume::ecrire_perso('suppr_batiment', $constr->get_def(), $constr->get_nom(), $constr->get_id(), $constr->get_x(), $constr->get_y());
		}
		break;
	case 'suppr_bourg':
		$bourg = new bourg($_GET['id']);
		if( !$bourg->get_buff('assiege') )
		{
			$bourg->supprimer();
			journal_royaume::ecrire_perso('suppr_batiment', $bourg->get_def(), $bourg->get_nom(), $bourg->get_id(), $bourg->get_x(), $bourg->get_y());
			$bourg->get_mines();
			foreach($bourg->mines as $mine)
			{
				$mine->supprimer();
			}
		}
		break;
	case 'voir':
		$carte = $_GET['id'];
		if( $ajax )
		{
			$bourg = new construction($carte);
			$cadre->add_section('carte_bourg', new interf_carte($bourg->get_x(), $bourg->get_y(), interf_carte::aff_gest_bourgs, 5, 'carte'));
		}
		break;
	case 'ameliorer':
		$construction = new construction(sSQL($_GET['id']));
		$ancien_batiment = new batiment($construction->get_id_batiment());
		$batiment = new batiment($ancien_batiment->get_suivant());
		///@ todo loguer triche
		if( !$ancien_batiment->get_suivant() )
			exit;
		if( $batiment->get_cond1() > (time() - $construction->get_date_construction()) )
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas encore améliorer ce bâtiment');
			break;
		}
		if( $royaume->get_star() < $batiment->get_cout())
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars');
			break;
		}
		// On modifie la contruction
		$construction->set_id_batiment($batiment->get_id());
		$construction->set_nom($batiment->get_nom());
		$construction->set_date_construction(time());
		$construction->set_hp($construction->get_hp() + $batiment->get_hp() - $ancien_batiment->get_hp());
		$construction->set_point_victoire($batiment->get_point_victoire());
		$construction->sauver();
		$royaume->set_star($royaume->get_star() - $batiment->get_cout());
		$royaume->sauver();
		interf_alerte::enregistre(interf_alerte::msg_succes, 'La construction a été correctement améliorée');
		break;
	case 'case':
		include_once(root.'interface/interf_gestion_mines.class.php');
		$cadre->set_dialogue( new interf_bourg_case(new bourg($_GET['bourg']), $_GET['x'], $_GET['y'], $royaume) );
		if( $ajax )
			exit;
		break;
	case 'construire':
		$bourg = new bourg($_GET['bourg']);
	
		if($bourg->get_mine_total() >= $bourg->get_mine_max())
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Construction impossible, ce bourg ne peut plus avoir de mine associée');
			break;
		}
		/// @todo vérifier sile terrain appartient bien au royaume et s'il est libre
		/// @todo passer à l'objet
		$requete = "SELECT nom, hp,temps_construction, cout FROM batiment WHERE id = ".sSQL($_GET['mine']);
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
	
		//On vérifie si on a assez de stars
		if($royaume->get_star() < $row['cout'])
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars');
			break;
		}
		$distance = calcul_distance(convert_in_pos($Trace[$royaume->get_race()]['spawn_x'], $Trace[$royaume->get_race()]['spawn_y']), convert_in_pos($_GET['x'], $_GET['y']));
		$time = time() + ($row['temps_construction'] * $distance);

		$placement = new placement();
		$placement->set_royaume($royaume->get_id());
		$placement->set_id_batiment($_GET['mine']);
		$placement->set_x($_GET['x']);
		$placement->set_y($_GET['y']);
		$placement->set_hp($row['hp']);
		$placement->set_nom($row['nom']);
		$placement->set_rez($_GET['bourg']);
		$placement->set_type('mine');
		$placement->set_debut_placement(time()); // Sans ća l'acceleration est trop forte
		$placement->set_fin_placement($time);
		$placement->sauver();
		
		//On enlève les stars au royaume
		$requete = "UPDATE royaume SET star = star - ".$row['cout']." WHERE ID = ".$royaume->get_id();
		$db->query($requete);
		interf_alerte::enregistre(interf_alerte::msg_succes, $placement->get_nom().' bien construit.');
		break;
	}
}

$cadre->set_gestion( $G_interf->creer_gestion_mines($royaume, $lieu && $perso->get_hp()>0, $carte) );
$cadre->maj_tooltips();


?>
