<?php
/**
 * @file interf_jeu.class.php
 * Interface principale du jeu
 */

/**
 * Classe pour l'interface de jeu
 */
class interf_jeu extends interf_sso_int
{
  function __construct()
  {
    interf_sso_int::__construct();
    $this->menu->add_elt( new interf_elt_menu('Diplomatie', '#', 'affichePopUp(\'diplomatie.php\');') );
    $this->menu->add_elt( new interf_elt_menu('Classement', '#', 'affichePopUp(\'classement.php\');') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    $autres->add( new interf_elt_menu('Statistiques', '#', 'affichePopUp(\'stats2.php?graph=carte_royaume\');') );
    $autres->add( new interf_elt_menu('Message d\'Accueil', '#', 'affichePopUp(\'message_accueil.php?affiche=all\');') );
    $autres->add( new interf_elt_menu('Bestiaire', '#', 'affichePopUp(\'liste_monstre.php\');') );
    $autres->add( new interf_elt_menu('Carte', '#', 'affichePopUp(\'royaume.php\');') );

    $cont = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $cont->add( new interf_bal_cont('header', 'perso') );
    $perso->add( new interf_barre_perso() );
    $cont_jeu = $cont->add( new interf_bal_cont('div', 'contenu_jeu') );
    $delp = $cont_jeu->add( new interf_bal_cont('div', 'deplacement') );
    $info = $cont_jeu->add( new interf_bal_cont('div', 'information') );
  }
  function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Wiki', 'http://wiki.starshine-online.com/'), false);
    $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
  }
}
?>