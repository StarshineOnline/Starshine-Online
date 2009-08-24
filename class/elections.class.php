<?php
class elections
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $id_royaume;

	/**
    * @access private
    * @var int(10)
    */
	private $date;

	/**
    * @access private
    * @var enum('universel','nomination')
    */
	private $type;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param tinyint(3) id_royaume attribut
	* @param int(10) date attribut
	* @param enum('universel','nomination') type attribut
	* @return none
	*/
	function __construct($id = 0, $id_royaume = 0, $date = 0, $type = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_royaume, date, type FROM elections WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->date, $this->type) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['id_royaume'];
			$this->date = $id['date'];
			$this->type = $id['type'];
			}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->date = $date;
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
				if($force) $champs = 'id_royaume = '.$this->id_royaume.', date = '.$this->date.', type = '.$this->type.'';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE elections SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO elections (id_royaume, date, type) VALUES(';
			$requete .= ''.$this->id_royaume.', '.$this->date.', '.$this->type.')';
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
			$requete = 'DELETE FROM elections WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_royaume, date, type FROM elections WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new elections($row);
				else $return[$row[$keys]][] = new elections($row);
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
		return 'id = '.$this->id.', id_royaume = '.$this->id_royaume.', date = '.$this->date.', type = '.$this->type;
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
	* @return tinyint(3) $id_royaume valeur de l'attribut id_royaume
	*/
	function get_id_royaume()
	{
		return $this->id_royaume;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $date valeur de l'attribut date
	*/
	function get_date()
	{
		return $this->date;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('universel','nomination') $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
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
	* @param tinyint(3) $id_royaume valeur de l'attribut
	* @return none
	*/
	function set_id_royaume($id_royaume)
	{
		$this->id_royaume = $id_royaume;
		$this->champs_modif[] = 'id_royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $date valeur de l'attribut
	* @return none
	*/
	function set_date($date)
	{
		$this->date = $date;
		$this->champs_modif[] = 'date';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('universel','nomination') $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

		//fonction

	static function get_prochain_election($id_royaume, $all = false)
	{
		if($all)
		{
			$champ ='id_royaume';
			$valeur = $id_royaume;
		}
		else
		{
			$champ = array('id_royaume', 'type');
			$valeur = array($id_royaume, 'universel');
		}
		return elections::create($champ, $valeur, 'id DESC');
	}

	static function is_mois_election($id_royaume)
	{
		$elections = elections::get_prochain_election($id_royaume);
		$explode_date = explode('-', $elections[0]);
		if($explode_date[0] == date('Y') && $explode_date[1] == date('m', mktime(0, 0, 0, date("m") + 1 , date("d"), date("Y")))) return true;
		else return false;
	}
}
?>
