<?php
/**
 * @file interface_avance.class.php
 * Classes génériques pour la gestion de l'interface.
 * Ces classes permettent la construction de structures de bases composées de plusieurs éléments HTML.
 */
 
class interf_onglets extends interf_bal_cont
{
  protected $haut;  ///< liste des onlets.

  /**
   * Constructeur
   * @param  $id        id de la balise.
   * @param  $class     classe de la balise.
   */
  function __construct($id='messagerie_liste', $class=false)
  {
    interf_bal_cont::__construct('div', $id, $class);
    $this->haut = new interf_menu(false, 'messagerie_onglet', '');
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->haut->affiche();
    $this->ligne('<div class="spacer"></div>');
    interf_bal_cont::debut();
  }
  /**
   * Ajoute un onglet.
   * @param $nom  nom à afficher sur l'onglet
   * @param $adresse  adresse de la page web pour le contenu
   * @param $selectione   indique si l'onglet est sélectionné
   */
  function add_onglet($nom, $adresse, $selectione=false)
  {
    global $id_elt_ajax;
    $this->haut->add( new interf_elt_menu($nom, $adresse, 'return envoiInfo(this.href, \''.$id_elt_ajax.'\');') );
  }
}
?>
