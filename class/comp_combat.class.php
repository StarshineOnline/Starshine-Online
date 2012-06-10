<?php
/**
 * @file comp_combat.class.php
 * Définition de la classe comp_combat servant de base aux compétences de combat
 */

/**
 * Classe comp_combat
 * Classe comp_combat servant de base aux compétences de combat
 */
class comp_combat extends comp
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $effet3;  ///< Troisième effet
	protected $etat_lie;  ///< État du personnage lié à cette compétence

  /// Renvoie le troisième effet
	function get_effet3()
	{
		return $this->effet3;
	}
	/// Modifie le troisième effet
	function set_effet3($effet3)
	{
		$this->effet3 = $effet3;
		$this->champs_modif[] = 'effet3';
	}

  /// Renvoie l'état du personnage lié à cette compétence
	function get_etat_lie()
	{
		return $this->etat_lie;
	}
	/// Modifie l'état du personnage lié à cette compétence
	function set_etat_lie($etat_lie)
	{
		$this->etat_lie = $etat_lie;
		$this->champs_modif[] = 'etat_lie';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id             Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type           Type générique.
	 * @param effet          Effet principal.
	 * @param duree          Durée
	 * @param comp_assoc     Compétence associée
	 * @param carac_assoc    Caractéristique associée
	 * @param comp_requis    Requis dans la compétence
	 * @param carac_requis   Requis dans la caractéristique (inutilisé)
	 * @param effet2         Deuxième effet
	 * @param requis         Compétence ou sort requis pour apprendre celui-ci
	 * @param cible          Cible de la compétence ou du sort
	 * @param description    Description du buff
	 * @param mp             Coût en MP ou en RM
	 * @param prix           Prix de la compétence ou le sort
	 * @param lvl_batiment   Niveau de l'école qui vent la compétence ou le sort
	 * @param arme_requis    Arme requise pour utiliser la compétence
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $effet3, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $arme_requis='', $etat_lie='')
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $arme_requis);
			$this->effet3 = $effet3;
			$this->etat_lie = $etat_lie;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp::init_tab($vals);
		$this->effet3 = $vals['effet3'];
		$this->etat_lie = $vals['etat_lie'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp::get_liste_champs().', effet3, ';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp::get_valeurs_insert().', '.$this->effet3.', '.$this->etat_lie;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp::get_liste_update().', effet3 = '.$this->effet3.', etat_lie = "'.mysql_escape_string($this->etat_lie).'"';
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM comp_jeu WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      default:
        return new comp_combat($row);
      }
  	}
  }
  
  /// Méthode renvoyant une instance correspondant à une attaque simple
  static function creer_attaque()
  {
    return new comp_combat(0, 'attaque', 'attaque');
  }
	// @}


	/**
	 * @name Gestion du lancement
	 * Méthodes utilisées lors de l'utilisation (lancement) de la compétence / du sort
	 */
  // @{
  /**
   * Méthode gérant l'utilisation d'une compétence
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function lance(&$actif, &$passif, &$effets)
  {
    global $log_combat $G_round_total, $comp_attaque;

  	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()),
													'passif' => array('comp' => array(), 'comp_perso' => array()));
  	$ups = array();

  	//Réctification si c'est un orc ou un donjon
		$round = is_donjon($actif->get_x(), $actif->get_y()) ? $G_round_total * 2 : $G_round_total;
		if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
			$round += 1;
		$rectif_augm = $round / $G_round_total;
		
		$log_combat .= 'c'.$this->get_id();
  	// Application des effets de début de round
  	foreach($effets as $effet)
  		$effet->debut_round($actif, $passif);
  		
    // Test pour toucher
  	$potentiel_toucher = $actif->get_potentiel_toucher();
		foreach($effets as $effet)
			$potentiel_toucher = $effet->calcul_attaque_physique($actif, $passif, $potentiel_toucher);
  	$potentiel_parer = $passif->get_potentiel_parer();
		foreach($effets as $effet)
			$potentiel_parer = $effet->calcul_defense_physique($actif, $passif, $potentiel_parer);
    if( $this->test_potentiel($potentiel_toucher, $potentiel_parer) )
    {
      $this->touche($actif, $passif, $effects);
      $passif->precedent['esquive'] = false;
    }
  	else
    {
  		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible</span><br />';
      $passif->precedent['esquive'] = true;
  		$log_combat .= '~e';
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

		if($comp_attaque)
		{
			$aug2 = attaque($mode, ${$mode}->get_comp_att(), $effects);
			$augmentations = merge_augmentations($augmentations, $aug2);
			$count = count($ups);
			if($count > 0)
			{
				$upi = 0;
				while($upi < $count)
				{
					$requete = "UPDATE comp_perso SET valeur = ".${$mode}['competences'][$ups[$upi]]." WHERE id_perso = ".${$mode}->get_id()." AND competence = '".$ups[$upi]."'";
					$db->query($requete);
					$upi++;
				}
			}
		}
		
  	/*if ($acteur == 'attaquant')
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
    }*/
	 return $augmentation;
  }
  
  /**
   * Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function touche(&$actif, &$passif, &$effets)
  {
    global $log_combat;
    $degat = calcul_degats($actif, $passif, $effects);
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
  	// Application des effets de degats
  	foreach($effets as $effet)
			$degat = $effet->calcul_degats($actif, $passif, $degat);
			
    if($passif->bouclier())
      $degat = $this->bouclier($degat, $actif, $passif, $effects);
   	//Posture défensive
    if($passif->etat['posture']['type'] == 'posture_defense') $buff_posture_defense = $passif->etat['posture']['effet']; else $buff_posture_defense = 0;
    $degat = $degat - $buff_posture_defense;
    if($degat < 0) $degat = 0;
    
    if(array_key_exists('coup_mortel', $actif->etat))
		{
			$degat = $degat * 3;
		}
    
    $reduction = $this->armure($actif, $passif, $effects);
    $degat_avant = $degat;
    $degat = round($degat * $reduction);

	  // Coup critique
	  $multiplicateur = $this->critiques($actif, $passif, $effects);
		print_debug("Dégâts de base : $degat, multiplicateur : $multiplicateur<br />");
		$degat = round($degat * $multiplicateur);
		$degat_avant = round($degat_avant * $multiplicateur);
		
		$reduction = $degat_avant - $degat;
    echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degat.'</strong> dégâts</span><br />';
    $log_combat .= '~'.$degat;
    if($reduction != 0)
      echo '&nbsp;&nbsp;<span class="small">(réduits de '.$reduction.' par l\'armure)</span><br />';

		// Application des effets de dégâts infligés
		foreach($effets as $effet)
			$degat = $effet->inflige_degats($actif, $passif, $degat);
    $passif->set_hp($passif->get_hp() - $degat);
  }
  
  /**
   * Méthode calculant les dégâts de base avant réduction
   * @param  $actif   Personnage utuilisant la compétence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function calcul_degats(&$actif, &$passif, &$effets)
  {
			if(array_key_exists('tir_vise', $actif->etat)) $buff_vise_degat = $actif->etat['tir_vise']['effet'] + 1; else $buff_vise_degat = 1;
			if($actif->etat['posture']['type'] == 'posture_degat') $buff_posture_degat = $actif->etat['posture']['effet']; else $buff_posture_degat = 0;
			$arme_degat = ($actif->get_arme_degat() + $buff_posture_degat) * $buff_vise_degat;

			// Application des effets de boost des armes
			foreach($effets as $effet)
				$arme_degat = $effect->calcul_arme($actif, $passif, $arme_degat);
			$de_degat = $this->calcule_des($actif->get_force(), $arme_degat);
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
			return $degat;
  }
  
  /**
   * Méthode gérant l'action du bouclier
   * @param  $degat   Dégâts avant action du bouclier
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function bouclier($degat, &$actif, &$passif, &$effets)
  {
			// Si c'est une flèche rapide, on ignore le blocage
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

				// Application des effets de blocage
				foreach($effets as $effet)
					$effet->calcul_bloquage($actif, $passif);

				// Blocage
				$blocage = rand(0, $passif->potentiel_bloquer);
				print_debug('Potentiel bloquer défenseur : '.
										$passif->potentiel_bloquer.'<br />Attaque : '.
										$attaque.'<br />Résultat => '.$blocage.' VS '.
										$attaque.'<br />');
				//Si le joueur bloque
				if ($attaque <= $blocage)
				{
					$degat_bloque = $passif->get_bouclier_degat();
					if($passif->is_buff('bouclier_terre')) $degat_bloque += $passif->get_buff('bouclier_terre', 'effet');

					// Application des degats bloques
					foreach($effets as $effet)
						$degat_bloque = $effet->calcul_bloquage_reduction($actif, $passif, $degat_bloque);

					// degats bloques

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

					// Application des effets de blocage
					foreach($effets as $effet)
						$degat = $effet->applique_bloquage($actif,$passif,$degat);

				}
				$diff_blocage = (2.5 * $G_round_total / 5) * $rectif_augm;
			  $augmentation['passif']['comp'][] = array('blocage', $diff_blocage);
			  if($passif->is_competence('maitrise_bouclier'))
				  $augmentation['passif']['comp_perso'][] = array('maitrise_bouclier', 6 * $rectif_augm);
			}
			return $degat;
  }

  /**
   * Méthode gérant l'action de l'armure
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function armure(&$actif, &$passif, &$effets)
  {
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
    $PP = round(($passif->get_pp() * $buff_bene_bouclier * $buff_batiment_bouclier * $aura_pierre) / ($buff_berz_bouclier * $debuff_acide));

    // Application des effets de PP
    foreach($effets as $effet)
			$PP = $effet->calcul_pp($actif, $passif, $PP);
		$passif->PP_effective = $PP;
		
    if(!$transperce) $reduction = calcul_pp($PP);
		else
		{
			$reduction = 1;
			echo 'Le coup porté est tellement violent qu\'il transperce l\'armure !<br />';
		}
    if ($reduction < 0) $reduction = 0; // On se soigne pas avec l'armure ^^
    return $reduction;
  }

  /**
   * Méthode gérant les coups critiques
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function critiques(&$actif, &$passif, &$effets)
  {
    global $log_combat;
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

    // Application des effets de chance critique
    foreach($effets as $effet)
		  $actif_chance_critique = $effet->calcul_critique($actif, $passif, $actif_chance_critique);

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

  		// Application des effets de multiplicateur critique
  		foreach($effets as $effet)
  			$multiplicateur = $effet->calcul_mult_critique($actif, $passif, $multiplicateur);
  		if(array_key_exists('renouveau_energique', $actif->buff) && $actif->get_arme_type() == 'arc')
  		{
  			$actif->set_rm_restant($actif->get_rm_restant() + $actif->get_buff('renouveau_energique', 'effet'));
  			echo $actif->get_nom().' se ressaisi et gagne '.$actif->get_buff('renouveau_energique', 'effet').' RM<br />';
  		}
  		$actif->precedent['critique'] = true;
  		return $multiplicateur;
    }
    else
    {
  		$actif->precedent['critique'] = false;
      return 1;
    }
  }
	// @}
}

?>
