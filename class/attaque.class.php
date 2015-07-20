<?php
/**
 * @file attaque.class.php
 * Définition de la classe attaque regroupant les variables et actions utiliséees lors d'un combat.
 */

/**
 * Classe attaque
 * Classe attaque regroupant les variables et actions utiliséees lors d'un combat.
 */
class attaque
{
  private $perso;  ///< perso
  private $attaquant;  ///< Entité attaquant
  private $defenseur;  ///< Entité défendant
  private $actif;  ///< Entité effectuant l'action courrante
  private $passif;  ///< Entité suboissant l'action courrante
  private $effets;
  private $degats;  ///< Dégâts du round en cours.
  private $mode;
  private $jet;  ///< résultat d'un jet (notamment celui pour toucher en physique)
  private $degats_bloques;  ///< dégâts bloqués
  private $type_degats;  ///< Type de dégâts infligés.
  private $distance;  ///< Distance entre les protagonistes.

  public $valeur;  ///< Variable utilisé pour transmettre le résultat de l'application des effets (voir function applique_effet)

	/**
	 * @name Accesseurs.
	 * Méthodes pour accéder aux données
	 */
  // @{
  /// Renvoie le personnage
  function &get_perso()
  {
    return $this->perso;
  }
  /// @deprecated
  function &get_joueur()
  {
    return $this->perso;
  }
  /// Renvoie l'attaquant
  function &get_attaquant()
  {
    return $this->attaquant;
  }
  /// Renvoie le défenseur
  function &get_defenseur()
  {
    return $this->defenseur;
  }
  /// Renvoie les effets
  function &get_effets()
  {
    return $this->effets;
  }
  /// Renvoie les dégâts
  function get_degats()
  {
    return $this->degats;
  }
  // Modifie les dégâts
  function set_degats($degats)
  {
    $this->degats = $degats;
  }
  // Modifie les dégâts en ajoutant une valeur
  function add_degats($degats)
  {
    $this->degats += $degats;
    if($this->degats < 0)
      $this->degats = 0;
  }
  // Modifie les dégâts en multipliant une valeur
  function mult_degats($degats)
  {
    $this->degats = round($this->degats*$degats);
  }
  /// Renvoie l'entité active
  function get_actif()
  {
    return $this->actif;
  }
  /// Renvoie l'entité passive
  function get_passif()
  {
    return $this->passif;
  }
  /// Renvoie la valeur du jet mémorisé
  function get_jet()
  {
    return $this->jet;
  }
  /// Modifie la valeur du jet mémorisé
  function set_jet($jet)
  {
    $this->jet = $jet;
  }
  /// Renvoie le mode
  function get_mode()
  {
    return $this->mode;
  }
  /// Renvoie les dégâts bloqués
  function get_degats_bloques()
  {
    return $this->degats_bloques;
  }
  /// Modifie les dégâts bloqués
  function set_degats_bloques($degats_bloques)
  {
    $this->degats_bloques = $degats_bloques;
  }
  /// Ajoute une valeur aux les dégâts bloqués
  function add_degats_bloques($degats_bloques)
  {
    $this->degats_bloques += $degats_bloques;
  }
  /// Renvoie le type de dégâts infligés.
  function get_type_degats()
  {
    return $this->type_degats;
  }
  /// Modifie le type de dégâts infligés.
  function set_type_degats($type)
  {
    $this->type_degats = $type;
  }
  // @}

	/**
	 * @name Gestion du combat.
	 * Méthode utilisées pour gérer les différentes parties du combat
	 */
  private $type;  ///< à modifier ?.
  // @{
  ///Méthode gérant l'attaque
  function attaque($distance, $type, $pa_attaque, $R, $pet, $defenseur_en_defense=false)
  {
  	global $G_round_total, $attaque_hp_avant, $defense_hp_avant, $attaque_hp_apres, $defense_hp_apres;
  	$this->distance = $distance;
  	$this->type = $type;
  	
  	$round_total = $G_round_total;
  	// Donjon ?
  	if( is_donjon($this->perso->get_x(), $this->perso->get_y()) && !$this->perso->in_arene('and donj = 0') && $this->perso->get_y()>190 )
  	{
			$round_total *= 2;
			$this->attaquant->set_rm_restant($this->attaquant->get_rm_restant() * 2);
			$this->defenseur->set_rm_restant($this->defenseur->get_rm_restant() * 2);
		}
  	// Nombre de rounds
		if( $this->attaquant->get_race() == 'orc' || $this->defenseur->get_race() == 'orc' )
			$round_total += 1;
		if( $this->attaquant->is_buff('buff_sacrifice') )
			$round_total -= $this->attaquant->get_buff('buff_sacrifice', 'effet2');
		if($type == 'siege' || $type == 'ville')
			$round_total = 1;

		// Maladies
		maladie::degenerescence($this->attaquant);
		maladie::degenerescence($this->defenseur);
  	
  	// Initialisation
		$this->attaquant->etat = array();
		$this->defenseur->etat = array();
		$attaque_hp_avant = $this->attaquant->get_hp();
		$defense_hp_avant = $this->defenseur->get_hp();
		$round = 1;
		
		// Boucle principale qui fait durer le combat $round_total rounds
  	while( $round <= $round_total && $this->attaquant->get_hp() > 0 && $this->defenseur->get_hp() > 0 )
  	{
	  	$this->interf->nouveau_round($round);
	  	$this->add_log_combat('r'.$round.':');
	  	// L'attaquant agit
	  	$this->gere_passe(true);
	  	$this->add_log_combat(',');
	  	// Le défenseur agit
	  	$this->gere_passe(false);
	  	
	  	// Fin du round
	    $this->log_combat .= ','.$this->log_effects_attaquant.','.$this->log_effects_defenseur;
			$round++;
			if( $round <= $round_total )
        $this->add_log_combat(';');
			$this->log_effects_attaquant = '';
			$this->log_effects_defenseur = '';
		}
		
		// HP
		$attaque_hp_apres = $this->attaquant->get_hp();
		$defense_hp_apres = $this->defenseur->get_hp();
		$degat_attaque = $defense_hp_avant - $defense_hp_apres;
		$degat_defense = $attaque_hp_avant - $attaque_hp_apres;
		//On donne les bons HP à l'attaque et la défense
		$this->attaquant->fin_combat($perso);
		$this->defenseur->fin_combat($perso, $degat_defense);
		
		//Calculs liés à la survie, fiabilité de l'estimation de HP etc.
		$survie = $this->perso->get_survie();
		// Survies specialisees
		$survies_a_monter = array();
		switch( $type )
		{
		case 'monstre':
			switch( $this->defenseur->get_type() )
			{
			case 'humanoide':
				if( $this->perso->is_competence('survie_humanoide') )
					$survie += $this->perso->get_competence('survie_humanoide');
				$survies_a_monter[] = 'survie_humanoide';
				break;
			case 'magique':
				if( $this->perso->is_competence('survie_magique') )
					$survie += $this->perso->get_competence('survie_magique');
				$survies_a_monter[] = 'survie_magique';
				break;
			case 'bete':
				if( $this->perso->is_competence('survie_bete') )
					$survie += $this->perso->get_competence('survie_bete');
				$survies_a_monter[] = 'survie_bete';
				break;
			}
			break;
		case 'perso':
			$survies_a_monter[] = 'survie_humanoide';
			if ($this->perso->is_competence('survie_humanoide'))
				$survie += $this->perso->get_competence('survie_humanoide');
			break;
		}
		
		$nbr_barre_total = $this->defenseur->get_level() ? ceil($survie / $this->defenseur->get_level()) : $survie;
		if($nbr_barre_total > 100)
			$nbr_barre_total = 100;
		$nbr_barre = round( $this->defenseur->get_hp() / $this->defenseur->get_hp_max() * $nbr_barre_total );
		$longueur = $this->defenseur->is_buff('potion_illusion') ? 100 : max(round(100 * $nbr_barre / $nbr_barre_total, 2), 0);
		$fiabilite = round(100 / $nbr_barre_total / 2, 2);
			
		//Augmentation des compétences liées
		$augmentation = augmentation_competence('survie', $this->perso, 2);
		if( $augmentation[1] == 1 )
			$this->perso->set_survie($augmentation[0]);
		if( get_class($this->attaquant) == 'pet' )
		{
			$augmentation = augmentation_competence('dressage', $this->perso, 0.85);
			if($augmentation[1] == 1)
				$this->perso->set_dressage($augmentation[0]);
		}
		if( get_class($this->defenseur) == 'pet' )
		{
			$perso_defenseur = new perso( $this->defenseur->get_id_joueur() );
			$augmentation = augmentation_competence('dressage', $perso_defenseur, 0.85);
			if($augmentation[1] == 1)
			{
				$perso_defenseur->set_dressage($augmentation[0]);
				$perso_defenseur->sauver();
			}
		}
		foreach( $survies_a_monter as $survie_test )
		{
			if ($this->perso->is_competence($survie_test))
			{
				$augmentation = augmentation_competence($survie_test, $this->perso, 4);
				if ($augmentation[1] == 1)
					$this->perso->set_comp($survie_test, $augmentation[0]);
			}
		}
		
		// Affichage de la fin du combat
		$this->interf->aff_fin($this->attaquant, $this->defenseur, $degat_attaque, $degat_defense, $longueur, $fiabilite, $type, true);
		$msg_xp .= $this->defenseur->fin_defense($this->perso, $R, $pet, $degat_attaque, $defenseur_en_defense);
		if( $msg_xp )
			$this->interf->aff_messages_fin($msg_xp);
		
		// Achievements
		if( $this->defenseur->get_race() == $this->attaquant->get_race() && $this->defenseur->get_rang_royaume() == 6 && $this->defenseur->get_hp()<=0 ) 
		{
			$this->perso->unlock_achiev('roi_race_mort');
		}
		if( $this->defenseur->get_rang_royaume() == 6 & $this->defenseur->get_hp()<=0 )
		{
			$achiev = $this->perso->get_compteur('roi_mort');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
		}
		if( get_class($this->attaquant) != 'pet' && $this->attaquant->get_compteur_critique() > 0 && $this->attaquant->get_type() == 'joueur')
		{
			$achiev = $this->attaquant->get_compteur('critique');
			$achiev->set_compteur($achiev->get_compteur() + $this->attaquant->get_compteur_critique());
			$achiev->sauver();
		}
		if( get_class($this->defenseur) != 'pet' && $this->defenseur->get_compteur_critique() > 0 && $this->defenseur->get_type() == 'joueur')
		{
			$achiev = $this->defenseur->get_compteur('critique');
			$achiev->set_compteur($achiev->get_compteur() + $this->defenseur->get_compteur_critique());
			$achiev->sauver();
		}
		
		$this->attaquant->fin_attaque($this->perso, $this->defenseur, $pa_attaque);
	}
  /// Méthode gérant une passe
  function gere_passe($attaquant)
  {
  	$mode = $attaquant ? 'attaquant' : 'defenseur';
  	$this->init_passe($mode);
  	
  	if( $attaquant )
  		$action = script_action($this->attaquant, $this->defenseur, 'attaquant', $this);
  	else
		{
			if( $this->type == 'batiment' )
				$action = null;
			else if( $this->distance > $this->defenseur->get_distance_tir() )
			{
				$this->interf->approche($this->defenseur);
				$action = null;
        $this->add_log_combat('n');
        $this->distance--;
			}
			else
				$action = script_action($this->defenseur, $this->attaquant, 'defenseur', $this);
		}
		
		$augmentations = array('actif' => array('comp' => array(), 'comp_perso' => array()), 'passif' => array('comp' => array(), 'comp_perso' => array()));
		$this->init_action();
		// action
		if($action)
		{
      // Calcul de MP nécessaires
    	$mp_need = $action->get_cout_mp($this->actif);
    	// Appel des ténebres
    	if($actif->etat['appel_tenebre']['duree'] > 0)
    	{
    		$mp_need += $this->actif->etat['appel_tenebre']['effet'];
    	}
    	//Appel de la forêt
    	if($this->actif->etat['appel_foret']['duree'] > 0)
    	{
    		$mp_need_avant = $mp_need;
    		$mp_need -= $this->actif->etat['appel_foret']['effet'];
    		if($mp_need < 1)
					$mp_need = $mp_need_avant;
    	}
      // Application des effets de mana
      $this->applique_effet('calcul_mp', $mp_need);
    	//Suppresion de la réserve
    	$this->actif->set_rm_restant($this->actif->get_rm_restant() - $mp_need);

      $augmentations = $action->lance($this);
    }
    $this->applique_effet('fin_round');
    
		//Augmentation des compétences liées
		/// @todo à améliorer
		if( get_class($this->actif) != 'pet' )
			$this->actif = augmentation_competences($augmentations['actif'], $this->actif);
		if( get_class($this->passif) != 'pet' )
			$this->passif = augmentation_competences($augmentations['passif'], $this->passif);
		// Mise à jour de l'entité pour refleter les up
		$this->attaquant->maj_comp();
		$this->defenseur->maj_comp();
		
		// Affichage des informations de debugage
		$this->interf->aff_debug();
	}
  /// Méthode gérant les actions initialisant une passe
  function init_passe($mode)
  {
    $this->mode = $mode;
    $this->attaquant->init_round();
		$this->defenseur->init_round();

		// Effets généraux
		$this->effets = effect::general_factory($this->attaquant, $this->defenseur, $mode);
		// Effets permanents des personnages
		$this->perso->get_effets_permanents($this->effets, $mode);
		if($mode == 'attaquant')
		{
			$this->defenseur->get_effets_permanents($this->effets, 'defenseur');
			foreach($this->defenseur->etat as $key => $value)
			{
				$this->defenseur->etat[$key]['duree'] -= 1;
				if($this->defenseur->etat[$key]['duree'] <= 0)
					unset($this->defenseur->etat[$key]);
				else
					interf_debug::enregistre($this->defenseur->get_nom().' est '.$key.' pour '.$this->defenseur->etat[$key]['duree'].' rounds');
			}
		}
		else
		{
			$this->defenseur->get_effets_permanents($this->effets, 'attaquant');
			foreach($this->attaquant->etat as $key => $value)
			{
				$this->attaquant->etat[$key]['duree'] -= 1;
				if($this->attaquant->etat[$key]['duree'] <= 0)
					unset($this->attaquant->etat[$key]);
				else
					interf_debug::enregistre($this->attaquant->get_nom().' est '.$key.' pour '.$this->attaquant->etat[$key]['duree'].' rounds');
			}
		}
		
		// Interface
		$this->interf->nouvelle_passe($mode);
  }
  function  init_action()
  {
    if($this->mode == 'attaquant')
    {
      $this->actif = &$this->attaquant;
      $this->passif = &$this->defenseur;
	    $this->log_effects_actif = &$this->log_effects_attaquant;
	    $this->log_effects_passif = &$this->log_effects_defenseur;
    }
  	else
    {
      $this->actif = &$this->defenseur;
      $this->passif = &$this->attaquant;
	    $this->log_effects_actif = &$this->log_effects_defenseur;
	    $this->log_effects_passif = &$this->log_effects_attaquant;
    }
  }
  function applique_effet($action, &$valeur=null)
  {
    $this->valeur = &$valeur;
		foreach($this->effets as $effet)
			$effet->$action($this);
  }
  function applique_blocage()
  {
    if( $this->degats_bloques > $this->degats )
      $this->degats_bloques = $this->degats;
		$this->degats -= $this->degats_bloques;
  }
  // @}

	/**
	 * @name Affichage.
	 * Gestion de l'affichage du combat
	 */
  // @{
  private $interf;
  /// Renvoie l'interface
  function &get_interface()
  {
  	return $this->interf;
	}
	/// Définit l'interface
	function set_interface($interf)
  {
  	$this->interf = &$interf;
	}
  // @}

	/**
	 * @name Logs de combat.
	 * Méthode utilisées pour gérer les logs de combat
	 */
  // @{
  private $log_combat;
	private $log_effects_attaquant;
	private $log_effects_defenseur;
	private $log_effects_actif;
	private $log_effects_passif;
  /// Renvoie le log de combat
  function get_log_combat()
  {
    return $this->log_combat;
  }
  /// Méthode ajoutant une information au log de combat
  function add_log_combat($val)
  {
    $this->log_combat .= $val;
  }
  /// Méthode ajoutant une information au log de combat
  function add_log_effet_actif($val)
  {
    $this->log_effects_actif .= $val;
  }
  /// Méthode ajoutant une information au log de combat
  function add_log_effet_passif($val)
  {
    $this->log_effects_passif .= $val;
  }
  // @}

  /**
   * Constructeur
   */
  function __construct(&$perso, &$attaquant, &$defenseur)
  {
    $this->perso = &$perso;
    $this->attaquant = &$attaquant;
    $this->defenseur = &$defenseur;
    $this->log_combat = '';
  }
}
?>
