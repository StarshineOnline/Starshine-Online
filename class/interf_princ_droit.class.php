<?php
/// @addtogroup Interface
/**
 * @file interf_princ_droit.class.php
 * Classe gérant l'élément d'interface principal pour le panneau droit dans l'interface de base.
 */

include_once('interface.class.php');

/**
 * Classe gérant l'élément d'interface principal pour le panneau droit.
 */
class interf_princ_droit extends interf_princ_ob
{
  protected $titre;  ///< Titre du panneau.
  
  /**
   * Constructeur
   * @param  $titre   titre du panneau.
   */
  function __construct($titre)
  {
    global $id_elt_ajax;
    $id_elt_ajax = 'information';
    $this->titre = $titre;
  }
  /**
   * Ajoute un message
   * @param $message  Message à afficher
   * @param $ok       Indique si le message est positif ou négatif.
   */
  function add_message($message, $ok=true)
  {
    $this->add(new interf_bal_smpl($ok?'h6':'h5', $message) );
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {

    $this->ligne('<h2>'.$this->titre.'</h2>');
    $this->ouvre('div class=windows_right');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme('div');
  }
}

?>
