<?php
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
  function compare_effects($a, $b) {
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
	function hit($aMessage) {
		echo "<span class=\"degat\">$aMessage</span>";
	}

  /**
   *  Affiche le message de dégâts correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function heal($aMessage) {
		echo "<span class=\"soin\">$aMessage</span>";
	}

  /**
   *  Affiche le message d'informations correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function notice($aMessage) {
		echo "<span class=\"small\">$aMessage</span>";
	}

  /**
   *  Affiche le message de debug correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function debug($aMessage) {
		global $debugs;
		echo "<div class=\"debug\" id=\"debug${debugs}\">$aMessage</div>";
		$debug++;
	}

  /**
   *  Affiche un message correspondant à l'effet
   *  
   * @param  $aMessage    Message à afficher.        
   */  
	function message($aMessage) {
		echo $aMessage."<br />\n";
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
	static function factory(&$effects, &$actif, &$passif, $acteur) {}


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
	 * 
	 * @return		reduction des degats
   */   
  function calcul_bloquage_reduction(&$actif, &$passif, $reduction) { return $reduction; }
  /**
   * Modifie les dégâts lorsque le coup est bloqué (en plus des dégâts bloqués)
	 * Ceci se fait apres l'affichage du blocage, donc si les degats sont modifies,
	 * la modification ne sera pas imputee au bouclier lui-meme.
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
  function inflige_degats(&$actif, &$passif, $degats) { }
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

?>