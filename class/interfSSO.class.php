<?php
/// @addtogroup Interface
/**
 * @file interfSSO.class.php
 * Classes pour la gestion de l'interface.
 */

include_once('interface.class.php');

/**
 * Classe de base pour la gestion de l'interface.
 * Cette classe fournie les méthode pour créer les autres classes, permettant ainsi
 * d'avoir plusieurs interfaces possibles.
 * Elle correspond à l'interface par défaut, les classes dérivées permettent de
 * définir d'autres interfaces.
 */
class interfSSO
{
  //protected $princ_html = '';
  protected $princ_droit = 'ihPrincDroit';
  
  /// Constructeur, innaccessible depuis l'extérieur
  protected __construct() {}
  
  /**
   * Méthode construisant l'objet.
   * L'objet est soit de cette classe soti d'une classe dérivée.
   */
  static function factory()
  {
    if( !isset($interf_def) )
      $interf_def = null;
    switch($interf_def)
    {
    case 'v09':
      return new interfSSOv09();
    default:
      return new interfSSO();
    }
  }
  /// Renvoie l'élément d'interface principal pour le panneau droit
  function getPrincDroit()
  {
    return new $this->princ_droit;
  }
  /*function getHTML()
  {
    return new $this->princ_html;
  }*/
}

/**
 * Classe de base pour la gestion de l'interface de la version 0.9
 */
class interfSSOv09 extends interfSSO
{
}
?>
