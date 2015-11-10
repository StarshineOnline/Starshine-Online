<?php
/**
 *	@file alchimie_recette.class.php
 * Classe représentant une recette d'alchimie.
 */
 
 
/**
 * Classe représentant une recette d'alchimie.
 */
class alchimie_recette extends table
{
	/// Renvoie le nom de la table (surcharge temporaire en attendant de modifier la table)
	static function get_table()
	{
		return 'craft_recette';
	}
	
	protected $nom;
	protected $description;
	protected $type;  ///< Type de d'effet
	protected $difficulte;  ///< Difficulté pour réaliser la recette
	protected $royaume_alchimie;  ///<< Valeur de recherche en alchimie du royaume nécessaire pour que la recette soit disponibe
	protected $prix;  ///< Prix d'achat de la recette
	
	/// Renvoie le nom
	function get_nom()
	{
		return $this->nom;
	}
	/// Modifie le nom
	function set_nom($val)
	{
		$this->nom = $val;
	}
	
	/// Renvoie la description
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description
	function set_description($val)
	{
		$this->description = $val;
	}
	
	/// Renvoie le type d'effet
	function get_type()
	{
		return $this->type;
	}
	/// Modifie le type d'effet
	function set_type($val)
	{
		$this->type = $val;
	}
	
	/// Renvoie la difficulte
	function get_difficulte()
	{
		return $this->difficulte;
	}
	/// Modifie la difficulte
	function set_difficulte($val)
	{
		$this->difficulte = $val;
	}
	
	/// Renvoie la valeur de recherche en alchimie du royaume nécessaire pour la recette
	function get_royaume_alchimie()
	{
		return $this->royaume_alchimie;
	}
	/// Modifie la valeur de recherche en alchimie du royaume nécessaire pour la recette
	function set_royaume_alchimie($val)
	{
		$this->royaume_alchimie = $val;
	}
	
	/// Renvoie le prix
	function get_prix()
	{
		return $this->prix;
	}
	/// Modifie le prix
	function set_prix($val)
	{
		$this->prix = $val;
	}
	
	/**
	 * Constructeur	
	 */	
	function __construct($id=0, $nom='', $type='', $description='', $difficulte=0, $royaume_alchimie=0, $prix=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->type = $type;
			$this->description = $description;
			$this->difficulte = $difficulte;
			$this->royaume_alchimie = $royaume_alchimie;
			$this->prix = $prix;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
		$this->id = $vals['id'];
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->description = $vals['description'];
		$this->difficulte = $vals['difficulte'];
		$this->royaume_alchimie = $vals['royaume_alchimie'];
		$this->prix = $vals['prix'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'type'=>'s', 'description'=>'s', 'difficulte'=>'i', 'royaume_alchimie'=>'i', 'prix'=>'i');
	}

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    return array('Description');
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    return array($this->description);
  }
}

?>