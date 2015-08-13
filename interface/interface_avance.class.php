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
  protected $menus = array();

  /**
   * Constructeur
   * @param  $id_tabs        id de la balise ul permettant la navigation.
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
  /**
   * Ajoute un onglet.
   * @param  $nom         nom à afficher sur l'onglet
   * @param  $adresse     adresse de la page web pour le contenu
   * @param  $id          id de la balise du contenu.
   * @param  $selection   indique si l'onglet est sélectionné
   */
  function add_onglet($nom, $adresse, $id, $classe='', $selection=false, $menu=false)
  {
    $classe .= ' tab-pane';
    if( $selection )
      $classe .= ' active';
    //$li = $this->haut->add( new interf_elt_menu($nom, '', 'charge_tab(this, \''.$this->id.'\');') );
    $li = new interf_elt_menu($nom, '#'.$id, 'charge_tab(this, \''.$id.'\');');
    if( $menu )
    {
    	if( array_key_exists($menu, $this->menus) )
    		$parent = &$this->menus[$menu];
    	else
    	{
    		$li_menu = $this->haut->add( new interf_bal_cont('li', false, 'dropdown') );
    		$lien = $li_menu->add( new interf_bal_cont('a', '#', false, 'dropdown-toggle') );
    		$lien->set_attribut('data-toggle', 'dropdown');
    		$lien->add( new interf_txt($menu) );
    		$lien->add( new interf_bal_smpl('span', '', false, 'caret') );
    		$parent = $li_menu->add( new interf_bal_cont('ul', false, 'dropdown-menu') );
    		$this->menus[$menu] = &$parent;
			}
			$parent->add( $li );
		}
		else
    	$this->haut->add( $li );
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
  
  // Renvoie la partie haute (liste des onglets)
  function &get_haut()
  {
  	return $this->haut;
	}
}

/**
 * Affiche un message d'alerte
 */
class interf_alerte extends interf_bal_cont
{
	const msg_info = 'info';
	const msg_succes = 'success';
	const msg_erreur = 'danger';
	const msg_avertis = 'warning';
  protected static $alertes = array();
	
  function __construct($type=null, $ferme=true, $id=false, $texte=null)
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
    if( $texte )
    	 $this->add( new interf_txt($texte) );
  }

  function add_message($msg)
  {
    return $this->add( new interf_txt($msg) );
  }
  
  /**
   * Crée et enregistre un message qui sera affiché grâce à la methode affiche_enregistres
   * @param  $type		type du message (cf. constantes msg_*)
   * @param  $texte		textre du message
   * @return 		l'objet interf_alerte créé.
   */  
  static function &enregistre($type, $texte=null, $id=null, $ferme=true)
  {
  	$alerte = new interf_alerte($type, $ferme, $id);
  	if($texte)
			$alerte->add_message( $texte );
  	self::$alertes[] = &$alerte;
  	return $alerte;
	}
	
	/**
	 * Affiche les message précédement enregistrés
	 * @param  $parent  	Objet parent des alerte
	 */	
	static function aff_enregistres($parent)
	{
		$n = 0;
		foreach(self::$alertes as $a)
		{
			$parent->add($a);
			unset($a);
			$n++;
		}
		self::$alertes = array();
		return $n;
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
    interf_base::code_js($code.'});');
    $this->ouvre($this->creer_balise());
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
class interf_dialogBS extends interf_cont//interf_princ
{
  protected $titre; ///< titre de la boite de dialogue (ou null s'il n'y en a pas).
  protected $btn_ferme; ///< Indique s'il y a un bouton (croix) pour fermer la boite dans l'en-tête, nécessite un titre.
  private $boutons = array();  /// Liste des boutons.
  protected $id;

  function __construct($titre=null, $btn_ferme=false, $id=false)
  {
    $this->titre = $titre;
    $this->btn_ferme = $btn_ferme;
    $this->id = $id;
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
    $this->ouvre('div class="modal-dialog"'.($this->id ? ' id="'.$this->id.'"' : ''));
    $this->ouvre('div class="modal-content"');
    if($this->titre)
    {
      $this->ouvre('div class="modal-header"');
      if( $this->btn_ferme )
      {
      	$this->ouvre('button type="button" class="close" data-dismiss="modal"');
      	$this->balise('span', '&times;', array('aria-hidden'=>'true'));
      	$this->balise('span', 'Fermer', array('class'=>'sr-only'));
      	$this->ferme('button');
			}
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

  function __construct($titre=null, $id=null, $classe=null, $id_titre=null, $class_titre=null, $lien_titre='#')
  {
    interf_bal_cont::__construct('nav', $id, 'navbar '.$classe);
    $cont = /*$this->*/parent::add( new interf_bal_cont('div', null, 'container') );
    $header = $cont->add( new interf_bal_cont('div', null, 'navbar-header') );
    $collapse = $header->add( new interf_bal_cont('button', null, 'navbar-toggle') );
    $collapse->set_attribut('data-toggle', 'collapse');
    $collapse->set_attribut('data-target', '#navbar');
    $collapse->add( new interf_bal_smpl('span', 'Afficher / cacher menu', null, 'sr-only') );
    $collapse->add( new interf_bal_smpl('span', '', null, 'icon-bar') );
    $collapse->add( new interf_bal_smpl('span', '', null, 'icon-bar') );
    $collapse->add( new interf_bal_smpl('span', '', null, 'icon-bar') );
    if( $titre !== null )
      $header->add( new interf_bal_smpl('a', $titre, $id_titre, 'navbar-brand '.$class_titre) )->set_attribut('href', $lien_titre);
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
	function __construct($titre, $id, $elt_titre='h3', $collaps=false, $montre=false, $style='default', $body=true)
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
		if( $body )
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
  function &nouv_panneau($titre, $id, $montre=false, $style='default', &$icone=null)
  {
  	if( is_object($titre) )
  	{
  		$lien_titre = new interf_bal_cont('a');
  		$lien_titre->add($titre);
		}
  	else
  		$lien_titre = new interf_bal_smpl('a', $titre);
  	$lien_titre->set_attribut('href', '#'.$id);
  	$lien_titre->set_attribut('data-toggle', 'collapse');
  	$lien_titre->set_attribut('data-parent', '#'.$this->id);
  	if( $icone )
  	{
  		$bal_titre = new interf_bal_cont('div');
  		$bal_titre->add($icone);
  		$bal_titre->add($lien_titre);
		}
  	else
  		$bal_titre = &$lien_titre;
    return $this->add( new interf_panneau($bal_titre, null, 'h4', $id, $montre, $style) );
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
    $this->tbl = $this->add( new interf_tableau(false, false, false, false, false) );
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

/// lien ajax simple
class interf_lien extends interf_bal_smpl
{
	function __construct($texte, $cible, $id=false, $classe=false, $verif=false, $classe_click=false)
	{
		parent::__construct('a', $texte, $id, $classe);
		$this->set_attribut('href', $cible);
		if( $classe_click === true )
			$pre = 'this.className=\'icone icone-charger\';this.onclick=null;';
		else if( $classe_click )
			$pre = 'this.className=\''.$classe_click.'\';this.onclick=null;';
		else
			$pre = '';
		if( $verif )
			$this->set_attribut('onclick', $pre.'return verif_charger(this.href, \''.$verif.'\');');
		else
			$this->set_attribut('onclick', $pre.'return charger(this.href);');
	}
}

/// lien ajax avec contenu
class interf_lien_cont extends interf_bal_cont
{
	function __construct($cible, $id=false, $classe=false)
	{
		parent::__construct('a', $id, $classe);
		$this->set_attribut('href', $cible);
		$this->set_attribut('onclick', 'return charger(this.href);');
	}
}

/// image
class interf_img extends interf_bal_smpl
{
	function __construct($img, $alt=false, $id=false, $classe=false)
	{
		parent::__construct('img', false, $id, $classe);
		$this->set_attribut('src', $img);
		if( $alt )
			$this->set_attribut('alt', $alt);
	}
}

/*class interf_jauge extends interf_bal_cont
{
	function __construct($nom, $valeur, $maximum, $type=null, $id=false)
	{
		parent::__construct('div', $id, 'jauge_barre progress');
    $this->set_tooltip($nom.'&nbsp;: '.$valeur, 'bottom');
    $barre = $this->add( new interf_bal_cont('div', null, 'progress-bar'.($type?' progress-bar-'.$type:'')) );
    $barre->set_attribut('style', 'width:'.round($valeur/$maximum*100,0).'%');
    $this->add( new interf_bal_smpl('div', $valeur.' / '.$maximum) );
	}
}*/

class interf_jauge_bulle extends interf_bal_cont
{
	function __construct($nom, $valeur, $maximum, $grand, $type=null, $id=false, $classe=false)
	{
		parent::__construct('div', $id, $classe.' progress');
		if( $nom )
    	$this->set_tooltip($nom.'&nbsp;: '.$valeur.' / '.$maximum, 'bottom', '#contenu');
    $barre = $this->add( new interf_bal_cont('div', null, 'bulle jauge-'.$type) );
    $pourcent = $maximum ? round($valeur / $maximum * 100, 0).'%' : '0%';
    $barre->set_attribut('style', 'height:'.$pourcent);
    if( $grand )
    {
    	$texte = $grand === '%' ? $pourcent : $valeur.'/'.$maximum;
			$this->add( new interf_bal_smpl('div', $texte, $type, 'bulle_valeur') );
		}
	}
}

/// description en utiliant les tableaux
class interf_descr_tbl extends interf_tableau
{
	function __construct($id)
	{
		parent::__construct($id, 'table table-striped', false, false, null);
	}
	
	function nouv_elt($terme, $def=false)
	{
		$this->nouv_ligne();
		$this->nouv_cell($terme, false, false, true);
		return $this->nouv_cell($def);
	}
}

/// tableaux triable en utilisant l'extension jQuery dataTable
class interf_data_tbl extends interf_tableau
{
  function __construct($id=false, $classe='', $pages=true, $search=true, $scroll=false, $ordre=null)
  {
  	parent::__construct($id, 'table table-striped '.$classe);
    // Javascript
    /*$script = $this->add( new interf_bal_smpl('script', '') );
    $script->set_attribut('type', 'text/javascript');
    $script->set_attribut('src', './javascript/jquery/jquery.dataTables.min.js');*/
    //
    $options = array('"language": {"emptyTable": "Pas d\'élément dans cette catégorie.", "search": "Recherche :", "zeroRecords": "Pas de résultat", "paginate": {"first":"Début", "last":"Fin", "next":"suivant", "previous":"Précédent"}, "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées", "infoFiltered": "(filtré à partir de _MAX_ entrées)", "lengthMenu": "Afficher _MENU_ entrées par page", "infoEmpty": "Affichage de 0 entrées sur 0"}');
    if( !$pages )
    	$options[] = '"paging":false, "info":false';
    if( $scroll )
    	$options[] = '"scrollY":"'.$scroll.'px", "scrollCollapse": true';
    if( !$search )
    	$options[] = '"searching":false';
    if( $ordre !== null )
    {
    	if( $ordre == '-' )
    		$options[] = '"order":[[0, "desc"]]';
    	if( $ordre >= 0 )
    		$options[] = '"order":[['.$ordre.', "asc"]]';
			else
    		$options[] = '"order":[['.(-$ordre).', "desc"]]';
		}
    
    $options = $options ?  '{'.implode(',', $options).'}' : '';

    //interf_base::code_js('var '.$id.' = $("#'.$id.'").DataTable('.$options.');');
    interf_base::code_js('var '.$id.' = $("#'.$id.'").DataTable('.$options.');');
    //interf_base::code_js('var '.$id.' = $("#'.$id.'").DataTable({"language": {"emptyTable": "Pas d\'élément dans cette catégorie.", "search": "Recherche :", "zeroRecords": "Pas de résultat", "paginate": {"first":"Début", "last":"Fin", "next":"suivant", "previous":"Précédent"}, "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées", "infoFiltered": "(filtré à partir de _MAX_ entrées)", "lengthMenu": "Afficher _MENU_ entrées par page", "infoEmpty": "Affichage de 0 entrées sur 0"},"scrollY":"'.$scroll.'px", "scrollCollapse": true,"paging":false, "info":false,"order":[[0, "desc"]],"searching":false});');
  }
}

class interf_pagination extends interf_bal_cont
{
	function __construct($page_act, $nbr_pages, &$url, $id=false)
	{
		parent::__construct('ul', $id, 'pagination');
		$onclick = 'return charger(this.href);';
		if( $page_act == 1 )
			$this->add( new interf_bal_smpl('li', '<span>&laquo;</span>', false, 'disabled') );
		else
			$this->add( new interf_elt_menu('&laquo;', $url->get('page', $page_act-1), $onclick) );
		if( $nbr_pages < 9 )
		{
			for($i=1; $i<=$nbr_pages; $i++)
			{
				if( $page_act == $i )
					$this->add( new interf_bal_smpl('li', '<span>'.$i.'</span>', false, 'active') );
				else
					$this->add( new interf_elt_menu($i, $url->get('page', $i), $onclick) );
			}
		}
		else
		{
			if( $page_act > 2 )
			{
				$this->add( new interf_elt_menu(1, $url->get('page', 1), $onclick) );
				$li = $this->add( new interf_bal_cont('li') );
				$li->add( new interf_bal_smpl('span', '…') );
				$min = $page_act - 2;
			}
			else
				$min = 1;
			$max = $page_act < $nbr_pages - 2 ? $page_act + 2 : $nbr_pages;
			for($i=$min; $i<=$max; $i++)
			{
				if( $page_act == $i )
					$this->add( new interf_bal_smpl('li', '<span>'.$i.'</span>', false, 'active') );
				else
					$this->add( new interf_elt_menu($i, $url->get('page', $i), $onclick) );
			}
			if( $max != $nbr_pages )
			{
				$li = $this->add( new interf_bal_cont('li', '…') );
				$li->add( new interf_bal_smpl('span', '…') );
				$this->add( new interf_elt_menu($nbr_pages, $url->get('page', $nbr_pages), $onclick) );
			}
		}
		if( $page_act == $nbr_pages )
			$this->add( new interf_bal_smpl('li', '<span>&raquo;</span>', false, 'disabled') );
		else
			$this->add( new interf_elt_menu('&raquo;', $url->get('page', $page_act+1), $onclick) );
	}
}

class interf_editeur extends interf_bal_cont
{
	protected $btn_grp;
	protected $contenu;
	const ind_exp = 0x1;
	const liste = 0x2;
	const indent = 0x4;
	const police = 0x8;
	const align = 0x10;
	/*const sel = 0x10;
	const copier = 0x10;
	const annuler = 0x10;
	const lien = 0x10;
	const image = 0x10;
	const smiley = 0x10;*/
	const messagerie = 0;
	const mot_roi = 0;
	const propagande = 0;
	const descr_bataille = 0;
	function __construct($id_editeur, $url=false, $id=false, $classe=false, $options=0)
	{
		parent::__construct('div', $id, $classe);
		// Barre d'outils
		$barre = $this->add( new interf_bal_cont('div', false, 'btn-toolbar editeur-outils') );
		$barre->set_attribut('data-role', 'editor-toolbar');
		$barre->set_attribut('data-target', '#'.$id_editeur);
		$this->btn_grp = $barre->add( new interf_bal_cont('div', false, 'btn-group') );
		if( $options & self::police )
		{
			//$this->add_boutton('police', 'superscript', 'Exposant');
		}
		$this->add_boutton('gras', 'bold', 'Gras (ctrl+B)');
		$this->add_boutton('italique', 'italic', 'Italique (ctrl+I)');
		$this->add_boutton('souligner', 'underline', 'Souligner (ctrl+U)');
		$this->add_boutton('barrer', 'strikethrough', 'Barrer (ctrl+S)');
		if( $options & self::ind_exp )
		{
			$this->add_boutton('exposant', 'superscript', 'Exposant');
			$this->add_boutton('indice', 'subscript', 'Indice');
		}
		if( $options & self::liste )
		{
			$this->add_boutton('liste', 'insertunorderedlist', 'Liste');
			$this->add_boutton('liste-num', 'insertorderedlist', 'Indice');
		}
		if( $options & self::indent )
		{
			$this->add_boutton('desindenter', 'outdent', 'Désindenter (maj+tab)');
			$this->add_boutton('indenter', 'indent', 'Indenter (tab)');
		}
		if( $options & self::align )
		{
			$this->add_boutton('indice', 'justifyleft', 'Aligner à gauche (ctrl+L)');
			$this->add_boutton('indice', 'justifycentert', 'Aligner au centre (ctrl+E)');
			$this->add_boutton('indice', 'justifyrigh', 'Aligner à droite (ctrl+R)');
			$this->add_boutton('indice', 'justifyfull', 'Justifier (ctrl+J)');
		}
		/*if( $options & self::sel )
			$this->add_boutton('indice', 'selectAll', 'Sélectionner tout (ctrl+A)');
		if( $options & self::copier )
		{
			$this->add_boutton('couper', 'cut', 'Couper (ctrl+X)');
			$this->add_boutton('copier', 'copy', 'Copier (ctrl+C)');
			$this->add_boutton('coller', 'paste', 'Coller (ctrl+V)');
		}
		if( $options & self::annuler )
		{
			$this->add_boutton('annuler', 'undo', 'Annuler (ctrl+Z)');
			$this->add_boutton('refaire', 'repeat', 'Refaie (ctrl+Y)');
		}*/
		// envoi
		if( $url )
		{
			$btn_grp = $barre->add( new interf_bal_cont('div', false, 'btn-group editeur-droite') );
			$btn = $btn_grp->add( new interf_bal_smpl('button', '', false, 'btn btn-default icone icone-message') );
			$btn->set_attribut('onclick', 'envoie_texte(\''.$url.'\', \''.$id_editeur.'\');');
			$btn->set_tooltip('Envoyer');
		}
		// Zone de texte
		$this->contenu = $this->add( new interf_bal_cont('div', $id_editeur, 'editeur-texte') );
		self::code_js('$("#'.$id_editeur.'").wysiwyg();');
	}
	function add_boutton($icone, $action, $info)
	{
		$btn = $this->btn_grp->add( new interf_bal_smpl('button', '', false, 'btn btn-default icone icone-'.$icone) );
		$btn->set_attribut('data-edit', $action);
		$btn->set_attribut('type', 'button');
		$btn->set_tooltip($info);
	}
	function set_texte($texte)
	{
			$this->contenu->add( new interf_txt($texte) );
	}
}

class interf_dropdown_select extends interf_bal_cont
{
  protected $groupe = null;  ///< Groupe actuel
	protected $texte;
	protected $liste;
	protected $pile_groupes = array();
  /**
   * Constructeur
   */
  function __construct($input=false, $id=false, $classe='default')
  {
  	parent::__construct('div', $id, ($input?'input-group-btn':'btn-group'));
  	$btn = $this->add( new interf_bal_cont('button', false, 'btn btn-'.$classe.' dropdown-toggle') );
  	$btn->set_attribut('type', 'button');
  	$btn->set_attribut('data-toggle', 'dropdown');
  	$this->texte = $btn->add( new interf_txt('Choix') );
  	$btn->add( new interf_bal_smpl('span', false, false, 'caret') );
  	$this->liste = $this->add( new interf_bal_cont('ul', false, 'dropdown-menu') );
  	$this->liste->set_attribut('role', 'menu');
	}
  /**
   * Ajoute une option
   * @param  $texte    texte de l'option.
   * @param  $val      valeur de l'option.
   * @param  $select   indique si cette option est sélectionnée ou non.
   */
  function add_option($texte, $url=null, $select=false, $id=false, $classe=false)
  {
  	$elt = new interf_elt_menu($texte, $url, $url?'return charger(this.href);':false, $id, $classe);
  	if( $this->groupe )
  		$this->groupe->add( $elt );
  	else
  		$this->liste->add( $elt );
  	if( $select )
  		$this->texte->set_texte($texte);
	}
  function &nouv_groupe($label, $sous_groupe=false)
	{
		$grp = new interf_sous_menu($label, true, false, 'dropdown-submenu', 'dropdown-menu', 'aff_sousmenu(this)');
		//$grp->get_lien()->set_attribut('onmouseover', 'aff_sousmenu()');
		if( $sous_groupe && $this->groupe )
		{
			array_push($this->pile_groupes, $this->groupe);
    	$this->groupe = $this->groupe->add( $grp );
		}
    else
    {
    	$this->groupe = $this->liste->add( $grp );
    	$this->pile_groupes = array();
		}
    return $grp;
	}
	function pop_groupe()
	{
		$this->groupe = array_pop($this->pile_groupes);
	}
	function set_texte($texte)
	{
  	$this->texte->set_texte($texte);
	}
}
/**
 * Classe permettant un affichage avec des onglets.
 * Utilise la librairie boostrap.
 */
class interf_pills extends interf_bal_cont
{
  protected $liste;  ///< liste des onglets.
  protected $menus = array();
  protected $contenu;

  /**
   * Constructeur
   */
  function __construct($id=false, $classe=false, $id_cont=false, $class_cont=false)
  {
    interf_bal_cont::__construct('div', $id, $classe);
    $this->liste = $this->add( new interf_bal_cont('ul', false, 'nav nav-pills nav-stacked') );
    $this->contenu = $this->add( new interf_bal_cont('div', $id_cont, $class_cont) );
    //$this->add( new interf_bal_smpl('div', false, 'rp_fin') );
  }
  /**
   * Ajoute un onglet.
   * @param  $nom         nom à afficher sur l'onglet
   * @param  $adresse     adresse de la page web pour le contenu
   * @param  $selection   indique si l'onglet est sélectionné
   */
  function add_elt($nom, $adresse, $selection=false, $menu=false)
  {
    if( $selection )
      $li = new interf_elt_menu($nom, '#', false, false, 'active');
    else
      $li = new interf_elt_menu($nom, $adresse, 'return charger(this.href);');
    if( $menu )
    {
    	if( array_key_exists($menu, $this->menus) )
    		$parent = &$this->menus[$menu];
    	else
    	{
    		$li_menu = $this->liste->add( new interf_bal_cont('li', false, 'dropdown') );
    		$lien = $li_menu->add( new interf_bal_cont('a', '#', false, 'dropdown-toggle') );
    		$lien->set_attribut('data-toggle', 'dropdown');
    		$lien->add( new interf_txt($menu) );
    		$lien->add( new interf_bal_smpl('span', '', false, 'caret') );
    		$parent = $li_menu->add( new interf_bal_cont('ul', false, 'dropdown-menu') );
    		$this->menus[$menu] = &$parent;
			}
			$parent->add( $li );
		}
		else
    	$this->liste->add( $li );
    $lien = $li->get_lien();
    return $li;
  }

  function &get_contenu()
  {
    return $this->contenu;
  }
}

class interf_menu_div extends interf_bal_cont
{
  protected $nom;  ///< Nom du sous-menu.
  protected $classe_div;  ///< Classe de la balise ul
  /**
   * Constructeur
   * @param  $nom
   */
  function __construct($nom=false, $id=false, $classe=false, $classe_div=false)
  {
    parent::__construct('li', $id, 'dropdown '.$classe);
    $this->nom = $nom;
    $this->classe_div = $classe_div;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    $this->ouvre($this->creer_balise());
    $this->ligne('<a tabindex="-1" href="#" class="dropdown-toggle" data-toggle="dropdown">'.$this->nom.'<b class="caret"></b></a>');
    $this->ouvre('div class="dropdown-menu '.$this->classe_div.'"');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  function fin()
  {
    $this->ferme('div');
    $this->ferme('li');
  }
}
?>
