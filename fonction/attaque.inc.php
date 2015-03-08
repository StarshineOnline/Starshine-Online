<?php //  -*- tab-width:2; mode: php;  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * @file attaque.inc.php
 * @deprecated
 */
include_once(root.$root.'class/competence.class.php');
include_once(root.$root.'class/gemme.class.php');

/**
 * Ca serait bien si dans cette fonction on se limitait à appliquer les effets,
 * lancer les dés, et qu'on ne codait que les trucs vraiment très particuliers,
 * qui modifient la cinématique de l'attaque.
 *
 * @param $acteur joueur qui agit
 * @param $competence compétence utilisée
 * @param $effects liste des effets.
 */
function attaque($acteur = 'attaquant', $competence, &$effects)
{
  echo "<h6>pourquoi c'est là ?</h6>";
  	global $attaquant, $defenseur, $G_buff, $G_debuff, $ups, $Gtrad, $G_round_total, $db, $log_combat, $log_effects_attaquant, $log_effects_defenseur;
  	if ($acteur == 'attaquant')
    {
      $actif = $attaquant;
      $passif = $defenseur;
	  $log_effects_actif = $log_effects_attaquant;
	  $log_effects_passif = $log_effects_defenseur;
    }
  	else
    {
      $actif = $defenseur;
      $passif = $attaquant;
	  $log_effects_actif = $log_effects_defenseur;
	  $log_effects_passif = $log_effects_attaquant;
    }
  	$augmentation = array('actif' => array('comp' => array(),
																					 'comp_perso' => array()),
													'passif' => array('comp' => array(),
																						'comp_perso' => array()));

  	//Réctification si c'est un orc ou un donjon
		$round = is_donjon($actif->get_x(), $actif->get_y()) ? $G_round_total * 2 : $G_round_total;
		if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
			$round += 1;
		$rectif_augm = $round / $G_round_total;
		
  	$ups = array();

  	/* Application des effets de début de round */
  	foreach ($effects as $effect) 
  		$effect->debut_round($actif, $passif);
  	/* ~Debut */

  	$potentiel_toucher = $actif->get_potentiel_toucher();
  	$potentiel_parer = $passif->get_potentiel_parer();

		/* Application des effets de potentiel toucher */
		foreach ($effects as $effect)
			$potentiel_toucher =
				$effect->calcul_attaque_physique($actif, $passif, $potentiel_toucher);
		/* ~Potentiel Toucher */
			
		/* Application des effets de potentiel parer */
		foreach ($effects as $effect)
		{
			$potentiel_parer =
				$effect->calcul_defense_physique($actif, $passif, $potentiel_parer);
    }
		/* ~Potentiel Parer */

  	//Test d'esquive
  	$attaque = rand(0, $potentiel_toucher);
  	$defense = rand(0, $potentiel_parer);
  	print_debug('Potentiel toucher attaquant : '.$potentiel_toucher.
							'<br />Potentiel parer défenseur : '.$potentiel_parer.
							'<br />Résultat => Attaquant : '.$attaque.' | Défense '.
							$defense.'<br />');
	if ($attaque > $defense)
	{
		//Si c'est un coup de bouclier, infliger les dégats du bouclier et tester l'étourdissement
		if($actif->etat['coup_bouclier']['effet'] > 0)
		{
			$degat = $actif->get_bouclier_degat();

			// Prise en compte des effets defenseurs de l'attaquant (protection artistique ...)
			$tmp_effets = array();
			$actif->get_effets_permanents($tmp_effets, 'defenseur');
			/* Application des degats bloques */
			foreach ($tmp_effets as $effect)
				// Actif et passif sont inversés puisque c'est l'actif qui touche au bouclier
				$degat = $effect->calcul_bloquage_reduction($passif, $actif, $degat);
			/* ~degats bloques */

			$att = $degat + $actif->get_force();
			$def = $passif->get_vie() + round($passif->get_pp() / 100);
			$atta = rand(0, $att);
			$defe = rand(0, $def);
			print_debug("Potentiel étourdir attaquant : $att<br />".
				"Potentiel resister défenseur : $def<br />".
				"Résultat => Attaquant : $atta | Défenseur : $defe <br />");
			//aff_var($actif->etat['coup_bouclier']);
			//Hop ca étourdit
			if($atta > $defe)
			{
				$passif->etat['etourdit']['effet'] = $actif->etat['coup_bouclier']['effet'];
				$passif->etat['etourdit']['duree'] = $actif->etat['coup_bouclier']['effet2'];
				echo '&nbsp;&nbsp;Le coup de bouclier étourdit '.$passif->get_nom().' pour '.$passif->etat['etourdit']['duree'].' rounds !<br />';
			}
		}
		//sinon
		else
		{
			if(array_key_exists('tir_vise', $actif->etat)) $buff_vise_degat = $actif->etat['tir_vise']['effet'] + 1; else $buff_vise_degat = 1;
			if($actif->etat['posture']['type'] == 'posture_degat') $buff_posture_degat = $actif->etat['posture']['effet']; else $buff_posture_degat = 0;
			$arme_degat = ($actif->get_arme_degat() + $buff_posture_degat) * $buff_vise_degat;

			/* Application des effets de boost des armes */
			foreach ($effects as $effect)
				$arme_degat = $effect->calcul_arme($actif, $passif, $arme_degat);
			/* ~Armes */
			$de_degat = de_degat($actif->get_force(), $arme_degat);
			$degat = 0;
			$i = 0;
			$dbg_msg = '';
			while($i < count($de_degat))
			{
				$de = rand(1, $de_degat[$i]);
				$degat += $de;
				$dbg_msg .= 'Max : '.$de_degat[$i].' - Dé : '.$de.'<br />';
				$i++;
			}
			print_debug($dbg_msg);
		}
      	if($passif->type2 == 'batiment' AND $actif->get_race() == 'barbare') $degat = floor($degat * 1.4);
      	$degat = $degat + $actif->degat_sup - $actif->degat_moins;
      	if(array_key_exists('attaque_vicieuse', $actif->etat))
		{
			$degat = $degat + $actif->etat['attaque_vicieuse'];
			$passif->etat['hemorragie']['duree'] = 5;
			$passif->etat['hemorragie']['effet'] = $actif->etat['attaque_vicieuse'];
			echo '&nbsp;&nbsp;L\'attaque inflige une hémorragie !<br />';
		}
      	if($degat < 0) $degat = 0;
      	if(array_key_exists('benediction', $actif->etat)) $buff_bene_degat = $actif->etat['benediction']['effet'] * $G_buff['bene_degat']; else $buff_bene_degat = 0;
      	if(array_key_exists('berzeker', $actif->etat)) $buff_berz_degat = $actif->etat['berzeker']['effet'] * $G_buff['berz_degat']; else $buff_berz_degat = 0;
      	if(array_key_exists('berzeker', $passif->etat)) $buff_berz_degat_r = $passif->etat['berzeker']['effet'] * $G_buff['berz_degat_recu']; else $buff_berz_degat_r = 0;
      	if($actif->is_buff('buff_force')) $buff_force = $actif->get_buff('buff_force', 'effet'); else $buff_force = 0;
      	if($actif->is_buff('buff_cri_victoire')) $buff_cri_victoire = $actif->get_buff('buff_cri_victoire', 'effet'); else $buff_cri_victoire = 0;
      	if($actif->is_buff('fleche_tranchante') && $actif->get_arme_type() == 'arc') $degat += $actif->get_buff('fleche_tranchante', 'effet');
      	if($actif->is_buff('oeil_chasseur') && $passif->get_espece() == 'bete' && $actif->get_arme_type() == 'arc') $degat += $actif->get_buff('oeil_chasseur', 'effet');
      	$degat = $degat + $buff_bene_degat + $buff_berz_degat + $buff_berz_degat_r + $buff_force + $buff_cri_victoire;
      	if($actif->is_buff('maladie_mollesse')) $degat = ceil($degat / (1 + ($actif->get_buff('maladie_mollesse', 'effet') / 100)));

 	  	/* Application des effets de degats */
    	foreach ($effects as $effect)
			$degat = $effect->calcul_degats($actif, $passif, $degat);
      	/* ~Degats */
	    if($passif->bouclier())
		{
			//Si c'est une flèche rapide, on ignore le blocage
			if(array_key_exists('fleche_rapide', $actif->etat))
			{
			}
			else
			{
				$p_e = $passif->get_enchantement();
				if(array_key_exists('blocage', $p_e)) $enchantement_blocage = ($p_e['blocage']['effet']); else $enchantement_blocage = 0;
				if($passif->is_buff('buff_bouclier_sacre')) $buff_blocage = 1 + ($passif->get_buff('buff_bouclier_sacre', 'effet') / 100); else $buff_blocage = 1;
				if(array_key_exists('benediction', $passif->etat)) $buff_bene_blocage = 1 + (($passif->etat['benediction']['effet'] * $G_buff['bene_bouclier']) / 100); else $buff_bene_blocage = 1;
				if(array_key_exists('a_c_bloque', $actif->etat)) $augmentation_chance_bloque = 1 + ($actif->etat['a_c_bloque']['effet'] / 100); else $augmentation_chance_bloque = 1;
				if(array_key_exists('b_c_bloque', $actif->etat)) $baisse_chance_bloque = 1 + ($actif->etat['b_c_bloque']['effet'] / 100); else $baisse_chance_bloque = 1;
				$passif->potentiel_bloquer = floor(($passif->get_blocage() + $enchantement_blocage ) * (pow($passif->get_dexterite(), 2) / 100) * $buff_bene_blocage * $buff_blocage * $augmentation_chance_bloque / ($baisse_chance_bloque));

				/* Application des effets de blocage */
				foreach ($effects as $effect)
					$effect->calcul_bloquage($actif, $passif);
				
				/* ~Blocage */
				$blocage = rand(0, $passif->potentiel_bloquer);
				print_debug('Potentiel bloquer défenseur : '.
										$passif->potentiel_bloquer.'<br />Attaque : '.
										$attaque.'<br />Résultat => '.$blocage.' VS '.
										$attaque.'<br />');
				//Si le joueur bloque
				if ($attaque <= $blocage)
				{
					$degat_bloque = $passif->get_bouclier_degat();//$passif->bouclier()->degat;
					if($passif->is_buff('bouclier_terre')) $degat_bloque += $passif->get_buff('bouclier_terre', 'effet');

					/* Application des degats bloques */
					foreach ($effects as $effect)
						$degat_bloque = $effect->calcul_bloquage_reduction($actif, $passif, $degat_bloque);

					/* ~degats bloques */

					$degat = $degat - $degat_bloque;
					if($degat < 0) $degat = 0;
					echo '&nbsp;&nbsp;<span class="manque">'.$passif->get_nom().' bloque le coup et absorbe '.$degat_bloque.' dégâts</span><br />';
					if($passif->is_buff('bouclier_feu'))
					{
						$degats = $passif->get_buff('bouclier_feu', 'effet');
						$actif->set_hp($actif->get_hp() - $degats);
						echo '&nbsp;&nbsp;<span class="degat">'.$passif->get_nom().' inflige '.$degats.' dégâts grâce au bouclier de feu</span><br />';
					}
					if($passif->is_buff('bouclier_eau'))
					{
						$chances = $passif->get_buff('bouclier_eau', 'effet') * 2;
						$diffi = 100;
						$att = rand(0, $chances);
						$def = rand(0, $diffi);
						print_debug("Potentiel glacer: $chances<br />Résultat => $att vs $def");
						if($att > $def)
						{
							echo '&nbsp;&nbsp;<span class="degat">'.$passif->get_nom().' bloque et glace '.$actif->get_nom().'</span><br />';
							$actif->etat['bouclier_glace']['effet'] = 1;
							$actif->etat['bouclier_glace']['duree'] = ($passif->get_buff('bouclier_eau', 'effet2') + 1);
						}
					}
									
                  //echo '<pre>'; var_dump($effects); echo '</pre>';

					/* Application des effets de blocage */
					foreach ($effects as $effect)
						$degat = $effect->applique_bloquage($actif,$passif,$degat);

					/* ~Blocage */

				}
				$diff_blocage = (2.5 * $G_round_total / 5) * $rectif_augm;
			$augmentation['passif']['comp'][] = array('blocage', $diff_blocage);
			if($passif->is_competence('maitrise_bouclier'))
				$augmentation['passif']['comp_perso'][] = array('maitrise_bouclier', 6 * $rectif_augm);
			}
			
		}
     	//Posture défensive
      	if($passif->etat['posture']['type'] == 'posture_defense') $buff_posture_defense = $passif->etat['posture']['effet']; else $buff_posture_defense = 0;
      	$degat = $degat - $buff_posture_defense;
      	if($degat < 0) $degat = 0;
	    //Diminution des dégats grâce à l'armure
    	if(array_key_exists('benediction', $passif->etat)) $buff_bene_bouclier = 1 + (($passif->etat['benediction']['effet'] * $G_buff['bene_bouclier']) / 100); else $buff_bene_bouclier = 1;
      	if(array_key_exists('berzeker', $passif->etat)) $buff_berz_bouclier = 1 + (($passif->etat['berzeker']['effet'] * $G_buff['berz_bouclier']) / 100); else $buff_berz_bouclier = 1;
      	if(array_key_exists('batiment_pp', $actif->etat)) $buff_batiment_bouclier = 1 + (($actif->etat['batiment_pp']['effet']) / 100); else $buff_batiment_bouclier = 1;
      	if(array_key_exists('acide', $passif->etat)) $debuff_acide = 1 + (($passif->etat['acide']['effet2']) / 100); else $debuff_acide = 1;
      	if($passif->etat['posture']['type'] == 'posture_pierre') $aura_pierre = 1 + (($passif->etat['posture']['effet']) / 100); else $aura_pierre = 1;
	    //Chance de transpercer l'armure
    	$transperce = false;
      	if($actif->etat['posture']['type'] == 'posture_transperce')
		{
			$atta = $actif->etat['posture']['effet'];
			$defe = 100;
			$att = rand(0, $atta);
			$defe = rand(0, $defe);
			if($att > $defe)
			{
				$transperce = true;
			}
		}
	    //Si c'est une flèche rapide, chance d'ignorer l'armure
    	if(array_key_exists('fleche_rapide', $actif->etat))
		{
			$atta = $actif->etat['fleche_rapide']['effet'];
			unset($actif->etat['fleche_rapide']);
			$defe = rand(0, 100);
			if($defe < $atta)
			{
				$transperce = true;
			}
		}
      	if(array_key_exists('coup_mortel', $actif->etat))
		{
			$degat = $degat * 3;
		}
      	$PP = round(($passif->get_pp() * $buff_bene_bouclier * $buff_batiment_bouclier * $aura_pierre) / ($buff_berz_bouclier * $debuff_acide));

      
      	/* Application des effets de PP */
      	foreach ($effects as $effect)
			$PP = $effect->calcul_pp($actif, $passif, $PP);
		$passif->PP_effective = $PP;
		
	    /* ~PP */

    	if(!$transperce) $reduction = calcul_pp($PP); 
		else 
		{
			$reduction = 1;
			echo 'Le coup porté est tellement violent qu\'il transperce l\'armure !<br />';
		}
      if ($reduction < 0) $reduction = 0; // On se soigne pas avec l'armure ^^
      $degat_avant = $degat;
      $degat = round($degat * $reduction);
			
	    //Coup critique
    	$actif_chance_critique = ceil(pow($actif->get_dexterite(), 1.5) * 10);

      	//Buff du critique
      	if($actif->is_buff('buff_critique', true)) $actif_chance_critique *= 1 + (($actif->get_buff('buff_critique', 'effet', true)) / 100);
      	if($actif->is_buff('buff_cri_rage', true)) $actif_chance_critique *= 1 + (($actif->get_buff('buff_cri_rage', 'effet')) / 100);
      	if(array_key_exists('benediction', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['benediction']['effet'] * $G_buff['bene_critique']) / 100);;
      	if(array_key_exists('tir_vise', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['tir_vise']['effet'] * 5) / 100);
      	if(array_key_exists('berzeker', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['berzeker']['effet'] * $G_buff['berz_critique']) / 100);
      	if(array_key_exists('coup_sournois', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['coup_sournois']['effet']) / 100);
      	if(array_key_exists('fleche_sanglante', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['fleche_sanglante']['effet']) / 100);
      	if(array_key_exists('a_critique', $actif->etat)) $actif_chance_critique *= 1 + (($actif->etat['a_critique']['effet']) / 100);
      	if(array_key_exists('b_critique', $actif->etat)) $actif_chance_critique /= 1 + (($actif->etat['b_critique']['effet']) / 100);
	    //Elfe des bois
    	if($actif->get_race() == 'elfebois') $actif_chance_critique *= 1.15;
      	if(array_key_exists('coup_mortel', $actif->etat))
		{
			$actif_chance_critique *= 1 + ($actif->etat['coup_mortel']['effet2']/100);
			unset($actif->etat['coup_mortel']);
		}
	    //Enchantement critique
    	if(array_key_exists('critique', $actif->get_enchantement())) $actif_chance_critique *= 1 + (($actif->enchantement['critique']['effet']) / 100);
      	if($actif->etat['posture']['type'] == 'posture_critique') $actif_chance_critique *= 1 + (($actif->etat['posture']['effet']) / 100);

      	/* Application des effets de chance critique */
      	foreach ($effects as $effect)
			$actif_chance_critique = $effect->calcul_critique($actif, $passif, $actif_chance_critique);
      	
      	/* ~chance critique */
	    $chance = rand(0, 10000);
    	$critique = false;
		print_debug('Potentiel critique attaquant : '.$actif_chance_critique.
								' / 10000<br />Résultat => '.$chance.
								' doit être inférieur au Potentiel critique<br />');
	    if($chance < $actif_chance_critique)
		{
			$actif->set_compteur_critique();
			echo '&nbsp;&nbsp;<span class="coupcritique">COUP CRITIQUE !</span><br />';
			$log_combat .= '!';
			//Chance de paralyser l'adversaire
			if($actif->etat['posture']['type'] == 'posture_paralyse')
			{
				$atta = $actif->etat['posture']['effet'];
// 				1d30 vs 1d100 << 30% de chances ( < 15% même)
// 				$defe = 100;
// 				$att = rand(0, $atta);
// 				$defe = rand(0, $defe);
// 				echo $att.' '.$defe.'<br />';
// 				if($att > $defe)
				$att = rand(0, 100);
				if($att <= $atta)
				{
					echo $passif->get_nom().' est paralysé par ce coup !<br />';
					if(array_key_exists('paralysie', $passif->etat)) $passif->etat['paralysie']['duree']++;
					else
					{
						$passif->etat['paralysie']['effet'] = 1;
						$passif->etat['paralysie']['duree'] = 1;
					}
				}
			}
			//Art du critique : augmente les dégâts fait par un coup critique
			if($actif->is_competence('art_critique')) $art_critique = $actif->get_competence2('art_critique')->get_valeur() / 100; else $art_critique = 0;
			//Buff Colère
			if($actif->is_buff('buff_colere')) $buff_colere = ($actif->get_buff('buff_colere', 'effet')) / 100; else $buff_colere = 0;
			//Orc
			if($actif->get_race() == 'orc') $bonuscritique_race = 1.05; else $bonuscritique_race = 1;
			if($passif->get_race() == 'troll') $maluscritique_race = 1.2; else $maluscritique_race = 1;

			$multiplicateur = (2 + $art_critique + $buff_colere) * $bonuscritique_race / $maluscritique_race;

			/* Application des effets de multiplicateur critique */
			foreach ($effects as $effect)
				$multiplicateur = 
					$effect->calcul_mult_critique($actif, $passif, $multiplicateur);
			/* ~multiplicateur critique */
			print_debug("Dégâts de base : $degat, multiplicateur : $multiplicateur<br />");
			$degat = round($degat * $multiplicateur);
			$degat_avant = round($degat_avant * $multiplicateur);
			$critique = true;
			if(array_key_exists('renouveau_energique', $actif->buff) && $actif->get_arme_type() == 'arc')
			{
				$actif->set_rm_restant($actif->get_rm_restant() + $actif->get_buff('renouveau_energique', 'effet'));
				echo $actif->get_nom().' se ressaisi et gagne '.$actif->get_buff('renouveau_energique', 'effet').' RM<br />';
			}
			//if(array_key_exists('maitre_critique', $actif['competences'])) augmentation_competence('maitre_critique', $actif, 3);
		}
    	$reduction = $degat_avant - $degat;
      	echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts</span><br />';
		$log_combat .= '~'.$degat;
      	if($reduction != 0) echo '&nbsp;&nbsp;<span class="small">(réduits de '.$reduction.' par l\'armure)</span><br />';
	    //Si flêche étourdissante
    	if($actif->etat['fleche_etourdit'] > 0)
		{
			echo '&nbsp;&nbsp;<strong>'.$passif->get_nom().'</strong> est étourdi par la flêche !<br />';
			$passif->etat['etourdit']['duree'] = 2;
		}
		//Si flêche magnetique
		if($actif->etat['fleche_magnetique_attaque'])
		{
			$effects[] = new fleche_magnetique($actif->etat['fleche_magnetique_attaque']['effet2'], $actif->etat['fleche_magnetique_attaque']['effet']);
			unset($actif->etat['fleche_magnetique_attaque']);
		}
		//Si flêche sable
		if($actif->etat['fleche_sable_attaque'])
		{
			$effects[] = new fleche_sable($actif->etat['fleche_sable_attaque']['effet'], 
						$actif->etat['fleche_sable_attaque']['effet2'],	$actif->etat['fleche_sable_attaque']['duree']);
			unset($actif->etat['fleche_sable_attaque']);
		}
		//Si flêche debilitante
		if($actif->etat['fleche_debilitante_attaque'])
		{
			$passif->etat['fleche_debilitante']['effet'] = $actif->etat['fleche_debilitante_attaque']['effet'];
			$passif->etat['fleche_debilitante']['duree'] = $actif->etat['fleche_debilitante_attaque']['duree'];
			unset($actif->etat['fleche_debilitante_attaque']);						
		}		
		if($actif->is_buff('buff_rage_vampirique', true))
		{
			$buff_rage_vampirique = $actif->get_buff('buff_rage_vampirique', 'effet') / 100;
			$effet = round($degat * $buff_rage_vampirique);
			if(($actif->get_hp() + $effet) > $actif->get_hp_max())
			{
				$effet = floor($actif->get_hp_max() - $actif->get_hp());
			}
			// Augmentation du nombre de HP récupérable par récupération
			if(array_key_exists('recuperation', $actif->etat)) $actif->etat['recuperation']['hp_max'] += $effet;
			$actif->set_hp($actif->get_hp() + $effet);
			if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$actif->get_nom().' gagne '.$effet.' HP par la rage vampirique</span><br />';
			$log_effects_actif .= "&ef8~".$effet;
		}
	    //Epines
    	if($passif->is_buff('buff_epine', true))
		{
			$buff_epine = $passif->get_buff('buff_epine', 'effet') / 100;
			$effet = round($degat * $buff_epine);
			$actif->set_hp($actif->get_hp() - $effet);
			if($effet > 0) echo '&nbsp;&nbsp;<span class="degat">'.$passif->get_nom().' renvoie '.$effet.' dégâts grâce à l\' Armure en épine</span><br />';
			$log_effects_passif .= "&ef9~".$effet;
		}
	    //Armure de glace
    	if($passif->is_buff('buff_armure_glace', true))
		{
			$chance = $passif->get_buff('buff_armure_glace', 'effet');
			$de1 = rand(0, $chance);
			$de2 = rand(0, 100);
			if($de1 > $de2)
			{
				echo '&nbsp;&nbsp;<span class="degat">'.$passif->get_nom().' glace '.$actif->get_nom().' avec son armure de glace</span><br />';
				$actif->etat['paralysie']['duree'] += 1;
			}
		}

		/* Application des effets de dégâts infligés */
		foreach ($effects as $effect)
			$degat = $effect->inflige_degats($actif, $passif, $degat);
			/* ~Dégâts infligés */

      	$passif->set_hp($passif->get_hp() - $degat);
	}
  	else
    {
		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible</span><br />';
		
		// On enleve les effets des comps qui ont raté
		unset($actif->etat['fleche_poison_attaque']);
		unset($actif->etat['fleche_debilitante_attaque']);
		
		$log_combat .= '~e';
    }
  	if(array_key_exists('coup_mortel', $actif))
    {
		unset($actif['coup_mortel']);
    }
  	if(array_key_exists('dissimulation', $actif->etat))
    {
		unset($actif->etat['dissimulation']);
    }

	//Augmentation des compétences de base
	$diff_att = (3.2 * $G_round_total / 5) * $rectif_augm;
	$augmentation['actif']['comp'][] = array($competence, $diff_att);
	$diff_esquive = (2.7 * $G_round_total / 5) * $rectif_augm;
	$augmentation['passif']['comp'][] = array('esquive', $diff_esquive);

	//Augmentation des compétences liées
	if($actif->is_competence('art_critique') && $critique)
		$augmentation['actif']['comp_perso'][] = array('art_critique', 2.5 * $rectif_augm);
	if($actif->is_competence('maitrise_critique') && $critique)
		$augmentation['actif']['comp_perso'][] = array('maitrise_critique', 2 * $rectif_augm);
	$arme = $actif->get_arme_type();
	if($actif->is_competence("maitrise_$arme"))
		$augmentation['actif']['comp_perso'][] = array("maitrise_$arme", 6 * $rectif_augm);

	/* Ici on va enregistrer les etats précédents */
	// Enregistre si on a esquivé ou non
	$passif->precedent['esquive'] = ($defense >= $attaque);
	// Enregistre si on a critiqué
	$actif->precedent['critique'] = $critique;

  	/* Application des effets de fin de round */
  	foreach ($effects as $effect)
    	$effect->fin_round($actif, $passif);
  	/* ~Fin de round */
	
  	if ($acteur == 'attaquant')
    {
      $attaquant = $actif;
      $defenseur = $passif;
	  $log_effects_attaquant = $log_effects_actif;
	  $log_effects_defenseur = $log_effects_passif;
    }
  	else
    {
      $attaquant = $passif;
      $defenseur = $actif;
	  $log_effects_defenseur = $log_effects_actif;
	  $log_effects_attaquant = $log_effects_passif;
    }
	return $augmentation;
}

function degat_magique($carac, $degat, $actif, $passif, $effects, $type)
{
	$dbg_msg = '';
  if (isset($actif->enchantement) &&
      isset($actif->enchantement['degat_magie'])) {
    global $db;
    $requete = "SELECT nom, enchantement_effet FROM gemme WHERE id = ".$actif->enchantement['degat_magie']['gemme_id'];
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $degat += $row['enchantement_effet'];
    $dbg_msg .= "La ".$row['nom'].' augmente les dégâts de '.
			$row['enchantement_effet'].'<br />';
  }

  $de_degat = de_degat($carac, $degat);
  $degat = 0;
  $i = 0;
  while($i < count($de_degat))
    {
      $de = rand(1, $de_degat[$i]);
      $degat += $de;
      $dbg_msg .= 'Max : '.$de_degat[$i].' - Dé : '.$de.'<br />';
      $i++;
    }
  print_debug($dbg_msg);
  if(critique_magique($actif, $passif))
    {
 	  $actif->set_compteur_critique();
      $degat = degat_critique($actif, $passif, $degat);
    }
  //Diminution des dégâts grâce à l'armure magique
  $reduction = calcul_pp(($passif->get_pm() * $passif->get_puissance()) / 12);
  $degat_avant = $degat;
  $degat = round($degat * $reduction);
  print_debug('(Réduction de '.($degat_avant - $degat).
						 ' dégâts par la PM)<br />');

  /* Application des modifications des dégâts */
  foreach ($effects as $effect)
    $degat = $effect->calcul_degats_magiques($actif, $passif, $degat, $type);
  /* ~modifications des dégâts */
				 
  return $degat;
}

/**
 * loot un item
 */
function loot_item(&$joueur, &$groupe, $item)
{
	global $db;
	$type_obj = '';
	//Nom de l'objet
	switch($item[0])
	{
	case 'h' :
		$objet_nom = 'Objet non identifié';
		//Gemme aléatoire
		if($item[1] == 'g')
		{
			//Niveau de la gemme
			$niveau_gemme = $item[2];
			//Recherche des gemmes de ce niveau
			$ids = array();
			$requete = "SELECT id FROM gemme WHERE niveau = ".$niveau_gemme;
			$req_g = $db->query($requete);
			while($row = $db->read_row($req_g))
			{
				$ids[] = $row[0];
			}
			$num = rand(0, (count($ids) - 1));
			$item = 'hg'.$ids[$num];
		}
		break;
	case 'o' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM objet WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = $row[0];
		break;
	case 'm' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM accessoire WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = $row[0];
		break;
	case 'a' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM arme WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = $row[0];
		break;
	case 'p' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM armure WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = $row[0];
		break;
	case 'r' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom, difficulte FROM recette WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = 'Recette unique : '.$row[0];
		$recette_difficulte = $row[1];
		break;
	case 'q' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM objet WHERE id = ".$id_objet;
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = $row[0];
		$item = 'o'.$id_objet;
		$type_obj = 'quete';
		break;
	case 'l' :
		$id_objet = mb_substr($item, 1);
		$requete = "SELECT nom FROM grimoire WHERE id = $id_objet";
		$req = $db->query($requete);
		$row = $db->read_row($req);
		$objet_nom = 'Grimoire : '.$row[0];
		break;
	}
	echo '<li>Vous fouillez le corps du monstre et découvrez "'.$objet_nom.'" !</li>';
	//Si le joueur a un groupe
	if ($joueur->get_groupe() > 0 AND $type_obj != 'quete')
	{
		//Répartition en fonction du mode de distribution
		switch ($groupe->get_partage())
		{
			//Aléatoire
		case 'r' :
			echo '<li>Répartition des objets aléatoire.</li>';
			$chance = count($groupe->membre);
			$aleat = rand(1, $chance);
			$gagnant = new perso($groupe->membre[($aleat - 1)]->get_id_joueur());
			break;
			//Par tour
		case 't' :
			echo '<li>Répartition des objets par tour.</li>';
			$gagnant = new perso($groupe->get_prochain_loot());
			//Changement du prochain loot
			$j_g = $groupe->trouve_position_joueur($groupe->get_prochain_loot());
			//Si c'est pas le dernier alors suivant
			if ((count($groupe->membre) - 1) != $j_g)
			{
				$groupe->set_prochain_loot($groupe->membre[($j_g + 1)]->get_id_joueur());
			}
			//Sinon premier
			else
			{
				$groupe->set_prochain_loot($groupe->membre[0]->get_id_joueur());
			}
			$groupe->sauver();
			break;
			//Leader
		case 'l' :
			echo '<li>Répartition des objets au leader.</li>';
			$gagnant = new perso($groupe->get_id_leader());
			break;
			//Celui qui trouve garde
		case 'k' :
			echo '<li>Répartition des objets, celui qui trouve garde.</li>';
			$gagnant = new perso($joueur->get_id());
			break;
		}
		echo '<li>'.$gagnant->get_nom().' reçoit "'.$objet_nom.'"</li>';
	}
	else
	{
		$gagnant = new perso($joueur->get_id());
	}
	//Insertion du loot dans le journal du gagnant
	$requete = 'INSERT INTO journal(id_perso, action, time, valeur, x, y) VALUES('.
		$gagnant->get_id().", 'loot', NOW(), '".mysql_escape_string($objet_nom).
		"', ".$joueur->get_x().", ".$joueur->get_y().")";
	$db->query($requete);
	if ($item[0] != 'r')
	{
		$gagnant->restack_objet();
		if($type_obj == 'quete')
		{
			verif_action('L'.$id_objet, $gagnant, 's');
			$gagnant->prend_objet($item);
		}
		else
		{
			$gagnant->prend_objet($item);
		}
	}
	else
	{
		prend_recette($item, $gagnant);
	}						
}
