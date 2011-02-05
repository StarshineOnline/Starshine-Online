<?php
/**
 * Classe de base pour les objets représentant un élément d'une table dans la 
 * base de donnée. 
 */
abstract class table
{
	protected $id; ///< id de l'élément dans la table.
	protected $champs_modif;  ///< Liste des champs modifiés.
	
	/// Renvoie le nom du champ servant d'identifiant
	protected function get_champ_id()
	{
    return 'id';
  }
  /// Renvoie le nom de la table (par défaut le nom de la classe)
  protected function get_table()
  {
    return get_class($this);
  }
	
	/// Renvoie l'id de l'élément dans la table
	function get_id()
	{
		return $this->id;
	}
	/// Modifieur l'id de l'élément dans la table
	function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Charge un élément de la base de donnée
	 * @param $id    Id (clé primaire) de l'élément dans la table
	 */
  protected function charger($id)
  {
		global $db;
		$requete = 'SELECT '.$this->get_liste_champs().' FROM '.$this->get_table().' WHERE '.$this->get_champ_id().' = "'.$id.'"';
		$req = $db->query($requete);
		if( $db->num_rows($req) )
		{
		  $this->init_tab( $db->read_assoc($req) );
    }
    else
    {
      $this->__construct();
      $this->id = $id;
    }
  }
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    $this->id = $vals['id'];
  }
	
	/**
   * Sauvegarde automatiquement l'élément dans la base de donnée.
   * Si c'est un nouvel objet utilise INSERT sinon UPDATE.
   *    
	 * @param bool $force    Force la mis à jour de tous les attributs de l'objet 
	 *                       si true, sinon uniquement ceux qui ont été modifiés.
   */   
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = $this->get_liste_update();
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
            $val = $this->get_champ($champ);
            if( $val === null )
              $val = 'NULL';
            else
              $val = '"'.mysql_escape_string($val).'"';
						$champs[] .= $champ.' = '.$val;
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE '.$this->get_table().' SET '.$champs.' WHERE '.$this->get_champ_id().' = "'.$this->id.'"';
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO '.$this->get_table().' ('.$this->get_liste_champs().') VALUES('.$this->get_valeurs_insert().')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}

  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    return $this->{$champ};
  }
  
	/// Renvoie la liste des champs pour une insertion dans la base
	abstract protected function get_liste_champs();
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	abstract protected function get_valeurs_insert();
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	abstract protected function get_liste_update();
	
	/// Supprime l'élément de la base de donnée
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM '.$this->get_table().' WHERE '.$this->get_champ_id().' = "'.$this->id.'"';
			$db->query($requete);
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
	* @param array|string $champs    Champs servant a trouver les résultats
	* @param array|string  $valeurs  Valeurs servant a trouver les résultats
	* @param string  $ordre          Ordre de tri
	* @param bool|string $keys       Si false, stockage en tableau classique, si string 
	*                                stockage avec sous tableau en fonction du champ $keys
	* @return array     Liste d'objets
	*/
	/*static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = 'SELECT '.static::get_champ_id().', '.$classe->get_liste_champs().' FROM '.$this->get_table().' WHERE '.$where.' ORDER BY '.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
		  $classe = get_class($this);
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new $classe($row);
				else $return[$row[$keys]][] = new $classe($row);
			}
		}
		else $return = array();
		return $return;
	}*/

	/**
	* Crée un tableau d'objets respectant certains critères pour n'importe qu'elle table
	* @param string      $classe     Classe des objets à créer
	* @param string      $table      Table ou chercher
	* @param string      $cond       Condition (+ éventuellement tri)
	* @param bool|string $keys       Si false, stockage en tableau classique, si string
	*                                stockage avec sous tableau en fonction du champ $keys
	* @return array     Liste d'objets
	*/
	static function gen_create($classe, $table, $cond, $keys = false)
	{
		global $db;
		$return = array();
		
		$requete = 'SELECT * FROM '.$table.' WHERE '.$cond;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys)
          $return[] = new $classe($row);
				else
          $return[$row[$keys]][] = new $classe($row);
			}
		  return $return;
		}
		else
      $return = array();
	}
	
	/// Affiche l'objet sous forme de string
	function __toString()
	{
    return 'id = '.$this->get_id().', '.$this->get_liste_update();
	}
} 
?>
