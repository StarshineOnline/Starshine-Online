<?php
/**
 * @file objet_pet.class.php
 * Gestion de l'équipement des créatuyres
 */

/**
 * Classe gérant les objets des créatuyres
 * Correspond à la table du même nom dans la bdd.
 */
class objet_pet extends objet_equip
{
	protected $dressage; ///< Valeur en dressage nécessaire pour utiliser l'objet.
	protected $bonus; ///< Type de bonus.
	protected $valeur; ///< Valeur du bonus.
	const code = 'd';   ///< Code de l'objet.

	/// Retourne la valeur en dressage nécessaire pour utiliser l'objet.
	function get_dressage()
	{
		return $this->dressage;
	}
	/// Modifie la valeur en dressage nécessaire pour utiliser l'objet.
	function set_dressage($dressage)
	{
		$this->dressage = $dressage;
		$this->champs_modif[] = 'dressage';
	}

	/// Retourne le type de bonus
	function get_bonus()
	{
		return $this->bonus;
	}
	/// Modifie le type de bonus
	function set_degat($bonus)
	{
		$this->bonus = $bonus;
		$this->champs_modif[] = 'bonus';
	}

	/// Retourne la valeur du bonus
	function get_valeur()
	{
		return $this->valeur;
	}
	/// Modifie la valeur du bonus
	function set_valeur($valeur)
	{
		$this->valeur = $valeur;
		$this->champs_modif[] = 'valeur';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet
	 * @param  $type	         type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	         prix de l'objet em magasin
	 * @param  $effet	         valeur de l'effet de l'objet
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible
	 * @param  $dressage	     valeur en dressage nécessaire pour utiliser l'objet
	 * @param  $bonus	         type de bonus
	 * @param  $valeur	   		 valeur du bonus
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $dressage=0, $bonus='', $valeur=0)
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
			$this->effet = $effet;
			$this->lvl_batiment = $lvl_batiment;
			$this->dressage = $dressage;
			$this->bonus = $bonus;
			$this->valeur = $valeur;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_equip::init_tab($vals);
		$this->dressage = $vals['dressage'];
		$this->bonus = $vals['bonus'];
		$this->valeur = $vals['valeur'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['dressage']='i';
    $tbl['bonus']='s';
    $tbl['valeur']='i';
		return $tbl;
	}

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $image = 'image/objet_pet/objet_pet'.$this->get_id().'.png';
    if( file_exists($image) )
      return $image;
    return null;
  }

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
  	global $Gtrad;
    $noms = array('Type', 'Dressage');
    $noms[] = $Gtrad[$this->type];
    if( $this->type == 'arme' )
    	$noms[] = 'Arme';
    $noms[] = $complet ? 'Prix HT (en magasin)' : 'Stars';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
  	global $Gtrad;
    $vals = array($this->type, $this->dressage, $this->valeur);
    if( $this->type == 'arme' )
    	$noms[] = $Gtrad[explode('_',$this->bonus)[1]];
    $vals = $this->prix;
    return $vals;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
  	global $Gtrad;
  	return $Gtrad[$this->bonus].' : '.$this->valeur;
  }
	
	function get_colone_int($partie)
  {
    if( $partie == 'equipement' )
      return 0;
    else
      return false;
  }

  function get_emplacement()
  {
    return $this->type;
  }
  
  function peut_utiliser(&$perso, $msg=true)
  {
    if( $perso->get_dressage() >= $this->dressage )
    	return true;
    if( $msg )
    	interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$this->dressage.' en dressage.');
    return false;
	}
}
?>