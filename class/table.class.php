<?php // -*- mode: php; tab-width: 2 -*-
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
    return get_called_class();
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
	 * Charge un élément de la base de donnée ou directement à partid d'un tableau
	 * @param $id    Id (clé primaire) de l'élément dans la table ou tableau contenant les valeurs des données.
	 */
  protected function charger($id)
  {
		global $db;
		if( is_array($id) )
		{
      $this->init_tab( $id );
    }
    else
    {
  		$requete = 'SELECT * FROM '.$this->get_table().' WHERE '.$this->get_champ_id().' = "'.$id.'"';
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
				$liste = $this->get_champs();
				$champs = array();
				$params = array();
				$types = '';
				if($force)
				{
					foreach($liste as $champ=>$type)
					{
						$params[] = $this->get_champ($champ);
						$champs[] = $champ.' = ?';
						$types .= $type;
					}
				}
				else
				{
					foreach($this->champs_modif as $champ)
					{
						$params[] = $this->get_champ($champ);
						$champs[] = $champ.' = ?';
						$types .= $liste[$champ];
					}
				}
				$champs = implode(', ', $champs);
				$requete = 'UPDATE '.$this->get_table().' SET '.$champs.' WHERE '.$this->get_champ_id().' = "'.$this->id.'"';
				$db->param_query($requete, $params, $types);
				$this->champs_modif = array();
			}
		}
		else
		{
			$liste = $this->get_champs();
			$params = array();
			$vals = array();
			$types .= '';
			foreach($liste as $champ=>$type)
			{
				$params[] = $this->get_champ($champ);
				$types .= $type;
				$vals[] = '?';
			}
			$champs = $liste[0];
			$champs = implode(', ', array_keys($champs));
			$vals = implode(', ', $vals);
			$requete = 'INSERT INTO '.$this->get_table().' ('.$champs.') VALUES('.$vals.')';
			$db->query($requete, $params, $types);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}

	/// Renvoie la valeur d'un champ de la base de donnée
	protected function get_champ($champ)
	{
		return $this->{$champ};
	}
  
	/// Renvoie la liste des champs sous forme de tableau associatif nom=>type pour une insertion dans la base
	/*abstract*/ protected function get_champs()
	{// implentation provisoire
		$champs = array();
		$liste = explode(',', $this->get_liste_champs());
		foreach($liste as $champ)
		{
			$champs[$champ] = 's';
		}
	}
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs() {}
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert() {}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update() {}
	
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
	* @param bool  $key_unique       Indique si la clé est unique ou non (si elle est unique
	*                                pour chaque clé il y a un seul objet, sinon il y a un tablmeau d'objets)
	* @return array     Liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false, $key_unique=false)
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

		$requete = 'SELECT '.static::get_champ_id().', '.static::get_liste_champs().' FROM '.static::get_table().' WHERE '.$where.' ORDER BY '.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
		  $classe = get_called_class();
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new $classe($row);
				elseif($key_unique) $return[$row[$keys]] = new $classe($row);
				else $return[$row[$keys]][] = new $classe($row);
			}
		}
		else $return = array();
		return $return;
	}

	/**
	* Crée un tableau d'objets respectant certains critères pour n'importe qu'elle table
	* @param string      $classe     Classe des objets à créer
	* @param string      $table      Table ou chercher
	* @param string      $cond       Condition (+ éventuellement tri)
	* @param bool|string $keys       Si false, stockage en tableau classique, si string
	*                                stockage avec sous tableau en fonction du champ $keys
	* @return array     Liste d'objets
	* 
	* @TODO: supprimer ?		
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
	
	static function get_valeurs($champs, $cond, $rangement=true)
	{
		global $db;
		if( is_array($champs) )
			$champs = implode(',', $champs);
		if( is_string($rangement) )
			$ordre = ' ORDER BY '.$rangement;
		else
		{
			$ordre= '';
			if( is_array($rangement) )
				$champs .= ', CONCAT_WS("|",'.implode(',', $rangement).') AS get_valeurs_cle';
		}
		
		
		$requete = 'SELECT '.$champs.' FROM '.static::get_table().' WHERE '.$cond.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			if( $rangement === false )
				return $db->read_object($req);
			else if( is_array($rangement) )
			{
				$liste = array();
				while( $row = $db->read_assoc($req) )
				{
					$liste[ $row['get_valeurs_cle'] ] = $row;
				}
			}
			else
			{
				$liste = array();
				while( $row = $db->read_assoc($req) )
				{
					$liste[] = $row;
				}
			}
			return $liste;
		}
		return null;
	}
	
	/// Affiche l'objet sous forme de string
	function __toString()
	{
    return 'id = '.$this->get_id().', '.$this->get_liste_update();
	}
} 
?>
