<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
//L'ID du du joueur attaqué
$W_ID = $_GET['ID'];

$attaquant = new perso($_SESSION['ID']);
if(array_key_exists('type', $_GET) AND $_GET['type'] == 'batiment')
{
	$ennemi = 'batiment';
	$defenseur = recupbatiment($W_ID, $_GET['table']);
	$defenseur['type2'] = 'batiment';
	$pa_attaque = $G_PA_attaque_batiment;
}
else
{
	$ennemi = 'monstre';
	$defenseur = new monstre($W_ID);
	$defenseur->type2 = 'monstre';
	$pa_attaque = $G_PA_attaque_monstre;
}

$action_a = recupaction($attaquant->get_action_a());

//Case du monstre
$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);
$W_distance = detection_distance($W_case, $attaquant->get_pos());

?>
<fieldset>
	<legend>Combat VS <?php echo $defenseur->get_nom(); ?></legend>
<?php
if($W_distance > $attaquant->get_distance_tir())
{
	echo '<h5>Vous êtes trop loin pour l\'attaquer !</h5>';
}
else
{
	$round = 1;
	$debugs = 0;
	$attaquant->etat = array();
	$defenseur->etat = array();
	
	if($attaquant->is_debuff('cout_attaque')) $pa_attaque = ceil($pa_attaque / $attaquant->get_debuff('cout_attaque', 'effet'));
	if($attaquant->is_debuff('plus_cout_attaque')) $pa_attaque = $pa_attaque * $attaquant->get_debuff('plus_cout_attaque', 'effet');
	if($attaquant->is_buff('buff_rapidite')) $reduction_pa = $attaquant->get_buff('buff_rapidite', 'effet'); else $reduction_pa = 0;
	if($attaquant->is_debuff('debuff_ralentissement')) $reduction_pa -= $attaquant->get_debuff('debuff_ralentissement', 'effet');
	if($attaquant->is_debuff('engloutissement')) $attaquant['dexterite'] -= $attaquant->get_debuff('engloutissement', 'effet');
	if($defenseur->is_debuff('engloutissement')) $defenseur['dexterite'] -= $defenseur->get_debuff('engloutissement', 'effet');
	if($attaquant->is_debuff('deluge')) $attaquant['volonte'] -= $attaquant->get_debuff('deluge', 'effet');
	if($defenseur->is_debuff('deluge')) $defenseur['volonte'] -= $defenseur->get_debuff('deluge', 'effet');

	if(is_donjon($attaquant->get_x(), $attaquant->get_y()))
	{
		$round_total = 20;
		$attaquant->reserve = $attaquant->reserve * 2;
		$defenseur->reserve = $defenseur->reserve * 2;
		//Un monstre attaque pas de pa pour attaquer
		if(array_key_exists('attaque_donjon', $_SESSION) AND $_SESSION['attaque_donjon'] == 'ok')
		{
			$pa_attaque = $reduction_pa;
			unset($_SESSION['attaque_donjon']);
		}
	}
	else $round_total = $G_round_total;
	if($attaquant->get_race() == 'orc') $round_total += 1;
	if($attaquant->is_buff('buff_sacrifice')) $round_total -= $attaquant->get_buff('buff_sacrifice', 'effet2');
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	$pa_attaque = $pa_attaque - $reduction_pa;
	
	if ($attaquant->get_pa() >= $pa_attaque)
	{
		if($attaquant->get_hp() > 0)
		{
			//Suppresion de longue portée si besoin
			if($attaquant->is_buff('longue_portee') AND $attaquant['arme_type'] == 'arc')
			{
				$requete = "DELETE FROM buff WHERE id = ".$attaquant->get_buff('longue_portee', 'id');
				$db->query($requete);
			}
			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant->get_hp() > 0) AND ($defenseur->get_hp() > 0))
			{
				if($attaquant->get_arme_type() == 'arc') $attaquant->comp = 'distance'; else $attaquant->comp = 'melee';
				$defenseur->comp = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant->get_potentiel_toucher();
				$defenseur->get_potentiel_toucher();
				$attaquant->get_potentiel_parer();
				$defenseur->get_potentiel_parer;
				$attaquant->degat_sup = 0;
				$attaquant->degat_moins = 0;
				$defenseur->degat_sup = 0;
				$defenseur->degat_moins = 0;
				
				if ($mode == 'attaquant') $mode = 'defenseur';
				else ($mode = 'attaquant');

        $effects = effect::general_factory($attaquant, $defenseur, $mode);
		$defenseur_hp_avant = $defenseur->get_hp();
				
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
						//else echo $defenseur['nom'].' est '.$key.' pour '.$defenseur['etat'][$key]['duree'].' rounds<br />';
					}
				}
				else
				{
					foreach($attaquant->etat as $key => $value)
					{
						$attaquant->etat[$key]['duree'] -= 1;
						if($attaquant->etat[$key]['duree'] <= 0) unset($attaquant->etat[$key]);
						//else echo $attaquant['nom'].' est '.$key.' pour '.$attaquant['etat'][$key]['duree'].' rounds<br />';
					}
				}
					?>
					<div class="combat">
					<?php

					//Résolution des actions
					if($mode == 'attaquant') $mode_def = 'defenseur'; else $mode_def = 'attaquant';
					if(($mode == 'defenseur') && ($W_distance >= $round) && $ennemi == 'monstre')
					{
						echo $defenseur['nom'].' s\'approche<br />';
						$action = '';
					}
					else
					{
						if($ennemi == 'monstre' OR $mode == 'attaquant') {
              $action = script_action(${$mode}, ${$mode_def}, $mode, $effects);
            }
						else $action = '';
						if(is_array($action[2])) ${$mode} = $action[2];
					}

					$args = array();
					$args_def = array();
					$args_comp = array();
					//echo $action[0];
					
					switch($action[0])
					{
						//Attaque
						case 'attaque' :
							attaque($mode, ${$mode}['comp'], $effects);
							$args[] = $attaquant['comp'].' = '.$attaquant[$attaquant['comp']];
							$args[] = 'esquive = '.$attaquant['esquive'];
							$count = count($ups);
							if($count > 0)
							{
								$upi = 0;
								while($upi < $count)
								{
									$requete = "UPDATE comp_perso SET valeur = ".$attaquant['competences'][$ups[$upi]]." WHERE id_perso = ".$attaquant['ID']." AND competence = '".$ups[$upi]."'";
									$db->query($requete);
									$upi++;
								}
							}
						break;
						//Lancement d'un sort
						case 'lance_sort' :
							$comp = lance_sort($action[1], $mode, $effects);
							$args[] = 'incantation = '.$attaquant['incantation'];
							$args[] = $comp.' = '.$attaquant[$comp];
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							$comp = lance_comp($action[1], $mode, $effects);
							if($comp_attaque)
							{
								attaque($mode, ${$mode}['comp'], $effects);
								$args[] = $attaquant['comp'].' = '.$attaquant[$attaquant['comp']];
								$args[] = 'esquive = '.$attaquant['esquive'];
								$count = count($ups);
								if($count > 0)
								{
									$upi = 0;
									while($upi < $count)
									{
										$requete = "UPDATE comp_perso SET valeur = ".$attaquant['competences'][$ups[$upi]]." WHERE id_perso = ".$attaquant['ID']." AND competence = '".$ups[$upi]."'";
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
						if($attaquant['etat']['poison']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['poison']['effet'] - $attaquant['etat']['poison']['duree'] + 1;
							if($attaquant['etat']['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $attaquant['etat']['putrefaction']['effet'];
							$attaquant->set_hp($attaquant->set_hp() - $perte_hp);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						if($defenseur['etat']['poison']['duree'] > 0)
						{
							$perte_hp = $defenseur['etat']['poison']['effet'] - $defenseur['etat']['poison']['duree'] + 1;
							if($defenseur['etat']['putrefaction']['duree'] > 0) $perte_hp = $perte_hp * $defenseur['etat']['putrefaction']['effet'];
							$defenseur['hp'] -= $perte_hp;
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$perte_hp.' HP par le poison</span><br />';
						}
						//Perte de HP par hémorragie
						if($attaquant['etat']['hemorragie']['duree'] > 0)
						{
							$perte_hp = $attaquant['etat']['hemorragie']['effet'];
							$attaquant->set_hp($attaquant->set_hp() - $perte_hp);
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
							$attaquant->set_hp($attaquant->set_hp() - $perte_hp);
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
							$attaquant->set_hp($attaquant->set_hp() - $perte_hp);
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
							$attaquant->set_hp($attaquant->set_hp() - $attaquant['etat']['lien_sylvestre']['effet']);
							echo '&nbsp;&nbsp;<span class="degat">'.$attaquant['nom'].' perd '.$attaquant['etat']['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						if($defenseur['etat']['lien_sylvestre']['duree'] > 0)
						{
							$defenseur['hp'] -= $defenseur['etat']['lien_sylvestre']['effet'];
							echo '&nbsp;&nbsp;<span class="degat">'.$defenseur['nom'].' perd '.$defenseur['etat']['lien_sylvestre']['effet'].' HP par le lien sylvestre</span><br />';
						}
						//Gain de HP par récupération
						if($attaquant['etat']['recuperation']['duree'] > 0)
						{
							$effet = $attaquant['etat']['recuperation']['effet'];
							if(($attaquant->get_hp() + $effet) > $attaquant['etat']['recuperation']['hp_max'])
							{
								$effet = $attaquant['etat']['recuperation']['hp_max'] - $attaquant->get_hp();
							}
							$attaquant->set_hp($attaquant->get_hp() + $effet);
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
					$args[] = 'hp = '.$attaquant->get_hp();
					if($attaquant['bouclier']) $args[] = 'blocage = '.$attaquant['blocage'];
					$args_def[] = 'hp = '.$defenseur->get_hp();
					
					//Update de la base de donnée.
					//Correction des bonus ignorables
					corrige_bonus_ignorables($attaquant, $defenseur, $mode, $args, $args_def);
					//Attaquant
					$requete = 'UPDATE perso SET '.implode(',', $args).' WHERE ID = '.$_SESSION['ID'];
					//echo $requete;
					$req = $db->query($requete);
					//Defenseur
					if($ennemi == 'monstre')
					{
						$requete = 'UPDATE map_monstre SET '.implode(',', $args_def).' WHERE ID = '.$W_ID;
					}
					elseif($ennemi == 'batiment')
					{
						$requete = 'UPDATE '.sSQL($_GET['table']).' SET hp = '.$defenseur->get_hp().' WHERE id = '.$W_ID;
					}
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
			$survie = $attaquant->get_survie();
			if($defenseur->get_type() == 'bete' AND $attaquant->is_competence('survie_bete')) $survie += $attaquant->get_competence('survie_bete', 'valeur');
			if($defenseur->get_type() == 'humanoide' AND $attaquant->is_competence('survie_humanoide')) $survie += $attaquant->get_competence('survie_humanoide', 'valeur');
			if($defenseur->get_type() == 'magique' AND $attaquant->is_competence('survie_magique')) $survie += $attaquant->get_competence('survie_magique', 'valeur');
			if($defenseur->get_level() > 0) $level = $defenseur->get_level(); else $level = 1;
			$nbr_barre_total = ceil($survie / $level);
			if($nbr_barre_total < 1) $nbr_barre_total = 1;
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur->get_hp() / $defenseur->get_hp_max()) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total), 2);
			echo '
			<hr />';

			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $attaquant, 2);
			//		var_dump($augmentation);
			if($augmentation[1] == 1)
			{
				$attaquant->set_survie($augmentation[0]);
				echo '<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie'].' en '.$Gtrad['survie'].'</span>';
			}

			if($defenseur->get_type() == 'bete' AND $attaquant->is_competence('survie_bete'))
			{
				//Augmentation des compétences liées
				$attaquant->survie_bete = $attaquant->get_competence('survie_bete', 'valeur');
				$augmentation = augmentation_competence('survie_bete', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant->competences['survie_bete']->set_valeur($augmentation[0]);
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$augmentation[0].' en '.$Gtrad['survie_bete'].'</span><br />';
					$attaquant->competences['survie_bete']->sauver();
				}
			}
			if($defenseur->get_type() == 'magique' AND $attaquant->is_competence('survie_magique'))
			{
				//Augmentation des compétences liées
				$attaquant->survie_magique = $attaquant->get_competence('survie_magique', 'valeur');
				$augmentation = augmentation_competence('survie_magique', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant->competences['survie_magique']->set_valeur($augmentation[0]);
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$augmentation[0].' en '.$Gtrad['survie_magique'].'</span><br />';
					$attaquant->competences['survie_magique']->sauver();
				}
			}
			if($defenseur->get_type() == 'humanoide' AND  $attaquant->is_competence('survie_humanoide'))
			{
				//Augmentation des compétences liées
				$attaquant->survie_humanoide = $attaquant->get_competence('survie_humanoide', 'valeur');
				$augmentation = augmentation_competence('survie_humanoide', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant->competences['survie_humanoide']->set_valeur($augmentation[0]);
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$augmentation[0].' en '.$Gtrad['survie_humanoide'].'</span><br />';
					$attaquant->competences['survie_humanoide']->sauver();
				}
			}
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
			//L'attaquant est mort !
			if ($attaquant->get_hp() <= 0)
			{
				$attaquant->set_mort($attaquant->get_mort() + 1);
			}
			$gains_xp = false;
			$gains_drop = false;
			$gains_star = false;
			$defenseur_hp_apres = $defenseur->get_hp();
			//Le défenseur est mort !
			if ($defenseur['hp'] <= 0)
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
				echo(' <a href="attaque_monstre.php?ID='.$W_ID.'&amp;type='.$ennemi.'&amp;table='.sSQL($_GET['table']).'&amp;poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/attaquer.png" alt="Combattre" title="Attaquer la même cible" style="vertical-align : middle;" /></a><br />');
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
					
			$groupe = new groupe($attaquant->get_groupe());
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
			$attaquant->set_pa($attaquant->get_pa() - $pa_attaque);
			sauve_sans_bonus_ignorables($attaquant, array('survie', 'melee', 'distance', 'esquive', 'hp', 'pa'));
			//$requete = 'UPDATE perso SET survie = '.$attaquant['survie'].' ,melee = '.$attaquant['melee'].', esquive = '.$attaquant['esquive'].', hp = '.$attaquant->get_hp().', pa = '.$attaquant->get_pa().' - '.$pa_attaque.' WHERE ID = '.$_SESSION['ID'];
			//$db->query($requete);
		}
		else
		{
			echo 'Vous êtes mort !<img src="image/pixel.gif" onload="window.location.reload();" />';
		}
		$R->sauver();
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
<?php

echo $out1;
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</fieldset>
