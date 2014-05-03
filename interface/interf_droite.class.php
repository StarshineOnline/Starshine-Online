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
class interf_droite extends interf_cont
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
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ligne('<h2>'.$this->titre.'</h2>');
    $this->ouvre('div id="contenu_droit" class="windows_right"');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme('div');
    $this->affiche_js();
  }
  /// Commande la mise-à-jour de la partie haute
  function add_maj_perso()
  {
    $img = $this->add( new interf_bal_smpl('img') );
    $img->set_attribut('src', 'image/pixel.gif');
    $img->set_attribut('onLoad', 'envoiInfo("infoperso.php?javascript=oui", "perso");');
  }
}

?>
