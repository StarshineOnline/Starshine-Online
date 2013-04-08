<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

/**
* @file effect.class.php
*/

/**
* Classe de base pour la gestion des effets en combat
*/
class effect
{
  var $nom;   ///< Nom de l'effet
  var $order; ///< Sert à déterminer l'ordre des effets
  var $used;  ///< true si l'effet a été utilisé, false sinon.

	static $DEBUT_DEBUT				= -10000;
	static $DEBUT_ADD					= -1000;
	static $DEBUT_MULT				= -100;
	static $STANDARD_MULT			= -10;
	static $STANDARD					= 1;
	static $STANDARD_ADD			= 10;
	static $FIN_MULT					= 100;
	static $FIN_ADD						= 1000;
	static $FIN_FIN						= 10000;

  /**
   * Constructeur
   * 
   * @param $aNom    Nom de l'effet.
   */  
  function __construct($aNom) {
    $this->nom = $aNom;
    $this->order = self::$STANDARD;
    $this->used = false;
  }

  /**
   * Compare l'ordre de deux effets
   * 
   * @param  $a, $b   Effets à comparer.
   * 
   * @return  1 si l'effet "a" a un ordre supérieur à l'effet "b", -1 sinon.
   */  
  static function compare_effects($a, $b) {
    if ($a->order == $b->order) {
      return 0;
    }
    return ($a->order > $b->order) ? -1 : 1;
  }

  /**
   *  Affiche le message de dégâts correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function hit($aMessage, $br = true) {
		echo "<span class=\"degat\">&nbsp;&nbsp;$aMessage";
    if ($br) { echo '<br />'; }
    echo '</span>';
	}

  /**
   *  Affiche le message de dégâts correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function heal($aMessage, $br = true) {
		echo "<span class=\"soin\">&nbsp;&nbsp;$aMessage";
    if ($br) { echo '<br />'; }
    echo '</span>';
	}

  /**
   *  Affiche le message d'informations correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function notice($aMessage, $br = true) {
		echo "<span class=\"small\">&nbsp;&nbsp;$aMessage";
    if ($br) { echo '<br />'; }
    echo '</span>';
	}

  /**
   *  Affiche le message de debug correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function debug($aMessage, $br = true) {
		global $debugs;
		echo "<div class=\"debug\" id=\"debug${debugs}\">$aMessage";
    if ($br) { echo '<br />'; }
    echo '</div>';
		$debugs++;
	}

  /**
   *  Affiche un message correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function message($aMessage) {
		echo "&nbsp;&nbsp;$aMessage<br />\n";
	}

  /**
   * Crée le tableau des effets
   *
   * @return le tableau des effets
   */
  static function general_factory(&$attaquant, &$defenseur, $mode) {
		include_once(root.'class/buff.class.php');

    $effects = array();

    if ($mode == 'attaquant')
    {
      $actif = $attaquant;
      $passif = $defenseur;
    }
    else
    {
      $actif = $defenseur;
      $passif = $attaquant;
    }

    /*
     * Etats
     */
    empoisonne::factory($effects, $actif, $passif, $acteur);
    poison_lent::factory($effects, $actif, $passif, $acteur);
    poison::factory($effects, $actif, $passif, $acteur);
    ensable::factory($effects, $actif, $passif, $acteur);
    tellurique::factory($effects, $actif, $passif, $acteur);
    bouclier_protecteur::factory($effects, $actif, $passif, $acteur);
    riposte_furtive::factory($effects, $actif, $passif, $acteur);
    hemorragie::factory($effects, $actif, $passif, $acteur);
    embraser::factory($effects, $actif, $passif, $acteur);
    acide::factory($effects, $actif, $passif, $acteur);
    lien_sylvestre::factory($effects, $actif, $passif, $acteur);
    recuperation::factory($effects, $actif, $passif, $acteur);
    debilitant::factory($effects, $actif, $passif, $acteur);
    /*
     * Compétences passives
     */
    maitrise_bouclier::factory($effects, $actif, $passif, $acteur);
    maitrise_epee::factory($effects, $actif, $passif, $acteur);
    maitrise_hache::factory($effects, $actif, $passif, $acteur);
    maitrise_arc::factory($effects, $actif, $passif, $acteur);
    maitrise_dague::factory($effects, $actif, $passif, $acteur);
    maitrise_critique::factory($effects, $actif, $passif, $acteur);
    /*
     * Équipement et buffs
     */
    gemme_enchassee::factory($effects, $actif, $passif, $acteur);
    buff_actif::factory($effects, $actif, $passif, $acteur);
    /*
     * Bonus raciaux
     */
    toucher_humainnoir::factory($effects, $actif, $passif, $acteur);

    /* Tri des effets selon leur ordre */
    sort_effects($effects);

    return $effects;

  }

  /**
   * Crée le tableau des effets
   *
   * @return le tableau des effets
   */
	static function general_simple_factory(&$joueur) {
		$effects = array();

		gemme_enchassee::factory($effects, $joueur, $joueur, '');

		/* Tri des effets selon leur ordre */
    sort_effects($effects);

    return $effects;
	}

  /**
   * Crée l'effet
   * 
   * @param  $effects   Tableau des effets.
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $acteur    Indique si le personnage à l'origine de l'effet est l'attaquant
   *                    ("attaquant") ou le défenseur.   
   */  
	static function factory(&$effects, &$actif, &$passif, $acteur = '') {}


  /**
   * @name  Actions des effets  
   * Méthodes à dériver selon les besoins
   */
  //@{
  /**
   * Action a effectuer en début de round
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.              
   */  
  function debut_round(&$actif, &$passif) { }
  /**
   * Calcul du mana requis
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $mp        MP requis
   *
   * @return    MP requis
   */
  function calcul_mp(&$actif, $mp) { return $mp; }
  /**
   * Modifie le potentiel toucher magique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $att       Potentiel toucher avant modification.
   * 
   * @return    Potentiel toucher après modification.              
   */  
	function calcul_attaque_magique(&$actif, &$passif, $att) { return $att; }
  /**
   * Modifie le potentiel parer magique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $def       Potentiel parer avant modification.
   * 
   * @return    Potentiel parer après modification.              
   */  
	function calcul_defense_magique(&$actif, &$passif, $def) { return $def; }
  /**
   * Modifie le potentiel toucher physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $att       Potentiel toucher avant modification.
   * 
   * @return    Potentiel toucher après modification.              
   */  
	function calcul_attaque_physique(&$actif, &$passif, $att) { return $att; }
  /**
   * Modifie le potentiel parer physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $def       Potentiel parer avant modification.
   * 
   * @return    Potentiel parer après modification.              
   */  
	function calcul_defense_physique(&$actif, &$passif, $def) { return $def; }
  /**
   * Modifie le facteur de dégâts de l'arme
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $arme      Facteur de dégâts avant modification.
   * 
   * @return    Facteur de dégâts après modification.              
   */  
  function calcul_arme(&$actif, &$passif, $arme) { return $arme; }
  /**
   * Modifie les dégâts
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts avant modification.
   * 
   * @return    Dégâts après modification.              
   */ 
  function calcul_degats(&$actif, &$passif, $degats) { return $degats; }
  /**
   * Modifie le bonos aux dégâts des sorts
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Bonus dégâts avant modification.
   * @param  $type      Type de sort.
   * 
   * @return    Bonus dégâts après modification.
   */ 
  function calcul_bonus_degats_magiques(&$actif, &$passif, $bonus_degats, $type) { return $bonus_degats; }
  /**
   * Modifie les dégâts des sorts
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts avant modification.
   * @param  $type      Type de sort.
   * 
   * @return    Dégâts après modification.
   */ 
  function calcul_degats_magiques(&$actif, &$passif, $degats, $type) { return $degats; }
  /**
   * Effectue les modifications concernant le blocage (potentiel blocage, maitrise du bouclier)
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   */   
  function calcul_bloquage(&$actif, &$passif) { }
  /**
   * Modifie les degats bloques par le blocage reussi
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
	 * @param  $reduction La reduction avant modification
	 * 
	 * @return		reduction des degats
   */   
  function calcul_bloquage_reduction(&$actif, &$passif, $reduction) { return $reduction; }
  /**
   * Modifie les dégâts lorsque le coup est bloqué (en plus des dégâts bloqués)
	 * Ceci se fait apres l'affichage du blocage, donc si les degats sont
   * modifies, la modification ne sera pas imputee au bouclier lui-meme.
	 * Il y a peu de raisons de modifier les degats a ce niveau.
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés avant modification.
   * 
   * @return    Dégâts infligés après modification.
   */ 
  function applique_bloquage(&$actif, &$passif, $degats) { return $degats; }
  /**
   * Modifie la PP
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $PP        PP avant modification.
   * 
   * @return    PP après modification.              
   */ 
  function calcul_pp(&$actif, &$passif, $pp) { return $pp; }
  /**
   * Modifie la PM
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $PM        PM avant modification.
   * 
   * @return    PM après modification.
   */ 
  function calcul_pm(&$actif, &$passif, $pm) { return $pm; }
  /**
   * Modifie le potentiel critique physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $chance    Potentiel critique avant modification.
   * 
   * @return    Potentiel critique après modification.              
   */ 
  function calcul_critique(&$actif, &$passif, $chance) { return $chance; }
  /**
   * Modifie le multiplicateur de dégâts en cas ce coup critique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $mult      Multiplicateur de dégâts avant modification.
   * 
   * @return    Multiplicateur de dégâts après modification.              
   */ 
  function calcul_mult_critique(&$actif, &$passif, $mult) { return $mult; }
  /**
   * Applique les effets ayant lieu lorsque les dégâts ont lieu
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés.           
   */ 
  function inflige_degats(&$actif, &$passif, $degats) { return $degats; }
  /**
   * Applique les effets ayant lieu lorsque les dégâts magiques ont lieu
   * Ne retourne rien, pas de modification des dégâts à ce stade
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés.           
   */ 
  function inflige_degats_magiques(&$actif, &$passif, $degats) { }
  /**
   * Action a effectuer en fin de round
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.    
   * @param  $mode      Connaitre le mode   
   */  
  function fin_round(&$actif, &$passif, $mode ) { }
  /**
   * Action a effectuer en fin de combat
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.              
   */  
  function fin_combat(&$actif, &$passif) { }
  //@}
}


/**
 * Tri un tableau d'effets
 *  
 * @param  $effects   Tableau d'effets.
 */
function sort_effects(array& $effects) {
  usort($effects, array('effect', 'compare_effects'));
}

class etat extends effect {
	var $effet;

  function __construct($effet, $nom) {
    parent::__construct($nom);
		$this->effet = $effet;
	}
	
  function fin_round(&$actif, &$passif, $mode)
  {
		if ($actif->etat[$this->nom]['duree'] < 1) {
			unset($actif->etat[$this->nom]);
		}
	}
}

/**
 * Hémorragie
 */
class perte_hp extends etat
{
  const type_log = false;
  
  function __construct($effet)
  {
    parent::__construct($effet, self::get_etat());
	}
	static function get_etat()
	{
    return get_called_class();
  }
	static function get_nom()
	{
    return get_called_class();
  }
	static function factory(&$effects, &$actif, &$passif, $acteur = '')
  {
		if (array_key_exists(self::get_etat(), $actif->etat))
    {
      $classe = get_called_class();
			$effects[] = new $classe( $actif->etat[self::get_etat()]['effet'] );
		}
	}

  function fin_round(&$actif, &$passif, $mode)
  {
    global $log_effects_attaquant;
		$perte_hp = $this->effet;
		$actif->set_hp($actif->get_hp() - $perte_hp);
		$this->hit($actif->get_nom().' perd '.$perte_hp. ' HP par '.$this->get_nom());
		$log_effects_attaquant .= '&'.static::type_log.'~'.$perte_hp;
	}
}

/**
 * empoisonné
 */
class empoisonne extends effect {
	var $vigueur;

  function __construct($aVigueur) {
    parent::__construct('poison');
		$this->vigueur = $aVigueur;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('empoisonne', $actif->etat))
			$effects[] = new empoisonne($actif->etat['empoisonne']['effet']);
	}

  function fin_round(&$actif, &$passif, $mode)
  {
		$this->hit($actif->get_nom().' perd '.$this->vigueur.' HP à cause du poison');
		$actif->set_hp($actif->get_hp() - $this->vigueur);
		$actif->etat['empoisonne']['effet'] -= 1;
		if ($actif->etat['empoisonne']['effet'] < 1)
			$actif->etat['empoisonne']['effet'] = 1;
		if ($actif->etat['empoisonne']['duree'] < 1)
			unset($actif->etat['empoisonne']);
	}
}

/**
 * Poison
 */
class poison extends effect
{
  function __construct()
  {
    parent::__construct('poison');
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '')
  {
		if (array_key_exists('poison', $actif->etat))
    {
			$effects[] = new poison();
		}
	}

  function fin_round(&$actif, &$passif, $mode)
  {
    global $log_effects_attaquant, $log_effects_defenseur;
		$perte_hp = $actif->etat['poison']['effet'] - $attaquant->etat['poison']['duree'] + 1;
		if($actif->etat['putrefaction']['duree'] > 0)
      $perte_hp = $perte_hp * $actif->etat['putrefaction']['effet'];
		$actif->set_hp($actif->get_hp() - $perte_hp);
		$this->hit($actif->get_nom().' perd '.$perte_hp. ' HP par le poison');
		//echo $mode;
		if ($mode == 'attaquant')
			$log_effects_attaquant .= "&ef1~".$perte_hp;
		else
			$log_effects_defenseur .= "&ef1~".$perte_hp;
	}
}

/**
 * Poison lent: pas d'atténuation de la vigueur
 */
class poison_lent extends effect {
	var $vigueur;

  function __construct($aVigueur) {
    parent::__construct('poison');
		$this->vigueur = $aVigueur;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('poison_lent', $actif->etat)) {
			$effects[] = new poison_lent($actif->etat['poison_lent']['effet']);
		}
	}

  function fin_round(&$actif, &$passif, $mode)
  {
		$this->hit($actif->get_nom().' perd '.$this->vigueur.
							 ' HP à cause du poison');
		$actif->add_hp(-$this->vigueur);
		if ($actif->etat['poison_lent']['duree'] < 1)
			unset($actif->etat['poison_lent']);
	}
}

/**
 * Hémorragie
 */
class hemorragie extends perte_hp
{
  const type_log = 'ef2';
}

/**
 * Embrasement
 */
class embraser extends perte_hp
{
  const type_log = 'ef3';
	static function get_nom()
	{
    return 'embrasement';
  }
}

/**
 * Acide
 */
class acide extends perte_hp
{
  const type_log = 'ef4';
}

/**
 * Lien sylvestre
 */
class lien_sylvestre extends perte_hp
{
  const type_log = 'ef5';
	static function get_nom()
	{
    return 'le lien sylvestre';
  }
}

/**
 * Récupération
 */
class recuperation extends etat
{
  function __construct($aEffet)
  {
    parent::__construct($aEffet, 'recuperation');
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '')
  {
		if (array_key_exists('recuperation', $actif->etat))
    {
			$effects[] = new recuperation($actif->etat['recuperation']['effet']);
    }
	}

  function fin_round(&$actif, &$passif, $mode)
  {
    global $log_effects_attaquant, $log_effects_defenseur;
		$effet = $this->effet;
		if(($actif->get_hp() + $effet) > $actif->etat['recuperation']['hp_max'])
		{
			$effet = $actif->etat['recuperation']['hp_max'] - $actif->get_hp();
		}
		$actif->set_hp($actif->get_hp() + $effet);
		if($effet > 0)
		{
			$actif->etat['recuperation']['hp_recup'] += $effet;
			echo '&nbsp;&nbsp;<span class="soin">'.$actif->get_nom().' gagne '.$effet.' HP par récupération</span><br />';
			if ($mode == 'attaquant')
				$log_effects_attaquant .= "&ef6~".$effet;
			else
				$log_effects_defenseur .= "&ef6~".$effet;
		}
		else
			print_debug($actif->get_nom().' ne peut pas gagner de HP par récupération');
  }
}

/**
 * Ensablé : sous l'effet de flèche de sable
 */
class ensable extends etat {

  function __construct($aEffet) {
    parent::__construct($aEffet, 'fleche_sable');
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('fleche_sable', $actif->etat)) {
			$effects[] = new ensable($actif->etat['fleche_sable']['effet']);
		}
	}

  function debut_round(&$actif, &$passif) {
    $this->debug($actif->get_nom().' est ensablé');
    $actif->set_potentiel_toucher( $actif->get_potentiel_toucher() / (1 + ($this->effet / 100)) );
	}

	function calcul_attaque_magique(&$actif, &$passif, $att) {
    $this->debug($actif->get_nom().' est ensablé');
    return $att / (1 + ($this->effet / 100));
  }
}

/**
 * debilitant : sous l'effet de flèche débilitant
 */
class debilitant extends etat {

  function __construct($aEffet)
  {
    parent::__construct($aEffet, 'fleche_debilitante');
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '')
  {
		if (array_key_exists('fleche_debilitante', $actif->etat))
    {
			$effects[] = new debilitant($actif->etat['fleche_debilitante']['effet']);
		}
	}


  /// Action a effectuer en début de round
  function debut_round(&$actif, &$passif)
  {
    $actif->set_potentiel_lancer_magique( $actif->get_potentiel_lancer_magique / (1 + ($this->effet / 100)) );
  }

  function fin_round(&$actif, &$passif, $mode)
  {
    global $log_effects_attaquant, $log_effects_defenseur;
		$this->debug($actif->get_nom().' est sous l\'effet de Flêche Débilisante');
		if ($mode == 'attaquant')
		$log_effects_attaquant .= "&ef7~0";
		else
		$log_effects_defenseur .= "&ef7~0";
  }
}

/**
 * Tellurique: est sous l'effet de frappe tellurique
 */
class tellurique extends etat {

  function __construct($aEffet) {
    parent::__construct($aEffet, 'tellurique');
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('tellurique', $actif->etat)) {
			$effects[] = new tellurique($actif->etat['tellurique']['effet']);
		}
	}

  function calcul_bonus_degats_magiques(&$actif, &$passif, $bonus_degats, $type) {
		switch ($type) {
		case 'degat_feu':
		case 'degat_froid':
		case 'degat_vent':
		case 'degat_terre':
		case 'lapidation':
		case 'globe_foudre':
		case 'embrasement':
		case 'sphere_glace':
			$bonus_degats += $this->effet;
		}
		return $bonus_degats;
	}
}

/**
 * Bonus nocturne des corrompus
 */
class toucher_humainnoir extends etat {

	var $effet_phy = 1;
	var $effet_mag = 1;

  function __construct($aPhy, $aMag) {
    parent::__construct(1, 'Bonus racial corrompu');
    $this->order = effect::$FIN_FIN;
		$this->effet_phy = $aPhy;
		$this->effet_mag = $aMag;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if ($actif->get_race() == 'humainnoir') {
			if (moment_jour() == 'Nuit')
				$effects[] = new toucher_humainnoir(1, 1.1);
			elseif (moment_jour() == 'Journee')
				$effects[] = new toucher_humainnoir(1.1, 1);
		}
	}
	
	function calcul_attaque_magique(&$actif, &$passif, $att) {
		if ($this->effet_mag != 1)
			$this->debug($this->nom.' augmente le potentiel magique');
		return $att * $this->effet_mag;
	}
	
	function calcul_attaque_physique(&$actif, &$passif, $att) {
		if ($this->effet_phy != 1)
			$this->debug($this->nom.' augmente le potentiel physique');
		return $att * $this->effet_phy;
	}
}

/**
 * Effet Vampirisme
 */
class effet_vampirisme extends effect
{
	var $effet;
	var $mode;
	var $pos = 'son';

  function __construct($aEffet, $aNom) {
		if ($aNom == null)
			$aNom = 'effet_vampirisme';
    parent::__construct($aNom);
		$this->effet = $aEffet;
	}

  function inflige_degats(&$actif, &$passif, $degats) {
		$this->debug("effet_vampirisme: inflige_degats($degats) [effet $this->effet]");
		$gain = round($degats * $this->effet / 100);
		if (($actif->get_hp() + $gain) > $actif->get_hp_max())
			$gain = $actif->get_hp_max() - $actif->get_hp();
		if ($passif->get_type() == 'batiment')
			$gain = 0;
		$actif->add_hp($gain);
		if ($gain > 0)
			$this->heal($actif->get_nom().' gagne '.$gain.' HP par '.$this->pos.' '.
									$this->nom, true);
		else
			$this->debug($actif->get_nom().' gagne '.$gain.' HP par '.$this->pos.' '.
									$this->nom);
		return $degats;
	}
}

/**
 * Protection Artistique (bouclier du gobelin artiste)
 */
class protection_artistique extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'protection_artistique';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }

	function calcul_bloquage_reduction(&$actif, &$passif, $reduction) {
		$bonus_reduction = 0;
		for ($tmp_honneur = $passif->get_honneur() - 20000; $tmp_honneur > 0;
				 $tmp_honneur -= 15000)
			$bonus_reduction += $this->effet;
		if ($bonus_reduction > 0)
			$this->debug($passif->get_nom().' voit son bloquage augmenté de '.
									 $bonus_reduction.' grâce à son honneur ('.$this->nom.') !');
		else
			$this->debug('Pas assez d\'honneur pour profiter du '.$this->nom);
		return max($reduction + $bonus_reduction, 9);
	}
}

/**
 * Pinceau Magique 
 */
class bonus_pinceau extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'bonus_pinceau';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }
	
	function calcul_attaque_magique(&$actif, &$passif, $potentiel) {
		$bonus_potentiel = 0;
		for ($tmp_honneur = $actif->get_honneur() - 20000; $tmp_honneur > 0;
				 $tmp_honneur -= 15000)
			echo $this->effet;
			$bonus_potentiel += $this->effet;
		if ($bonus_potentiel > 0)
			$this->debug($actif->get_nom().' voit son potentiel magique augmenté de '.
									 $bonus_potentiel.' grâce à son honneur ('.$this->nom.') !');
		else
			$this->debug('Pas assez d\'honneur pour profiter du '.$this->nom);
		return $potentiel * (1 + 0.1*$bonus_potentiel);
	}
}

class pierre_precision extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'pierre_precision';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }
  
	function calcul_attaque_magique(&$actif, &$passif, $potentiel) {
		$this->debug($actif->get_nom().' voit son potentiel magique augmenté de '.
									 $this->effet.'% ('.$this->nom.') !');
		return $potentiel*(1+$this->effet/100);
								 }
	
	function calcul_attaque_physique(&$actif, &$passif, $potentiel) {
		$this->debug($actif->get_nom().' voit son potentiel physique augmenté de '.
									 $this->effet.'% ('.$this->nom.') !');
		return $potentiel*(1+$this->effet/100);					 
								 }
	
}

/**
 * Pinceau Magique 
 */
class bonus_pinceau_degats extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'bonus_pinceau_degat';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }
	
	function calcul_bonus_degats_magiques(&$actif, &$passif, $bonus_degats, $type) {
		$bonus_degat = 0;
		for ($tmp_honneur = $actif->get_honneur() - 20000; $tmp_honneur > 0;
				 $tmp_honneur -= 15000)
			$bonus_degat += $this->effet;
		if ($bonus_degat > 0)
			$this->debug($actif->get_nom().' voit son facteur de dégat augmenté de '.
									 $bonus_degat.' grâce à son honneur ('.$this->nom.') !');
		else
			$this->debug('Pas assez d\'honneur pour profiter du '.$this->nom);
		return $bonus_degats + $bonus_degat;
	}
}
/**
 * boutte flamme
 */
class boutte_flamme extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'boutte_flamme';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }
		
	function calcul_degats_magiques(&$actif, &$passif, $degats, $type) {
		switch ($type) {
		case 'degat_feu':
		case 'degat_froid':
		case 'degat_vent':
		case 'degat_terre':
		case 'lapidation':
		case 'globe_foudre':
		case 'embrasement':
		case 'sphere_glace':
			$degats += $this->effet;
			$this->debug($actif->get_nom().' voit ses dégâts augmentés de '.
									 $this->effet.' grâce à son arme ('.$this->nom.') !');
		}
		return $degats;
	}
	/*
	function inflige_degats_magiques(&$actif, &$passif, $degats, $type) {  
    switch ($type) {
      case 'degat_feu':
      case 'degat_froid':
      case 'degat_vent':
      case 'degat_terre':
      case 'lapidation':
      case 'globe_foudre':
      case 'embrasement':
      case 'sphere_glace':
      {
        $this->hit('Le boute-flamme de '.$actif->get_nom().' augmente votre sensibilité aux sorts élémentaires
			Vous recevez '.$this->effet.' dégâts supplémentaire');
        $passif->add_hp($this->effet * -1);
        break;
      }
      return $degats;
    }
  }
  */
}

/**
 * Riposte furtive
 */
class riposte_furtive extends effect
{
	var $effet;

	function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'Riposte furtive';
		parent::__construct($aNom);
    $this->effet = $aEffet / 100;
    $this->order = effect::$FIN_FIN;
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('riposte_furtive', $passif->etat)) {
      $effet = $passif->etat['riposte_furtive']['effet'];
      effect::debug('riposte_furtive active: '.$effet.'%');
			$effects[] = new riposte_furtive($effet);
    }
	}
	
  function inflige_degats(&$actif, &$passif, $degats) {
    $inf = floor($degats * $this->effet);
    $this->hit('La riposte furtive de '.$passif->get_nom().' inflige '.$inf.
               ' dégâts à '.$actif->get_nom());
    $actif->add_hp($inf * -1);
    return $degats;
  }
	
  function inflige_degats_magiques(&$actif, &$passif, $degats) {
    $this->inflige_degats($actif, $passif, $degats);
  }
}

/**
 * Carapace de pierre incisive
 */
class carapace_incisive extends effect
{
	var $effet;

	function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'carapace_incisive';
		parent::__construct($aNom);
    $this->effet = $aEffet;
	}
	
	function inflige_degats(&$actif, &$passif, $degats) {
    if (rand(1, 100) <= $this->effet)
		{
			$degat = 2 * $degats;
			$this->hit('La carapace incisve de '.$passif->get_nom().' inflige '.$degat.
								 ' dégâts à '.$actif->get_nom());
			$actif->add_hp($degat * -1);
		}
		else $this->debug('La carapace incisive n\'agit pas');
		return $degats;
  }
}

class mirroir_eclatant extends effect
{
	var $effet;

	function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'mirroir_eclatant';
		parent::__construct($aNom);
    $this->effet = $aEffet / 100;
	}
	
	function applique_bloquage(&$actif, &$passif, $degats) {
		$degat = $this->effet * $degats;
		$this->hit('Votre bouclier inflige '.ceil($degat).
               ' dégâts à '.$actif->get_nom());
		$actif->add_hp(ceil($degat) * -1);
		$degat = $degats * $this->effet;
		$this->hit('Votre bouclier bloque '.ceil($degat).
               ' dégâts');
               return ceil($degat*(1-$this->effet/100));		
	}
}
  
class anneau_resistance extends effect
{
	var $effet;

	function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'anneau_resistance';
		parent::__construct($aNom);
    $this->effet = $aEffet;
	}
	
	function calcul_degats(&$actif, &$passif, $degats) {
	if (get_class($passif) != 'perso')
	return $degats;
    if ($actif->get_race() == $this->effet) {
      $reduction = min(2, $degats);
			$this->hit('L\'anneau de resistance de '.$passif->get_nom().
                 ' reduit les degats de '.$reduction);
      $degats -= $reduction;
    }
    return $degats;
  }
  
  function calcul_degats_magiques(&$actif, &$passif, $degats, $type) {
    return $this->calcul_degats($actif, $passif, $degats);
  }
}

?>
