<?php
/**
 * @file interface_avance.class.php
 * Classes génériques pour la gestion de l'interface.
 * Ces classes permettent la construction de structures de bases composées de plusieurs éléments HTML.
 */
 
/**
 * Classe permettant un affichage avec des onglets.
 * Utilise la librairie boostrap.
 */
class interf_onglets extends interf_bal_cont
{
  protected $haut;  ///< liste des onglets.
  protected $id;
  //protected $divs;  ///< balises divs contenant le contenu de chaque onglets.

  /**
   * Constructeur
   * @param  $id_cont        id de la balise ul permettant la navigation.
   * @param  $id_cont        id de la balise div contenant tous les onglets.
   * @param  $class_cont     classe de la balise contenant tous les onglets.
   */
  function __construct($id_tabs, $id_cont, $class_cont=false)
  {
    interf_bal_cont::__construct('div', $id_cont, /*'tab-pane '.*/$class_cont);
    $this->haut = $this->add( new interf_bal_cont('ul', $id_tabs, 'nav nav-tabs') );
    $this->divs = array();
    $this->id = $id_cont;
    //self::code_js( '$(function() { $(\'#'.$id_tabs.'\').tab(); $(\'#'.$id_tabs.'\').bind(\'show\', charge_tab); });' );
    //self::code_js( '$(\''.$id_tabs.' a\').click(function (e) { e.preventDefault() $(this).tab(\'show\') });' );
    //self::code_js( '$(\''.$id_tabs.' a\').click(function (e) { charge_tab(e, "'.$id_cont.'") };' );
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->haut->affiche();
    //$this->ligne('<div class="spacer"></div>');
    interf_bal_cont::debut();
  }
  /**
   * Ajoute un onglet.
   * @param  $nom         nom à afficher sur l'onglet
   * @param  $adresse     adresse de la page web pour le contenu
   * @param  $id          id de la balise du contenu.
   * @param  $selection   indique si l'onglet est sélectionné
   */
  function /*&*/add_onglet($nom, $adresse, $id, $selection=false)
  {
    $classe = 'tab-pane';//
    $li = $this->haut->add( new interf_elt_menu($nom, /*'#'.$id*/'', 'charge_tab(this, \''.$this->id.'\');') );
    $lien = $li->get_lien();
    if($selection)
    {
      $li->set_attribut('class', 'active');
      $classe .= ' active';
    }
    $lien->set_attribut('data-toggle', 'tab');
    $li->set_attribut('data-url', $adresse);
    /*$div = $this->add( new interf_bal_cont('div', $id, $classe) );
    $this->divs[$id] = &$div;
    return $div;*/
  }

  /**
   * Renvoie un element div pour le contenu d'un onglet
   *
   * @param  $id  id de l'élément
   */
  /*function &get_onglet($id)
  {
    return $this->divs[$id];
  }*/
}
?>
