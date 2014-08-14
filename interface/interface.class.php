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
  protected $affiche_js = false;
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
  /// renvoie l'élément conteneur courrant.
  static function &get_courrent()
  {
    return self::$courrant;
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
    if( $inter !== null )
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
  /// Insert un élément entre celui-ci et ces fils
  function &insert(&$fils)
  {
    $fils->fils = $this->fils;
    $this->fils = array(&$fils);
    interf_base::$courrant = &$fils;
    return $fils;
  }
  /*
   * Renvoie un fils précis ou tous les fils
   * @param  $fils  indice du fils ou null pour tous les fils
   * @return    fils demandé ou tableau contenant tous les fils
   */
  function get_fils($fils=null)
  {
    if( $fils === null )
      return $this->fils;
    return $this->fils[$fils];
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut() {}
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin() {}
  /// Renvoie le nombre de tabulations suppléméntaires
  protected function get_tab() { return 0; }
  /// Ajoute une balise avec le code javascript s'il y en a
  function add_js()
  {
  	if( self::$code_js )
  		$this->add( new interf_js() );
	}
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
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->affiche_js();
  }
}

/**
 * Classe de base pour l'élément principal de l'interface, avec mise en tampon du texte.
 * Tout le texte qui aurait dû être affiché par echo et print entre la création
 * de l'objet et sa destruction est affiché dans une balise div à la fin du contenu.
 * Ne fonctionne que si l'affichage est déclenché automatiquement à la destruction de l'objet.
 */
class interf_princ_ob extends interf_princ
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
  /*
   * Renvoie un attribut précis ou tous les attributs
   * @param  $nom  indice de l'attribut ou null pour tous les attributs
   * @return    attribut demandé ou tableau contenant tous les attributs
   */
  function get_attribut($nom=null)
  {
    if( $nom === null )
      return $this->attributs;
    return $this->attributs[$nom];
  }
  /**
   * Ajoute une infobulle compatible avec la librairie Bootstrap
   *
   * @param  $texte   Texte du tooltip.
   * @param  $pos     Position du tooltip
   */
  function set_tooltip($texte, $pos=null)
  {
    $this->set_attribut('title', $texte);
    $this->set_attribut('data-toggle', 'tooltip');
    if( $pos )
      $this->set_attribut('data-placement', $pos);
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
  /*
   * Renvoie un attribut précis ou tous les attributs
   * @param  $nom  indice de l'attribut ou null pour tous les attributs
   * @return    attribut demandé ou tableau contenant tous les attributs
   */
  function get_attribut($nom=null)
  {
    if( $nom === null )
      return $this->attributs;
    return $this->attributs[$nom];
  }
  /// Change le type de la balise
  function set_balise($balise)
  {
  	 $this->balise = $balise;
	}
  /**
   * Ajoute une infobulle compatible avec la librairie Bootstrap
   *
   * @param  $texte   Texte du tooltip.
   * @param  $pos     Position du tooltip
   */
  function set_tooltip($texte, $pos=null, $parent=null)
  {
    $this->set_attribut('title', $texte);
    $this->set_attribut('data-toggle', 'tooltip');
    if( $pos )
      $this->set_attribut('data-placement', $pos);
  	if( $parent )
      $this->set_attribut('data-container', $parent);
  }
  /// Ajoute les messages (alerte bootstrap) 
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
  private $encodage;  /// Encodage de la page
  private $xhtml;  /// version du langage XHTML utilisé ou false si ce n'est pas du XHTML
  
  /**
   * Constructeur
   * @param  $titre   titre de la page.
   */
  function __construct($titre, $encodage='utf-8', $xhtml='1.1')
  {
    interf_princ_ob::__construct();
    $this->titre = $titre;
    $this->encodage = $encodage;
    $this->xhtml = $xhtml;
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
  /**
   * Ajout d'une balise meta
   */
  function meta($nom, $contenu)
  {
    $this->entete[] ='<meta name="'.$nom.'"  content="'.$contenu.'"/>';
  }
  /**
   * Ajout d'une balise meta
   */
  function meta_http($equiv, $contenu)
  {
    $this->entete[] ='<meta name="'.$nom.'"  content="'.$contenu.'"/>';
  }
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    if( $this->xhtml )
    {
      $this->ligne('<?xml version="'.$this->xhtml.'"" encoding="'.$this->encodage.'"?'.'>');
      $this->ligne('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">');
    }
    $this->ouvre('html');
    $this->ouvre('head');
    $this->balise('title', $this->titre);
    $this->ligne('<meta charset="'.$this->encodage.'"/>');
    foreach($this->entete as $lgn)
    {
      $this->ligne($lgn);
    }
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
  protected $lien;   ///< balise de lien.
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
    interf_bal_cont::__construct('li', $id, $classe);
    $this->lien = self::add( new interf_bal_cont('a') );
    if($nom)
    {
    	if( !is_object($nom) )
    		$nom = new interf_txt($nom);
			$this->lien->add( $nom );
		}
    $this->lien->set_attribut('href', $lien);
    if( $onclick )
      $this->lien->set_attribut('onclick', $onclick);
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
  function __construct($action = null, $id=null, $method = 'get', $classe=null)
  {
    interf_bal_cont::__construct('form', $id, $classe);
    if( $action )
      $this->set_attribut('action', $action);
    if( $action )
      $this->set_attribut('method', $method);
  }

  /**
   * Affiche un champ en utilisany la librairie bootstrap pour le style
   *
   * @param  $type          type de champ
   * @param  $name          nom du champ
   * @param  $placeholder   texte situé dans le champ s'il est vide (ou null s'il n'y en a pas)
   * @param  $value         valeur de départ (ou null s'il n'y en a pas)
   * @param  $avant         texte ou boutton situé avant le champ (ou null s'il n'y en a pas)
   * @param  $apres         texte ou boutton situé après le champ (ou null s'il n'y en a pas)
   *
   * @return    Élément "input"
   */
  function add_champ_bs($type, $name, $placeholder=null, $value=null, $avant=null, $apres=null, $id=false)
  {
    $div = $this->add( new interf_bal_cont('div', null, 'input-group') );
    if( $avant )
    {
      if( !is_object($avant) )
        $avant = new interf_bal_smpl('span', $avant, null, 'input-group-addon');
      $div->add( $avant );
    }
    $chp = $div->add( new interf_bal_smpl('input', null, $id, 'form-control') );
    $chp->set_attribut('type', $type);
    $chp->set_attribut('name', $name);
    if( $value !== null && $value !== false )
      $chp->set_attribut('value', $value);
    if( $placeholder )
      $chp->set_attribut('placeholder', $placeholder);
    if( $apres )
    {
      if( !is_object($apres) )
        $apres = new interf_bal_smpl('span', $apres, null, 'input-group-addon');
      $div->add( $apres );
    }
    return $chp;
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
 * Classe gérant un chamo de formulaire (de type input)
 * Peut aussi afficher un texte avant le  champ.
 */
class interf_chp_form extends interf_bal_smpl
{
  protected $label;  ///< Texte à afficher avant le champ.
  protected $label_avant;  ///< True si le texte doit être avant, false s'il doit être après.
  protected $classe_label;  ///< Classe du texte à afficher avant le champ.
  /**
   * Constructeur
   * @param  $type    type de champ.
   * @param  $name    nom du champ.
   * @param  $label   texte à afficher avant le champ.
   * @param  $value   valeur par défaut.
   */
  function __construct($type, $name, $label=false, $value=false, $id=null, $classe=null, $classe_label=false)
  {
    interf_bal_smpl::__construct('input', '', $id, $classe);
    //$this->balise = 'input';
    $this->set_attribut('type', $type);
    $this->label_avant = $type != 'checkbox' && $type != 'radio';
    $this->classe_label = $classe_label;
    if($name)
    	$this->set_attribut('name', $name);
    if( $value !== false )
      $this->set_attribut('value', $value);
    $this->label = $label;
  }
  /// Affiche le contenu de l'élément.
  function contenu()
  {
  	$attr=null;
  	if( $this->classe_label )
    if( $this->label && $this->label_avant )
      $this->balise('label', $this->label, $attr);
    interf_bal_smpl::contenu();
    if( $this->label && !$this->label_avant )
      $this->balise('label', $this->label, $attr);
  }
}

/**
 * Champ de formulaire permettant de faire un choix entre plusieurs élément (balise select)
 */
class interf_select_form extends interf_bal_cont
{
  protected $groupe = null;  ///< Groupe actuel
  protected $label;  ///< Texte à afficher avant le champ.
  protected $classe_label;  ///< Classe du texte à afficher avant le champ.
  /**
   * Constructeur
   * @param  $name    nom du champ.
   * @param  $label   texte à afficher avant le champ.
   */
  function __construct($name, $label=false, $id=null, $classe=null, $classe_label=false)
  {
    interf_bal_cont::__construct('select', $id, $classe);
    $this->set_attribut('name', $name);
    $this->label = $label;
    $this->classe_label = $classe_label;
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
  	$attr=null;
  	if( $this->classe_label )
  		$attr = array('class'=>$this->classe_label);
    if( $this->label )
      $this->balise('label', $this->label, $attr);
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
  protected $est_entete;   ///< Indique si la ligne actuelle est l'en-tête.
  protected $entete = null;   ///< En-tête du tableau.
  protected $corps = null;   ///< Corps du tableau.
  protected $pied = null;   ///< Pied du tableau.
  protected $cour = null;   ///< Parti courante.
  
  const entete = 1;
  const corps = 2;
  const pied = 3;
  
  function __construct($id=false, $classe=false, $id_lgn1=false, $classe_lgn1=false, $entete=true)
  {
  	parent::__construct('table', $id, $classe);
  	if( $entete !== null )
    	$this->nouv_ligne($id_lgn1, $classe_lgn1, $entete ? self::entete : self::corps);
    $this->est_entete = $entete;
  }
  /// Ajoute un élément fils
  function &add(&$fils)
  {
  	if( $this->cour )
    	return $this->cour->add($fils);
    else
    	return parent::add($fils);
  }
  /**
   * Ajoute une nouvelle ligne.
   * @param  $id        id de la ligne.
   * @param  $class     classe de la ligne.
   * @return    objet gérant la ligne (de type interf_bal_cont).
   */
  function &nouv_ligne($id=false, $classe=false, $type=self::corps)
  {
  	switch($type)
  	{
  	case self::entete:
  		if( !$this->entete )
  			$this->entete = $this->add( new interf_bal_cont('thead') );
  		$this->cour = $this->entete;
  		$this->est_entete = true;
  		break;
  	case self::corps:
  		if( !$this->corps )
  			$this->corps = $this->add( new interf_bal_cont('tbody') );
  		$this->cour = $this->corps;
    	$this->est_entete = false;
  		break;
  	case self::pied:
  		if( !$this->pied )
  			$this->pied = $this->add( new interf_bal_cont('tfoot') );
  		$this->cour = $this->pied;
    	$this->est_entete = false;
  		break;
		}
    $this->lgn_act = $this->add( new interf_bal_cont('tr', $id, $classe) );
    return $this->lgn_act;
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
    $bal = ($this->est_entete || $entete) ? 'th' : 'td';
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
    $this->est_entete = $entete;
  }
}

/**
 * Liste de description
 */
class interf_descr extends interf_bal_cont
{
	function __construct($id=false, $classe='dl-horizontal')
	{
		parent::__construct('dl', $id, $classe);
	}
	function nouv_elt($terme, $def=false)
	{
		if( is_object($terme) )
		{
			$dt = $this->add( new interf_bal_cont('dt') );
			$dt->add( $terme );
		}
		else
			$this->add( new interf_bal_smpl('dt', $terme) );
		if( $def )
		{
			if( is_object($def) )
			{
				$dd = $this->add( new interf_bal_cont('dd') );
				$dd->add( $def );
			}
			else
				$dd = $this->add( new interf_bal_smpl('dd', $def) );
		}
		else
			$dd = $this->add( new interf_bal_cont('dd') );
		return $dd;
	}
} 

class interf_js extends interf_smpl
{
  /// affiche le contenu de l'élément.
  function contenu()
  {
  	$this->affiche_js();
	}
}

class url
{
	private $base;
	private $vars=array();
	function __construct($base='')
	{
		$this->base = $base;
	}
	function add($nom, $valeur)
	{
		$this->vars[$nom] = $valeur;
	}
	function get($nom=null, $valeur=null)
	{
		if( $nom === null )
			$vars = array();
		else
		{
			if( is_array($nom) )
			{
				$vars = array();
				foreach($nom as $nm=>$val)
				{
					$vars[] = $nm.'='.$val;
				}
			}
			else
				$vars = array($nom.'='.$valeur);
		}
		foreach($this->vars as $nm=>$val)
		{
			$vars[] = $nm.'='.$val;
		}
		return $this->base.'?'.implode('&', $vars);
	}
}
?>
