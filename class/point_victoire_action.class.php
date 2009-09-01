<?php
class point_victoire_action
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var varchar(100)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var int(10)
    */
	private $action;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $cout;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type_buff;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $effet;

	/**
    * @access private
    * @var text
    */
	private $description;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param varchar(100) nom attribut
	* @param varchar(50) type attribut
	* @param int(10) action attribut
	* @param mediumint(8) cout attribut
	* @param varchar(50) type_buff attribut
	* @param mediumint(8) effet attribut
	* @param text description attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $type = '', $action = '', $cout = '', $type_buff = '', $effet = '', $description = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, type, action, cout, type_buff, effet, description FROM point_victoire_action WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->type, $this->action, $this->cout, $this->type_buff, $this->effet, $this->description) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->type = $id['type'];
			$this->action = $id['action'];
			$this->cout = $id['cout'];
			$this->type_buff = $id['type_buff'];
			$this->effet = $id['effet'];
			$this->description = $id['description'];
			}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->action = $action;
			$this->cout = $cout;
			$this->type_buff = $type_buff;
			$this->effet = $effet;
			$this->description = $description;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", action = "'.mysql_escape_string($this->action).'", cout = "'.mysql_escape_string($this->cout).'", type_buff = "'.mysql_escape_string($this->type_buff).'", effet = "'.mysql_escape_string($this->effet).'", description = "'.mysql_escape_string($this->description).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE point_victoire_action SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO point_victoire_action (nom, type, action, cout, type_buff, effet, description) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->action).'", "'.mysql_escape_string($this->cout).'", "'.mysql_escape_string($this->type_buff).'", "'.mysql_escape_string($this->effet).'", "'.mysql_escape_string($this->description).'")';
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
			$requete = 'DELETE FROM point_victoire_action WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Supprime de la base de donnée
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

		$requete = "SELECT id, nom, type, action, cout, type_buff, effet, description FROM point_victoire_action WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new point_victoire_action($row);
				else $return[$row[$keys]][] = new point_victoire_action($row);
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
		return 'id = '.$this->id.', nom = '.$this->nom.', type = '.$this->type.', action = '.$this->action.', cout = '.$this->cout.', type_buff = '.$this->type_buff.', effet = '.$this->effet.', description = '.$this->description;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(100) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
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
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $action valeur de l'attribut action
	*/
	function get_action()
	{
		return $this->action;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $cout valeur de l'attribut cout
	*/
	function get_cout()
	{
		return $this->cout;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $type_buff valeur de l'attribut type_buff
	*/
	function get_type_buff()
	{
		return $this->type_buff;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $effet valeur de l'attribut effet
	*/
	function get_effet()
	{
		return $this->effet;
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
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id valeur de l'attribut
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
	* @param varchar(100) $nom valeur de l'attribut
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
	* @param varchar(50) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $action valeur de l'attribut
	* @return none
	*/
	function set_action($action)
	{
		$this->action = $action;
		$this->champs_modif[] = 'action';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $cout valeur de l'attribut
	* @return none
	*/
	function set_cout($cout)
	{
		$this->cout = $cout;
		$this->champs_modif[] = 'cout';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $type_buff valeur de l'attribut
	* @return none
	*/
	function set_type_buff($type_buff)
	{
		$this->type_buff = $type_buff;
		$this->champs_modif[] = 'type_buff';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $effet valeur de l'attribut
	* @return none
	*/
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
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
//fonction
}
?>
