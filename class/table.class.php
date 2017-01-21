<?php // -*- mode: php; tab-width: 2 -*-
/**
 * Classe de base pour les objets représentant un élément d'une table dans la 
 * base de donnée. 
 */
abstract class table
{
	protected $id; ///< id de l'élément dans la table.
	protected $champs_modif=array();  ///< Liste des champs modifiés.
	
	/// Renvoie le nom du champ servant d'identifiant
	protected function get_champ_id()
	{
		return 'id';
	}
	/// Renvoie le nom de la table (par défaut le nom de la classe)
	static function get_table()
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
		if( is_array($id) )
		{
			$this->init_tab( $id );
		}
		else
		{
			$requete = 'SELECT * FROM '.$this->get_table().' WHERE '.$this->get_champ_id().' = "'.$id.'"';
			$req = $this->get_db()->query($requete);
			if( $this->get_db()->num_rows($req) )
			{
				$this->init_tab( $this->get_db()->read_assoc($req) );
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
		$this->id = $vals[$this->get_champ_id()];
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
		if( $this->id > 0 )
		{
			if( $force || count($this->champs_modif) > 0 )
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
						$val = $this->get_champ($champ);
						if( $val === null )
							$val = 'NULL';
						else
							$val = '"'.mysql_escape_string($val).'"';
						$champs[] .= $champ.' = '.$val;
						$params[] = $this->get_champ($champ);
						$champs[] = $champ.' = ?';
						$types .= $liste[$champ];
					}
				}
				$champs = implode(', ', $champs);
				$requete = 'UPDATE '.$this->get_table().' SET '.$champs.' WHERE '.$this->get_champ_id().' = "'.$this->id.'"';
				$this->get_db()->param_query($requete, $params, $types);
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
				$params[] = $this->get_champ($champ) !== null ? $this->get_champ($champ) : 'NULL';
				$types .= $type;
				$vals[] = '?';
			}
			//$champs = $liste[0];
			$champs = implode(', ', array_keys($liste));
			$vals = implode(', ', $vals);
			$requete = 'INSERT INTO '.$this->get_table().' ('.$champs.') VALUES('.$vals.')';
			$this->get_db()->param_query($requete, $params, $types);
			//Récuperation du dernier ID inséré.
			$this->id = $this->get_db()->last_insert_id();
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
			$champs[trim($champ)] = 's';
		}
		return $champs;
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
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM '.$this->get_table().' WHERE '.$this->get_champ_id().' = "'.$this->id.'"';
			$this->get_db()->query($requete);
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
	 *                                pour chaque clé il y a un seul objet, sinon il y a un tableau d'objets)
	 * @return array     Liste d'objets
	 */
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false, $key_unique=false)
	{
		$return = array();
		if(!$where)
			$where = self::construit_condition($champs, $valeurs);

		$requete = 'SELECT * FROM '.static::get_table().' WHERE '.$where.' ORDER BY '.$ordre;
		$req = static::get_db()->query($requete);
		if(static::get_db()->num_rows($req) > 0)
		{
		  $classe = get_called_class();
			while($row = static::get_db()->read_assoc($req))
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
	* @todo supprimer ?		
	*/
	static function gen_create($classe, $table, $cond, $keys = false)
	{
		$return = array();
		
		$requete = 'SELECT * FROM '.$table.' WHERE '.$cond;
		$req = static::$db->query($requete);
		if(static::get_db()->num_rows($req) > 0)
		{
			while($row = static::get_db()->read_assoc($req))
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
		$req = static::get_db()->query($requete);
		if(static::get_db()->num_rows($req) > 0)
		{
			if( $rangement === false )
				return static::get_db()->read_object($req);
			else if( is_array($rangement) )
			{
				$liste = array();
				while( $row = static::get_db()->read_assoc($req) )
				{
					$liste[ $row['get_valeurs_cle'] ] = $row;
				}
			}
			else
			{
				$liste = array();
				while( $row = static::get_db()->read_assoc($req) )
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
	
	/**
	 * Recherche et retourne le premier objet correspondant
	 *
	 * @param array $params Tableau de conditions, de la forme array('nom_champ_table' => 'valeur')
	 * @param array $orders Tableau pour trier la recherche, de la forme array('nom_champ_table' => 'ASC|DESC')
	 * @return Object Le premier objet correspondant à la demande, si aucun objet ne correspond la fonction retourne null
	 * 
	 * @todo à renommer pour respecter la convetion de nommage utilisée
	 */
	public static function findOneBy($params = array(), $orders = array())
	{
		$result = null;
		
		$className = get_called_class();
		$results = $className::findBy($params, $orders);
		if(!empty($results))
			$result = current($results);
		
		return $result;
	}
	
	/**
	 * Recherche et retourne tous les objets
	 *
	 * @param array $orders Tableau pour trier la recherche, de la forme array('nom_champ_table' => 'ASC|DESC')
	 * @return array[Object] Tableau contenant tous les objets, peut être vide
	 * 
	 * @todo à renommer pour respecter la convetion de nommage utilisée
	 */
	public static function findAll($orders = array())
	{
		$className = get_called_class();
		return $className::findBy(array(), $orders);
	}
	
	/**
	 * Recherche et retourne tous les objets correspondants
	 *
	 * @param array $params Tableau de conditions, de la forme array('nom_champ_table' => 'valeur')
	 * @param array $orders Tableau pour trier la recherche, de la forme array('nom_champ_table' => 'ASC|DESC')
	 * @return array[Object] Tableau contenant tous les objets correspondants, peut être vide
	 * 
	 * @todo à renommer pour respecter la convetion de nommage utilisée
	 */
	public static function findBy($params = array(), $orders = array())
	{
		$results = array();
		
		$requete = '';
		$requete .= 'SELECT * FROM '.self::get_table();
		$requete .= ' WHERE 1';
		foreach($params as $key => $value){
			$requete .= ' AND';
			$requete .= " $key = $value";
		}
		if(!empty($orders))
		{
			$requete .= ' ORDER BY';
			$first = true;
			foreach($orders as $key => $value)
			{
				if($first) $first = false;
				else $requete .= ',';
				$requete .= " $key $value";
			}
		}
		
		$req = static::get_db()->query($requete);
		
		$className = get_called_class();
		while($row = static::get_db()->read_assoc($req))
		{
			$results[] = new $className($row);
		}
		
		return $results;
	}
	
	static function calcul_somme($somme, $champs, $valeurs, $groupe=false, $where=false)
	{
		if(!$where)
			$where = self::construit_condition($champs, $valeurs);
		if( $groupe )
		{
			$sel_grp == $groupe.', ';
			$group_by = ' GROUP BY '.$groupe;
		}
		else
			$sel_grp = $group_by = '';

		if( is_array($somme) )
		{
			$unique = false;
			foreach($somme as &$s)
			{
				$s = 'SUM('.$s.') AS '.$s;
			}
			$somme = implode(', ', $somme);
		}
		else
		{
			$somme = 'SUM('.$somme.')';
			$unique = true;
		}
		$requete = 'SELECT '.$sel_grp.$somme.' FROM '.static::get_table().' WHERE '.$where.$group_by;
		$req = static::get_db()->query($requete);
		if($groupe)
		{
			$res = array();
			while($row = static::get_db()->read_array($req))
			{
				$res[ $row[$groupe] ] = $unique ? $row : $row[0];
			}
			return $res;
		}
		$row = static::get_db()->read_array($req);
		if($unique)
			return $row[0];
		return $row;
	}
	
	static function calcul_nombre($champs, $valeurs, $groupe=false, $where=false)
	{
		if(!$where)
			$where = self::construit_condition($champs, $valeurs);
		if( $groupe )
		{
			$sel_grp == $groupe.', ';
			$group_by = ' GROUP BY '.$groupe;
		}
		else
			$sel_grp = $group_by = '';
		$requete = 'SELECT '.$sel_grp.'COUNT(*) AS nbr FROM '.static::get_table().' WHERE '.$where.$group_by;
		$req = static::get_db()->query($requete);
		if($groupe)
		{
			$res = array();
			while($row = static::get_db()->read_assoc($req))
			{
				$res[ $row[$groupe] ] = $row['nbr'];
			}
			return $res;
		}
		$row = static::get_db()->read_array($req);
		return $row[0];
	}
	
	protected function construit_condition($champs, $valeurs)
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
			if( $array_valeurs[$key] === null )
				$where[] = $champ .' IS NULL';
			else
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
		}
		$where = implode(' AND ', $where);
		if($champs === 0)
		{
			$where = ' 1 ';
		}
		return $where;
	}
	
	static protected function &get_db()
	{
		global $db;
		return $db;
	}
}