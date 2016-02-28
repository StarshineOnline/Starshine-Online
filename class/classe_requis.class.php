<?php
class classe_requis extends table
{
	protected $id_classe;
	protected $competence;
	protected $requis;
	protected $categorie;

	function get_id_classe()
	{
		return $this->id_classe;
	}
	
	function set_id_classe($id_classe)
	{
		$this->id_classe = $id_classe;
		$this->champs_modif[] = 'id_classe';
	}

	function get_competence()
	{
		return $this->competence;
	}
	
	function set_competence($competence)
	{
		$this->competence = $competence;
		$this->champs_modif[] = 'competence';
	}

	function get_requis()
	{
		return $this->requis;
	}
	
	function set_requis($requis)
	{
		$this->requis = $requis;
		$this->champs_modif[] = 'requis';
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
	* Constructeur
	* @param int(10) id attribut
	* @param tinyint(3) id_classe attribut
	* @param varchar(50) competence attribut
	* @param int(10) requis attribut
	* @return none
	*/
	function __construct($id = 0, $id_classe = 0, $competence = '', $requis = 0, $categorie = 0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id_classe = $id_classe;
			$this->competence = $competence;
			$this->requis = $requis;
			$this->categorie = $categorie;

		}
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_classe, competence, requis FROM classe_requis WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_classe, $this->competence, $this->requis) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			}
		else
		{
			$this->id = $id;
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
		$this->requis = $vals['requis'];
		$this->categorie = $vals['categorie'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_classe'=>'i', 'competence'=>'s', 'requis'=>'i', 'categorie'=>'i');
	}

}
?>
