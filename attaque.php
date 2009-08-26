<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php // -*- tab-width:2 -*- 
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
$type = $_GET['type'];
switch($type)
{
	case 'joueur' :
		$joueur = new perso($_SESSION['ID']);
		$joueur_defenseur = new perso($_GET['id_joueur']);
		$joueur_defenseur->check_perso();
		$joueur->action_do = $joueur->recupaction('attaque');
		$joueur_defenseur->action_do = $joueur_defenseur->recupaction('defense');
		$attaquant = new entite('joueur', $joueur);
		$defenseur = new entite('joueur', $joueur_defenseur);
	break;
	case 'monstre' :
		$joueur = new perso($_SESSION['ID']);
		$map_monstre = new map_monstre($_GET['id_monstre']);
		$joueur->action_do = $joueur->recupaction('attaque');
		$joueur_defenseur = new monstre($map_monstre->get_type());
		$joueur_defenseur->hp_max = $joueur_defenseur->get_hp();
		$joueur_defenseur->set_hp($map_monstre->get_hp());
		$joueur_defenseur->x = $map_monstre->get_x();
		$joueur_defenseur->y = $map_monstre->get_y();
		$attaquant = new entite('joueur', $joueur);
		$defenseur = new entite('monstre', $joueur_defenseur);
	break;
	case 'batiment' :
		$joueur = new perso($_SESSION['ID']);
		$map_batiment = new construction($_GET['id_batiment']);
		$joueur->action_do = $joueur->recupaction('attaque');
		$joueur_defenseur = new batiment($map_batiment->get_id_batiment());
		$joueur_defenseur->hp_max = $joueur_defenseur->get_hp();
		$joueur_defenseur->set_hp($map_batiment->get_hp());
		$joueur_defenseur->x = $map_batiment->get_x();
		$joueur_defenseur->y = $map_batiment->get_y();
		$attaquant = new entite('joueur', $joueur);
		$defenseur = new entite('batiment', $joueur_defenseur);
	break;
}

$W_case = convert_in_pos($defenseur->get_x(), $defenseur->get_y());
$W_distance = detection_distance($W_case, convert_in_pos($attaquant->get_x(), $attaquant->get_y()));
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
	if($type == 'joueur')
	{
		//Récupération si la case est une ville et diplomatie
		$chateau = false;
		$requete = "SELECT type FROM map WHERE id = ".$W_case." AND type = 1 AND royaume = ".$Trace[$defenseur->get_race()]['numrace'];
		$db->query($requete);
		if($db->num_rows > 0)
		{
			echo 'Le défenseur est sur sa ville, application des bonus !<br />';
			$defenseur->set_pm($defenseur->get_pm() * 1.16);
			$defenseur->set_pp($defenseur->get_pp() * 1.3);
			$chateau = true;
		}
		//On vérifie si le défenseur est sur un batiment défensif
		$requete = "SELECT id_batiment FROM construction WHERE x = ".$defenseur->get_x()." AND y = ".$defenseur->get_y()." AND royaume = ".$Trace[$defenseur->get_race()]['numrace'];
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$row = $db->read_row($req);
			$requete = "SELECT bonus1, bonus2, bonus3 FROM batiment WHERE id = ".$row[0];
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
			$requete = "SELECT bonus1, bonus2 FROM batiment WHERE id = ".$row[0];
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
	} //fin $type = 'joueur'
	$round_total = $G_round_total;
	$round = 1;
	$attaquant->etat = array();
	$defenseur->etat = array();
	$debugs = 0;
	$pa_attaque = $G_PA_attaque_joueur;
	if($attaquant->get_race() == $defenseur->get_race()) $pa_attaque += 3;
	if($attaquant->get_race() == 'orc' OR $defenseur->get_race() == 'orc') $round_total += 1;
	if($attaquant->is_buff('buff_sacrifice')) $round_total -= $attaquant->get_buff('buff_sacrifice', 'effet2');
	if($attaquant->is_buff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $attaquant->get_buff('cout_attaque', 'effet'));
	if($attaquant->is_buff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $attaquant->get_buff('plus_cout_attaque', 'effet');
	if($attaquant->is_buff('buff_rapidite')) $reduction_pa = $attaquant->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($attaquant->is_buff('debuff_ralentissement')) $reduction_pa -= $attaquant->get_debuff('debuff_ralentissement', 'effet');
	if($attaquant->is_buff('engloutissement')) $attaquant->set_dexterite($attaquant->get_dexterite - $attaquant->get_buff('engloutissement', 'effet'));
	if($attaquant->is_buff('deluge')) $attaquant->set_volonte($attaquant->get_colonte - $attaquant->get_buff('deluge', 'effet'));
	if($defenseur->is_buff('engloutissement')) $defenseur->set_dexterite($defenseur->get_dexterite() - $defenseur->get_buff('engloutissement', 'effet'));
	if($defenseur->is_buff('deluge')) $defenseur->set_volonte($defenseur->get_volonte - $defenseur->get_buff('deluge', 'effet'));
	$pa_attaque = $pa_attaque - $reduction_pa;
	if($pa_attaque <= 0) $pa_attaque = 1;
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	if ($attaquant->get_pa() >= $pa_attaque)
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
						$joueur->set_crime($joueur->get_crime() + $points);
						echo '<h5>Vous attaquez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
					}
				}
			}

			$attaque_hp_avant = $attaquant->get_hp();
			$defense_hp_avant = $defenseur->get_hp();

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
						${$mode}->get_action();
						$action = script_action(${$mode}, ${$mode_def}, $mode, $effects);
						if(is_array($action[2])) ${$mode} = $action[2];
					}
					//print_r($action);
					$args = array();
					$args_def = array();
					//echo $action[0];
					$hp_avant = ${$mode_def}->get_hp();
					$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
					switch($action[0])
					{
						//Attaque
						case 'attaque' :
							$augmentations = attaque($mode, ${$mode}->get_comp_combat(), $effects);
						break;
						//Lancement d'un sort
						case 'lance_sort' :
							$augmentations = lance_sort($action[1], $mode, $effects);
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							$augmentations = lance_comp($action[1], $mode, $effects);
							if($comp_attaque)
							{
								attaque($mode, ${$mode}->get_comp_combat(), $effects);
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
						$joueur = augmentation_competences($augmentations['actif'], $joueur);
						$joueur_defenseur = augmentation_competences($augmentations['passif'], $joueur_defenseur);
					}
					else
					{
						$joueur_defenseur = augmentation_competences($augmentations['actif'], $joueur_defenseur);
						$joueur = augmentation_competences($augmentations['passif'], $joueur);
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
					$joueur->sauver();
					//Defenseur
					$joueur_defenseur->sauver();
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
			$survie = $joueur->get_survie();
			if($joueur->is_competence('survie_humanoide')) $survie += $joueur->get_competence('survie_humanoide');
			$nbr_barre_total = ceil($survie / $defenseur->get_level());
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur->get_hp() / $defenseur->get_hp_max()) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total), 2);
			echo '
			<hr />';
			
			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $joueur, 2);
			if($augmentation[1] == 1)
			{
				$joueur->set_survie($augmentation[0]);
				echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_survie().' en '.$Gtrad['survie'].'</span><br />';
			}
			if($joueur->is_competence('survie_humanoide'))
			{
				//Augmentation des compétences liées
				$augmentation = augmentation_competence('survie_humanoide', $joueur, 4);
				if($augmentation[1] == 1)
				{
					$joueur->set_comp('survie_humanoide', $augmentation[0]);
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$augmentation[0].' en '.$Gtrad['survie_humanoide'].'</span><br />';
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

			if($type == 'joueur')
			{
				$gains = false;
				$coef = 1;
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
						$reputation_gagne = floor($honneur_gagne / 10);
						$membre->set_star($membre->get_star() + $star);
						$membre->set_xp($membre->get_xp() + $xp_gagne);
						$membre->set_honneur($membre->get_honneur() + $honneur_gagne);
						$membre->set_reputation($membre->get_reputation() + $reputation_gagne);
						$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_gagne.' XP</strong> et <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong><br />';
						if($membre->get_id() == $attaquant->get_id()) verif_action('J'.$row_diplo[0], $membre, 's');
						else verif_action('J'.$row_diplo[0], $membre, 'g');
						$membre->sauver();
					}
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
					if($ennemi == 'monstre')
					{
						$coeff = 0.5;
						//Différence de level
						$diff_level = abs($attaquant['level'] - $defenseur['level']);
						//Perde d'honneur
						$coeff = 1 - ($diff_level * 0.02);
						//Si c'est Dévorsis
						if($defenseur['ID'] == 61)
						{
							$gain_hp = floor($attaquant['hp_max'] * 0.1);
							$defenseur['hp'] += $gain_hp;
							echo 'Dévorsis regagne '.$gain_hp.' HP en vous tuant.<br />';
						}
						$gains_xp = true;
						$coef = 1;
						$gains_drop = true;
						$gains_star = true;

						//On efface le monstre
						$requete = "DELETE FROM map_monstre WHERE ID = '".$W_ID."'";
						$req = $db->query($requete);
						//Si c'est Devorsis on fait pop le fossoyeur
						if($defenseur['type'] == 64)
						{
							$requete = "INSERT INTO map_monstre VALUES(NULL, '65','3','212','4800', 6, '".addslashes('Le Fossoyeur')."','fossoyeur', ".(time() + 2678400).")";
							$db->query($requete);
							echo '<strong>Rha, tu me détruis aujourdhui mais le fossoyeur saura saisir ton âme... tu es déja mort !</strong>';
						}
						//Si c'est le fossoyeur on fait pop finwir
						if($defenseur['type'] == 65)
						{
							$requete = "INSERT INTO map_monstre VALUES(NULL, '75','24','209','8000', 7, '".addslashes('Finrwirr le serviteur')."','finrwirr', ".(time() + 2678400).")";
							$db->query($requete);
							echo '<strong>Tu ne fait que retarder l\'inévitable, Le maître saura te faire payer ton insolence !</strong>';
						}
						//Si c'est Finrwirr on fait pop le gros monstre
						/*if($defenseur['type'] == 75)
						{
							$requete = "INSERT INTO map_monstre VALUES(NULL, '116','24','209','10000', 8, '".addslashes('Adenaïos le nécromant')."','adennaios', ".(time() + 2678400).")";
							$db->query($requete);
							echo '<strong>Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!</strong>';
						}*/
						//Si c'est un draconide
						if($defenseur['type'] == 125 OR $defenseur['type'] == 126)
						{
							//Si les 2 sont morts, on fait pop le roi gobelin
							$requete = "SELECT type FROM map_monstre WHERE type = 125 OR type = 126";
							$req_d = $db->query($requete);
							//Si il n'est pas là on le fait pop
							if($db->num_rows($req_d) == 0)
							{
								$requete = "INSERT INTO map_monstre VALUES(NULL,'123','44','293','5800', 18, 'Roi Goblin','roi_goblin', ".(time() + 2678400).")";
								$db->query($requete);
								echo '<strong>Un bruit de mécanisme eveil votre attention, mais il vous est impossible de savoir d\'où provient ce son.</strong>';
							}
						}
					}
					elseif($ennemi == 'batiment')
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
						$defenseur->supprimer();
					}
				}
				else
				{
					if($ennemi == 'monstre')
					{
						$gains_xp = true;
						$coef = 0.5 * ($defenseur_hp_avant - $defenseur_hp_apres) / $defenseur_hp_total;
					}
				}

				if($gains_xp)
				{
						//Niveau du groupe
						if($attaquant->get_groupe() == 0)
						{
							$groupe = new groupe();
							$groupe->level_groupe = $attaquant->get_level();
							$groupe->somme_groupe = $attaquant->get_level();
							$groupe->share_xp = 100;
							$groupe->membres[0]['id_joueur'] = $attaquant->get_id();
							$groupe->membres[0]['share_xp'] = 100;
							$groupe->membres[0]['level'] = $attaquant->get_level();
						}
						//Gain d'expérience
						$requete = "SELECT xp, star, drops FROM monstre WHERE id = '".$defenseur['type']."'";
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
						if($attaquant->get_race() == 'nain') $star = floor($star * 1.1);
						if(in_array('recherche_precieux', $attaquant['buff'])) $star = $star * (1 + ($attaquant['buff']['recherche_precieux']['effet'] / 100));
						$star = ceil($star);
						$taxe = floor($star * $R['taxe'] / 100);
						$star = $star - $taxe;
						//Récupération de la taxe
						if($taxe > 0)
						{
							$R->set_star($R->get_star() + $taxe);
							$requete = "UPDATE argent_royaume SET monstre = monstre + ".$taxe." WHERE race = '".$R->get_race()."'";
							$db->query($requete);
						}
				}

				$groupe = new groupe($joueur->get_groupe());
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
								if($attaquant->get_race() == 'humain') $taux = floor($taux / 1.3);
								if(in_array('fouille_gibier', $attaquant['buff'])) $taux = floor($taux / (1 + ($attaquant['buff']['fouille_gibier']['effet'] / 100)));
								$tirage = rand(1, $taux);
								//Si c'est un objet de quête :
								if($objet[0] == 'q')
								{
									$check = false;
									$i_quete = 0;
									$count_quete = count($attaquant['quete']);
									while(!$check AND $i_quete < $count_quete)
									{
										if($attaquant['quete'][$i_quete]['id_quete'] == $share[1]) $check = true;
										$i_quete++;
									}
									if($check) $tirage = 1;
									else $tirage = 2;
								}
								if($tirage == 1)
								{
									$type = '';
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
											$type = 'quete';
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
									if($attaquant['groupe'] > 0 AND $type != 'quete')
									{
										//Répartition en fonction du mode de distribution
										switch($groupe['partage'])
										{
											//Aléatoire
											case 'r' :
												echo 'Répartition des objets aléatoire.<br />';
												$chance = count($groupe['membre']);
												$aleat = rand(1, $chance);
												$gagnant = recupperso($groupe['membre'][($aleat - 1)]['id_joueur']);
											break;
											//Par tour
											case 't' :
												echo 'Répartition des objets par tour.<br />';
												$gagnant = recupperso($groupe['prochain_loot']);
												//Changement du prochain loot
												$j_g = groupe_trouve_joueur($groupe['prochain_loot'], $groupe);
												//Si c'est pas le dernier alors suivant
												if(($groupe['nombre_joueur'] - 1) != $j_g)
												{
													$requete = "UPDATE groupe SET prochain_loot = ".$groupe['membre'][($j_g + 1)]['id_joueur']." WHERE id = ".$groupe['id'];
												}
												//Sinon premier
												else
												{
													$requete = "UPDATE groupe SET prochain_loot = ".$groupe['membre'][0]['id_joueur']." WHERE id = ".$groupe['id'];
												}
												$db->query($requete);
											break;
											//Leader
											case 'l' :
												echo 'Répartition des objets au leader.<br />';
												$gagnant = recupperso($groupe['id_leader']);
											break;
											//Celui qui trouve garde
											case 'k' :
												echo 'Répartition des objets, celui qui trouve garde.<br />';
												$gagnant = recupperso($attaquant['ID']);
											break;
										}
										echo $gagnant['nom'].' reçoit "'.$objet_nom.'"<br />';
									}
									else
									{
										$gagnant = recupperso($attaquant['ID']);
									}
									//Insertion du loot dans le journal du gagnant
									$requete = "INSERT INTO journal VALUES('', ".$gagnant['ID'].", 'loot', '', '', NOW(), '".mysql_escape_string($objet_nom)."', '', ".$attaquant->get_x().", ".$attaquant->get_y().")";
									$db->query($requete);
									if($objet[0] != 'r')
									{
										if($type == 'quete')
										{
											verif_action('L'.$id_objet, $gagnant, 's');
											$gagnant = prend_objet($objet, $gagnant);
										}
										else
										{
											$gagnant = prend_objet($objet, $gagnant);
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

					foreach($groupe->membres as $membre)
					{
						//XP Final
						$xp_joueur = $xp * (1 + (($defenseur['level'] - $membre->get_level()) / $G_range_level));
						$xp_joueur = floor($xp_joueur * $membre->get_share_xp() / $groupe->get_share_xp());
						if($xp_joueur < 0) $xp_joueur = 0;
						if($gains_star)
						{
							$star_joueur = floor($star * $membre->get_share_xp() / $groupe->get_share_xp());
							$membre->set_star($membre->get_star() + $star_joueur);
						}
						$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_joueur.' XP</strong> et <strong class="reward">'.$star_joueur.' Stars</strong><br />';
						//Vérification de l'avancement des quètes solo pour le tueur, groupe pour les autres
						if($membre->get_id() == $attaquant->get_id()) verif_action('M'.$defenseur['type'], $membre, 's');
						else verif_action('M'.$defenseur['type'], $membre, 'g');
					}
				}
			}

			if ($defenseur->get_hp() >= 0)
			{
				if($type == 'joueur') echo(' <a href="attaque.php?id_joueur='.$joueur_defenseur->get_id().'&amp;type=joueur" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'monstre') echo(' <a href="attaque.php?id_monstre='.$map_monstre->get_id().'&amp;type=monstre" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
				elseif($type == 'batiment') echo(' <a href="attaque.php?id_batiment='.$map_batiment->get_id().'&amp;type=batiment" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
			}

			$joueur->set_pa($joueur->get_pa() - $pa_attaque);
			$joueur->sauver();
			$joueur_defenseur->sauver();

			//Mise dans les journaux si attaque pvp
			if($type == 'joueur')
			{
				//Insertion de l'attaque dans les journaux des 2 joueurs
				$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'attaque', '".$joueur->get_nom()."', '".$defenseur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$joueur_defenseur->get_id().", 'defense', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
				$db->query($requete);
				if($defenseur->get_hp() <= 0)
				{
					$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'tue', '".$joueur->get_nom()."', '".$joueur_defenseur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
					$requete = "INSERT INTO journal VALUES('', ".$joueurdefenseur->get_id().", 'mort', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueurdefenseur->get_y().")";
					$db->query($requete);
				}
				elseif($attaquant->get_hp() <= 0)
				{
					$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'mort', '".$joueur->get_nom()."', '".$joueur_defenseur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
					$requete = "INSERT INTO journal VALUES('', ".$joueur_defenseur->get_id().", 'tue', '".$joueur_defenseur->get_nom()."', '".$joueur->get_nom()."', NOW(), 0, 0, ".$joueur_defenseur->get_x().", ".$joueur_defenseur->get_y().")";
					$db->query($requete);
				}
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
