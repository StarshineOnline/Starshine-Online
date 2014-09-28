<?php
/**
* @file terrain.php
* Terrains des joueurs
*/
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');

$interf_princ = $G_interf->creer_jeu();
//Vérifie si le perso est mort
$perso = joueur::get_perso();
$perso->check_perso();
$interf_princ->verif_mort($perso);

// Royaume
///@todo à améliorer
$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$perso->get_x().' and y = '.$perso->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);

// On vérifie qu'on est bien sur une ville
/// @todo logguer triche
if($W_row['type'] != 1)
	exit;

// On vérifie la diplomatie
/// @todo logguer triche
if( $R->get_diplo($perso->get_race()) != 127 && $R->get_diplo($perso->get_race()) >= 7 )
	exit;

// Ville rasée
/// @todo logguer triche
if ($R->is_raz() && $perso->get_x() <= 190 && $perso->get_y() <= 190)
	exit; //echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";

$action = array_key_exists('action', $_GET) ? $_GET['action'] : false;
$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
/// @todo ajouter des vérifications et des messages
switch($action)
{
case 'onglet':
	$construction = new terrain_construction($id);
	$batiment = $construction->get_batiment();
	switch($batiment->type)
	{
	case 'coffre':
		$interf_princ->add( $G_interf->creer_coffre($R, $construction) );
		break;
	case 'laboratoire':
		$interf_princ->add( $G_interf->creer_laboratoire($R, $construction) );
		break;
	case 'grenier':
		$interf_princ->add( $G_interf->creer_grenier($R, $construction) );
		break;
	}
	exit;
case 'deposer':
	$construction = new terrain_construction($id);
	$batiment = $construction->get_batiment();
	$coffre = new coffre($construction->id);
	$coffre_inventaire = $coffre->get_coffre_inventaire();
	if(count($coffre_inventaire) < $batiment->effet)
	{
		$item = $perso->get_inventaire_slot_partie($_GET['index']);
		$objet = decompose_objet($item);
		//On le met dans le coffre
		$coffre->depose_objet($objet);
		//On supprime l'objet
		$perso->supprime_objet($item, 1);

		$coffre_inventaire = $coffre->get_coffre_inventaire();
		$perso->check_perso();
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de place dans le coffre.');
	break;
case 'prendre':
	$construction = new terrain_construction($id);
	$coffre = new coffre($construction->id);
	$coffre_inventaire = $coffre->get_coffre_inventaire();
	$item = $coffre_inventaire[$_GET['index']];
	if($perso->prend_objet($item->objet))
	{
		$item->moins();
		$coffre_inventaire = $coffre->get_coffre_inventaire();
		$perso->check_perso();
	}
	else
		interf_alerte::enregistre(interf_alerte::msg_erreur, $G_erreur);
	break;
case 'achat_instr':
	$instrument = new craft_instrument($_GET['id_instr']);
	$taxe = round($R->get_taxe_diplo($perso->get_race()) * $instrument->prix / 100);
	$prix = $instrument->prix + $taxe;
	if($prix > 0)
	{
		if($perso->get_star() >= $prix)
		{
			$laboratoire = new terrain_laboratoire();
			$laboratoire->id_laboratoire = $id;
			$laboratoire->id_instrument = $instrument->id;
			$laboratoire->type = $instrument->type;
			$laboratoire->sauver();
			$perso->set_star($perso->get_star() - $prix);
			$perso->sauver();
			$R->add_star_taxe($taxe, 'terrain');
			$R->sauver();
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars.');
	}
	break;
case 'ameliore_instr':
			$instrument = new craft_instrument($_GET['id_instr']);
			$taxe = round($R->get_taxe_diplo($perso->get_race()) * $instrument->prix / 100);
			$prix = $instrument->prix + $taxe;
			if($prix > 0)
			{
				if($perso->get_star() >= $prix)
				{
					$requete = "SELECT id, id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE type = '".$instrument->type."' AND id_laboratoire = ".$id;
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$laboratoire = new terrain_laboratoire($row);
					$laboratoire->id_instrument = $instrument->id;
					$laboratoire->sauver();
					$perso->set_star($perso->get_star() - $prix);
					$perso->sauver();
					$R->add_star_taxe($taxe, 'terrain');
					$R->sauver();
				}
				else
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars');
			}
	break;
case 'chantier':
	$terrain = new terrain();
	$terrain = $terrain->recoverByIdJoueur($perso->get_id());
	$batiment = new terrain_batiment($_GET['construire']);
	$star_point = ceil($_GET['star_point']);
	$cout_total = $batiment->point_structure * $star_point;
	if($cout_total > 0)
	{
		if($perso->get_star() >= $cout_total)
		{
			if($batiment->nb_case <= $terrain->place_restante())
			{
				//On lance le chantier
				$chantier = new terrain_chantier();
				$chantier->id_batiment = $batiment->id;
				$chantier->id_terrain = $terrain->id;
				$chantier->star_point = $star_point;
				$chantier->sauver();
				//On supprime les stars du joueur
        $perso->add_star( -$cout_total );
        $perso->sauver();
				$taxe = floor(($chantier->star_point * $batiment->point_structure) * $R->get_taxe_diplo($perso->get_race()) / 100);
				//On donne les stars au royaume
				$R->add_star_taxe($taxe, 'terrain');
				interf_alerte::enregistre(interf_alerte::msg_succes, 'Le chantier a commencé !');
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de place.');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars.');
	}
	break;
}
$interf_princ->set_gauche( $G_interf->creer_terrain($R, $id) );
$interf_princ->maj_tooltips();


?>
