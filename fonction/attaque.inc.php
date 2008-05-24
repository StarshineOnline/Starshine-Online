<?php  //  -*- tab-width:2  -*-
include_once('class/comp.class.php');

function attaque($acteur = 'attaquant', $competence)
{
  global $attaquant, $defenseur, $debugs, $G_buff, $G_debuff, $ups, $Gtrad, $G_round_total, $db;
  $ups = array();

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
	fleche_magnetique::factory($effects, $actif, $passif, $acteur);
	fleche_poison::factory($effects, $actif, $passif, $acteur);
	maitrise_bouclier::factory($effects, $actif, $passif, $acteur);

  /* Tri des effets selon leur ordre */
  sort_effects($effects);


  //Buff evasion
  if(array_key_exists('buff_evasion', $passif['buff'])) $passif['potentiel_parer'] *= (1 + (($passif['buff']['buff_evasion']['effet']) / 100));
  if(array_key_exists('buff_cri_detresse', $passif['buff'])) $passif['potentiel_parer'] *= 1 + (($passif['buff']['buff_cri_detresse']['effet']) / 100);
  if(array_key_exists('batiment_esquive', $passif['buff'])) $passif['potentiel_parer'] *= 1 + (($passif['buff']['batiment_esquive']['effet']) / 100);
  if(array_key_exists('benediction', $passif['etat'])) $passif['potentiel_parer'] *= 1 + (($passif['etat']['benediction']['effet'] * $G_buff['bene_evasion']) / 100);
  if(array_key_exists('berzeker', $passif['etat'])) $passif['potentiel_parer'] /= 1 + (($passif['etat']['berzeker']['effet'] * $G_buff['berz_evasion']) / 100);
  if(array_key_exists('derniere_chance', $passif['etat'])) $passif['potentiel_parer'] /= 1 + (($passif['etat']['derniere_chance']['effet']) / 100);
  if($passif['etat']['posture']['type'] == 'posture_esquive') $passif['potentiel_parer'] *= 1 + (($passif['etat']['posture']['effet']) / 100);
  if($passif['etat']['posture']['type'] == 'posture_vent') $passif['potentiel_parer'] *= 1 + (($passif['etat']['posture']['effet']) / 100);
  if($passif['arme_type'] != 'baton') $passif['potentiel_parer'] *= (100 - $passif['arme_var1']) / 100;
  if($passif['race'] == 'elfebois') $passif['potentiel_parer'] *= 1.15;
  //Debuff precision
  if(array_key_exists('debuff_aveuglement', $actif['debuff'])) $actif['potentiel_toucher'] /= 1 + (($actif['debuff']['debuff_aveuglement']['effet']) / 100);
  if(array_key_exists('aveugle', $actif['etat'])) $actif['potentiel_toucher'] /= 1 + (($actif['etat']['aveugle']['effet']) / 100);
  if(array_key_exists('lien_sylvestre', $actif['etat'])) $actif['potentiel_toucher'] /= 1 + (($actif['etat']['lien_sylvestre']['effet2']) / 100);
  if(array_key_exists('fleche_sable', $actif['etat'])) $actif['potentiel_toucher'] /= 1 + ($actif['etat']['fleche_sable']['effet'] / 100);
  if(array_key_exists('b_toucher', $actif['etat'])) $actif['potentiel_toucher'] /= 1 + ($actif['etat']['b_toucher']['effet'] / 100);
  //Buff précision
  if(array_key_exists('benediction', $actif['etat']))	$actif['potentiel_toucher'] *= 1 + (($actif['etat']['benediction']['effet'] * $G_buff['bene_accuracy']) / 100);
  if(array_key_exists('berzeker', $actif['etat'])) $actif['potentiel_toucher'] *= 1 + (($actif['etat']['berzeker']['effet'] * $G_buff['berz_accuracy']) / 100);
  if(array_key_exists('tir_vise', $actif['etat'])) $actif['potentiel_toucher'] *= 1 + (($actif['etat']['tir_vise']['effet'] * $G_buff['vise_accuracy']) / 100);
  if(array_key_exists('batiment_distance', $actif['buff'])) $actif['potentiel_toucher'] *= 1 + (($actif['buff']['batiment_distance']['effet']) / 100);
  if(array_key_exists('buff_cri_bataille', $actif['buff'])) $actif['potentiel_toucher'] *= 1 + (($actif['buff']['buff_cri_bataille']['effet']) / 100);
  if(array_key_exists('dissimulation', $actif['etat'])) $actif['potentiel_toucher'] *= 1 + (($actif['etat']['dissimulation']['effet']) / 100);
  if(array_key_exists('buff_position', $actif['buff'])) $actif['potentiel_toucher'] *= 1 + (($actif['buff']['buff_position']['effet']) / 100);
  if(array_key_exists('a_toucher', $actif['etat'])) $actif['potentiel_toucher'] *= 1 + ($actif['etat']['a_toucher']['effet'] / 100);
  //Corrompu la journée
  if($actif['race'] == 'humainnoir' AND moment_jour() == 'Journée') $actif['potentiel_toucher'] *= 1.1; else $bonus_race = 1;
  if($actif['etat']['posture']['type'] == 'posture_touche') $actif['potentiel_toucher'] *= 1 + (($actif['etat']['posture']['effet']) / 100); else $buff_posture_touche = 1;
  if($actif['arme_type'] == 'epee' AND array_key_exists('maitrise_epee', $actif['competences'])) $actif['potentiel_toucher'] *= 1 + ($actif['competences']['maitrise_epee'] / 1000);
  if($actif['arme_type'] == 'hache' AND array_key_exists('maitrise_hache', $actif['competences'])) $actif['potentiel_toucher'] *= 1 + ($actif['competences']['maitrise_hache'] / 1000);
  if($actif['arme_type'] == 'dague' AND array_key_exists('maitrise_dague', $actif['competences'])) $actif['potentiel_toucher'] *= 1 + ($actif['competences']['maitrise_dague'] / 1000);
  if($actif['arme_type'] == 'arc' AND array_key_exists('maitrise_arc', $actif['competences'])) $actif['potentiel_toucher'] *= 1 + ($actif['competences']['maitrise_arc'] / 1000);

  /* Application des effets de début de round */
  foreach ($effects as $effect) $effect->debut_round($actif, $passif);
  /* ~Debut */


  //Test d'esquive
  $attaque = rand(0, $actif['potentiel_toucher']);
  $defense = rand(0, $passif['potentiel_parer']);
  echo '
	<div id="debug'.$debugs.'" class="debug">
		Potentiel toucher attaquant : '.$actif['potentiel_toucher'].'<br />
		Potentiel parer défenseur : '.$passif['potentiel_parer'].'<br />
		Résultat => Attaquant : '.$attaque.' | Défense '.$defense.'<br />
	</div>';
  $debugs++;

  if ($attaque > $defense)
    {
      //Si c'est un coup de bouclier, infliger les dégats du bouclier et teste d'étourdissement
      if($actif['etat']['coup_bouclier'] > 0)
				{
					$degat = $actif['bouclier_degat'];
					$att = $actif['force'] + $actif['bouclier_degat'];
					$def = $passif['vie'] + round($passif['PP'] / 100);
					$atta = rand(0, $att);
					$defe = rand(0, $def);
					echo "<div id=\"debug${debugs}\" class=\"debug\">".
						"Potentiel étourdir attaquant : $att<br />".
						"Potentiel resister défenseur : $def<br />".
						"Résultat => Attaquant : $atta | Défenseur : $defe".
						"<br /></div>";
					//aff_var($actif['etat']['coup_bouclier']);
					$debug++;
					//Hop ca étourdit
					if($atta > $defe)
						{
							$passif['etat']['etourdit']['effet'] = $actif['etat']['coup_bouclier']['effet'];
							$passif['etat']['etourdit']['duree'] = $actif['etat']['coup_bouclier']['effet2'];
							echo '&nbsp;&nbsp;Le coup de bouclier étourdit '.$passif['nom'].' pour '.$passif['etat']['etourdit']['duree'].' !<br />';
						}
				}
      //sinon
      else
				{
					if(array_key_exists('tir_vise', $actif['etat'])) $buff_vise_degat = $actif['etat']['tir_vise']['effet'] + 1; else $buff_vise_degat = 1;
					if($actif['etat']['posture']['type'] == 'posture_degat') $buff_posture_degat = $actif['etat']['posture']['effet']; else $buff_posture_degat = 0;
					$arme_degat = ($actif['arme_degat'] + $buff_posture_degat) * $buff_vise_degat;

					
					/* Application des effets de boost des armes */
					foreach ($effects as $effect)
						$arme_degat = $effect->calcul_arme($actif, $passif, $arme_degat);
					/* ~Armes */

					$de_degat = de_degat($actif['force'], $arme_degat);
					$degat = 0;
					$i = 0;
					echo '
			<div id="debug'.$debugs.'" class="debug">';
					while($i < count($de_degat))
						{
							$de = rand(1, $de_degat[$i]);
							$degat += $de;
							echo 'Max : '.$de_degat[$i].' - Dé : '.$de.'<br />';
							$i++;
						}
					echo '</div>';
					$debugs++;
				}
      if($passif['type2'] == 'batiment' AND $actif['race'] == 'barbare') $degat = floor($degat * 1.4);
      $degat = $degat + $actif['degat_sup'] - $actif['degat_moins'];
      if(array_key_exists('attaque_vicieuse', $actif))
				{
					$degat = $degat + $actif['attaque_vicieuse'];
					$passif['etat']['hemorragie']['duree'] = 5;
					$passif['etat']['hemorragie']['effet'] = $actif['attaque_vicieuse'];
					echo '&nbsp;&nbsp;L\'attaque inflige une hémorragie !<br />';
				}
      if($degat < 0) $degat = 0;
      if(array_key_exists('benediction', $actif['etat'])) $buff_bene_degat = $actif['etat']['benediction']['effet'] * $G_buff['bene_degat']; else $buff_bene_degat = 0;
      if(array_key_exists('berzeker', $actif['etat'])) $buff_berz_degat = $actif['etat']['berzeker']['effet'] * $G_buff['berz_degat']; else $buff_berz_degat = 0;
      if(array_key_exists('berzeker', $passif['etat'])) $buff_berz_degat_r = $passif['etat']['berzeker']['effet'] * $G_buff['berz_degat_recu']; else $buff_berz_degat_r = 0;
      if(array_key_exists('buff_force', $actif['buff'])) $buff_force = $actif['buff']['buff_force']['effet']; else $buff_force = 0;
      if(array_key_exists('buff_cri_victoire', $actif['buff'])) $buff_cri_victoire = $actif['buff']['buff_cri_victoire']['effet']; else $buff_cri_victoire = 0;
      if(array_key_exists('fleche_tranchante', $actif['buff'])) $degat += $actif['buff']['fleche_tranchante']['effet'];
      if(array_key_exists('oeil_chasseur', $actif['buff']) AND $passif['espece'] == 'bete') $degat += $actif['buff']['oeil_chasseur']['effet'];
      $degat = $degat + $buff_bene_degat + $buff_berz_degat + $buff_berz_degat_r + $buff_force + $buff_cri_victoire;

      /* Application des effets de degats */
      foreach ($effects as $effect)
				$degat = $effect->calcul_degats($actif, $passif, $degat);
      /* ~Degats */

      if($passif['bouclier'])
				{
					//Si c'est une flèche rapide, on ignore le blocage
					if(array_key_exists('fleche_rapide', $actif['etat']))
						{
						}
					else
						{
							if(array_key_exists('blocage', $passif['enchantement'])) $enchantement_blocage = 1 + ($passif['enchantement']['blocage']['effet'] / 100); else $enchantement_blocage = 1;
							if(array_key_exists('buff_bouclier_sacre', $passif['buff'])) $buff_blocage = 1 + ($passif['buff']['buff_bouclier_sacre']['effet'] / 100); else $buff_blocage = 1;
							if(array_key_exists('benediction', $passif['etat'])) $buff_bene_blocage = 1 + (($passif['etat']['benediction']['effet'] * $G_buff['bene_bouclier']) / 100); else $buff_bene_blocage = 1;
							if(array_key_exists('a_c_bloque', $actif['etat'])) $augmentation_chance_bloque = 1 + ($actif['etat']['a_c_bloque']['effet'] / 100); else $augmentation_chance_bloque = 1;
							if(array_key_exists('b_c_bloque', $actif['etat'])) $baisse_chance_bloque = 1 + ($actif['etat']['b_c_bloque']['effet'] / 100); else $baisse_chance_bloque = 1;
							$passif['potentiel_bloquer'] = floor($passif['blocage'] * (pow($passif['dexterite'], 2) / 100) * $enchantement_blocage * $buff_bene_blocage * $buff_blocage * $augmentation_chance_bloque / ($baisse_chance_bloque));


							/* Application des effets de blocage */
							foreach ($effects as $effect)
								$effect->calcul_bloquage($actif, $passif);
							/* ~Blocage */

							$blocage = rand(0, $passif['potentiel_bloquer']);
							echo '
				<div id="debug'.$debugs.'" class="debug">
					Potentiel bloquer défenseur : '.$passif['potentiel_bloquer'].'<br />
					Attaque : '.$attaque.'<br />
					Résultat => '.$blocage.' VS '.$attaque.'<br />
				</div>';
							$debugs++;
							//Si le joueur bloque
							if ($attaque <= $blocage)
								{
									$degat_bloque = $passif['bouclier_degat'];
									if(array_key_exists('bouclier_terre', $passif['buff'])) $degat_bloque += $passif['buff']['bouclier_terre']['effet'];
									$degat = $degat - $degat_bloque;
									if($degat < 0) $degat = 0;
									echo '&nbsp;&nbsp;<span class="manque">'.$passif['nom'].' bloque le coup et absorbe '.$degat_bloque.' dégats</span><br />';
									if(array_key_exists('bouclier_feu', $passif['buff']))
										{
											$degats = $passif['buff']['bouclier_feu']['effet'];
											$actif['hp'] -= $effet;
											echo '&nbsp;&nbsp;<span class="degat">'.$passif['nom'].' inflige '.$degats.' dégats grâce au bouclier de feu</span><br />';
										}
									if(array_key_exists('bouclier_eau', $passif['buff']))
										{
											$chances = $passif['buff']['bouclier_eau']['effet'] * 2;
											$diffi = 100;
											$att = rand(0, $chances);
											$def = rand(0, $diffi);
											if($att > $def)
												{
													echo '&nbsp;&nbsp;<span class="degat">'.$passif['nom'].' bloque et glace '.$actif['nom'].'</span><br />';
													$actif['etat']['paralysie']['effet'] = 1;
													$actif['etat']['paralysie']['duree'] = ($passif['buff']['bouclier_eau']['effet2'] + 1);
												}
										}
									
									/* Application des effets de blocage */
									foreach ($effects as $effect)
										$degat = $effect->applique_bloquage($actif,$passif,$degat);
									/* ~Blocage */

								}
						}
				}
      //Posture défensive
      if($passif['etat']['posture']['type'] == 'posture_defense') $buff_posture_defense = $passif['etat']['posture']['effet']; else $buff_posture_defense = 0;
      $degat = $degat - $buff_posture_defense;
      if($degat < 0) $degat = 0;
      //Diminution des dégats grâce à l'armure
      if(array_key_exists('benediction', $passif['etat'])) $buff_bene_bouclier = 1 + (($passif['etat']['benediction']['effet'] * $G_buff['bene_bouclier']) / 100); else $buff_bene_bouclier = 1;
      if(array_key_exists('berzeker', $passif['etat'])) $buff_berz_bouclier = 1 + (($passif['etat']['berzeker']['effet'] * $G_buff['berz_bouclier']) / 100); else $buff_berz_bouclier = 1;
      if(array_key_exists('batiment_pp', $passif['buff'])) $buff_batiment_bouclier = 1 + (($passif['buff']['batiment_pp']['effet']) / 100); else $buff_batiment_bouclier = 1;
      if(array_key_exists('acide', $passif['etat'])) $debuff_acide = 1 + (($passif['etat']['acide']['effet2']) / 100); else $debuff_acide = 1;
      if($passif['etat']['posture']['type'] == 'posture_pierre') $aura_pierre = 1 + (($passif['etat']['posture']['effet']) / 100); else $aura_pierre = 1;
      //Chance de transpercer l'armure
      $transperce = false;
      if($actif['etat']['posture']['type'] == 'posture_transperce')
				{
					$atta = $actif['etat']['posture']['effet'];
					$defe = 100;
					$att = rand(0, $atta);
					$defe = rand(0, $defe);
					if($att > $defe)
						{
							echo 'Le coup porté est tellement violent qu\'il transperce l\'armure !<br />';
							$transperce = true;
						}
				}
      //Si c'est une flèche rapide, chance d'ignorer l'armure
      if(array_key_exists('fleche_rapide', $actif['etat']))
				{
					$atta = $actif['etat']['fleche_rapide']['effet'];
					$defe = rand(0, 100);
					if($defe < $atta)
						{
							echo 'Le coup porté est tellement violent qu\'il transperce l\'armure !<br />';
							$transperce = true;
						}
				}
      if(array_key_exists('coup_mortel', $actif))
				{
					$degat = $degat * 4;
				}
      $PP = round(($passif['PP'] * $buff_bene_bouclier * $buff_batiment_bouclier * $aura_pierre) / ($buff_berz_bouclier * $debuff_acide));

      
      /* Application des effets de PP */
      foreach ($effects as $effect)
				$PP = $effect->calcul_pp($actif, $passif, $PP);
			$passif['PP_effective'] = $PP;
      /* ~PP */
      

      if(!$transperce) $reduction = calcul_pp($PP); else $reduction = 1;
      $degat_avant = $degat;
      $degat = round($degat * $reduction);
			
      //Coup critique
      $actif_chance_critique = ceil(pow($actif['dexterite'], 1.5) * 10);
      //Maitrise du critique
      if(array_key_exists('maitrise_critique', $actif['competences'])) $actif_chance_critique *= 1 + ($actif['competences']['maitrise_critique'] / 1000);
      //Buff du critique
      if(array_key_exists('buff_critique', $actif['buff'])) $actif_chance_critique *= 1 + (($actif['buff']['buff_critique']['effet']) / 100);
      if(array_key_exists('buff_cri_rage', $actif['buff'])) $actif_chance_critique *= 1 + (($actif['buff']['buff_cri_rage']['effet']) / 100);
      if(array_key_exists('benediction', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['benediction']['effet'] * $G_buff['bene_critique']) / 100);;
      if(array_key_exists('tir_vise', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['tir_vise']['effet'] * 5) / 100);
      if(array_key_exists('berzeker', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['berzeker']['effet'] * $G_buff['berz_critique']) / 100);
      if(array_key_exists('coup_sournois', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['coup_sournois']['effet']) / 100);
      if(array_key_exists('fleche_sanglante', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['fleche_sanglante']['effet']) / 100);
      if(array_key_exists('a_critique', $actif['etat'])) $actif_chance_critique *= 1 + (($actif['etat']['a_critique']['effet']) / 100);
      if(array_key_exists('b_critique', $actif['etat'])) $actif_chance_critique /= 1 + (($actif['etat']['b_critique']['effet']) / 100);
      //Elfe des bois
      if($actif['race'] == 'elfebois') $actif_chance_critique *= 1.15;
      if(array_key_exists('coup_mortel', $actif))
				{
					$actif_chance_critique *= 1.7;
					unset($actif['coup_mortel']);
				}
      //Enchantement critique
      if(array_key_exists('critique', $actif['enchantement'])) $actif_chance_critique *= 1 + (($actif['enchantement']['critique']['effet']) / 100);
      if($actif['etat']['posture']['type'] == 'posture_critique') $actif_chance_critique *= 1 + (($actif['etat']['posture']['effet']) / 100);

      /* Application des effets de chance critique */
      foreach ($effects as $effect)
				$actif_chance_critique = $effect->calcul_critique($actif, $passif, $actif_chance_critique);
      /* ~chance critique */

      $chance = rand(0, 10000);

      $critique = false;
      echo '
		<div id="debug'.$debugs.'" class="debug">
			Potentiel critique attaquant : '.$actif_chance_critique.' / 10000<br />
			Résultat => '.$chance.' doit être inférieur au Potentiel critique<br />
		</div>';
      $debugs++;
      if($chance < $actif_chance_critique)
				{
					echo '&nbsp;&nbsp;<span class="coupcritique">COUP CRITIQUE !</span><br />';
					//Chance de paralyser l'adversaire
					if($actif['etat']['posture']['type'] == 'posture_paralyse')
						{
							$atta = $actif['etat']['posture']['effet'];
							$defe = 100;
							$att = rand(0, $atta);
							$defe = rand(0, $defe);
							echo $att.' '.$defe.'<br />';
							if($att > $defe)
								{
									echo $passif['nom'].' est paralysé par ce coup !<br />';
									if(array_key_exists('paralysie', $passif['etat'])) $passif['etat']['paralysie']['duree']++;
									else
										{
											$passif['etat']['paralysie']['effet'] = 1;
											$passif['etat']['paralysie']['duree'] = 1;
										}
								}
						}
					//Art du critique : augmente les dégats fait par un coup critique
					if(array_key_exists('art_critique', $actif['competences'])) $art_critique = ($actif['competences']['art_critique'] / 100); else $art_critique = 0;
					//Buff Colère
					if(array_key_exists('buff_colere', $actif['buff'])) $buff_colere = ($actif['buff']['buff_colere']['effet']) / 100; else $buff_colere = 0;
					//Orc
					if($actif['race'] == 'orc') $bonuscritique_race = 1.05; else $bonuscritique_race = 1;
					if($passif['race'] == 'troll') $maluscritique_race = 1.2; else $maluscritique_race = 1;

					$multiplicateur = (2 + $art_critique + $buff_colere) * $bonuscritique_race / $maluscritique_race;

					/* Application des effets de multiplicateur critique */
					foreach ($effects as $effect)
						$multiplicateur = 
							$effect->calcul_mult_critique($actif, $passif, $multiplicateur);
					/* ~multiplicateur critique */

					echo "<div id=\"debug$debugs\" class=\"debug\">Dégats de base : $degat, multiplicateur : ".$multiplicateur."<br /></div>";
					$degat = round($degat * $multiplicateur);
					$degat_avant = round($degat_avant * $multiplicateur);
					$critique = true;
					if(array_key_exists('renouveau_energique', $actif['buff']))
						{
							$actif['reserve'] += $actif['buff']['renouveau_energetique']['effet'];
							echo $actif['nom'].' se ressaisi et gagne '.$actif['buff']['renouveau_energetique']['effet'].' RM<br />';
						}
					//if(array_key_exists('maitre_critique', $actif['competences'])) augmentation_competence('maitre_critique', $actif, 3);
				}
      $reduction = $degat_avant - $degat;
      echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif['nom'].'</strong> inflige <strong>'.$degat.'</strong> dégats</span><br />';
      if($reduction != 0) echo '&nbsp;&nbsp;<span class="small">(réduits de '.$reduction.' par l\'armure)</span><br />';
      //Si flêche étourdissante
      if($actif['etat']['fleche_etourdit'] > 0)
				{
					echo '&nbsp;&nbsp;<strong>'.$passif['nom'].'</strong> est étourdit par la flêche !<br />';
					$passif['etat']['etourdit']['duree'] = 2;
				}
      if(array_key_exists('buff_rage_vampirique', $actif['buff']))
				{
					$buff_rage_vampirique = $actif['buff']['buff_rage_vampirique']['effet'] / 100;
					$effet = round($degat * $buff_rage_vampirique);
					if(($actif['hp'] + $effet) > $actif['hp_max'])
						{
							$effet = $actif['hp_max'] - $actif['hp'];
						}
					$actif['hp'] += $effet;
					if($effet > 0) echo '&nbsp;&nbsp;<span class="soin">'.$actif['nom'].' gagne '.$effet.' HP par la rage vampirique</span><br />';
				}
      //Epines
      if(array_key_exists('buff_epine', $passif['buff']))
				{
					$buff_epine = $passif['buff']['buff_epine']['effet'] / 100;
					$effet = round($degat * $buff_epine);
					$actif['hp'] -= $effet;
					if($effet > 0) echo '&nbsp;&nbsp;<span class="degat">'.$passif['nom'].' renvoi '.$effet.' dégats grâce a Armure en épine</span><br />';
				}
      //Armure de glace
      if(array_key_exists('buff_armure_glace', $passif['buff']))
				{
					$chance = $passif['buff']['buff_armure_glace']['effet'];
					$de1 = rand(0, $chance);
					$de2 = rand(0, 100);
					if($de1 > $de2)
						{
							echo '&nbsp;&nbsp;<span class="degat">'.$passif['nom'].' glace '.$actif['nom'].' avec son armure de glace</span><br />';
							$actif['etat']['paralysie']['duree'] += 1;
						}
				}

			/* Application des effets de dégats infligés */
			foreach ($effects as $effect)
				$effect->inflige_degats($actif, $passif, $degat);
			/* ~Dégats infligés */

      $passif['hp'] = $passif['hp'] - $degat;
    }
  else
    {
      echo '&nbsp;&nbsp;<span class="manque">'.$actif['nom'].' manque la cible</span><br />';
    }
  if(array_key_exists('coup_mortel', $actif))
    {
      unset($actif['coup_mortel']);
    }
  if(array_key_exists('dissimulation', $actif['etat']))
    {
      unset($actif['etat']['dissimulation']);
    }
  //Augmentation des compétences liées
  if($actif['arme_type'] == 'arc' AND array_key_exists('maitrise_arc', $actif['competences']))
    {
      $actif['maitrise_arc'] = $actif['competences']['maitrise_arc'];
      $augmentation = augmentation_competence('maitrise_arc', $actif, 6);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_arc'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_arc'].' en '.$Gtrad['maitrise_arc'].'</span><br />';
					$ups[] = 'maitrise_arc';
				}
    }
  if($actif['arme_type'] == 'epee' AND array_key_exists('maitrise_epee', $actif['competences']))
    {
      $actif['maitrise_epee'] = $actif['competences']['maitrise_epee'];
      $augmentation = augmentation_competence('maitrise_epee', $actif, 6);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_epee'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_epee'].' en '.$Gtrad['maitrise_epee'].'</span><br />';
					$ups[] = 'maitrise_epee';
				}
    }
  if($actif['arme_type'] == 'hache' AND array_key_exists('maitrise_hache', $actif['competences']))
    {
      $actif['maitrise_hache'] = $actif['competences']['maitrise_hache'];
      $augmentation = augmentation_competence('maitrise_hache', $actif, 6);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_hache'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_hache'].' en '.$Gtrad['maitrise_hache'].'</span><br />';
					$ups[] = 'maitrise_hache';
				}
    }
  if($actif['arme_type'] == 'dague' AND array_key_exists('maitrise_dague', $actif['competences']))
    {
      $actif['maitrise_dague'] = $actif['competences']['maitrise_dague'];
      $augmentation = augmentation_competence('maitrise_dague', $actif, 6);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_dague'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_dague'].' en '.$Gtrad['maitrise_dague'].'</span><br />';
					$ups[] = 'maitrise_dague';
				}
    }
  if(array_key_exists('maitrise_critique', $actif['competences']) && $critique)
    {
      $actif['maitrise_critique'] = $actif['competences']['maitrise_critique'];
      $augmentation = augmentation_competence('maitrise_critique', $actif, 3.5);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['maitrise_critique'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['maitrise_critique'].' en '.$Gtrad['maitrise_critique'].'</span><br />';
					$ups[] = 'maitrise_critique';
				}
    }
  if(array_key_exists('art_critique', $actif['competences']) && $critique)
    {
      $actif['art_critique'] = $actif['competences']['art_critique'];
      $augmentation = augmentation_competence('art_critique', $actif, 3.5);
      if ($augmentation[1] == 1)
				{
					$actif['competences']['art_critique'] = $augmentation[0];
					if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif['competences']['art_critique'].' en '.$Gtrad['art_critique'].'</span><br />';
					$ups[] = 'art_critique';
				}
    }
  $diff_att = 3.2 * $G_round_total / 5;
  $diff_esquive = 3 * $G_round_total / 5;
  $diff_blocage = 2.5 * $G_round_total / 5;
  $augmentation = augmentation_competence($competence, $actif, $diff_att);
  if ($augmentation[1] == 1)
    {
      $actif[$competence] = $augmentation[0];
      if($acteur == 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$actif[$competence].' en '.$Gtrad[$competence].'</span><br />';
    }
  $augmentation = augmentation_competence('esquive', $passif, $diff_esquive);
  if ($augmentation[1] == 1)
    {
      $passif['esquive'] = $augmentation[0];
      if($acteur != 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$passif['esquive'].' en esquive</span><br />';
    }
  if($passif['bouclier'] AND ($attaque > $defense))
    {
      $augmentation = augmentation_competence('blocage', $passif, $diff_blocage);
      if ($augmentation[1] == 1)
				{
					$passif['blocage'] = $augmentation[0];
					if($acteur != 'attaquant') echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$passif['blocage'].' en blocage</span><br />';
				}
    }

	/* Ici on va enregistrer les etats précédents */
	// Enregistre si on a esquivé ou non
	$passif['precedent']['esquive'] = ($defense >= $attaque);
	// Enregistre si on a critiqué
	$actif['precedent']['critique'] = $critique;


	
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
}

function degat_magique($carac, $degat, $actif, $passif)
{
  global $debugs;
  echo '<div id="debug'.$debugs.'" class="debug">';
  $de_degat = de_degat($carac, $degat);
  $degat = 0;
  $i = 0;
  while($i < count($de_degat))
    {
      $de = rand(1, $de_degat[$i]);
      $degat += $de;
      echo 'Max : '.$de_degat[$i].' - Dé : '.$de.'<br />';
      $i++;
    }
  $debugs++;
  echo '</div>';
  if(critique_magique($actif, $passif))
    {
      $degat = degat_critique($actif, $passif, $degat);
    }
  //Diminution des dégats grâce à l'armure magique
  $reduction = calcul_pp(($passif['PM'] * $passif['puissance']) / 12);
  $degat_avant = $degat;
  $degat = round($degat * $reduction);
  echo '<div id="debug'.$debugs.'" class="debug">';
  echo '(Réduction de '.($degat_avant - $degat).' dégats par la PM)<br />';
  echo '</div>';
  $debugs++;
  return $degat;
}
?>