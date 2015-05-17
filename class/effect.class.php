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
   * @depreaceted	     
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
   * @depreaceted    
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
		interf_debug::enregistre($aMessage);
		/*global $debugs;
		echo "<div class=\"debug\" id=\"debug${debugs}\">$aMessage";
    if ($br) { echo '<br />'; }
    echo '</div>';
		$debugs++;*/
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
  function debut_round(&$attaque) { }
  /**
   * Calcul du mana requis
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $mp        MP requis
   *
   * @return    MP requis
   */
  function calcul_mp(&$attaque) {  }
  /**
   * Modifie l'anticipation
   * 
   * @param  $attaque		Objet contenant les paramètres du combat
   */
  function anticipation(&$attaque) {  }
  /**
   * Modifie le potentiel toucher magique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $att       Potentiel toucher avant modification.
   * 
   * @return    Potentiel toucher après modification.              
   */  
	function calcul_attaque_magique(&$attaque) {  }
  /**
   * Modifie le potentiel parer magique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $def       Potentiel parer avant modification.
   * 
   * @return    Potentiel parer après modification.              
   */  
	function calcul_defense_magique(&$attaque) {  }
  /**
   * Modifie le potentiel toucher physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $att       Potentiel toucher avant modification.
   * 
   * @return    Potentiel toucher après modification.              
   */  
	function calcul_attaque_physique(&$attaque) {  }
  /**
   * Modifie le potentiel parer physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $def       Potentiel parer avant modification.
   * 
   * @return    Potentiel parer après modification.              
   */  
	function calcul_defense_physique(&$attaque) {  }
  /**
   * Modifie le facteur de dégâts de l'arme
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $arme      Facteur de dégâts avant modification.
   * 
   * @return    Facteur de dégâts après modification.              
   */  
  function calcul_arme(&$attaque) { }
  /**
   * Modifie les dégâts
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts avant modification.
   * 
   * @return    Dégâts après modification.              
   */ 
  function calcul_degats(&$attaque) {  }
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
  function calcul_bonus_degats_magiques(&$attaque) {  }
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
  function calcul_degats_magiques(&$attaque) {  }
  /**
   * Effectue les modifications concernant le blocage (potentiel blocage, maitrise du bouclier)
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   */   
  function calcul_bloquage(&$attaque) { }
  /**
   * Modifie les degats bloques par le blocage reussi
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
	 * @param  $reduction La reduction avant modification
	 * 
	 * @return		reduction des degats
   */   
  function calcul_bloquage_reduction() {  }
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
  function applique_bloquage(&$attaque) { }
  /**
   * Modifie la PP
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $PP        PP avant modification.
   * 
   * @return    PP après modification.              
   */ 
  function calcul_pp(&$attaque) {  }
  /**
   * Modifie la PM
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $PM        PM avant modification.
   * 
   * @return    PM après modification.
   */ 
  function calcul_pm(&$attaque) {  }
  /**
   * Modifie le potentiel critique physique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $chance    Potentiel critique avant modification.
   * 
   * @return    Potentiel critique après modification.              
   */ 
  function calcul_critique(&$attaque) {  }
  /**
   * Modifie le multiplicateur de dégâts en cas ce coup critique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $mult      Multiplicateur de dégâts avant modification.
   * 
   * @return    Multiplicateur de dégâts après modification.              
   */ 
  function calcul_mult_critique(&$attaque) {  }
  /**
   * Applique les effets ayant lieu lorsque les dégâts ont lieu
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés.           
   */ 
  function calcul_critique_magique(&$attaque) {  }
  /**
   * Modifie le multiplicateur de dégâts en cas ce coup critique
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $mult      Multiplicateur de dégâts avant modification.
   * 
   * @return    Multiplicateur de dégâts après modification.              
   */ 
  function calcul_mult_critique_magique(&$attaque) {  }
  /**
   * Applique les effets ayant lieu lorsque les dégâts ont lieu
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés.           
   */ 
  function inflige_degats(&$attaque) {  }
  /**
   * Applique les effets ayant lieu lorsque les dégâts magiques ont lieu
   * Ne retourne rien, pas de modification des dégâts à ce stade
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.
   * @param  $degats    Dégâts infligés.           
   */ 
  function inflige_degats_magiques(&$attaque) { }
  /**
   * Applique les effets ayant lieu lorsque l'attaque est ratée
   * 
   * @param  $attaque		Objet contenant les paramètres du combat
   */
  function rate(&$attaque) { }
  /**
   * Modification du potentiel pour étourdir avec un bouclier
   * 
   * @param  $attaque		Objet contenant les paramètres du combat
   */
  function coup_bouclier(&$attaque) { }
  /**
   * Modification du potentiel pour résister aux étourdissement
   * 
   * @param  $attaque		Objet contenant les paramètres du combat
   */
  function resite_etourdissement(&$attaque) { }
  /**
   * Action a effectuer en fin de round
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.    
   * @param  $mode      Connaitre le mode   
   */  
  function fin_round(&$attaque ) { }
  /**
   * Action a effectuer en fin de combat
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.              
   */  
  function fin_combat(&$attaque) { }
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
	
  function fin_round(&$attaque)
  {
    $actif = $attaque->get_actif();
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
	static function factory(&$effects, &$actif, &$passif, $acteur = '')
  {
		if (array_key_exists(self::get_etat(), $actif->etat))
    {
      $classe = get_called_class();
			$effects[] = new $classe( $actif->etat[self::get_etat()]['effet'] );
		}
	}

  function fin_round(&$attaque)
  {
    global $log_effects_attaquant;
    $actif = $attaque->get_actif();
		$perte_hp = $this->effet;
		$actif->set_hp($actif->get_hp() - $perte_hp);
		$attaque->get_interface()->effet(static::type_log, $perte_hp, $actif->get_nom());
		$attaque->add_log_effet_actif('&ef'.static::type_log.'~'.$perte_hp);
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

  function fin_round(&$attaque)
  {
    $actif = $attaque->get_actif();
		$attaque->get_interface()->effet(static::type_log, $this->vigueur, $actif->get_nom());
		$attaque->add_log_effet_actif('&ef1~'.$this->vigueur);
		$actif->set_hp($actif->get_hp() - $this->vigueur);
		
		$attaque->add_log_effet_actif("&ef1~".$this->vigueur);
		
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

  function fin_round(&$attaque)
  {
		$actif = $attaque->get_actif();
		$perte_hp = $actif->etat['poison']['effet'] - $actif->etat['poison']['duree'] + 1;
		
		if($actif->etat['putrefaction']['duree'] > 0)
			$perte_hp = $perte_hp * $actif->etat['putrefaction']['effet'];
			
		$actif->set_hp($actif->get_hp() - $perte_hp);
		$attaque->get_interface()->effet(1, $perte_hp, $actif->get_nom());
		$attaque->add_log_effet_actif("&ef1~".$perte_hp);
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

  function fin_round(&$attaque)
  {
    $actif = $attaque->get_actif();
		$attaque->get_interface()->effet(1, $this->vigueur, $actif->get_nom());
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
  const type_log = 2;
}

/**
 * Embrasement
 */
class embraser extends perte_hp
{
  const type_log = 3;
}

/**
 * Acide
 */
class acide extends perte_hp
{
  const type_log = 4;
}

/**
 * Lien sylvestre
 */
class lien_sylvestre extends perte_hp
{
  const type_log = 5;
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

  function fin_round(&$attaque)
  {
    $actif = $attaque->get_actif();
		$effet = $this->effet;
		if(($actif->get_hp() + $effet) > $actif->etat['recuperation']['hp_max'])
		{
			$effet = $actif->etat['recuperation']['hp_max'] - $actif->get_hp();
		}
		$actif->set_hp($actif->get_hp() + $effet);
		if($effet > 0)
		{
			$actif->etat['recuperation']['hp_recup'] += $effet;
			$attaque->get_interface()->effet(6, $effet, $actif->get_nom());
			$attaque->add_log_effet_actif("&ef6~".$effet);
		}
		else
			interf_debug::enregistre($actif->get_nom().' ne peut pas gagner de HP par récupération');
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

	function calcul_attaque_magique(&$attaque) {
    $actif = $attaque->get_actif();
    $this->debug($actif->get_nom().' est ensablé');
    $actif->set_potentiel_lancer_magique( $actif->get_potentiel_lancer_magique() / (1 + $this->effet/100) );
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

	function calcul_attaque_magique(&$attaque) {
    $actif = $attaque->get_actif();
    $actif->set_potentiel_lancer_magique( $actif->get_potentiel_lancer_magique() / (1 + $this->effet/100) );
  }

  function fin_round(&$attaque)
  {
    global $log_effects_attaquant, $log_effects_defenseur;
		$this->debug($attaque->get_actif()->get_nom().' est sous l\'effet de Flêche Débilisante');
		if ($attaque->get_mode() == 'attaquant')
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

  function calcul_bonus_degats_magiques(&$attaque) {
		switch ($attaque->get_type_degats()) {
		case 'degat_feu':
		case 'degat_froid':
		case 'degat_vent':
		case 'degat_terre':
		case 'lapidation':
		case 'globe_foudre':
		case 'embrasement':
		case 'sphere_glace':
      $attaque->add_degats($this->effet);
		}
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
	
	function calcul_attaque_magique(&$attaque) {
		if ($this->effet_mag != 1)
			$this->debug($this->nom.' augmente le potentiel magique');
    $this->valeur *= $this->effet_mag;
	}
	
	function calcul_attaque_physique(&$attaque) {
		if ($this->effet_phy != 1)
			$this->debug($this->nom.' augmente le potentiel physique');
    $this->valeur *= $this->effet_phy;
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

  function inflige_degats(&$attaque) {
    $actif = $attaque->get_actif();
    $degats = $attaque->get_degats();
		$this->debug("effet_vampirisme: inflige_degats($degats) [effet $this->effet]");
		$gain = round($degats * $this->effet / 100);
		if (($actif->get_hp() + $gain) > $actif->get_hp_max())
			$gain = $actif->get_hp_max() - $actif->get_hp();
		if ($attaque->get_passif()->get_type() == 'batiment')
			$gain = 0;
		$actif->add_hp($gain);
		if ($gain > 0)
		{
			$attaque->get_interface()->effet(30, $gain, $actif->get_nom());
			$attaque->add_log_effet_actif('&ef30~'.$gain);
		}
		else
			$this->debug($actif->get_nom().' gagne '.$gain.' HP par '.$this->pos.' '.
									$this->nom);
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

	function calcul_bloquage_reduction(&$attaque) {
    $actif = $attaque->get_actif();
    $passif = $attaque->get_passif();
		$bonus_reduction = 0;
		for ($tmp_honneur = $passif->get_honneur() - 20000; $tmp_honneur > 0;
				 $tmp_honneur -= 15000)
			$bonus_reduction += $this->effet;
    $reduction = max($attaque->get_degats_bloques() + $bonus_reduction, 9);
		if ($bonus_reduction > 0)
			$this->debug($passif->get_nom().' voit son bloquage augmenté de '.
									 $bonus_reduction.' grâce à son honneur ('.$this->nom.') !');
		else
			$this->debug('Pas assez d\'honneur pour profiter du '.$this->nom);
    $attaque->set_degats_bloques($reduction);
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
	
	function calcul_attaque_magique(&$attaque) {
		$actif = $attaque->get_actif();
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
    $attaque->valeur *= 1 + 0.1*$bonus_potentiel;
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
  
	function calcul_attaque_magique(&$attaque) {
		$actif = $attaque->get_actif();
		$this->debug($actif->get_nom().' voit son potentiel magique augmenté de '.
									 $this->effet.'% ('.$this->nom.') !');
		$attaque->valeur *= 1+$this->effet/100;
	}
	
	function calcul_attaque_physique(&$attaque) {
		$actif = $attaque->get_actif();
		$this->debug($actif->get_nom().' voit son potentiel physique augmenté de '.
									 $this->effet.'% ('.$this->nom.') !');
		$attaque->valeur *= 1+$this->effet/100;
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
	
	function calcul_bonus_degats_magiques(&$attaque) {
		$actif = $attaque->get_actif();
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
		
	function calcul_degats_magiques(&$attaque) {
		$actif = $attaque->get_actif();
		switch ($attaque->get_type_degats()) {
		case 'degat_feu':
		case 'degat_froid':
		case 'degat_vent':
		case 'degat_terre':
		case 'lapidation':
		case 'globe_foudre':
		case 'embrasement':
		case 'sphere_glace':
      $attaque->add_degats($this->effet);
			$this->debug($actif->get_nom().' voit ses dégâts augmentés de '.
									 $this->effet.' grâce à son arme ('.$this->nom.') !');
		}
	}
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
	
  function inflige_degats_magiques(&$attaque) {
    $actif = $attaque->get_actif();
    $inf = floor($attaque->get_degats() * $this->effet);
		$attaque->get_interface()->effet(25, $inf, $actif->get_nom(), $attaque->get_passif()->get_nom());
		$attaque->add_log_effet_actif('&ef25~'.$inf);
    $actif->add_hp($inf * -1);
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
	
	function inflige_degats(&$attaque) {
    if (rand(1, 100) <= $this->effet)
		{
      $actif = $attaque->get_actif();
			$degat = 2 * $attaque->get_degats();
			$attaque->get_interface()->effet(26, $degat, $actif->get_nom(), $attaque->get_passif()->get_nom());
			$attaque->add_log_effet_actif('&ef26~'.$inf);
			$actif->add_hp($degat * -1);
		}
		else $this->debug('La carapace incisive n\'agit pas');
  }
}

class arc_tung extends effect
{
	var $effet;

	function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'arc_tung';
		parent::__construct($aNom);
    $this->effet = $aEffet;
	}
	
	function inflige_degats(&$attaque) {
    $actif = $attaque->get_actif();
		$degat = ceil(-0.2 * $attaque->get_degats);
		$actif->add_hp($degat);
		$attaque->get_interface()->effet(27, $degat, $actif->get_nom());
		$attaque->add_log_effet_actif('&ef27~'.$degat);
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
	
	function applique_bloquage(&$attaque) {
		$degat = ceil($this->effet * $attaque->get_degats());
		$actif->add_hp( -$degat );
		$attaque->get_interface()->effet(28, $degat, $attaque->get_actif()->get_nom());
		$attaque->add_log_effet_actif('&ef28~'.$degat);
    $attaque->set_degats( $degat*(1-$this->effet/100) );
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
  
  function calcul_degats_magiques(&$attaque) {
    if (get_class($attaque->get_passif()) == 'perso' && $attaque->get_actif()->get_race() == $this->effet) {
      $reduction = min(2, $degats);
			$attaque->get_interface()->effet(29, $reduction, '', $attaque->get_passif()->get_nom());
			$attaque->add_log_effet_actif('&ef29~'.$reduction);
      $attaque->add_degats(-$reduction);
    }
  }
}

class cape_troll extends effect
{
	var $effet;
	
		function __construct($aEffet, $aNom = null) {
    if ($aNom == null)
      $aNom = 'capte_troll';
		parent::__construct($aNom);
    $this->effet = $aEffet;
	}
	
	function calcul_mult_critique(&$attaque)
	{
    $this->valeur = ceil($this->valeur*(1-$this->effet/100));
	}
}

/**
 * Bouclier de pensée
 */
class bouclier_pensee extends effect
{
	var $effet;

	function __construct($aEffet, $aNom) {
    if ($aNom == null)
      $aNom = 'bouclier_pensee';
		parent::__construct($aNom);
    $this->effet = $aEffet;
  }

	function calcul_bloquage_reduction(&$attaque) {
    $attaque->set_degats_bloques( ceil($attaque->get_passif()->get_volonte()/2) );
	}
}


/**
 * Chances de toucher des sorts de feu
 * Ajouter lancer ? 
 */
class chances_feu extends effect
{
	private $effet;

	function __construct($effet, $nom='chances_feu')
	{
		parent::__construct($aNom);
    $this->effet = $effet;
  }

  function calcul_attaque_magique(&$attaque)
	{
		switch( $attaque->get_type_degats() )
		{
		case 'degat_feu':
		case 'embrasement':
      $attaque->valeur *= 1 + $this->effet / 100;
      break;
		}
	}
}


/**
 * Dégâts des sorts de feu
 */
class degats_feu extends effect
{
	private $effet;

	function __construct($effet, $nom='degats_feu')
	{
		parent::__construct($aNom);
    $this->effet = $effet;
  }

  function calcul_bonus_degats_magiques(&$attaque)
	{
		switch( $attaque->get_type_degats() )
		{
		case 'degat_feu':
		case 'embrasement':
      $attaque->add_degats($this->effet);
      break;
		}
	}
}


/**
 * Réduction du blocage
 */
class reduction_blocage extends effect
{
	private $effet;

	function __construct($effet, $nom='reduction_blocage')
	{
		parent::__construct($aNom);
    $this->effet = $effet;
  }

  function calcul_bloquage_reduction(&$attaque)
	{
		if( comp_sort::test_de(100, $this->effet) )
			$attaque->add_degats_bloques(-1);
	}
}

?>
