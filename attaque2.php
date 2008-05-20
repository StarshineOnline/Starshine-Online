<?php
include('inc/fp.php');
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
$degats_total = 0;
$degats_total_d = 0;
if($W_distance > $attaquant['arme_distance'])
{
	echo 'Vous êtes trop loin pour l\'attaquer !';
}
elseif($attaquant['hp'] <= 0 OR $defenseur['hp'] <= 0)
{
	echo 'Un des protagonistes n\'a plus de points de vie';
}
else
{
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
	$round = 1;
	$attaquant['etat'] = array();
	$defenseur['etat'] = array();
	$debugs = 0;
	if($attaquant['race'] == 'orc' OR $defenseur['race'] == 'orc') $G_round_total += 1;
	if(array_key_exists('buff_rapidite', $attaquant['buff'])) $reduction_pa = $attaquant['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
	if(array_key_exists('debuff_ralentissement', $attaquant['debuff'])) $reduction_pa -= $attaquant['debuff']['debuff_ralentissement']['effet'];
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	if ($attaquant['pa'] >= ($G_PA_attaque_joueur - $reduction_pa))
	{
		if($attaquant['hp'] > 0)
		{
			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($G_round_total + 1)) AND ($attaquant['hp'] > 0) AND ($defenseur['hp'] > 0))
			{
				if($attaquant['arme_type'] == 'arc') $attaquant['comp'] = 'distance'; else $attaquant['comp'] = 'melee';
				if($defenseur['arme_type'] == 'arc') $defenseur['comp'] = 'distance'; else $defenseur['comp'] = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant['potentiel_toucher'] = round($attaquant[$attaquant['comp']] + ($attaquant[$attaquant['comp']] * ($attaquant['dexterite'] / 100)));
				$defenseur['potentiel_toucher'] = round($defenseur[$defenseur['comp']] + ($defenseur[$defenseur['comp']] * ($defenseur['dexterite'] / 100)));
				$attaquant['potentiel_parer'] = round($attaquant['esquive'] + ($attaquant['esquive'] * ($attaquant['dexterite'] / 100)));
				$defenseur['potentiel_parer'] = round($defenseur['esquive'] + ($defenseur['esquive'] * ($defenseur['dexterite'] / 100)));
				$actif['degat_sup'];
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');
				
				if($mode == 'attaquant')
				{
					echo '<strong>Round '.$round.'</strong><br />';
					$etati = 0;
					$etatkeys = array_keys($attaquant['etat']);
					while($etati < count($attaquant['etat']))
					{
						$attaquant['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($attaquant['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($attaquant['etat'], $etati, 1);
						$etati++;
					}
					$etati = 0;
					$etatkeys = array_keys($defenseur['etat']);
					while($etati < count($defenseur['etat']))
					{
						$defenseur['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($defenseur['etat'][$etatkeys[$etati]]['duree'] <= 0) array_splice($defenseur['etat'], $etati, 1);
						$etati++;
					}
				}

					if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
					$action = script_action(${$mode}, ${$mode_def}, $mode);
					if(($mode == 'defenseur') && ($W_distance >= $round))
					{
						if($defenseur['arme_distance'] < $attaquant['arme_distance'])
						{
							echo $defenseur['nom'].' s\'approche<br />';
							$action[0] = '';
						}
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
							$attaquant['hp'] -= $attaquant['etat']['poison']['effet'] * $attaquant['etat']['poison']['duree'];
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$attaquant['etat']['poison']['effet'].' HP par le poison</span><br />';
						}
						if($defenseur['etat']['poison']['duree'] > 0)
						{
							$defenseur['hp'] -= $defenseur['etat']['poison']['effet'] * $defenseur['etat']['poison']['duree'];
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$defenseur['etat']['poison']['effet'].' HP par le poison</span><br />';
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

					//echo $hp_avant.' '.${$mode_def}['hp'];
					if($mode_def == 'defenseur') $degats_total += ($hp_avant - ${$mode_def}['hp']);
					else $degats_total_d += ($hp_avant - ${$mode_def}['hp']);
					//Update de la base de donnée.
					//Attaquant
					$requete = 'UPDATE perso SET '.implode(',', $args).' WHERE ID = '.${$mode}['ID'];
					$req = $db->query($requete);
					//Defenseur
					$requete = 'UPDATE perso SET '.implode(',', $args_def).' WHERE ID = '.${$mode_def}['ID'];
					$req = $db->query($requete);
				if($mode == 'defenseur') $round++;
			}
			echo '<br /><table><tr style="text-align : center;"><td>'.$attaquant['nom'].'</td><td>'.$attaquant['hp'].' HP</td></tr><tr style="text-align : center;"><td>'.$defenseur['nom'].'</td><td>'.$defenseur['hp'].' HP</td></tr></table><br />';
			
			//L'attaquant est mort !
			if ($attaquant['hp'] <= 0)
			{
				//Gain d'expérience
				$xp = $attaquant['level'] * 100 * $G_xp_rate;
				
				//Niveau du groupe
				if($defenseur['groupe'] > 0)
				{
					$groupe = recupgroupe($defenseur['groupe']);
					$lvl_groupe = level_groupe($groupe);
					$level_groupe = $lvl_groupe[0];
					$somme_groupe = $lvl_groupe[1];
				}
				else
				{
					$level_groupe = $defenseur['level'];
					$somme_groupe = $defenseur['level'];
				}
				$xp = $xp * (1 + (($attaquant['level'] - $level_groupe) / $G_range_level));
				if ($xp < 0) $xp = 0;
				$honneur = floor($xp * 5);
				
				//Partage de l'xp au groupe
				if(($defenseur['groupe'] != '') AND ($defenseur['groupe'] != 0))
				{
					$W_requete_perso = 'SELECT * FROM perso WHERE groupe = '.$defenseur['groupe'];
					$W_req_perso = $db->query($W_requete_perso);
				}
				else
				{
					$W_requete_perso = 'SELECT * FROM perso WHERE ID = '.$defenseur['ID'];
					$W_req_perso = $db->query($W_requete_perso);
				}
				while($row = $db->read_array($W_req_perso))
				{
					//Facteur de diplomatie	
					$requete = "SELECT ".$attaquant['race']." FROM diplomatie WHERE race = '".$row['race']."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					
					if ($row_diplo[0] == 127) $row_diplo[0] = 0;
					$facteur_xp = $row_diplo[0] * 0.2;
					$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
					if ($facteur_honneur < 0) $facteur_honneur = 0;
					
					//XP Final	
					$xp_gagne = floor(($xp * $facteur_xp) * $row['level'] / $somme_groupe);
					$xp_joueur = $row['exp'] + $xp_gagne;
					$honneur_joueur = $row['honneur'] + floor(($honneur * $facteur_honneur) * $row['level'] / $somme_groupe);
					$requete = 'UPDATE perso SET exp = '.$xp_joueur.', honneur = '.$honneur_joueur.' WHERE ID = '.$row['ID'];
					$db->query($requete);
					echo $row['nom'].' gagne '.$xp_gagne.' points d\'expériences et '.($honneur_joueur - $row['honneur']).' points d\'honneur<br />';
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
					$groupe = recupgroupe($attaquant['groupe']);
					$lvl_groupe = level_groupe($groupe);
					$level_groupe = $lvl_groupe[0];
					$somme_groupe = $lvl_groupe[1];
				}
				else
				{
					$level_groupe = $attaquant['level'];
					$somme_groupe = $attaquant['level'];
				}
				$xp = $xp * (1 + (($defenseur['level'] - $level_groupe) / $G_range_level));
				if ($xp < 0) $xp = 0;
				$honneur = floor($xp * 5);
				
				//Partage de l'xp au groupe
				if(($attaquant['groupe'] != '') AND ($attaquant['groupe'] != 0))
				{
					$W_requete_perso = 'SELECT * FROM perso WHERE groupe = '.$attaquant['groupe'];
					$W_req_perso = $db->query($W_requete_perso);
				}
				else
				{
					$W_requete_perso = 'SELECT * FROM perso WHERE ID = '.$_SESSION['ID'];
					$W_req_perso = $db->query($W_requete_perso);
				}
				while($row = $db->read_array($W_req_perso))
				{
					//Facteur de diplomatie	
					$requete = "SELECT ".$defenseur['race']." FROM diplomatie WHERE race = '".$row['race']."'";
					$req_diplo = $db->query($requete);
					$row_diplo = $db->read_row($req_diplo);
					
					if ($row_diplo[0] == 127) $row_diplo[0] = 0;
					$facteur_xp = $row_diplo[0] * 0.2;
					$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
					if ($facteur_honneur < 0) $facteur_honneur = 0;
					
					//XP Final	
					$xp_gagne = floor(($xp * $facteur_xp) * $row['level'] / $somme_groupe);
					$xp_joueur = $row['exp'] + $xp_gagne;
					$honneur_gagne = floor(($honneur * $facteur_honneur) * $row['level'] / $somme_groupe);
					$honneur_joueur = $row['honneur'] + $honneur_gagne;
					$requete = 'UPDATE perso SET exp = '.$xp_joueur.', honneur = '.$honneur_joueur.' WHERE ID = '.$row['ID'];
					$db->query($requete);
					echo $row['nom'].' gagne <strong class="reward">'.$xp_gagne.' XP</strong> et <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong><br />';
				}
				$requete = 'UPDATE perso SET frag = frag + 1 WHERE ID = '.$attaquant['ID'];
				$db->query($requete);
				$requete = 'UPDATE perso SET mort = mort + 1 WHERE ID = '.$defenseur['ID'];
				$db->query($requete);
			}
			else
			{
				echo('<a href="javascript:envoiInfo(\'attaque.php?ID='.$W_ID.'&amp;poscase='.$W_case.'\', \'information\')">Attaquer la même cible</a><br />');
			}
			
			$attaquant['pa'] = $attaquant['pa'] - $G_PA_attaque_joueur + $reduction_pa;
			$requete = 'UPDATE perso SET pa = '.$attaquant['pa'].' WHERE ID = '.$_SESSION['ID'];
			$db->query($requete);
	
			//Insertion de l'attaque dans les journaux des 2 joueurs
			$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'attaque', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), ".$degats_total.", ".$degats_total_d.")";
			$db->query($requete);
			$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'defense', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), ".$degats_total.", ".$degats_total_d.")";
			$db->query($requete);
			if($defenseur['hp'] <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'tue', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), 0, 0)";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'mort', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), 0, 0)";
				$db->query($requete);
			}
			elseif($attaquant['hp'] <= 0)
			{
				$requete = "INSERT INTO journal VALUES('', ".$attaquant['ID'].", 'mort', '".$attaquant['nom']."', '".$defenseur['nom']."', NOW(), 0, 0)";
				$db->query($requete);
				$requete = "INSERT INTO journal VALUES('', ".$defenseur['ID'].", 'tue', '".$defenseur['nom']."', '".$attaquant['nom']."', NOW(), 0, 0)";
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
		echo 'Vous n\'avez pas assez de points d\'actions';
	}
}

?>
<a onclick="for (i=0; i<<?php echo $debugs; ?>; i++) {if(document.getElementById('debug' + i).style.display == 'inline') document.getElementById('debug' + i).style.display = 'none'; else document.getElementById('debug' + i).style.display = 'inline';}">Debug</a><br />
<a href="javascript:envoiInfo('informationcase.php?case=<?php echo $W_case; ?>', 'information');">Retour aux informations de la case</a><br />
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />