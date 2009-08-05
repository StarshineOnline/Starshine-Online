<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php // -*- tab-width:2 -*- 
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
//L'ID du du joueur attaqué
$W_ID = $_GET['ID'];

$attaquant = new perso($_SESSION['ID']);
$defenseur = new perso($W_ID);

$attaquant->action = recupaction($attaquant->get_action_a());
$defenseur->action = recupaction($defenseur->get_action_d());

$defenseur->check_perso();

$W_case = convert_in_pos($defenseur->get_x(), $defenseur->get_y());
$W_coord = convert_in_coord($W_case);
$W_distance = detection_distance($W_case, convert_in_pos($joueur->get_x(), $joueur->get_y()));
?>
<fieldset>
	<legend>Combat VS <?php echo $defenseur->get_nom(); ?></legend>
<?php
if($W_distance > $attaquant->get_distance_tir())
{
	echo '<h5>Vous êtes trop loin pour l\'attaquer !</h5>';
}
elseif($attaquant->get_hp() <= 0 OR $defenseur->get_hp() <= 0)
{
	echo '<h5>Un des protagonistes n\'a plus de points de vie</h5>';
}
else
{
	//Récupération si la case est une ville et diplomatie
	$chateau = false;
	$requete = "SELECT type FROM map WHERE id = ".$W_case." AND type = 1 AND royaume = ".$Trace[$defenseur->get_race()]['numrace'];
	$db->query($requete);
	if($db->num_rows > 0)
	{
		echo 'Le défenseur est sur sa ville, application des bonus !<br />';
		$defenseur->pm = $defenseur->get_pm() * 1.16;
		$defenseur->pp = $defenseur->get_pp() * 1.3;
		$chateau = true;
	}
	//On vérifie si le défenseur est sur un batiment défensif
	$requete = "SELECT id_batiment FROM construction WHERE x = ".$W_coord['x']." AND y = ".$W_coord['y']." AND royaume = ".$Trace[$defenseur->get_race()]['numrace'];
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_row($req);
		$requete = "SELECT * FROM batiment WHERE id = ".$row[0];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		switch($row['type'])
		{
			case 'fort' :
				//Augmentation des chances d'esquiver
				$defenseur->add_buff('batiment_esquive', $row['bonus1']);
				//Augmentation de la PP
				$defenseur->add_buff('batiment_pp', $row['bonus2']);
				//Augmentation de la PM
				$defenseur->add_buff('batiment_pm', $row['bonus3']);
			break;
		}
	}
	//On vérifie si l'attaquant est sur un batiment offensif
	$requete = "SELECT id_batiment FROM construction WHERE x = ".$attaquant->get_x()." AND y = ".$attaquant->get_y()." AND royaume = ".$Trace[$attaquant->get_race()]['numrace'];
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$row = $db->read_row($req);
		$requete = "SELECT * FROM batiment WHERE id = ".$row[0];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		switch($row['type'])
		{
			case 'tour' :
				//Augmentation de tir à distance
				$defenseur->add_buff('batiment_distance', $row['bonus1']);
				//Augmentation de l'ancement de sorts
				$defenseur->add_buff('batiment_incantation', $row['bonus2']);
			break;
		}
	}
	$round_total = $G_round_total;
	$round = 1;
	$attaquant->etat = array();
	$defenseur->etat = array();
	$debugs = 0;
	$pa_attaque = $G_PA_attaque_joueur;
	if($attaquant->get_race() == $defenseur->get_race()) $pa_attaque += 3;
	if($attaquant->get_race() == 'orc' OR $defenseur->get_race() == 'orc') $round_total += 1;
	if($attaquant->is_buff('buff_sacrifice')) $round_total -= $attaquant->get_buff('buff_sacrifice', 'effet2');
	if($attaquant->is_debuff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $attaquant->get_debuff('cout_attaque', 'effet'));
	if($attaquant->is_debuff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $attaquant->get_debuff('plus_cout_attaque', 'effet');
	if($attaquant->is_buff('buff_rapidite')) $reduction_pa = $attaquant->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($attaquant->is_debuff('debuff_ralentissement')) $reduction_pa -= $attaquant->get_debuff('debuff_ralentissement', 'effet');
	if($attaquant->is_debuff('engloutissement')) $attaquant['dexterite'] -= $attaquant->get_debuff('engloutissement', 'effet');
	if($attaquant->is_debuff('deluge')) $attaquant['volonte'] -= $attaquant->get_debuff('deluge', 'effet');
	if($defenseur->is_debuff('engloutissement')) $defenseur->get_dexterite() -= $defenseur->get_debuff('engloutissement', 'effet');
	if($defenseur->is_debuff('deluge')) $defenseur['volonte'] -= $defenseur->get_debuff('deluge', 'effet');
	$pa_attaque = $pa_attaque - $reduction_pa;
	if($pa_attaque <= 0) $pa_attaque = 1;
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	if ($attaquant->get_pa() >= $pa_attaque)
	{
		if($attaquant->get_hp() > 0)
		{
			//Suppresion de longue portée si besoin
			if($attaquant->id_buff('longue_portee') AND $attaquant['arme_type'] == 'arc')
			{
				$requete = "DELETE FROM buff WHERE id = ".$attaquant->get_buff('longue_portee', 'id');
				$db->query($requete);
			}
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
					$amende = recup_amende($defenseur->get_id());
					if($amende)
					{
						if($amende['statut'] != 'normal') $pascrime = true;
					}
				}
				if(!$pascrime)
				{
					$crime = true;
					$points = ($G_crime[$row[0]] / 10);
					$attaquant->set_crime($attaquant->get_crime() + $points);
					echo '<h5>Vous attaquez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
				}
			}

			$attaque_hp_avant = $attaquant->get_hp();
			$defense_hp_avant = $defenseur->get_hp();

			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant->get_hp() > 0) AND ($defenseur->get_hp() > 0))
			{
				if($attaquant['arme_type'] == 'arc') $attaquant->comp = 'distance'; else $attaquant->comp = 'melee';
				if($defenseur['arme_type'] == 'arc') $defenseur->comp = 'distance'; else $defenseur->comp = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant->potentiel_toucher = round($attaquant[$attaquant->comp] + ($attaquant[$attaquant->comp] * ((pow($attaquant->get_dexterite(), 2)) / 1000)));
				$defenseur->potentiel_toucher = round($defenseur[$defenseur->comp] + ($defenseur[$defenseur->comp] * ((pow($defenseur->get_dexterite(), 2)) / 1000)));
				$attaquant->potentiel_parer = round($attaquant->get_esquive() + ($attaquant->get_esquive() * ((pow($attaquant->get_dexterite(), 2)) / 1000)));
				if($chateau) $esquive = $defenseur->get_esquive() * 1.5; else $esquive = $defenseur->get_esquive();
				$defenseur->potentiel_parer = round($esquive + ($esquive * ((pow($defenseur->get_dexterite(), 2)) / 1000)));
				$attaquant->degat_sup = 0;
				$attaquant->degat_moins = 0;
				$defenseur->degat_sup = 0;
				$defenseur->degat_moins = 0;
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');

				$effects = effect::general_factory($attaquant, $defenseur, $mode);

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
					}
					else
					{
						$action = script_action(${$mode}, ${$mode_def}, $mode, $effects);
						if(is_array($action[2])) ${$mode} = $action[2];
					}
					$args = array();
					$args_def = array();
					//echo $action[0];
					$hp_avant = ${$mode_def}['hp'];
					switch($action[0])
					{
						//Attaque
						case 'attaque' :
							attaque($mode, ${$mode}->comp, $effects);
							$args[] = ${$mode}->comp.' = '.${$mode}->get_{${$mode}->comp}();
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
						break;
						//Lancement d'un sort
						case 'lance_sort' :
							lance_sort($action[1], $mode, $effects);
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							lance_comp($action[1], $mode, $effects);
							if($comp_attaque)
							{
								attaque($mode, ${$mode}->comp, $effects);
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
					if($mode == 'defenseur')
					{
						//Perte de HP par le poison
						if($attaquant->etat['poison']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['poison']['effet'] - $attaquant->etat['poison']['duree'] + 1;
							if($attaquant->etat['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $attaquant->etat['putrefaction']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						if($defenseur->etat['poison']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['poison']['effet'] - $defenseur->etat['poison']['duree'] + 1;
							if($defenseur->etat['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $defenseur->etat['putrefaction']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						//Perte de HP par hémorragie
						if($attaquant->etat['hemorragie']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['hemorragie']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par hémorragie</span><br />';
						}
						if($defenseur->etat['hemorragie']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['hemorragie']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par hémorragie</span><br />';
						}
						//Perte de HP par embrasement
						if($attaquant->etat['embraser']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['embraser']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par embrasement</span><br />';
						}
						if($defenseur->etat['embraser']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['embraser']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par embrasement</span><br />';
						}
						//Perte de HP par acide
						if($attaquant->etat['acide']['duree'] > 0)
						{
							$perte_hp = $attaquant->etat['acide']['effet'];
							$attaquant->set_hp($attaquant->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$perte_hp.' HP par acide</span><br />';
						}
						if($defenseur->etat['acide']['duree'] > 0)
						{
							$perte_hp = $defenseur->etat['acide']['effet'];
							$defenseur->set_hp($defenseur->get_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$perte_hp.' HP par acide</span><br />';
						}
						//Perte de HP par lien sylvestre
						if($attaquant->etat['lien_sylvestre']['duree'] > 0)
						{
							$attaquant->set_hp($attaquant->get_hp() - $attaquant->etat['lien_sylvestre']['effet']);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant->get_nom().' perd '.$attaquant->etat['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						if($defenseur->etat['lien_sylvestre']['duree'] > 0)
						{
							$defenseur->set_hp($defenseur->get_hp() - $defenseur->etat['lien_sylvestre']['effet']);
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur->get_nom().' perd '.$defenseur->etat['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						if($attaquant->etat['recuperation']['duree'] > 0)
						{
							$effet = $attaquant->etat['recuperation']['effet'];
							if(($attaquant->get_hp() + $effet) > $attaquant->etat['recuperation']['hp_max'])
							{
								$effet = $attaquant->etat['recuperation']['hp_max'] - $attaquant->get_hp();
							}
							$attaquant->set_hp($attaquant->get_hp() + $effet);
							if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$attaquant->get_nom().' gagne '.$effet.' HP par récupération</span><br />';
						}
						if($defenseur->etat['recuperation']['duree'] > 0)
						{
							$effet = $defenseur->etat['recuperation']['effet'];
							if(($defenseur->get_hp() + $effet) > $defenseur->etat['recuperation']['hp_max'])
							{
								$effet = $defenseur->etat['recuperation']['hp_max'] - $defenseur->get_hp();
							}
							$defenseur->set_hp($defenseur->get_hp() + $effet);
							if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$defenseur->get_nom().' gagne '.$effet.' HP par récupération</span><br />';
						}
					}

					//Update de la base de donnée.
					//Correction des bonus ignorables
					corrige_bonus_ignorables($attaquant, $defenseur, $mode, $args, $args_def);
					//Attaquant
					$attaquant->sauver();
					//Defenseur
					$defenseur->sauver();
					?>
					</div>
					<?php
				//Fin du round
				if($mode == 'defenseur')
				{
					$round++;
					?>
					</td>
				</tr>
			</table>
					<?php
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
			$survie = $attaquant->get_survie();
			if($attaquant->is_competence('survie_humanoide')) $survie += $attaquant->get_competence('survie_humanoide');
			$nbr_barre_total = ceil($survie / $defenseur->get_level());
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur->get_hp() / $defenseur->hp_max()) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total), 2);
			echo '
			<hr />';
			
			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $attaquant, 2);
			if($augmentation[1] == 1)
			{
				$attaquant->set_survie($augmentation[0]);
				echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant->get_survie().' en '.$Gtrad['survie'].'</span><br />';
			}
			if(array_key_exists('survie_humanoide', $attaquant['competences']))
			{
				//Augmentation des compétences liées
				$attaquant['survie_humanoide'] = $attaquant['competences']['survie_humanoide'];
				$augmentation = augmentation_competence('survie_humanoide', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant['survie_humanoide'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie_humanoide'].' en '.$Gtrad['survie_humanoide'].'</span><br />';
					$db->query("UPDATE comp_perso SET valeur = ".$augmentation[0]." WHERE id_perso = ".$attaquant->get_id()." AND competence = 'survie_humanoide'");
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
			$attaque_hp_apres = $attaquant->get_hp();
			$defense_hp_apres = $defenseur->get_hp();
			
			$gains = false;
			
			//L'attaquant est mort !
			if ($attaquant->get_hp() <= 0)
			{
				$actif = $defenseur;
				$passif = $attaquant;
				$gains = true;
			}
			//Le défenseur est mort !
			if ($defenseur->get_hp() <= 0)
			{
				$actif = $attaquant;
				$passif = $defenseur;
				$gains = true;
			}

			if($gains)
			{
				//Gain d'expérience
				$xp = $passif->get_level() * 100 * $G_xp_rate;
            	
				//Si le joueur a un groupe
				if($actif->get_groupe() > 0)
				{
					$groupe = new groupe($actif->get_groupe());
					$groupe->get_membre_joueur();
					//Si on tape un joueur de son groupe xp = 0
					foreach($groupe->membre_joueur as $membre_id)
					{
						if($membre_id->get_id() == $passif->get_id()) $xp = 0;
					}
				}
				//Joueur solo
				else
				{
					$groupe = new groupe();
					$groupe->level_groupe = $actif->get_level();
					$groupe->somme_groupe = $actif->get_level();
					$groupe->share_xp = 100;
					$groupe->membre_joueur[0]['id_joueur'] = $actif->get_id();
					$groupe->membre_joueur[0]['share_xp'] = 100;
					$groupe->membre_joueur[0]['race'] = $actif->get_race();
				}
				$G_range_level = ceil($passif->get_level * 0.5);
				$xp = $xp * (1 + (($passif->get_level() - $actif->get_level()) / $G_range_level));
				if($xp < 0) $xp = 0;
				//Si il est en groupe réduction de l'xp gagné par rapport au niveau du groupe
				if($actif->get_groupe() > 0)
				{
					$xp = $xp * $actif->get_level() / $groupe['level_groupe'];
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
					$xp_gagne = floor(($xp * $facteur_xp) * $membre->share_xp / $groupe->share_xp);
					$honneur_gagne = floor(($honneur * $facteur_honneur) * $membre->share_xp / $groupe->share_xp);
					$membre->set_star($membre->get_star() + $star);
					$membre->set_xp($membre->get_xp() + $xp_gagne);
					$membre->set_honneur($membre->get_honneur() + $honneur_gagne);
					$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_gagne.' XP</strong> et <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong><br />';
					if($membre->get_id() == $attaquant->get_id()) verif_action('J'.$row_diplo[0], $membre, 's');
					else verif_action('J'.$row_diplo[0], $membre 'g');
					$membre->sauver();
				}
				$actif->set_frag($actif->get_frag() + 1);
				$passif->set_mort($passif->get_mort() + 1);
				$actif->sauver();
				$passif->sauver();
			}

			if ($defenseur->get_hp() >= 0)
			{
				echo(' <a href="attaque.php?ID='.$W_ID.'&amp;poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
			}

			$attaquant->set_pa($attaquant->get_pa() - $pa_attaque);
			$attaquant->sauver();
			$defenseur->sauver();
	
			//Insertion de l'attaque dans les journaux des 2 joueurs
			$requete = "INSERT INTO journal VALUES('', ".$attaquant->get_id().", 'attaque', '".$attaquant->get_nom()."', '".$defenseur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur['x'].", ".$defenseur['y'].")";
			$db->query($requete);
			$requete = "INSERT INTO journal VALUES('', ".$defenseur->get_id().", 'defense', '".$defenseur->get_nom()."', '".$attaquant->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur['x'].", ".$defenseur['y'].")";
			$db->query($requete);
			if($defenseur->get_hp() <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant->get_id().", 'tue', '".$attaquant->get_nom()."', '".$defenseur->get_nom()."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur->get_id().", 'mort', '".$defenseur->get_nom()."', '".$attaquant->get_nom()."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
			}
			elseif($attaquant->get_hp() <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant->get_id().", 'mort', '".$attaquant->get_nom()."', '".$defenseur->get_nom()."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur->get_id().", 'tue', '".$defenseur->get_nom()."', '".$attaquant->get_nom()."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
			}
		}
		else
		{
			echo 'Vous êtes mort !<img src="image/pixel.gif" onload="window.location.reload();" />';
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
