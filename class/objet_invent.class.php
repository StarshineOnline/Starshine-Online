<?php
/**
 * @file objet_invent.class.php
 * Contient la définition de la classe objet_invent qui représente un objet
 * qu'un personnage peut avoir dans son inventaire.
 */

/**
	Classe abstraite représentant un objet qu'un personnage peut avoir dans son inventaire
*/
abstract class objet_invent extends table
{
	protected $nom;  ///< Nom de l'objet.
	protected $type;  ///< Type de l'objet (epee, hache, dos, potion_hp,… ).
	protected $prix;  ///< Prix de l'objet en magasin.
	protected $identifie = true;  ///< Indique si l'objet a été identifié
	protected $texte;  ///< Forme textuelle
	protected $encombrement;  ///< Encombrement de l'objet (place prise dans l'inventaire)
	
	// Renvoie le nom de l'objet
	function get_nom()
	{
		return $this->nom;
	}
	
	/// Modifie le nom de l'objet
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}
	
	// Renvoie le type de l'objet
	function get_type()
	{
		return $this->type;
	}
	
	/// Modifie le type de l'objet
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}
	
	// Renvoie le prix de l'objet en magasin
	function get_prix()
	{
		return $this->prix;
	}
	
	/// Modifie le prix de l'objet en magasin
	function set_prix($prix)
	{
		$this->prix = $prix;
		$this->champs_modif[] = 'prix';
	}

  /// Renvoie le nombre d'exemplaires disponibles.
	function get_nombre()
	{
		return 1;
	}
	/// Modifie le nombre d'exemplaires disponibles.
	function set_nombre($stack)
	{
    // Pas de stack par défaut
	}

  /// Renvoie le nombre d'exemplaires qu'on peut mettre dans un emplacement de l'inventaire.
	function get_stack()
	{
		return 1;
	}

  /// Renvoie l'enchantement par gemme
  function get_enchantement()
  {
    return null;
  }
	/// Modifie l'enchantement par gemme
	function set_enchantement($enchantement)
	{
    // Pas d'enchantement par défaut
	}

  /// Renvoie la modification par la forge
  function get_modification()
  {
    return null;
  }
	/// Modifie la modification par la forge
	function set_modification($modif)
	{
    // Pas d'enchantement par défaut
	}

  /// Renvoie le slot
  function get_slot()
  {
    return null;
  }
	/// Modifie le slot
	function set_slot($slot)
	{
    // Pas de slot par défaut
	}

  /// Indique si l'objet a été identifié
  function est_identifie()
  {
    return $this->identifie;
  }
	/// Modifie si l'objet a été identifié
	function set_identifie($identifie)
	{
    $this->identifie = $identifie;
	}

	// Renvoie la forme textuelle
	function get_texte()
	{
		return $this->texte;
	}

	/// Modifie la forme textuelle
	function set_texte($texte)
	{
		$this->texte = $texte;
	}

	// Renvoie l'id textuel
	function get_texte_id()
	{
		$id = explode('x', $this->texte);
		return $id[0];
	}
	
	// Renvoie l'encombrement de l'objet em magasin
	function get_encombrement()
	{
		return $this->encombrement;
	}
	
	/// Modifie l'encombrement de l'objet em magasin
	function set_encombrement($encombrement)
	{
		$this->encombrement = $encombrement;
		$this->champs_modif[] = 'encombrement';
	}
	
	/**
	 * Constructeur
	 * @param  $nom		nom de l'objet
	 * @param  $type	type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	prix de l'objet em magasin
	 */
	function __construct($nom='', $type='', $prix=0, $encombrement=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->prix = $prix;
			$this->encombrement = $encombrement;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->prix = $vals['prix'];
		$this->encombrement = $vals['encombrement'];
	}

  /**
   * Méthode créant le bon objet à partir de sa forme textuelle
   *
   * @param  $objet   Forme textuelle de l'objet
   */
  static function factory($objet, $cat=null)
  {
  	if( !$cat )
  	{
	    // nombre d'objet "stackés"
			$decomp = explode('x', $objet);
			$obj = $decomp[0];
			$stack = count($decomp) > 1 ? $decomp[1] : 1;
	    // enchantement par une gemme
			$decomp = explode('e', $obj);
			$obj = $decomp[0];
			$enchantement = count($decomp)>1 ? $decomp[1] : null;
			// Modification de forge
			$decomp = explode('f', $obj);
			$obj = $decomp[0];
			$modif = count($decomp)>1 ? $decomp[1] : null;
	    // slot disponible
			$decomp = explode('s', $obj);
			$obj = $decomp[0];
			$slot = count($decomp)>1 ? $decomp[1] : null;
	    // catégorie & id
	    if( $obj[0] == 'h' )
	    {
	      $ident = false;
	      $cat = $obj[1];
	      $id = substr($obj, 2);
	    }
	    else
	    {
	      $ident = true;
	      $cat = $obj[0];
	      $id = substr($obj, 1);
	    }
		}
		else
		{
			$id = $objet;
			$decomp = false;
		}
    switch($cat)
    {
    case 'p':
    case 'armure':
      $obj = new armure($id);
      $txt = 'p';
      break;
    case 'a':
    case 'arme':
      $obj = new arme($id);
      $txt = 'a';
      break;
    case 'l':
    case 'grimoire':
      $obj = new grimoire($id);
      $txt = 'l';
      break;
    case 'o':
    case 'objet':
      $obj = new objet($id);
      $txt = 'o';
      break;
    case 'd':
    case 'objet_pet':
    case 'dressage':
      $obj = new objet_pet($id);
      $txt = 'd';
      break;
    case 'g':
    case 'gemme':
      $obj = new gemme($id);
      $txt = 'g';
      break;
    case 'm':
    case 'accessoire':
      $obj = new accessoire($id);
      $txt = 'm';
      break;
    case 'r':
    case 'objet_royaume':
      $obj = new objet_royaume($id);
      $txt = 'r';
      break;
    default:
      //debug_print_backtrace();
      log_admin::log('erreur','catégorie d\'objet inconnue : "'.$cat.'"', true);
      return null;
    }
  	if( $decomp )
  	{
	    $obj->set_texte($objet);
	    $obj->set_nombre($stack);
	    $obj->set_enchantement($enchantement);
	    $obj->set_modification($modif);
	    $obj->set_slot($slot);
	    $obj->set_identifie($ident);
		}
		else
		{
	    $obj->set_texte($txt.$id);
		}
    return $obj;
  }
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'type'=>'s', 'prix'=>'i', 'encombrement'=>'i');
	}

  /// recompose la forme textuelle de l'objet
  function recompose_texte()
  {
    $this->texte = ($this->identifie ? '' : 'h').$this::code.$this->id;
    $slot = $this->get_slot();
    if( $slot !== null )
      $this->texte .= 's'.$slot;
    $modif = $this->get_modification();
    if( $modif !== null )
      $this->texte .= 'f'.$modif->get_id();
    $enchant = $this->get_enchantement();
    if( $enchant !== null )
      $this->texte .= 'e'.$enchant;
    $nbr = $this->get_nombre();
    if( $nbr > 1 )
      $this->texte .= 'x'.$nbr;
  }

	/// Méthode renvoyant l'image de l'objet
	public function get_image() { return null; }

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	abstract public function get_noms_infos($complet=true);

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	abstract public function get_valeurs_infos($complet=true);

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ() { return null; }

  /// Méthode renvoyant l'info sur l'enchantement par gemme
  public function get_info_enchant()
  {
    $enchant = $this->get_enchantement();
    if( $enchant )
    {
      $gemme = new gemme( $enchant );
      return 'enchantement '.$gemme->get_enchantement_nom();
    }
    else
    {
      $slot = $this->get_slot();
      if( $slot )
        return 'Slot niveau '.$slot;
      else if( $slot === '0' )
        return 'Slot impossible';
      else
        return null;
    }
  }

  /**
   */
  function get_colone($partie)
  {
    if( $this->identifie )
      return $this->get_colone_int($partie);
    else if( $partie == 'utile' )
      return 2;
    else
      return false;
  }


  /**
   */
  protected abstract function get_colone_int($partie);

  /// Indique si l'objet est utilisable
  function est_utilisable() { return false; }

  /// Indique si l'objet est slotable
  function est_slotable() { return false; }

  /// Indique si l'objet est slotable
  function est_enchassable() { return false; }

  /// Indique si l'objet est modifiable par la forge
  function est_modifiable() { return false; }

  /**
   * Utilise l'objet
   */
  function utiliser(&$perso, &$princ) { return false; }

  /**
   * Déposer
   */
  function deposer(&$perso, &$princ) { return false; }

  /**
   * Renvoie le prix de vente
   */
  function get_prix_vente()
  {
    global $G_taux_vente;
		$modif_prix = 1;
    /// @todo mettre le maxmum ailleurs
    $slot = $this->get_slot();
		if( $slot > 0 && $slot < 3 )
		{
			$modif_prix = 1 + ($slot / 5);
		}
		elseif($slot == 0)
		{
			$modif_prix = 0.9;
		}
    $enchant = $this->get_enchantement();
		if($enchant)
		{
      $gemme = new gemme( $enchant );
			$modif_prix = 1 + ($gemme->get_niveau() / 2);
		}
		return floor($this->get_prix() * $modif_prix / $G_taux_vente);
  }

  /**
   * Vendre au marchand
   */
  function vendre_marchand(&$perso, &$princ, $nombre)
  {
    $prix = $this->get_prix_vente();
    $stars = $prix * $nombre;
    $perso->add_star( $stars );
    $perso->supprime_objet($this->get_texte(), $nombre);
    return $stars;
  }

  /**
   * Mettre à l'hotel des ventes
   */
  function vendre_hdv(&$perso, &$princ, $prix)
  {
    global $db;
    // On vérifie qu'il n'est pas déjà au maximum d'objets en vente
    /// @todo à améliorer
    /*$requete = 'SELECT COUNT(*) FROM hotel WHERE type = "vente" AND id_vendeur = '.$perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$objet_max = 10;
		$bonus_craft = ceil($perso->get_artisanat() / 5);
		$objet_max += $bonus_craft;
		if($row[0] >= $objet_max)
		{
		   $princ->add( new interf_alerte('danger', true) )->add_message('Vous avez déjà '.$objet_max.' objets ou plus en vente.');
		}*/
    // On vérifie que le prix est positif
    if( $prix < 0 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('L\'objet ne peut être vendu : le prix n\'est pas valide !');
      return false;
    }
    // On vérifie que le prix est bien inférieure au prix max
    /// TODO : loguer les tentatives de triche
    /// TODO : centraliser prix max
    if( $prix > $this->get_prix_vente() * 10 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous voulez vendre cet objet trop chère, le commissaire priseur n\'en veut pas !');
      return false;
    }
    $case = new map_case( $perso->get_pos() );
    $R = new royaume( $case->get_royaume() );
    // On vérifie que le personnage a assez de star pour payer les taxes
    $taxe = round($prix * $R->get_taxe_diplo($perso->get_race()) / 100);
    if( $taxe > $perso->get_star() )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('L\'objet ne peut être vendu : vous n\'avez pas assez de stars pour payer la commission !');
      return false;
    }
    // On reverse les taxes au royaume
		$perso->supprime_objet($this->get_texte(), 1);
		$perso->set_star($perso->get_star() - $taxe);
		$perso->sauver();
		/// @todo passer par la nouvelle méthode
		$R->set_star($R->get_star() + $taxe);
		$R->sauver();
		$requete = 'UPDATE argent_royaume SET hv = hv + '.$taxe.' WHERE race = "'.$R->get_race().'"';
		$db->query($requete);
    // On ajoute l'objet dans l'hotel de ville
		$requete = 'INSERT INTO hotel VALUES (NULL, "'.$this->get_texte_id().'", '.$perso->get_id().', "vente", '.$prix.', "'.$R->get_race().'", '.time().')';
		$req = $db->query($requete);
    // On enregistre dans les logs
    log_admin::log('mis en vente HV', $perso->get_nom().' vend '.$this->nom.' ('.$this->id.') pour '.$prix.' stars. Taxes : '.$taxe.' stars');
    // réussi
    $princ->add( new interf_alerte('success', true) )->add_message('Vous mettez en vente '.$this->nom.' pour '.$prix.' stars. Taxes : '.$taxe.' stars');
    return true;
  }

  /**
   * Identifier
   */
  function identifier(&$perso, &$princ, $slot)
  {
    if( $this->identifie )
    {
      /// @todo ajouter un log de triche
			$princ->add( new interf_alerte('warning') )->add_message('L\'objet est déjà identifié !');
  		return false;
    }
		$materiel = $perso->recherche_objet('o2');
    /// @todo centraliser le coût
		if( !$materiel )
    {
      $princ->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas de materiel d\'identification');
  		return false;
		}
		else if( $perso->get_pa() < 1 )
    {
      $princ->add( new interf_alerte('danger') )->add_message('Vous n\'avez pas assez de PA !');
  		return false;
		}
		$perso->add_pa(-1);

    if( comp_sort::test_potentiel($perso->get_identification(), .2*pow($this->prix, .666)) )
    {
			//On remplace l'objet par celui identifiée
      /// @todo à refaire
			//$obj = mb_substr($perso->get_inventaire_slot_partie($slot), 1);
      $this->identifie = true;
      $this->recompose_texte();
			$perso->set_inventaire_slot_partie($this->get_texte(), $slot);
			$perso->set_inventaire_slot(serialize($perso->get_inventaire_slot_partie(false, true)));
      $msg = $princ->add( new interf_alerte('success') );
      $msg->add_message('Identification réussie !');
      $msg->add( new interf_bal_smpl('br') );
      $msg->add_message('L\'objet est : '.$this->nom);
      log_admin::log('identification', $perso->get_nom().' a identifié '.$this->nom);
    }
		else
      $princ->add( new interf_alerte('danger') )->add_message('L\'identification n\'a pas marché…', false);
		//On supprime l'objet de l'inventaire
		$perso->supprime_objet('o2', 1);

		$augmentation = augmentation_competence('identification', $perso, 3);
		if ($augmentation[1] == 1)
		{
			$perso->set_comp('identification', $augmentation[0]);
		}
		$perso->sauver(); // On sauve a la fin pour les PA
  }

  /**
   * Mettre un slot
   */
  function mettre_slot(&$perso, &$princ, $niveau) { return false; }

  /**
   * Mettre une gemme ou en retirer une
   */
  function enchasser(&$perso, &$princ, $gemme) { return false; }

  /**
   * Retirer une gemme
   */
  function recup_gemme(&$perso, &$princ) { return false; }
  
	public static function get_abbrev($categorie)
	{
		switch($categorie)
		{
			case 'arme':
				return 'a';
			case 'armure':
				return 'p';
			case 'dressage':
				return 'd';
			case 'objet':
				return 'o';
			case 'gemme':
				return 'g';
			case 'accessoire':
				return 'm';
			case 'grimoire':
				return 'l';
			case 'royaume':
				return 'r';
			default	:
				return '';
		}
	}
	static function decomp_nombre($objet)
	{
		$res = explode('x', $objet);
		if( count($res) == 1 )
			$res[1] = 1;
		return $res;
	}
	static function test_forge($objet)
	{
		return strstr($objet, 'f') !== false;
	}
}

class zone_invent extends objet_invent
{
  private $lock;
  private $perso;

  function __construct($zone, $lock, &$perso=null)
  {
    global $Gtrad;
    $this->type = $zone;
    $this->nom = $Gtrad[substr($zone, 0, 16)];
    $this->lock = $lock;
    $this->perso = &$perso;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
    if( $this->lock )
      return 'vérouillée';
    else
    {
      switch( $this->type )
      {
      case 'slot_1':
      case 'slot_2':
      case 'slot_3':
        return 'PA : 10';
      case 'enchasser':
        return 'PA : 20';
      case 'identifier':
        return 'PA : 1';
      case 'vendre_marchand':
      case 'hotel_vente':
      case 'depot':
      case 'utiliser':
        return null;
      default:
        return 'vide';
      }
    }
  }

  /// Méthode renvoyant l'info sur l'enchantement par gemme
  public function get_info_enchant()
  {
    switch( $this->type )
    {
    case 'slot_1':
      return pourcent_reussite($this->perso->get_forge(), 10).'% de chances de réussite';
    case 'slot_2':
      return pourcent_reussite($this->perso->get_forge(), 30).'% de chances de réussite';
    case 'slot_3':
      return pourcent_reussite($this->perso->get_forge(), 100).'% de chances de réussite';
    default:
      return null;
    }
  }

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $image = 'image/inventaire/'.$this->type.'.png';
    if( file_exists($image) )
      return $image;
    return null;
  }

  public function get_noms_infos($complet=true) {}
  public function get_valeurs_infos($complet=true) {}
  function get_colone_int($partie) { return false; }
}
?>
