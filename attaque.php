<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
//Si attaque via pet, on fait les verifs nécessaires
$check_pet = false;
$check_pet_def = false;
$log_combat = "";
$no_pa_attaque = false;
$joueur = new perso($_SESSION['ID']);
if(array_key_exists('pet', $_GET))
{
	if($joueur->nb_pet() > 0) $check_pet = true;
}
if(is_donjon($joueur->get_x(), $joueur->get_y())
	 && ($joueur->in_arene('and donj = 0') == false))
	 {
		 $donj = true;
	 }
$type = $_GET['type'];
switch($type)
{
	case 'joueur' :
		if(!$check_pet)
		{
			$joueur = new perso($_SESSION['ID']);
			$joueur->check_perso();
			$joueur->action_do = $joueur->recupaction('attaque');
			$attaquant = entite::factory('joueur', $joueur);
		}
		else
		{
			$attaquant = entite::factory('pet', $joueur->get_pet(), $joueur, true);
		}
		$def = false;
		$joueur_defenseur = new perso($_GET['id_joueur']);
		$joueur_defenseur->check_perso(false);
		//On vérifie que ya pas un buff qui fait défendre par un pet
		if($joueur_defenseur->is_buff('defense_pet'))
		{
			$pet = $joueur_defenseur->get_pet();
			if (!$pet || $pet->get_hp() < 1)
			{
				$check_pet_def = false;
				print_debug("Le pet est mort ...");
			}
			else
			{
				$defense = $joueur_defenseur->get_buff('defense_pet', 'effet');
				$collier = decompose_objet($joueur_defenseur->get_inventaire_partie('cou'));		
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
				print_debug("Defense par le pet: $rand VS $defense");
				if($rand < $defense)
				{
					print_debug("Defense par le pet OK");
					$check_pet_def = true;
				}
			}
		}
		//Si il est en train de dresser un mob, le dressage est arrêté
		if($joueur_defenseur->is_buff('dressage'))
		{
			$buff_def = $joueur_defenseur->get_buff('dressage');
			$buff_def->supprimer();
		}
		if(!$check_pet_def)
		{
			$joueur_defenseur->action_do = $joueur_defenseur->recupaction('defense');
			$defenseur = entite::factory('joueur', $joueur_defenseur);
		}
		else
		{
			$defenseur = entite::factory('pet', $joueur_defenseur->get_pet(), $joueur_defenseur, false);
		}
	break;
	case 'monstre' :
		if(!$check_pet)
		{
			if (!$donj)
			{
				$joueur = new perso($_SESSION['ID']);
				$joueur->check_perso();
				$joueur->action_do = $joueur->recupaction('attaque');
				$attaquant = entite::factory('joueur', $joueur);
			}
			else
			{
				//On vérifie que ya pas un buff qui fait défendre par un pet
				if($joueur->is_buff('defense_pet'))
				{
					$pet = $joueur->get_pet();
					if (!$pet || $pet->get_hp() < 1)
					{
						$check_pet_donj = false;
						print_debug("Le pet est mort ...");
					}
					else
					{
						$attaque_donj = $joueur->get_buff('defense_pet', 'effet');
						$rand = rand(0, 100);
						//Défense par le pet
						print_debug("Defense par le pet: $rand VS $defense");
						if($rand < $attaque_donj)
						{
							print_debug("Defense par le pet OK");
							$check_pet_donj = true;
						}
					}
				}
				
				if(!$check_pet_donj)
				{
					$joueur->action_do = $joueur->recupaction('defense');
					$attaquant = entite::factory('joueur', $joueur);
				}
				else
				{
					$attaquant = entite::factory('pet', $joueur->get_pet(), $joueur);
				}
			}
		}
		else
		{
			$attaquant = entite::factory('pet', $joueur->get_pet(), $joueur);
		}
		$map_monstre = new map_monstre($_GET['id_monstre']);
		$map_monstre->check_monstre();
		if ($map_monstre->nonexistant)
		{
			echo '<h5>Ce monstre est déjà au paradis des monstres</h5>';
			exit (0);
		}
		$joueur_defenseur = new monstre($map_monstre->get_type());
		$defenseur = entite::factory('monstre', $map_monstre);
		$diff_lvl = abs($joueur->get_level() - $defenseur->get_level());
	break;
	case 'batiment' :
		if ($joueur->is_buff('debuff_rvr')) $no_rvr = true;
		$joueur = new perso($_SESSION['ID']);
    $joueur->check_perso();
		if(!$check_pet)
		{
			$joueur->action_do = $joueur->recupaction('attaque');
			$attaquant = entite::factory('joueur', $joueur);
		}
		else
		{
			$attaquant = entite::factory('pet', $joueur->get_pet(), $joueur);
		}
		if($_GET['table'] == 'construction') $map_batiment = new construction($_GET['id_batiment']);
		else $map_batiment = new placement($_GET['id_batiment']);
		$defenseur = entite::factory('batiment', $map_batiment, $joueur);
	break;
	case 'siege' :
		if ($joueur->is_buff('debuff_rvr')) $no_rvr = true;
		$map_siege = new arme_siege($_GET['id_arme_de_siege']);
		if($_GET['table'] == 'construction') $map_batiment = new construction($_GET['id_batiment']);
		else $map_batiment = new placement($_GET['id_batiment']);
		$joueur = new perso($_SESSION['ID']);
    $joueur->check_perso();
		/*if($joueur->get_pa() >= 10)
		{*/
			$siege = new batiment($map_siege->get_id_batiment());
			$defenseur = entite::factory('batiment', $map_batiment);
			$attaquant = entite::factory('siege', $map_siege, $joueur, true, $defenseur);
		//}
	break;
	case 'ville' :
		if ($joueur->is_buff('debuff_rvr')) $no_rvr = true;
		$map_siege = new arme_siege($_GET['id_arme_de_siege']);
		$joueur = new perso($_SESSION['ID']);
    $joueur->check_perso();
		$map_case = new map_case($_GET['id_ville']);
		$map_royaume = new royaume($map_case->get_royaume());
		$map_royaume->verif_hp();
		$siege = new batiment($map_siege->get_id_batiment());
		$coord = convert_in_coord($_GET['id_ville']);
		$map_royaume->x =$coord['x'];
		$map_royaume->y =$coord['y'];
		$defenseur = entite::factory('ville', $map_royaume);
		$attaquant = entite::factory('siege', $map_siege, $joueur, true, $defenseur);
		if ($map_royaume->is_raz())
		{
			echo '<h5>Cette ville est déjà mise à sac</h5>';
			exit (0);
		}
	break;
}

//Achievement
if(!$check_pet AND ($type == "joueur" OR $type == "monstre") AND $attaquant->action == $defenseur->action)
	$joueur->unlock_achiev('same_action');

// round_total sera modifié ensuite au besoin, mais on doit le seter au début
$round_total = $G_round_total;
$W_case = convert_in_pos($defenseur->get_x(), $defenseur->get_y());
$W_distance = detection_distance($W_case, convert_in_pos($attaquant->get_x(), $attaquant->get_y()));
?>
<fieldset>
	<legend>Combat VS <?php echo $defenseur->get_nom(); ?></legend>
<?php
if(is_donjon($joueur->get_x(), $joueur->get_y())
	 && ($joueur->in_arene('and donj = 0') == false) && $joueur->get_y()>190)
{
	$round_total = $G_round_total * 2;
	$attaquant->set_rm_restant($attaquant->get_rm_restant() * 2);
	$defenseur->set_rm_restant($defenseur->get_rm_restant() * 2);
	$W_case = convertd_in_pos($defenseur->get_x(), $defenseur->get_y());
	$W_distance = detection_distance($W_case, convertd_in_pos($attaquant->get_x(), $attaquant->get_y()));
	//Un monstre attaque pas de pa pour attaquer
	if(array_key_exists('attaque_donjon', $_SESSION) AND $_SESSION['attaque_donjon'] == 'ok')
	{
		$no_pa_attaque = true;
		unset($_SESSION['attaque_donjon']);
		$W_distance--;
		if($W_distance < 0 ) $W_distance = 0;
	}
}

$dist_tir_att = $attaquant->get_distance_tir();
//On vérifie si l'attaquant est sur un batiment offensif
$requete = "SELECT id_batiment FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y()." AND royaume = ".$Trace[$joueur->get_race()]['numrace'];
$req = $db->query($requete);
if($db->num_rows > 0)
{
	$row = $db->read_row($req);
	$batiment_off = new batiment($row[0]);
	//Augmentation de tir à distance
	if ($batiment_off->has_bonus('batiment_distance'))
		$attaquant->add_buff('batiment_distance',
												 $batiment_off->get_bonus('batiment_distance'));
	//Augmentation de la distance de tir
	if ($batiment_off->has_bonus('batiment_incantation'))
		$attaquant->add_buff('batiment_incantation',
												 $batiment_off->get_bonus('batiment_incantation'));
  if( $attaquant->get_arme_type() == 'arc' && $batiment_off->has_bonus('distance_arc') )
    $dist_tir_att += $batiment_off->get_bonus('distance_arc');
  else if( $attaquant->get_arme_type() == 'baton' && $batiment_off->has_bonus('distance_baton') )
    $dist_tir_att += $batiment_off->get_bonus('distance_baton');
}
		
if($joueur->is_buff('repos_sage') && !$no_pa_attaque)
{
	echo '<h5>Vous êtes sous repos du sage, vous ne pouvez pas attaquer.</h5>';
}
elseif($no_rvr)
{
	echo '<h5>Vous ne pouvez pas attaquer pendant la trêve.</h5>';
}
elseif($joueur->is_buff('dressage'))
{
	echo '<h5>Vous dressez un monstre, vous ne pouvez pas attaquer.</h5>';
}
else if($W_distance > $dist_tir_att )
{
	echo '<h5>Vous êtes trop loin pour l\'attaquer !</h5>';
}
elseif($attaquant->get_hp() <= 0 OR $defenseur->get_hp() <= 0)
{
	echo '<h5>Un des protagonistes n\'a plus de points de vie</h5>';
}
elseif($joueur->is_buff('petrifie'))
{
	echo '<h5>Vous êtes pétrifié, vous ne pouvez pas attaquer.</h5>';
}
else
{
	$R = new royaume($Trace[$joueur->get_race()]['numrace']);
	if($type == 'joueur')
	{
		//Récupération si la case est une ville et diplomatie
		$defenseur_en_defense = false;
		$requete = "SELECT type FROM map WHERE x = ".$defenseur->get_x()
			.' and y = '.$defenseur->get_y()." AND type = 1 AND royaume = ".
			$Trace[$defenseur->get_race()]['numrace'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			echo 'Le défenseur est sur sa ville, application des bonus !<br />';
			$defenseur->add_buff('batiment_pp', 30);
			$defenseur->add_buff('batiment_pm', 16);
			$defenseur->add_buff('batiment_esquive', 50);
			$defenseur_en_defense = true;
		}
		//On vérifie si le défenseur est sur un batiment défensif
		$requete = "SELECT id_batiment FROM construction WHERE x = ".$defenseur->get_x()." AND y = ".$defenseur->get_y()." AND royaume = ".$Trace[$defenseur->get_race()]['numrace'];
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_row($req);
			$batiment_def = new batiment($row[0]);
			//Augmentation des chances d'esquiver
			if ($batiment_def->has_bonus('batiment_esquive'))
				$defenseur->add_buff('batiment_esquive',
														 $batiment_def->get_bonus('batiment_esquive'));
			//Augmentation de la PP
			if ($batiment_def->has_bonus('batiment_pp'))
				$defenseur->add_buff('batiment_pp', $batiment_def->get_bonus('batiment_pp'));
			//Augmentation de la PM
			if ($batiment_def->has_bonus('batiment_pm'))
				$defenseur->add_buff('batiment_pm', 
														 $batiment_def->get_bonus('batiment_pm'));
			//Augmentation de l'incantation
			if ($batiment_def->has_bonus('batiment_incantation'))
				$defenseur->add_buff('batiment_incantation', 
														 $batiment_def->get_bonus('batiment_incantation'));
			//Augmentation du tir à distance
			if ($batiment_def->has_bonus('batiment_distance'))
				$defenseur->add_buff('batiment_distance', 
														 $batiment_def->get_bonus('batiment_distance'));
			$defenseur_en_defense = true;
		}
	} //fin $type = 'joueur'
	if($type == 'siege' OR $type == 'ville') $round_total = 1;
	$round = 1;
	$attaquant->etat = array();
	$defenseur->etat = array();
	$debugs = 0;
	/*if($type == 'joueur') $pa_attaque = $G_PA_attaque_joueur;
	elseif($type == 'batiment') $pa_attaque = $G_PA_attaque_batiment;
	else $pa_attaque = $G_PA_attaque_monstre;*/
	if($attaquant->get_race() == $defenseur->get_race() && $joueur->in_arene() == false) $pa_attaque += 3;
	if($attaquant->get_race() == 'orc' OR $defenseur->get_race() == 'orc') $round_total += 1;
	if($attaquant->is_buff('buff_sacrifice')) $round_total -= $attaquant->get_buff('buff_sacrifice', 'effet2');
	/*if($attaquant->is_buff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $attaquant->get_buff('cout_attaque', 'effet'));
	if($attaquant->is_buff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $attaquant->get_buff('plus_cout_attaque', 'effet');
	if($attaquant->is_buff('buff_rapidite')) $reduction_pa = $attaquant->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($attaquant->is_buff('debuff_ralentissement')) $reduction_pa -= $attaquant->get_buff('debuff_ralentissement', 'effet');*/
	if($attaquant->is_buff('engloutissement')) $attaquant->add_bonus_permanents('dexterite', -$attaquant->get_buff('engloutissement', 'effet'));
	if($attaquant->is_buff('deluge')) $attaquant->add_bonus_permanents('volonte', -$attaquant->get_buff('deluge', 'effet'));
	if($defenseur->is_buff('engloutissement')) $defenseur->add_bonus_permanents('dexterite', -$defenseur->get_buff('engloutissement', 'effet'));
	if($defenseur->is_buff('deluge')) $defenseur->add_bonus_permanents('volonte', -$defenseur->get_buff('deluge', 'effet'));

	// Check des maladies
	maladie::degenerescence($attaquant);
	maladie::degenerescence($defenseur);

	/*$pa_attaque = $pa_attaque - $reduction_pa;
	if($pa_attaque <= 0) $pa_attaque = 1;*/
	$pa_attaque = $attaquant->get_cout_attaque($joueur, $defenseur);
	if (isset($no_pa_attaque) && $no_pa_attaque == true)
		$pa_attaque = 0;
	/*if($type == 'siege' OR $type == 'ville')
	{
		$pa_attaque = 10;
		if($attaquant->is_buff('debuff_rez')) $pa_attaque *= 2;
	}*/

	$joueur_true = false;
	$siege_true = false;
	if ($type == 'joueur' OR $type == 'monstre' OR $type == 'batiment') if($joueur->get_pa() >= $pa_attaque) $joueur_true = true;
	if($type == 'siege' OR $type == 'ville') if ($attaquant->peut_attaquer()) if($joueur->get_pa() >= $pa_attaque) $siege_true = true;
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer ou si l'arme de siege a assez de rechargement
	if ($joueur_true OR $siege_true)
	{
		if($attaquant->get_hp() > 0)
		{
			//Suppresion de longue portée si besoin
			if($attaquant->is_buff('longue_portee') AND $attaquant->get_arme_type() == 'arc')
			{
				$requete = "DELETE FROM buff WHERE id = ".$attaquant->get_buff('longue_portee', 'id');
				$db->query($requete);
			}
			//Gestion des points de crime
			if($type == 'joueur')
			{
				$crime = false;
				$requete = "SELECT ".$defenseur->get_race()." FROM diplomatie WHERE race = '".$attaquant->get_race()."'";
				$req = $db->query($requete);
				$row = $db->read_row($req);
				$pascrime = false;
				//Vérification si crime
				if(array_key_exists($row[0], $G_crime))
				{
					if($row[0] == 127)
					{
						$amende = recup_amende($joueur_defenseur->get_id());
						if($amende)
						{
							if($amende['statut'] != 'normal') $pascrime = true;
						}
					}
					if(!$pascrime)
					{
						$crime = true;
						$points = ($G_crime[$row[0]] / 10);
						$joueur->set_crime($joueur->get_crime() + $points);
						echo '<h5>Vous attaquez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
					}
				}
			}

			$attaque_hp_avant = $attaquant->get_hp();
			$defense_hp_avant = $defenseur->get_hp();

			$log_combat .= 'r'.$round.':';
			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant->get_hp() > 0) AND ($defenseur->get_hp() > 0))
			{
				$attaquant->init_round();
				$defenseur->init_round();
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');

				// Effets généraux
				$effects = effect::general_factory($attaquant, $defenseur, $mode);
				// Effets permanents des joueurs
				$joueur->get_effets_permanents($effects, $mode);
				if($type == 'joueur')
				{
					if($mode == 'attaquant')
						$joueur_defenseur->get_effets_permanents($effects, 'defenseur');
					else
						$joueur_defenseur->get_effets_permanents($effects, 'attaquant');
				}

				if($mode == 'attaquant')
				{
					echo '
					<table style="width : 100%;">
						<tr>
							<td style="vertical-align : top; width : 20%;">
								<h3 style="margin-top : 3px;">Round '.$round.'</h3>
							</td>
							<td>';
					foreach($defenseur->etat as $key => $value)
					{
						$defenseur->etat[$key]['duree'] -= 1;
						if($defenseur->etat[$key]['duree'] <= 0) unset($defenseur->etat[$key]);
						//else echo $defenseur->get_nom().' est '.$key.' pour '.$defenseur->etat[$key]['duree'].' rounds<br />';
					}
				}
				else
				{
					foreach($attaquant->etat as $key => $value)
					{
						$attaquant->etat[$key]['duree'] -= 1;
						if($attaquant->etat[$key]['duree'] <= 0) unset($attaquant->etat[$key]);
						//else echo $attaquant->get_nom().' est '.$key.' pour '.$attaquant->etat[$key]['duree'].' rounds<br />';
					}
				}
					?>
					<div class="combat">
					<?php
					$W_distance_relative = $W_distance - $defenseur->get_distance_tir();
					if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
					if(($mode == 'defenseur') && ($W_distance_relative >= $round))
					{
						echo $defenseur->get_nom().' s\'approche<br />';
						//$action[0] = '';
						$action = null;
						$log_combat .= 'n';
					}
					elseif (($mode == 'defenseur') && ($type == 'batiment'))
					{
						//$action[0] = '';
						$action = null;
					}
					else
					{
						${$mode}->get_action();
						$action = script_action(${$mode}, ${$mode_def}, $mode, $effects);
						//if(is_array($action[2])) ${$mode} = $action[2];
					}
					//print_r($action);
					$args = array();
					$args_def = array();
					//echo $action[0];
					$hp_avant = ${$mode_def}->get_hp();
					$augmentations = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
          /*switch($action[0])
					{
						//Attaque
						case 'attaque' :
							$log_combat .= 'c0';
							$augmentations = attaque($mode, ${$mode}->get_comp_att(), $effects);
						break;
						//Lancement d'un sort
						case 'lance_sort' :
							$log_combat .= 's'.$action[1];
							$augmentations = lance_sort($action[1], $mode, $effects);
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							$log_combat .= 'c'.$action[1];
							$augmentations = lance_comp($action[1], $mode, $effects);
							if($comp_attaque)
							{
								$aug2 = attaque($mode, ${$mode}->get_comp_att(), $effects);
								$augmentations = merge_augmentations($augmentations, $aug2);
								$count = count($ups);
								if($count > 0)
								{
									$upi = 0;
									while($upi < $count)
									{
										$requete = "UPDATE comp_perso SET valeur = ".${$mode}['competences'][$ups[$upi]]." WHERE id_perso = ".${$mode}->get_id()." AND competence = '".$ups[$upi]."'";
										$db->query($requete);
										$upi++;
									}
								}
							}
						break;
						// Rien eu du tout
					case '':

						// Application des effets de fin de round
						foreach ($effects as $effect)
							$effect->fin_round(${$mode}, ${$mode_def});
						// ~Fin de round
						break ;
					}*/
					
					if($action)
					{
            if ($mode == 'attaquant')
            {
              $actif = &$attaquant;
              $passif = &$defenseur;
        	    $log_effects_actif = $log_effects_attaquant;
        	    $log_effects_passif = $log_effects_defenseur;
            }
          	else
            {
              $actif = &$defenseur;
              $passif = &$attaquant;
        	    $log_effects_actif = $log_effects_defenseur;
        	    $log_effects_passif = $log_effects_attaquant;
            }

            // Calcul de MP nécessaires
          	/*$mp_need = round($action->get_mp() * (1 - (($Trace[$actif->get_race()]['affinite_'.$action->get_comp_assoc()] - 5) / 10)));
          	if($actif->get_type() == "pet") $mp_need = $action->get_mp();*/
          	$mp_need = $action->get_cout_mp($actif);
          	// Appel des ténebres
          	if($actif->etat['appel_tenebre']['duree'] > 0)
          	{
          		$mp_need += $actif->etat['appel_tenebre']['effet'];
          	}
          	//Appel de la forêt
          	if($actif->etat['appel_foret']['duree'] > 0)
          	{
          		$mp_need_avant = $mp_need;
          		$mp_need -= $actif->etat['appel_foret']['effet'];
          		if($mp_need < 1) $mp_need = $mp_need_avant;
          	}
            // Application des effets de mana
            foreach ($effects as $effect)
              $mp_need = $effect->calcul_mp($actif, $mp_need);
          	//Suppresion de la réserve
          	$actif->set_rm_restant($actif->get_rm_restant() - $mp_need);

            $augmentations = $action->lance($actif, $passif, $effects);
          }
          foreach ($effects as $effect)
						$effect->fin_round(${$mode}, ${$mode_def}, $mode);

					//Augmentation des compétences liées
					if($mode == 'attaquant')
					{
						if(!$check_pet && !$check_pet_donj) $joueur = augmentation_competences($augmentations['actif'], $joueur);
						if(!$check_pet_def) $joueur_defenseur = augmentation_competences($augmentations['passif'], $joueur_defenseur);
					}
					else
					{
						if(!$check_pet_def) $joueur_defenseur = augmentation_competences($augmentations['actif'], $joueur_defenseur);
						if(!$check_pet&& !$check_pet_donj) $joueur = augmentation_competences($augmentations['passif'], $joueur);
					}
					// Mise à jour de l'entité pour refleter les up
					$attaquant->maj_comp();
					$defenseur->maj_comp();

					if($mode == 'defenseur')
					{
						//Perte de HP par le poison
						/*if($attaquant->etat['poison']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['poison']['effet'] - $attaquant->etat['poison']['duree'] + 1;
							if($attaquant->etat['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $attaquant->etat['putrefaction']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par le poison</span><br />';
							$log_effects_attaquant .= "&ef1~".$perte_hp;
						}
						if($defenseur->etat['poison']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['poison']['effet'] - $defenseur->etat['poison']['duree'] + 1;
							if($defenseur->etat['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $defenseur->etat['putrefaction']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par le poison</span><br />';
							$log_effects_defenseur .= "&ef1~".$perte_hp;
						}
						//Perte de HP par hémorragie
						if($attaquant->etat['hemorragie']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['hemorragie']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par hémorragie</span><br />';
							$log_effects_attaquant .= "&ef2~".$perte_hp;
						}
						if($defenseur->etat['hemorragie']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['hemorragie']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par hémorragie</span><br />';
							$log_effects_defenseur .= "&ef2~".$perte_hp;
						}
						//Perte de HP par embrasement
						if($attaquant->etat['embraser']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['embraser']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par embrasement</span><br />';
							$log_effects_attaquant .= "&ef3~".$perte_hp;
						}
						if($defenseur->etat['embraser']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['embraser']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par embrasement</span><br />';
							$log_effects_defenseur .= "&ef3~".$perte_hp;
						}
						//Perte de HP par acide
						if($attaquant->etat['acide']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['acide']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par acide</span><br />';
							$log_effects_attaquant .= "&ef4~".$perte_hp;
						}
						if($defenseur->etat['acide']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['acide']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par acide</span><br />';
							$log_effects_defenseur .= "&ef4~".$perte_hp;
						}
						//Perte de HP par lien sylvestre
						if($attaquant->etat['lien_sylvestre']['duree'] > 0)
						{
							$attaquant->set_hp($attaquant->get_hp() - $attaquant->etat['lien_sylvestre']['effet']);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$attaquant->etat['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
							$log_effects_attaquant .= "&ef5~".$attaquant->etat['lien_sylvestre']['effet'];
						}
						if($defenseur->etat['lien_sylvestre']['duree'] > 0)
						{
							$defenseur->set_hp($defenseur->get_hp() - $defenseur->etat['lien_sylvestre']['effet']);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$defenseur->etat['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
							$log_effects_defenseur .= "&ef5~".$defenseur->etat['lien_sylvestre']['effet'];
						}
						if($attaquant->etat['recuperation']['duree'] > 0)
						{
							$effet = $attaquant->etat['recuperation']['effet'];
							if(($attaquant->get_hp() + $effet) > $attaquant->etat['recuperation']['hp_max'])
							{
								$effet = $attaquant->etat['recuperation']['hp_max'] - $attaquant->get_hp();
							}
							$attaquant->set_hp($attaquant->get_hp() + $effet);
							if($effet > 0)
							{
								$attaquant->etat['recuperation']['hp_recup'] += $effet;
								echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' gagne '.$effet.' HP par récupération</span><br />';
								$log_effects_attaquant .= "&ef6~".$effet;
							}
							else
								print_debug($attaquant->get_nom().' ne peut pas gagner de HP par récupération');
						}
						if($defenseur->etat['recuperation']['duree'] > 0)
						{
							$effet = $defenseur->etat['recuperation']['effet'];
							if(($defenseur->get_hp() + $effet) > $defenseur->etat['recuperation']['hp_max'])
							{
								$effet = $defenseur->etat['recuperation']['hp_max'] - $defenseur->get_hp();
							}
							$defenseur->set_hp($defenseur->get_hp() + $effet);
							if($effet > 0)
							{
								$defenseur->etat['recuperation']['hp_recup'] += $effet; 
								echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' gagne '.$effet.' HP par récupération</span><br />';
								$log_effects_defenseur .= "&ef6~".$effet;
							}
						}
						if($defenseur->etat['fleche_debilitante']['duree'] > 0)
						{
							echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' est sous l\'effet de Flêche Débilisante</span><br />';
							$log_effects_defenseur .= "&ef7~0";
						}
						if($attaquant->etat['fleche_debilitante']['duree'] > 0)
						{
							echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' est sous l\'effet de Flêche Débilisante</span><br />';
							$log_effects_attaquant .= "&ef7~0";
						}*/
					}

					//Update de la base de donnée.
					//Correction des bonus ignorables
					corrige_bonus_ignorables($attaquant, $defenseur, $mode, $args, $args_def);

					?>
					</div>
					<?php
				//Fin du round et gestion du log du combat
				if($mode == 'defenseur')
				{
					$round++;
					$log_combat .= ','.$log_effects_attaquant.','.$log_effects_defenseur;
					$log_effects_attaquant = "";
					$log_effects_defenseur = "";
					if ($round < ($round_total + 1)) $log_combat .= ';r'.$round.':';
					?>
					</td>
				</tr>
			</table>
					<?php
				}
				else
					$log_combat .= ','; // Fin du round de l'attaquant
					
					if ($siege_true) break;
			}
			
			$attaque_hp_apres = $attaquant->get_hp();
			$defense_hp_apres = $defenseur->get_hp();
			$degat_defense = $defense_hp_avant - $defense_hp_apres;
			//On donne les bons HP à l'attaque et la défense
			$attaquant->fin_combat($joueur);
			$defenseur->fin_combat($joueur, $degat_defense);
			//Fin du combat
			if($mode == 'attaquant')
			{
					?>
					</td>
				</tr>
			</table>
					<?php
			}

			//Calculs liés à la survie, fiabilité de l'estimation de HP etc.
			$survie = $joueur->get_survie();
			if ($survie <= 0) $survie = 1;

			// Survies specialisees
			
			$survies_a_monter = array();
			if ($type == 'monstre')
			{
				if ($joueur_defenseur->get_type() == 'humanoide')
				{
					if ($joueur->is_competence('survie_humanoide'))
						$survie += $joueur->get_competence('survie_humanoide');
					$survies_a_monter[] = 'survie_humanoide';
				}
				elseif ($joueur_defenseur->get_type() == 'magique')
				{
					if ($joueur->is_competence('survie_magique'))
						$survie += $joueur->get_competence('survie_magique');
					$survies_a_monter[] = 'survie_magique';
				}
				elseif ($joueur_defenseur->get_type() == 'bete')
				{
					if ($joueur->is_competence('survie_bete'))
						$survie += $joueur->get_competence('survie_bete');
					$survies_a_monter[] = 'survie_bete';
				}
			}
			if ($type == 'joueur')
			{
				$survies_a_monter[] = 'survie_humanoide';
				if ($joueur->is_competence('survie_humanoide'))
					$survie += $joueur->get_competence('survie_humanoide');
			}
			
			if ($defenseur->get_level() != 0) $nbr_barre_total = ceil($survie / $defenseur->get_level()); // Pour les PNJ
			else $nbr_barre_total = $survie;
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur->get_hp() / $defenseur->get_hp_max()) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total) / 2, 2);
			echo '
			<hr />';
			
			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $joueur, 2);
			if($augmentation[1] == 1)
			{
				$joueur->set_survie($augmentation[0]);
			}

			if($check_pet OR $check_pet_donj)
			{
				$augmentation = augmentation_competence('dressage', $joueur, 0.43);
				if($augmentation[1] == 1)
				{
					$joueur->set_dressage($augmentation[0]);
				}
			}
			if($check_pet_def)
			{
				$augmentation = augmentation_competence('dressage', $joueur_defenseur, 0.43);
				if($augmentation[1] == 1)
				{
					$joueur_defenseur->set_dressage($augmentation[0]);
					$joueur_defenseur->sauver();
				}
			}

			foreach ($survies_a_monter as $survie_test)
			{
				if ($joueur->is_competence($survie_test))
				{
					$augmentation = augmentation_competence($survie_test, $joueur, 4);
					if ($augmentation[1] == 1)
						$joueur->set_comp($survie_test, $augmentation[0]);
				}
			}

			//Cartouche de fin de combat
			echo ' 
			<div id="combat_cartouche">
			<ul style="float:left;">
				<li><span style="display:block;float:left;width:150px;">'.$attaquant->get_nom().'</span>
					<span style="display:block;float:left;width:150px;">'.$attaquant->get_hp().' HP</span>
					</li>
					<li><span style="display:block;float:left;width:150px;">'.$defenseur->get_nom().'</span>
						<span style="display:block;float:left;width:150px;"><img src="genere_barre_vie.php?longueur='.$longueur.'" alt="Estimation des HP : '.$longueur.'% / + ou - : '.$fiabilite.'%"" title="Estimation des HP : '.$longueur.'% / + ou - : '.$fiabilite.'%" /></span>
					<li>
			</ul>
			<div style="float:left;">';

			
			$msg_xp .= $defenseur->fin_defense($joueur, $R, $pet, $degat_defense, $defenseur_en_defense);

			if ($defenseur->get_hp() > 0)
			{
				if($check_pet) $link = '&pet'; else $link = '';
				if($type == 'joueur') echo(' <a href="attaque.php?id_joueur='.$joueur_defenseur->get_id().'&amp;type=joueur'.$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'monstre') echo(' <a href="attaque.php?id_monstre='.$map_monstre->get_id().'&amp;type=monstre'.$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'batiment') echo(' <a href="attaque.php?id_batiment='.$map_batiment->get_id().'&amp;type=batiment&amp;table='.$_GET['table'].$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
			}
			

			if (!$check_pet AND ($attaquant->get_compteur_critique() > 0) AND ($attaquant->get_type() == 'joueur'))
			{
				$achiev = $attaquant->get_compteur('critique');
				$achiev->set_compteur($achiev->get_compteur() + $attaquant->get_compteur_critique());
				$achiev->sauver();
			}
			if (!$check_pet_def AND ($defenseur->get_compteur_critique() > 0) AND ($defenseur->get_type() == 'joueur'))
			{
				$achiev = $defenseur->get_compteur('critique');
				$achiev->set_compteur($achiev->get_compteur() + $defenseur->get_compteur_critique());
				$achiev->sauver();
			}
			$attaquant->fin_attaque($joueur, $defenseur, $pa_attaque);
			//Suppression des PA si c'est une attaque du joueur
			if($type == 'joueur' OR $type == 'monstre' OR $type == 'batiment')
			{
				if(get_class($attaquant)=="perso")
				{
					$joueur->set_pa($attaquant->get_pa() - $pa_attaque);
				}
				else
				{
					$joueur->set_pa($joueur->get_pa() - $pa_attaque);
				}
				$joueur->sauver();
			}
			//Sinon c'est une arme de siège, et il faut modifier son rechargement
			elseif ($type == 'siege' OR $type == 'ville')
			{
				$joueur->set_pa($joueur->get_pa() - $pa_attaque);
				$joueur->sauver();
				/*$map_siege->set_rechargement(time() + $siege->get_bonus('rechargement'));
				$map_siege->sauver();*/
			}
			else
			{
				echo "<b>Error: </b> type is [$type] !<br/>";
			}

			//Mise dans les journaux si attaque pvp
			if($type == 'joueur')
			{
				//Insertion de l'attaque dans les journaux des 2 joueurs
				
				//Journal de l'attaquant 
				if(!$check_pet)
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'attaque', '".sSQL($joueur->get_nom())."', '".sSQL($defenseur->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				else
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'attaque', '".sSQL($attaquant->get_nom())."', '".sSQL($defenseur->get_nom())."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				$db->query($requete);
				// Creation du log du combat
				$combat = new combat();
				$combat->attaquant = $joueur->get_id();
				$combat->defenseur = $joueur_defenseur->get_id();
				$combat->combat = $log_combat;
				$combat->id_journal = $db->last_insert_id();
				$combat->sauver();
				
				//Journal du défenseur
				if(!$check_pet_def)
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur_defenseur->get_id().", 'defense', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				else
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur_defenseur->get_id().", 'defense', '".$defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				
				$db->query($requete);
				$combat = new combat();
				$combat->attaquant = $joueur->get_id();
				$combat->defenseur = $joueur_defenseur->get_id();
				$combat->combat = $log_combat;
				$combat->id_journal = $db->last_insert_id();
				$combat->sauver();
				
				if($defenseur->get_hp() <= 0 && !$check_pet_def)
				{
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'tue', '".$joueur->get_nom()."', '".$joueur_defenseur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur_defenseur->get_id().", 'mort', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
				}
				
				if($attaquant->get_hp() <= 0 && !$check_pet)
				{
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'mort', '".$joueur->get_nom()."', '".$joueur_defenseur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
					$requete = "INSERT INTO journal VALUES(NULL, ".$joueur_defenseur->get_id().", 'tue', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
				}
			}
			//Mise dans le journal si attaque sur batiment
			elseif($type == 'batiment')
			{
				//Insertion de l'attaque dans les journaux des 2 joueurs
				$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'attaque', '".$joueur->get_nom()."', '".$defenseur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur->get_x().", ".$defenseur->get_y().")";
				$db->query($requete);
				// Creation du log du combat
				$combat = new combat();
				$combat->attaquant = $joueur->get_id();
				$combat->defenseur = $defenseur->get_id();
				$combat->combat = $log_combat;
				$combat->id_journal = $db->last_insert_id();
				$combat->sauver();
			}
		}
		else
		{
			echo "Vous êtes mort !<img src=\"image/pixel.gif\" onload=\"window.location.reload();\" />\n";
		}
	}
	else
	{
		echo '<h5>Vous n\'avez pas assez de points d\'actions</h5>';
	}
}
?>
<a onclick="for (i=0; i<<?php echo $debugs; ?>; i++) {if(document.getElementById('debug' + i).style.display == 'inline') document.getElementById('debug' + i).style.display = 'none'; else document.getElementById('debug' + i).style.display = 'inline';}"><img src="image/interface/debug.png" alt="Debug" Title="Débug pour voir en détail le combat" style="vertical-align : middle;cursor:pointer;" /></a> <br />
<a href="informationcase.php?case=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href, 'information')"><img src="image/interface/retour.png" alt="Retour" title="Retour à l'information case" style="vertical-align : middle;" /></a>
</div>
<?php
if (!empty($msg_xp)){echo "<p style='clear:both;'>".$msg_xp."</p>";}
?>
</div>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</fieldset>
