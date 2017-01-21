<?php
/**
 * @file interf_sso.class.ph
 * Interface principale de SSO
 */

/**
 * Classe de base pour l'interface principale de SSO
 */
abstract class interf_sso extends interf_html
{
  protected $menu;
	const prefixe_fichiers = '';
  const droite_prem = false;
  function __construct($theme)
  {
    interf_html::__construct("Starshine Online", 'utf-8', false);
    // Méta-informations
    $this->meta('language', 'fr');
    $this->meta_http('content-type', 'text/html; charset=utf-8');
    $this->meta_http('content-language', 'fr');
    // feuilles de style
    $this->css(root_url.'css/jquery.ui.all.css');
    $this->css(root_url.'css/bootstrap.css');
    $this->css(root_url.'css/bootstrap-'.$theme.'.css');
    $this->css(root_url.'css/icones.css');
    $this->css(root_url.'css/commun.css');
    $this->css(root_url.'css/jquery.dataTables.css');
    $this->css(root_url.'css/dataTables.bootstrap.css');
    // javascript
    $this->javascript(root_url.'javascript/jquery/jquery-2.0.2.min.js');
    $this->javascript(root_url.'javascript/jquery/jquery-ui-1.10.3.custom.min.js');
    $this->javascript(root_url.'javascript/jquery/jquery.dataTables.js');
    $this->javascript(root_url.'javascript/jquery/jquery.hotkeys.js');
    $this->javascript(root_url.'javascript/bootstrap.min.js');
    $this->javascript(root_url.'javascript/jquery/dataTables.bootstrap.js');
    $this->javascript(root_url.'javascript/bootstrap-wysiwyg.js');
    $this->javascript(root_url.'javascript/bbcodeParser.js');
    $this->javascript(root_url.'javascript/fonction.js');
    // icone de favori
    $this->link('icon', 'image/png', 'image/favicon.png');
    
    $this->menu = $this->add( new interf_navbar('', 'barre_menu', 'navbar-inverse', 'icone-sso', 'icone icone-sso', root_url.$this::page, $this::droite_prem) );
  }
  function aff_menu_joueur()
  {
    $joueur = joueur::factory();
    $menu_joueur = $this->menu->add_elt(new interf_nav_deroul($joueur->get_pseudo()), false);
    $this->menu_joueur($menu_joueur);
    $menu_joueur->add( new interf_elt_menu('Options', root_url.'option.php', 'return charger(this.href);') );
    $menu_joueur->add( new interf_elt_menu('Signaler un bug', 'http://bug.starshine-online.com/') );
    $menu_joueur->add( new interf_elt_menu('Votes & dons', root_url.'vote_dons.php', 'return charger(this.href);') );
    $admin = $joueur->get_droits() & joueur::droit_interf_admin;
    $persos = (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) || $joueur->get_droits() & joueur::droit_pnj;
    //if( $admin || $persos )
    {
      $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
      $menu_joueur->add( new interf_elt_menu('Page d\'accueil', root_url.'index.php') );
			if( $persos )
        $menu_joueur->add( new interf_elt_menu('Changer de perso', root_url.'changer_perso.php', 'return charger(this.href);') );
      if( $admin )
        $menu_joueur->add( new interf_elt_menu('Administration', root_url.'./admin/') );
    }
    $menu_joueur->add( new interf_bal_smpl('li', null, null, 'divider') );
    $menu_joueur->add( new interf_elt_menu('Déconnecter', '#', 'if(confirm(\'Voulez vous déconnecter ?\')) { document.location.href=\''.static::prefixe_fichiers.'index.php?deco=ok\'; };') );
	}
  protected function menu_joueur($menu_joueur) {}
  protected function aff_fin($idsite=1)
  {
    global $G_no_piwik;
    // Piwik
    if( !isset($G_no_piwik) || $G_no_piwik != true )
    {
    	self::code_js('var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.starshine-online.com/piwik/" : "http://www.starshine-online.com/piwik/");');
    	self::code_js('document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));');
    	self::code_js('try {');
    	self::code_js('var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);');
    	self::code_js('piwikTracker.trackPageView();');
    	self::code_js('piwikTracker.enableLinkTracking();');
    	self::code_js('} catch( err ) {}');
    	$noscript = $this->add( new interf_bal_cont('noscript') );
    	$img_piwik = $noscript->add( new interf_img('http://www.starshine-online.com/piwik/piwik.php?idsite='.$idsite) );
    	$img_piwik->set_attribut('style', 'border:0');
		}
    
    $this->code_js('maj_tooltips();');
	}
	static function change_url()
	{
  	global $G_url;
		interf_base::code_js('history.replaceState(null, "Starshine Online", "'.$G_url->get_base().'");');
	}
}

/**
 * Classe de base pour pages internes
 * Concerne toutes les pages une fois connecté
 */
abstract class interf_sso_int extends interf_sso
{
  const page = 'interface.php';
  function __construct($theme)
  {
    interf_sso::__construct($theme);
    $this->set_attribut('ng:app', 'ssoApp');
    //$this->set_attribut_body('ng:controller', 'ssoPrinc');
    // feuilles de style
    $this->css(root_url.'css/texture.css');
    $this->css(root_url.'css/texture_low.css');
    $this->css(root_url.'css/interface.css');
    $this->css(root_url.'css/interface-'.$theme.'.css');
    // javascript
    $this->javascript(root_url.'javascript/jquery/jquery.hoverIntent.minified.js');
    $this->javascript(root_url.'javascript/jquery/jquery.cluetip.min.js');
    $this->javascript(root_url.'javascript/jquery/atooltip.min.jquery.js');
    //$this->javascript(static::prefixe_fichiers.'javascript/overlib/overlib.js');
    $this->javascript('https://ajax.googleapis.com/ajax/libs/angularjs/1.4.4/angular.min.js');
    $this->javascript(root_url.'javascript/app.js');
    // Barre de menu
    $this->menu_droite();
    $this->aff_menu_joueur();
    
  }
  abstract protected function menu_droite();
}
?>
