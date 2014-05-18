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
  protected $contenu;
  protected $id;
  protected $divs;  ///< balises divs contenant le contenu de chaque onglets.

  /**
   * Constructeur
   * @param  $id_cont        id de la balise ul permettant la navigation.
   * @param  $id_cont        id de la balise div contenant tous les onglets.
   * @param  $class_cont     classe de la balise contenant tous les onglets.
   */
  function __construct($id_tabs, $id_cont, $class_cont=false)
  {
    interf_bal_cont::__construct('div', $id_cont, $class_cont);
    $this->haut = $this->add( new interf_bal_cont('ul', $id_tabs, 'nav nav-tabs') );
    $this->contenu = $this->add( new interf_bal_cont('div', null, 'tab-content') );
    $this->divs = array();
    $this->id = $id_cont;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  /*protected function debut()
  {
    $this->haut->affiche();
    interf_bal_cont::debut();
  }*/
  /**
   * Ajoute un onglet.
   * @param  $nom         nom à afficher sur l'onglet
   * @param  $adresse     adresse de la page web pour le contenu
   * @param  $id          id de la balise du contenu.
   * @param  $selection   indique si l'onglet est sélectionné
   */
  function add_onglet($nom, $adresse, $id, $classe, $selection=false)
  {
    $classe .= ' tab-pane';
    if( $selection )
      $classe .= ' active';
    //$li = $this->haut->add( new interf_elt_menu($nom, '', 'charge_tab(this, \''.$this->id.'\');') );
    $li = $this->haut->add( new interf_elt_menu($nom, '#'.$id, 'charge_tab(this, \''.$id.'\');') );
    $lien = $li->get_lien();
    if($selection)
    {
      $li->set_attribut('class', 'active');
      $classe .= ' active';
    }
    $lien->set_attribut('data-toggle', 'tab');
    $lien->set_attribut('data-url', $adresse);
    /*if( !$selection )
      $this->divs[] = $id;*/
    $div =  $this->contenu->add( new interf_bal_cont('div', $id, $classe) );
    $this->divs[$id] = &$div;
    return $div;
  }

  /**
   * Renvoie un element div pour le contenu d'un onglet
   *
   * @param  $id  id de l'élément
   */
  function &get_onglet($id)
  {
    return $this->divs[$id];
  }
}

/**
 * Affiche un message d'alerte
 */
class interf_alerte extends interf_bal_cont
{
  function __construct($type=null, $ferme=true, $id=null)
  {
    $classe = 'alert'.($type ? ' alert-'.$type : '');
    if( $ferme )
      $classe .= ' alert-dismissable';
    interf_bal_cont::__construct('div', $id, $classe);
    if( $ferme )
    {
      //interf_base::code_js( '$("'.($id?'#'.$id:'.alert').'").alert();' );
      $btn = $this->add( new interf_bal_smpl('button', '&times;', null, 'close') );
      $btn->set_attribut('type', 'button');
      $btn->set_attribut('data-dismiss', 'alert');
      $btn->set_attribut('aria-hidden', 'true');
    }
  }

  function add_message($msg)
  {
    return $this->add( new interf_txt($msg) );
  }
}

/**
 * Affiche une boite de dialogue.
 * La boite de dialogue est gérée par jQuery.
 */
class interf_dialog extends interf_bal_cont
{
  private $modal;  ///< Indique si la boite est modale.
  private $ouvre;  ///< Indique si la boite s'ouvre dès l'affichage de la page.
  private $height = null;  ///< Hauteur de la boite.
  private $width = null;  ///< Largeur de la boite.
  private $boutons = array();  /// Liste des boutons.

  /**
   * Constructeur
   * @param  $id        id de la balise.
   * @param  $titre     titre de la boite de dialogue.
   * @param  $modal     indique si la boite est modale.
   * @param  $modal     indique si la boite s'ouvre dès l'affichage de la page.
   */
  function __construct($id, $titre, $modal = true, $ouvre = false)
  {
    interf_bal_cont::__construct('div', $id);
    $this->set_attribut('title', $titre);
    $this->modal = $modal;
    $this->ouvre = $ouvre;
  }
  /**
   * Définit les dimensions de la boite de dialogue
   * @param  $height    hauteur de la boite.
   * @param  $width     largeur de la boite.
   */
  function set_size($height, $width)
  {
    $this->height = $height;
    $this->width = $width;
  }
  /**
   * Ajoute un bouton à la boite de dialogue
   * @param  $nom   nom du bouton
   * @param  $code  code javascript exécuté lorsque l'on clique sur le bouton.
   */
  function ajout_btn($nom, $code=null)
  {
    $this->boutons[$nom] = $code;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    $code = '$("#'.$this->attributs['id'].'").dialog({autoOpen: '.($this->ouvre?'true':'false').', ';
    if( $this->height )
      $code .= 'height: '.$this->height.', ';
    if( $this->width )
      $code .= 'width: '.$this->width.', ';
    $code .= 'modal: '.($this->modal?'true':'false');
    if( count($this->boutons) )
    {
      $btn = array();
      foreach($this->boutons as $nom=>$codeBtn)
      {
        $btn[] = '"'.$nom.'":function() {'.$codeBtn.' $( this ).dialog( "close" ); }';
      }
      $code .= ', buttons: {'.implode(',', $btn).'}';
    }
    interf_base::CodeJS($code.'});');
    $this->ouvre($this->Creerbalise());
  }
  /// Renvoie le code javascript pour l'ouverture de la boite de dialogue
  function code_affiche()
  {
    return '$(\'#'.$this->id.'\').dialog(\'open\');';
  }
}

/**
 * Affiche le contenu d'une boite de dialogue modale
 * La boite de dialogue est gérée par Bootstrap.
 */
class interf_dialogBS extends interf_princ
{
  protected $titre; ///< titre de la boite de dialogue (ou null s'il n'y en a pas).
  private $boutons = array();  /// Liste des boutons.

  function __construct($titre=null)
  {
    $this->titre = $titre;
  }
  /**
   * Ajoute un bouton à la boite de dialogue
   * @param  $nom   nom du bouton
   * @param  $code  code javascript exécuté lorsque l'on clique sur le bouton.
   */
  function ajout_btn($nom, $code=null, $style='default')
  {
    $btn['code'] = $code;
    $btn['style'] = $style;
    $this->boutons[$nom] = $btn;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ouvre('div class="modal-dialog"');
    $this->ouvre('div class="modal-content"');
    if($this->titre)
    {
      $this->ouvre('div class="modal-header"');
      $this->balise('h4', $this->titre, array('class'=>'modal-title'));
      $this->ferme('div');
    }
    $this->ouvre('div class="modal-body"');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme('div');
    if( $this->boutons )
    {
      $this->ouvre('div class="modal-footer"');
      foreach($this->boutons as $nom=>$btn)
      {
        $attr = array('type'=>'button', 'class'=>'btn btn-'.$btn['style']);
        if( $btn['code'] == 'fermer' )
          $attr['data-dismiss'] = 'modal';
        else if( $btn['code'] )
          $attr['onclick'] = $btn['code'];
        $this->balise('button', $nom, $attr);
      }
      $this->ferme('div');
    }
    $this->ferme('div');
    $this->ferme('div');
    $this->affiche_js();
  }
}

/**
 * Barre de navigation
 */
class interf_navbar extends interf_bal_cont
{
  protected $menu;
  protected $gauche;
  protected $droite = null;

  function __construct($titre=null, $id=null, $classe=null, $id_titre=null, $class_titre=null)
  {
    interf_bal_cont::__construct('nav', $id, 'navbar '.$classe);
    $cont = /*$this->*/parent::add( new interf_bal_cont('div', null, 'container') );
    $header = $cont->add( new interf_bal_cont('div', null, 'navbar-header') );
    $collapse = $header->add( new interf_bal_cont('button', null, 'navbar-toggle') );
    $collapse->set_attribut('data-toggle', 'collapse');
    $collapse->set_attribut('data-target', 'navbar');
    $collapse->add( new interf_bal_smpl('span', 'Afficher / cacher menu', null, 'sr-only') );
    $collapse->add( new interf_bal_smpl('span', null, null, 'icon-bar') );
    $collapse->add( new interf_bal_smpl('span', null, null, 'icon-bar') );
    $collapse->add( new interf_bal_smpl('span', null, null, 'icon-bar') );
    if( $titre !== null )
      $header->add( new interf_bal_smpl('a', $titre, $id_titre, 'navbar-brand '.$class_titre) )->set_attribut('href', '#');
    $this->menu = $cont->add( new interf_bal_cont('div', 'navbar', 'collapse navbar-collapse') );
    $this->gauche = $this->menu->add( new interf_bal_cont('ul', null, 'nav navbar-nav') );
  }

  function &add_elt($elt, $gauche=true)
  {
    if( $gauche )
      return $this->gauche->add( $elt );
    else
    {
      if( !$this->droite )
        $this->droite = $this->menu->add( new interf_bal_cont('ul', null, 'nav navbar-nav navbar-right') );
      return $this->droite->add( $elt );
    }
  }
}

/**
 * Menus déroulant dans une barre de navigation
 */
class interf_nav_deroul extends interf_elt_menu
{
  protected $liste;
  function __construct($nom, $id=null)
  {
    interf_elt_menu::__construct($nom.'<b class="caret"></b>', '#', false, $id, 'dropdown');
    $this->lien->set_attribut('class', 'dropdown-toggle');
    $this->lien->set_attribut('data-toggle', 'dropdown');
    $this->liste = interf_cont::add( new interf_bal_cont('ul', null, 'dropdown-menu') );
  }
  function &add($elt)
  {
    $this->liste->add($elt);
  }
}
 
/**
* Panneau Bootstrap
*/
class	interf_panneau extends interf_bal_cont
{
	protected $body=false;
	function __construct($titre, $id, $elt_titre='h3', $collaps=false, $montre=false, $style='default')
	{
		parent::__construct('div', $id, 'panel panel-'.$style);
		if( $titre )
		{
			if( $elt_titre )
				$entete = $this->add( new interf_bal_cont('div', false, 'panel-heading') );
			else
				$entete = &$this;
			if( !is_object($titre) )
				$titre = new interf_txt($titre);
			$entete->add( $titre );
		}
		if( $collaps )
			$parent = $this->add( new interf_bal_cont('div', $collaps, 'panel-collapse collapse'.($montre?' in':'')) );
		else
			$parent = &$this;
		$this->body = $parent->add( new interf_bal_cont('div', null, 'panel-body')  );
	}
  /// Ajoute un élément fils
  function &add(&$fils)
  {
  	if( $this->body )
    	return $this->body->add($fils);
    else
    	return parent::add($fils);
  }
  // Ajoute un pied
  function set_footer($footer, $id=null)
  {
  	if( is_object($footer) )
			parent::add( interf_bal_cont('div', $id, 'panel-footer') )->add($footer);
		else
  		parent::add( new interf_bal_smpl('div', $footer, $id, 'panel-footer') );
	}
}

/**
 * Accordéon bootstrap
 */
class interf_accordeon extends interf_bal_cont
{
	protected $id;
	function __construct($id)
	{
		parent::__construct('div', $id, 'panel-group');
		$this->id = $id;
	}
	// Ajoute un panneau
  function &nouv_panneau($titre, $id, $montre=false, $style='default')
  {
  	if( is_object($titre) )
  		$titre = new interf_bal_cont('a');
  	else
  		$titre = new interf_bal_smpl('a', $titre);
  	$titre->set_attribut('href', '#'.$id);
  	$titre->set_attribut('data-toggle', 'collapse');
  	$titre->set_attribut('data-parent', '#'.$this->id);
    return $this->add( new interf_panneau($titre, null, 'h4', $id, $montre, $style) );
  }
}

/// Popver bootstrap pour montrer des informations
class interf_infos_popover extends interf_princ
{
	protected $tbl;
  /**
   * Constructeur
   * @param $noms    	noms des informations à afficher
   * @param $valeurs	valeurs des informations à afficher
   */
  function __construct($noms=null, $valeurs=null)
  {
    $this->tbl = $this->add( new interf_tableau() );
    if( $noms )
    {
	    for($i=0; $i<count($noms); $i++)
	    {
	    	if( $i > 0 )
	      	$this->tbl->nouv_ligne();
	      $this->tbl->nouv_cell($noms[$i], null, null, true);
	      $this->tbl->nouv_cell($valeurs[$i]);
	    }
		}
  }
  
  /**
   * Ajoute une informatipon
   * @param $nom    	nom de l'information à afficher
   * @param $valeur		valeur de l'information à afficher
   */
  function nouv_info($nom, $valeur)
  {
	  $this->tbl->nouv_ligne();
	  $this->tbl->nouv_cell($nom, null, null, true);
	  $this->tbl->nouv_cell($valeur);
	}
}
?>
