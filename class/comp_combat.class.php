<?php
/**
 * @file comp_combat.class.php
 * Définition de la classe comp_combat servant de base aux compétences de combat
 */
include_once(root.'class/competence.class.php');

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
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
  	global $Gtrad;
    if($complet)
    {/// @todo à utiliser
      return array('Description', 'RM', 'Effet', $Gtrad[$this->comp_requis], 'Cible', 'Durée'/*, 'Prix HT (en magasin)'*/);
    }
    else ///@todo à faire (et à utiliser pour la liste d'achat)
      return array('Stars');
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
  	global $Gtrad;
    $vals = array($this->get_description(true), $this->mp, $this->effet, $this->comp_requis, $Gtrad['cible'.$this->cible], 
		$this->duree ? $this->duree.' rounds' : 'instantané'/*, $this->prix*/);
    return $vals;
  }

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
    $effet2=0, $effet3=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $arme_requis='', $etat_lie='')
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
    return comp::get_liste_champs().', effet3';
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
   * Vérifie si un personnage a les pré-requis pour le sort ou la compétence 
   * @param $perso   personnage concerné
   */
  function verif_prerequis(&$perso, $txt_action=false)
  {
  	$res = parent::verif_prerequis($perso, $txt_action);
  	return $res && $this->verif_requis($perso->get_comp_combat(), 'cette compétence', $txt_action);
	}
  /**
   * Vérifie si un personnage connait le sort ou la compétence 
   * @param $perso   personnage concerné
   */
  function est_connu(&$perso, $erreur=false)
  {
  	if( in_array($this->get_id(),  explode(';', $perso->get_comp_combat())) )
  		return true;
  	if( $erreur )
  		interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne connaissez pas cette compétence !');
  	return false;
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM comp_combat WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      case 'tir_precis':
      case 'feinte':
        return new comp_combat_toucher($row);
      case 'coup_puissant':
      case 'coup_violent':
      case 'tir_puissant':
        return new comp_combat_degats($row);
      case 'coup_mortel': // à revoir
        return new comp_combat_degat_etat($row, 'v-coup_mortel', 1);
      case 'coup_sournois': // à revoir
        return new comp_combat_degat_etat($row, 'v-coup_sournois', 1);
      case 'fleche_sanglante': // à revoir
        return new comp_combat_degat_etat($row, 'v-fleche_sanglante', 1);
      case 'attaque_vicieuse':
        return new comp_combat_vicieuse($row);
      case 'berzeker':
        return new comp_combat_etat($row);
      case 'bouclier_protecteur':
        return new comp_combat_etat($row);
      case 'tir_vise':
        return new comp_combat_etat($row, 'v-tir_vise', 2);
      case 'fleche_etourdissante':
        return new comp_combat_etourdi($row);
      case 'fleche_magnetique': // à revoir
        return new comp_combat_effet($row, new fleche_magnetique($row['effet2'], $row['effet']));
      case 'fleche_poison':
      case 'vol_a_la_tire':
      case 'botte_scorpion':
      case 'botte_aigle':
      case 'botte_crabe':
      case 'botte_chat':
      case 'botte_chien':
      case 'botte_scolopendre':
      case 'botte_tortue':
      case 'botte_rhinoceros':
      case 'botte_tigre':
      case 'botte_ours':
        return new comp_combat_effet($row);
      case 'fleche_rapide': // à revoir
        return new comp_combat_degat_etat($row, 'v-fleche_rapide', 1);
      case 'fleche_debilitante':
      case 'fleche_sable':
        return new comp_combat_degat_etat($row);
      case 'coup_bouclier': // à revoir
        return new comp_combat_coup_bouclier($row);
      case 'slam': // plus utilisé apparement
        return new comp_combat_etourdi($row, false);
      case 'frappe_derniere_chance':
        return new comp_combat_der_chance($row);
      case 'posture_critique':
      case 'posture_esquive':
      case 'posture_defense':
      case 'posture_degat':
      case 'posture_transperce':
      case 'posture_paralyse':
      case 'posture_touche':
        return new comp_combat_posture($row);
      case 'dissimulation':
        return new comp_combat_dissim($row);
      case 'attaque_cote':
      case 'attaque_rapide':
        return new comp_combat_pot($row);
      case 'attaque_brutale':
        return new comp_combat_deg_pot($row);
      default:
        $classe = 'comp_combat_'.$row['type'];
        if( class_exists($classe) )
          return new $classe($row);
        interf_debug::enregistre('Compétence non gérée : '.$row['type'].'<br/>');
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
  function lance(&$attaque)
  {
    global $G_round_total, $comp_attaque;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();

    $passif->precedent['bouclier'] = false;
		//$log_combat .= 'c'.$this->get_id();
    $attaque->add_log_combat('c'.$this->get_id());
  	// Application des effets de début de round
    $attaque->applique_effet('debut_round');

    $attaque->get_interface()->competence($this->get_type(), $actif->get_nom(), $this->get_nom());
    // Test pour toucher
  	$potentiel_toucher = $actif->get_potentiel_toucher();
    $attaque->applique_effet('calcul_attaque_physique', $potentiel_toucher);
  	$potentiel_parer = $passif->get_potentiel_parer();
    $attaque->applique_effet('calcul_defense_physique', $potentiel_parer);
    if( $this->test_potentiel($potentiel_toucher, $potentiel_parer, $attaque) )
    {
      $this->touche($attaque);
      $passif->precedent['esquive'] = false;
      $actif->precedent['touche'] = true;
    }
  	else
    {
    	$attaque->applique_effet('rate');
    	$attaque->get_interface()->manque($actif->get_nom());
      $passif->precedent['esquive'] = true;
      $passif->precedent['bloque'] = false;
      $actif->precedent['critique'] = false;
      $actif->precedent['touche'] = false;
      $attaque->add_log_combat('~e');
    }

	 return $this->get_augmentations($actif, $passif);
  }

  /**
   * Méthode gérant ce qu'il se passe lorsque la compétence à été utilisé avec succès
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function touche(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    global $log_combat, $G_buff;
    $degat = $this->calcul_degats($attaque);
    if($passif->type2 == 'batiment' AND $actif->get_race() == 'barbare') $degat = floor($degat * 1.4);
    $degat = $degat + $actif->degat_sup - $actif->degat_moins;
  	if($degat < 0) $degat = 0;
  	if(array_key_exists('benediction', $actif->etat)) $buff_bene_degat = $actif->etat['benediction']['effet'] * $G_buff['bene_degat']; else $buff_bene_degat = 0;
  	if(array_key_exists('berzeker', $actif->etat)) $buff_berz_degat = $actif->etat['berzeker']['effet'] * $G_buff['berz_degat']; else $buff_berz_degat = 0;
  	if(array_key_exists('berzeker', $passif->etat)) $buff_berz_degat_r = $passif->etat['berzeker']['effet'] * $G_buff['berz_degat_recu']; else $buff_berz_degat_r = 0;
  	if($actif->etat['posture']['type'] == 'posture_degat') $buff_posture_degat = $actif->etat['posture']['effet']; else $buff_posture_degat = 0;
		if($actif->is_buff('buff_force')) $buff_force = $actif->get_buff('buff_force', 'effet'); else $buff_force = 0;
  	if($actif->is_buff('buff_cri_victoire')) $buff_cri_victoire = $actif->get_buff('buff_cri_victoire', 'effet'); else $buff_cri_victoire = 0;
  	if($actif->is_buff('fleche_tranchante') && $actif->get_arme_type() == 'arc') $degat += $actif->get_buff('fleche_tranchante', 'effet');
  	if($actif->is_buff('oeil_chasseur') && $passif->get_espece() == 'bete' && $actif->get_arme_type() == 'arc') $degat += $actif->get_buff('oeil_chasseur', 'effet');
  	if($actif->is_buff('potion_inerte') && $passif->get_espece() == 'magique')
			$degat += $actif->get_buff('potion_inerte', 'effet');
  	if($actif->is_buff('potion_tueuse_homme') && $passif->get_espece() == 'humanoide')
			$degat += $actif->get_buff('potion_tueuse_homme', 'effet');
  	if($actif->is_buff('potion_tueuse_bete') && $passif->get_espece() == 'bete')
			$degat += $actif->get_buff('potion_tueuse_bete', 'effet');
		if($actif->is_buff('potion_berzerk'))
			$degat += $actif->get_buff('potion_berzerk', 'effet');
		if($passif->is_buff('potion_berzerk'))
			$degat += $passif->get_buff('potion_berzerk', 'effet2');
		if($actif->is_buff('potion_force'))
			$degat += $actif->get_buff('potion_force', 'effet');
    $degat = $degat + $buff_bene_degat + $buff_berz_degat + $buff_berz_degat_r + $buff_posture_degat + $buff_force + $buff_cri_victoire;
  	if($actif->is_buff('maladie_mollesse')) $degat = ceil($degat / (1 + ($actif->get_buff('maladie_mollesse', 'effet') / 100)));
    $attaque->set_degats($degat);
  	// Application des effets de degats
    $attaque->applique_effet('calcul_degats');

    if($passif->bouclier())
      $this->bouclier($attaque);
      
   	//Posture défensive
    if($passif->etat['posture']['type'] == 'posture_defense')
      $attaque->add_degats(-$passif->etat['posture']['effet']);

    if(array_key_exists('coup_mortel', $actif->etat))
		{
      $attaque->mult_degats(3);
		}

    $reduction = $this->armure($attaque);
    $degat_avant = $attaque->get_degats();
    $attaque->mult_degats($reduction);

	  // Coup critique
	  $multiplicateur = $this->critiques($attaque);
		interf_debug::enregistre('Dégâts de base : '.$attaque->get_degats().', multiplicateur : '.$multiplicateur);
    $attaque->mult_degats($multiplicateur);
		$degat_avant = round($degat_avant * $multiplicateur);
		if( $multiplicateur > 1 )
			$attaque->applique_effet('inflige_critique');
			
		// Potion d'épiderme
    if($passif->is_buff('potion_epiderme', true))
    	$attaque->add_degats( -$passif->get_buff('potion_epiderme', 'effet') );

    $degat = $attaque->get_degats();
		$reduction = $degat_avant - $degat;
    $attaque->get_interface()->degats($degat, $actif->get_nom());
    $attaque->add_log_combat('~'.$degat);
    if($reduction != 0)
    	$attaque->get_interface()->reduction($reduction);

    if($actif->is_buff('buff_rage_vampirique', true))
		{
			$buff_rage_vampirique = $actif->get_buff('buff_rage_vampirique', 'effet') / 100;
			$effet = round($degat * $buff_rage_vampirique);
			if(($actif->get_hp() + $effet) > $actif->get_hp_max())
			{
				$effet = floor($actif->get_hp_max() - $actif->get_hp());
			}
			// Augmentation du nombre de HP récupérable par récupération
			if(array_key_exists('recuperation', $actif->etat))
				$actif->etat['recuperation']['hp_max'] += $effet;
			$actif->set_hp($actif->get_hp() + $effet);
			if($effet > 0)
			{
				$attaque->get_interface()->effet(8, $effet, $actif->get_nom(), $passif->get_nom());
				$attaque->add_log_effet_actif('&ef8~'.$effet);
			}
		}
	  //Epines
    if($passif->is_buff('buff_epine', true))
		{
			$buff_epine = $passif->get_buff('buff_epine', 'effet') / 100;
			$effet = round($degat * $buff_epine);
			$actif->set_hp($actif->get_hp() - $effet);
			if($effet > 0)
			{
				$attaque->get_interface()->effet(9, $effet, $actif->get_nom(), $passif->get_nom());
				$attaque->add_log_effet_passif('&ef9~'.$effet);
			}
		}
	  //Armure de glace
    if($passif->is_buff('buff_armure_glace', true))
		{
			$chance = $passif->get_buff('buff_armure_glace', 'effet');
			$de1 = rand(0, $chance);
			$de2 = rand(0, 100);
			if($de1 > $de2)
			{
				$attaque->get_interface()->effet(11, 1, $actif->get_nom(), $passif->get_nom());
				$attaque->add_log_effet_passif('&ef11~1');
				$actif->etat['paralysie']['duree'] += 1;
			}
		}
		// Potion du moustique / vampire
    if( $actif->is_buff('potion_moustique') )
    {
    	$hp = $actif->get_buff('potion_moustique', 'effet');
    	$actif->add_hp( $hp );
			$attaque->get_interface()->effet(20,  $hp, '', $rm->get_nom());
			$attaque->add_log_effet_actif('&ef20~'.$hp);
		}

		// Application des effets de dégâts infligés
    $attaque->applique_effet('inflige_degats');
    $passif->set_hp($passif->get_hp() - $degat);
    // Potion de poison
    if( $actif->is_buff('potion_poison') )
    {
    	$passif->etat['poison']['effet'] = $actif->get_buff('potion_poison', 'effet');
    	$passif->etat['poison']['duree'] = $actif->get_buff('potion_poison', 'effet');
		}
		if( $actif->is_buff('potion_affaiblissement') )
		{
    	$passif->etat['affaiblissement']['effet'] += $actif->get_buff('potion_affaiblissement', 'effet');
    	$passif->etat['affaiblissement']['duree'] = 21;
		}
  }

  /**
   * Méthode calculant les dégâts de base avant réduction
   * @param  $actif   Personnage utuilisant la compétence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function calcul_degats(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
		if(array_key_exists('tir_vise', $actif->etat)) $buff_vise_degat = $actif->etat['tir_vise']['effet'] + 1; else $buff_vise_degat = 1;
		$arme_degat = $actif->get_arme_degat() * $buff_vise_degat;

		// Application des effets de boost des armes
    $attaque->applique_effet('calcul_arme', $arme_degat);
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
		interf_debug::enregistre($dbg_msg);
		return $degat;
  }

  /**
   * Méthode gérant l'action du bouclier
   * @param  $degat   Dégâts avant action du bouclier
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function bouclier(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    $degat =$attaque->get_degats();
		// Si c'est une flèche rapide, on ignore le blocage
		if(array_key_exists('fleche_rapide', $actif->etat))
		{
		}
		else
		{
			$passif->get_potentiel_bloquer();

			// Application des effets de blocage
      $attaque->applique_effet('calcul_bloquage');

			// Blocage
      $jet_toucher = $attaque->get_jet();
			$blocage = rand(0, $passif->get_potentiel_bloquer());
			$dbg = interf_debug::enregistre();
			$dbg->add_message( 'Potentiel bloquer défenseur : '.$passif->get_potentiel_bloquer() );
			$dbg->add_message( 'Potentiel bloquer défenseur : '.$passif->get_potentiel_bloquer() );
			$dbg->add_message( 'Attaque : '.$jet_toucher );
			$dbg->add_message( 'Résultat => '.$blocage.' VS '.$jet_toucher );
			//Si le joueur bloque
			if ($jet_toucher <= $blocage)
			{
				$degat_bloque = $passif->get_bouclier_degat();
				if($passif->is_buff('bouclier_terre')) $degat_bloque += $passif->get_buff('bouclier_terre', 'effet');
        $attaque->set_degats_bloques($degat_bloque);

				// Application des degats bloques
        $attaque->applique_effet('calcul_bloquage_reduction');

				// degats bloques
        $attaque->applique_blocage();
        $degat_bloque = $attaque->get_degats_bloques();
        $attaque->get_interface()->bloque($degat_bloque, $passif->get_nom());
				if($passif->is_buff('bouclier_feu'))
				{
					$degats_feu = ceil( $degat_bloque * $passif->get_buff('bouclier_feu', 'effet') / 100 );
					$actif->set_hp($actif->get_hp() - $degats_feu);
					$attaque->get_interface()->effet(12, $degats_feu, $actif->get_nom(), $passif->get_nom());
					$attaque->add_log_effet_passif('&ef12~'.$degats_feu);
				}
				if( $buff = $passif->get_buff('bouclier_eau') )
				{
					if( $this->test_de(100, $buff->get_effet()) )
					{
						$attaque->get_interface()->effet(13, 1, $actif->get_nom(), $passif->get_nom());
						$attaque->add_log_effet_passif('&ef13~1');
						$actif->etat['glace']['effet'] = true;
						$actif->etat['glace']['duree'] = $buff->get_effet2()+1; // +1 car ce round est décompté alors qu'il ne compte pas
					}
				}

				// Application des effets de blocage
        $attaque->applique_effet('applique_bloquage');
					
        $passif->precedent['bloque'] = true;
			}
			else
        $passif->precedent['bloque'] = false;
			$passif->precedent['bouclier'] = true;
		}
  }

  /**
   * Méthode gérant l'action de l'armure
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function armure(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
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
    $attaque->applique_effet('calcul_pp', $PP);
		$passif->PP_effective = $PP;

    if(!$transperce) $reduction = calcul_pp($PP);
		else
		{
			$attaque->get_interface()->special('sv');
			$reduction = 1;
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
  function critiques(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    global $log_combat;
  	$actif_chance_critique = $actif->get_potentiel_critique();


    // Application des effets de chance critique
    $attaque->applique_effet('calcul_critique', $actif_chance_critique);

    if( $this->test_de(10000, $actif_chance_critique) )
  	{
  		$actif->set_compteur_critique();
  		$attaque->get_interface()->critique();
      $attaque->add_log_combat('!');
  		//Chance de paralyser l'adversaire
  		if($actif->etat['posture']['type'] == 'posture_paralyse')
  		{
  			$atta = $actif->etat['posture']['effet'];
  			$att = rand(0, 100);
  			if($att <= $atta)
  			{
					$attaque->get_interface()->effet(14, 1, $actif->get_nom(), $passif->get_nom());
					$attaque->add_log_effet_passif('&ef14~1');
  				if(array_key_exists('paralysie', $passif->etat))
						$passif->etat['paralysie']['duree']++;
  				else
  				{
  					$passif->etat['paralysie']['effet'] = 1;
  					$passif->etat['paralysie']['duree'] = 1;
  				}
  			}
  		}
  		
  		$maluscritique_race = $passif->get_race() == 'troll' ? 1.2 : 1;
  		$multiplicateur = $actif->get_mult_critique() / $maluscritique_race;

  		// Application des effets de multiplicateur critique
      $attaque->applique_effet('calcul_mult_critique', $multiplicateur);
  		if(array_key_exists('renouveau_energique', $actif->buff) && $actif->get_arme_type() == 'arc')
  		{
  			$actif->set_rm_restant($actif->get_rm_restant() + $actif->get_buff('renouveau_energique', 'effet'));
				$attaque->get_interface()->effet(15, $actif->get_buff('renouveau_energique', 'effet'), $actif->get_nom(), $passif->get_nom());
				$attaque->add_log_effet_passif('&ef15~'.$actif->get_buff('renouveau_energique', 'effet'));
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
  /**
   * Donne la liste des compétences pouvant augmenter.
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   */
  function get_augmentations(&$actif, &$passif)
  {
    global $G_round_total;
    
  	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()),
													'passif' => array('comp' => array(), 'comp_perso' => array()));
  	$ups = array();

    // code rapide pour empêcher les ads de faire augmenter la mêlée, à revoir
    if( $actif->get_type() == 'siege' ) return $augmentation;

  	//Réctification si c'est un orc ou un donjon
		$round = ($actif->get_y() > 190) ? $G_round_total * 2 : $G_round_total;
		if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
			$round += 1;
		$rectif_augm = $round / $G_round_total;

  	//Augmentation des compétences de base
  	$diff_att = (3.2 * $G_round_total / 5) * $rectif_augm;
  	$augmentation['actif']['comp'][] = array($actif->get_comp_att(), $diff_att);
  	$diff_esquive = (2.7 * $G_round_total / 5) * $rectif_augm;
  	$augmentation['passif']['comp'][] = array('esquive', $diff_esquive);

    // bouclier
    if( $passif->precedent['bouclier'] )
    {
      $diff_blocage = (2 * $G_round_total / 5) * $rectif_augm;
			$augmentation['passif']['comp'][] = array('blocage', $diff_blocage);
			if($passif->is_competence('maitrise_bouclier'))
				$augmentation['passif']['comp_perso'][] = array('maitrise_bouclier', 6 * $rectif_augm);
    }

  	//Augmentation des compétences liées
  	if( isset($actif->precedent['critique']) && $actif->precedent['critique'] )
  	{
    	if( $actif->is_competence('art_critique') )
    		$augmentation['actif']['comp_perso'][] = array('art_critique', 2.5 * $rectif_augm);
    	if( $actif->is_competence('maitrise_critique') )
    		$augmentation['actif']['comp_perso'][] = array('maitrise_critique', 2 * $rectif_augm);
    }
  	$arme = $actif->get_arme_type();
  	if($actif->is_competence("maitrise_$arme"))
  		$augmentation['actif']['comp_perso'][] = array("maitrise_$arme", 6 * $rectif_augm);

    return $augmentation;
  }
	// @}
}

/// Classe gérant les compétences donnant un bonus au potentiel toucher (avec éventuellement un bonus au potentiel toucher critique)
class comp_combat_toucher extends comp_combat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 + ($this->get_effet() / 100)));
    if( $this->get_effet2() )
      $actif->set_potentiel_critique($actif->get_potentiel_critique() * (1 + ($this->get_effet2() / 100)));
    return parent::lance($attaque);
  }
}

/// Classe gérant les compétences donnant un bonus aux dégâts (avec éventuellement un malus au potentiel toucher en effet2)
class comp_combat_degats extends comp_combat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $actif->degat_sup = $this->get_effet();
    if( $this->get_effet2() )
      $actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 - ($this->get_effet2() / 100)));
    return parent::lance($attaque);
  }
}

/// Classe gérant les compétences à état en plus des dégâts
class comp_combat_degat_etat extends comp_combat
{
  protected $etat; ///< État à ajouter si le sort touche
  protected $duree_etat; ///< Durée de l'état (null s'il faut prendre le paramètre duree)
  protected $effet_etat; ///< Effet de l'état (null s'il faut prendre le paramètre effet2)
  protected $autocible;  /// true si la cible est soi-même, false si c'est l'ennemi
  function __construct($tbl, $etat=null, $duree=null, $effet=null)
  {
    $this->charger($tbl);
    $this->duree_etat = $duree;
    $this->effet_etat = $effet;
    
    if( $etat === null )
      $etat_explode = explode('-', $tbl['etat_lie']);
    else
      $etat_explode = explode('-', $etat);
		$this->etat = $etat_explode[1];
		$this->autocible = $etat_explode[0][0] == 'v';
  }
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    if( $this->autocible )
      $this->ajout_etat($attaque);
    return parent::lance($attaque);
  }

  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$attaque)
  {
    if( !$this->autocible )
      $this->ajout_etat($attaque);
    return parent::touche($attaque);
  }

  /// Ajoute l'état
  protected function ajout_etat(&$attaque)
  {
		if( $this->autocible )
      $cible = &$attaque->get_actif();
    else
      $cible = &$attaque->get_passif();
    if( $this->effet_etat === null )
      $cible->etat[$this->etat]['effet'] =  $this->get_effet();
    else
      $cible->etat[$this->etat]['effet'] = $this->effet_etat;
		if( $this->duree_etat === null )
      $cible->etat[$this->etat]['duree'] =  $this->get_duree();
    else
      $cible->etat[$this->etat]['duree'] = $this->duree_etat;
    $cible->etat[$this->etat]['effet2'] = $this->get_effet2();
  }
}

/// Classe gérant les compétences à état sans dégâts
class comp_combat_etat extends comp_combat_degat_etat
{
  function __construct($tbl, $etat=null, $duree=null, $effet=null)
  {
    parent::__construct($tbl, $etat, $duree, $effet);
  }
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $attaque->add_log_combat('c'.$this->get_id());
    $this->ajout_etat($attaque);
    $attaque->get_interface()->competence($this->get_type(), $actif->get_nom(), $this->get_nom());
    $passif->precedent['critique'] = false;
    return $this->get_augmentations($actif, $passif);
  }
}

/// Classe gérant les compétences attaque vicieuse
class comp_combat_vicieuse extends comp_combat_toucher
{
  /// Méthode calculant les dégâts de base avant réduction
  function calcul_degats(&$attaque)
  {
    $passif = &$attaque->get_passif();
		$passif->etat['hemorragie']['duree'] = 5;
		$passif->etat['hemorragie']['effet'] = $this->get_effet2();
		$attaque->get_interface()->special('sh');
    return parent::calcul_degats($attaque) + $this->get_effet2();
  }
}

/// Classe gérant les compétences flèche étourdissante
class comp_combat_etourdi extends comp_combat_degat_etat
{
  protected $message;  ///< Indique si on affiche un message ou non
  /// Constructeur
  function __construct($tbl, $msg=true)
  {
    parent::__construct($tbl);
    $this->message = $msg;
  }
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $actif->degat_moins = $this->get_effet();
    return comp_combat::lance($attaque);
  }
  /// Méthode gérant ce qu'il se passe lorsque la compétence à été utilisé avec succès
  function touche(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    comp_combat::touche($attaque);
    $pot_att = ($actif->get_force() + $actif->get_dexterite()) / 2;
		$pot_deff = $passif->get_vie();
    $attaque->applique_effet('resite_etourdissement', $pot_deff);
    if( $this->test_potentiel($pot_att, $pot_deff) )
    {
      $this->ajout_etat($attaque);
      if( $this->message )
      {
				$attaque->get_interface()->effet(16, 1, $actif->get_nom(), $passif->get_nom());
				$attaque->add_log_effet_passif('&ef16~1');
			}
    }
  }
}

/// Classe gérant les compétences utilisant des effets
class comp_combat_effet extends comp_combat
{
  protected $obj_eff;  ///< Classe de l'effet
  /// Constructeur
  function __construct($tbl, $effet=null)
  {
    $this->charger($tbl);
    $this->obj_eff = $effet;
  }
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $effets = &$attaque->get_effets();
    if( is_object($this->obj_eff) )
      $effets[] = $this->obj_eff;
    else
    {
      if( is_string($this->obj_eff) )
        $classe = $this->obj_eff;
      else
        $classe = $this->get_type();
      $effets[] = new $classe($this->get_effet(), $this->get_effet2(), $this->get_duree());
    }
    return parent::lance($attaque);
  }
}

/// Classe gérant les compétences frappe de la dernière chance
class comp_combat_der_chance extends comp_combat_degat_etat
{
  /// Constructeur
  function __construct($tbl)
  {
    parent::__construct($tbl, 'v-derniere_chance');
  }
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $this->ajout_etat($attaque);
    $actif->set_pm($actif->get_pm() / (1 + ($actif->etat['derniere_chance']['effet2'] / 100))); // à déplacer
    $actif->degat_sup = $this->get_effet();
    return parent::lance($attaque);
  }
}

/// Classe gérant les sorts à état sans dégâts
class comp_combat_posture extends comp_combat_degat_etat
{
  function __construct($tbl)
  {
    parent::__construct($tbl, 'v-posture');
  }
  /// Ajoute l'état
  protected function ajout_etat($attaque)
  {
    parent::ajout_etat($attaque);
		$attaque->get_actif()->etat['posture']['type'] = $this->get_type();
  }
}

/// Classe gérant les sorts à état sans dégâts
class comp_combat_dissim extends comp_combat_etat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    global $db;
    $attaque->add_log_combat('c'.$this->get_id());
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    //$attaque->get_interface()->competence($this->get_type(), $actif->get_nom(), $this->get_nom());
    $bonus = 1;
		if ($actif->get_type() == 'joueur')
		{ // à déplacer
			$objet_t = decompose_objet($actif->get_inventaire_partie('dos'));
			if ($objet_t != '' && $objet_t['id_objet'] != '')
			{
				$requete = "SELECT * FROM armure WHERE ID = ".$objet_t['id_objet'];
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$effet = explode('-', $row['effet']);
				if ($effet[0] == '24')
					$bonus *= 1.5;
			}
		}
		$att = $actif->get_esquive() * (1 + $actif->get_dexterite()/100) * $bonus;
		$def = 20 * sqrt($passif->get_pm() + 5) * (1 + $passif->get_volonte()/100);
		$test = $this->test_potentiel($att, $def);
		if( $test )
			$this->ajout_etat($attaque);
		$attaque->get_interface()->tentative('d', $test, $actif->get_nom());
    $passif->precedent['critique'] = false;
    return $this->get_augmentations($actif, $passif);
  }
}

/// Classe gérant les compétences donnant des bonus/malus aux potentiels
class comp_combat_pot extends comp_combat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() * (1 + ($this->get_effet() / 100)));
    if( $this->get_effet2() )
      $passif->set_potentiel_bloquer($passif->get_potentiel_bloquer() * (1 + ($this->get_effet3() / 100)));
    $actif->set_potentiel_critique($actif->get_potentiel_critique() / (1 + ($this->get_effet2() / 100)));
    return parent::lance($attaque);
  }
}

/// Classe gérant les compétences donnant des bonus/malus aux potentiels
class comp_combat_deg_pot extends comp_combat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $actif->degat_sup = $this->get_effet();
    $passif->set_potentiel_bloquer($passif->get_potentiel_bloquer() * (1 + ($this->get_effet2() / 100)));
    $actif->set_potentiel_critique($actif->get_potentiel_critique() / (1 + ($this->get_effet3() / 100)));
    return parent::lance($attaque);
  }
}

/// Classe gérant les compétences donnant des bonus/malus aux potentiels
class comp_combat_coup_bouclier extends comp_combat_degat_etat
{
  /// Méthode gérant l'utilisation d'une compétence
  function lance(&$attaque)
  {
		$actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $actif->set_comp_att('melee');
    return parent::lance($attaque);
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$attaque)
  {
    return comp_combat::touche($attaque);
  }
  /// Méthode calculant les dégâts de base avant réduction
  function calcul_degats(&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
		$degat = $actif->get_bouclier_degat();
		if($passif->is_buff('bouclier_terre'))
      $degat += $passif->get_buff('bouclier_terre', 'effet');

		// Prise en compte des effets defenseurs de l'attaquant (protection artistique ...)
		/*$tmp_effets = array();
		$actif->get_effets_permanents($tmp_effets, 'defenseur');
		// Application des degats bloques
		foreach ($tmp_effets as $effect)
			// Actif et passif sont inversés puisque c'est l'actif qui touche au bouclier
			$degat = $effect->calcul_bloquage_reduction($passif, $actif, $degat);*/
    // @todo

		$att = $degat + $actif->get_force();
    $attaque->applique_effet('coup_bouclier', $att);
		$def = $passif->get_vie() + round($passif->get_pp() / 100);
    $attaque->applique_effet('resite_etourdissement', $def);
		//Hop ca étourdit
		if( $this->test_potentiel($att, $def) )
		{
			$this->ajout_etat($attaque);
			$attaque->get_interface()->effet(17, $this->get_duree(), $actif->get_nom(), $passif->get_nom());
			$attaque->add_log_effet_passif('&ef17~'.$this->get_duree());
		}
		
		return $degat;
  }
}

/// Classe gérant la fleche de sable
class comp_combat_sable extends comp_combat_effet
{
  /// Méthode calculant les dégâts de base avant réduction
  function calcul_degats(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    return parent::calcul_degats($attaque) - $this->get_effet();
  }
}

/// Classe gérant la compétence flèche enflammée
class comp_combat_fleche_enflammee extends comp_combat
{
  /// Méthode gérant l'utilisation d'une compétence
  function touche(&$attaque)
  {
    parent::touche($attaque);
    if( $this->test_de(100, $this->get_effet()) )
    {
      $passif = &$attaque->get_passif();
			$attaque->get_interface()->effet(18, $this->get_effet2(), $attaque->get_actif()->get_nom(), $passif->get_nom());
			$attaque->add_log_effet_passif('&ef18~'.$this->get_effet2());
      $passif->set_hp( $passif->get_hp() -  $this->get_effet2());
    }
  }
}

/// Classe gérant la compétence flèche barbelée
class comp_combat_fleche_barbelee extends comp_combat
{
  /// Méthode gérant les coups critiques
  function critiques(&$attaque)
  {
    $mult = parent::critiques($attaque);
    if( $mult > 1)
      return $mult * (1 + $this->get_effet()/100);
    return $mult;
  }
}
?>
