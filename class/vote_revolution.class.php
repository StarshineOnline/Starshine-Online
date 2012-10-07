<?php
class vote_revolution
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var int(10)
    */
	private $id_revolution;

	/**
    * @access private
    * @var int(10)
    */
	private $id_perso;

	/**
    * @access private
    * @var tinyint(1)
    */
	private $pour;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $poid_vote;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_revolution attribut
	* @param int(10) id_perso attribut
	* @param tinyint(1) pour attribut
	* @param mediumint(8) poid_vote attribut
	* @return none
	*/
	function __construct($id = 0, $id_revolution = 0, $id_perso = 0, $pour = 0, $poid_vote = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_revolution, id_perso, pour, poid_vote FROM vote_revolution WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_revolution, $this->id_perso, $this->pour, $this->poid_vote) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_revolution = $id['id_revolution'];
			$this->id_perso = $id['id_perso'];
			$this->pour = $id['pour'];
			$this->poid_vote = $id['poid_vote'];
			}
		else
		{
			$this->id_revolution = $id_revolution;
			$this->id_perso = $id_perso;
			$this->pour = $pour;
			$this->poid_vote = $poid_vote;
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
				if($force) $champs = 'id_revolution = '.$this->id_revolution.', id_perso = '.$this->id_perso.', pour = '.$this->pour.', poid_vote = '.$this->poid_vote.'';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE vote_revolution SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO vote_revolution (id_revolution, id_perso, pour, poid_vote) VALUES(';
			$requete .= ''.$this->id_revolution.', '.$this->id_perso.', '.$this->pour.', '.$this->poid_vote.')';
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
			$requete = 'DELETE FROM vote_revolution WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_revolution, id_perso, pour, poid_vote FROM vote_revolution WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new vote_revolution($row);
				else $return[$row[$keys]][] = new vote_revolution($row);
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
		return 'id = '.$this->id.', id_revolution = '.$this->id_revolution.', id_perso = '.$this->id_perso.', pour = '.$this->pour.', poid_vote = '.$this->poid_vote;
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
	* @return int(10) $id_revolution valeur de l'attribut id_revolution
	*/
	function get_id_revolution()
	{
		return $this->id_revolution;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_perso valeur de l'attribut id_perso
	*/
	function get_id_perso()
	{
		return $this->id_perso;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(1) $pour valeur de l'attribut pour
	*/
	function get_pour()
	{
		return $this->pour;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $poid_vote valeur de l'attribut poid_vote
	*/
	function get_poid_vote()
	{
		return $this->poid_vote;
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
	* @param int(10) $id_revolution valeur de l'attribut
	* @return none
	*/
	function set_id_revolution($id_revolution)
	{
		$this->id_revolution = $id_revolution;
		$this->champs_modif[] = 'id_revolution';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_perso valeur de l'attribut
	* @return none
	*/
	function set_id_perso($id_perso)
	{
		$this->id_perso = $id_perso;
		$this->champs_modif[] = 'id_perso';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(1) $pour valeur de l'attribut
	* @return none
	*/
	function set_pour($pour)
	{
		$this->pour = $pour;
		$this->champs_modif[] = 'pour';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $poid_vote valeur de l'attribut
	* @return none
	*/
	function set_poid_vote($poid_vote)
	{
		$this->poid_vote = $poid_vote;
		$this->champs_modif[] = 'poid_vote';
	}

		//fonction
}
?>
