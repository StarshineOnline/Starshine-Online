<?php
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include('haut_ajax.php');
//L'ID du du joueur attaqué
$W_ID = $_GET['ID'];

$attaquant = recupperso($_SESSION['ID']);
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
	$defenseur = recupmonstre($W_ID);
	$defenseur['type2'] = 'monstre';
	$pa_attaque = $G_PA_attaque_monstre;
}

$attaquant['action_a'] = recupaction($attaquant['action_a']);
$defenseur['action_d'] = $defenseur['action_d'];

//Case du monstre
$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);
$W_distance = detection_distance($W_case,$_SESSION["position"]);

?>
<h2>COMBAT</h2>
<?php
if($W_distance > $attaquant['arme_distance'])
{
	echo '<h5>Vous êtes trop loin pour l\'attaquer !</h5>';
}
else
{
	$round = 1;
	$debugs = 0;
	$attaquant['etat'] = array();
	$defenseur['etat'] = array();
	
	if($attaquant['race'] == 'orc' OR $defenseur['race'] == 'orc') $round_total += 1;
	if(array_key_exists('buff_sacrifice', $attaquant['buff'])) $round_total -= $attaquant['buff']['buff_sacrifice']['effet2'];
	if(array_key_exists('buff_rapidite', $attaquant['buff'])) $reduction_pa = $attaquant['buff']['buff_rapidite']['effet']; else $reduction_pa = 0;
	if(array_key_exists('debuff_ralentissement', $attaquant['debuff'])) $reduction_pa -= $attaquant['debuff']['debuff_ralentissement']['effet'];
	if(array_key_exists('engloutissement', $attaquant['debuff'])) $attaquant['dexterite'] -= $attaquant['debuff']['engloutissement']['effet'];
	if(array_key_exists('engloutissement', $defenseur['debuff'])) $defenseur['dexterite'] -= $defenseur['debuff']['engloutissement']['effet'];
	if(array_key_exists('deluge', $attaquant['debuff'])) $attaquant['volonte'] -= $attaquant['debuff']['deluge']['effet'];
	if(array_key_exists('deluge', $defenseur['debuff'])) $defenseur['volonte'] -= $defenseur['debuff']['deluge']['effet'];

	if(is_donjon($attaquant['x'], $attaquant['y']))
	{
		$round_total = 20;
		$attaquant['reserve'] = $attaquant['reserve'] * 2;
		$defenseur['reserve'] = $defenseur['reserve'] * 2;
		//Un monstre attaque pas de pa pour attaquer
		if(array_key_exists('attaque_donjon', $_SESSION) AND $_SESSION['attaque_donjon'] == 'ok') $pa_attaque = $reduction_pa;
	}
	else $round_total = $G_round_total;
	//Vérifie si l'attaquant a assez de points d'actions pour attaquer
	if ($attaquant['pa'] >= ($pa_attaque - $reduction_pa))
	{
		if($attaquant['hp'] > 0)
		{
			//Suppresion de longue portée si besoin
			if(array_key_exists('longue_portee', $attaquant['buff']) AND $attaquant['arme_type'] == 'arc')
			{
				$requete = "DELETE FROM buff WHERE id = ".$attaquant['buff']['longue_portee']['id'];
				$db->query($requete);
			}
			//Boucle principale qui fait durer le combat $round_total round
			while(($round < ($round_total + 1)) AND ($attaquant['hp'] > 0) AND ($defenseur['hp'] > 0))
			{
				if($attaquant['arme_type'] == 'arc') $attaquant['comp'] = 'distance'; else $attaquant['comp'] = 'melee';
				$defenseur['comp'] = 'melee';
				//Calcul du potentiel de toucher et parer
				$attaquant['potentiel_toucher'] = round($attaquant[$attaquant['comp']] + ($attaquant[$attaquant['comp']] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				$defenseur['potentiel_toucher'] = round($defenseur[$defenseur['comp']] + ($defenseur[$defenseur['comp']] * ((pow($defenseur['dexterite'], 2)) / 1000)));
				$attaquant['potentiel_parer'] = round($attaquant['esquive'] + ($attaquant['esquive'] * ((pow($attaquant['dexterite'], 2)) / 1000)));
				$defenseur['potentiel_parer'] = round($defenseur['esquive'] + ($defenseur['esquive'] * ((pow($defenseur['dexterite'], 2)) / 1000)));
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
					?>
					<div id="debug<?php echo $debugs; ?>" class="debug">
					<?php
					while($etati < count($defenseur['etat']))
					{
						$defenseur['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($defenseur['etat'][$etatkeys[$etati]]['duree'] <= 0)
						{
							array_splice($defenseur['etat'], $etati, 1);
							$etatkeys = array_keys($defenseur['etat']);
						}
						else
						{
							echo $defenseur['nom'].' est '.$etatkeys[$etati].' pour '.$defenseur['etat'][$etatkeys[$etati]]['duree'].'<br />';
							$etati++;
						}
					}
					$debugs++;
					?>
					</div>
					<?php
				}
				else
				{

					$etati = 0;
					$etatkeys = array_keys($attaquant['etat']);
					?>
					<div id="debug<?php echo $debugs; ?>" class="debug">
					<?php
					while($etati < count($attaquant['etat']))
					{
						$attaquant['etat'][$etatkeys[$etati]]['duree'] -= 1;
						if($attaquant['etat'][$etatkeys[$etati]]['duree'] <= 0)
						{
							array_splice($attaquant['etat'], $etati, 1);
							$etatkeys = array_keys($attaquant['etat']);
						}
						else
						{
							echo $attaquant['nom'].' est '.$etatkeys[$etati].' pour '.$attaquant['etat'][$etatkeys[$etati]]['duree'].'<br />';
							$etati++;
						}
					}
					$debugs++;
					?>
					</div>
					<?php
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
						if($ennemi == 'monstre' OR $mode == 'attaquant') $action = script_action(${$mode}, ${$mode_def}, $mode);
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
							attaque($mode, ${$mode}['comp']);
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
							$comp = lance_sort($action[1], $mode);
							$args[] = 'incantation = '.$attaquant['incantation'];
							$args[] = $comp.' = '.$attaquant[$comp];
						break;
						//Lancement d'une compétence
						case 'lance_comp' :
							$comp = lance_comp($action[1], $mode);
							if($comp_attaque)
							{
								attaque($mode, ${$mode}['comp']);
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
						//Gain de HP par récupération
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
					$args[] = 'hp = '.$attaquant['hp'];
					if($attaquant['bouclier']) $args[] = 'blocage = '.$attaquant['blocage'];
					$args_def[] = 'hp = '.$defenseur['hp'];
		
					//Update de la base de donnée.
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
						$requete = 'UPDATE '.sSQL($_GET['table']).' SET hp = '.$defenseur['hp'].' WHERE id = '.$W_ID;
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
			$survie = $attaquant['survie'];
			if($defenseur['espece'] == 'bete' AND array_key_exists('survie_bete', $attaquant['competences'])) $survie += $attaquant['competences']['survie_bete'];
			if($defenseur['espece'] == 'humanoide' AND array_key_exists('survie_humanoide', $attaquant['competences'])) $survie += $attaquant['competences']['survie_humanoide'];
			if($defenseur['espece'] == 'magique' AND array_key_exists('survie_magique', $attaquant['competences'])) $survie += $attaquant['competences']['survie_magique'];
			if($defenseur['level'] > 0) $level = $defenseur['level']; else $level = 1;
			$nbr_barre_total = ceil($survie / $level);
			if($nbr_barre_total < 1) $nbr_barre_total = 1;
			if($nbr_barre_total > 100) $nbr_barre_total = 100;
			$nbr_barre = round(($defenseur['hp'] / $defenseur['hp_max_1']) * $nbr_barre_total);
			$longueur = round(100 * ($nbr_barre / $nbr_barre_total), 2);
			if($longueur < 0) $longueur = 0;
			$fiabilite = round((100 / $nbr_barre_total), 2);
			echo '
			<table class="information_case" style="float:left;width:40%;">
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
			<div class="information_case" style="float : right;width:45%;">';
			//Augmentation des compétences liées
			$augmentation = augmentation_competence('survie', $attaquant, 2);
			if($augmentation[1] == 1)
			{
				$attaquant['survie'] = $augmentation[0];
				echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie'].' en '.$Gtrad['survie'].'</span><br />';
			}
			if($defenseur['espece'] == 'bete' AND array_key_exists('survie_bete', $attaquant['competences']))
			{
				//Augmentation des compétences liées
				$attaquant['survie_bete'] = $attaquant['competences']['survie_bete'];
				$augmentation = augmentation_competence('survie_bete', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant['survie_bete'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie_bete'].' en '.$Gtrad['survie_bete'].'</span><br />';
					$db->query("UPDATE comp_perso SET valeur = ".$augmentation[0]." WHERE id_perso = ".$attaquant['ID']." AND competence = 'survie_bete'");
				}
			}
			if($defenseur['espece'] == 'magique' AND array_key_exists('survie_magique', $attaquant['competences']))
			{
				//Augmentation des compétences liées
				$attaquant['survie_magique'] = $attaquant['competences']['survie_magique'];
				$augmentation = augmentation_competence('survie_magique', $attaquant, 4);
				if($augmentation[1] == 1)
				{
					$attaquant['survie_magique'] = $augmentation[0];
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$attaquant['survie_magique'].' en '.$Gtrad['survie_magique'].'</span><br />';
					$db->query("UPDATE comp_perso SET valeur = ".$augmentation[0]." WHERE id_perso = ".$attaquant['ID']." AND competence = 'survie_magique'");
				}
			}
			if($defenseur['espece'] == 'humanoide' AND array_key_exists('survie_humanoide', $attaquant['competences']))
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
			
			//L'attaquant est mort !
			if ($attaquant['hp'] <= 0)
			{
				$coeff = 1;
				//Si c'était un monstre
				if($ennemi == 'monstre')
				{
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
				}
				$requete = 'UPDATE perso SET mort = mort + 1, honneur = honneur * '.$coeff.' WHERE ID = '.$attaquant['ID'];
				$db->query($requete);
			}
			//Le défenseur est mort !
			if ($defenseur['hp'] <= 0)
			{
				if($ennemi == 'monstre')
				{
					//Gain d'expérience
					$requete = "SELECT xp, star, drops FROM monstre WHERE id = '".$defenseur['type']."'";
					$req = $db->query($requete);
					$row = $db->read_row($req);
					$drop = $row[2];
					
					$xp = $row[0] * $G_xp_rate;
					$starmax = $row[1];
					$starmin = floor($row[1] / 2);
					$star = rand($starmin, $starmax) * $G_drop_rate;
					if($attaquant['race'] == 'nain') $star = floor($star * 1.1);
					if(in_array('recherche_precieux', $attaquant['buff'])) $star = $star * (1 + ($attaquant['buff']['recherche_precieux']['effet'] / 100));
					$star = ceil($star);
					$taxe = floor($star * $R['taxe'] / 100);
					$star = $star - $taxe;
					//Récupération de la taxe
					if($taxe > 0)
					{
						$requete = 'UPDATE royaume SET star = star + '.$taxe.' WHERE ID = '.$R['ID'];
						$db->query($requete);
						$requete = "UPDATE argent_royaume SET monstre = monstre + ".$taxe." WHERE race = '".$R['race']."'";
						$db->query($requete);
					}
					
					$groupe = recupgroupe($attaquant['groupe'], $attaquant['x'].'-'.$attaquant['y']);
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
							if($attaquant['race'] == 'humain') $taux = floor($taux / 1.3);
							if(in_array('fouille_gibier', $attaquant['buff'])) $taux = $taux * (1 + ($attaquant['buff']['fouille']['effet'] / 100));
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
								//Insertion du loot dans le journal du gagnat
								$requete = "INSERT INTO journal VALUES('', ".$gagnant['ID'].", 'loot', '', '', NOW(), '".mysql_escape_string($objet_nom)."', '', ".$attaquant['x'].", ".$attaquant['y'].")";
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

					//Niveau du groupe
					if($attaquant['groupe'] == 0)
					{
						$groupe = array();
						$groupe['level_groupe'] = $attaquant['level'];
						$groupe['somme_groupe'] = $attaquant['level'];
						$groupe['share_xp'] = 100;
						$groupe['membre'][0]['id_joueur'] = $attaquant['ID'];
						$groupe['membre'][0]['share_xp'] = 100;
					}

					//Partage de l'xp au groupe
					$xp = $xp * (1 + (($defenseur['level'] - $groupe['level_groupe']) / $G_range_level));
					if ($xp < 0) $xp = 0;
					
					foreach($groupe['membre'] as $membre)
					{
						//XP Final
						$xp_joueur = floor($xp * $membre['share_xp'] / $groupe['share_xp']);
						$star_joueur = floor($star * $membre['share_xp'] / $groupe['share_xp']);
						$requete = 'UPDATE perso SET exp = exp + '.$xp_joueur.', star = star + '.$star_joueur.' WHERE ID = '.$membre['id_joueur'];
						$db->query($requete);
						$player = recupperso($membre['id_joueur']);
						echo $player['nom'].' gagne <strong class="reward">'.$xp_joueur.' XP</strong> et <strong class="reward">'.$star_joueur.' Stars</strong><br />';
						//Vérification de l'avancement des quètes solo pour le tueur, groupe pour les autres
						if($membre['id_joueur'] == $attaquant['ID']) verif_action('M'.$defenseur['type'], $player, 's');
						else verif_action('M'.$defenseur['type'], $player, 'g');
					}
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
					if($defenseur['type'] == 75)
					{
						$requete = "INSERT INTO map_monstre VALUES(NULL, '116','24','209','10000', 8, '".addslashes('Adenaïos le nécromant')."','adennaios', ".(time() + 2678400).")"; 
						$db->query($requete);
						echo '<strong>Aaaargh VAINCU, JE SUIS VAINCU, comment est ce possible !!! Maître !! Maître venez à moi, vengez votre plus fidèle serviteur !!!</strong>';
					}
				}
				elseif($ennemi == 'batiment')
				{
                   	//On supprime un bourg au compteur
	            	if($defenseur['type'] == 'bourg')
       		    	{
        		    	$requete = "UPDATE royaume SET bourg = bourg - 1 WHERE ID = ".$defenseur['ID'];
        		    	$db->query($requete);
       		    	}
					//On efface le batiment
					$requete = "DELETE FROM ".sSQL($_GET['table'])." WHERE ID = '".$W_ID."'";
					$req = $db->query($requete);
				}
			}
			else
			{
				echo('<img src="image/attaquer.png" alt="Combattre" style="vertical-align : middle;" /> <a href="javascript:envoiInfo(\'attaque_monstre.php?ID='.$W_ID.'&amp;type='.$ennemi.'&amp;table='.sSQL($_GET['table']).'&amp;poscase='.$W_case.'\', \'information\')">Attaquer la même cible</a><br />');
			}
			
			$attaquant['pa'] = $attaquant['pa'] - $pa_attaque + $reduction_pa;
			$requete = 'UPDATE perso SET survie = '.$attaquant['survie'].' ,melee = '.$attaquant['melee'].', esquive = '.$attaquant['esquive'].', hp = '.$attaquant['hp'].', pa = '.$attaquant['pa'].' WHERE ID = '.$_SESSION['ID'];
			$db->query($requete);
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
<a onclick="for (i=0; i<<?php echo $debugs; ?>; i++) {if(document.getElementById('debug' + i).style.display == 'inline') document.getElementById('debug' + i).style.display = 'none'; else document.getElementById('debug' + i).style.display = 'inline';}">Debug</a> <br />
<a href="javascript:envoiInfo('informationcase.php?case=<?php echo $W_case; ?>', 'information')">Retour aux informations de la case</a>
</div>
<?php
echo $out1;
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />