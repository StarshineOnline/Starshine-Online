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
	
	// Renvoie le nom de l'objet
	function get_nom()
	{
		return $this->nom;
	}
	
	/// Modifie le nom de l'objet
	function set_nom($newNom)
	{
		$this->nom = $newNom;
	}
	
	// Renvoie le type de l'objet
	function get_type()
	{
		return $this->type;
	}
	
	/// Modifie le type de l'objet
	function set_type($newType)
	{
		$this->type = $newType;
	}
	
	// Renvoie le prix de l'objet em magasin
	function get_prix()
	{
		return $this->prix;
	}
	
	/// Modifie le prix de l'objet em magasin
	function set_prix($newPrix)
	{
		$this->prix = $newPrix;
	}

  /// Renvoie le nombre d'exmplaires disponibles.
	function get_nombre()
	{
		return 1;
	}
	/// Modifie le nombre d'exmplaires disponibles.
	function set_nombre($stack)
	{
    // Pas de stack par défaut
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
	
	/**
	 * Constructeur
	 * @param  $nom		nom de l'objet
	 * @param  $type	type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	prix de l'objet em magasin
	 */
	function __construct($nom='', $type='', $prix=0)
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
	}

  /**
   * Méthode créant le bon objet à partir de sa forme textuelle
   *
   * @param  $objet   Forme textuelle de l'objet
   */
  function factory($objet)
  {
    // nombre d'objet "stackés"
		$decomp = explode('x', $objet);
		$objet = $decomp[0];
		$stack = $decomp[1];
    // enchantement par une gemme
		$decomp = explode('e', $objet);
		$objet = $decomp[0];
		$enchantement = $decomp[1];
    // slot disponible
		$decomp = explode('s', $objet);
		$objet = $decomp[0];
		$slot = $decomp[1];
    // catégorie & id
    if( $objet[0] == 'h' )
    {
      $ident = false;
      $cat = $objet[1];
      $id = substr($objet, 2);
    }
    else
    {
      $ident = true;
      $cat = $objet[0];
      $id = substr($objet, 1);
    }
    switch($cat)
    {
    case 'p':
      $obj = new armure($id);
      break;
    case 'a':
      $obj = new arme($id);
      break;
    case 'l':
      $obj = new grimoire($id);
      break;
    case 'o':
      $obj = new objet($id);
      break;
    case 'd':
      $obj = new objet_pet($id);
      break;
    case 'g':
      $obj = new gemme($id);
      break;
    case 'm':
      $obj = new accessoire($id);
      break;
    case 'r':
      $obj = new objet_royaume($id);
      break;
    default:
      die("catégorie d'objet inconnue : '$cat'");
    }
    $obj->set_nombre($stack);
    $obj->set_enchantement($enchantement);
    $obj->set_slot($slot);
    $obj->set_identifie($ident);
    return $obj;
  }
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'type'=>'s', 'prix'=>'i');
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
      return $enchant->get_description();
    else
    {
      $slot = $this->get_slot();
      if( $slot )
        return 'Slot niveau '.$slot;
      else if( $slot === 0 )
        return 'Slot impossible';
      else
        return null;
    }
  }

  /**
   */
  abstract function get_colone($partie);

  /**
   */
  function est_utilisable() { return false; }

  /**
   */
  function utiliser(&$perso, &$princ) { return false; }

  /**
   * Déposer
   */
  function deposer() { return false; }

  /**
   * Vendre au marchand
   */
  function vendre_marchand()
  {
    global $G_taux_vente;
		$modif_prix = 1;
    /// TODO: mettre le maxmum ailleurs
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
			$modif_prix = 1 + ($enchant->get_niveau() / 2);
		}
		$prix = floor($this->get_prix() * $modif_prix / $G_taux_vente);
    $perso->add_star( $prix );
    return true;
  }

  /**
   * Mettre à l'hotel des ventes
   */
  function vendre_hdv() { return false; }
}

class zone_invent extends objet_invent
{
  private $lock;
  private $perso;

  function __construct($zone, $lock, &$perso=null)
  {
    global $Gtrad;
    $this->type = $zone;
    $this->nom = $Gtrad[$zone];
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
  function get_colone($partie) { return false; }
}
?>
