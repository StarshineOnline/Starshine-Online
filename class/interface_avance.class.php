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

/**
 * Affiche un message d'alerte
 */
class interf_alerte extends interf_bal_cont
{
  function __construct($type=null, $ferme=true, $id=null)
  {
    $classe = 'alert'.($type ? ' '.$type : '');
    interf_bal_cont::__construct('div', $id, $classe);
    if( $ferme )
      interf_base::code_js( '$("'.($id?'#'.$id:'.alert').'").alert();' );
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
  }
}
?>
