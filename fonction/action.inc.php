<?php //  -*- mode: php; tab-width: 2; -*-
if (file_exists('../root.php'))
  include_once('../root.php');

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
function script_action($joueur, $ennemi, $mode, &$attaque)
{
	$effectue = sub_script_action($joueur, $ennemi, $mode, $attaque);
	switch ($effectue[0])
	{
	case 'attaque':
		$att = comp_combat::creer_attaque();
		break;
	case 'lance_comp':
		$table = 'comp_combat';
		$att = $table::factory($effectue[1]);
		break;
	case 'lance_sort':
		$table = 'sort_combat';
		$att = $table::factory($effectue[1]);
		break;
	default:
		return null;
	}
	// On gère la dissimulation *après* le choix de l'action
	if ($ennemi->etat['dissimulation'] > 0)
		{
			/*switch ($effectue[0])
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
				}*/
			// Chercher la cible de la capacité utilisée
			/*global $db, $G_cibles;
			$requete = "SELECT cible FROM $table WHERE id = ".$effectue[1];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);*/
			// Si la cible est l'adversaire, alors c'est foiré
			if (/*$G_cibles[$row['cible']] == 'Ennemi'*/$att->get_cible() == comp_sort::cible_autre ) {
				echo $ennemi->get_nom().' est caché, '.$joueur->get_nom().' ne peut pas attaquer<br />';
				$attaque->add_log_combat('cc');
				return null;//'';
			}
		}
	print_debug("action sélectionnée: $effectue[0] $effectue[1]<br/>");
	return $att;//$effectue;
}

/*
 * Cette fonction permet de regrouper plusieurs états et leurs états dérivés
 * @param $valeur valeur 'maître de l'état'
 * @return le tableau de toutes les faleurs qui matchent
 */
function get_array_condition($valeur) {
  $array = array($valeur);
  switch ($valeur)
  {
    case 'poison':
      $array[] = 'empoisonne';
      $array[] = 'poison_lent';
      break;
  }
  return $array;
}

/**
 * Détermine l'action a effectuer, si on peut.
 * Commence par vérifier si le personnage peut effectuer une action en fonction de son état.
 * Ensuite détermine l'action à effectuer en foncion du script. Puis finallement détermine si
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
function sub_script_action($joueur, $ennemi, $mode, &$attaque)
{
	global $db, $round, $Trace, $debugs, $log_combat;
	$stop = false;
	$test = true;
	if($joueur->etat['paralysie'] > 0)
	{
		echo $joueur->get_nom().' est paralysé<br />';
		if (isset($joueur->etat['paralysie']['cpt']))
		{
			$joueur->etat['paralysie']['cpt']++;
		}
		else
		{
			$joueur->etat['paralysie']['cpt'] = 1;
		}
		$resist_para = pow($joueur->get_pm_para(), 0.5)*pow($joueur->get_volonte(),1.85) + $joueur->etat['paralysie']['cpt']*1000;
		$sm = ($ennemi->get_volonte() * $ennemi->get_sort_mort());
							
		$att = rand(0, $sm);
		$def = rand(0, $resist_para);	
		echo '<div class="debug" id="debug'.$debugs++."\">Potentiel para : $sm<br />Potentiel résister : $resist_para<br />Résultat => Lanceur : $att | Défenseur $def<br /></div>";
		if($att < $def)
		{
			unset($joueur->etat['paralysie']['cpt']);
			$joueur->etat['paralysie']['duree'] = 0;
			echo $joueur->get_nom().' se défait de la paralysie<br />';
			
		}
		else
		{
			$attaque->add_log_combat('cp');
			$test = false;
			return '';
			
		}
	}
	if($joueur->etat['etourdit'] > 0)
	{
		echo $joueur->get_nom().' est étourdi<br />';
		$attaque->add_log_combat('ce');
		$test = false;
		return '';
	}
	if($joueur->etat['glace'] > 0)
	{
		print_debug($joueur->get_nom().' est glacé<br />');
		$test = true;
	}
	if($joueur->etat['tir_vise'] > 0)
	{
		echo $joueur->get_nom().' décoche une terrible flèche<br />';
		$effectue[0] = 'attaque';
		$test = false;
		return $effectue;
	}
	if ($test)
	{
	  //var_dump($joueur->etat['glacer']);
		if($joueur->etat['glacer'] > 0)
		{
		  // On regarde si le personnage est glacé
			$rand = rand(1, 100);
			$cible = 20 + (($joueur->etat['glacer']['effet'] - 1) * 10);
			//echo $cible.' chance de glacer / 100. Résultat : '.$rand.'<br />';
			if($rand < $cible)
			{
				echo $joueur->get_nom().' est glacé<br />';
				$attaque->add_log_combat('cg');
				return '';
				$stop = true;
			}
		}
		if($joueur->etat['silence'] > 0)
		{
			echo $joueur->get_nom().' est sous silence<br />';
			$attaque->add_log_combat('cs');
			$effectue[0] = 'attaque';
			return $effectue;
		}
		if(!$stop)  // Etrange : il y a un "return" juste avant le seul moment où stop est mis à "true".
		{
		  // Récupèration des actions du personnage
			$actions = explode(';', $joueur->get_action());
			$count = count($actions);
			$action = false;
			$i = 0;
			//my_dump($actions);
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
								$param = $joueur->get_hp();
							break;
							//Réserve de mana du joueur
							case '01' :
								$param = $joueur->get_rm_restant();
							break;
							//Round
							case '09' :
								$param = $round;
							break;
							//Etat du joueur
							case '10' :
								$param = $joueur->etat;
							break;
							//Etat de l'adversaire
							case '11' :
								$param = $ennemi->etat;
							break;
							//Etat du joueur
							case '12' :
								$param = $joueur->etat;
							break;
							//Etat de l'adversaire
							case '13' :
								$param = $ennemi->etat;
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
								$param = $joueur->anticipation[$use][substr($solution, 1)];
							break;
							//Dernière action
							case '15' :
                switch($valeur)
                {
                case 'E':
                  $param = $joueur->precedent['esquive'];
                  break;
                case 'C':
                  $param = $joueur->precedent['critique'];
                  break;
                case 'B':
                  $param = $joueur->precedent['bloque'];
                  break;
                case 'T':
                  $param = $joueur->precedent['touche'];
                  break;
                }
								$valeur = true;
						}
						//echo $param.' '.$operateur.' '.$valeur.'<br />';
						// Vérification de la condition
            //echo 'test : '.$param.' '.$operateur.' '.$valeur.'<br/>';
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
							// Vérification si le personnage n'est pas dans un certain état
							case '°' :
								$array_valeurs = get_array_condition($valeur);
								foreach ($array_valeurs as $la_valeur)
								{
									if(array_key_exists($la_valeur, $param))
									{
										$valid = false;
										break;
									}
									else
									{
										$valid = true;
									}
								}
								break;
							// Vérification si le personnage est dans un certain état
							case '+' :
                				$array_valeurs = get_array_condition($valeur);
                				foreach ($array_valeurs as $la_valeur)
                				{
                  					if(!array_key_exists($la_valeur, $param))
                  					{
                    					$valid = false;
                  					}
                  					else
                  					{
                    					$valid = true;
                    					break;
                  					}
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
							$mp_need = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
							if($joueur->get_type() == "pet") $mp_need = $row['mp'];
							// Appel des ténebres
							if($joueur->etat['appel_tenebre']['duree'] > 0)
							{
								$mp_need += $joueur->etat['appel_tenebre']['effet'];
							}
							// Appel de la forêt
							if($joueur->etat['appel_foret']['duree'] > 0 && $mp_need > 1)
							{
								$mp_need -= $joueur->etat['appel_foret']['effet'];
								if($mp_need < 1) $mp_need = 1;
							}

              /* Application des effets de mana */
              /*foreach ($effects as $effect)
                $mp_need = $effect->calcul_mp($actif, $mp_need);*/
              $attaque->applique_effet('calcul_mp');
              /* ~Mana */

							// Si le joueur a assez de reserve on indique l'action à effectuer
							if($joueur->get_rm_restant() >= $mp_need)
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
							if($joueur->etat['appel_tenebre']['duree'] > 0)
							{
								$mp_need += $joueur->etat['appel_tenebre']['effet'];
							}
							//Appel de la forêt
							if($joueur->etat['appel_foret']['duree'] > 0)
							{
								$mp_need -= $joueur->etat['appel_foret']['effet'];
								if($mp_need < 1) $mp_need = 1;
							}
							
              /* Application des effets de mana */
              /*foreach ($effects as $effect)
                $mp_need = $effect->calcul_mp($actif, $mp_need);*/
              $attaque->applique_effet('calcul_mp');
              /* ~Mana */

							// On vérifie que le personnage a assez de MP
							if($joueur->get_rm_restant() >= $mp_need)
							{
								// Si l'arme utilisée est la bonne on indique l'action à effectuer
								$arme_requis = explode(';', $row['arme_requis']);
								if(in_array($joueur->get_arme_type(), $arme_requis) OR in_array($joueur->get_bouclier_type(), $arme_requis) OR $row['arme_requis'] == '')
								{
									$effectue[0] = 'lance_comp';
									$effectue[1] = $id_sort;
									$action = true;
								}
							}
							//else { echo "mana inssufisant : $mp_need vs ".$joueur->get_reserve(); }
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
			if(array_key_exists('anticipation', $joueur) AND array_key_exists($effectue[0], $joueur->anticipation) AND array_key_exists($id, $joueur->anticipation[$effectue[0]]) AND $joueur->anticipation[$effectue[0]][$id] > 0)
			{
				// On récupère le nombre d'utilisations et calcul des chances de réussite
				$nbr_utilisation = $joueur->anticipation[$effectue[0]][$id];
				$chance_reussite = 100 - ($nbr_utilisation * $nbr_utilisation);
				// Si l'adversaire est de niveau < 5, alors il a moins de chances d'anticiper
				if($ennemi->get_level() < 5)
				{
					$chance_reussite = 100 - ( (100 - $chance_reussite) / (6 - $ennemi->get_level()) );
				}
				// Réduction des chances d'anticiper si adversaire glacé (avec une orbe de glace)
				if($ennemi->etat['glace_anticipe']['duree'] > 0) $chance_reussite = $chance_reussite + $ennemi->etat['glace_anticipe']['effet'];
				// Réduction des chances d'anticiper si adversaire amorphe
				if($joueur->is_buff('maladie_amorphe')) $chance_reussite = $chance_reussite - $joueur->get_buff('maladie_amorphe', 'effet');
				// item donjon
				if ($ennemi->get_type() == 'joueur')
				{
				$joueur2 = new perso($ennemi->get_id());
				$item = $joueur2->get_inventaire_partie('tete');
				if ($item != '') $objet_t = decompose_objet($item);		
				if ($objet_t !='')
				{
					$requete = "SELECT * FROM armure WHERE ID = ".$objet_t['id_objet'];
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$effet = explode('-', $row['effet']);
					if ($effet[0] == '21')
					{
						$chance_reussite -= $effet[1];
					}
				}
				}
				
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
					echo $ennemi->get_nom().' anticipe l\'attaque, et elle échoue !<br />';
					$attaque->add_log_combat('a');
					return '';
				}
			}
			//On incrémente l'anticipation de la compétence de l'attaque classique ou du sort.
			$joueur->anticipation[$effectue[0]][$id]++;
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
function lance_sort($id, $acteur, &$effects)
{
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $Trace, $G_buff, $G_debuff, $G_round_total, $log_combat;
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

	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
	
	//Réctification si c'est un orc ou un donjon
	$round = is_donjon($actif->get_x(), $actif->get_y()) ? $G_round_total * 2 : $G_round_total;
	if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
		$round += 1;
	$rectif_augm = $round / $G_round_total;

  /* Application des effets de début de round */
  foreach ($effects as $effect) $effect->debut_round($actif, $passif);
  /* ~Debut */

	//Recherche du sort
	$requete = "SELECT * FROM sort_combat WHERE id = ".$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

  // Calcul de MP nécessaires
	$mp_need = round($row['mp'] * (1 - (($Trace[$actif->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
	if($actif->get_type() == "pet") $mp_need = $row['mp'];
	//Appel des ténebres
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

  /* Application des effets de mana */
  foreach ($effects as $effect)
    $mp_need = $effect->calcul_mp($actif, $mp_need);
  /* ~Mana */

	//Suppresion de la réserve
	$actif->set_rm_restant($actif->get_rm_restant() - $mp_need);

  // Calcul du potentiel magique
	$get = 'get_'.$row['comp_assoc'];
	$potentiel_magique = floor($actif->get_incantation() + 1.9 * $actif->$get());
	if($passif->is_buff('batiment_incantation')) $potentiel_magique *= 1 + (($passif->get_buff('batiment_incantation', 'effet') / 100));
	if($actif->is_buff('buff_meditation')) $potentiel_magique *= 1 + (($actif->get_buff('buff_meditation', 'effet') / 100));
	if(array_key_exists('lien_sylvestre', $actif->etat)) $potentiel_magique /= 1 + (($actif->etat['lien_sylvestre']['effet2']) / 100);
	if(array_key_exists('fleche_debilitante', $actif->etat)) $potentiel_magique /= 1 + ($actif->etat['fleche_debilitante']['effet'] / 100);
	if($actif->etat['posture']['type'] == 'posture_feu') $potentiel_magique *= 1 + (($actif->etat['posture']['effet']) / 100);
	if($actif->get_arme_type() == 'baton')
  {
    $arme = $actif->get_arme();
    $potentiel_magique_arme = $potentiel_magique * (1 + ($arme->var1 / 100));
    $facteur_degats_arme = 0;
  }
	else
  {
    $potentiel_magique_arme = $potentiel_magique;
    $facteur_degats_arme = 0;
  }

  /* Application des effets de début de round */
  foreach ($effects as $effect) $effect->debut_round($actif, $passif);
  /* ~Debut */

	//Objets magiques
	foreach($actif->objet_effet as $effet)
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
			$pm = $passif->get_pm();

			/* Application des effets de PM */
			foreach ($effects as $effect)
				$pm = $effect->calcul_pm($actif, $passif, $pm);
			/* ~PM */

			if($passif->is_buff('batiment_pm')) $buff_batiment_barriere = 1 + (($passif->get_buff('batiment_pm', 'effet') / 100)); else $buff_batiment_barriere = 1;
			if($passif->is_buff('debuff_desespoir')) $debuff_desespoir = 1 + (($passif->get_buff('debuff_desespoir', 'effet')) / 100); else 	$debuff_desespoir = 1;
			if($passif->etat['posture']['type'] == 'posture_glace') $aura_glace = 1 + (($passif->etat['posture']['effet']) / 100); else $aura_glace = 1;
			$PM = $pm * $aura_glace * $buff_batiment_barriere;
			// Calcul des potentiels toucher et parer
			$potentiel_toucher = round($actif->get_volonte() * $potentiel_magique);
			$potentiel_parer = round($passif->get_volonte() * $PM / $debuff_desespoir);

			/* Application des effets de potentiel toucher */
			foreach ($effects as $effect)
				$potentiel_toucher =
					$effect->calcul_attaque_magique($actif, $passif, $potentiel_toucher);
			/* ~Potentiel Toucher */
			
			/* Application des effets de potentiel parer */
			foreach ($effects as $effect)
				$potentiel_parer =
					$effect->calcul_defense_magique($actif, $passif, $potentiel_parer);
			/* ~Potentiel Parer */

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

			$bonus_degats_magique = 0;
			/* Application des effets de degats magiques */
			foreach ($effects as $effect)
				$bonus_degats_magique =
					$effect->calcul_bonus_degats_magiques($actif, $passif,
                                                $bonus_degats_magique,
                                                $row['type']);
			/* ~degats magiques */
			$bonus_degats_magique += $facteur_degats_arme;
			$bonus_degats_magique += $actif->get_buff('buff_surpuissance', 'effet');

			$get_comp_assoc = 'get_'.$row['carac_assoc'];
			switch($row['type'])
			{

          /***************************************/
          /****       Les sorts spéciaux      ****/
          /***************************************/

			case 'debuff_enracinement':
        $degat = degat_magique($actif->$get_comp_assoc(), $bonus_degats_magique, $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);

					$d_attaque = rand(1, $row['effet2']);
					$d_defense = rand(1, $passif->get_dexterite() + $passif->get_force());
					print_debug("enracinement: $d_attaque vs $d_defense");
					if ($d_attaque > $d_defense) {
						echo '<strong>'.$passif->get_nom().
							'</strong> est affecté par le debuff '.$row['nom'].'<br/>';
						lance_buff($row['type'], $passif->get_id(),
											 $row['effet'], '0', $row['effet'] * 60, $row['nom'],
											 sSQL($row['description']), 'perso', 1, 0, 0, 0);
					}
          break;

			case 'heresie_divine':
					$degat = degat_magique($actif->$get_comp_assoc(), $row['effet'] + $bonus_degats_magique, $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);

          echo '<strong>'.$passif->get_nom().
            '</strong> est affecté par le debuff '.$row['nom'].'<br/>';
          lance_buff('debuff_antirez', $passif->get_id(),
                     $row['effet'], $row['effet2'],
                     $row['effet2'] * 3600, $row['nom'],
                     sSQL($row['description']), 'perso', 1, 0, 0, 0);
          break;

			case 'encombrement_psy':
					$degat = degat_magique($actif->$get_comp_assoc(), $row['effet'] + $bonus_degats_magique, $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);

					if ($passif->get_type() == 'joueur') {
						echo '<strong>'.$passif->get_nom().
							'</strong> est affecté par le debuff '.$row['nom'].'<br/>';
						lance_buff('debuff_charisme', $passif->get_id(),
											 8, $row['effet2'], $row['effet2'] * 86400, $row['nom'],
											 sSQL($row['description']), 'perso', 1, 0, 0, $row['effet']);
					}
          break;

			case 'tsunami_drain':
			case 'tsunami':
					$degat = degat_magique($actif->$get_comp_assoc(),
																 $row['effet'] + $bonus_degats_magique,
																 $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().
						'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.
						$row['nom'];
					$passif->set_hp($passif->get_hp() - $degat);
					if ($row['type'] == 'tsunami_drain') {
						// Si drain: gain de min(degats, pv manquants)
						$drain = min($degat, ($actif->get_hp_max() - $actif->get_hp()));
						$actif->set_hp($actif->get_hp() + $drain);
						echo 'Et gagne <strong>'.$drain.'</strong> hp grâce au drain';
					}
					echo '</span><br />';
          
          projection($actif, $passif, $row['effet2']);
          break;

			  case 'empalement_abomination':
					$degat = degat_magique($actif->$get_comp_assoc(),
																 ($row['effet'] + $bonus_degats_magique),
																 $actif, $passif, $effects, $row['type']);
					if ($passif->get_hp() > $degat) { // Si on survit
						$degat = $passif->get_hp() - 4; // 1 + 3 de LS
          }
					echo '&nbsp;&nbsp;<span class="degat">Une &eacute;pine jaillit de <strong>'.
						$actif->get_nom().'</strong> infligeant <strong>'.$degat.
						'</strong> dégâts, et transpercant '.$passif->get_nom().
						'</span><br/>';
					$passif->set_hp($passif->get_hp() - $degat);

					if ($passif->get_hp() > 0) {
						// On augmente d'un la marque de l'abomination
						$achiev = $passif->get_compteur('abomination_mark');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}

			  case 'cri_abomination':
					echo '&nbsp;&nbsp;<span class="degat">L\'abomination profère un hurlement terrifiant !</span><br/>';
					$xi = $passif->get_x() - 3;
					$xa = $passif->get_x() + 3;
					$yi = $passif->get_y() - 3;
					$ya = $passif->get_y() + 3;
					$requete_persos = "select id from perso where x >= $xi and x <= $xa and y >= $yi and y <= $ya and hp > 0 and statut = 'actif'";
					$req_persos = $db->query($requete_persos);
					while ($row_persos = $db->read_assoc($req_persos))
					{
						if ($row_persos['id'] == $passif->get_id())	continue;
						$spectateur = new perso($row_persos['id']);
						$rand = rand(0, 20);
						$final = $rand + $spectateur->get_volonte();
						print_debug("Jet de terreur pour ".$spectateur->get_nom().": $rand ($final) vs $row[effet2]<br/>");
						if ($final < $row['effet2'] && $rand != 20)
						{
							echo '<strong>'.$spectateur->get_nom().'</strong> est effray&eacute; par ce spectacle, et se glace de terreur !<br/>';
							lance_buff('debuff_enracinement', $row_persos['id'], '10', '0', 86400, 'Terreur',
												 'Vous etes terroris&eacute; par l\'affreux spectacle du supplice de '.$passif->get_nom(), 'perso', 1, 0, 0, 0);
						}
					}
					echo 'La marque de l\'abomination restera longtemps sur vous ...<br/>';
					$achiev = $passif->get_compteur('abomination_mark');
					if ($achiev->get_compteur() == 0) {
						// Premier combat contre l'abomination
						$achiev->set_compteur(1);
						$achiev->sauver();
					}

					if ($passif->get_hp() > 3)
						lance_buff('debuff_enracinement', $passif->get_id(), '10', '0', 86400, 'Terreur',
											 'Vous etes terroris&eacute; par l\'attaque de la cr&eacute;ature', 'perso', 1, 0, 0, 0);
					lance_buff('lente_agonie', $passif->get_id(), 1, 0, 2678400, 'Marque de l\\\'abomination', 
										 'Les blessures engendrées par l\'épine de l\'abomination vous laissent dans une souffrance atroce. Il vous faudra du temps pour vous en remettre',
										 'perso', 1, 0, 0, 0);
					break;

        case 'nostalgie_karn':
          $description = 'Vous sentez votre esprit vieillir, vous ne pensez quʼaux moments où vous étiez en pleine santé et vous avez du mal a vous concentrer';
					$degat = degat_magique($actif->$get_comp_assoc(),
                                 ($row['effet'] + $bonus_degats_magique),
                                 $actif, $passif, $effects, $row['type']);
          lance_buff('maladie_degenerescence', $passif->get_id(),
                     $row['effet2'], '0', 2678400, 'Nostalgie de Karn',
                     sSQL($description), 'perso', 1, 0, 0, 0);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
          echo '<br/><em>'.$description.'</em>';
          break;

					
        case 'absorb_temporelle':

          $description = 'Vous êtes complétement déstabilisé et ne voyez plus rien pendant quelques secondes. En revenant à vous, vous avez la douloureuse impression que vos gestes vous ont échappé.';

          $perte_pa = rand(1, $row['effet2']);
          $pa = max(0, $passif->get_pa() - $perte_pa);
          $passif->set_pa($pa);
          $degat = degat_magique($actif->$get_comp_assoc(),
                                 ($row['effet'] + $bonus_degats_magique),
                                 $actif, $passif, $effects, $row['type']);
          echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().
            '</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.
            $row['nom'].'</span><br />';
          $passif->set_hp($passif->get_hp() - $degat);
          print_debug($passif->get_nom().' perd '.$perte_pa.' PA');
          
          echo '<br/><em>'.$description.'</em>';
          break;
          
        case 'debuff_nysin_femelle':
          // Si le joueur est déjà entravé, on ne change rien mais on tape
          if ($passif->is_buff('debuff_bloque_deplacement_alea'))
          {
            $degat = degat_magique($actif->$get_comp_assoc(),
                                   (($row['effet'] * 4) + $bonus_degats_magique),
                                   $actif, $passif, $effects, $row['type']);
            if (is_bloque_Deplacement_alea(
                  $passif->get_buff('debuff_bloque_deplacement_alea', 'effet'),
                  $passif->get_buff('debuff_bloque_deplacement_alea', 'effet2'))) {
              // Si les entraves sont actives, on tape 4 fois plus fort
              $degat *= 4;
              echo '<em>Les entraves de Nysin sont actives !</em><br/>';
            }
            echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().
              '</strong> inflige <strong>'.$degat.'</strong> dégâts à '.
              'travers les entraves de Nysin</span><br />';
            break;
          }
          $debut = rand(0, 23);
          $fin = $debut + $row['effet'];
          if ($fin > 24) $fin -= 24;
          $description = "Vous êtes entravé par Nysin";
          lance_buff('debuff_bloque_deplacement_alea', $passif->get_id(),
                     $debut, $row['effet'], 604800, 'Entraves de Nysin',
                     sSQL($description), 'perso', 1, 0, 0, 0);
          echo '<br/><em>'.$description.'</em>';          
          break;

          /***************************************/
          /****        Les sorts normaux      ****/
          /***************************************/
  			case 'degat_feu' : /* Les 3 c'est pareil une fois tellurique  */
	  		case 'degat_nature' :
		  	case 'degat_mort' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
				break;
				case 'degat_froid' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					// On regarde si la cible est glacé
					/*$chances = rand(0, ($row['effet2'] * 10));
					$tirage = rand(0, 100);
					if($chances > $tirage)
					{*/
						$passif->etat['glacer']['effet'] = $row['effet2'];
						$passif->etat['glacer']['duree'] = $row['duree'];
					//}
				break;
				case 'degat_vent' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					// On regarde s'il y a un gain de PA
					$cap = $row['effet2'];
					$de = rand(0, 100);
					// Affichage des informations de debug
					print_debug('1d100 doit être inférieur a '.$row['effet2'].'<br />
						Résultat => '.$de.' doit être inférieur a '.$row['effet2'].'<br />');
					if($de < $cap)
					{
						echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> gagne 1 PA<br />';
						$actif->set_pa($actif->get_pa() + 1);
					}
					$passif->set_hp($passif->get_hp() - $degat);
				break;
				case 'sacrifice_morbide' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> se suicide et inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$actif->set_hp(0);
				break;
				case 'degat_terre' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$actif->etat['tellurique']['effet'] += $row['effet2'];
					$actif->etat['tellurique']['duree'] = $row['duree'];
					$passif->set_hp($passif->get_hp() - $degat);
				break;
				case 'lapidation' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					// On regarde si la cible est étourdie
					$chances = $row['effet2'];
					$tirage = rand(0, 100);
					if($tirage < $chances)
					{
						$passif->etat['etourdit']['effet'] = 1;
						$passif->etat['etourdit']['duree'] = $row['duree'];
					}
				break;
				case 'globe_foudre' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);

					// On ajoute pas a la stack d'effet car on a besoin de savoir
					// tout de suite si la foudre passe ou pas pour le +1 degats
					$foudre = new globe_foudre(15, true);
					if ($foudre->magnetise($actif, $passif) == false)
						$degat++;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
				break;
				case 'pacte_sang' :
					$cout_hp = ceil($actif->get_hp_max() * $row['effet2'] / 100);
					// On vérifie que le personnage a assez de HP
					if($cout_hp < $actif->get_hp())
					{
						$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
						echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
						$passif->set_hp($passif->get_hp() - $degat);
						$actif->set_hp($actif->get_hp() - $cout_hp);
					}
					else
					{
					   // S'il n'a pas assez de HP il ne fait rien
					}
				break;
				case 'drain_vie' :
					$degat = degat_magique(($actif->$get_comp_assoc() - 2), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					if ($passif->get_type() == 'batiment') $drain = 0;
					else $drain = round($degat * 0.3);
					// Augmentation du nombre de HP récupérable par récupération
					if(array_key_exists('recuperation', $actif->etat)) $actif->etat['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$actif->set_hp($actif->get_hp() + $drain);
					// On vérifie que le personnage n'a pas plus de HP que son maximum
					if($actif->get_hp() > floor($actif->get_hp_max())) $actif->set_hp($actif->get_hp_max());
				break;
				case 'vortex_vie' :
					$degat = degat_magique(($actif->$get_comp_assoc() - 2), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					if ($passif->get_type() == 'batiment') $drain = 0;
					else $drain = round($degat * 0.4);
					// Augmentation du nombre de HP récupérable par récupération
					if(array_key_exists('recuperation', $actif->etat)) $actif->etat['recuperation']['hp_max'] += $drain;
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$actif->set_hp($actif->get_hp() + $drain);
					// On vérifie que le personnage n'a pas plus de HP que son maximum
					if($actif->get_hp() > floor($actif->get_hp_max())) $actif->set_hp($actif->get_hp_max());
				break;
				case 'vortex_mana' :
					$degat = degat_magique(($actif->$get_comp_assoc() - 2), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					$drain = round($degat * 0.2);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'<br />
					Et gagne <strong>'.$drain.'</strong> RM grâce au drain</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$actif->set_rm_restant($actif->get_rm_restant() + $drain);
				break;
				case 'putrefaction' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$passif->etat['putrefaction']['duree'] = 1;
					$passif->etat['putrefaction']['effet'] = 2;
				break;
				case 'brisement_os' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					if($passif->etat['paralysie']['duree'] > 0) $degat = round($degat * 1.6);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
				break;
				case 'embrasement' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$passif->etat['embraser']['duree'] = 5;
					$passif->etat['embraser']['effet'] = 1;
				break;
				case 'sphere_glace' :
					$degat = degat_magique($actif->$get_comp_assoc(), ($row['effet'] + $bonus_degats_magique), $actif, $passif, $effects, $row['type']);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$passif->etat['glace_anticipe']['duree'] = 3;
					$passif->etat['glace_anticipe']['effet'] = 10;
				break;
				case 'brulure_mana' :
					$brule_mana = $row['effet'];
					$degat = $row['effet'] * $row['effet2'];
          $degat += $bonus_degats_magique;
					// On regarde si c'est un critique et si oui on modifie les dégât en conséquence
					if(critique_magique($actif, $passif))
					{
						$degat = degat_critique($actif, $passif, $degat);
						$degat += $bonus_degats_magique;
					}
					// Diminution des dégâts grâce à l'armure magique
					$reduction = calcul_pp(($PM * $passif->get_puissance()) / 12);
					$degat = round($degat * $reduction);
					echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> retire '.$brule_mana.' réserve de mana et inflige <strong>'.$degat.'</strong> dégâts avec '.$row['nom'].'</span><br />';
					$passif->set_hp($passif->get_hp() - $degat);
					$passif->set_rm_restant($passif->get_rm_restant() - $brule_mana);
				break;
				case 'appel_tenebre' :
					$passif->etat['appel_tenebre']['effet'] = $row['effet'];
					$passif->etat['appel_tenebre']['duree'] = 10;
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'.<br />';
				break;
				case 'appel_foret' :
					$actif->etat['appel_foret']['effet'] = $row['effet'];
					$actif->etat['appel_foret']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'.<br />';
				break;
				case 'benediction' :
					$actif->etat['benediction']['effet'] = $row['effet'];
					$actif->etat['benediction']['duree'] = 10;
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se lance le sort '.$row['nom'].'<br />';
				break;
				case 'paralysie' :
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
						echo ' et réussit !<br />';
						$passif->etat['paralysie']['effet'] = $row['effet'];
						$passif->etat['paralysie']['duree'] = $row['effet'];
					
				break;
				case 'silence' :
					// Calcul du potentiel paralyser
					$sm = ($actif->get_volonte() * $actif->get_sort_mort());
					
					// Calcul du potentiel résister
					$pm = $passif->get_pm_para();

					// On utilise bien la PM DE BASE pour le 3eme jet

					$pm = $passif->get_volonte() * $pm;
					// Lancer des dés
					$att = rand(0, $sm);
					$def = rand(0, $pm);
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
					if($att > $def)
					{
						echo ' et réussit !<br />';
						$passif->etat['silence']['effet'] = $row['effet'];
						$passif->etat['silence']['duree'] = $row['duree'];
					}
					else
					{
						echo ' et échoue...<br />';
					}
					echo '<div class="debug" id="debug'.$debugs++."\">Potentiel silencer : $sm<br />Potentiel résister : $pm<br />Résultat => Lanceur : $att | Défenseur $def<br /></div>";
				break;
				case 'lien_sylvestre' :
					$passif->etat['lien_sylvestre']['effet'] = $row['effet'];
					$passif->etat['lien_sylvestre']['effet2'] = $row['effet2'];
					$passif->etat['lien_sylvestre']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
				break;
				case 'poison' :
					$passif->etat['poison']['effet'] = $row['effet'];
					$passif->etat['poison']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$row['nom'].'<br />';
				break;
				case 'jet_acide' :
					$passif->etat['acide']['effet'] = $row['effet'];
					$passif->etat['acide']['effet2'] = $row['effet2'];
					$passif->etat['acide']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort jet d\'acide<br />';
				break;
				case 'recuperation' :
					$actif->etat['recuperation']['effet'] = $row['effet'];
					$actif->etat['recuperation']['duree'] = 11;
					$actif->etat['recuperation']['hp_max'] = $actif->get_hp();
					$actif->etat['recuperation']['hp_recup'] = 0;
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se lance le sort '.$row['nom'].'<br />';
				break;
				case 'aura_feu' :
					echo '&nbsp;&nbsp;Une enveloppe de feu entoure <strong>'.$actif->get_nom().'</strong> !<br />';
					$actif->etat['posture']['effet'] = $row['effet'];
					$actif->etat['posture']['duree'] = 20;
					$actif->etat['posture']['type'] = 'posture_feu';
				break;
				case 'aura_glace' :
					echo '&nbsp;&nbsp;Une enveloppe de glace entoure <strong>'.$actif->get_nom().'</strong> !<br />';
					$actif->etat['posture']['effet'] = $row['effet'];
					$actif->etat['posture']['duree'] = 20;
					$actif->etat['posture']['type'] = 'posture_glace';
				break;
				case 'aura_vent' :
					echo '&nbsp;&nbsp;Des tourbillons d\'air entourent <strong>'.$actif->get_nom().'</strong> !<br />';
					$actif->etat['posture']['effet'] = $row['effet'];
					$actif->etat['posture']['duree'] = 20;
					$actif->etat['posture']['type'] = 'posture_vent';
				break;
				case 'aura_pierre' :
					echo '&nbsp;&nbsp;De solides pierres volent autour de <strong>'.$actif->get_nom().'</strong> !<br />';
					$actif->etat['posture']['effet'] = $row['effet'];
					$actif->etat['posture']['duree'] = 20;
					$actif->etat['posture']['type'] = 'posture_pierre';
				break;
				case 'riposte_furtive' :
					$actif->etat['riposte_furtive']['effet'] = $row['effet'];
					$actif->etat['riposte_furtive']['effet2'] = $row['effet2'];
					$actif->etat['riposte_furtive']['duree'] = $row['duree'];
					echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().
            '</strong> lance le sort '.$row['nom'].'<br />';
				break;
			}
			$log_combat .= "~".$degat;
      if ($degat > 0) {
        /* Application des effets de degats magiques */
        foreach ($effects as $effect)
          $effect->inflige_degats_magiques($actif, $passif, $degat, $row['type']);
        /* ~Fin de round */
      }
		}
		else  // pas touché
		{
			echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible avec '.$row['nom'].'</span><br />';
			$log_combat .= "~m";
		}
	}
	else // lancer raté
	{
		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' rate le lancement de '.$row['nom'].'</span><br />';
		$log_combat .= "~l";
	}
	//Augmentation des compétences liées
	$get = 'get_'.$row['comp_assoc'];
	
	$augmentation['actif']['comp'][] = array($row['comp_assoc'], $rectif_augm * (4.2 * sqrt(pow($actif->$get(), 1.3) / ($row['difficulte'] / 4))));
	$augmentation['actif']['comp'][] = array('incantation', $rectif_augm * (5 * sqrt($actif->get_incantation() / ($row['difficulte'] / 2))));

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

	return $augmentation;
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
function lance_comp($id, $acteur, &$effects)
{
	if (file_exists('root.php'))
	include_once('root.php');
	global $attaquant, $defenseur, $db, $Gtrad, $debugs, $comp_attaque, $G_round_total, $ups, $log_combat;
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
	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
	//Réctification si c'est un orc
	$round = is_donjon($actif->get_x(), $actif->get_y()) ? 20 : 10;
	$rectif_augm = $actif->get_race() == 'orc' ? 2 - ($round / ($round + 1)) : 1;
	if($rectif_augm == 1)
		$rectif_augm = $passif->get_race() == 'orc' ? 2 - ($round / ($round + 1)) : 1;
		
	//Recherche de la compétence
	$requete = "SELECT * FROM comp_combat WHERE id = ".$id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

  // Calcul des MP nécessaires
	$mp_need = $row['mp'];
	//Appel des ténebres
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

  /* Application des effets de mana */
  foreach ($effects as $effect)
    $mp_need = $effect->calcul_mp($actif, $mp_need);
  /* ~Debut */

	//Suppresion de la réserve
	$actif->set_rm_restant($actif->get_rm_restant() - $mp_need);

	$comp_attaque = false;  // Indique si le personnage attaque se round-ci.
	$utilise_comp = $row['type'];
	//echo $row['type'];
	switch($row['type'])
	{
		case 'tir_precis' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 + ($row['effet'] / 100)));
			$comp_attaque = true;
		break;
		case 'oeil_faucon' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 + ($row['effet'] / 10)));
			$comp_attaque = true;
		break;
		case 'coup_puissant' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->degat_sup = $row['effet'];
			$comp_attaque = true;
		break;
		case 'coup_violent' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->degat_sup = $row['effet'];
			$comp_attaque = true;
		break;
		case 'coup_mortel' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 - ($row['effet'] / 100)));
			$actif->etat['coup_mortel']['effet'] = true;
			$actif->etat['coup_mortel']['effet2'] = $row['effet2'];
			$actif->etat['coup_mortel']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'coup_sournois' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->etat['coup_sournois']['effet'] = $row['effet'];
			$actif->etat['coup_sournois']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_vicieuse' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 - ($row['effet'] / 100)));
			$actif->degat_sup = $row['effet2'];
			$actif['attaque_vicieuse'] = $row['effet2'];
			$comp_attaque = true;
		break;
		case 'berzeker' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> passe en mode '.$row['nom'].' !<br />';
			$actif->etat['berzeker']['effet'] = $row['effet'];
			$actif->etat['berzeker']['duree'] = 10;
			$comp_attaque = false;
		break;
		case 'tir_vise' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se concentre pour viser !<br />';
			$actif->etat['tir_vise']['effet'] = $row['effet'];
			$actif->etat['tir_vise']['duree'] = 2;
			$comp_attaque = false;
			// Augmentation des compétences
			//$diff = 3 * $G_round_total / 10;
			$diff = (3.2 * $G_round_total / 5) * $rectif_augm; // Irulan: soyons cohérents
			//$diff = 0.0001; // TESTS
			$augmentation['actif']['comp'][] = array('distance', $diff);
			if($actif->get_arme_type() == 'arc' AND $actif->is_competence('maitrise_arc')) $maitrise_arc = 1 + ($actif->competences['maitrise_arc'] / 1000); else $maitrise_arc = 1;
			if($maitrise_arc != 1)
			{
				$augmentation['actif']['comp_perso'][] = array('maitrise_arc', $actif, 5 * $rectif_augm);
			}
		break;
		case 'fleche_etourdissante' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise une flêche étourdissante !<br />';
			$actif->degat_moins = $row['effet'];
			// On regarde si l'adversaire est étourdit
			$de_att = rand(0, (($actif->get_force() + $actif->get_dexterite()) / 2));
			$de_deff = rand(0, $passif->get_vie());
			if($de_att > $de_deff)
			{
				$actif->etat['fleche_etourdit']['effet'] = $row['effet'];
				$actif->etat['fleche_etourdit']['duree'] = 1;
			}
			$comp_attaque = true;
		break;
		case 'tir_puissant' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 - ($row['effet2'] / 100)));
			$actif->degat_sup = $row['effet'];
			$comp_attaque = true;
		break;
		case 'fleche_magnetique' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.
				$row['nom'].'<br />';
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
      		//$effects[] = new fleche_magnetique($row['effet2'], $row['effet']);
			/*$de_att = rand(1, $passif->get_vie());
			$de_deff = rand(1, 40);
			if($de_att <= $de_deff)
			{*/
	      		$actif->etat['fleche_magnetique_attaque']['effet'] = $row['effet'];
		  		$actif->etat['fleche_magnetique_attaque']['effet2'] = $row['effet2'];
			/*}
			else echo 'La flêche magnétique ne fera rien<br />';*/
		break;
		case 'fleche_sable' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
      		//$effects[] = new fleche_sable($row['effet'], $row['effet2'], $row['duree']);
      		$actif->etat['fleche_sable_attaque']['effet'] = $row['effet'];
      		$actif->etat['fleche_sable_attaque']['effet2'] = $row['effet2'];
      		$actif->etat['fleche_sable_attaque']['duree'] = $row['duree'];
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'fleche_poison' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
      		$effects[] = new fleche_poison($row['effet'], $row['effet2'], $row['duree']);
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'fleche_rapide' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
			$actif->etat['fleche_rapide']['effet'] = $row['effet'];
			$actif->etat['fleche_rapide']['duree'] = 1;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'fleche_sanglante' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
			$actif->etat['fleche_sanglante']['effet'] = $row['effet'];
			$actif->etat['fleche_sanglante']['duree'] = 1;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'fleche_debilitante' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
			$actif->etat['fleche_debilitante_attaque']['effet'] = $row['effet'];
			$actif->etat['fleche_debilitante_attaque']['duree'] = 3;
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'coup_bouclier' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> donne un coup de bouclier !<br />';
			$actif->etat['coup_bouclier']['effet'] = $row['effet'];
			$actif->etat['coup_bouclier']['effet2'] = $row['duree'];
			$actif->etat['coup_bouclier']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'slam' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilises SLAM !<br />';
			// On regarde si la compétence fait effet
			$de_att = rand(0, (($actif['force'] + $actif['dexterite']) / 2));
			$de_deff = rand(0, $passif['vie']);
			echo $de_att.' '.$de_deff.'<br />';
			if($de_att > $de_deff)
			{
				$passif->etat['etourdit']['effet'] = $row['effet'];
				$passif->etat['etourdit']['duree'] = 1;
			}
			$comp_attaque = true;
		break;
		case 'frappe_derniere_chance' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].' !<br />';
			$actif->etat['derniere_chance']['effet'] = $row['effet2'];
			$actif->etat['derniere_chance']['duree'] = 20;
			// Diminution de la PM
			$actif->set_pm($actif->get_pm() / (1 + ($actif->etat['derniere_chance']['effet'] / 100)));
			$actif->degat_sup = $row['effet'];
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_critique' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_critique';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_esquive' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_esquive';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_defense' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_defense';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_degat' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_degat';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_transperce' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_transperce';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_paralyse' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_paralyse';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'posture_touche' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> se met en '.$row['nom'].' !<br />';
			$actif->etat['posture']['effet'] = $row['effet'];
			$actif->etat['posture']['duree'] = 20;
			$actif->etat['posture']['type'] = 'posture_touche';
			//On prends en compte la bonne compétence
			$row['comp_assoc'] = $actif->get_comp_combat();
			$comp_attaque = true;
		break;
		case 'dissimulation' :
		  // On regarde si la dissimulation est réussie
			$bonus = 1;
			if ($actif->get_type() == 'joueur')
			{
			$joueur = new perso($actif->get_id());
			$objet_t = decompose_objet($joueur->get_inventaire_partie('dos'));		
			if ($objet_t != '' && $objet_t['id_objet'] != '')
			{
				$requete = "SELECT * FROM armure WHERE ID = ".$objet_t['id_objet'];
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$effet = explode('-', $row['effet']);
				if ($effet[0] == '24')
				{
					$bonus *= 1.5;
				}
			}
			}
			$att = rand(0, $actif->get_dexterite() * $actif->get_esquive() * $bonus);
			$def = rand(0, $passif->get_volonte() * ($passif->get_pm() * 2.5));
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> tente de se dissimuler...';
			if($att > $def)
			{
				echo ' et réussit !<br />';
				$actif->etat['dissimulation']['effet'] = $row['effet'];
				$actif->etat['dissimulation']['duree'] = 2;
			}
			else
			{
				echo ' et échoue...<br />';
			}
		break;
		case 'bouclier_protecteur' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> intensifie sa protection magique grace à son bouclier !<br />';
			$actif->etat['bouclier_protecteur']['effet'] = $row['effet'];
			$actif->etat['bouclier_protecteur']['duree'] = 10;
		break;
		case 'feinte' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->etat['a_toucher']['effet'] += $row['effet'];
			$actif->etat['a_toucher']['duree'] = 1;
			$actif->etat['a_critique']['effet'] += $row['effet2'];
			$actif->etat['a_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_cote' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->etat['a_toucher']['effet'] += $row['effet'];
			$actif->etat['a_toucher']['duree'] = 1;
			$actif->etat['a_c_bloque']['effet'] += $row['effet2'];
			$actif->etat['a_c_bloque']['duree'] = 1;
			$actif->etat['b_critique']['effet'] += $row['effet3'];
			$actif->etat['b_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_brutale' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->degat_sup += $row['effet'];
			$actif->etat['a_c_bloque']['effet'] += $row['effet2'];
			$actif->etat['a_c_bloque']['duree'] = 1;
			$actif->etat['b_critique']['effet'] += $row['effet3'];
			$actif->etat['b_critique']['duree'] = 1;
			$comp_attaque = true;
		break;
		case 'attaque_rapide' :
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> utilise '.$row['nom'].'<br />';
			$actif->etat['a_toucher']['effet'] += $row['effet'];
			$actif->etat['a_toucher']['duree'] = 1;
			$actif->etat['b_critique']['effet'] += $row['effet'];
			$actif->etat['b_critique']['duree'] = 1;
			$comp_attaque = true;
			break;
    case 'vol_a_la_tire' :
      $effects[] = new vol_a_la_tire($row['effet'], $row['effet2'], 0);
      $comp_attaque = true;
      break;
	  default:
			// On traite toutes les autres compétences, génériquement
			$actif->etat[$type]['effet'] = $row['effet'];
			$actif->etat[$type]['effet2'] = $row['effet2'];
			$actif->etat[$type]['effet3'] = $row['effet3'];
			$actif->etat[$type]['duree'] = $row['duree'];
			$actif->etat[$type]['level'] = $row['level'];
			$comp_attaque = ($G_cibles[$row['cible']] == 'Ennemi');
			//echo "lance: $row[nom]: $row[effet] <br/>";
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
	return $augmentation;
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
	global $debugs,$log_combat;  // Numéro des informations de debug.
	// Dé de critiques
	$chance = rand(0, 10000);
	// Calcule des chances de critique
	$actif_chance_critique = ($attaquant->get_volonte() * 50);
	if(array_key_exists('buff_furie_magique', $attaquant->buff)) $actif_chance_critique = $actif_chance_critique  * (1 + ($attaquant->get_buff('buff_furie_magique', 'effet') / 100));
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
		$log_combat .= '!';
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
	//Les dégâts des critiques sont diminués par la puissance
	$puissance = 1 + ($passif->get_puissance() * $passif->get_puissance() / 1000);
	$degat = ($degat * 2);
	$degat_avant = $degat;
	$degat = round($degat / $puissance);
	echo '(Réduction de '.($degat_avant - $degat).' dégâts critique par la puissance)<br />
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
	$etats['glace']['nom'] = 'Glacé';
	$etats['glace']['id'] = 'glace';
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
	$etats['embraser']['nom'] = 'Embrasé';
	$etats['embraser']['id'] = 'embraser';
	$etats['desarme']['nom'] = 'Désarmé';
	$etats['desarme']['id'] = 'desarme';
	return $etats;
}

function projection(&$actif, &$passif, $effet)
{
  global $db;

  // Choose direction: 1->N 2->E 3->S 4->W
  $ax = $actif->get_x();
  $ay = $actif->get_y();
  $px = $passif->get_x();
  $py = $passif->get_y();
  if ($ax == $px && $ay == $py) $direction = rand(1, 4);
  elseif ($ax == $px) $direction = ($ay < $py) ? 1 : 3;
  elseif ($ay == $py) $direction = ($ax < $px) ? 2 : 4;
  else {
    $p = array();
    if ($ay > $py) $p[] = 3;
    if ($ay < $py) $p[] = 1;
    if ($ax > $px) $p[] = 4;
    if ($ax < $px) $p[] = 2;
    shuffle($p);
    $direction = array_pop($p);
  }

  print_debug("projection vers: $direction");
  $translation = 0;
  $continue_projection = true;
  do
  {
    $cur = translation($px, $py, $direction);
    $map = $db->query_get_object("select * from map where x = $cur[x] and y = $cur[y]");
    if ($map) {
      $info = type_terrain($map->info);
      $pa = cout_pa($info[0], $passif->get_race());
    } else {
      print_debug("BORD DE CARTE !!");
      $pa = 50;
    }
    if ($pa > 49) {
      // Infranchissable: mur
      $continue_projection = false;
      echo '<span class="degat">&nbsp;&nbsp;'.$passif->get_nom().
        ' est projeté contre un mur et perds '.$effet.
        ' points de vie!<br/></span>';
      $passif->add_hp($effet * -1);
      continue;
    }
    else {
      $px = $cur['x'];
      $py = $cur['y'];
      print_debug("déplacement en: $px/$py");
      if ($translation++ > 1) $continue_projection = false;
    }
    $def = rand(1, $passif->get_force());
    $att = rand(1, 40 / $translation);
    print_debug("resistance à la projection: $def vs $att<br/>");
    if ($def > $att) $continue_projection = false;
  } while ($continue_projection);
  if ($translation > 0) {
    $joueur = $passif->get_objet();
    $joueur->set_x($px);
    $joueur->set_y($py);
    $joueur->sauver();
    print_reload_area('deplacement.php?deplacement=centre', 'centre');
    $row = $db->query_get_object("select * from map_monstre where x = $px and y = $py");
    if ($row) {
      $_SESSION['attaque_donjon'] = 'ok';
      print_js_onload("alert('Vous êtes projeté sur un monstre!'); ".
                      "envoiInfo('attaque.php?type=monstre&".
                      "id_monstre=$row->id', 'information')");
    }
  }
}

function translation($x, $y, $direction) 
{
  switch ($direction)
  {
    case 1:
      return array('x' => $x, 'y' => $y - 1);
      break;
    case 2:
      return array('x' => $x + 1, 'y' => $y);
      break;
    case 3:
      return array('x' => $x, 'y' => $y + 1);
      break;
    case 4:
      return array('x' => $x - 1, 'y' => $y);
      break;
  }
  return array('x' => $x, 'y' => $y);
}
?>
