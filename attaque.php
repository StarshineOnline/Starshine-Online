<?php // -*- tab-width:2 -*- 
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
//L'ID du du joueur attaqué
$W_ID = $_GET['ID'];

$attaquant = recupperso($_SESSION['ID']);
$defenseur = recupperso($W_ID);

$attaquant['action_a'] = recupaction($attaquant['action_a']);
$defenseur['action_d'] = recupaction($defenseur['action_d']);

$defenseur = check_perso($defenseur);

$W_case = convert_in_pos($defenseur['x'], $defenseur['y']);
$W_coord = convert_in_coord($W_case);
$W_distance = detection_distance($W_case, $_SESSION["position"]);
?>
<h2>COMBAT</h2>
<?php
if($W_distance > $attaquant['arme_distance'])
{
	echo '<h5>Vous êtes trop loin pour l\'attaquer !</h5>';
}
elseif($attaquant['hp'] <= 0 OR $defenseur['hp'] <= 0)
{
	echo '<h5>Un des protagonistes n\'a plus de points de vie</h5>';
}
else
{
	//Récupération si la case est une ville et diplomatie
	$chateau = false;
	$requete = "SELECT type FROM map WHERE ID = ".$W_case." AND type = 1 AND royaume = ".$Trace[$defenseur['race']]['numrace'];
	$db->query($requete);
	if($db->num_rows > 0)
	{
		echo 'Le défenseur est sur sa ville, application des bonus !<br />';
		$defenseur['PM'] *= 1.16;
		$defenseur['PP'] *= 1.3;
		$chateau = true;
	}
	//On vérifie si le défenseur est sur un batiment défensif
	$requete = "SELECT id_batiment FROM construction WHERE x = ".$W_coord['x']." AND y = ".$W_coord['y']." AND royaume = ".$Trace[$defenseur['race']]['numrace'];
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
				$defenseur['buff']['batiment_esquive']['effet'] = $row['bonus1'];
				//Augmentation de la PP
				$defenseur['buff']['batiment_pp']['effet'] = $row['bonus2'];
				//Augmentation de la PM
				$defenseur['buff']['batiment_pm']['effet'] = $row['bonus3'];
			break;
		}
	}
	//On vérifie si l'attaquant est sur un batiment offensif
	$requete = "SELECT id_batiment FROM construction WHERE x = ".$attaquant['x']." AND y = ".$attaquant['y']." AND royaume = ".$Trace[$attaquant['race']]['numrace'];
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
				$defenseur['buff']['batiment_distance']['effet'] = $row['bonus1'];
				//Augmentation de l'ancement de sorts
				$defenseur['buff']['batiment_incantation']['effet'] = $row['bonus2'];
			break;
		}
	}
	$round_total = $G_round_total;
	$round = 1;
	$attaquant['etat'] = array();
	$defenseur['etat'] = array();
	$debugs = 0;
	if($attaquant['race'] == 'orc' OR $defenseur['race'] == 'orc') $round_total += 1;
	if(array_key_exists('buff_sacrifice', $attaquant['buff'])) $round_total -= $attaquant['buff']['buff_sacrifice']['effet2'];
	if(array_key_exists('cout_attaque', $attaquant['debuff'])) $pa_attaque = ceil($pa_attaque / $attaquant['debuff']['cout_attaque']['effet']);
	if(array_key_exists('plus_cout_attaque', $attaquant['debuff'])) $pa_attaque = $pa_attaque * $attaquant['debuff']['plus_cout_attaque']['effet'];
	if(array_key_exists('buff_rapidite', $attaquant['buff'])) $reduction_pa = $attaquant['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
	if(array_key_exists('debuff_ralentissement', $attaquant['debuff'])) $reduction_pa -= $attaquant['debuff']['debuff_ralentissement']['effet'];
	if(array_key_exists('engloutissement', $attaquant['debuff'])) $attaquant['dexterite'] -= $attaquant['debuff']['engloutissement']['effet'];
	if(array_key_exists('engloutissement', $defenseur['debuff'])) $defenseur['dexterite'] -= $defenseur['debuff']['engloutissement']['effet'];
	if(array_key_exists('deluge', $attaquant['debuff'])) $attaquant['volonte'] -= $attaquant['debuff']['deluge']['effet'];
	if(array_key_exists('deluge', $defenseur['debuff'])) $defenseur['volonte'] -= $defenseur['debuff']['deluge']['effet'];
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	if ($attaquant['pa'] >= ($G_PA_attaque_joueur - $reduction_pa))
	{
		if($attaquant['hp'] > 0)
		{
			//Suppresion de longue portée si besoin
			if(array_key_exists('longue_portee', $attaquant['buff']) AND $attaquant['arme_type'] == 'arc')
			{
				$requete = "DELETE FROM buff WHERE id = ".$attaquant['buff']['longue_portee']['id'];
				$db->query($requete);
			}
			$crime = false;
			$requete = "SELECT ".$defenseur['race']." FROM diplomatie WHERE race = '".$attaquant['race']."'";
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$pascrime = false;
			//Vérification si crime
			if(array_key_exists($row[0], $G_crime))
			{
				if($row[0] == 127)
				{
					$amende = recup_amende($defenseur['ID']);
					if($amende)
					{
						if($amende['statut'] != 'normal') $pascrime = true;
					}
				}
				if(!$pascrime)
				{
					$crime = true;
					$points = ($G_crime[$row[0]] / 10);
					$requete = "UPDATE perso SET crime = crime + ".$points." WHERE ID = ".$attaquant['ID'];
					$db->query($requete);
					echo '<h5>Vous attaquez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime</h5>';
				}
			}

			$attaque_hp_avant = $attaquant['hp'];
			$defense_hp_avant = $defenseur['hp'];

			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant['hp'] > 0) AND ($defenseur['hp'] > 0))
			{
				if($attaquant['arme_type'] == 'arc') $attaquant['comp'] = 'distance'; else $attaquant['comp'] = 'melee';
				if($defenseur['arme_type'] == 'arc') $defenseur['comp'] = 'distance'; else $defenseur['comp'] = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant['potentiel_toucher'] = round($attaquant[$attaquant['comp']] + ($attaquant[$attaquant['comp']] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				$defenseur['potentiel_toucher'] = round($defenseur[$defenseur['comp']] + ($defenseur[$defenseur['comp']] * ((pow($defenseur['dexterite'], 2)) / 1000)));
				$attaquant['potentiel_parer'] = round($attaquant['esquive'] + ($attaquant['esquive'] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				if($chateau) $esquive = $defenseur['esquive'] * 1.5; else $esquive = $defenseur['esquive'];
				$defenseur['potentiel_parer'] = round($esquive + ($esquive * ((pow($defenseur['dexterite'], 2)) / 1000)));
				$attaquant['degat_sup'] = 0;
				$attaquant['degat_moins'] = 0;
				$defenseur['degat_sup'] = 0;
				$defenseur['degat_moins'] = 0;
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');
				
				if($mode == 'attaquant')
				{
					echo '
					<table style="width : 100%;">
						<tr>
							<td style="vertical-align : top; width : 20%;">
								<h3 style="margin-top : 3px;">Round '.$round.'</h3>
							</td>
							<td>';
					$etati = 0;
					$etatkeys = array_keys($defenseur['etat']);
					while($etati < count($defenseur['etat']))
					{
						$defenseur['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($defenseur['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($defenseur['etat'], $etati, 1);
						//else echo $defenseur['nom'].' est '.$etatkeys[$etati].' pour '.$defenseur['etat'][$etatkeys[$etati]]['duree'].'<br />';
						$etati++;
					}
				}
				else
				{
					$etati = 0;
					$etatkeys = array_keys($attaquant['etat']);
					while($etati < count($attaquant['etat']))
					{
						$attaquant['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($attaquant['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($attaquant['etat'], $etati, 1);
						//else echo $attaquant['nom'].' est '.$etatkeys[$etati].' pour '.$attaquant['etat'][$etatkeys[$etati]]['duree'].'<br />';
						$etati++;
					}
				}
					?>
					<div class="combat">
					<?php
					$W_distance_relative = $W_distance - $defenseur['arme_distance'] ;
					if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
					if(($mode == 'defenseur') && ($W_distance_relative >= $round))
					{
						echo $defenseur['nom'].' s\'approche<br />';
						$action[0] = '';
					}
					else
					{
						$action = script_action(${$mode}, ${$mode_def}, $mode);
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
							attaque($mode, ${$mode}['comp']);
							$args[] = ${$mode}['comp'].' = '.${$mode}[${$mode}['comp']];
							$count = count($ups);
							if($count > 0)
							{
								$upi = 0;
								while($upi < $count)
								{
									$requete = "UPDATE comp_perso SET valeur = ".${$mode}['competences'][$ups[$upi]]." WHERE id_perso = ".${$mode}['ID']." AND competence = '".$ups[$upi]."'";
									$db->query($requete);
									$upi++;
								}
							}
						break;
						//Lancement d'un sort
						case 'lance_sort' :
							$comp = lance_sort($action[1], $mode);
							$args[] = 'incantation = '.${$mode}['incantation'];
							$args[] = $comp.' = '.${$mode}[$comp];
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							$comp = lance_comp($action[1], $mode);
							if($comp_attaque)
							{
								attaque($mode, ${$mode}['comp']);
								$args[] = ${$mode}['comp'].' = '.${$mode}[${$mode}['comp']];
								$count = count($ups);
								if($count > 0)
								{
									$upi = 0;
									while($upi < $count)
									{
										$requete = "UPDATE comp_perso SET valeur = ".${$mode}['competences'][$ups[$upi]]." WHERE id_perso = ".${$mode}['ID']." AND competence = '".$ups[$upi]."'";
										$db->query($requete);
										$upi++;
									}
								}
							}
						break;
					}
					if($mode == 'defenseur')
					{
						//Perte de HP par le poison
						if($attaquant['etat']['poison']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['poison']['effet'] * $attaquant['etat']['poison']['duree'];
							if($attaquant['etat']['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $attaquant['etat']['putrefaction']['effet'];
							$attaquant['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						if($defenseur['etat']['poison']['duree'] > 0)
						{
							$perte_hp = $defenseur['etat']['poison']['effet'] * $defenseur['etat']['poison']['duree'];
							if($defenseur['etat']['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $defenseur['etat']['putrefaction']['effet'];
							$defenseur['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						//Perte de HP par hémorragie
						if($attaquant['etat']['hemorragie']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['hemorragie']['effet'];
							$attaquant['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$perte_hp.' HP par hémorragie</span><br />';
						}
						if($defenseur['etat']['hemorragie']['duree'] > 0)
						{
							$perte_hp = $defenseur['etat']['hemorragie']['effet'];
							$defenseur['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$perte_hp.' HP par hémorragie</span><br />';
						}
						//Perte de HP par embrasement
						if($attaquant['etat']['embraser']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['embraser']['effet'];
							$attaquant['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$perte_hp.' HP par embrasement</span><br />';
						}
						if($defenseur['etat']['embraser']['duree'] > 0)
						{
							$perte_hp = $defenseur['etat']['embraser']['effet'];
							$defenseur['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$perte_hp.' HP par embrasement</span><br />';
						}
						//Perte de HP par acide
						if($attaquant['etat']['acide']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['acide']['effet'];
							$attaquant['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$perte_hp.' HP par acide</span><br />';
						}
						if($defenseur['etat']['acide']['duree'] > 0)
						{
							$perte_hp = $defenseur['etat']['acide']['effet'];
							$defenseur['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$perte_hp.' HP par acide</span><br />';
						}
						//Perte de HP par lien sylvestre
						if($attaquant['etat']['lien_sylvestre']['duree'] > 0)
						{
							$attaquant['hp'] -= $attaquant['etat']['lien_sylvestre']['effet'];
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$attaquant['etat']['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						if($defenseur['etat']['lien_sylvestre']['duree'] > 0)
						{
							$defenseur['hp'] -= $defenseur['etat']['lien_sylvestre']['effet'];
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$defenseur['etat']['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						if($attaquant['etat']['recuperation']['duree'] > 0)
						{
							$effet = $attaquant['etat']['recuperation']['effet'];
							if(($attaquant['hp'] + $effet) > $attaquant['etat']['recuperation']['hp_max'])
							{
								$effet = $attaquant['etat']['recuperation']['hp_max'] - $attaquant['hp'];
							}
							$attaquant['hp'] += $effet;
							if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$attaquant['nom'].' gagne '.$effet.' HP par récupération</span><br />';
						}
						if($defenseur['etat']['recuperation']['duree'] > 0)
						{
							$effet = $defenseur['etat']['recuperation']['effet'];
							if(($defenseur['hp'] + $effet) > $defenseur['etat']['recuperation']['hp_max'])
							{
								$effet = $defenseur['etat']['recuperation']['hp_max'] - $defenseur['hp'];
							}
							$defenseur['hp'] += $effet;
							if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$defenseur['nom'].' gagne '.$effet.' HP par récupération</span><br />';
						}
					}
					$args[] = 'hp = '.${$mode}['hp'];
					$args_def[] = 'hp = '.${$mode_def}['hp'];
					$args_def[] = 'esquive = '.${$mode_def}['esquive'];
					if(${$mode_def}['bouclier']) $args_def[] = 'blocage = '.${$mode_def}['blocage'];

					//Update de la base de donnée.
					//Attaquant
					$requete = 'UPDATE perso SET '.implode(',', $args).' WHERE ID = '.${$mode}['ID'];
					$req = $db->query($requete);
					//Defenseur
					$requete = 'UPDATE perso SET '.implode(',', $args_def).' WHERE ID = '.${$mode_def}['ID'];
					$req = $db->query($requete);
					?>
					</div>
					<?php
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
			if($mode == 'attaquant')
			{
					?>
					</td>
				</tr>
			</table>
					<?php
			}
			$survie = $attaquant['survie'];
			if(array_key_exists('survie_humanoide', $attaquant['competences'])) $survie += $attaquant['competences']['survie_humanoide'];
			$nbr_barre_total = ceil($survie / $defenseur['level']);
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur['hp'] / $defenseur['hp_max_1']) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total), 2);
			echo '
			<table class="information_case" style="float:left;width:180px;">
				<tr style="text-align : center;">
					<td>
						'.$attaquant['nom'].'
					</td>
					<td>
						'.$attaquant['hp'].' HP
					</td>
				</tr>
				<tr style="text-align : center;">
					<td>
						'.$defenseur['nom'].'
					</td>
					<td>
						<img src="genere_barre_vie.php?longueur='.$longueur.'" title="Estimation des HP : '.$longueur.'% / + ou - : '.$fiabilite.'%" />
					</td>
				</tr>
			</table>
			<div class="information_case" style="float : right;width:220px;">
			';
			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $attaquant, 2);
			if($augmentation[1] == 1)
			{
				$attaquant['survie'] = $augmentation[0];
				echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie'].' en '.$Gtrad['survie'].'</span><br />';
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
					$db->query("UPDATE comp_perso SET valeur = ".$augmentation[0]." WHERE id_perso = ".$attaquant['ID']." AND competence = 'survie_humanoide'");
				}
			}
			$attaque_hp_apres = $attaquant['hp'];
			$defense_hp_apres = $defenseur['hp'];
			
			//L'attaquant est mort !
			if ($attaquant['hp'] <= 0)
			{
				//Gain d'expérience
				$xp = $attaquant['level'] * 100 * $G_xp_rate;
				
				//Niveau du groupe
				if($defenseur['groupe'] > 0)
				{
					$groupe = recupgroupe($defenseur['groupe'], $defenseur['x'].'-'.$defenseur['y']);
				}
				else
				{
					$groupe = array();
					$groupe['level_groupe'] = $defenseur['level'];
					$groupe['somme_groupe'] = $defenseur['level'];
					$groupe['share_xp'] = 100;
					$groupe['membre'][0]['id_joueur'] = $defenseur['ID'];
					$groupe['membre'][0]['share_xp'] = 100;
					$groupe['membre'][0]['race'] = $defenseur['race'];
				}
				$xp = $xp * (1 + (($attaquant['level'] - $defenseur['level']) / $G_range_level));
				if($xp < 0) $xp = 0;
				//Si il est en groupe réduction de l'xp gagné par rapport au niveau du groupe
				if($defenseur['groupe'] > 0)
				{
					$xp = $xp * $defenseur['level'] / $groupe['level_groupe'];
				}
				if ($xp < 0) $xp = 0;
				$honneur = floor($xp * 4);
				
				//Partage de l'xp au groupe
				foreach($groupe['membre'] as $membre)
				{
					//Facteur de diplomatie	
					$requete = "SELECT ".$attaquant['race']." FROM diplomatie WHERE race = '".$membre['race']."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					
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
							break;
						}
					}
					$facteur_xp = $row_diplo[0] * 0.2;
					$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
					if ($facteur_honneur < 0) $facteur_honneur = 0;
					
					//XP Final	
					$xp_gagne = floor(($xp * $facteur_xp) * $membre['share_xp'] / $groupe['share_xp']);
					$honneur_gagne = floor(($honneur * $facteur_honneur) * $membre['share_xp'] / $groupe['share_xp']);
					$requete = 'UPDATE perso SET exp = exp + '.$xp_gagne.', honneur = honneur + '.$honneur_gagne.' WHERE ID = '.$membre['id_joueur'];
					$db->query($requete);
					$player = recupperso($membre['id_joueur']);
					//echo $player['nom'].' gagne '.$xp_gagne.' points d\'expériences et '.$honneur_gagne.' points d\'honneur<br />';
					//Vérification de l'avancement des quètes solo pour le tueur, groupe pour les autres
					if($membre['id_joueur'] == $defenseur['ID']) verif_action('J'.$row_diplo[0], $player, 's');
					else verif_action('J'.$row_diplo[0], $player, 'g');
				}
				$requete = 'UPDATE perso SET frag = frag + 1 WHERE ID = '.$defenseur['ID'];
				$db->query($requete);
				$requete = 'UPDATE perso SET mort = mort + 1 WHERE ID = '.$attaquant['ID'];
				$db->query($requete);
			}
			//Le défenseur est mort !
			if ($defenseur['hp'] <= 0)
			{
				//Gain d'expérience
				$xp = $defenseur['level'] * 100 * $G_xp_rate;
				
				//Niveau du groupe
				if($attaquant['groupe'] > 0)
				{
					$groupe = recupgroupe($attaquant['groupe'], $attaquant['x'].'-'.$attaquant['y']);
				}
				else
				{
					$groupe = array();
					$groupe['level_groupe'] = $attaquant['level'];
					$groupe['somme_groupe'] = $attaquant['level'];
					$groupe['share_xp'] = 100;
					$groupe['membre'][0]['id_joueur'] = $attaquant['ID'];
					$groupe['membre'][0]['share_xp'] = 100;
					$groupe['membre'][0]['race'] = $attaquant['race'];
				}
				$G_range_level = ceil($defenseur['level'] * 0.5);
				$xp = $xp * (1 + (($defenseur['level'] - $attaquant['level']) / $G_range_level));
				if($xp < 0) $xp = 0;
				//Si il est en groupe réduction de l'xp gagné par rapport au niveau du groupe
				if($attaquant['groupe'] > 0)
				{
					$xp = $xp * $attaquant['level'] / $groupe['level_groupe'];
				}
				$honneur = floor($xp * 4);
				
				//Partage de l'xp au groupe
				foreach($groupe['membre'] as $membre)
				{
					//Facteur de diplomatie	
					$requete = "SELECT ".$defenseur['race']." FROM diplomatie WHERE race = '".$membre['race']."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					
					//Vérification crime
					if($membre['id_joueur'] == $attaquant['ID'] AND $crime)
					{
						$points = $G_crime[$row_diplo[0]];
						$requete = "UPDATE perso SET crime = crime + ".$points." WHERE ID = ".$attaquant['ID'];
						$db->query($requete);
						echo 'Vous tuez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime<br />';
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
									echo 'Vous avez tué un criminel ayant une prime sur sa tête, vous gagnez '.$star.' stars.<br />';
									$requete = "UPDATE amende SET prime = 0 WHERE id = ".$amende['id'];
									$db->query($requete);
									$requete = "DELETE FROM prime_criminel WHERE id_amende = ".$amende['id'];
									$db->query($requete);
								}
							break;
						}
					}
					$facteur_xp = $row_diplo[0] * 0.2;
					$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
					if ($facteur_honneur < 0) $facteur_honneur = 0;
					//XP Final
					$xp_gagne = floor(($xp * $facteur_xp) * $membre['share_xp'] / $groupe['share_xp']);
					$honneur_gagne = floor(($honneur * $facteur_honneur) * $membre['share_xp'] / $groupe['share_xp']);
					$requete = 'UPDATE perso SET star = star + '.$star.', exp = exp + '.$xp_gagne.', honneur = honneur + '.$honneur_gagne.' WHERE ID = '.$membre['id_joueur'];
					$db->query($requete);
					$player = recupperso($membre['id_joueur']);
					echo $player['nom'].' gagne <strong class="reward">'.$xp_gagne.' XP</strong> et <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong><br />';
					if($membre['id_joueur'] == $attaquant['ID']) verif_action('J'.$row_diplo[0], $player, 's');
					else verif_action('J'.$row_diplo[0], $player, 'g');
				}
				$requete = 'UPDATE perso SET frag = frag + 1 WHERE ID = '.$attaquant['ID'];
				$db->query($requete);
				$requete = 'UPDATE perso SET mort = mort + 1 WHERE ID = '.$defenseur['ID'];
				$db->query($requete);
			}
			else
			{
				echo('<img src="image/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> <a href="javascript:envoiInfo(\'attaque.php?ID='.$W_ID.'&amp;poscase='.$W_case.'\', \'information\')">Attaquer la même cible</a><br />');
			}
			
			$attaquant['pa'] = $attaquant['pa'] - $G_PA_attaque_joueur + $reduction_pa;
			$requete = 'UPDATE perso SET survie = '.$attaquant['survie'].' ,pa = '.$attaquant['pa'].' WHERE ID = '.$_SESSION['ID'];
			$db->query($requete);
	
			//Insertion de l'attaque dans les journaux des 2 joueurs
			$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'attaque', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur['x'].", ".$defenseur['y'].")";
			$db->query($requete);
			$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'defense', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), ".($defense_hp_avant - $defense_hp_apres).", ".($attaque_hp_avant - $attaque_hp_apres).", ".$defenseur['x'].", ".$defenseur['y'].")";
			$db->query($requete);
			if($defenseur['hp'] <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'tue', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'mort', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
			}
			elseif($attaquant['hp'] <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'mort', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'tue', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), 0, 0, ".$defenseur['x'].", ".$defenseur['y'].")";
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
<a onclick="for (i=0; i<<?php echo $debugs; ?>; i++) {if(document.getElementById('debug' + i).style.display == 'inline') document.getElementById('debug' + i).style.display = 'none'; else document.getElementById('debug' + i).style.display = 'inline';}">Debug</a><br />
<a href="javascript:envoiInfo('informationcase.php?case=<?php echo $W_case; ?>', 'information');">Retour aux informations de la case</a>
</div>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />