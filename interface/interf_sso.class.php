<?php
/**
 * @file interf_sso.class.ph
 * Interface principale de SSO
 */

/**
 * Classe de base pour l'interface principale de SSO
 */
class interf_sso extends interf_html
{
  function __construct()
  {
    interf_html::__construct("Starshine Online", 'utf-8', false);
    // Méta-informations
    $this->meta('language', 'fr');
    $this->meta_http('content-type', 'text/html; charset=utf-8');
    $this->meta_http('content-language', 'fr');
    // feuilles de style
    $this->css('css/jquery.ui.all.css');
    $this->css('css/bootstrap.css');
    $this->css('css/bootstrap-theme.min.css');
    $this->css('css/icones.css');
    $this->css('css/jquery.dataTables.css');
    $this->css('css/dataTables.bootstrap.css');
    // javascript
    $this->javascript('javascript/jquery/jquery-2.0.2.min.js');
    $this->javascript('javascript/jquery/jquery-ui-1.10.3.custom.min.js');
    $this->javascript('javascript/jquery/jquery.dataTables.js');
    $this->javascript('javascript/jquery/jquery.hotkeys.js');
    $this->javascript('javascript/bootstrap.min.js');
    $this->javascript('javascript/jquery/dataTables.bootstrap.js');
    $this->javascript('javascript/bootstrap-wysiwyg.js');
    $this->javascript('javascript/bbcodeParser.js');
    $this->javascript('javascript/fonction.js');
  }
}

/**
 * Classe de base pour pages internes
 * Concerne toutes les pages une fois connecté
 */
abstract class interf_sso_int extends interf_sso
{
  protected $menu;
  function __construct()
  {
    interf_sso::__construct();
    // feuilles de style
    $this->css('css/texture.css');
    $this->css('css/texture_low.css');
    $this->css('css/interfacev3.css');
    // javascript
    $this->javascript('javascript/jquery/jquery.hoverIntent.minified.js');
    $this->javascript('javascript/jquery/jquery.cluetip.min.js');
    $this->javascript('javascript/jquery/atooltip.min.jquery.js');
    $this->javascript('javascript/overlib/overlib.js');
    // Barre de menu
    $joueur = joueur::factory();
    $this->menu = $this->add( new interf_navbar('', 'barre_menu', 'navbar-inverse', 'icone-sso', 'icone icone-sso') );
    $this->menu_droite();
    $menu_joueur = $this->menu->add_elt(new interf_nav_deroul($joueur->get_pseudo()), false);
    $this->menu_joueur($menu_joueur);
    $menu_joueur->add( new interf_elt_menu('Options', '#', 'affichePopUp(\'option.php\');') );
    //$menu_joueur->add( new interf_elt_menu('Son', '#', 'showSoundPanel();') );
    $menu_joueur->add( new interf_elt_menu('Signaler un bug', 'http://bug.starshine-online.com/') );
    $admin = $joueur->get_droits() & joueur::droit_interf_admin;
    $persos = (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) or $joueur->get_droits() & joueur::droit_pnj;
    if( $admin or $persos )
    {
      $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
      if( $persos )
        $menu_joueur->add( new interf_elt_menu('Changer de perso', 'changer_perso.php', 'return charger(this.href);') );
      if( $admin )
        $menu_joueur->add( new interf_elt_menu('Administration', './admin/') );
    }
    $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
    $menu_joueur->add( new interf_elt_menu('Déconnecter', '#', 'if(confirm(\'Voulez vous déconnecter ?\')) { document.location.href=\'index.php?deco=ok\'; };') );
  }
  abstract protected function menu_droite();
  abstract protected function menu_joueur($menu_joueur);
}
?>