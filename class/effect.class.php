<?php
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

  /**
   * Constructeur
   * 
   * @param $aNom    Nom de l'effet.
   */  
  function __construct($aNom) {
    $this->nom = $aNom;
    $this->order = 1;
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
    ensable::factory($effects, $actif, $passif, $acteur);
		tellurique::factory($effects, $actif, $passif, $acteur);
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
   * Action a effectuer en fin de round
   * 
   * @param  $actif     Personnage actif lors de l'action.
   * @param  $passif    Personnage passif lors de l'action.              
   */  
  function fin_round(&$actif, &$passif) { }
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
	
  function fin_round(&$actif, &$passif)
  {
		if ($actif->etat[$this->nom]['duree'] < 1) {
			unset($actif->etat[$this->nom]);
		}
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

  function fin_round(&$actif, &$passif)
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

  function fin_round(&$actif, &$passif)
  {
		$this->hit($actif->get_nom().' perd '.$this->vigueur.
							 ' HP à cause du poison');
		$actif->add_hp(-$this->vigueur);
		if ($actif->etat['poison_lent']['duree'] < 1)
			unset($actif->etat['poison_lent']);
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
    $actif->potentiel_toucher /= 1 + ($this->effet / 100);
	}

	function calcul_attaque_magique(&$actif, &$passif, $att) {
    $this->debug($actif->get_nom().' est ensablé');
    return $att / (1 + ($this->effet / 100));
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
		}
		return $degats;
	}
}

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
		return $reduction + $bonus_reduction;
	}
}

?>