<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( $perso->get_rang() != 6 || $royaume->is_raz() )
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
		$constr = new construction($batiment['id_batiment'])
		$lieu = $bourg->has_bonus('royaume') && !$constr->is_buff('assaut');
	}
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

$cadre = $G_interf->creer_royaume();

if( $action && $lieu && $perso->get_hp()>0 )
{
	switch($action)
	{
	case 'utilise':
		$action = new point_victoire_action($_GET['id']);
		/// @todo passer à l'objet
		if($royaume->get_point_victoire() >= $action->get_cout())
		{
			$persos = "SELECT id FROM perso WHERE race = '".$royaume->get_race()."' AND statut = 'actif'";
		}
		else
		{
				interf_alerte::enregistre(interff_alerte::msg_erreur, 'Vous n\'avez pas assez de point de victoire');
				break;
		}
		switch($action->get_type())
		{
			case 'famine' :
				$requete = "DELETE FROM buff WHERE type = 'famine' AND id_perso IN ($persos)";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, 'La famine a bien été supprimée');
				break;
			case 'remove_buff' :
				$requete = "DELETE FROM buff WHERE type = '".$action->get_type_buff()."' AND id_perso IN ($persos)";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, $action->get_type_buff().' a bien été supprimé(e)');
				break;
			case 'remove_buffs' :
				$requete = "DELETE FROM buff WHERE type IN (".$action->get_type_buff().") AND id_perso IN ($persos)";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, $action->get_type_buff().' a bien été supprimé(e)');
				break;
			case 'buff' :
			  $requete = "INSERT INTO buff(`type`, `effet`, `effet2`, `fin`, `duree`, `id_perso`, `nom`, `description`, `debuff`, `supprimable`)
								SELECT '".$action->get_type_buff()."', ".$action->get_effet().", 0, ".(time()+$action->get_duree()).", ".$action->get_duree().", id, '".$action->get_nom()."', '".addslashes($action->get_description())."', 1, 0
                FROM ($persos) persos";
				$db->query($requete);
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Votre royaume bénéficie maintenant du buff : '.$action->get_nom());
			break;
		}
		$royaume->set_point_victoire($royaume->get_point_victoire() - $action->get_cout());
		$royaume->sauver();
		$cadre->maj_royaume();
		break;
	}
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_points_victoire($lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();

?>
