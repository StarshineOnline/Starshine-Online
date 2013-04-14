<?php
/// @addtogroup Interface
/**
 * @file ihPrincDroit.class.php
 * Classe gérant l'élément d'interface principal pour le panneau droit dans l'interface de base.
 */

include_once('interface.class.php');

/**
 * Classe gérant l'élément d'interface principal pour le panneau droit.
 */
class interf_princ_droit extends interf_princ_ob
{
  protected $titre;  ///< Titre du panneau.
  
  function factory($titre)
  {
      return new interf_princ_droit($titre);
  }
  /**
   * Constructeur
   * @param  $titre   titre du panneau.
   */
  protected function __construct($titre)
  {
    $this->titre = $titre;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ouvre('fieldset');
    $this->ligne('<legend>'.$this->titre.'</legend>');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme('fieldset');
  }
}

?>
