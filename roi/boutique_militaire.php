<?php
if (file_exists('../root.php'))
  include_once('../root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'inc/fp.php');


$perso = joueur::get_perso();
$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
if( ($perso->get_rang() != 6 && $royaume->get_ministre_militaire() != $perso->get_id()) || $royaume->is_raz() )
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
		$lieu = $bourg->has_bonus('royaume');
	}
}
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

$cadre = $G_interf->creer_royaume();

if( $action && $lieu && $perso->get_hp()>0 )
{
	switch($action)
	{
	case 'achat':
		$requete = "SELECT * FROM objet_royaume WHERE id = ".sSQL($_GET['id']);
		$nombre = $_GET['nombre'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		//Si c'est pour une bourgade on vérifie combien il y en a déjà
		if($row['type'] == 'bourg')
		{
			$nb_bourg = nb_bourg($R->get_id());
			$nb_case = nb_case($royaume->get_id());
			if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250))
			{
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il y a déjà trop de bourg sur votre royaume.');
				break;
			}
		}
		//On vérifie les stars
		if($royaume->get_star() < ($row['prix'] * $nombre))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume n\'a pas assez de stars');
			break;
		}
		//On vérifie les ressources
		if(($royaume->get_pierre() < $row['pierre'] * $nombre) || ($royaume->get_bois() < $row['bois'] * $nombre) || ($royaume->get_eau() < $row['eau'] * $nombre) || ($royaume->get_charbon() < $row['charbon'] * $nombre) || ($royaume->get_sable() < $row['sable'] * $nombre) || ($royaume->get_essence() < $row['essence'] * $nombre))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous manque des ressources !');
			break;
		}
		$i = 0;
		while($i < $nombre)
		{
			//Achat
			/// @todo passer à l'objet
			$requete = "INSERT INTO depot_royaume VALUES (NULL, ".$row['id'].", ".$royaume->get_id().")";
			$db->query($requete);
			//On rajoute un bourg au compteur
			if($row['type'] == 'bourg')
			{
				$royaume->set_bourg($royaume->get_bourg() + 1);
			}
			//On enlève les stars au royaume
			$royaume->set_star($royaume->get_star() - $row['prix']);
			$royaume->set_eau($royaume->get_eau() - $row['eau']);
			$royaume->set_pierre($royaume->get_pierre() - $row['pierre']);
			$royaume->set_bois($royaume->get_bois() - $row['bois']);
			$royaume->set_sable($royaume->get_sable() - $row['sable']);
			$royaume->set_essence($royaume->get_essence() - $row['essence']);
			$royaume->set_charbon($royaume->get_charbon() - $row['charbon']);
			$royaume->sauver();
			$i++;
		}
		if($nombre > 1)
		{
			/// @todo mettre le pluriel en bddd
			$tab = array("Drapeau"=>"Drapeaux","Poste avancé"=>"Postes avancés", "Fortin"=>"Fortins", "Fort"=>"Forts", "Forteresse"=>"Forteresses", "Tour de guet"=>"Tours de guet", "Tour de garde"=>"Tours de garde", "Tour de mages"=>"Tours de mages", "Tour d archers"=>"Tours d'archers", "Bourgade"=>"Bourgades", "Palissade"=>"Palissades", "Mur"=>"Murs", "Muraille"=>"Murailles", "Grande muraille"=>"Grandes murailles", "Bélier"=>"Béliers", "Catapulte"=>"Catapultes", "Trébuchet"=>"Trébuchets", "Baliste"=>"Balistes", "Grand drapeau"=>"Grands drapeaux", "Étendard"=>"Étendards", "Grand étendard"=>"Grands étendards", "Petit drapeau"=>"Petits drapeaux") ; 
			if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
				interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$row['nom']].' bien achetées');
			else
				interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$row['nom']].' bien achetés');
     
     }
	   else
	   {
       if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$row['nom'].' bien achetée');
       else
					interf_alerte::enregistre(interf_alerte::msg_succes, $row['nom'].' bien acheté');
	   }
	   break;
	}
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_boutique_mil($royaume, $lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();



?>