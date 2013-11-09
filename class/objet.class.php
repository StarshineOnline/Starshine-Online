<?php
/**
 * @file objet.class.php
 * Gestion des objets alchimiques, ingrédients, de quêtes, et autrs objets divers
 */

/**
 * Classe gérant les objets alchimiques, ingrédients, de quêtes, et autrs objets divers.
 * Correspond à la table du même nom dans la bdd.
 */
class objet extends objet_equip
{
	protected $stack;  ///< nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	protected $utilisable;  ///< indique si l'objet est utilisable.
	protected $description;  ///< description de l'objet.
	protected $pa;  ///< pa nécessaires pour utiliser l'objet.
	protected $mp;  ///< mp nécessaires pour utiliser l'objet.

  /// Renvoie le nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	function get_stack()
	{
		return $this->stack;
	}
	/// Modifie le nombre d'exmplaires qu'on peut mettre dans un emplacement de l'inventaire.
	function set_stack($stack)
	{
		$this->stack = $stack;
		$this->champs_modif[] = 'stack';
	}

  /// Renvoie la description de l'objet
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description de l'objet
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

  /// Renvoie si l'objet est utilisable.
	function is_utilisable()
	{
		return $this->utilisable == 'y';
	}
	/// Modifie si l'objet est utilisable.
	function set_utilisable($utilisable)
	{
		$this->utilisable = $utilisable ? 'y' : 'n';
		$this->champs_modif[] = 'utilisable';
	}

  /// Renvoie les pa nécessaires pour utiliser l'objet.
	function get_pa()
	{
		return $this->pa;
	}
	/// Modifie les pa nécessaires pour utiliser l'objet.
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

  /// Renvoie les mp nécessaires pour utiliser l'objet.
	function is_mp()
	{
		return $this->mp;
	}
	/// Modifie les mp nécessaires pour utiliser l'objet.
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet.
	 * @param  $type	         type de l'objet.
	 * @param  $prix	         prix de l'objet em magasin.
	 * @param  $effet	         valeur de l'effet de l'objet.
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible.
	 * @param  $stack          indique combien d'exmplaire on peut mettre dans un emplacement de l'inventaire.
	 * @param  $utilisable     indique si l'objet est utilisable.
	 * @param  $description    description de l'objet.
	 * @param  $pa             pa nécessaire pour utiliser l'objet.
	 * @param  $mp             mp nécessaire pour utiliser l'objet.
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $stack=0, $utilisable='y', $description='', $pa=0, $mp=0)
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
			$this->stack = $stack;
			$this->utilisable = $utilisable;
			$this->description = $description;
			$this->pa = $pa;
			$this->mp = $mp;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_equip::init_tab($vals);
		$this->stack = $vals['stack'];
		$this->utilisable = $vals['utilisable'];
		$this->description = $vals['description'];
		$this->pa = $vals['pa'];
		$this->mp = $vals['mp'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['stack']='i';
    $tbl['utilisable']='s';
    $tbl['description']='s';
    $tbl['pa']='i';
    $tbl['mp']='i';
		return $tbl;
	}
	
	//Infobulle d'un objet
	function infobulle()
	{
		$milieu = '<tr><td>Stack:</td><td>'.$this->stack.'</td></tr>';
		$milieu .= '<tr><td>Description:</td></tr><tr><td>'.addslashes($this->description).'</td></tr>';
		return bulleBase($milieu);
	}
}
?>