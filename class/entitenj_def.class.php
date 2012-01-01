<?php
/**
 * @file entitenj_def.class.php
 * Définition de la classe entitenj_def
 */

/**
 * Classe abstraite entitenj_def
 * Cette classe de base pour les définitions des entités non joueurs (monstre, batiment)
 */
abstract class entitenj_def extends table
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $nom;  ///< Nom de l'entité
	protected $type;  ///< Type de l'entité
	protected $hp;  ///< HP maximum de l'entité
	protected $pp;  ///< PP de l'entité
	protected $pm;  ///< PM de l'entité
	protected $description;  ///< Description de l'entité

	/// Renvoie le nom de l'entité
	function get_nom()
	{
		return $this->nom;
	}
	/// Modifie le nom de l'entité
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/// Renvoie le type de l'entité
	function get_type()
	{
		return $this->type;
	}
	/// Modifie le type de l'entité
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/// Renvoie les HP maximum de l'entité
	function get_hp()
	{
		return $this->hp;
	}
	/// Modifie les HP maximum de l'entité
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}

	/// Renvoie la PP de l'entité
	function get_pp()
	{
		return $this->pp;
	}
	/// Modifie la PP de l'entité
	function set_pp($pp)
	{
		$this->pp = $pp;
		$this->champs_modif[] = 'pp';
	}

	/// Renvoie la PM de l'entité
	function get_pm()
	{
		return $this->pm;
	}
	/// Modifie la PM de l'entité
	function set_pm($pm)
	{
		$this->pm = $pm;
		$this->champs_modif[] = 'pm';
	}

	/// Renvoie la description de l'entité
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description de l'entité
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
  /**
   * Constructeur
   * @param  $id            Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param  $nom           Nom de l'entité
   * @param  $type          Type de l'entité
   * @param  $hp            HP maximums de l'entité
   * @param  $pp            PP de l'entité
   * @param  $pm            PM de l'entité
   * @param  $description   Description de l'entité
   */
  function __construct($id = 0, $nom = '', $type = '', $hp = 0, $pp = 0, $pm = 0, $description = '')
  {
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->type = $type;
			$this->hp = $hp;
			$this->pp = $pp;
			$this->pm = $pm;
			$this->description = $description;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->hp = $vals['hp'];
		$this->pp = $vals['pp'];
		$this->pm = $vals['pm'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'nom, type, hp, pp, pm';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return mysql_escape_string($this->nom).', '.mysql_escape_string($this->type).', '.$this->hp.', '.$this->pp.', '.$this->pm;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", hp = '.$this->hp.', pp = '.$this->pp.', pm = '.$this->pm;
	}
	// @}
}
?>
