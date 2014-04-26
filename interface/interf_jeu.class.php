<?php
/**
 * @file interf_jeu.class.php
 * Interface principale du jeu
 */
include_once(root.'interface/interf_sso.class.php');

/**
 * Classe pour l'interface de jeu
 */
class interf_jeu extends interf_sso_int
{
  protected $gauche;
  protected $droite;

  function __construct()
  {
    interf_sso_int::__construct();
    $this->menu->add_elt( new interf_elt_menu('Diplomatie', '#', 'affichePopUp(\'diplomatie.php\');') );
    $this->menu->add_elt( new interf_elt_menu('Cartes', '#', 'affichePopUp(\'royaume.php\');') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    $autres->add( new interf_elt_menu('Message d\'Accueil', '#', 'affichePopUp(\'message_accueil.php?affiche=all\');') );
    $autres->add( new interf_elt_menu('Bestiaire', '#', 'affichePopUp(\'liste_monstre.php\');') );
    $autres->add( new interf_elt_menu('Background', '#', 'affichePopUp(\'background.php\');') );
    $autres->add( new interf_elt_menu('Statistiques', '#', 'affichePopUp(\'stats2.php?graph=carte_royaume\');') );
    $autres->add( new interf_elt_menu('Classement', '#', 'affichePopUp(\'classement.php\');') );

    $msg = $this->menu->add_elt( new interf_elt_menu('Messages', '', '') );
    $msg->get_lien()->add( new interf_bal_smpl('span', 12, 'nbr_msg', 'badge') );
    $ech = $this->menu->add_elt( new interf_elt_menu('Échanges', '', '') );
    $ech->get_lien()->add( new interf_bal_smpl('span', 1, 'nbr_echg', 'badge') );
    $this->menu->add_elt( new interf_elt_menu('Groupe', '', '') );

    $cont = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $cont->add( new interf_bal_cont('header', 'perso') );
    $perso->add( new interf_barre_perso() );
    $cont_jeu = $cont->add( new interf_bal_cont('div', 'contenu_jeu') );
    $this->gauche = $cont_jeu->add( new interf_bal_cont('div', 'deplacement') );
    $this->droite = $cont_jeu->add( new interf_bal_cont('div', 'information') );
  }
  function set_gauche($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_cadre_carte();
    $this->gauche->add( $fils );
  }
  function set_droite($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_inventaire(joueur::get_perso(), 'perso', 'utile', true);
    $this->droite->add( $fils );
  }
  function maj_perso() {}
  function set_carte($fils)
  {
    $this->gauche->add( $G_interf->creer_cadre_carte($fils) );
  }
  function set_dialogue($fils)
  {
  }
  protected function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Wiki', 'http://wiki.starshine-online.com/'), false);
    $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
  }
}
?>