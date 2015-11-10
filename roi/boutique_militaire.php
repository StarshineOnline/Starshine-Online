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
		$obj = new objet_royaume(sSQL($_GET['id']));
		$nombre = $_GET['nombre'];
		//Si c'est pour une bourgade on vérifie combien il y en a déjà
		if($obj->get_type() == 'bourg')
		{
			$nb_bourg = nb_bourg($royaume->get_id());
			$nb_case = nb_case($royaume->get_id());
			if(($nb_bourg + $nombre - 1) >= ceil($nb_case / 250))
			{
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il y a déjà trop de bourg sur votre royaume.');
				break;
			}
		}
		//On vérifie les stars
		if($royaume->get_star() < ($obj->get_prix() * $nombre))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Le royaume n\'a pas assez de stars');
			break;
		}
		$facteur = $royaume->get_facteur_entretien();
		//On vérifie les ressources
		if(($royaume->get_pierre() < $obj->get_pierre($facteur) * $nombre) || ($royaume->get_bois() < $obj->get_bois($facteur) * $nombre) || ($royaume->get_eau() < $obj->get_eau($facteur) * $nombre) || ($royaume->get_charbon() < $obj->get_charbon($facteur) * $nombre) || ($royaume->get_sable() < $obj->get_sable($facteur) * $nombre) || ($royaume->get_essence() < $obj->get_essence($facteur) * $nombre))
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous manque des ressources !');
			break;
		}
		$i = 0;
		while($i < $nombre)
		{
			//Achat
			/// @todo passer à l'objet
			$requete = "INSERT INTO depot_royaume VALUES (NULL, ".$obj->get_id().", ".$royaume->get_id().")";
			$db->query($requete);
			//On rajoute un bourg au compteur
			if($row['type'] == 'bourg')
			{
				$royaume->set_bourg($royaume->get_bourg() + 1);
			}
			//On enlève les stars au royaume
			$royaume->set_star($royaume->get_star() - $obj->get_prix());
			$royaume->set_eau($royaume->get_eau() - $obj->get_eau($facteur));
			$royaume->set_pierre($royaume->get_pierre() - $obj->get_pierre($facteur));
			$royaume->set_bois($royaume->get_bois() - $obj->get_bois($facteur));
			$royaume->set_sable($royaume->get_sable() - $obj->get_sable($facteur));
			$royaume->set_essence($royaume->get_essence() - $obj->get_essence($facteur));
			$royaume->set_charbon($royaume->get_charbon() - $obj->get_charbon($facteur));
			$royaume->sauver();
			$i++;
		}
		if($nombre > 1)
		{
			/// @todo mettre le pluriel en bddd
			$tab = array("Drapeau"=>"Drapeaux","Poste avancé"=>"Postes avancés", "Fortin"=>"Fortins", "Fort"=>"Forts", "Forteresse"=>"Forteresses", "Tour de guet"=>"Tours de guet", "Tour de garde"=>"Tours de garde", "Tour de mages"=>"Tours de mages", "Tour d archers"=>"Tours d'archers", "Bourgade"=>"Bourgades", "Palissade"=>"Palissades", "Mur"=>"Murs", "Muraille"=>"Murailles", "Grande muraille"=>"Grandes murailles", "Bélier"=>"Béliers", "Catapulte"=>"Catapultes", "Trébuchet"=>"Trébuchets", "Baliste"=>"Balistes", "Grand drapeau"=>"Grands drapeaux", "Étendard"=>"Étendards", "Grand étendard"=>"Grands étendards", "Petit drapeau"=>"Petits drapeaux") ; 
			if( in_array($obj->get_nom(), array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
				interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$obj->get_nom()].' bien achetées');
			else
				interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$tab[$obj->get_nom()].' bien achetés');
     
     }
	   else
	   {
       if( in_array($row['nom'], array('Forteresse', 'Tour de guet', 'Tour de garde', 'Tour de mages', 'Tour d archers', 'Bourgade', 'Palissade', 'Muraille', 'Grande muraille', 'Catapulte', 'Baliste')) )
					interf_alerte::enregistre(interf_alerte::msg_succes, $nombre.' '.$obj->get_nom().' bien achetée');
       else
					interf_alerte::enregistre(interf_alerte::msg_succes, $obj->get_nom().' bien acheté');
	   }
		 journal_royaume::ecrire_perso('achat', $obj, '', $nombre);
	   break;
	}
}

$cont = $cadre->set_gestion( new interf_bal_cont('div') );
interf_alerte::aff_enregistres($cont);
$cont->add( $G_interf->creer_boutique_mil($royaume, $lieu && $perso->get_hp()>0) );
$cadre->maj_tooltips();



?>