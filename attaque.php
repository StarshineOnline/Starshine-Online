<?php // -*- tab-width:2; mode: php -*- 
/**
 * @file attaque.php
 * Gestion des combats 
 */ 
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

$perso = &joueur::get_perso();
$interf_princ = $G_interf->creer_jeu();
$interf_princ->verif_mort($perso);


$check_pet = array_key_exists('pet', $_GET) && $perso->nb_pet() > 0;
$donj = is_donjon($perso->get_x(), $perso->get_y()) && $perso->in_arene('and donj = 0') == false && $perso->get_y()>190;
$type = $_GET['type'];

// Ivresse
$ivresse = $perso->get_buff('ivresse');
if( $ivresse )
{
	if( comp_sort::test_de(100, $ivresse->get_effet()) )
	{
		$cibles = array();
		switch($type)
		{
		case 'perso':
		case 'monstre':
		case 'batiment':
			/// @todo passer à l'objet
			switch($type)
			{
			case 'perso':
				$requete = 'SELECT x, y FROM perso WHERE id = '.$_GET['id_perso'];
				break;
			case 'monstre':
				$requete = 'SELECT x, y FROM map_monstre WHERE id = '.$_GET['id_monstre'];
				break;
			case 'batiment':
				$requete = 'SELECT x, y FROM construction WHERE id = '.$_GET['id_batiment'];
			}
			$req = $db->query($requete);
			$pos = $db->read_assoc($req);
			$requete = 'SELECT id FROM construction WHERE type != "bourg" AND x = '.$pos['x'].' AND y = '.$pos['y'];
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'batiment', 'table'=>'construction', 'id'=>$row[0]);
			$requete = 'SELECT id FROM placement WHERE x = '.$pos['x'].' AND y = '.$pos['y'];
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'batiment',  'table'=>'placement', 'id'=>$row[0]);
			$requete = 'SELECT id FROM perso WHERE x = '.$pos['x'].' AND y = '.$pos['y'];
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'perso', 'id'=>$row[0]);
			$requete = 'SELECT id FROM map_monstre WHERE x = '.$pos['x'].' AND y = '.$pos['y'];
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'monstre', 'id'=>$row[0]);
			break;
		case 'siege':
		case 'ville':
			$x_min = $perso->get_x() - 1;
			$x_max = $perso->get_x() + 1;
			$y_min = $perso->get_y() - 1;
			$y_max = $perso->get_y() + 1;
			$requete = 'SELECT id FROM construction WHERE x BETWEEN '.$x_min.' AND '.$x_max.' AND y = '.$y_min.' AND '.$y_max;
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'siege', 'table'=>'construction', 'id'=>$row[0]);
			$requete = 'SELECT id FROM placement WHERE x BETWEEN '.$x_min.' AND '.$x_max.' AND y = '.$y_min.' AND '.$y_max;
			$req = $db->query($requete);
			while( $row = $db->read_array($req) )
				$cibles[] = array('type'=>'siege', 'table'=>'placement', 'id'=>$row[0]);
			$requete = 'SELECT map.x as x, map.y as y, nom, race, map.royaume FROM map LEFT JOIN royaume ON map.royaume = royaume.id WHERE map.x BETWEEN '.$x_min.' AND '.$x_max.' AND map.y BETWEEN '.$y_min.' AND '.$y_max.' AND type = 1 AND royaume.fin_raz_capitale = 0';
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			if( $row )
				$cibles[] = array('type'=>'ville', 'id'=>convert_in_pos($row['x'], $row['y']));
		}
		$ind = rand(0, count($cibles)-1);
		$type = $cibles[$ind]['type'];
		switch($type)
		{
		case 'perso':
			$_GET['id_perso'] = $cibles[$ind]['id'];
			break;
		case 'monstre':
			$_GET['id_monstre'] = $cibles[$ind]['id'];
			break;
		case 'batiment':
		case 'siege':
			$_GET['id_batiment'] = $cibles[$ind]['id'];
			$_GET['table'] = $cibles[$ind]['table'];
			break;
		case 'ville':
			$_GET['id_ville'] = $cibles[$ind]['id'];
		}
	}
}


switch($type)
{
case 'perso' :
  if ($_SESSION['ID'] == $_GET['id_perso'])
    security_block(URL_MANIPULATION, 'Auto-attaque prohibée');
	if(!$check_pet)
	{
		$perso = new perso($_SESSION['ID']);
		$perso->check_perso();
		$perso->action_do = $perso->recupaction('attaque');
		$attaquant = entite::factory('perso', $perso);
	}
	else
	{
		$attaquant = entite::factory('pet', $perso->get_pet(), $perso, true);
	}
	$def = false;
	$perso_defenseur = new perso($_GET['id_perso']);
	$perso_defenseur->check_perso(false);
	//On vérifie que ya pas un buff qui fait défendre par un pet
	if($perso_defenseur->is_buff('defense_pet'))
	{
		$pet = $perso_defenseur->get_pet();
		if (!$pet || $pet->get_hp() < 1)
		{
			$check_pet_def = false;
			interf_debug::enregistre('Le pet est mort…');
		}
		else
		{
			$defense = $perso_defenseur->get_buff('defense_pet', 'effet');
			$collier = decompose_objet($perso_defenseur->get_inventaire_partie('cou'));		
			if ($collier != '')
			{
				$requete = "SELECT * FROM armure WHERE ID = ".$collier['id_objet'];
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$effet = explode('-', $row['effet']);
				if ($effet[0] == '20')
				{
					$defense = $defense + $effet[1];
				}
			}
			$rand = rand(0, 100);
			//Défense par le pet
			interf_debug::enregistre('Defense par le pet: '.$rand.' VS '.$defense);
			if($rand < $defense)
			{
				interf_debug::enregistre('Defense par le pet OK');
				$check_pet_def = true;
			}
		}
	}
	//Si il est en train de dresser un mob, le dressage est arrêté
	if($perso_defenseur->is_buff('dressage'))
	{
		$buff_def = $perso_defenseur->get_buff('dressage');
		$buff_def->supprimer();
	}
	if(!$check_pet_def)
	{
		$perso_defenseur->action_do = $perso_defenseur->recupaction('defense');
		$defenseur = entite::factory('perso', $perso_defenseur);
	}
	else
	{
		$defenseur = entite::factory('pet', $perso_defenseur->get_pet(), $perso_defenseur, false);
	}
	break;
case 'monstre' :
	if(!$check_pet)
	{
		if (!$donj)
		{
			$perso = new perso($_SESSION['ID']);
			$perso->check_perso();
			$perso->action_do = $perso->recupaction('attaque');
			$attaquant = entite::factory('perso', $perso);
		}
		else
		{
			//On vérifie que ya pas un buff qui fait défendre par un pet
			if($perso->is_buff('defense_pet'))
			{
				$pet = $perso->get_pet();
				if (!$pet || $pet->get_hp() < 1)
				{
					$check_pet_donj = false;
					interf_debug::enregistre('Le pet est mort ...');
				}
				else
				{
					$attaque_donj = $perso->get_buff('defense_pet', 'effet');
					$rand = rand(0, 100);
					//Défense par le pet
					interf_debug::enregistre('Defense par le pet: '.$rand.' VS '.$defense);
					if($rand < $attaque_donj)
					{
						interf_debug::enregistre('Defense par le pet OK');
						$check_pet_donj = true;
					}
				}
			}
			
			if(!$check_pet_donj)
			{
				$perso->action_do = $perso->recupaction('defense');
				$attaquant = entite::factory('perso', $perso);
			}
			else
			{
				$attaquant = entite::factory('pet', $perso->get_pet(), $perso);
			}
		}
	}
	else
	{
		$attaquant = entite::factory('pet', $perso->get_pet(), $perso);
	}
	$map_monstre = new map_monstre($_GET['id_monstre']);
	$map_monstre->check_monstre();
	if ($map_monstre->nonexistant)
	{
		$interf_princ->set_droite( new interf_alerte(interf_alerte::msg_erreur, true, false, 'Ce monstre est déjà au paradis des monstres') );
		exit (0);
	}
	$perso_defenseur = new monstre($map_monstre->get_type());
	$defenseur = entite::factory('monstre', $map_monstre);
	$diff_lvl = abs($perso->get_level() - $defenseur->get_level());
	break;
case 'batiment' :
	if ($perso->is_buff('debuff_rvr')) $no_rvr = true;
	$perso = new perso($_SESSION['ID']);
  $perso->check_perso();
	if(!$check_pet)
	{
		$perso->action_do = $perso->recupaction('attaque');
		$attaquant = entite::factory('perso', $perso);
	}
	else
	{
		$attaquant = entite::factory('pet', $perso->get_pet(), $perso);
	}
	if($_GET['table'] == 'construction') $map_batiment = new construction($_GET['id_batiment']);
	else $map_batiment = new placement($_GET['id_batiment']);
	$defenseur = entite::factory('batiment', $map_batiment, $perso);
	break;
case 'siege' :
	if ($perso->is_buff('debuff_rvr')) $no_rvr = true;
	$map_siege = new arme_siege($_GET['id_arme_de_siege']);
	if($_GET['table'] == 'construction')
	{
		$map_batiment = new construction($_GET['id_batiment']);
		$id_constr = $_GET['id_batiment'];
		$id_plac = 0;
	}
	else
	{
		$map_batiment = new placement($_GET['id_batiment']);
		$id_constr = 0;
		$id_plac = $_GET['id_batiment'];
	}
	/// debuff empêchant la suppression du bâtiment
	$buff = new buff_batiment_def( buff_batiment_def::id_assiege );
	$buff->lance($id_constr, $id_plac);
	$perso = new perso($_SESSION['ID']);
  $perso->check_perso();
	$siege = new batiment($map_siege->get_id_batiment());
	$defenseur = entite::factory('batiment', $map_batiment);
	$attaquant = entite::factory('siege', $map_siege, $perso, true, $defenseur);
	break;
case 'ville' :
	if ($perso->is_buff('debuff_rvr')) $no_rvr = true;
	$map_siege = new arme_siege($_GET['id_arme_de_siege']);
	$perso = new perso($_SESSION['ID']);
  $perso->check_perso();
	$map_case = new map_case($_GET['id_ville']);
	$map_royaume = new royaume($map_case->get_royaume());
	$map_royaume->verif_hp();
	$siege = new batiment($map_siege->get_id_batiment());
	$coord = convert_in_coord($_GET['id_ville']);
	$map_royaume->x =$coord['x'];
	$map_royaume->y =$coord['y'];
	$defenseur = entite::factory('ville', $map_royaume);
	$attaquant = entite::factory('siege', $map_siege, $perso, true, $defenseur);
	if ($map_royaume->is_raz())
	{
		$interf_princ->set_droite( new interf_alerte(interf_alerte::msg_erreur, true, false, 'Cette ville est déjà mise à sac') );
		exit (0);
	}
	break;
}

//Achievement
if(!$check_pet AND ($type == "perso" OR $type == "monstre") AND $attaquant->action == $defenseur->action)
	$perso->unlock_achiev('same_action');

// round_total sera modifié ensuite au besoin, mais on doit le seter au début
$distance = $attaquant->calcule_distance($defenseur);

$cadre = $interf_princ->set_droite( $G_interf->creer_droite('Combat VS '.$defenseur->get_nom()) );

interf_debug::aff_enregistres($cadre);

/*if( is_donjon($perso->get_x(), $perso->get_y()) && ($perso->in_arene('and donj = 0') == false) && $perso->get_y()>190 )
{
	$W_case = convertd_in_pos($defenseur->get_x(), $defenseur->get_y());
	$distance = detection_distance($W_case, convertd_in_pos($attaquant->get_x(), $attaquant->get_y()));
	//Un monstre attaque pas de PA pour attaquer
	if(array_key_exists('attaque_donjon', $_SESSION) AND $_SESSION['attaque_donjon'] == 'ok')
	{
		$no_pa_attaque = true;
		unset($_SESSION['attaque_donjon']);
		$distance--;
		if($distance < 0 ) $distance = 0;
	}
}*/

$dist_tir_att = $attaquant->get_distance_tir();
//On vérifie si l'attaquant est sur un batiment offensif
/// @todo passer à l'objet
$requete = "SELECT id_batiment FROM construction WHERE x = ".$perso->get_x()." AND y = ".$perso->get_y()." AND royaume = ".$Trace[$perso->get_race()]['numrace'];
$req = $db->query($requete);
if($db->num_rows > 0)
{
	$row = $db->read_row($req);
	$batiment_off = new batiment($row[0]);
	//Augmentation de tir à distance
	if ($batiment_off->has_bonus('batiment_distance'))
		$attaquant->add_buff('batiment_distance', $batiment_off->get_bonus('batiment_distance'));
	//Augmentation de la distance de tir
	if ($batiment_off->has_bonus('batiment_incantation'))
		$attaquant->add_buff('batiment_incantation', $batiment_off->get_bonus('batiment_incantation'));
  if( $attaquant->get_arme_type() == 'arc' && $batiment_off->has_bonus('distance_arc') )
    $dist_tir_att += $batiment_off->get_bonus('distance_arc');
  else if( $attaquant->get_arme_type() == 'baton' && $batiment_off->has_bonus('distance_baton') )
    $dist_tir_att += $batiment_off->get_bonus('distance_baton');
}

if($perso->is_buff('repos_sage') && !$no_pa_attaque)
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes sous repos du sage, vous ne pouvez pas attaquer.') );
elseif($no_rvr)
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous ne pouvez pas attaquer pendant la trêve.') );
elseif($perso->is_buff('dressage'))
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous dressez un monstre, vous ne pouvez pas attaquer.') );
else if($distance > $dist_tir_att )
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes trop loin pour l\'attaquer !') );
elseif($attaquant->get_hp() <= 0 OR $defenseur->get_hp() <= 0)
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Un des protagonistes n\'a plus de points de vie.') );
elseif($perso->is_buff('petrifie'))
	$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous êtes pétrifié, vous ne pouvez pas attaquer.') );
else
{
	$R = new royaume($Trace[$perso->get_race()]['numrace']);
	if($type == 'perso')
	{
		//Récupération si la case est une ville et diplomatie
		$defenseur_en_defense = false;
		/// @todo passer à l'objet
		$requete = "SELECT type FROM map WHERE x = ".$defenseur->get_x()
			.' and y = '.$defenseur->get_y()." AND type = 1 AND royaume = ".
			$Trace[$defenseur->get_race()]['numrace'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			$cadre->add( new interf_alerte(interf_alerte::msg_info, true, false, 'Le défenseur est sur sa ville, application des bonus !') );
			$defenseur->add_buff('batiment_pp', 30);
			$defenseur->add_buff('batiment_pm', 16);
			$defenseur->add_buff('batiment_esquive', 50);
			$defenseur_en_defense = true;
		}
		//On vérifie si le défenseur est sur un batiment défensif
		if( !$defenseur->is_buff('sape') )
		{}
		/// @todo passer à l'objet
		$constr = construction::create(array('x','y','royaume'), array($defenseur->get_x(), $defenseur->get_y(), $Trace[$defenseur->get_race()]['numrace']))
		if( $constr && !$constr[0]->is_buff('sape') )
		{
			$batiment_def = new batiment( $constr[0]->get_id() );
			//Augmentation des chances d'esquiver
			if ($batiment_def->has_bonus('batiment_esquive'))
				$defenseur->add_buff('batiment_esquive', $batiment_def->get_bonus('batiment_esquive'));
			//Augmentation de la PP
			if ($batiment_def->has_bonus('batiment_pp'))
				$defenseur->add_buff('batiment_pp', $batiment_def->get_bonus('batiment_pp'));
			//Augmentation de la PM
			if ($batiment_def->has_bonus('batiment_pm'))
				$defenseur->add_buff('batiment_pm', $batiment_def->get_bonus('batiment_pm'));
			//Augmentation de l'incantation
			if ($batiment_def->has_bonus('batiment_incantation'))
				$defenseur->add_buff('batiment_incantation', $batiment_def->get_bonus('batiment_incantation'));
			//Augmentation du tir à distance
			if ($batiment_def->has_bonus('batiment_distance'))
				$defenseur->add_buff('batiment_distance', $batiment_def->get_bonus('batiment_distance'));
			$defenseur_en_defense = true;
		}
	} //fin $type = 'perso'

	$pa_attaque = $attaquant->get_cout_attaque($perso, $defenseur);
	if (isset($no_pa_attaque) && $no_pa_attaque == true)
		$pa_attaque = 0;

	$perso_true = false;
	$siege_true = false;
	$perso_true = ($type == 'perso' || $type == 'monstre' || $type == 'batiment') && $perso->get_pa() >= $pa_attaque;
	$siege_true = ($type == 'siege' || $type == 'ville') && $attaquant->peut_attaquer() && $perso->get_pa() >= $pa_attaque;
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer ou si l'arme de siege a assez de rechargement
	if ($perso_true || $siege_true)
	{
		//Suppresion de longue portée si besoin
		if($attaquant->is_buff('longue_portee') && $attaquant->get_arme_type() == 'arc')
		{
			/// @todo passer à l'objet
			$requete = "DELETE FROM buff WHERE id = ".$attaquant->get_buff('longue_portee', 'id');
			$db->query($requete);
		}
		//Gestion des points de crime
		if($type == 'perso')
		{
			$crime = false;
			/// @todo passer à l'objet
			$requete = "SELECT ".$defenseur->get_race()." FROM diplomatie WHERE race = '".$attaquant->get_race()."'";
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$pascrime = false;
			//Vérification si crime
			if(array_key_exists($row[0], $G_crime))
			{
				if($row[0] == 127)
				{
					$amende = recup_amende($perso_defenseur->get_id());
					if($amende)
					{
						if($amende['statut'] != 'normal') $pascrime = true;
					}
				}
				if(!$pascrime)
				{
					$crime = true;
					$points = ($G_crime[$row[0]] / 10);
					$perso->set_crime($perso->get_crime() + $points);
					$cadre->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'Vous attaquez un personnage en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime') );
				}
			}
		}

		/// @todo à améliorer
		$G_url->add('type', $type);
		switch($type)
		{
		case 'joueur':
		case 'perso':
			$G_url->add('id_perso', $defenseur->get_id());
			break;
		case 'monstre':
			$G_url->add('id_monstre', $map_monstre->get_id());
			break;
		case 'batiment':
			$G_url->add('id_batiment', $map_batiment->get_id());
			$G_url->add('table', $_GET['table']);
			break;
		}
		if ($check_pet)
			$G_url->add('pet', 1);
		interf_alerte::aff_enregistres($cadre);
    $attaque = new attaque($perso, $attaquant, $defenseur);
    $interf = $cadre->add( $G_interf->creer_combat() );
		$attaque->set_interface( $interf );
    $attaque->attaque($distance, $type, $pa_attaque, $R, $pet, $defenseur_en_defense);
		if( interf_debug::doit_aff_bouton() )
		{
			$lien = $interf->add( new interf_bal_smpl('a', '', 'debug_droit', 'icone icone-debug') );
			$lien->set_attribut('onclick', 'return debugs();');
		}

		//Suppression des PA si c'est une attaque du perso
		if ($type == 'perso' OR $type == 'monstre' OR $type == 'batiment') {
			$perso->set_pa($perso->get_pa() - $pa_attaque);
			$perso->sauver();
		}
		//Sinon c'est une arme de siège, et il faut modifier son rechargement
		elseif ($type == 'siege' OR $type == 'ville') {
			$perso->set_pa($perso->get_pa() - $pa_attaque);
			$perso->sauver();
		}
		$interf_princ->maj_perso();
		$interf_princ->maj_tooltips();

		//Mise dans les journaux si attaque pvp
		if($type == 'perso')
		{
			//Insertion de l'attaque dans les journaux des 2 joueurs
			
			//Journal de l'attaquant 
			if(!$check_pet)
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'attaque', '".mysql_escape_string(sSQL($perso->get_nom()))."', '".mysql_escape_string(sSQL($defenseur->get_nom()))."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
			else
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'attaque', '".mysql_escape_string(sSQL($attaquant->get_nom()))."', '".mysql_escape_string(sSQL($defenseur->get_nom()))."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
			$db->query($requete);
			// Creation du log du combat
			$combat = new combat();
			$combat->attaquant = $perso->get_id();
			$combat->defenseur = $perso_defenseur->get_id();
			$combat->combat = $attaque->get_log_combat();//$log_combat;
			$combat->id_journal = $db->last_insert_id();
			$combat->sauver();
			
			//Journal du défenseur
			if(!$check_pet_def)
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso_defenseur->get_id().", 'defense', '".mysql_escape_string($perso_defenseur->get_nom())."', '".mysql_escape_string($perso->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
			else
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso_defenseur->get_id().", 'defense', '".mysql_escape_string($defenseur->get_nom())."', '".mysql_escape_string($perso->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
			
			$db->query($requete);
			$combat = new combat();
			$combat->attaquant = $perso->get_id();
			$combat->defenseur = $perso_defenseur->get_id();
			$combat->combat = $attaque->get_log_combat();//$log_combat;
			$combat->id_journal = $db->last_insert_id();
			$combat->sauver();
			
			if($defenseur->get_hp() <= 0 && !$check_pet_def)
			{
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'tue', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($perso_defenseur->get_nom())."', NOW(), 0, 0, ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso_defenseur->get_id().", 'mort', '".mysql_escape_string($perso_defenseur->get_nom())."', '".mysql_escape_string($perso->get_nom())."', NOW(), 0, 0, ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
				$db->query($requete);
			}
			
			if($attaquant->get_hp() <= 0 && !$check_pet)
			{
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'mort', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($perso_defenseur->get_nom())."', NOW(), 0, 0, ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso_defenseur->get_id().", 'tue', '".mysql_escape_string($perso_defenseur->get_nom())."', '".mysql_escape_string($perso->get_nom())."', NOW(), 0, 0, ".$perso_defenseur->get_x().", ".$perso_defenseur->get_y().")";
				$db->query($requete);
			}
		}
		//Mise dans le journal si attaque sur batiment
		elseif($type == 'batiment')
		{
			$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'attaque', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($defenseur->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur->get_x().", ".$defenseur->get_y().")";
			$db->query($requete);
			// Creation du log du combat
			$combat = new combat();
			$combat->attaquant = $perso->get_id();
			$combat->defenseur = $defenseur->get_id();
			$combat->combat = $attaque->get_log_combat();
			$combat->id_journal = $db->last_insert_id();
			$combat->sauver();
			
			if($defenseur->get_hp() <= 0)
			{
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'destruction', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($defenseur->get_nom())."', NOW(), 0, 0, ".$defenseur->get_x().", ".$defenseur->get_y().")";
				$db->query($requete);
			}
		}
		elseif($type == 'ville')
		{
			$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'siege', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($map_royaume->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur->get_x().", ".$defenseur->get_y().")";
			$db->query($requete);
				
			// Creation du log du combat
			$combat = new combat();
			$combat->attaquant = $perso->get_id();
			$combat->defenseur = $defenseur->get_id();
			$combat->combat = $attaque->get_log_combat();//$log_combat;
			$combat->id_journal = $db->last_insert_id();
			$combat->sauver();
			
			if($map_royaume->is_raz())
			{
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'destruction', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($map_royaume->get_nom())."', NOW(), 0, 0, ".$defenseur->get_x().", ".$defenseur->get_y().")";
				$db->query($requete);
			}
		}
		elseif($type == 'siege')
		{
			$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'siege', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($defenseur->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur->get_x().", ".$defenseur->get_y().")";
			$db->query($requete);
				
			// Creation du log du combat
			$combat = new combat();
			$combat->attaquant = $perso->get_id();
			$combat->defenseur = $defenseur->get_id();
			$combat->combat = $attaque->get_log_combat();//$log_combat;
			$combat->id_journal = $db->last_insert_id();
			$combat->sauver();
			
			if($defenseur->get_hp() <= 0)
			{
				$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'destruction', '".mysql_escape_string($perso->get_nom())."', '".mysql_escape_string($defenseur->get_nom())."', NOW(), 0, 0, ".$defenseur->get_x().", ".$defenseur->get_y().")";
				$db->query($requete);
			}
		}
	}
	else
		$cadre->add( new interf_alerte(interf_alerte::msg_erreur, false, false, 'Vous n\'avez pas assez de points d\'actions.') );
}
?>
