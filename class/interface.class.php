<?php
/// @addtogroup Interface
/**
 * @file interface.class.php
 * Classes génériques pour la gestion de l'interface.
 * Ces classes permettent la construction de structures HTML classiques, avec éventuellement jQuery.
 * Elles servent aussi de classe de base pour les classes plus spécialisées.
 */

/**
 * Classe de base pour la gestion de l'interface.
 * Cette classe est la classe de base de toutes les autres classes gérant l'interface.
 */
abstract class interf_base
{
  protected $tab = 0;  ///< Nombre de tabulation au début des lignes.
  protected $affiche = false;  ///< Indique si le contenu a été affiché ou non.
  protected static $code_js = '';  ///< Code Javascript qui sera affiché au début.
  protected static $courrant = null;  ///< Élément conteneur courrant.
  const cont = false;  ///< Indique si cet élément peut contenir d'autres éléments.

  /**
   * Gère l'affichage de l'élément.
   * @param $tabs   nombre de tabulation au début des lignes.
   */
  abstract function affiche($tab=0);
  /// Ajoute du code javascript à afficher au début.
  static function code_js($code)
  {
  
    self::$code_js .= $code."\n";
  }
  /// Définit l'élément conteneur courrant.
  static function set_courrent(&$obj)
  {
    self::$courrant = $obj;
  }
  /// Ajoute un élément fils à l'élément conteneur courrant.
  static function add_courr(&$fils)
  {
    self::$courrant->add($fils);
  }
  /// affiche le code javascript
  function affiche_js()
  {
    if( self::$code_js )
    {
      $this->ouvre('script type="text/javascript"');
      echo self::$code_js;
      $this->ferme('script');
      self::$code_js = '';
    }
  }
  /**
   * affiche une ligne.
   * affiche le texte donné avec les tabulations au début et un retour à la ligne à la fin.
   * @param  $ligne     texte à afficher.
   * @param  $tabInc    nombre de tabulation à ajouter au nombre actuel.
   */
  protected function ligne($ligne, $tabInc=0)
  {
    if( $tabInc < 0 )
      $this->tab += $tabInc;
    for($i=0; $i<$this->tab; $i++)
      echo "\t";
    echo $ligne."\n";
    if( $tabInc > 0 )
      $this->tab += $tabInc;
  }
  /**
   * ouvre une balise HTML.
   * ouvre la balise (précédé par les tabulations), passe à la ligne et augmente
   * le nombre de tabulation de 1.
   * @param  $balise    balise à ouvrir, contient éventuellement es attributs.
   */
  protected function ouvre($balise)
  {
    $this->ligne('<'.$balise.'>', 1);
  }
  /**
   * ferme une balise HTML.
   * ferme la balise (précédé par les tabulations), passe à la ligne et diminue
   * le nombre de tabulation de 1.
   * @param  $balise    balise à fermer.
   */
  protected function ferme($balise)
  {
    $this->ligne('</'.$balise.'>', -1);
  }
  /**
   * affiche une balise est son contenu sur une seule ligne.
   * affiche la balise (précédé par les tabulations) avec éventuellement les
   * attributs fournis et le contenu, puis passe à la ligne.
   * @param  $balise      balise à afficher (sans les attributs).
   * @param  $inter       contenu sous forme textuel ou null s'il n'y en a pas.
   * @param  $attributs   attributs sous forme de tableau associatif (attribut=>valeur).
   */
  protected function balise($balise, $inter=null, $attributs=null)
  {
    $this->ligne($this->texte_balise($balise, $inter, $attributs));
  }
  /**
   * Génère le texte pour l'affichage d'une balise
   * @param  $balise      balise à afficher (sans les attributs).
   * @param  $inter       contenu sous forme textuel ou null s'il n'y en a pas.
   * @param  $attributs   attributs sous forme de tableau associatif (attribut=>valeur).
   * @return  texte pour l'affichage d'une balise.
   */
  protected function texte_balise($balise, $inter=null, $attributs=null)
  {
    if( $inter )
      return '<'.$balise.$this->texte_attributs($attributs).' >'.$inter.'</'.$balise.'>';
    else
      return'<'.$balise.$this->texte_attributs($attributs).' />';
  }
  /**
   * Génère le texte pour la définition des attributs d'une balise.
   * @param  $attributs   attributs sous forme de tableau associatif (attribut=>valeur).
   * @return  texte pour la définition des attributs .
   */
  protected function texte_attributs($attributs)
  {
    $attr = '';
    if( $attributs )
    {
      foreach($attributs as $nom=>$val)
      {
        $attr .= ' '.$nom.'="'.$val.'"';
      }
    }
    return $attr;
  }
}

/**
 * Classe de base pour les éléments ne contenant pas d'autres éléments
 */
abstract class interf_smpl extends interf_base
{
  /**
   * Gère l'affichage de l'élément.
   * @param $tabs   nombre de tabulation au début des lignes.
   */
  function affiche($tab=0)
  {
    if( !$this->affiche )
    {
      $this->tab = $tab;
      $this->contenu();
    }
  }
  /// affiche le contenu de l'élément.
  abstract function contenu();
}

/**
 * Classe de base pour les éléments contenant d'autres éléments
 */
abstract class interf_cont extends interf_base
{
  protected $fils = array();  ///< Tableau contenants les éléments fils.
  const cont = true;  ///< Indique si cet élément peut contenir d'autres éléments.

  /**
   * Gère l'affichage de l'élément.
   * @param $tabs   nombre de tabulation au début des lignes.
   */
  function affiche($tab=0)
  {
    if( !$this->affiche )
    {
      $this->tab = $tab;
      $this->debut();
      foreach($this->fils as $fils)
      {
        $fils->affiche( $this->tab + $this->get_tab() );
      }
      $this->fin();
      $this->affiche = true;
    }
  }
  /// Ajoute un élément fils
  function &add(&$fils)
  {
    $this->fils[] = &$fils;
    if( $fils::cont )
      interf_base::$courrant = &$fils;
    return $fils;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut() {}
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin() {}
  /// Renvoie le nombre de tabulations suppléméntaires
  protected function get_tab() { return 0; }
}

/**
 * Classe de base pour l'élément principal de l'interface.
 * Si l'affichage n'a pas été déclenché manuellement, il est effectué automatiquement
 * à la destruction de l'objet.
 */
abstract class interf_princ extends interf_cont
{
  /// Destructeur
  function __destruct()
  {
    $this->affiche();
  }
}

/**
 * Classe de base pour l'élément principal de l'interface, avec mise en tampon du texte.
 * Tout le texte qui aurait dû être affiché par echo et print entre la création
 * de l'objet et sa destruction est affiché dans une balise div à la fin du contenu.
 * Ne fonctionne que si l'affichage est déclenché automatiquement à la destruction de l'objet.
 */
abstract class interf_princ_ob extends interf_princ
{
  const div_ob = null;  ///< id de la balise div contenant le texte mis en tampon.
  const classe_ob = null;  ///< classe de la balise div contenant le texte mis en tampon.
  /// Constructeur
  function __construct()
  {
    ob_start();
  }
  /// Destructeur
  function __destruct()
  {
    $this->add( new interf_bal_smpl('div', ob_get_contents(), static::div_ob, static::classe_ob) );
    ob_end_clean();
    interf_princ::__destruct();
  }
}

/**
 * Classe de base pour les éléments principaux contenant d'autres éléments
 */
class interf_princ_cont extends interf_princ
{
  /// Destructeur
  function __destruct()
  {
    $this->affiche();
  }
}

/**
 * Classe gérant l'affichage de texte.
 */
class interf_txt extends interf_smpl
{
  protected $texte;  ///< Texte à afficher.
  /**
   * Constructeur
   * @param  $texte   texte à afficher.
   */
  function __construct($texte)
  {
    $this->texte = $texte;
  }
  /// Affiche le contenu de l'élément.
  function contenu()
  {
    echo $this->texte;
  }
}

/**
 * Classe gérant l'affichage d'une balise HTML ne contenant pas d'autre balise.
 * Va à la ligne à la fin de la balise (et place les tabulations avant).
 */
class interf_bal_smpl extends interf_smpl
{
  protected $balise;  ///< balise à afficher.
  protected $inter;   ///< contenu de la balise sous forme textuelle.
  protected $attributs=array();  ///< Attributs sous forme de tableau associatif (attribut=>valeur).

  /**
   * Constructeur
   * @param  $balise    balise à afficher.
   * @param  $inter     contenu de la balise sous forme textuelle.
   * @param  $id        id de la balise.
   * @param  $class     classe de la balise.
   */
  function __construct($balise, $inter=null, $id=false, $class=false)
  {
    $this->balise = $balise;
    $this->inter = $inter;
    if( is_array($id) )
    {
      $this->attributs = $id;
    }
    else
    {
      if($id)
        $this->attributs['id'] = $id;
      if($class)
        $this->attributs['class'] = $class;
    }
  }
  /// Affiche le contenu de l'élément.
  function contenu()
  {
    $this->balise($this->balise, $this->inter, $this->attributs);
  }
  /**
   * Définit la valeur d'un attribut.
   * @param  $nom   nom de l'attribut.
   * @param  $val   valeur de l'attribut.
   */
  function set_attribut($nom, $val)
  {
    $this->attributs[$nom] = $val;
  }
}

/**
 * Classe gérant l'affichage d'une balise HTML ne contenant pas d'autre balise, sans retour à la ligne.
 * Ne va à la ligne à la fin de la balise et ne place les tabulations avant.
 */
class interf_bal_enlgn extends interf_bal_smpl
{
  /// Affiche le contenu de l'élément.
  function contenu()
  {
    echo $this->texte_balise($this->balise, $this->inter, $this->attributs);
  }
}

/**
 * Classe gérant l'affichage d'une balise HTML contenant d'autres balises.
 * Va à la ligne à la fin de la balise (et place les tabulations avant).
 */
class interf_bal_cont extends interf_cont
{
  protected $balise;  ///< balise à afficher.
  protected $attributs=array();  ///< Attributs sous forme de tableau associatif (attribut=>valeur).

  /**
   * Constructeur
   * @param  $balise    balise à afficher.
   * @param  $id        id de la balise.
   * @param  $class     classe de la balise.
   */
  function __construct($balise, $id=false, $class=false)
  {
    $this->balise = $balise;
    if( is_array($id) )
    {
      $this->attributs = $id;
    }
    else
    {
      if($id)
        $this->attributs['id'] = $id;
      if($class)
        $this->attributs['class'] = $class;
    }
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ouvre($this->balise.$this->texte_attributs($this->attributs));
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme($this->balise);
  }
  // Crée la balise pour la méthode ouvrir en ajoutant les attributs au nom de la balise.
  protected function creer_balise()
  {
    return $this->balise.$this->texte_attributs($this->attributs);
  }
  /**
   * Définit la valeur d'un attribut.
   * @param  $nom   nom de l'attribut.
   * @param  $val   valeur de l'attribut.
   */
  function set_attribut($nom, $val)
  {
    $this->attributs[$nom] = $val;
  }
}

/**
 * Élément principal pour l'affichage d'une page complète.
 * Utilise la balise html comme balise racine et gère l'affichage de l'en-tête.
 * Les éléments fils sont ajoutés dans le corps du doculent (balise body).
 */
class interf_html extends interf_princ_ob
{
  private $titre;   ///< Titre de la page.
  private $entete = array();  ///< contenu de l'en-tête sous forme de tableau de lignes.
  
  /**
   * Constructeur
   * @param  $titre   titre de la page.
   */
  function __construct($titre)
  {
    interf_princ_ob::__construct();
    $this->titre = $titre;
  }
  /**
   * Ajoute un lien vers un fichier CSS
   * @param  $fichier   url du fichier CSS.
   * @param  $media     type de média concerné.
   */
  function css($fichier, $media=false)
  {
    $balise = '<link href="'.$fichier.'" rel="stylesheet"';
    if( $media )
      $balise .= ' media="'.$media.'"';
    $this->entete[] = $balise.' />';
  }
  /**
   * Ajoute un fichier javascript à inclure
   * @param  $fichier   url du fichier javascript.
   */
  function javascript($fichier)
  {
    $this->entete[] ='<script src="'.$fichier.'"  type="text/javascript"></script>';
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ouvre('html');
    $this->ouvre('head');
    $this->balise('title', $this->titre);
    foreach($this->entete as $lgn)
    {
      $this->ligne($lgn);
    }
    $this->affiche_js();
    $this->ferme('head');
    $this->ouvre('body');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->affiche_js();
    $this->ferme('body');
    $this->ferme('html');
  }
}

/**
 * Élément d'un menu
 */
class interf_elt_menu extends interf_bal_cont
{
  private $lien;   ///< balise de lien.
  /**
   * Constructeur
   * @param  $nom
   * @param  $lien
   * @param  $onclick   contenu de l'attribut onclick.
   * @param  $id
   * @param  $classe
   */
  function __construct($nom, $lien, $onclick=false, $id=false, $classe=false)
  {
    /*$inter = '<a href="'.$lien;
    if( $onclick )
      $inter .= '" onclick="'.$onclick;
    $inter .= '">'.$nom.'</a>';*/
    interf_bal_cont::__construct('li', $id, $classe);
    $this->lien = $this->add( new interf_bal_smpl('a', $nom) );
    $this->set_attribut('href', $lien);
    if( $onclick )
      $this->set_attribut('onclick', $onclick);
  }

  function &get_lien()
  {
    return $this->lien;
  }
}

/**
 * Classe gérant un sous-menu, i.e. un men u imbriqué dans un autre
 */
class interf_sous_menu extends interf_bal_cont
{
  private $nom;  ///< Nom du sous-menu.
  /**
   * Constructeur
   * @param  $nom
   */
  function __construct($nom=false)
  {
    $this->balise = 'li';
    $this->nom = $nom;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    $this->ouvre($this->creer_balise());
    if( $this->nom )
      $this->ligne('<span>'.$this->nom.'</span>');
    $this->ouvre('ul');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  function fin()
  {
    $this->ferme('ul');
    $this->ferme('li');
  }
}

/**
 * Classe gérant un menu.
 */
class interf_menu extends interf_bal_cont
{
  private $titre;  ///< Titre du menu.
  private $classe_ul;  ///< Classe de la balise ul
  const bal_titre = 'h3';  ///< balise utilisé pour le titre.
  /**
   * Constructeur
   * @param  $titre     titre du menu ou false s'il n'y en a pas.
   * @param  $id        id de la balise.
   * @param  $class     classe de la balise.
   */
  function __construct($titre = false, $id = 'menu',$classe = 'menu', $classe_ul=false)
  {
    interf_bal_cont::__construct('div', $id ,$classe);
    $this->titre = $titre;
    $this->classe_ul = $classe_ul;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    $this->ouvre($this->creer_balise());
    if( $this->titre )
      $this->balise(self::bal_titre, $this->titre);
    if($this->classe_ul)
      $this->ouvre('ul class="'.$this->classe_ul.'"');
    else
      $this->ouvre('ul');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  function fin()
  {
    $this->ferme('ul');
    $this->ferme($this->balise);
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
   * Ajoute un bouton à la botie de dialogue
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
 * Classe gérant un formulaire
 */
class interf_form extends interf_bal_cont
{
  private $action;  ///< Action à effectuer lors de la validation du formulaire.
  private $method;  ///< Méthode à utiliser pour l'envoie des données.
  
  /**
   * Constructeur
   * @param  $action    action à effectuer lors de la validation du formulaire.
   * @param  $method    méthode à utiliser pour l'envoie des données.
   */
  function __construct($action = false, $method = false)
  {
    $this->balise = 'form';
    if( $action )
      $this->set_attribut('action', $action);
    if( $action )
      $this->set_attribut('method', $method);
  }
}

/**
 * Classe gérant un ensemble de champ d'un formulaire (balise fieldset).
 */
class interf_ens_chps extends interf_bal_cont
{
  private $legend;   ///< Légende du champ.
  
  /**
   * Constructeur.
   * @param  $legend    Légende du champ.
   */
  function __construct($legend=null)
  {
    $this->balise = 'fieldset';
    $this->legend = $legend;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    interf_bal_cont::debut();
    if( $this->legend )
      $this->balise('legend', $this->legend);
  }
}

/**
 * Calsse gérant un chamo de formulaire (de type input)
 * Peut aussi afficher un texte avant le  champ.
 */
class interf_chp_form extends interf_bal_smpl
{
  protected $label;  ///< Texte à afficher avant le champ.
  /**
   * Constructeur
   * @param  $type    type de champ.
   * @param  $name    nom du champ.
   * @param  $label   texte à afficher avant le champ.
   * @param  $value   valeur par défaut.
   */
  function __construct($type, $name, $label=false, $value=false)
  {
    $this->balise = 'input';
    $this->set_attribut('type', $type);
    $this->set_attribut('name', $name);
    if( $value )
      $this->set_attribut('value', $value);
    $this->label = $label;
  }
  /// Affiche le contenu de l'élément.
  function contenu()
  {
    if( $this->label )
      $this->balise('label', $this->label);
    interf_bal_smpl::contenu();
  }
}

/**
 * Champ de formulaire permettant de faire un choix entre plusieurs élément (balise select)
 */
class interf_select_form extends interf_bal_cont
{
  protected $groupe = null;  ///< Groupe actuel
  protected $label;  ///< Texte à afficher avant le champ.
  /**
   * Constructeur
   * @param  $name    nom du champ.
   * @param  $label   texte à afficher avant le champ.
   */
  function __construct($name, $label=false)
  {
    $this->balise = 'select';
    $this->set_attribut('name', $name);
    $this->label = $label;
  }
  /**
   * Ajoute un groupe
   * @param  $label   titre du groupe.
   */
  function &nouv_groupe($label)
  {
    $grp = new interf_bal_cont('optgroup');
    $grp->set_attribut('label', $label);
    $this->Add($grp);
    $this->groupe = &$grp;
    return $grp;
  }
  /**
   * Ajoute une option
   * @param  $texte    texte de l'option.
   * @param  $val      valeur de l'option.
   * @param  $select   indique si cette option est sélectionnée ou non.
   */
  function add_option($texte, $val=null, $select=false)
  {
    $opt = new interf_bal_smpl('option', $texte);
    if( $val )
      $opt->set_attribut('value', $val);
    if( $select )
      $opt->set_attribut('selected', 'selected');
    if( $this->groupe )
      $this->groupe->add($opt);
    else
      $this->add($opt);
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    if( $this->label )
      $this->balise('label', $this->label);
    interf_bal_cont::debut();
  }
}

/**
 * Classe gérant l'affichage d'un tableau.
 * Comme pour le code HTML, la création se fait ligne par ligne.
 * Par défaut, la première ligne est définit comme étant l'en-tête et les suivantes
 * des lignes "normales". La première ligne est créée automatiquement lor de la création
 * du tableau.
 */
class interf_tableau extends interf_bal_cont
{
  protected $lgn_act = null;  ///< Ligne actuelle du tableau.
  protected $entete;   ///< Indique si la ligne actuelle est l'en-tête.
  function __construct()
  {
    $this->balise = 'table';
    $this->nouv_ligne();
    $this->entete = true;
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  function debut()
  {
    interf_bal_cont::debut();
    $this->Ouvre('tbody');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  function fin()
  {
    $this->ferme('tbody');
    interf_bal_cont::fin();
  }
  /**
   * Ajoute une nouvelle ligne.
   * @param  $id        id de la ligne.
   * @param  $class     classe de la ligne.
   * @return    objet gérant la ligne (de type interf_bal_cont).
   */
  function &nouv_ligne($id=null, $classe=null)
  {
    $lgn = new interf_bal_cont('tr', $id, $classe);
    $this->add($lgn);
    $this->lgn_act = &$lgn;
    $this->entete = false;
    return $lgn;
  }
  /**
   * Ajoute une nouvelle cellule à la ligne en cours.
   * @param  $cont      contenu de la cellule, sous forme d'objet d'interface ou de texte.
   * @param  $id        id de la cellule.
   * @param  $class     classe de la cellule.
   * @return    objet gérant la cellule (de type interf_bal_cont).
   */
  function &nouv_cell($cont=null, $id=null, $classe=null, $entete=false)
  {
    $bal = ($this->entete || $entete) ? 'th' : 'td';
    if( $cont === null || is_object($cont) )
    {
      $cell = new interf_bal_cont($bal, $id, $classe);
      if( $cont )
        $cell->add( $cont );
    }
    else
    {
      $cell = new interf_bal_smpl($bal, $cont, $id, $classe);
    }
    $this->lgn_act->add($cell);
    return $cell;
  }
  /// Définit si la ligne actuelle est l'en-tête ou non.
  function set_entete($entete)
  {
    $this->entete = $entete;
  }
}
?>
