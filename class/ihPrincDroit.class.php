<?php
/// @addtogroup Interface
/**
 * @file ihPrincDroit.class.php
 * Classe gérant l'élément d'interface principal pour le panneau droit dans l'interface de base.
 */

/**
 * Classe gérant l'élément d'interface principal pour le panneau droit.
 */
class ihPrincDroit extends ihPrincOB
{
  protected $titre;  ///< Titre du panneau.
  
  /**
   * Constructeur
   * @param  $titre   titre du panneau.
   */
  function __construct($titre)
  {
    $this->titre = $titre;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function Debut()
  {
    $this->Ouvre('fieldset');
    $this->Ligne('<legend>'.$this->titre.'</legend>');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function Fin()
  {
    $this->Ferme('fieldset');
  }
}

?>
