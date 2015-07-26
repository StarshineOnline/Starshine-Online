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
		$att->set_cible(4);
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
		// Si la cible est l'adversaire, alors c'est foiré
		if( $att->get_cible() == comp_sort::cible_autre )
		{
			$attaque->get_interface()->special('cc', $joueur->get_nom(), $ennemi->get_nom());
			$attaque->add_log_combat('cc');
			return null;
		}
	}
	interf_debug::enregistre('action sélectionnée: '.$effectue[0].' -> '.$effectue[1]);
	return $att;
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
	global $db, $Trace, $debugs, $log_combat;
	$stop = false;
	$test = true;
	$round = $attaque->get_round();
	if($joueur->etat['paralysie'] > 0)
	{
		$attaque->get_interface()->special('cp', $joueur->get_nom());
		if (isset($joueur->etat['paralysie']['cpt']))
		{
			$joueur->etat['paralysie']['cpt']++;
		}
		else
		{
			$joueur->etat['paralysie']['cpt'] = 1;
		}
		$bonus_para = 1;
		if ($res_para = $joueur->get_bonus_permanents('resistance_para'))
		{
			interf_debug::enregistre('Ajuste la resistance à la paralysie de '.$res_para.'%');
      $bonus_para = 1 + $res_para / 100;
    }
		$resist_para = $bonus_para*((1+pow($joueur->get_pm_para(), 0.62))*pow($joueur->get_volonte(),1.6)) + ($joueur->etat['paralysie']['cpt']-1)*1000 + 500;
		$sm = ($ennemi->get_volonte() * $ennemi->get_sort_mort());
							
		$att = rand(0, $sm);
		$def = rand(0, $resist_para);
		if( comp_sort::test_potentiel($att, $def) )
		{
			unset($joueur->etat['paralysie']['cpt']);
			$joueur->etat['paralysie']['duree'] = 0;
			$attaque->get_interface()->special('dp', $joueur->get_nom());
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
		$attaque->get_interface()->special('ce', $joueur->get_nom());
		$attaque->add_log_combat('ce');
		$test = false;
		return '';
	}
	if($joueur->etat['glace'] > 0)
	{
		interf_debug::enregistre($joueur->get_nom().' est glacé');
		$test = true;
	}
	if($joueur->etat['tir_vise'] > 0)
	{
		$attaque->get_interface()->special('sv', $joueur->get_nom());
		$attaque->add_log_combat('sv');
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
				$attaque->get_interface()->special('cg', $joueur->get_nom());
				$attaque->add_log_combat('cg');
				return '';
				$stop = true;
			}
		}
		if($joueur->etat['silence'] > 0)
		{
			$attaque->get_interface()->special('cs', $joueur->get_nom());
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
              $attaque->set_type_degats('sort');
              $attaque->applique_effet('calcul_mp', $mp_need);
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
              $attaque->set_type_degats('comp');
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
				// 
				if($joueur->is_buff('potion_vitesse')) $chance_reussite *= 1 + $joueur->get_buff('maladie_amorphe', 'effet')/100;
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
        $attaque->applique_effet('anticipation', $chance_reussite);
				
				// On détermine si l'action est anticipée
				/*$rand = rand(0, 100);
				echo '
					<div id="debug'.$debugs.'" class="debug">
						Probabilité de réussir l\'anticipation : '.(100 - $chance_reussite).'%<br />
						Le résultat doit être supérieur à <b>'.$chance_reussite.'</b> pour anticipation.<br />
						Résultat : <b> '.$rand.'</b><br />
					</div>';
				$debugs++;*/
				// Echec
				if(/*$rand > $chance_reussite*/!comp_sort::test_de(100, $chance_reussite) ) 
				{
					$attaque->get_interface()->anticipe($ennemi->get_nom());
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
?>
