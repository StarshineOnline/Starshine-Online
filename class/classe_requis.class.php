<?php
class classe_requis_db

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
	private $id_classe;

	/**
    * @access private
    * @var varchar(50)
    */
	private $competence;

	/**
    * @access private
    * @var int(10)
    */
	private $requis;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param tinyint(3) id_classe attribut
	* @param varchar(50) competence attribut
	* @param int(10) requis attribut
	* @return none
	*/
	function __construct($id = 0, $id_classe = 0, $competence = '', $requis = '')
	{
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
			$this->id_classe = $id['id_classe'];
			$this->competence = $id['competence'];
			$this->requis = $id['requis'];
			}
		else
		{
			$this->id_classe = $id_classe;
			$this->competence = $competence;
			$this->requis = $requis;
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
				if($force) $champs = 'id_classe = '.$this->id_classe.', competence = "'.mysql_escape_string($this->competence).'", requis = "'.mysql_escape_string($this->requis).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE classe_requis SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO classe_requis (id_classe, competence, requis) VALUES(';
			$requete .= ''.$this->id_classe.', "'.mysql_escape_string($this->competence).'", "'.mysql_escape_string($this->requis).'")';
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
			$requete = 'DELETE FROM classe_requis WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_classe, competence, requis FROM classe_requis WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new classe_requis_db($row);
				else $return[$row[$keys]] = new classe_requis_db($row);
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
		return 'id = '.$this->id.', id_classe = '.$this->id_classe.', competence = '.$this->competence.', requis = '.$this->requis;
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
	* @return tinyint(3) $id_classe valeur de l'attribut id_classe
	*/
	function get_id_classe()
	{
		return $this->id_classe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $competence valeur de l'attribut competence
	*/
	function get_competence()
	{
		return $this->competence;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $requis valeur de l'attribut requis
	*/
	function get_requis()
	{
		return $this->requis;
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
	* @param tinyint(3) $id_classe valeur de l'attribut
	* @return none
	*/
	function set_id_classe($id_classe)
	{
		$this->id_classe = $id_classe;
		$this->champs_modif[] = 'id_classe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $competence valeur de l'attribut
	* @return none
	*/
	function set_competence($competence)
	{
		$this->competence = $competence;
		$this->champs_modif[] = 'competence';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $requis valeur de l'attribut
	* @return none
	*/
	function set_requis($requis)
	{
		$this->requis = $requis;
		$this->champs_modif[] = 'requis';
	}

}

class classe_requis extends classe_requis_db {
  function __construct($id = 0, $id_classe = 0, $competence = '', $requis = '') {
    if( (func_num_args() == 1) && (
         is_numeric($id) || is_array($id)))
      parent::__construct($id);
    else
      parent::__construct($id, $id_classe, $competence, $requis);
  }


  //fonction

}
?>
