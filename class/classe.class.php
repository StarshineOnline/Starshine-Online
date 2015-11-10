<?php
class classe_db

{
/**
    * @access private
    * @var tinyint(3)
    */
	private $id;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var text
    */
	private $description;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $rang;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	
	/**
	* @access public

	* @param tinyint(3) id attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param tinyint(3) rang attribut
	* @param varchar(50) type attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $rang = '', $type = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, description, rang, type FROM classe WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->rang, $this->type) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->rang = $id['rang'];
			$this->type = $id['type'];
			}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->rang = $rang;
			$this->type = $type;
			$this->id = $id;
		}
	}

	/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", rang = "'.mysql_escape_string($this->rang).'", type = "'.mysql_escape_string($this->type).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE classe SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO classe (nom, description, rang, type) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->rang).'", "'.mysql_escape_string($this->type).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}

	/**
	* Supprime de la base de donnée
	* @access public
	* @param none
	* @return none
	*/
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM classe WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
	* @access static
	* @param array|string $champs champs servant a trouver les résultats
	* @param array|string $valeurs valeurs servant a trouver les résultats
	* @param string $ordre ordre de tri
	* @param bool|string $keys Si false, stockage en tableau classique, si string stockage avec sous tableau en fonction du champ $keys
	* @return array $return liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
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

		$requete = "SELECT id, nom, description, rang, type FROM classe WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new classe_db($row);
				else $return[$row[$keys]] = new classe_db($row);
			}
		}
		else $return = array();
		return $return;
	}

	/**
	* Affiche l'objet sous forme de string
	* @access public
	* @param none
	* @return string objet en string
	*/
	function __toString()
	{
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', rang = '.$this->rang.', type = '.$this->type;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $description valeur de l'attribut description
	*/
	function get_description()
	{
		return $this->description;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $rang valeur de l'attribut rang
	*/
	function get_rang()
	{
		return $this->rang;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $id valeur de l'attribut
	* @return none
	*/
	function set_id($id)
	{
		$this->id = $id;
		$this->champs_modif[] = 'id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $description valeur de l'attribut
	* @return none
	*/
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $rang valeur de l'attribut
	* @return none
	*/
	function set_rang($rang)
	{
		$this->rang = $rang;
		$this->champs_modif[] = 'rang';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

}

class classe extends classe_db
{
	function __construct($id = 0, $nom = '', $description = '', $rang = '', $type = '')
	{
		if( (func_num_args() == 1) && (is_numeric($id) || is_array($id))) parent::__construct($id);
		else parent::__construct($id, $nom, $description, $rang, $type);
  }


  //fonction

}
?>
