<?php //  -*- tab-width:2  -*-
function script_action($joueur, $ennemi, $mode)
{
	$effectue = sub_script_action($joueur, $ennemi, $mode);
	// On gère la dissimulation *après* le choix de l'action
	if ($ennemi['etat']['dissimulation'] > 0)
		{
			switch ($effectue[0])
				{
				case 'attaque':
					return '';
				case 'lance_comp':
					$table = 'comp_combat';
					break;
				case 'lance_sort':
					$table = 'sort_combat';
					break;
				default:
					return '';
				}
			// Chercher la cible de la capacité utilisée
			global $db, $G_cibles;
			$requete = "SELECT cible FROM $table WHERE id = ".$effectue[1];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			// Si la cible est l'adversaire, alors c'est foiré
			if ($G_cibles[$row['cible']] == 'Ennemi') {
				echo $ennemi['nom'].' est caché, '.$joueur['nom'].
					' ne peut pas attaquer<br />';
				return '';
			}
		}
	return $effectue;
}

function sub_script_action($joueur, $ennemi, $mode)
{
	global $db, $round, $Trace, $debugs;
	$stop = false;
	if($joueur['etat']['paralysie'] > 0)
	{
		echo $joueur['nom'].' est paralysé<br />';
		return '';
	}
	elseif($joueur['etat']['etourdit'] > 0)
	{
		echo $joueur['nom'].' est étourdi<br />';
		return '';
	}
	elseif($joueur['etat']['tir_vise'] > 0)
	{
		echo $joueur['nom'].' décoche une terrible flèche<br />';
		$effectue[0] = 'attaque';
		return $effectue;
	}
	/*
	elseif($ennemi['etat']['dissimulation'] > 0)
	{
		echo $ennemi['nom'].' est caché, '.$joueur['nom'].' ne peut pas attaquer<br />';
		return '';
	}
*/
	else
	{
	  //var_dump($joueur['etat']['glacer']);
		if($joueur['etat']['glacer'] > 0)
		{
			$rand = rand(1, 100);
			$cible = 20 + (($joueur['etat']['glacer']['effet'] - 1) * 10);
			//echo $cible.' chance de glacer / 100. Résultat : '.$rand.'<br />';
			if($rand < $cible)
			{
				echo $joueur['nom'].' est glacé<br />';
				return '';
				$stop = true;
			}
		}
		if($joueur['etat']['silence'] > 0)
		{
			echo $joueur['nom'].' est sous silence<br />';
			$effectue[0] = 'attaque';
			return $effectue;
		}
		if(!$stop)
		{
			if($mode == 'attaquant') $actions = explode(';', $joueur['action_a']);
			else $actions = explode(';', $joueur['action_d']);
			$count = count($actions);
			$action = false;
			$i = 0;
			while(($i < $count) && (!$action))
			{
				$decompose = explode('@', $actions[$i]);
				$conditions = '';
				if(count($decompose) > 1)
				{
					$conditions = $decompose[0];
					$solution = $decompose[1];
				}
				else
				{
					$solution = $decompose[0];
				}
				$type_action = substr($solution, 0, 1);
				if($conditions != '')
				{
					$conditions = explode('µ', $conditions);
					$valid = true;
					$count_c = count($conditions);
					$c = 0;
					while($c < $count_c && $valid)
					{
						$condition = $conditions[$c];
						$parametre = mb_substr($condition, 1, 2);
						$operateur = mb_substr($condition, 3, 1);
						$valeur = mb_substr($condition, 4);
						//Vérification des conditions
						//Recherche valeur du paramètre
						switch($parametre)
						{
							//Points de vie du joueur
							case '00' :
								$param = $joueur['hp'];
							break;
							//Réserve de mana du joueur
							case '01' :
								$param = $joueur['reserve'];
							break;
							//Round
							case '09' :
								$param = $round;
							break;
							//Etat du joueur
							case '10' :
								$param = $joueur['etat'];
							break;
							//Etat de l'adversaire
							case '11' :
								$param = $ennemi['etat'];
							break;
							//Etat du joueur
							case '12' :
								$param = $joueur['etat'];
							break;
							//Etat de l'adversaire
							case '13' :
								$param = $ennemi['etat'];
							break;
							//Nombre d'utilisations
							case '14' :
								if($type_action == '_')
								{
									$use = 'lance_comp';
								}
								elseif($type_action == '~')
								{
									$use = 'lance_sort';
								}
								else
								{
									$use = 'attaque';
								}
								$param = $joueur['anticipation'][$use][substr($solution, 1)];
							break;
							//Dernière action
							case '15' :
								if($type_action == 'E')
									$param = $joueur['precedente']['esquive'];
								else if($type_action == 'C')
									$param = $joueur['precedente']['critique'];
						}
						//echo $param.' '.$operateur.' '.$valeur.'<br />';
						//Vérification
						switch($operateur)
						{
							case '>' :
								if($param > $valeur) $valid = true; else $valid = false;
							break;
							case '<' :
								if($param < $valeur) $valid = true; else $valid = false;
							break;
							case '=' :
								if($param == $valeur) $valid = true; else $valid = false;
							break;
							//Vérification d'état
							case '°' :
								if(array_key_exists($valeur, $param))
								{
									$valid = false;
								}
								else
								{
									$valid = true;
								}
							break;
							//Vérification du nom état
							case '+' :
								if(!array_key_exists($valeur, $param))
								{
									$valid = false;
								}
								else
								{
									$valid = true;
								}
							break;
						}
						$c++;
					}
				}
				else
				{
					$valid = true;
				}
				if($valid)
				{
					//Vérification si action possible
					if($type_action == '~')
					{
						//Recherche du sort
						$id_sort = substr($solution, 1);
						$requete = "SELECT * FROM sort_combat WHERE id = ".$id_sort;
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$mp_need = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
						//Appel des ténebres
						if($joueur['etat']['appel_tenebre']['duree'] > 0)
						{
							$mp_need += $joueur['etat']['appel_tenebre']['effet'];
						}
						//Appel de la forêt
						if($joueur['etat']['appel_foret']['duree'] > 0 && $mp_need > 1)
						{
							$mp_need -= $joueur['etat']['appel_foret']['effet'];
							if($mp_need < 1) $mp_need = 1;
						}
						//Si le joueur a assez de reserve
						if($joueur['reserve'] >= $mp_need)
						{
							$effectue[0] = 'lance_sort';
							$effectue[1] = $id_sort;
							$action = true;
						}
					}
					elseif($type_action == '_')
					{
						//Recherche de la compétence
						$id_sort = substr($solution, 1);
						$requete = "SELECT * FROM comp_combat WHERE id = ".$id_sort;
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$mp_need = $row['mp'];
						//Appel des ténebres
						if($joueur['etat']['appel_tenebre']['duree'] > 0)
						{
							$mp_need += $joueur['etat']['appel_tenebre']['effet'];
						}
						//Appel de la forêt
						if($joueur['etat']['appel_foret']['duree'] > 0)
						{
							$mp_need -= $joueur['etat']['appel_foret']['effet'];
							if($mp_need < 1) $mp_need = 1;
						}
						//Si le joueur a assez de reserve
						if($joueur['reserve'] >= $mp_need)
						{
							//Si l'arme utilisée est la bonne
							$arme_requis = explode(';', $row['arme_requis']);
							if(in_array($joueur['arme_type'], $arme_requis) OR in_array($joueur['bouclier_type'], $arme_requis) OR $row['arme_requis'] == '')
							{
								$effectue[0] = 'lance_comp';
								$effectue[1] = $id_sort;
								$action = true;
							}
						}
					}
					if($solution == '!')
					{
						$effectue[0] = 'attaque';
						$action = true;
					}
				}
				$i++;
			}
			if(!$action) $effectue[0] = 'attaque';
			//Mis en place de chance que l'ennemi anticipe la compétence et que ca foire
			if($effectue[0] == 'attaque') $id = 0;
			else $id = $effectue[1];
			//Si il y a déjà eu une attaque de ce type, alors risque d'échec
			if(array_key_exists('anticipation', $joueur) AND array_key_exists($effectue[0], $joueur['anticipation']) AND array_key_exists($id, $joueur['anticipation'][$effectue[0]]) AND $joueur['anticipation'][$effectue[0]][$id] > 0)
			{
				//On récupère le nombre d'utilisations
				$nbr_utilisation = $joueur['anticipation'][$effectue[0]][$id];
				$chance_reussite = 100 - ($nbr_utilisation * $nbr_utilisation);
				//Si l'adversaire est de niveau < 5, alors il a moins de chances d'anticiper
				if($ennemi['level'] < 5)
				{
					$chance_reussite = 100 - ((100 - $chance_reussite) / (6 - $ennemi['level']));
				}
				//Réduction des chances d'anticiper si adversaire glacé
				if($ennemi['etat']['glace_anticipe']['duree'] > 0) $chance_reussite = $chance_reussite * $ennemi['etat']['glace_anticipe']['effet'];
				$rand = rand(0, 100);
				echo '
					<div id="debug'.$debugs.'" class="debug">
						Probabilité de réussir l\'anticipation : '.(100 - $chance_reussite).'%<br />
						'.$rand.' doit être supérieur à '.$chance_reussite.' pour anticipation<br />
					</div>';
				$debugs++;
				//Echec
				if($rand > $chance_reussite)
				{
					echo $ennemi['nom'].' anticipe l\'attaque, et elle échoue !<br />';
					return '';
				}
			}
			//On incrémente l'anticipation de la compétence de l'attaque classique ou du sort.
			$joueur['anticipation'][$effectue[0]][$id]++;
			$effectue[2] = $joueur;
			return $effectue;
		}
	}
}

function lance_sort($id, $acteur)
{
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $Trace, $G_buff, $G_debuff;
	if ($acteur == 'attaquant')
	{
		$actif = $attaquant;
		$passif = $defenseur;
	}
	else
	{
		$actif = $defenseur;
		$passif = $attaquant;
	}
  /* Instanciation de toutes les compétences ou effets */
  $effects = array();

	empoisonne::factory($effects, $actif, $passif, $acteur);

  /* Tri des effets selon leur ordre */
  sort_effects($effects);

	//Recherche du sort
	$requete = "SELECT * FROM sort_combat WHERE id = ".$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

	$mp_need = round($row['mp'] * (1 - (($Trace[$actif['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
	//Appel des ténebres
	if($actif['etat']['appel_tenebre']['duree'] > 0)
	{
		$mp_need += $actif['etat']['appel_tenebre']['effet'];
	}
	//Appel de la forêt
	if($actif['etat']['appel_foret']['duree'] > 0)
	{
		$mp_need_avant = $mp_need;
		$mp_need -= $actif['etat']['appel_foret']['effet'];
		if($mp_need < 1) $mp_need = $mp_need_avant;
	}
	//Suppresion de la réserve
	$actif['reserve'] -= $mp_need;

	$potentiel_magique = floor($actif['incantation'] + 1.9 * $actif[$row['comp_assoc']]);
	if(array_key_exists('batiment_incantation', $passif['buff'])) $potentiel_magique *= 1 + (($passif['buff']['batiment_incantation']['effet']) / 100);
	if(array_key_exists('buff_meditation', $actif['buff'])) $potentiel_magique *= 1 + (($actif['buff']['buff_meditation']['effet']) / 100);
	if(array_key_exists('lien_sylvestre', $actif['etat'])) $potentiel_magique /= 1 + (($actif['etat']['lien_sylvestre']['effet2']) / 100);
	if(array_key_exists('fleche_sable', $actif['etat'])) $potentiel_magique /= 1 + ($actif['etat']['fleche_sable']['effet'] / 100);
	if(array_key_exists('fleche_debilitante', $actif['etat'])) $potentiel_magique /= 1 + ($actif['etat']['fleche_debilitante']['effet'] / 100);
	if($actif['etat']['posture']['type'] == 'posture_feu') $potentiel_magique *= 1 + (($actif['etat']['posture']['effet']) / 100);
	if($actif['arme_type'] == 'baton') $potentiel_magique_arme = $potentiel_magique * (1 + ($actif['arme_var1'] / 100));
	else $potentiel_magique_arme = $potentiel_magique;

  /* Application des effets de début de round */
  foreach ($effects as $effect) $effect->debut_round($actif, $passif);
  /* ~Debut */

	//Objets magiques
	foreach($actif['objet_effet'] as $effet)
	{
		switch($effet['id'])
		{
			case '11' :
				$potentiel_magique_arme += $potentiel_magique_arme * (1 + ($effet['effet'] / 100));
			break;
		}
	}
	$de_pot = 100;
	$de_diff = 100;
	if($potentiel_magique_arme > $row['difficulte']) $de_pot += $potentiel_magique_arme - $row['difficulte'];
	else $de_diff += $row['difficulte'] - $potentiel_magique_arme;
	$attaque = rand(0, $de_pot);
	$defense = rand(0, $de_diff);
	echo '
	<div id="debug'.$debugs.'" class="debug">
		Potentiel magique : '.$potentiel_magique_arme.'<br />
		Difficulté : '.$row['difficulte'].'<br />
		Résultat => Lanceur : '.$attaque.' | Sort '.$defense.'<br />
	</div>';
	$debugs++;
	//Si le sort touche, on le lance
	if ($attaque > $defense)
	{
		//Lancement du sort sur l'ennemi
		if($row['cible'] == 4)
		{
			$pm = $passif['PM'];
			if(array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
			if(array_key_exists('debuff_desespoir', $passif['debuff'])) $debuff_desespoir = 1 + (($passif['debuff']['debuff_desespoir']['effet']) / 100); else $debuff_desespoir = 1;
			if(array_key_exists('batiment_pm', $passif['buff'])) $buff_batiment_barriere = 1 + (($passif['buff']['batiment_pm']['effet']) / 100); else $buff_batiment_barriere = 1;
			if(array_key_exists('buff_forteresse', $passif['buff'])) $buff_forteresse = 1 + (($passif['buff']['buff_forteresse']['effet']) / 100); else $buff_forteresse = 1;
			if($passif['etat']['posture']['type'] == 'posture_glace') $aura_glace = 1 + (($passif['etat']['posture']['effet']) / 100); else $aura_glace = 1;
			//Corrompu la nuit
			if($actif['race'] == 'humainnoir' AND moment_jour() == 'Nuit') $bonus_race = 1.1; else $bonus_race = 1;
			$PM = $pm * $bonus_race * $aura_glace * $buff_batiment_barriere * $buff_forteresse / $debuff_desespoir;
			$potentiel_toucher = round($actif['volonte'] * $potentiel_magique);
			$potentiel_parer = round($passif['volonte'] * $PM);

			/* Application des effets de début de round */
			foreach ($effects as $effect)
				$potentiel_toucher =
					$effect->calcul_attaque_magique($actif, $passif, $potentiel_toucher);
			/* ~Debut */
			
			/* Application des effets de début de round */
			foreach ($effects as $effect)
				$potentiel_parer =
					$effect->calcul_defense_magique($actif, $passif, $potentiel_parer);
			/* ~Debut */

			$attaque = rand(0, $potentiel_toucher);
			$defense = rand(0, $potentiel_parer);
		}
		//Lancement du sort sur soi
		else
		{
			$attaque = 1;
			$defense = 0;
		}
		echo '
		<div id="debug'.$debugs.'" class="debug">
			Potentiel toucher : '.$potentiel_toucher.'<br />
			Potentiel parer : '.$potentiel_parer.'<br />
			Résultat => Lanceur : '.$attaque.' | Défenseur '.$defense.'<br />
		</div>';
		$debugs++;
		//Si le sort touche, on le lance
		if ($attaque > $defense)
		{
			switch($row['type'])
			{
				case 'degat_feu' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'degat_nature' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'degat_mort' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'degat_froid' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$chances = rand(0, ($row['effet2'] * 10));
					$tirage = rand(0, 100);
					if($chances > $tirage)
					{
						$passif['etat']['glacer']['effet'] = 1;
						$passif['etat']['glacer']['duree'] = $row['duree'];
					}
				break;
				case 'degat_vent' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$cap = $row['effet2'];
					$de = rand(0, 100);
					echo '
					<div id="debug'.$debugs.'" class="debug">
						1d100 doit être inférieur a '.$row['effet2'].'<br />
						Résultat => '.$de.' doit être inférieur a '.$row['effet2'].'<br />
					</div>';
					$debugs++;
					if($de < $cap)
					{
						echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> gagne 1 PA<br />';
						$actif['pa'] += 1;
					}
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'sacrifice_morbide' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> se suicide et inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['hp'] = 0;
				break;
				case 'degat_terre' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$actif['etat']['tellurique']['effet'] += $row['effet2'];
					$actif['etat']['tellurique']['duree'] = $row['duree'];
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'lapidation' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$chances = $row['effet2'];
					$tirage = rand(0, 100);
					if($tirage < $chances)
					{
						$passif['etat']['etourdit']['effet'] = 1;
						$passif['etat']['etourdit']['duree'] = $row['duree'];
					}
				break;
				case 'globe_foudre' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					$rand = rand(0, 100);
					//Debuff
					if($rand < 50)
					{
						$effects[] = new globe_foudre(1, 15, true);
					}
					else
					{
						//Dégat +1
						$degat++;
					}
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'pacte_sang' :
					$cout_hp = ceil($actif['hp_max'] * $row['effet2'] / 100);
					if($cout_hp < $actif['hp'])
					{
						$bonus = $actif['buff']['buff_surpuissance']['effet'];
						$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
						echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
						$passif['hp'] = $passif['hp'] - $degat;
						$actif['hp'] = $actif['hp'] - $cout_hp;
					}
					else
					{
					}
				break;
				case 'drain_vie' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique(($actif[$row['carac_assoc']] - 2), ($row['effet'] + $bonus), $actif, $passif);
					if($passif['type2'] == 'batiment') $drain = 0; else $drain = round($degat * 0.3);
					if(array_key_exists('recuperation', $actif)) $actif['etat']['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['hp'] = $actif['hp'] + $drain;
					if($actif['hp'] > $actif['hp_max']) $actif['hp'] = $actif['hp_max'];
				break;
				case 'vortex_vie' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique(($actif[$row['carac_assoc']] - 2), ($row['effet'] + $bonus), $actif, $passif);
					if($passif['type2'] == 'batiment') $drain = 0; else $drain = round($degat * 0.5);
					if(array_key_exists('recuperation', $actif)) $actif['etat']['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['hp'] = $actif['hp'] + $drain;
					if($actif['hp'] > $actif['hp_max']) $actif['hp'] = $actif['hp_max'];
				break;
				case 'vortex_mana' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique(($actif[$row['carac_assoc']] - 2), ($row['effet'] + $bonus), $actif, $passif);
					$drain = round($degat * 0.2);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> RM grâce au drain</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['reserve'] = $actif['reserve'] + $drain;
				break;
				case 'putrefaction' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$passif['etat']['putrefaction']['duree'] = 1;
					$passif['etat']['putrefaction']['effet'] = 2;
				break;
				case 'brisement_os' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					if($passif['etat']['paralysie']['duree'] > 0) $degat = round($degat * 1.6);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
				break;
				case 'embrasement' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$passif['etat']['embraser']['duree'] = 5;
					$passif['etat']['embraser']['effet'] = 1;
				break;
				case 'sphere_glace' :
					$bonus = $actif['etat']['tellurique']['effet'] + $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique($actif[$row['carac_assoc']], ($row['effet'] + $bonus), $actif, $passif);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$passif['etat']['glace_anticipe']['duree'] = 3;
					$passif['etat']['glace_anticipe']['effet'] = 10;
				break;
				case 'brulure_mana' :
					$brule_mana = $row['effet'];
					$degat = $row['effet'] * $row['effet2'];
					if(critique_magique($actif, $passif))
					{
						$degat = degat_critique($actif, $passif, $degat);
					}
					//Diminution des dégats grâce à l'armure magique
					$reduction = calcul_pp(($PM * $passif['puissance']) / 12);
					$degat = round($degat * $reduction);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> retire '.$brule_mana.' réserve de mana et inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$passif['reserve'] = $passif['reserve'] - $brule_mana;
				break;
				case 'appel_tenebre' :
					$passif['etat']['appel_tenebre']['effet'] = $row['effet'];
					$passif['etat']['appel_tenebre']['duree'] = 10;
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort Appel des ténêbres.<br />';
				break;
				case 'appel_foret' :
					$actif['etat']['appel_foret']['effet'] = $row['effet'];
					$actif['etat']['appel_foret']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort Appel de la forêt.<br />';
				break;
				case 'benediction' :
					$actif['etat']['benediction']['effet'] = $row['effet'];
					$actif['etat']['benediction']['duree'] = 10;
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se lance le sort bénédiction<br />';
				break;
				case 'paralysie' :
					//Objets magiques
					foreach($actif['objet_effet'] as $effet)
					{
						switch($effet['id'])
						{
							case '6' :
								//$potentiel_magique_arme += $potentiel_magique_arme * (1 + ($effet['effet'] / 100));
							break;
						}
					}
					$sm = ($actif['volonte'] * $actif['sort_mort']);
					$pm = $passif['PM'];
					if (array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
					$pm = $passif['volonte'] * ($pm * 2);
					$att = rand(0, $sm);
					$def = rand(0, $pm);
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort paralysie<br />';
					if($att > $def)
					{
						echo ' et réussit !<br />';
						$passif['etat']['paralysie']['effet'] = $row['effet'];
						$passif['etat']['paralysie']['duree'] = $row['effet'];
					}
					else
					{
						echo ' et échoue...<br />';
					}
					echo '<div class="debug" id="debug'.$debugs++."\">Potentiel paralyser : $sm<br />Potentiel résister : $pm<br />Résultat => Lanceur : $att | Défenseur $def<br /></div>";
				break;
				case 'silence' :
					$sm = ($actif['volonte'] * $actif['sort_mort']);
					
					$pm = $passif['PM'];
					if (array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
					$pm = $passif['volonte'] * $pm;
					$att = rand(0, $sm);
					$def = rand(0, $pm);
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort silence<br />';
					if($att > $def)
					{
						echo ' et réussit !<br />';
						$passif['etat']['silence']['effet'] = $row['effet'];
						$passif['etat']['silence']['duree'] = $row['duree'];
					}
					else
					{
						echo ' et échoue...<br />';
					}
					echo '<div class="debug" id="debug'.$debugs++."\">Potentiel silencer : $sm<br />Potentiel résister : $pm<br />Résultat => Lanceur : $att | Défenseur $def<br /></div>";
				break;
				case 'lien_sylvestre' :
					$passif['etat']['lien_sylvestre']['effet'] = $row['effet'];
					$passif['etat']['lien_sylvestre']['effet2'] = $row['effet2'];
					$passif['etat']['lien_sylvestre']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort lien sylvestre<br />';
				break;
				case 'poison' :
					$passif['etat']['poison']['effet'] = $row['effet'];
					$passif['etat']['poison']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort poison<br />';
				break;
				case 'jet_acide' :
					$passif['etat']['acide']['effet'] = $row['effet'];
					$passif['etat']['acide']['effet2'] = $row['effet2'];
					$passif['etat']['acide']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> lance le sort jet d\'acide<br />';
				break;
				case 'recuperation' :
					$actif['etat']['recuperation']['effet'] = $row['effet'];
					$actif['etat']['recuperation']['duree'] = 10;
					$actif['etat']['recuperation']['hp_max'] = $actif['hp'];
					echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se lance le sort récupération<br />';
				break;
				case 'aura_feu' :
					echo '&nbsp;&nbsp;Une enveloppe de feu entoure <strong>'.$actif['nom'].'</strong> !<br />';
					$actif['etat']['posture']['effet'] = $row['effet'];
					$actif['etat']['posture']['duree'] = 20;
					$actif['etat']['posture']['type'] = 'posture_feu';
				break;
				case 'aura_glace' :
					echo '&nbsp;&nbsp;Une enveloppe de glace entoure <strong>'.$actif['nom'].'</strong> !<br />';
					$actif['etat']['posture']['effet'] = $row['effet'];
					$actif['etat']['posture']['duree'] = 20;
					$actif['etat']['posture']['type'] = 'posture_glace';
				break;
				case 'aura_vent' :
					echo '&nbsp;&nbsp;Des tourbillons d\'air entourent <strong>'.$actif['nom'].'</strong> !<br />';
					$actif['etat']['posture']['effet'] = $row['effet'];
					$actif['etat']['posture']['duree'] = 20;
					$actif['etat']['posture']['type'] = 'posture_vent';
				break;
				case 'aura_pierre' :
					echo '&nbsp;&nbsp;De solides pierre volent autour de <strong>'.$actif['nom'].'</strong> !<br />';
					$actif['etat']['posture']['effet'] = $row['effet'];
					$actif['etat']['posture']['duree'] = 20;
					$actif['etat']['posture']['type'] = 'posture_pierre';
				break;
			}
		}
		else
		{
			echo '&nbsp;&nbsp;<span class="manque">'.$actif['nom'].' manque la cible avec '.$row['nom'].'</span><br />';
		}
	}
	else
	{
		echo '&nbsp;&nbsp;<span class="manque">'.$actif['nom'].' rate le lancement de '.$row['nom'].'</span><br />';
	}
	//Augmentation des compétences liées
	$augmentation = augmentation_competence($row['comp_assoc'], $actif, (4 * sqrt($actif[$row['comp_assoc']] / ($row['difficulte'] / 4))));
	if ($augmentation[1] == 1)
	{
		$actif[$row['comp_assoc']] = $augmentation[0];
		if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
	}
	$augmentation = augmentation_competence('incantation', $actif, (3 * sqrt($actif['incantation'] / ($row['difficulte'] / 2))));
	if ($augmentation[1] == 1)
	{
		$actif['incantation'] = $augmentation[0];
		if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['incantation'].' en incantation</span><br />';
	}

	if ($acteur == 'attaquant')
	{
		$attaquant = $actif;
		$defenseur = $passif;
	}
	else
	{
		$attaquant = $passif;
		$defenseur = $actif;
	}

  /* Application des effets de fin de round */
  // On passe directement la globale, car $actif et $passif sont des copies
  if ($acteur == 'attaquant')
    {
      $ref_actif =& $attaquant;
      $ref_passif =& $defenseur;
    }
  else
    {
      $ref_actif =& $defenseur;
      $ref_passif =& $attaquant;
    }
  foreach ($effects as $effect)
    $effect->fin_round($ref_actif, $ref_passif);
  /* ~Fin de round */

	return $row['comp_assoc'];
}

function lance_comp($id, $acteur)
{
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $comp_attaque, $G_round_total;
	if ($acteur == 'attaquant')
	{
		$actif = $attaquant;
		$passif = $defenseur;
	}
	else
	{
		$actif = $defenseur;
		$passif = $attaquant;
	}
	//Recherche de la compétence
	$requete = "SELECT * FROM comp_combat WHERE id = ".$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

	$mp_need = $row['mp'];
	//Appel des ténebres
	if($actif['etat']['appel_tenebre']['duree'] > 0)
	{
		$mp_need += $actif['etat']['appel_tenebre']['effet'];
	}
	//Appel de la forêt
	if($actif['etat']['appel_foret']['duree'] > 0)
	{
		$mp_need_avant = $mp_need;
		$mp_need -= $actif['etat']['appel_foret']['effet'];
		if($mp_need < 1) $mp_need = $mp_need_avant;
	}
	//Suppresion de la réserve
	$actif['reserve'] -= $mp_need;

	$comp_attaque = false;
	$utilise_comp = $row['type'];
	//echo $row['type'];
	switch($row['type'])
	{
		case 'tir_precis' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['potentiel_toucher'] = $actif['potentiel_toucher'] * (1 + ($row['effet'] / 100));
			$comp_attaque = true;
		break;
		case 'oeil_faucon' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['potentiel_toucher'] = $actif['potentiel_toucher'] * (1 + ($row['effet'] / 10));
			$comp_attaque = true;
		break;
		case 'coup_puissant' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['degat_sup'] = $row['effet'];
			$comp_attaque = true;
		break;
		case 'coup_violent' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['degat_sup'] = $row['effet'];
			$comp_attaque = true;
		break;
		case 'coup_mortel' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['potentiel_toucher'] = $actif['potentiel_toucher'] * (1 - ($row['effet'] / 100));
			$actif['coup_mortel'] = true;
			$comp_attaque = true;
		break;
		case 'coup_sournois' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['etat']['coup_sournois']['effet'] = $row['effet'];
			$actif['etat']['coup_sournois']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_vicieuse' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['potentiel_toucher'] = $actif['potentiel_toucher'] * (1 - ($row['effet'] / 100));
			$actif['degat_sup'] = $row['effet2'];
			$actif['attaque_vicieuse'] = $row['effet2'];
			$comp_attaque = true;
		break;
		case 'berzeker' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> passe en mode '.$row['nom'].' !<br />';
			$actif['etat']['berzeker']['effet'] = $row['effet'];
			$actif['etat']['berzeker']['duree'] = 10;
			$comp_attaque = false;
		break;
		case 'tir_vise' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se concentre pour viser !<br />';
			$actif['etat']['tir_vise']['effet'] = $row['effet'];
			$actif['etat']['tir_vise']['duree'] = 2;
			$comp_attaque = false;
			$diff = 3 * $G_round_total / 10;
			$augmentation = augmentation_competence('distance', $actif, $diff);
			if($actif['arme_type'] == 'arc' AND array_key_exists('maitrise_arc', $actif['competences'])) $maitrise_arc = 1 + ($actif['competences']['maitrise_arc'] / 1000); else $maitrise_arc = 1;
			if ($augmentation[1] == 1)
			{
				$actif['distance'] = $augmentation[0];
				if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['distance'].' en '.$Gtrad['distance'].'</span><br />';
			}
			if($maitrise_arc != 1)
			{
				$actif['maitrise_arc'] = $actif['competences']['maitrise_arc'];
				$augmentation = augmentation_competence('maitrise_arc', $actif, 5);
				if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_arc'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_arc'].' en '.$Gtrad['maitrise_arc'].'</span><br />';
					$ups[] = 'maitrise_arc';
				}
			}
		break;
		case 'fleche_etourdissante' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise une flêche étourdissante !<br />';
			$actif['degat_moins'] = $row['effet'];
			$de_att = rand(0, (($actif['force'] + $actif['dexterite']) / 2));
			$de_deff = rand(0, $passif['vie']);
			if($de_att > $de_deff)
			{
				$actif['etat']['fleche_etourdit']['effet'] = $row['effet'];
				$actif['etat']['fleche_etourdit']['duree'] = 1;
			}
			$comp_attaque = true;
		break;
		case 'tir_puissant' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['potentiel_toucher'] = $actif['potentiel_toucher'] * (1 - ($row['effet2'] / 100));
			$actif['degat_sup'] = $row['effet'];
			$comp_attaque = true;
		break;
		case 'fleche_magnetique' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.
				$row['nom'].'<br />';
			$comp_attaque = true;
			$passif['etat']['fleche_magnetique']['effet'] = $row['effet'];
			$passif['etat']['fleche_magnetique']['effet2'] = $row['effet2'];
		break;
		case 'fleche_sable' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$passif['etat']['fleche_sable']['effet'] = $row['effet2'];
			$passif['etat']['fleche_sable']['duree'] = $row['duree'];
			$actif['degat_moins'] = $row['effet'];
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'fleche_poison' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> empoisone avec '.$row['nom'].' !<br />';
			$actif['etat']['fleche_poison']['effet'] = $row['effet'];
			$actif['etat']['fleche_poison']['level'] = $row['level'];
			$actif['etat']['fleche_poison']['duree'] = 1;
			$actif['degat_sup'] = $row['effet'];
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'fleche_rapide' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$passif['etat']['fleche_rapide']['effet'] = $row['effet'];
			$passif['etat']['fleche_rapide']['duree'] = 1;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'fleche_sanglante' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$actif['etat']['fleche_sanglante']['effet'] = $row['effet'];
			$actif['etat']['fleche_sanglante']['duree'] = 1;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'fleche_debilitante' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$passif['etat']['fleche_debilitante']['effet'] = $row['effet'];
			$passif['etat']['fleche_debilitante']['duree'] = 3;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'coup_bouclier' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> donne un coup de bouclier !<br />';
			$actif['etat']['coup_bouclier']['effet'] = $row['effet'];
			$actif['etat']['coup_bouclier']['effet2'] = $row['duree'];
			$actif['etat']['coup_bouclier']['duree'] = 0;
			$comp_attaque = true;
		break;
		case 'slam' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilises SLAM !<br />';
			$de_att = rand(0, (($actif['force'] + $actif['dexterite']) / 2));
			$de_deff = rand(0, $passif['vie']);
			echo $de_att.' '.$de_deff.'<br />';
			if($de_att > $de_deff)
			{
				$passif['etat']['etourdit']['effet'] = $row['effet'];
				$passif['etat']['etourdit']['duree'] = 1;
			}
			$comp_attaque = true;
		break;
		case 'frappe_derniere_chance' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$actif['etat']['derniere_chance']['effet'] = $row['effet2'];
			$actif['etat']['derniere_chance']['duree'] = 20;
			$actif['pm'] = $actif['pm'] / (1 + ($actif['etat']['derniere_chance']['effet'] / 100));
			$actif['degat_sup'] = $row['effet'];
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_critique' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_critique';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_esquive' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_esquive';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_defense' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_defense';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_degat' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_degat';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_transperce' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_transperce';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_paralyse' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_paralyse';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'posture_touche' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> se met en '.$row['nom'].' !<br />';
			$actif['etat']['posture']['effet'] = $row['effet'];
			$actif['etat']['posture']['duree'] = 20;
			$actif['etat']['posture']['type'] = 'posture_touche';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif['comp'];
			$comp_attaque = true;
		break;
		case 'dissimulation' :
			$att = rand(0, $actif['dexterite'] * $actif['esquive']);
			$def = rand(0, $passif['volonte'] * ($passif['PM'] * 2.5));
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> tente de se dissimuler...';
			if($att > $def)
			{
				echo ' et réussit !<br />';
				$actif['etat']['dissimulation']['effet'] = $row['effet'];
				$actif['etat']['dissimulation']['duree'] = 2;
			}
			else
			{
				echo ' et échoue...<br />';
			}
		break;
		case 'bouclier_protecteur' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> intensifie sa protection magique grace à son bouclier !<br />';
			$actif['etat']['bouclier_protecteur']['effet'] = $row['effet'];
			$actif['etat']['bouclier_protecteur']['duree'] = 10;
		break;
		case 'feinte' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['etat']['a_toucher']['effet'] += $row['effet'];
			$actif['etat']['a_toucher']['duree'] = 1;
			$actif['etat']['a_critique']['effet'] += $row['effet'];
			$actif['etat']['a_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_cote' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['etat']['a_toucher']['effet'] += $row['effet'];
			$actif['etat']['a_toucher']['duree'] = 1;
			$actif['etat']['a_c_bloque']['effet'] += $row['effet2'];
			$actif['etat']['a_c_bloque']['duree'] = 1;
			$actif['etat']['b_critique']['effet'] += $row['effet'];
			$actif['etat']['b_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_brutale' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['degat_sup'] += $row['effet'];
			$actif['etat']['a_c_bloque']['effet'] += $row['effet2'];
			$actif['etat']['a_c_bloque']['duree'] = 1;
			$actif['etat']['b_critique']['effet'] += $row['effet'];
			$actif['etat']['b_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_rapide' :
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].'<br />';
			$actif['etat']['a_toucher']['effet'] += $row['effet'];
			$actif['etat']['a_toucher']['duree'] = 1;
			$actif['etat']['b_critique']['effet'] += $row['effet'];
			$actif['etat']['b_critique']['duree'] = 1;
			$comp_attaque = true;
			break;
	  default:
			// On traite toutes les autres compétences, génériquement
			$actif['etat'][$type]['effet'] = $row['effet'];
			$actif['etat'][$type]['effet2'] = $row['effet2'];
			$actif['etat'][$type]['effet3'] = $row['effet3'];
			$actif['etat'][$type]['duree'] = $row['duree'];
			$actif['etat'][$type]['level'] = $row['level'];
			$comp_attaque = ($G_cibles[$row['cible']] == 'Ennemi');
			break;
	}

	if ($acteur == 'attaquant')
	{
		$attaquant = $actif;
		$defenseur = $passif;
	}
	else
	{
		$attaquant = $passif;
		$defenseur = $actif;
	}
	return $row['comp_assoc'];
}

function critique_magique($attaquant, $defenseur)
{
	global $debugs;
	$chance = rand(0, 10000);
	$actif_chance_critique = ($attaquant['volonte'] * 50);
	if(array_key_exists('buff_furie_magique', $attaquant['buff'])) $actif_chance_critique = $actif_chance_critique  * (1 + ($attaquant['buff']['buff_furie_magique']['effet'] / 100));
	$critique = false;
	echo '
	<div id="debug'.$debugs.'" class="debug">
		Potentiel critique attaquant : '.$actif_chance_critique.' / 10000<br />
		Résultat => '.$chance.' doit être inférieur au Potentiel critique<br />
	</div>';
	$debugs++;
	if($chance < $actif_chance_critique)
	{
		echo '&nbsp;&nbsp;<span class="coupcritique">SORT CRITIQUE !</span><br />';
		$critique = true;
	}
	return $critique;
}

function degat_critique($actif, $passif, $degat)
{
	global $debugs;
	echo '<div id="debug'.$debugs.'" class="debug">';
	//Les dégats des critiques sont diminués par la puissance
	$puissance = 1 + ($passif['puissance'] * $passif['puissance'] / 1000);
	$degat = ($degat * 2);
	$degat_avant = $degat;
	$degat = round($degat / $puissance);
	echo '(Réduction de '.($degat_avant - $degat).' dégats critique par la puissance)<br />
	</div>';
	return($degat);
}

function get_etats()
{
	$etats = array();
	$etats['poison']['nom'] = 'Empoisonné';
	$etats['poison']['id'] = 'poison';
	$etats['paralysie']['nom'] = 'Paralysé';
	$etats['paralysie']['id'] = 'paralysie';
	$etats['etourdit']['nom'] = 'Etourdit';
	$etats['etourdit']['id'] = 'etourdit';
	$etats['silence']['nom'] = 'sous Silence';
	$etats['silence']['id'] = 'silence';
	$etats['dissimulation']['nom'] = 'Dissimulé';
	$etats['dissimulation']['id'] = 'dissimulation';
	$etats['glacer']['nom'] = 'Glacé';
	$etats['glacer']['id'] = 'glacer';
	$etats['posture']['nom'] = 'En posture / Aura';
	$etats['posture']['id'] = 'posture';
	$etats['berzeker']['nom'] = 'Berzerk';
	$etats['berzeker']['id'] = 'berzeker';
	$etats['appel_foret']['nom'] = 'Appel de la forêt';
	$etats['appel_foret']['id'] = 'appel_foret';
	$etats['appel_tenebre']['nom'] = 'Appel des ténêbres';
	$etats['appel_tenebre']['id'] = 'appel_tenebre';
	$etats['recuperation']['nom'] = 'sous Récupération';
	$etats['recuperation']['id'] = 'recuperation';
	$etats['benediction']['nom'] = 'Béni';
	$etats['benediction']['id'] = 'benediction';
	$etats['lien_sylvestre']['nom'] = 'sous Lien Sylvestre';
	$etats['lien_sylvestre']['id'] = 'lien_sylvestre';
	$etats['tellurique']['nom'] = 'Frappe Tellurique';
	$etats['tellurique']['id'] = 'tellurique';
	$etats['glace_anticipe']['nom'] = 'Orbe de Glace';
	$etats['glace_anticipe']['id'] = 'glace_anticipe';
	$etats['tir_vise']['nom'] = 'Tir visé';
	$etats['tir_vise']['id'] = 'tir_vise';
	$etats['fleche_sable']['nom'] = 'Flêche de sable';
	$etats['fleche_sable']['id'] = 'fleche_sable';
	$etats['fleche_poison']['nom'] = 'Flêche Empoisonnée';
	$etats['fleche_poison']['id'] = 'fleche_poison';
	$etats['fleche_debilitante']['nom'] = 'Flêche Débilitante';
	$etats['fleche_debilitante']['id'] = 'fleche_debilitante';
	$etats['derniere_chance']['nom'] = 'Dernière Chance';
	$etats['derniere_chance']['id'] = 'derniere_chance';
	$etats['bouclier_protecteur']['nom'] = 'Bouclier Protecteur';
	$etats['bouclier_protecteur']['id'] = 'bouclier_protecteur';
	return $etats;
}
?>
