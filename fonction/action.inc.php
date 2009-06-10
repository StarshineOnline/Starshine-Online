<?php //  -*- tab-width:2  -*-
/**
 * @file action.inc.php
 * Gestion des scripts. 
 */ 

/**
 * Renvoie l'action a effectuer, si le personnage peut en faire une.
 * Récupère l'action à effectuer à l'aide à l'aide de la fonction sub_script_action(), 
 * vérifie si une dissimulation ne l'empêche pas et si c'est le cas renvoie cette action.  
 *
 * @param $joueur Pesonnage du joueur. 
 * @param $ennemi Adversaire. 
 * @param $mode Indique si le personnage attaque ("attaquant") ou défend.
 * @param $effects liste des effets.
 * 
 * @return [0] Type d'action : "lance_sort" pour un sort, "lance_comp" pour une compétence, 
 *  "attaque" pour une attaque simple, "" si le personnage ne peut pas effectuer d'action
 * @return [1] ID d la compétence ou du sort.
 * @return [2] Pesonnage (paramètre $joueur auquel on a incrémenté le compteur d'utilisation de l'action à effectuer).   
 */
function script_action($joueur, $ennemi, $mode, $effects)
{
	$effectue = sub_script_action($joueur, $ennemi, $mode, $effects);
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

/**
 * Détermine l'action a effectuer, si on peut.
 * Commence par vérifier si le personnage peut effectuer une action en fonction de son état.
 * Ensuite détermine l'action à efectuer en foncion du script. Puis finallement détermine si
 * l'action est anticipée.    
 *
 * @param $joueur Pesonnage du joueur. 
 * @param $ennemi Adversaire. 
 * @param $mode Indique si le personnage attaque ("attaquant") ou défend.
 * @param $effects liste des effets.
 * 
 * @return [0] Type d'action : "lance_sort" pour un sort, "lance_comp" pour une compétence, 
 *  "attaque" pour une attaque simple, "" si le personnage ne peut pas effectuer d'action
 * @return [1] ID d la compétence ou du sort.
 * @return [2] Pesonnage (paramètre $joueur auquel on a incrémenté le compteur d'utilisation de l'action à effectuer).  
 */   
function sub_script_action($joueur, $ennemi, $mode, $effects)
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
		  // On regarde si le personnage est glacé
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
		if(!$stop)  // Etrange : il y a un "return" juste avant le seul moment ou stop est mit à "true".
		{
		  // Récupèration des actions du personnage
			if($mode == 'attaquant') $actions = explode(';', $joueur['action_a']);
			else $actions = explode(';', $joueur['action_d']);
			$count = count($actions);
			$action = false;
			$i = 0;
			while(($i < $count) && (!$action))
			{
			  // Récupération des conditions et de l'action
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
				// Récupération du type d'action (sort, compétence ou attaque)
				$type_action = substr($solution, 0, 1);
				if($conditions != '')
				{
				  // Vérification des conditions
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
						// Recherche de la valeur du paramètre
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
								// Récupération du nombre d'utilisation
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
						// Vérification de la condition
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
							// Vérification si le personnage est dans un certain état
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
							// Vérification si le personnage n'est pas dans un certain état
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
					}  // Fin de la vérification des actions (boucle while)
				}
				else // Pas de condition
				{
					$valid = true;
				}
				if($valid)
				{
					//Vérification si action possible
					if($type_action == '~')  // sort
					{
						// Recherche du sort
						$id_sort = substr($solution, 1);
						if($id_sort != '')
						{
							$requete = "SELECT * FROM sort_combat WHERE id = ".$id_sort;
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							// Récupération des MP nécessaires
							$mp_need = round($row['mp'] * (1 - (($Trace[$joueur['race']]['affinite_'.$row['comp_assoc']] - 5) / 10)));
							// Appel des ténebres
							if($joueur['etat']['appel_tenebre']['duree'] > 0)
							{
								$mp_need += $joueur['etat']['appel_tenebre']['effet'];
							}
							// Appel de la forêt
							if($joueur['etat']['appel_foret']['duree'] > 0 && $mp_need > 1)
							{
								$mp_need -= $joueur['etat']['appel_foret']['effet'];
								if($mp_need < 1) $mp_need = 1;
							}

              /* Application des effets de mana */
              foreach ($effects as $effect)
                $mp_need = $effect->calcul_mp($actif, $mp_need);
              /* ~Mana */

							// Si le joueur a assez de reserve on indique l'action à effectuer
							if($joueur['reserve'] >= $mp_need)
							{
								$effectue[0] = 'lance_sort';
								$effectue[1] = $id_sort;
								$action = true;
							}
						}
						else
						{
							$effectue[0] = 'attaque';
							$action = true;
						}
					}
					elseif($type_action == '_')  // compétence
					{
						// Recherche de la compétence
						$id_sort = substr($solution, 1);
						if($id_sort != '')
						{
							$requete = "SELECT * FROM comp_combat WHERE id = ".$id_sort;
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							// Récupération des MP nécessaires
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
							
              /* Application des effets de mana */
              foreach ($effects as $effect)
                $mp_need = $effect->calcul_mp($actif, $mp_need);
              /* ~Mana */

							// On vérifie que le personnage a assez de MP
							if($joueur['reserve'] >= $mp_need)
							{
								// Si l'arme utilisée est la bonne on indique l'action à effectuer
								$arme_requis = explode(';', $row['arme_requis']);
								if(in_array($joueur['arme_type'], $arme_requis) OR in_array($joueur['bouclier_type'], $arme_requis) OR $row['arme_requis'] == '')
								{
									$effectue[0] = 'lance_comp';
									$effectue[1] = $id_sort;
									$action = true;
								}
							}
						}
						else
						{
							$effectue[0] = 'attaque';
							$action = true;
						}
					}
					// Attaque simple
					if($solution == '!')
					{
						$effectue[0] = 'attaque';
						$action = true;
					}
				}
				$i++;
			} // Fin de la revue des actions (boucle while)
			// Si aucune action n'est définie on indique une attaque par défaut
			if(!$action) $effectue[0] = 'attaque';
			
			// Anticipation (si l'ennemi anticipe alors échec de l'action)
			if($effectue[0] == 'attaque') $id = 0;
			else $id = $effectue[1];
			//Si il y a déjà eu une attaque de ce type, alors risque d'échec
			if(array_key_exists('anticipation', $joueur) AND array_key_exists($effectue[0], $joueur['anticipation']) AND array_key_exists($id, $joueur['anticipation'][$effectue[0]]) AND $joueur['anticipation'][$effectue[0]][$id] > 0)
			{
				// On récupère le nombre d'utilisations et calcul des chances de réussite
				$nbr_utilisation = $joueur['anticipation'][$effectue[0]][$id];
				$chance_reussite = 100 - ($nbr_utilisation * $nbr_utilisation);
				// Si l'adversaire est de niveau < 5, alors il a moins de chances d'anticiper
				if($ennemi['level'] < 5)
				{
					$chance_reussite = 100 - ( (100 - $chance_reussite) / (6 - $ennemi['level']) );
				}
				// Réduction des chances d'anticiper si adversaire glacé (avec une orbe de glace)
				if($ennemi['etat']['glace_anticipe']['duree'] > 0) $chance_reussite = $chance_reussite * $ennemi['etat']['glace_anticipe']['effet'];
				// Réduction des chances d'anticiper si adversaire amorphe
				if(array_key_exists('maladie_amorphe', $joueur['debuff'])) $chance_reussite = $chance_reussite - $joueur['debuff']['maladie_amorphe']['effet'];
				// Ob détermine si l'action est anticipée
				$rand = rand(0, 100);
				echo '
					<div id="debug'.$debugs.'" class="debug">
						Probabilité de réussir l\'anticipation : '.(100 - $chance_reussite).'%<br />
						'.$rand.' doit être supérieur à '.$chance_reussite.' pour anticipation<br />
					</div>';
				$debugs++;
				// Echec
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

/**
 * Lance un sort lors d'un combat.
 * Effectuer les jets de lancer et toucher, plus éventuellement le troisième jet
 * pour les effets s'il y en a un. Si le sort est réussi, retire la RM et 
 * applique les dégâts et/ou enregistre les effets.
 * 
 * @param $id ID du sort.
 * @param $acteur indique si l'acteur de l'action est l'attaquant
 * ('attaquant') ou le défenseur.
 * @param $effects liste des effets.
 * 
 * return Compétence de magie associée au sort.
 */
function lance_sort($id, $acteur, $effects)
{
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $Trace, $G_buff, $G_debuff;
	// Définition des personnages actif et passif
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

  /* Application des effets de début de round */
  foreach ($effects as $effect) $effect->debut_round($actif, $passif);
  /* ~Debut */

	//Recherche du sort
	$requete = "SELECT * FROM sort_combat WHERE id = ".$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

  // Calcul de MP nécessaires
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

  /* Application des effets de mana */
  foreach ($effects as $effect)
    $mp_need = $effect->calcul_mp($actif, $mp_need);
  /* ~Mana */

	//Suppresion de la réserve
	$actif['reserve'] -= $mp_need;

  // Calcul du potentiel magique
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
	// Calcule des dés de potentiel magique et de difficulté
	$de_pot = 300;
	$de_diff = 300;
	if($potentiel_magique_arme > $row['difficulte']) $de_pot += $potentiel_magique_arme - $row['difficulte'];
	else $de_diff += $row['difficulte'] - $potentiel_magique_arme;
	// Lancer des dés
	$attaque = rand(0, $de_pot);
	$defense = rand(0, $de_diff);
	// Affichage des informations de debug
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
		  // Calcul de la PM
			$pm = $passif['PM'];

			/* Application des effets de PM */
			foreach ($effects as $effect)
				$pm = $effect->calcul_pm($actif, $passif, $pm);
			/* ~PM */

			if(array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
			if(array_key_exists('batiment_pm', $passif['buff'])) $buff_batiment_barriere = 1 + (($passif['buff']['batiment_pm']['effet']) / 100); else $buff_batiment_barriere = 1;
			if($passif['etat']['posture']['type'] == 'posture_glace') $aura_glace = 1 + (($passif['etat']['posture']['effet']) / 100); else $aura_glace = 1;
			//Corrompu la nuit
			if($actif['race'] == 'humainnoir' AND moment_jour() == 'Nuit') $bonus_race = 1.1; else $bonus_race = 1;
			$PM = $pm * $bonus_race * $aura_glace * $buff_batiment_barriere;
			// Calcul des potentiels toucher et parer
			$potentiel_toucher = round($actif['volonte'] * $potentiel_magique);
			$potentiel_parer = round($passif['volonte'] * $PM);

			/* Application des effets de début de round pour le potentiel toucher */
			foreach ($effects as $effect)
				$potentiel_toucher =
					$effect->calcul_attaque_magique($actif, $passif, $potentiel_toucher);
			/* ~Debut */
			
			/* Application des effets de début de round pour le potentiel parer */
			foreach ($effects as $effect)
				$potentiel_parer =
					$effect->calcul_defense_magique($actif, $passif, $potentiel_parer);
			/* ~Debut */

      // Lancer des dés
			$attaque = rand(0, $potentiel_toucher);
			$defense = rand(0, $potentiel_parer);
		}
		//Lancement du sort sur soi
		else
		{
		  // Réussite automatique
			$attaque = 1;
			$defense = 0;
		}
		// Affichage des informations de debug
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
					// On regarde si la cible est glacé
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
					// On regarde s'il y a un gain de PA
					$cap = $row['effet2'];
					$de = rand(0, 100);
					// Affichage des informations de debug
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
					// On regarde si la cible est étourdie
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
					// On vérifie que le personnage a assez de HP
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
					   // S'il n'a pas assez de HP il ne fait rien
					}
				break;
				case 'drain_vie' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique(($actif[$row['carac_assoc']] - 2), ($row['effet'] + $bonus), $actif, $passif);
					if($passif['type2'] == 'batiment') $drain = 0; else $drain = round($degat * 0.3);
					// Augmentation du nombre de HP récupérable par récupération
					if(array_key_exists('recuperation', $actif['etat'])) $actif['etat']['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['hp'] = $actif['hp'] + $drain;
					// On vérifie que le personnage n'a pas plus de HP que son maximum
					if($actif['hp'] > $actif['hp_max']) $actif['hp'] = $actif['hp_max'];
				break;
				case 'vortex_vie' :
					$bonus = $actif['buff']['buff_surpuissance']['effet'];
					$degat = degat_magique(($actif[$row['carac_assoc']] - 2), ($row['effet'] + $bonus), $actif, $passif);
					if($passif['type2'] == 'batiment') $drain = 0; else $drain = round($degat * 0.5);
					// Augmentation du nombre de HP récupérable par récupération
					if(array_key_exists('recuperation', $actif['etat'])) $actif['etat']['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif['hp'] = $passif['hp'] - $degat;
					$actif['hp'] = $actif['hp'] + $drain;
					// On vérifie que le personnage n'a pas plus de HP que son maximum
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
					// On regarde si c'est un critique et si oui on modifie les dégât en conséquence
					if(critique_magique($actif, $passif))
					{
						$degat = degat_critique($actif, $passif, $degat);
					}
					// Diminution des dégats grâce à l'armure magique
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
					/*foreach($actif['objet_effet'] as $effet)
					{
						switch($effet['id'])
						{
							case '6' :
								//$potentiel_magique_arme += $potentiel_magique_arme * (1 + ($effet['effet'] / 100));
							break;
						}
					}*/
					// Calcul du potentiel paralyser
					$sm = ($actif['volonte'] * $actif['sort_mort']);
					// Calcul du potentiel résister
					$pm = $passif['PM'];
					if (array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
					$pm = pow($passif['volonte'], 1.83) * sqrt($pm) * 3;
					// Lancer des dés
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
					// Calcul du potentiel paralyser
					$sm = ($actif['volonte'] * $actif['sort_mort']);
					
					// Calcul du potentiel résister
					$pm = $passif['PM'];
					if (array_key_exists('bouclier_protecteur', $passif['etat'])) $pm = $pm + ($passif['etat']['bouclier_protecteur']['effet'] * $passif['bouclier_degat']);
					$pm = $passif['volonte'] * $pm;
					// Lancer des dés
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
		else  // pas touché
		{
			echo '&nbsp;&nbsp;<span class="manque">'.$actif['nom'].' manque la cible avec '.$row['nom'].'</span><br />';
		}
	}
	else // lancer raté
	{
		echo '&nbsp;&nbsp;<span class="manque">'.$actif['nom'].' rate le lancement de '.$row['nom'].'</span><br />';
	}
	//Augmentation des compétences liées
	$augmentation = augmentation_competence($row['comp_assoc'], $actif, (6.5 * sqrt($actif[$row['comp_assoc']] / ($row['difficulte'] / 4))));
	if ($augmentation[1] == 1)
	{
		$actif[$row['comp_assoc']] = $augmentation[0];
		if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
	}
	$augmentation = augmentation_competence('incantation', $actif, (4.5 * sqrt($actif['incantation'] / ($row['difficulte'] / 2))));
	if ($augmentation[1] == 1)
	{
		$actif['incantation'] = $augmentation[0];
		if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['incantation'].' en incantation</span><br />';
	}

  /* Application des effets de fin de round */
  foreach ($effects as $effect)
    $effect->fin_round($actif, $passif);
  /* ~Fin de round */

  // On met à jour les protagonistes
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

/**
 * Utilise une compétence.
 * Applique les effets de la compétence et retire la RM.
 * 
 * @param $id ID de la compétence.
 * @param $acteur indique si l'acteur de l'action est l'attaquant ('attaquant') ou
 * le défenseur.
 * 
 * return Compétence associée à la compétence.
 */
function lance_comp($id, $acteur, $effects)
{
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $comp_attaque, $G_round_total;
	// Définition des personnages actif et passif
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

  // Calcul des MP nécessaires
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

  /* Application des effets de mana */
  foreach ($effects as $effect)
    $mp_need = $effect->calcul_mp($actif, $mp_need);
  /* ~Debut */

	//Suppresion de la réserve
	$actif['reserve'] -= $mp_need;

	$comp_attaque = false;  // Indique si le personnage attaque se round-ci.
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
			// Augmentation des compétences
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
			// On regarde si l'adversaire est étourdit
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
			echo '&nbsp;&nbsp;<strong>'.$actif['nom'].'</strong> utilise '.$row['nom'].' !<br />';
			$actif['degat_sup'] = $row['effet'];
			// On regarde si le poison fait effet
			$de_att = rand(0, (($actif['force'] + $row['effet'])));
			$de_deff = rand(0, $passif['volonte']);
			if($de_att > $de_deff)
			{
				echo '&nbsp;&nbsp;<strong>'.$passif['nom'].'</strong> est empoisonné pour '.$row['duree'].' tours !<br />';
				$passif['etat']['poison']['effet'] = $row['effet'];
				$passif['etat']['poison']['level'] = $row['effet'];
				$passif['etat']['poison']['duree'] += $row['duree'];
				if($passif['etat']['poison']['duree'] > $row['effet']) $passif['etat']['poison']['duree'] = $row['effet'] - 1;
			}
			else echo '&nbsp;&nbsp;Le poison n\'agit pas<br />';
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
			// On regarde si la compétence fait effet
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
			// Diminution de la PM
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
		  // On regarde si la dissimulation est réussie
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

  /* Application des effets de fin de round SI on ne fait pas d'attaque */
	if ($comp_attaque == false)
		foreach ($effects as $effect)
			$effect->fin_round($actif, $passif);
  /* ~Fin de round */


  // On met à jour les protagonistes
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

/**
 * Regarde s'il y a un critique magique.
 * Calcule le potentiel critique pour les sorts et lance le dé.
 * 
 * @param $attaquant Personnage attaquant.
 * @param $defenseur Personnage défendant.
 * 
 * @return true s'il y un critique, false s'il n'y en a pas.  
 */
function critique_magique($attaquant, $defenseur)
{
	global $debugs;  // Numéro des informations de debug.
	// Dé de critiques
	$chance = rand(0, 10000);
	// Calcule des chances de critique
	$actif_chance_critique = ($attaquant['volonte'] * 50);
	if(array_key_exists('buff_furie_magique', $attaquant['buff'])) $actif_chance_critique = $actif_chance_critique  * (1 + ($attaquant['buff']['buff_furie_magique']['effet'] / 100));
	$critique = false;  // Indique s'il y a un critique.
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

/**
 * Ajuste les dégâts en cas de critique.
 *  Double les dégâts et applique la réduction due à la puissance.
 *  
 * @param $actif Personnage attaquant.
 * @param $passif Personnage défendant.
 * @param $degat Dégâts de base.
 * 
 * @return Dégâts une fois les critiques appliqués.       
 */
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

/**
 *  Renvoie un tableau des éEtats avec l'ID et le nom.
 *  Les clés du tableau sont les ID, chaque élément contient deux autres éléments :
 *  le nom (clé "nom") et l'ID (clé "id"").
 *  
 * @return Tableau des états.
 */
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
