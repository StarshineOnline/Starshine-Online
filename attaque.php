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
$type = $_GET['type'];
switch($type)
{
	case 'joueur' :
		if(!$check_pet)
		{
			$joueur = new perso($_SESSION['ID']);
			$joueur->action_do = $joueur->recupaction('attaque');
			$attaquant = new entite('joueur', $joueur);
		}
		else
		{
			$attaquant = new entite('pet', $joueur);
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
			$defenseur = new entite('joueur', $joueur_defenseur);
		}
		else
		{
			$defenseur = new entite('pet', $joueur_defenseur);
		}
	break;
	case 'monstre' :
		if(!$check_pet)
		{
			$joueur = new perso($_SESSION['ID']);
			$joueur->action_do = $joueur->recupaction('attaque');
			$attaquant = new entite('joueur', $joueur);
		}
		else
		{
			$attaquant = new entite('pet', $joueur);
		}
		$map_monstre = new map_monstre($_GET['id_monstre']);
		$map_monstre->check_monstre();
		if ($map_monstre->nonexistant)
		{
			echo '<h5>Ce monstre est déjà au paradis des monstres</h5>';
			exit (0);
		}
		$joueur_defenseur = new monstre($map_monstre->get_type());
		$joueur_defenseur->hp_max = $joueur_defenseur->get_hp();
		$joueur_defenseur->set_hp($map_monstre->get_hp());
		$joueur_defenseur->x = $map_monstre->get_x();
		$joueur_defenseur->y = $map_monstre->get_y();
		$joueur_defenseur->buff = $map_monstre->get_buff();
		$defenseur = new entite('monstre', $joueur_defenseur);
		$diff_lvl = abs($joueur->get_level() - $defenseur->get_level());
	break;
	case 'batiment' :
		if(!$check_pet)
		{
			$joueur = new perso($_SESSION['ID']);
			$joueur->action_do = $joueur->recupaction('attaque');
			$attaquant = new entite('joueur', $joueur);
		}
		else
		{
			$attaquant = new entite('pet', $joueur);
		}
		if($_GET['table'] == 'construction') $map_batiment = new construction($_GET['id_batiment']);
		else $map_batiment = new placement($_GET['id_batiment']);
		$joueur_defenseur = new batiment($map_batiment->get_id_batiment());
		if($_GET['table'] == 'construction') $joueur_defenseur->coef = 1;
		else $joueur_defenseur->coef = $map_batiment->get_temps_restant() / $map_batiment->get_temps_total();
		$joueur_defenseur->hp_max = $joueur_defenseur->get_hp();
		$joueur_defenseur->set_hp($map_batiment->get_hp());
		$joueur_defenseur->x = $map_batiment->get_x();
		$joueur_defenseur->y = $map_batiment->get_y();
		$defenseur = new entite('batiment', $joueur_defenseur);
	break;
	case 'siege' :
		$map_siege = new construction($_GET['id_arme_de_siege']);
		if($_GET['table'] == 'construction') $map_batiment = new construction($_GET['id_batiment']);
		else $map_batiment = new placement($_GET['id_batiment']);
		$joueur = new perso($_SESSION['ID']);
		if($joueur->get_pa() >= 10)
		{
			$siege = new batiment($map_siege->get_id_batiment());
			$siege->bonus_architecture = 1 + ($joueur->get_architecture() / 100);
			$siege->hp_max = $siege->get_hp();
			$siege->set_hp($map_siege->get_hp());
			$siege->x = $map_siege->get_x();
			$siege->y = $map_siege->get_y();
			$joueur_defenseur = new batiment($map_batiment->get_id_batiment());
			if($_GET['table'] == 'construction') $joueur_defenseur->coef = 1;
			else $joueur_defenseur->coef = $map_batiment->get_temps_restant() / $map_batiment->get_temps_total();
			$joueur_defenseur->hp_max = $joueur_defenseur->get_hp();
			$joueur_defenseur->set_hp($map_batiment->get_hp());
			$joueur_defenseur->x = $map_batiment->get_x();
			$joueur_defenseur->y = $map_batiment->get_y();
			//Si en défense c'est une arme de siège, on applique les dégats 2
			if($joueur_defenseur->get_type() == 'arme_de_siege') $siege->arme_degat = $siege->get_bonus('degats_siege');
			else $siege->arme_degat = $siege->get_bonus('degats_bat');
			if($joueur->get_race() == 'barbare') $siege->arme_degat = ceil($siege->arme_degat * 1.1);
			$attaquant = new entite('siege', $siege);
			$defenseur = new entite('batiment', $joueur_defenseur);
		}
	break;
	case 'ville' :
		$map_siege = new construction($_GET['id_arme_de_siege']);
		$joueur = new perso($_SESSION['ID']);
		$map_case = new map_case($_GET['id_ville']);
		$map_royaume = new royaume($map_case->get_royaume());
		$map_royaume->verif_hp();
		$siege = new batiment($map_siege->get_id_batiment());
		$siege->bonus_architecture = 1 + ($joueur->get_architecture() / 100);
		$siege->hp_max = $siege->get_hp();
		$siege->set_hp($map_siege->get_hp());
		$siege->x = $map_siege->get_x();
		$siege->y = $map_siege->get_y();
		$joueur_defenseur = new batiment();
    /* Bastien: 15 c'est un bourg, 16 bourg + 1 */
		$joueur_defenseur->set_carac(16 + $map_royaume->get_level_mur());
		$joueur_defenseur->coef = 1;
		$joueur_defenseur->hp_max = 30000;
		$joueur_defenseur->set_hp($map_royaume->get_capitale_hp());
		$joueur_defenseur->set_pp($map_royaume->get_pp());
		$coord = convert_in_coord($_GET['id_ville']);
		$joueur_defenseur->x = $coord['x'];
		$joueur_defenseur->y = $coord['y'];
		$siege->arme_degat = $siege->get_bonus('degats_bat');
		$attaquant = new entite('siege', $siege);
		$defenseur = new entite('ville', $joueur_defenseur);
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
	 && ($joueur->in_arene('and donj = 0') == false))
{
	$round_total = $G_round_total * 2;
	$attaquant->set_reserve($attaquant->get_reserve() * 2);
	$defenseur->set_reserve($defenseur->get_reserve() * 2);
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

if($joueur->is_buff('repos_sage') && !$no_pa_attaque)
{
	echo '<h5>Vous êtes sous repos du sage, vous ne pouvez pas attaquer.</h5>';
}
elseif($joueur->is_buff('dressage'))
{
	echo '<h5>Vous dressez un monstre, vous ne pouvez pas attaquer.</h5>';
}
else if($W_distance > $attaquant->get_distance_tir())
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
		$chateau = false;
		$defenseur_en_defense = false;
		$requete = "SELECT type FROM map WHERE x = ".$defenseur->get_x()
			.' and y = '.$defenseur->get_y()." AND type = 1 AND royaume = ".
			$Trace[$defenseur->get_race()]['numrace'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			echo 'Le défenseur est sur sa ville, application des bonus !<br />';
			$defenseur->set_pm($defenseur->get_pm() * 1.16);
			$defenseur->set_pp($defenseur->get_pp() * 1.3);
			$chateau = true;
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
				$defenseur->add_buff('batiment_pp', 
														 $batiment_def->get_bonus('batiment_pp'));
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
		//On vérifie si l'attaquant est sur un batiment offensif
		$requete = "SELECT id_batiment FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$attaquant->get_y()." AND royaume = ".$Trace[$attaquant->get_race()]['numrace'];
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_row($req);
			$batiment_off = new batiment($row[0]);
			//Augmentation de tir à distance
			if ($batiment_off->has_bonus('batiment_distance'))
				$attaquant->add_buff('batiment_distance', 
														 $batiment_off->get_bonus('batiment_distance'));
			//Augmentation de lancement de sorts
			if ($batiment_off->has_bonus('batiment_incantation'))
				$attaquant->add_buff('batiment_incantation', 
														 $batiment_off->get_bonus('batiment_incantation'));
		}
	} //fin $type = 'joueur'
	if($type == 'siege' OR $type == 'ville') $round_total = 1;
	$round = 1;
	$attaquant->etat = array();
	$defenseur->etat = array();
	$debugs = 0;
	if($type == 'joueur') $pa_attaque = $G_PA_attaque_joueur;
	elseif($type == 'batiment') $pa_attaque = $G_PA_attaque_batiment;
	else $pa_attaque = $G_PA_attaque_monstre;
	if($attaquant->get_race() == $defenseur->get_race() && $joueur->in_arene() == false) $pa_attaque += 3;
	if($attaquant->get_race() == 'orc' OR $defenseur->get_race() == 'orc') $round_total += 1;
	if($attaquant->is_buff('buff_sacrifice')) $round_total -= $attaquant->get_buff('buff_sacrifice', 'effet2');
	if($attaquant->is_buff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $attaquant->get_buff('cout_attaque', 'effet'));
	if($attaquant->is_buff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $attaquant->get_buff('plus_cout_attaque', 'effet');
	if($attaquant->is_buff('buff_rapidite')) $reduction_pa = $attaquant->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($attaquant->is_buff('debuff_ralentissement')) $reduction_pa -= $attaquant->get_buff('debuff_ralentissement', 'effet');
	if($attaquant->is_buff('engloutissement')) $attaquant->set_dexterite($attaquant->get_dexterite(true) - $attaquant->get_buff('engloutissement', 'effet'));
	if($attaquant->is_buff('deluge')) $attaquant->set_volonte($attaquant->get_volonte(true) - $attaquant->get_buff('deluge', 'effet'));
	if($defenseur->is_buff('engloutissement')) $defenseur->set_dexterite($defenseur->get_dexterite(true) - $defenseur->get_buff('engloutissement', 'effet'));
	if($defenseur->is_buff('deluge')) $defenseur->set_volonte($defenseur->get_volonte(true) - $defenseur->get_buff('deluge', 'effet'));

	// Check des maladies
	maladie::degenerescence($attaquant);
	maladie::degenerescence($defenseur);

	$pa_attaque = $pa_attaque - $reduction_pa;
	if($pa_attaque <= 0) $pa_attaque = 1;
	if (isset($no_pa_attaque) && $no_pa_attaque == true)
		$pa_attaque = 0;
	if($type == 'siege' OR $type == 'ville')
	{
		$pa_attaque = 10;
		if($attaquant->is_buff('debuff_rez')) $pa_attaque *= 2;
	}

	$joueur_true = false;
	$siege_true = false;
	if ($type == 'joueur' OR $type == 'monstre' OR $type == 'batiment') if($joueur->get_pa() >= $pa_attaque) $joueur_true = true;
	if($type == 'siege' OR $type == 'ville') if ($map_siege->get_rechargement() <= time()) if($attaquant->get_pa() >= $pa_attaque) $siege_true = true;
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
				if($attaquant->get_arme_type() == 'arc') $attaquant->set_comp('distance'); else $attaquant->set_comp('melee');
				if($defenseur->get_arme_type() == 'arc') $defenseur->set_comp('distance'); else $defenseur->set_comp('melee');
				//Calcul du potentiel de toucher et parer
				$attaquant->get_potentiel_toucher();
				$defenseur->get_potentiel_toucher();
				$attaquant->get_potentiel_parer();
				if($type == 'joueur' && $chateau) $esquive = $defenseur->get_esquive() * 1.5; else $esquive = $defenseur->get_esquive();
				$defenseur->get_potentiel_parer($esquive);
				$attaquant->degat_sup = 0;
				$attaquant->degat_moins = 0;
				$defenseur->degat_sup = 0;
				$defenseur->degat_moins = 0;
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
						$action[0] = '';
						$log_combat .= 'n';
					}
					elseif (($mode == 'defenseur') && ($type == 'batiment'))
					{
						$action[0] = '';
					}
					else
					{
						${$mode}->get_action();
						$action = script_action(${$mode}, ${$mode_def}, $mode, $effects);
						if(is_array($action[2])) ${$mode} = $action[2];
					}
					//print_r($action);
					$args = array();
					$args_def = array();
					//echo $action[0];
					$hp_avant = ${$mode_def}->get_hp();
					$augmentations = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
					switch($action[0])
					{
						//Attaque
						case 'attaque' :
							$log_combat .= 'c0';
							$augmentations = attaque($mode, ${$mode}->get_comp(), $effects);
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
								$aug2 = attaque($mode, ${$mode}->get_comp(), $effects);
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

						/* Application des effets de fin de round */
						foreach ($effects as $effect)
							$effect->fin_round(${$mode}, ${$mode_def});
						/* ~Fin de round */
						break ;
					}

					//Augmentation des compétences liées
					if($mode == 'attaquant')
					{
						if(!$check_pet) $joueur = augmentation_competences($augmentations['actif'], $joueur);
						if(!$check_pet_def) $joueur_defenseur = augmentation_competences($augmentations['passif'], $joueur_defenseur);
					}
					else
					{
						if(!$check_pet_def) $joueur_defenseur = augmentation_competences($augmentations['actif'], $joueur_defenseur);
						if(!$check_pet) $joueur = augmentation_competences($augmentations['passif'], $joueur);
					}
					// Mise à jour de l'entité pour refleter les up
					$attaquant->maj_comp();
					$defenseur->maj_comp();

					if($mode == 'defenseur')
					{
						//Perte de HP par le poison
						if($attaquant->etat['poison']['duree'] > 0)
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
						}
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
			if($type == 'joueur')
			{
				if(!$check_pet)
				{
					$joueur->set_hp($attaquant->get_hp());
					$joueur->sauver();
				}
				else
				{
					$pet = $joueur->get_pet();
					$pet->set_hp($attaquant->get_hp());
					$pet->sauver();
				}
				if(!$check_pet_def)
				{
					$joueur_defenseur->set_hp($defenseur->get_hp());
					$joueur_defenseur->sauver();
				}
				else
				{
					$pet_def = $joueur_defenseur->get_pet();
					$pet_def->set_hp($defenseur->get_hp());
					$pet_def->sauver();
				}
			}
			if($type == 'monstre')
			{
				//echo '<pre>'; var_dump($joueur); echo '</pre>';
				if(!$check_pet)
				{
					$joueur->set_hp($attaquant->get_hp());
					$joueur->sauver();
				}
				else
				{
					$pet = $joueur->get_pet();
					$pet->set_hp($attaquant->get_hp());
					$pet->sauver();
				}
				$map_monstre->set_hp($defenseur->get_hp());
				if($map_monstre->get_hp() > 0) $map_monstre->sauver();
				else $map_monstre->supprimer();
			}
			elseif($type == 'batiment')
			{
				if(!$check_pet)
				{
					$joueur->set_hp($attaquant->get_hp());
					$joueur->sauver();
				}
				else
				{
					$pet = $joueur->get_pet();
					$pet->set_hp($attaquant->get_hp());
					$pet->sauver();
				}
				$map_batiment->set_hp($defenseur->get_hp());
				if($map_batiment->get_hp() > 0) $map_batiment->sauver();
				else 
				{
					$map_batiment->supprimer();
					$royaume_attaquant = new royaume($Trace[$joueur->get_race()]['numrace']);
					$royaume_attaquant->add_point_victoire($map_batiment->get_point_victoire());
				}
				
				// Augmentation du compteur de l'achievement
				$achiev = $joueur->get_compteur('structure_degats');
				$achiev->set_compteur($achiev->get_compteur() + $degat_defense);
				$achiev->sauver();
			}
			elseif($type == 'siege')
			{
				$map_batiment->set_hp($defenseur->get_hp());
				if($map_batiment->get_hp() > 0) $map_batiment->sauver();
				else $map_batiment->supprimer();
			}
			elseif($type == 'ville')
			{
				if($degat_defense > 0)
				{
					//hasard pour différente actions de destruction sur la ville.
					//Si il y a assez de ressources en ville
					$suppr_hp = true;
					if($map_royaume->total_ressources() > 1000)
					{
						$rand = rand(1, 100);
						//Premier cas, on supprime les ressources
						if($rand >= 50)
						{
							$suppr_hp = false;
							$map_royaume->supprime_ressources($degat_defense / 100);
							echo '<h6>L\'attaque détruit des ressources au royaume '.$Gtrad[$map_royaume->get_race()].'</h6><br />';
						}
					}
					//Sinon on attaque les batiments ou la ville
					if($suppr_hp)
					{
						print_debug("Les infrastructures sont touchées<br/>");
						$map_royaume->get_constructions_ville(true);
						$count = count($map_royaume->constructions_ville);
						//Si on peut détruire des bâtiments en ville
						if($count > 0)
						{
							$rand = rand(0, $count - 1);
							//On attaque la construction $rand du tableau
							$construction_ville = new construction_ville($map_royaume->constructions_ville[$rand]['id']);
							$return = $construction_ville->suppr_hp($degat_defense);
							echo '<h6>Attaque d\'un batiment en ville</h6>';
							//On a downgrade un batiment, on gagne des points de victoire
							if($return > 0)
							{
								$royaume_attaquant = new royaume($Trace[$joueur->get_race()]['numrace']);
								$royaume_attaquant->add_point_victoire($return);
								$royaume_attaquant->sauver();
								echo '<h6>Une construction a été détruite ! Votre royaume gagne '.$return .' points de victoire.</h6><br />';
							}
						}
						else
						{
							echo '<h6>Le coeur même de la ville est attaqué</h6>';
							$map_royaume->set_capitale_hp($defenseur->get_hp());
							//Si la capitale n'a plus de vie, on met le royaume en raz
							if($map_royaume->get_capitale_hp() < 0)
							{
								$time = time() + 3600 * 24 * 31;
								$map_royaume->set_fin_raz_capitale($time);
								$royaume_attaquant = new royaume($Trace[$joueur->get_race()]['numrace']);
								$royaume_attaquant->add_point_victoire(100);
								$royaume_attaquant->sauver();
								echo '<h6>La capitale est détruite ! Votre royaume gagne 100 points de victoire.</h6><br />';
								
								// On debloque l'achievement
								$joueur->unlock_achiev('capitale_detruite');
							}
						}
					}
					$map_royaume->sauver();
				}
			}
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

			$nbr_barre_total = ceil($survie / $defenseur->get_level());
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

			if($check_pet)
			{
				$augmentation = augmentation_competence('dressage', $joueur, 1.3);
				if($augmentation[1] == 1)
				{
					$joueur->set_dressage($augmentation[0]);
				}
			}
			if($check_pet_def)
			{
				$augmentation = augmentation_competence('dressage', $joueur_defenseur, 1.3);
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

			
			if($type == 'joueur')
			{
				$gains = false;
				$coef = 1;
				//L'attaquant est mort !
				if (!$check_pet && $attaquant->get_hp() <= 0)
				{
					$joueur->trigger_arene();
					$actif = $joueur_defenseur;
					$passif = $joueur;
					$gains = true;
					//On supprime toutes les rez
					$joueur->supprime_rez();
					
					//Achievement
					if($attaquant->get_hp() == 0)
						$joueur->unlock_achiev('near_kill');
				}
				//Le défenseur est mort !
				if (!$check_pet_def && $defenseur->get_hp() <= 0)
				{
					$joueur->trigger_arene();
					$actif = $joueur;
					$passif = $joueur_defenseur;
					$gains = true;
					$joueur_defenseur->supprime_rez();
					
					if($defenseur_en_defense)
					{
						// Augmentation du compteur de l'achievement
						$achiev = $joueur->get_compteur('kill_defense');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
					
					//Achievement
					if($defenseur->get_hp() == 0)
						$joueur_defenseur->unlock_achiev('near_kill');

					if ($defenseur->get_nom() == 'Irulan')
					{
						$actif->unlock_achiev('kill_bastounet');
					}
					
					if ($passif->get_crime() > 0)
					{
						$achiev = $actif->get_compteur('dredd');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
				}

				if($gains)
				{
					// Augmentation du compteur de l'achievement
					$achiev = $actif->get_compteur('kill_'.$passif->get_race());
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				
					//Gain d'expérience
					$xp = $passif->get_level() * 100 * $G_xp_rate;

					//Si le joueur a un groupe
					if($actif->get_groupe() > 0)
					{
						$groupe = new groupe($actif->get_groupe());
						$groupe->get_share_xp($actif->get_pos());
						//Si on tape un joueur de son groupe xp = 0
						foreach($groupe->membre_joueur as $membre_id)
						{
							if($membre_id->get_id() == $passif->get_id()) 
							{
								$xp = 0;
								
								// Augmentation du compteur de l'achievement
								$achiev = $actif->get_compteur('kill_teammate');
								$achiev->set_compteur($achiev->get_compteur() + 1);
								$achiev->sauver();
							}
						}
					}
					//Joueur solo
					else
					{
						$groupe = new groupe();
						$groupe->level_groupe = $actif->get_level();
						$groupe->somme_groupe = $actif->get_level();
						$groupe->set_share_xp(100);
						$groupe->membre_joueur[0] = new perso();
						$groupe->membre_joueur[0]->set_x($actif->get_x());
						$groupe->membre_joueur[0]->set_y($actif->get_y());
						$groupe->membre_joueur[0]->set_id($actif->get_id());
						$groupe->membre_joueur[0]->share_xp = 100;
						$groupe->membre_joueur[0]->set_race($actif->get_race());
						$groupe->membre_joueur[0]->set_level($actif->get_level());
						$groupe->membre_joueur[0]->set_exp($actif->get_exp());
						$groupe->membre_joueur[0]->set_star($actif->get_star());
						$groupe->membre_joueur[0]->set_honneur($actif->get_honneur());
						$groupe->membre_joueur[0]->set_reputation($actif->get_reputation());
					}
					$G_range_level = ceil($passif->get_level() * 0.5);
					$xp = $xp * (1 + (($passif->get_level() - $actif->get_level()) / $G_range_level));
					if($xp < 0) $xp = 0;
					//Si il est en groupe réduction de l'xp gagné par rapport au niveau du groupe
					if($actif->get_groupe() > 0)
					{
						$xp = $xp * $actif->get_level() / $groupe->get_level();
					}
					$honneur = floor($xp * 4);

					//Partage de l'xp au groupe
					foreach($groupe->membre_joueur as $membre)
					{
						//Facteur de diplomatie
						$requete = "SELECT ".$passif->get_race()." FROM diplomatie WHERE race = '".$membre->get_race()."'";
						$req_diplo = $db->query($requete);
						$row_diplo = $db->read_row($req_diplo);

						//Vérification crime
						if($membre->get_id() == $actif->get_id() AND $crime AND $actif->get_id() == $attaquant->get_id())
						{
							$points = $G_crime[$row_diplo[0]];
							$actif->set_crime($actif->get_crime() + $points);
							$msg_xp .=  'Vous tuez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime<br />';
						}
						$star = 0;
						if ($row_diplo[0] == 127) $row_diplo[0] = 0;
						//Si le défenseur est criminel
						if($pascrime)
						{
							switch($amende['statut'])
							{
								case 'bandit' :
									$row_diplo[0] = 5;
									$statut_joueur = 'Bandit';
								break;
								case 'criminel' :
									$row_diplo[0] = 10;
									$statut_joueur = 'Criminel';
									if($amende['prime'] > 0)
									{
										$star = $amende['prime'];
										$msg_xp .=  'Vous avez tué un criminel ayant une prime sur sa tête, vous gagnez '.$star.' stars.<br />';
										$requete = "UPDATE amende SET prime = 0 WHERE id = ".$amende['id'];
										$db->query($requete);
										$requete = "DELETE FROM prime_criminel WHERE id_amende = ".$amende['id'];
										$db->query($requete);
									}
								break;
							}

							$xp = $xp / 5;
							$honneur = $honneur / 5;
						}
						$facteur_xp = $row_diplo[0] * 0.2;
						$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
						if ($facteur_honneur < 0) $facteur_honneur = 0;
						//XP Final
						$partage = $groupe->get_share_xp($actif->get_pos());
						$partage = $partage == 0 ? 1 : $partage;
						$xp_gagne = floor(($xp * $facteur_xp) * $membre->share_xp / $partage);
						if($xp_gagne < 0) $xp_gagne = 0;
						$honneur_gagne = floor(($honneur * $facteur_honneur) * $membre->share_xp / $partage);
						//Buff moral
						if($membre->is_buff('moral')) $honneur_gagne = floor( $honneur_gagne * (1 + ($membre->get_buff('moral', 'effet') / 100)) );
						if($membre->is_buff('cacophonie')) $honneur_gagne = floor( $honneur_gagne * (1 - ($membre->get_buff('cacophonie', 'effet') / 100)) );
						$reputation_gagne = floor($honneur_gagne / 10);

						// Pas d'honneur pour un kill de sa propre race
						if ($membre->get_race() == $passif->get_race())
							$honneur_gagne = 0;

						$membre->set_star($membre->get_star() + $star);
						$membre->set_exp($membre->get_exp() + $xp_gagne);
						$membre->set_honneur($membre->get_honneur() + $honneur_gagne);
						$membre->set_reputation($membre->get_reputation() + $reputation_gagne);
						$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_gagne.' XP</strong>, <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong>, et <strong class="reward">'.$reputation_gagne.' points de réputation</strong><br />';
						$membre->sauver();
						if($membre->get_id() == $attaquant->get_id()) verif_action('J'.$row_diplo[0], $membre, 's');
						else verif_action('J'.$row_diplo[0], $membre, 'g');
					}
					
					// Augmentation du compteur de l'achievement
					if($actif->get_level() >= $passif->get_level()) // Kill d'un joueur d'un plus petit level
						$achiev = $actif->get_compteur('kill_lower');
					else
						$achiev = $actif->get_compteur('kill_higher');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
					
					// Achievement joueur meme race
					if($actif->get_race() == $passif->get_race())
					{
						$achiev = $actif->get_compteur('kill_race');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
					
					$achievement_bouche_oreille = achievement_type::create('variable', 'bouche_oreille');
					// Si le joueur mort l'a deja debloqué
					if($passif->already_unlocked_achiev($achievement_bouche_oreille[0]))
						$actif->unlock_achiev('bouche_oreille');
					
					$actif->set_frag($actif->get_frag() + 1);
					$passif->set_mort($passif->get_mort() + 1);
					$actif->sauver();
					$passif->sauver();
				}
			}
			elseif($type == 'monstre')
			{
				//Le défenseur est mort !
				if ($defenseur->get_hp() <= 0)
				{
					$coeff = 0.5;
					//Différence de level
					$diff_level = abs($attaquant->get_level() - $defenseur->get_level());
					//Perde d'honneur
					$coeff = 1 - ($diff_level * 0.02);

					$gains_xp = true;
					$coef = 1;
					$gains_drop = true;
					$gains_star = true;
					
					// On gere les monstres de donjon
					$map_monstre->kill_monstre_de_donjon();

					// On efface le monstre
					$map_monstre->supprimer();
					
					// Augmentation du compteur de l'achievement
					$achiev = $joueur->get_compteur('kill_monstres');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
					
					//Si c'est un Seigneur loup-garou on debloque l'achievement
					if($map_monstre->get_type() == 115)
						$joueur->unlock_achiev('seigneur_loup_garou');
					if($map_monstre->get_type() == 56)
						$joueur->unlock_achiev('sworling');
				}
				elseif ($attaquant->get_hp() <= 0 && !$pet) //L'attaquant est mort !
				{
					$coeff = 0.5;
					//Différence de level
					$diff_level = abs($attaquant->get_level() - $defenseur->get_level());
					//Perde d'honneur
					$coeff = 1 - ($diff_level * 0.02);
					if ($coeff != 1)
					{
						echo 'Vous perdez '.
							($joueur->get_honneur() - $joueur->get_honneur() * $coeff).
							' honneur en mourrant.<br />';
						$joueur->set_honneur($joueur->get_honneur() * $coeff);
					}
					$joueur->set_mort($joueur->get_mort() + 1);
					$joueur->sauver();

					//Si c'est Dévorsis
					if($map_monstre->get_type() == 61)
					{
						$gain_hp = floor($attaquant->get_hp_max() * 0.1);
						$map_monstre->set_hp($defenseur->get_hp() + $gain_hp);
						$map_monstre->sauver();
						echo 'Dévorsis regagne '.$gain_hp.' HP en vous tuant.<br />';
					}
					
					// achievement
					if($map_monstre->get_type() == 1)
					{	
						$joueur->unlock_achiev('killer_rabbit');
					}
				}
				else
				{
					$gains_xp = true;
					if($degat_defense > 0) $coef = 0.5 * ($degat_defense) / $joueur_defenseur->hp_max;
				}
				if($gains_xp)
				{
					//Niveau du groupe
					if($joueur->get_groupe() == 0)
					{
						$groupe = new groupe();
						$groupe->level_groupe = $joueur->get_level();
						$groupe->somme_groupe = $joueur->get_level();
						$groupe->set_share_xp(100);
						$groupe->membre_joueur[0] = $joueur;
						$groupe->membre_joueur[0]->share_xp = 100;
					}
					else
					{
						$groupe = new groupe($joueur->get_groupe());
						$groupe->get_membre();
					}
					//Gain d'expérience
					$requete = "SELECT xp, star, drops FROM monstre WHERE id = '".$map_monstre->get_type()."'";
					$req = $db->query($requete);
					$row = $db->read_row($req);
					$xp = $row[0] * $G_xp_rate * $coef;
				}
				if($gains_drop)
				{
						$drop = $row[2];
				}
				if($gains_star)
				{
					$starmax = $row[1];
					$starmin = floor($row[1] / 2);
					$star = rand($starmin, $starmax) * $G_drop_rate;
					if($joueur->get_race() == 'nain') $star = floor($star * 1.1);
					if($joueur->is_buff('recherche_precieux')) $star = $star * (1 + ($joueur->get_buff('recherche_precieux', 'effet') / 100));
					$star = ceil($star);
					$taxe = floor($star * $R->get_taxe_diplo($joueur->get_race()) / 100);
					$star = $star - $taxe;
					//Récupération de la taxe
					if($taxe > 0)
					{
						$R->gain_star($taxe, 'monstre');
						$R->sauver();
					}
				}

				if($gains_drop)
				{
					//Drop d'un objet ?
					$drops = explode(';', $drop);
					if($drops[0] != '')
					{
						$count = count($drops);
						$i = 0;
						while($i < $count)
						{
							$share = explode('-', $drops[$i]);
							$objet = $share[0];
							$taux = ceil($share[1] / $G_drop_rate);
							if($joueur->get_race() == 'humain') $taux = $taux / 1.3;
							if($joueur->is_buff('fouille_gibier')) $taux = $taux / (1 + ($joueur->get_buff('fouille_gibier', 'effet') / 100));
							if ($taux < 2) $taux = 2; // Comme ca, pas de 100%
							$tirage = rand(1, floor($taux));
							//Si c'est un objet de quête :
							if($objet[0] == 'q')
							{
								$check = false;
								$i_quete = 0;
								$liste_quete = $joueur->get_liste_quete();
								$count_quete = count($liste_quete);
								while(!$check AND $i_quete < $count_quete)
								{
									if($liste_quete[$i_quete]['id_quete'] == $share[1])
										$check = true;
									$i_quete++;
								}
								if($check) $tirage = 1;
								else $tirage = 2;
							}
							if($tirage == 1)
							{
								$type_obj = '';
								//Nom de l'objet
								switch($objet[0])
								{
									case 'h' :
										$objet_nom = 'Objet non identifié';
										//Gemme aléatoire
										if($objet[1] == 'g')
										{
											//Niveau de la gemme
											$niveau_gemme = $objet[2];
											//Recherche des gemmes de ce niveau
											$ids = array();
											$requete = "SELECT id FROM gemme WHERE niveau = ".$niveau_gemme;
											$req_g = $db->query($requete);
											while($row = $db->read_row($req_g))
											{
												$ids[] = $row[0];
											}
											$num = rand(0, (count($ids) - 1));
											$objet = 'hg'.$ids[$num];
										}
									break;
									case 'o' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM objet WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = $row[0];
									break;
									case 'm' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM accessoire WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = $row[0];
									break;
									case 'a' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM arme WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = $row[0];
									break;
									case 'p' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM armure WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = $row[0];
									break;
									case 'r' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom, difficulte FROM recette WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = 'Recette unique : '.$row[0];
										$recette_difficulte = $row[1];
									break;
									case 'q' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM objet WHERE id = ".$id_objet;
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = $row[0];
										$objet = 'o'.$id_objet;
										$type_obj = 'quete';
									break;
									case 'l' :
										$id_objet = mb_substr($objet, 1);
										$requete = "SELECT nom FROM grimoire WHERE id = $id_objet";
										$req = $db->query($requete);
										$row = $db->read_row($req);
										$objet_nom = 'Grimoire : '.$row[0];
									break;
								}
								echo 'Vous fouillez le corps du monstre et découvrez "'.$objet_nom.'" !<br />';
								//Si le joueur a un groupe
								if($joueur->get_groupe() > 0 AND $type_obj != 'quete')
								{
									//Répartition en fonction du mode de distribution
									switch($groupe->get_partage())
									{
										//Aléatoire
										case 'r' :
											echo 'Répartition des objets aléatoire.<br />';
											$chance = count($groupe->membre);
											$aleat = rand(1, $chance);
											$gagnant = new perso($groupe->membre[($aleat - 1)]->get_id_joueur());
										break;
										//Par tour
										case 't' :
											echo 'Répartition des objets par tour.<br />';
											$gagnant = new perso($groupe->get_prochain_loot());
											//Changement du prochain loot
											$j_g = $groupe->trouve_position_joueur($groupe->get_prochain_loot());
											//Si c'est pas le dernier alors suivant
											if((count($groupe->membre) - 1) != $j_g)
											{
												$groupe->set_prochain_loot($groupe->membre[($j_g + 1)]->get_id_joueur());
											}
											//Sinon premier
											else
											{
												$groupe->set_prochain_loot($groupe->membre[0]->get_id_joueur());
											}
											$groupe->sauver();
										break;
										//Leader
										case 'l' :
											echo 'Répartition des objets au leader.<br />';
											$gagnant = new perso($groupe->get_id_leader());
										break;
										//Celui qui trouve garde
										case 'k' :
											echo 'Répartition des objets, celui qui trouve garde.<br />';
											$gagnant = new perso($joueur->get_id());
										break;
									}
									echo $gagnant->get_nom().' reçoit "'.$objet_nom.'"<br />';
								}
								else
								{
									$gagnant = new perso($joueur->get_id());
								}
								//Insertion du loot dans le journal du gagnant
								$requete = "INSERT INTO journal VALUES(NULL, ".$gagnant->get_id().", 'loot', '', '', NOW(), '".mysql_escape_string($objet_nom)."', 0, ".$attaquant->get_x().", ".$attaquant->get_y().")";
								$db->query($requete);
								if($objet[0] != 'r')
								{
									$gagnant->restack_objet();
									if($type_obj == 'quete')
									{
										verif_action('L'.$id_objet, $gagnant, 's');
										$gagnant->prend_objet($objet);
									}
									else
									{
										$gagnant->prend_objet($objet);
									}
								}
								else
								{
									prend_recette($objet, $gagnant);
								}
							}
							$i++;
						}
					}
				}

				if($gains_xp)
				{
					//Partage de l'xp au groupe
					if ($xp < 0) $xp = 0;

					$groupe->get_share_xp($joueur->get_pos());
					foreach($groupe->membre_joueur as $membre)
					{
						//XP Final
						$xp_joueur = $xp * (1 + (($defenseur->get_level() - $membre->get_level()) / $G_range_level));
						$xp_joueur = floor($xp_joueur * $membre->share_xp / $groupe->get_share_xp($joueur->get_pos()));
						if($xp_joueur < 0) $xp_joueur = 0;
						$membre->set_exp($membre->get_exp() + $xp_joueur);
						if($gains_star)
						{
							$star_joueur = floor($star * $membre->share_xp / $groupe->get_share_xp($joueur->get_pos()));
							$membre->set_star($membre->get_star() + $star_joueur);
						}
						else $star_joueur = 0;
						$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_joueur.' XP</strong> et <strong class="reward">'.$star_joueur.' Stars</strong><br />';
						//Vérification de l'avancement des quêtes solo pour le tueur, groupe pour les autres
						if($defenseur->get_hp() <= 0)
						{
							if($membre->get_id() == $attaquant->get_id()) verif_action('M'.$map_monstre->get_type(), $membre, 's');
							else verif_action('M'.$map_monstre->get_type(), $membre, 'g');
						}
						$membre->sauver();
					}
				}
			}
			elseif($type == 'batiment')
			{
				if($defenseur->get_hp() <= 0)
				{
					//On supprime un bourg au compteur
					if($defenseur->get_type() == 'bourg')
					{
						supprime_bourg($R->get_id());
					}
					//On retrouve les points de victoire
					$point_victoire = $defenseur->get_point_victoire();
					$R->add_point_victoire($point_victoire);
					//On efface le batiment
					$map_batiment->supprimer();
				}
			}

			if ($defenseur->get_hp() > 0)
			{
				if($check_pet) $link = '&pet'; else $link = '';
				if($type == 'joueur') echo(' <a href="attaque.php?id_joueur='.$joueur_defenseur->get_id().'&amp;type=joueur'.$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'monstre') echo(' <a href="attaque.php?id_monstre='.$map_monstre->get_id().'&amp;type=monstre'.$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'batiment') echo(' <a href="attaque.php?id_batiment='.$map_batiment->get_id().'&amp;type=batiment&amp;table='.$_GET['table'].$link.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
			}
			

			if (!$check_pet AND !$check_pet_def AND ($attaquant->get_compteur_critique() > 0))
			{

				$achiev = $attaquant->get_compteur('critique');
				$achiev->set_compteur($achiev->get_compteur() + $attaquant->get_compteur_critique());
				$achiev->sauver();
			}
			if (!$check_pet AND !$check_pet_def AND ($defenseur->get_compteur_critique() > 0))
			{
				$achiev = $defenseur->get_compteur('critique');
				$achiev->set_compteur($achiev->get_compteur() + $defenseur->get_compteur_critique());
				$achiev->sauver();
			}
			//Suppression des PA si c'est une attaque du joueur
			if($type == 'joueur' OR $type == 'monstre' OR $type == 'batiment')
			{
				$joueur->set_pa($joueur->get_pa() - $pa_attaque);
				$joueur->sauver();
			}
			//Sinon c'est une arme de siège, et il faut modifier son rechargement
			elseif ($type == 'siege' OR $type == 'ville')
			{
				$joueur->set_pa($joueur->get_pa() - $pa_attaque);
				$joueur->sauver();
				$map_siege->set_rechargement(time() + $siege->get_bonus('rechargement'));
				$map_siege->sauver();
			}
			else
			{
				echo "<b>Error: </b> type is [$type] !<br/>";
			}

			//Mise dans les journaux si attaque pvp
			if($type == 'joueur')
			{
				//Insertion de l'attaque dans les journaux des 2 joueurs
				$requete = "INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'attaque', '".$joueur->get_nom()."', '".$defenseur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				$db->query($requete);
				// Creation du log du combat
				$combat = new combat();
				$combat->attaquant = $joueur->get_id();
				$combat->defenseur = $joueur_defenseur->get_id();
				$combat->combat = $log_combat;
				$combat->id_journal = $db->last_insert_id();
				$combat->sauver();
				
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
