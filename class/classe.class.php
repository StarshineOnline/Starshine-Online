<?php
class classe extends table
{
	protected $nom;
	protected $description;
	protected $rang;
	protected $type;
	protected $categories;		///< Nombre de catégories d'aptitudes (hors catégorie 0) pour le calcul de l'avancement

	function get_nom()
	{
		return $this->nom;
	}

	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	function get_description()
	{
		return $this->description;
	}
	
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

	function get_rang()
	{
		return $this->rang;
	}

	function set_rang($rang)
	{
		$this->rang = $rang;
		$this->champs_modif[] = 'rang';
	}

	function get_type()
	{
		return $this->type;
	}

	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	function get_categories()
	{
		return $this->categories;
	}

	function set_categories($valeur)
	{
		$this->categories = $valeur;
		$this->champs_modif[] = 'categories';
	}

	
	/**
	* @access public

	* @param tinyint(3) id attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param tinyint(3) rang attribut
	* @param varchar(50) type attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $rang = 0, $type = '', $categories=1)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->description = $description;
			$this->rang = $rang;
			$this->type = $type;
			$this->categories = $categories;
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
		$this->description = $vals['description'];
		$this->rang = $vals['rang'];
		$this->type = $vals['type'];
		$this->categories = $vals['categories'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'description'=>'s', 'rang'=>'i', 'type'=>'s', 'categories'=>'i');
	}

}
?>
