<?php // -*- mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

include(root.'haut_ajax.php');

if(array_key_exists('tri', $_GET)) $tris = $_GET['tri']; else $tris = 'favoris';

if(array_key_exists('type', $_GET))	$type_cible = $_GET['type'];
else $type_cible = 'joueur';

if(array_key_exists('lanceur', $_GET)) $type_lanceur = $_GET['lanceur'];
else $type_lanceur = 'joueur';

$joueur = new perso($_SESSION['ID']);

switch($type_cible)
{
	case 'joueur':
		if(array_key_exists('id_joueur', $_GET)) $perso = new perso($_GET['id_joueur']);
		else $perso = new perso($joueur->get_id());
			
		$perso->check_perso(false);
		$cible = $perso;
	break;
	case 'monstre':
		$map_monstre = new map_monstre($_GET['id_monstre']);
		$monstre = new monstre($map_monstre->get_type());
		$monstre->hp_max = $monstre->get_hp();
		$monstre->set_hp($map_monstre->get_hp());
		$monstre->x = $map_monstre->get_x();
		$monstre->y = $map_monstre->get_y();
		$cible = new entite('monstre', $monstre);
		$cible->set_id($map_monstre->get_id());
	break;
}

switch($type_lanceur)
{
	case 'joueur':
		$lanceur = $joueur;
		$possible_augmentation = true;
	break;
	case 'monstre':
		$lanceur = new pet($_GET['id_pet']);
		$possible_augmentation = false;
		// Check des spells du mob
		$monstre = new monstre($lanceur->get_id_monstre());
		$spells = explode(';', $monstre->get_sort_dressage());
		if (!in_array("s$_GET[ID]", $spells)) security_block(URL_MANIPULATION);
	break;
}
if($type_lanceur == 'joueur') include ('livre.php');
?>
<hr>
<?php
$lancement = false;
$buff = false;
$debuff = false;

if($joueur->get_groupe() != 0) $groupe_joueur = new groupe($joueur->get_groupe());

if (isset($_GET['ID']) && !$joueur->is_buff('bloque_sort'))
{
	$no_req = false;
	$sort = new sort_jeu($_GET['ID']);

	if ($type_lanceur == 'joueur') {
		// Check des spells du joueur
		$spells = explode(';', $joueur->get_sort_jeu());
		if (!in_array($_GET['ID'], $spells)) security_block(URL_MANIPULATION);
		// Check prérequis
		$prerequis = explode(';', $sort->get_requis());
		foreach ($prerequis as $requis) {
			$regs = array();
			if (mb_ereg('^classe:(.*)$', $requis, $regs)) {
				if ($regs[1] != mb_strtolower($perso->get_classe())) {
					print_debug("La classe $regs[1] est requise pour ce sort");
					$no_req = true;
				}
			}
			if (mb_ereg('^([0-9]+)$', $requis, $regs)) {
				if (!in_array($regs[1], explode(';', $joueur->get_sort_jeu()))) {
					print_debug("Il vous manque le sort $regs[1] pour lancer ce sort");
					$no_req = true;
				}
			}
		}
		if ($sort->get_incantation()*$joueur->get_facteur_magie() > $joueur->get_incantation() && $sort->get_special() == false) {
			print_debug("Il vous faut ".$sort->get_incantation()*$joueur->get_facteur_magie()." en incantation pour lancer ce sort");
			$no_req = true;
		}
	}

	$W_distance = calcul_distance_pytagore($cible->get_pos(), $joueur->get_pos());
	
	if ($no_req) {
		echo 'Vous n\'avez pas les pré-requis pour lancer ce sort !';
	}
	elseif($W_distance > $sort->get_portee()) {
		echo 'Vous êtes trop loin pour lancer ce sort !';
	}
  elseif($joueur->is_buff('petrifie'))
  {
  	echo 'Vous êtes pétrifié, vous ne pouvez pas lancer de sort.';
  }
	else
	{
		if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes')
			$groupe = true;
		elseif ($sort->get_cible() == 3) {
			$force_groupe = true;
			$groupe = false;
		}
		else $groupe = false;
		//Vérification que c'est un buff de groupe
		$sortpa_base = $sort->get_pa();
		$sortmp_base = $sort->get_mp();

		//Vérification que le joueur a le droit aux sorts de groupe
		if ($groupe && 
				!($joueur->is_competence('sort_groupe') ||
					$joueur->is_competence('sort_groupe_'.$sort->get_comp_assoc()) ||
					$type_lanceur == 'monstre'))
			security_block(URL_MANIPULATION, 'Sort de groupe non autorisé');
		
		// Pas d'affinité si c'est le pet qui lance le sort ou pour les sorts speciaux
		if($type_lanceur != "monstre" && $sort->get_special() == false)
		{
			$joueur->check_sort_jeu_connu($_GET['ID']);
			$sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
			$sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
			//Réduction du cout par concentration
			if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
			//Coût en MP * 1.5 si sort de groupe
			if($groupe) $sortmp = ceil($sortmp * 1.5);
		}
		else
		{
			$sortpa = $sortpa_base;
			$sortmp = $sortmp_base;
			if($groupe) $sortmp = ceil($sortmp * 1.5);
		}

		if ($joueur->is_buff('buff_contagion')) {
			if (mb_ereg('^maladie_', $sort->get_type())) {
				$contagion = $joueur->get_buff('buff_contagion');
				print_debug("réduction de coût par la contagion (depuis $sortpa/$sortmp)");
				$sortpa -= $contagion->get_effet();
				$sortmp -= $contagion->get_effet2();
				if ($sortpa < 1) $sortpa = 1;
				if ($sortmp < 0) $sortmp = 0;
				print_debug("-> $sortpa/$sortmp");
			}	
		}
		
		$action = false;
		if(isset($groupe_joueur) AND ($groupe OR $force_groupe))
		{
			$cibles = array();
			foreach($groupe_joueur->get_membre_joueur() as $membre)
			{
				//On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
				if($joueur->get_distance_pytagore($membre) <= 7) $cibles[] = $membre;
			}
		}
		else $cibles = array($cible);

		if($joueur->get_pa() < $sortpa) echo '<h5>Pas assez de PA</h5>';
		elseif($lanceur->get_mp() < $sortmp) echo '<h5>Pas assez de mana</h5>';
		elseif($lanceur->get_hp() <= 0) echo '<h5>Vous êtes mort</h5>';
		else
		{
			switch($sort->get_type())
			{
				case 'vie_pourcent' :
					$soin_total = 0;
					$action = false;
					foreach($cibles as $cible)
					{
						if ($cible->get_hp() <= 0) continue;
						$soin = floor($cible->get_hp_maximum() * 0.05);
						if($soin > (floor($cible->get_hp_maximum()) - $cible->get_hp()))
							$soin = floor($cible->get_hp_maximum()) - $cible->get_hp();
						if ($soin == 0) continue;
						$action = true;
						echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
						$soin_total += $soin;
						$cible->set_hp($cible->get_hp() + $soin);
						$cible->sauver();
								
						// Augmentation du compteur de l'achievement
						$achiev = $joueur->get_compteur('total_heal');
						$achiev->set_compteur($achiev->get_compteur() + $soin);
						$achiev->sauver();
						
								// Augmentation du compteur de l'achievement
						$achiev = $joueur->get_compteur('nbr_heal');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();

						if ($groupe)
						{
							//Insertion du soin de groupe dans le journal de la cible
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
							$db->query($requete);
						}
						else if($cible->get_id() != $joueur->get_id())
						{
							//Insertion du soin de groupe dans le journal de la cible
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
							$db->query($requete);
						}
					}

					if($action)
					{
						$lancement = true;
						//Insertion du soin de groupe dans le journal du lanceur
						if($groupe)
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'gsoin', '".$joueur->get_nom()."', 'groupe', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
						}
						else
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'soin', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
						}
						$db->query($requete);
					}

					if($groupe) $groupe_href = '&amp;groupe=yes';
					else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
					echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';

					break;
				case 'vie' :
					$soin_total = 0;
					foreach($cibles as $cible)
					{
						if($cible->get_hp() > 0)
						{
							if($cible->get_hp() < floor($cible->get_hp_maximum()))
							{
								$action = true;
								$de_degat_sort = de_soin($joueur->get_comp($sort->get_carac_assoc()), $sort->get_effet());
								$i = 0;
								$de_degat_sort2 = array();
								while($i < count($de_degat_sort))
								{
									$de_degat_sort2[$de_degat_sort[$i]] += 1;
									$i++;
								}
								$i = 0;
								$keys = array_keys($de_degat_sort2);
								while($i < count($de_degat_sort2))
								{
									if ($i > 0) echo ' + ';
									echo $de_degat_sort2[$keys[$i]].'D'.$keys[$i];
									$i++;
								}
								echo '<br />';
								$soin = 0;
								$i = 0;
								while($i < count($de_degat_sort))
								{
									$soin += rand(1, $de_degat_sort[$i]);
									$i++;
								}
								if($soin > (floor($cible->get_hp_maximum()) - $cible->get_hp())) $soin = floor($cible->get_hp_maximum()) - $cible->get_hp();
								echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
								$soin_total += $soin;
								
								$cible->set_hp($cible->get_hp() + $soin);
								$cible->sauver();
								
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('total_heal');
								$achiev->set_compteur($achiev->get_compteur() + $soin);
								$achiev->sauver();
								
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('nbr_heal');
								$achiev->set_compteur($achiev->get_compteur() + 1);
								$achiev->sauver();
								
								if($groupe)
								{
									//Insertion du soin de groupe dans le journal de la cible
									$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
									$db->query($requete);
								}
								else if($cible->get_id() != $joueur->get_id())
								{
									//Insertion du soin de groupe dans le journal de la cible
									$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
									$db->query($requete);
								}
							}
							else
							{
								echo 'La cible a toute sa vie<br />';
							}	
						}
						else
						{
							echo $cible->get_nom().' est mort.<br />';
						}
					}	
					if($action)
					{
						$lancement = true;
						//Insertion du soin de groupe dans le journal du lanceur
						if($groupe)
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'gsoin', '".$joueur->get_nom()."', 'groupe', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
						}
						else
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'soin', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
						}
						$db->query($requete);
					}
					if($groupe) $groupe_href = '&amp;groupe=yes'; else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
					echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
				break;
				case 'balance' :
					$nbr_membre = 0;
					$total_pourcent = 0;
					foreach($cibles as $cible)
					{
						if($cible->get_hp() > 0)
						{
							$total_pourcent += $cible->get_hp() / $cible->get_hp_max();
							$nbr_membre++;
						}
					}
					$pourcent = $total_pourcent / $nbr_membre;
					print_debug("équilibrage: $pourcent");
					foreach($cibles as $cible)
					{
						if($cible->get_hp() > 0)
						{
							$cible->set_hp(floor($cible->get_hp_max() * $pourcent));
							echo $cible->get_nom().' est équilibré à '.$cible->get_hp().
								' HP.<br />';
							$cible->sauver();
						}
						else
						{
							echo $cible->get_nom().' est mort.<br />';
						}
					}
					$cible = $joueur;
					$lancement = true;
				break;
				case 'body_to_mind' :
					if($joueur->get_hp() > $sort->get_effet())
					{
						$sorthp = $sort->get_effet();
						$sortmp = $sort->get_effet2() * -1;
						$sortmp_base = $sort->get_effet();
						if(($sortmp * -1) > (floor($joueur->get_mp_maximum()) - $joueur->get_mp())) $sortmp = (floor($joueur->get_mp_maximum()) - $joueur->get_mp()) * -1;
						echo 'Vous utilisez '.$sorthp.' HP pour convertir en '.($sortmp * -1).' MP<br />';
						$joueur->set_hp($joueur->get_hp() - $sorthp);
						if($sortmp < 0)
						{
							$lancement = true;
						}
						echo '<a href="sort.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
					}
					else	
					{
					echo 'Vous n\'avez pas assez de points de vie.';
					}
				break;
				case 'teleport' :
					if($joueur->get_hp() > 0)
					{
						foreach($cibles as $cible)
						{
							$cible->set_x($Trace[$joueur->get_race()]['spawn_x']);
							$cible->set_y($Trace[$joueur->get_race()]['spawn_y']);
							$cible->sauver();
							echo $cible->get_nom().' a été téléporté dans votre capitale.<br />';
						}
						$lancement = true;
						echo '<img src="image/pixel.gif" onLoad="envoiInfo(\'deplacement.php\', \'centre\');" />';
					}	
					else
					{
						echo 'Vous êtes mort.';
					}
				break;
				case 'repos_sage' :
					//On vérifie qu'il a pas déjà le debuff
					if(!$joueur->is_buff('repos_sage', true))
					{
						//Mis en place du debuff
						lance_buff($sort->get_type(), $joueur->get_id(), 1, 0, $sort->get_duree(), $sort->get_nom(), 'Vous ne pouvez plus attaquer ni lancer le sort repos du sage', 'perso', 1, 0, 0, 0);
						$sortmp_base = $sort->get_effet();
						$sortmp = $sort->get_effet() * -1;
						if($joueur->get_mp() > $joueur->get_mp_maximum()) $joueur->set_mp($joueur->get_mp_maximum());
						$joueur->sauver();
						$lancement = true;
					}
					else
					{
						echo 'Vous êtes déjà reposé';
					}
				break;
			case 'buff_critique' : case 'buff_evasion' : case 'buff_bouclier' : case 'buff_sacrifice' : case 'buff_inspiration' : case 'buff_force' : case 'buff_armure_glace' : case 'buff_barriere' : case 'buff_bouclier_sacre' : case 'buff_colere' : case 'buff_epine' : case 'buff_meditation' : case 'buff_rage_vampirique' : case 'buff_rapidite' : case 'buff_concentration' : case 'buff_furie_magique' : case 'buff_surpuissance' : case 'bouclier_feu' : case 'bouclier_terre' : case 'bouclier_eau' : case 'souffrance_extenuante' : case 'bulle_sanctuaire' : case 'bulle_dephasante' : case 'buff_contagion' :
					foreach($cibles as $cible)
					{
						//Mis en place du buff
						if(lance_buff($sort->get_type(), $cible->get_id(), $sort->get_effet(), $sort->get_effet2(), $sort->get_duree(), $sort->get_nom(), description($sort->get_description(), $sort), $type_cible == 'joueur' ? 'perso' : 'monstre', 0, count($cible->get_buff_only()), $cible->get_grade()->get_rang()))
						{
							$action = true;
							echo $cible->get_nom().' a bien reçu le buff<br />';
							//Insertion du buff dans le journal de la cible
							if($groupe)
							{
								$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgbuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, 0, 0)";
								$db->query($requete);
								
							}
							else if(($cible->get_id() != $joueur->get_id()) && $type_cible != 'monstre')
							{
								$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rbuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
							}
						}
						else
						{
							if($G_erreur == 'puissant') echo $cible->get_nom().' bénéficie d\'un buff plus puissant<br />';
							else echo $cible->get_nom().' a trop de buffs.<br />';
						}
					}
					if($action)
					{
						$lancement = true;
						$buff = true;
						if($groupe)
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'gbuff', '".$joueur->get_nom()."', 'groupe', NOW(), '".$sort->get_nom()."', 0, 0, 0)";
							$db->query($requete);
						}
						else
						{
							$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'buff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
							$db->query($requete);
						}
					}
				break;
				case 'engloutissement' : case 'deluge' : case 'blizzard' : case 'orage_magnetique' :
					//Récupération de tous les joueurs de la case
					$cibles = perso::create(array('x', 'y', 'statut'),
																	array($joueur->get_x(), $joueur->get_y(),
																				'actif'),
																	'id ASC', false, false);
					foreach($cibles as $cible)
					{
						//Si c'est pas le joueur
						if($cible->get_id() != $joueur->get_id())
						{
							//Test d'esquive du sort
							$protection = $cible->get_volonte() * $cible->get_pm() / 3;
							if($cible->is_buff('bulle_sanctuaire', true)) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
							if($cible->is_buff('bulle_dephasante', true)) $protection *= $cible->get_buff('bulle_dephasante','effet');
							$puissance = $joueur->get_volonte() * $joueur->get_comp($sort->get_comp_assoc());
							$attaque = rand(0, $puissance);
							$defense = rand(0, $protection);
							print_debug("Lance sort: $attaque ($puissance) vs $defense ($protection)");
							if ($attaque > $defense)
							{
								//Mis en place du debuff pour tous
								if(lance_buff($sort->get_type(), $cible->get_id(), $sort->get_effet(), $sort->get_effet2(), $sort->get_duree(), $sort->get_nom(), description($sort->get_description(),$sort), $type_cible == 'monstre' ? 'monstre' : 'perso', 1, 0, 0))
								{
									echo 'Le sort '.$sort->get_nom().' a été lancé avec succès sur '.$cible->get_nom().'<br />';
									//Insertion du debuff dans les journaux des 2 joueurs
									if($type_cible != 'monstre')
									{
										$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
										$db->query($requete);
										$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
										$db->query($requete);
									}
								}
								else
								{
									echo 'Il bénéficie d\'un debuff plus puissant.<br />';
								}
								//Suppression de MP pour orage magnétique
								if($sort->get_type() == 'orage_magnetique')
								{
									if($cible->is_buff('orage_magnetique', true))
									{
										echo $cible->get_nom().' est déjà sous cet effet.<br />';
									}
									else
									{
										//Réduction des mp de la cible
										$cible->set_mp($cible->get_mp() - ($cible->get_mp_maximum() * $sort->get_effet() / 100));
										if($cible->get_mp() < 0) $cible->set_mp(0);
										$cible->sauver();
									}
								}
							}
							else
							{
								echo $cible->get_nom().' résiste à votre sort !<br />';
							}
						}
					}
					$lancement = true;
				break;
				case "guerison" :
					$check = false;
					//-- Suppression d'un debuff au hasard
					foreach($cibles as $cible)
					{
						$debuff_tab = array();
						foreach($cible->get_buff() as $debuff)
						{
							if($debuff->get_debuff() == 1)
							{
								if($debuff->is_supprimable())
								{
									$debuff_tab[] = $debuff->get_id();
								}
							}
						}
						if(count($debuff_tab) > 0)
						{
							$requete = "DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";";
							$db->query($requete);
							$check = true;
						}
						else
						{
							echo "Impossible de lancer de lancer le sort. ".addslashes($cible->get_nom())." n&apos;a aucun debuff.<br/>";
						};
					}
					if($check)
					{
						$lancement = true;
					}
					$groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
					echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$_GET["ID"].$groupe_href."', 'information')\">Utiliser de nouveau cette compétence</a>";
				break;
				case "esprit_sacrifie" :	 //-- Esprit Sacrifié
					//-- Suppression d'un debuff au hasard
					if($joueur->is_buff())
					{
						$debuff_tab = array();
						$buff_tab = array();
						foreach($joueur->get_buff() as $buff)
						{
							if($buff->get_debuff() == 1)
							{
								if($buff->is_supprimable()) { $debuff_tab[] = $buff->get_id(); }
							}
							else
							{
								if($buff->is_supprimable()) { $buff_tab[] = $buff->get_id(); }
							}
						}	
						if(count($debuff_tab) == 0)
						{
							echo "Impossible de lancer le sort. Vous n&apos;avez aucun debuff.<br/>";
						}
						elseif (count($buff_tab) == 0)
						{
							echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucun buff.<br/>";
						}
						else
						{
							$lancement = true;
							$db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
							$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
						}
					}
					else { echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucun buff.<br/>"; };
					$groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
					echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utiliser de nouveau cette compétence.</a>";
				break;
				case 'debuff_aveuglement' : case 'debuff_enracinement' : case 'debuff_desespoir' : case 'debuff_ralentissement' : case 'lente_agonie' :
					//Test d'esquive du sort
					$protection = $cible->get_volonte() * $cible->get_pm() / 3;
					if($cible->is_buff('bulle_sanctuaire', true)) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
					if($cible->is_buff('bulle_dephasante', true)) $protection *= $cible->get_buff('bulle_dephasante','effet');
					$attaque = rand(0, ($joueur->get_volonte() * $joueur->get_comp($sort->get_comp_assoc())));
					$defense = rand(0, $protection);
					if ($attaque > $defense)
					{
						$duree = $sort->get_duree();
						if($joueur->is_buff('souffrance_extenuante', true)) $duree = $duree * $joueur->get_buff('souffrance_extenuante', 'effet');
						//Mis en place du debuff
						if(lance_buff($sort->get_type(), $cible->get_id(), $sort->get_effet(), $sort->get_effet2(), $duree, $sort->get_nom(), description($sort->get_description(), $sort), $type_cible == 'monstre' ? 'monstre' : 'perso', 1, 0, 0))
						{
							echo 'Le sort '.$sort->get_nom().' a été lancé avec succès sur '.$cible->get_nom().'<br />';
							//Insertion du debuff dans les journaux des 2 joueurs
							if($type_cible != 'monstre')
							{
								$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
								$requete = "INSERT INTO journal VALUES(NULL,  ".$cible->get_id().", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
								
								if($sort->get_type() == "debuff_enracinement")
								{
									// Augmentation du compteur de l'achievement
									$achiev = $cible->get_compteur('nbr_enracine');
									$achiev->set_compteur($achiev->get_compteur() + 1);
									$achiev->sauver();
								}
								elseif($sort->get_type() == "lente_agonie")
								{
									// Augmentation du compteur de l'achievement
									$achiev = $joueur->get_compteur('nbr_lenteagonie');
									$achiev->set_compteur($achiev->get_compteur() + 1);
									$achiev->sauver();
								}
							}
						}
						else
						{
							echo $cible->get_nom().' bénéficie d\'un debuff plus puissant<br />';
						}
					}
					else
					{
						echo $cible->get_nom().' résiste à votre sort !<br />';
				 	}
					$lancement = true;
					$debuff = true;
				break;
				case 'maladie_amorphe' : case 'maladie_degenerescence' : case 'maladie_mollesse' :
					if($type_cible == 'joueur')
					{
						$perso = new perso($cible->get_id());
						$cibles = array();
						
						if($perso->get_groupe() != 0)
						{
							$groupe_cible = new groupe($perso->get_groupe());
							
							foreach($groupe_cible->get_membre_joueur() as $cbl)
								$cibles[] = new entite('joueur', $cbl);
						}
						else
							$cibles[] = new entite('joueur', $perso);
					}
					else
					{
						$cibles_mob = map_monstre::create(array('x', 'y'), array($cible->get_x(), $cible->get_y()));
						$cibles = array();
						
						foreach($cibles_mob as $cbl)
						{
							$monstre = new monstre($cbl->get_type());
							$monstre->hp_max = $monstre->get_hp();
							$monstre->set_hp($cbl->get_hp());
							$monstre->x = $cbl->get_x();
							$monstre->y = $cbl->get_y();
							$cibles[] = new entite('monstre', $monstre);
							end($cibles)->set_id($cbl->get_id());
						}
					}
					
					foreach($cibles as $cible)
					{
						$distance = calcul_distance_pytagore($joueur->get_pos(), $cible->get_pos());
						if($distance <= 7)
						{
							//Test d'esquive du sort
							$protection = $cible->get_volonte() * $cible->get_pm() / 3;
							if($cible->is_buff('bulle_sanctuaire', true)) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
							if($cible->is_buff('bulle_dephasante', true)) $protection *= $cible->get_buff('bulle_dephasante','effet');
							$attaque = rand(0, ($joueur->get_volonte() * $joueur->get_comp($sort->get_comp_assoc())));
							$defense = rand(0, $protection);
							if ($attaque > $defense)
							{
								$duree = $sort->get_duree();
								if($joueur->is_buff('souffrance_extenuante', true)) $duree = $duree * $joueur->get_buff('souffrance_extenuante','effet');
								//Mis en place du debuff
								if(lance_buff($sort->get_type(), $cible->get_id(), $sort->get_effet(), $sort->get_effet2(), $duree, $sort->get_nom(), description($sort->get_description(), $sort), $type_cible == 'monstre' ? 'monstre' : 'perso', 1, 0, 0))
								{
									echo 'Le sort '.$sort->get_nom().' a été lancé avec succès sur '.$cible->get_nom().'<br />';
									//Insertion du debuff dans les journaux des 2 joueurs
									if($type_cible != 'monstre')
									{										
										$requete = "INSERT INTO journal VALUES(NULL,  ".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
										$db->query($requete);
										$requete = "INSERT INTO journal VALUES(NULL,  ".$cible->get_id().", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
										$db->query($requete);
									}
								}
								else
								{
									echo $cible->get_nom().' bénéficie d\'un debuff plus puissant.<br />';
								}
							}
							else
							{
								echo $cible->get_nom().' résiste à votre sort !<br />';
				 			}
			 			}
			 		}
					$lancement = true;
					$debuff = true;
				break;
				case 'rez' :
					//Sale
					if($type_cible != 'joueur')
					{
						echo 'Ce sort ne peut être utilisé que sur un joueur mort.';
						break;
					}
					
					//On vérifie que le joueur est bien mort !
					if($cible->get_hp() <= 0)
					{
						//On vérifie si le joueur n'a pas déjà une rez plus efficace d'active
						$requete = "SELECT pourcent FROM rez WHERE id_perso = ".$cible->get_id();
						$req_pourcent = $db->query($requete);
						$pourcent_max = 0;
						while($row_pourcent = $db->read_assoc($req_pourcent))
						{
							if($row_pourcent['pourcent'] > $pourcent_max) $pourcent_max = $row_pourcent['pourcent'];
						}
						if($sort->get_effet() > $pourcent_max)
						{
							$lancement = true;
							//Mis en place de la résurection
							$requete = "INSERT INTO rez VALUES('', ".$cible->get_id().", ".$joueur->get_id().", '".$joueur->get_nom()."', ".$sort->get_effet().", ".$sort->get_effet2().", ".$sort->get_duree().", NOW())";
							$db->query($requete);
							
							// Augmentation du compteur de l'achievement
							$achiev = $joueur->get_compteur('rez');
							$achiev->set_compteur($achiev->get_compteur() + 1);
							$achiev->sauver();
							
							echo 'Résurrection bien lancée.';
						}
						else
						{
							echo 'Le joueur bénéficie d\'une résurrection plus puissante.';
						}
					}
					else
					{
						echo 'Le joueur n\'est pas mort';
					}
				break;				
			}
		}
		//On fait le final si le lancement est réussi
		if($lancement)
		{
			echo '<br />';
			$joueur->set_pa($joueur->get_pa() - $sortpa);
			$lanceur->set_mp($lanceur->get_mp() - $sortmp);
			if($possible_augmentation)
			{
				//Augmentation des compétences
				$difficulte_sort = diff_sort($sort->get_difficulte() * 1.1, $joueur, 'incantation', $sortpa_base, $sortmp_base);
				$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
				if ($augmentation[1] == 1)
				{
					$joueur->set_incantation($augmentation[0]);
				}
				$difficulte_sort = diff_sort($sort->get_difficulte() * 1.1, $joueur, $sort->get_comp_assoc(), $sortpa_base, $sortmp_base);
				$augmentation = augmentation_competence($sort->get_comp_assoc(), $joueur, $difficulte_sort);
				if ($augmentation[1] == 1)
				{
					$joueur->set_comp($sort->get_comp_assoc(), $augmentation[0]);
				}
			}
			$joueur->sauver();
			$lanceur->sauver();
			
			// Augmentation du compteur de l'achievement
			if($buff)
			{
				$achiev = $joueur->get_compteur('buff');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			elseif($debuff)
			{
				$achiev = $joueur->get_compteur('debuff');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
		}
		if($groupe) $cible = $joueur;
		if($type_lanceur == 'joueur') echo '<br /><a href="sort.php?type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id().'" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
		else echo '<br /><a href="gestion_monstre.php" onclick="return envoiInfo(this.href, \'information\');">Revenir à la gestion des monstres</a>';
	}
}
elseif($joueur->is_buff('bloque_sort'))
{
	echo 'Vous êtes sous vunérabilité, vous ne pouvez plus lancer de sorts hors combat.';
}
elseif($type_lanceur == 'joueur')
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris(id_sort, id_perso) VALUES(".sSQL($_GET['id']).", ".$joueur->get_id().")";
				$db->query($requete);
			break;
			case 'delfavoris' :
				$requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur->get_id();
				$db->query($requete);
			break;
		}
	}
	$i = 0;
	$type = '';
	$magies = array('favoris');
	$magie = '';
	$requete = "SELECT * FROM sort_jeu GROUP BY comp_assoc";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$magies[] = $row['comp_assoc'];
		}
	}
	$groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
	foreach($magies as $magie)
	{
		echo '<a href="sort.php?tri='.$magie.$groupe_href.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" style="vertical-align : middle;" /></a> ';
	}
	echo 'Cible : '.$cible->get_nom();
	$where = '';
	
	if(array_key_exists('tri', $_GET)) 
		$where = 'comp_assoc = \''.$_GET['tri'].'\''; 
	else 
		$_GET['tri'] = 'favoris';
	
	if($_GET['tri'] == 'favoris')
		$where = 'id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = \''.$joueur->get_id().'\')';

	$test = false;
	$sorts = sort_jeu::create('', '', 'comp_assoc ASC, type ASC', false, ''.$where);
	//$req = $db->query($requete);
	$magie = '';
	echo '<table width="97%" class="information_case">';
	foreach($sorts as $sort)
	{
		if ($sort->get_special() == false)
		{
			$sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
			$sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
		}
		else 
		{
			$sortpa = $sort->get_pa();
			$sortmp = $sort->get_mp();
		}
		
		//Réduction du cout par concentration
		if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($magie != $sort->get_comp_assoc())
		{
			$magie = $sort->get_comp_assoc();
			$type = '';
			echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
		}
		if(in_array($sort->get_id(), explode(';',$joueur->get_sort_jeu())))
		{
			$image = image_sort($sort->get_type());
			$incanta = $sort->get_incantation();
			echo '
			<div style="z-index: 3;">
				<tr>';
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			$affiche = false;
			if($type_cible == 'joueur')
			{
				$sort_groupe = false;
				if($cible->get_id() == $joueur->get_id())
				{
					$cond = ($sort->get_cible() == 1 OR $sort->get_cible() == 8 OR $sort->get_cible() == 2 OR $sort->get_cible() == 3) && $sort->get_type() != 'rez';
					if($cond)
						$sort_groupe = true;
				}
				else
					$cond = ($sort->get_cible() == 2 OR $sort->get_cible() == 4);
					
				if($cond)
				{
					$href = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;type=joueur&amp;id_joueur='.$cible->get_id().'\', \'information\')';
					$href2 = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;groupe=yes&amp;type=joueur&amp;id_joueur='.$cible->get_id().'\', \'information\')';	
					$color = '#444';
					$cursor = 'cursor : pointer;';
					$affiche = true;
				}
			}
			else if($type_cible == 'monstre')
			{
				if(($sort->get_cible() == 2 OR $sort->get_cible() == 4) AND $sort->get_type() != 'rez')
				{
					$href = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;type=monstre&amp;id_monstre='.$cible->get_id().'\', \'information\')';
					$color = '#444';
					$cursor = 'cursor : pointer;';
					$affiche = true;
				}
			}
			if(!$affiche)
			{
				$href = '';
				$href2 = '';
				$cursor = '';
				$color = 'black';
			}
			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>; text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>; return nd();" onmouseover="return <?php echo make_overlib(description($sort->get_description(), $sort).'<br/><span class=&quot;xmall&quot;>Incantation : '.$incanta.'</span>'); ?>" onmouseout="return nd();"> <strong><?php echo $sort->get_nom(); ?></strong></span>
			</td>
			<?php
			echo '
			<td>
				<span class="xsmall"> '.$sortpa.' PA 
			</td>
			<td>
				'.$sortmp.' MP
			</td> 
			<td>';
			if($sort->get_cible() == 2 && $sort_groupe)
				if($joueur->is_competence('sort_groupe')|| $joueur->is_competence('sort_groupe_'.$sort->get_comp_assoc())) echo ' <span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href2.'">(groupe - '.ceil($sortmp * 1.5).' MP)</span>';
				
			if($_GET['tri'] == 'favoris') echo ' <td><a href="sort.php?action=delfavoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="sort.php?action=favoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}

	}
	echo '</table>';
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
