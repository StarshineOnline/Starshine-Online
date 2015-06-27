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
	const prefixe_fichiers = '';
  function __construct($theme)
  {
    interf_html::__construct("Starshine Online", 'utf-8', false);
    // Méta-informations
    $this->meta('language', 'fr');
    $this->meta_http('content-type', 'text/html; charset=utf-8');
    $this->meta_http('content-language', 'fr');
    // feuilles de style
    $this->css(static::prefixe_fichiers.'css/jquery.ui.all.css');
    $this->css(static::prefixe_fichiers.'css/bootstrap.css');
    $this->css(static::prefixe_fichiers.'css/bootstrap-'.$theme.'.css');
    $this->css(static::prefixe_fichiers.'css/icones.css');
    $this->css(static::prefixe_fichiers.'css/jquery.dataTables.css');
    $this->css(static::prefixe_fichiers.'css/dataTables.bootstrap.css');
    // javascript
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery-2.0.2.min.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery-ui-1.10.3.custom.min.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.dataTables.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.hotkeys.js');
    $this->javascript(static::prefixe_fichiers.'javascript/bootstrap.min.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/dataTables.bootstrap.js');
    $this->javascript(static::prefixe_fichiers.'javascript/bootstrap-wysiwyg.js');
    $this->javascript(static::prefixe_fichiers.'javascript/bbcodeParser.js');
    $this->javascript(static::prefixe_fichiers.'javascript/fonction.js');
    // icone de favori
    $this->link('icon', 'image/png', 'image/favicon.png');
  }
}

/**
 * Classe de base pour pages internes
 * Concerne toutes les pages une fois connecté
 */
abstract class interf_sso_int extends interf_sso
{
  protected $menu;
  function __construct($theme)
  {
    interf_sso::__construct($theme);
    // feuilles de style
    $this->css(static::prefixe_fichiers.'css/texture.css');
    $this->css(static::prefixe_fichiers.'css/texture_low.css');
    $this->css(static::prefixe_fichiers.'css/interfacev3.css');
    $this->css(static::prefixe_fichiers.'css/interface-'.$theme.'.css');
    // javascript
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.hoverIntent.minified.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.cluetip.min.js');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/atooltip.min.jquery.js');
    $this->javascript(static::prefixe_fichiers.'javascript/overlib/overlib.js');
    // Barre de menu
    $joueur = joueur::factory();
    $this->menu = $this->add( new interf_navbar('', 'barre_menu', 'navbar-inverse', 'icone-sso', 'icone icone-sso') );
    $this->menu_droite();
    $menu_joueur = $this->menu->add_elt(new interf_nav_deroul($joueur->get_pseudo()), false);
    $this->menu_joueur($menu_joueur);
    $menu_joueur->add( new interf_elt_menu('Options', static::prefixe_fichiers.'option.php', 'return charger(this.href);') );
    $menu_joueur->add( new interf_elt_menu('Signaler un bug', 'http://bug.starshine-online.com/') );
    $menu_joueur->add( new interf_elt_menu('Votes & dons', static::prefixe_fichiers.'vote_dons.php', 'return charger(this.href);') );
    $admin = $joueur->get_droits() & joueur::droit_interf_admin;
    $persos = (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) || $joueur->get_droits() & joueur::droit_pnj;
    if( $admin || $persos )
    {
      $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
      if( $persos )
        $menu_joueur->add( new interf_elt_menu('Changer de perso', static::prefixe_fichiers.'changer_perso.php', 'return charger(this.href);') );
      if( $admin )
        $menu_joueur->add( new interf_elt_menu('Administration', static::prefixe_fichiers.'./admin/') );
    }
    $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
    $menu_joueur->add( new interf_elt_menu('Déconnecter', '#', 'if(confirm(\'Voulez vous déconnecter ?\')) { document.location.href=\''.static::prefixe_fichiers.'index.php?deco=ok\'; };') );
  }
  abstract protected function menu_droite();
  abstract protected function menu_joueur($menu_joueur);
}
?>