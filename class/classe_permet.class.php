<?php
class classe_permet extends table
{
	protected $id_classe;   ///< id de la classe correspondante
	protected $competence;   ///< aptitude concernée
	protected $permet;   ///< maximum permit pas la classe
	protected $new;   ///< indique si l'aptitude est nouvelle
	protected $categorie;   ///< catégorie de l'aptitude pour le calcul de l'avancement

	/// renvoie l'id de la classe correspondante
	function get_id_classe()
	{
		return $this->id_classe;
	}

	/// Modifie l'id de la classe correspondante
	function set_id_classe($id_classe)
	{
		$this->id_classe = $id_classe;
		$this->champs_modif[] = 'id_classe';
	}
	
	/// Renvoie l'aptitude concernée
	function get_competence()
	{
		return $this->competence;
	}

	/// Modifie l'aptitude concernée
	function set_competence($competence)
	{
		$this->competence = $competence;
		$this->champs_modif[] = 'competence';
	}

	/// Renvoie le maximum permit pas la classe
	function get_permet()
	{
		return $this->permet;
	}

	/// Modifie le maximum permit pas la classe
	function set_permet($permet)
	{
		$this->permet = $permet;
		$this->champs_modif[] = 'permet';
	}

	/// Renvoie si l'aptitude est nouvelle
	function get_new()
	{
		return $this->new;
	}

	/// Modifie si l'aptitude est nouvelle
	function set_new($new)
	{
		$this->new = $new;
		$this->champs_modif[] = 'new';
	}

	/// Renvoie la catégorie de l'aptitude pour le calcul de l'avancement
	function get_categorie()
	{
		return $this->categorie;
	}

	/// Modifie la catégorie de l'aptitude pour le calcul de l'avancement
	function set_categorie($valeur)
	{
		$this->categorie = $valeur;
		$this->champs_modif[] = 'categorie';
	}
	
	/**
	* @access public

	* @param int(10) id attribut
	* @param tinyint(3) id_classe attribut
	* @param varchar(50) competence attribut
	* @param float permet attribut
	* @param enum('yes','no') new attribut
	* @return none
	*/
	function __construct($id = 0, $id_classe = 0, $competence = '', $permet = 0, $new = 'no', $categorie = 1)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->id_classe = $id_classe;
			$this->competence = $competence;
			$this->permet = $permet;
			$this->new = $new;
			$this->categorie = $categorie;

		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_classe = $vals['id_classe'];
		$this->competence = $vals['competence'];
		$this->permet = $vals['permet'];
		$this->new = $vals['new'];
		$this->categorie = $vals['categorie'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_classe'=>'i', 'competence'=>'s', 'permet'=>'i', 'new'=>'s', 'categorie'=>'i');
	}
}
?>
