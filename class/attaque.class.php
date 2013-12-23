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
  private $joueur;  ///< joueur
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
  
  public $valeur;  ///< Variable utilisé pour transmettre le résultat de l'application des effets (voir function applique_effet)

	/**
	 * @name Accesseurs.
	 * Méthodes pour accéder aux données
	 */
  // @{
  /// Renvoie le joueur
  function &get_joueur()
  {
    return $this->joueur;
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
  // @{
  /// Méthode gérant les actions initialisant un round
  function init_round($mode)
  {
    $this->mode = $mode;
    $this->attaquant->init_round();
		$this->defenseur->init_round();

		// Effets généraux
		$this->effets = effect::general_factory($this->attaquant, $this->defenseur, $mode);
		// Effets permanents des joueurs
		$this->joueur->get_effets_permanents($this->effets, $mode);
  }
  /// Méthode gérant les actions de fin de round
  function fin_round()
  {
    $this->log_combat .= ','.$this->log_effects_attaquant.','.$this->log_effects_defenseur;
		$this->log_effects_attaquant = '';
		$this->log_effects_defenseur = '';
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
  function __construct(&$joueur, &$attaquant, &$defenseur)
  {
    $this->joueur = &$joueur;
    $this->attaquant = &$attaquant;
    $this->defenseur = &$defenseur;
    $this->log_combat = '';
  }
}
?>
