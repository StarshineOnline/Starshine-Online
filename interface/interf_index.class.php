<?php
include_once(root.'fonction/forum.inc.php');

class interf_index extends interf_sso
{
  const page = 'index2.php';
  protected $contenu;
	function __construct($theme)
	{
		global $estConnexionReussie, $erreur_login;
		parent::__construct($theme);
    $this->css(static::prefixe_fichiers.'css/site.css');
    $this->css(static::prefixe_fichiers.'css/site-'.$theme.'.css');
    $this->css(static::prefixe_fichiers.'css/jquery.lightbox-0.5.css');
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/jquery.lightbox-0.5.min.js');
		
    // Barre de menu
    $joueur = joueur::factory();
    $this->menu->add_elt( new interf_elt_menu('Infos', self::page.'?page=infos', 'return charger(this.href);') );
    $this->menu->add_elt( new interf_elt_menu('Captures d\'écran', self::page.'?page=captures', 'return charger(this.href);') );
    //$this->menu->add_elt( new interf_elt_menu('Charte', self::page.'page=charte', 'return charger(this.href);') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    /*$autres->add_elt( new interf_elt_menu('Diplomatie', self::page.'?page=', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Cartes', self::page.'?page=', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Bestiaire', self::page.'?page=', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Histoire de Starshine', self::page.'?page=', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Statistiques', self::page.'?page=', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Classement', self::page.'?page=', 'return charger(this.href);') );*/
    
    
    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
    if( $joueur )
    {
	    $nbr_posts = get_nbr_posts_forum(joueur::get_perso());
	    $forum->get_lien()->add( new interf_bal_smpl('span', $nbr_posts ? $nbr_posts : '', 'nbr_posts', 'badge') );
	    /// @todo à améliorer
	    //$persos = (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) || $joueur->get_droits() & joueur::droit_pnj;
	    if( count( perso::create('id_joueur', $joueur->get_id()) ) == 0 )
    		$this->menu->add_elt(new interf_elt_menu('Créer un personnage', self::page.'?page=creer_perso'), false);
    	$this->aff_menu_joueur();
		}
		else
		{
    	$this->menu->add_elt(new interf_elt_menu('Créer un compte', self::page.'?page=creer_compte'), false);
    	$this->menu->add_elt(new interf_menu_div('Connexion'), false);
		}
		
		$main = $this->add( new interf_bal_cont('main') );
		$this->contenu = $main->add( new interf_bal_cont('section', 'contenu') );
		
		// pub
	  //if( file_exists(root.'pub.php') )
	  {
	  	$pub = $this->add( new interf_bal_smpl('iframe', '', 'pub') );
	  	//$pub->set_attribut('src', 'pub.php');
	  	$pub->set_attribut('scrolling', 'no');
		}
		// erreur de connexion
		if( $estConnexionReussie === false )
		{
			$dlg = set_dialogue( new interf_dialogBS('Erreur', true) );
			$dlg->add( new interf_alerte(interf_alerte::msg_erreur, false, false, $erreur_login) );
			$dlg->ajout_btn('Ok', 'ferme');
		}
		// Partenaires
		$part = $this->add( new interf_bal_cont('div', 'partenaires') );
		$part->add( new interf_bal_smpl('span', 'Partenaires :') );
		$lien1 = $part->add( new interf_bal_smpl('a', 'Jeux sur navigateur en ligne') );
		$lien1->set_attribut('href', 'http://www.jeux-sociaux.org/navigateur/');
		
    $this->aff_fin();
	}
	
  function set_dialogue($fils)
  {
  	$dlg = $this->add( new interf_bal_cont('div', 'modal', 'modal fade') );
  	$dlg->set_attribut('role', 'dialog');
  	$dlg->set_attribut('tabindex', '-1');
  	$dlg->set_attribut('aria-labelledby', 'modalLabel');
  	$dlg->code_js('$("#modal").modal("show");');
  	return $dlg->add( $fils );
  }
	
	function set_contenu($fils)
	{
		return $this->contenu->add( $fils );
	}
}

class interf_index_infos extends interf_cont
{
	function __construct()
	{
		$this->add( new interf_bal_smpl('span', false, false, 'logo') );
		$pres = $this->add( new interf_bal_cont('div', 'presentation') );
		$pres->add( new interf_bal_smpl('h4', 'Présentation') );
		$txt = "Bienvenue dans le monde de Starshine-Online.
Pour l'instant au stade de la bêta (c'est à dire en phase d'équilibrage et d'amélioration du monde), Starshine-Online sera un jeu de rôle massivement multijoueur (MMORPG) en tour par tour.<br />
<br />
Il vous permettra d'incarner un grand héros de l'univers Starshine, peuplé de nombreuses créatures et d'autres héros ennemis prêts à tout pour détruire votre peuple.<br />
<br /><br />
Il est recommandé d'utiliser <strong>un navigateur dernière génération (évitez Internet Explorer)</strong> pour jouer à Starshine, nous vous conseillons <a href='http://www.mozilla-europe.org/'>Firefox</a> ou bien <a href='http://www.google.fr/chrome'>Google Chrome</a>.
N'oubliez pas de reporter les bugs et problèmes, et d'apporter vos suggestions sur le forum.";
		$pres->add( new interf_bal_smpl('p', $txt) );
		
		$ancienne = annonce::derniere(10);
		if( $ancienne )
		{
			$nouv = $this->add( new interf_bal_cont('div', 'nouvelles') );
			$nouv->add( new interf_bal_smpl('h4', 'Nouveautés') );
			foreach($ancienne as $a)
			{
				$art = $nouv->add( new interf_bal_cont('article') );
				$art->add( new interf_bal_smpl('span', date('d/m/Y H:i', strtotime($a->get_date())), false, 'small') );
				$art->add( new interf_bal_smpl('p', texte::parse_url($a->get_message())) );
			}
		}
	}
}

class interf_index_captures extends interf_bal_cont
{
	function __construct()
	{
		parent::__construct('div', 'captures'/*, 'row'*/);
		$minis = glob(root.'/image/screenshots/*-mini.png');
		foreach($minis as $m)
		{
			$p = pathinfo($m);
			$titre = substr($p['filename'], 0, strlen($p['filename'])-5);
			$div = $this->add( new interf_bal_cont('div', false, 'col-sm-6 col-md-4') );
			$lien = $div->add( new interf_bal_cont('a', false, 'thumbnail') );
			$lien->set_attribut('href', 'image/screenshots/'.$titre.'.png');
			$lien->set_attribut('title', $titre);
			$img = $lien->add( new interf_img('image/screenshots/'.$p['basename'], $titre) );
			$leg = $lien->add( new interf_bal_smpl('div', $titre, false, 'caption') );
		}
		self::code_js('$(function()
			{
				$("#captures a").lightBox({
					fixedNavigation:true,
					overlayBgColor: "#000000",
					overlayOpacity: 0.6,
					imageLoading: "image/jquery-lightbox/loading.gif",
					imageBtnClose: "image/jquery-lightbox/btn-close.gif",
					imageBtnPrev: "image/jquery-lightbox/btn-prev.gif",
					imageBtnNext: "image/jquery-lightbox/btn-next.gif",
					imageBlank: "image/jquery-lightbox/blank.gif",
					containerResizeSpeed: 350,
					txtImage: "Image",
					txtOf: "sur"
				});
			});');
	}
}

class interf_index_compte extends interf_cont
{
	function __construct()
	{
	}
}

class interf_index_perso extends interf_cont
{
	function __construct()
	{
	}
}


?>