<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class comp_perso
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
	private $id_comp;

	/**
    * @access private
    * @var varchar(50)
    */
	private $competence;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $valeur;

	/**
    * @access private
    * @var int(10)
    */
	private $id_perso;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_comp attribut
	* @param varchar(50) competence attribut
	* @param mediumint(8) valeur attribut
	* @param int(10) id_perso attribut
	* @return none
	*/
	function __construct($id = 0, $id_comp = 0, $competence = '', $valeur = '', $id_perso = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_comp, competence, valeur, id_perso FROM comp_perso WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_comp, $this->competence, $this->valeur, $this->id_perso) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_comp = $id['id_comp'];
			$this->competence = $id['competence'];
			$this->valeur = $id['valeur'];
			$this->id_perso = $id['id_perso'];
			}
		else
		{
			$this->id_comp = $id_comp;
			$this->competence = $competence;
			$this->valeur = $valeur;
			$this->id_perso = $id_perso;
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
				if($force) $champs = 'id_comp = '.$this->id_comp.', competence = "'.mysql_escape_string($this->competence).'", valeur = "'.mysql_escape_string($this->valeur).'", id_perso = "'.mysql_escape_string($this->id_perso).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE comp_perso SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO comp_perso (id_comp, competence, valeur, id_perso) VALUES(';
			$requete .= ''.$this->id_comp.', "'.mysql_escape_string($this->competence).'", "'.mysql_escape_string($this->valeur).'", "'.mysql_escape_string($this->id_perso).'")';
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
			$requete = 'DELETE FROM comp_perso WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_comp, competence, valeur, id_perso FROM comp_perso WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new comp_perso($row);
				else $return[$row[$keys]][] = new comp_perso($row);
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
		return 'id = '.$this->id.', id_comp = '.$this->id_comp.', competence = '.$this->competence.', valeur = '.$this->valeur.', id_perso = '.$this->id_perso;
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
	* @return int(10) $id_comp valeur de l'attribut id_comp
	*/
	function get_id_comp()
	{
		return $this->id_comp;
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
	* @return mediumint(8) $valeur valeur de l'attribut valeur
	*/
	function get_valeur()
	{
		return $this->valeur;
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
	* @param int(10) $id_comp valeur de l'attribut
	* @return none
	*/
	function set_id_comp($id_comp)
	{
		$this->id_comp = $id_comp;
		$this->champs_modif[] = 'id_comp';
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
	* @param mediumint(8) $valeur valeur de l'attribut
	* @return none
	*/
	function set_valeur($valeur)
	{
		$this->valeur = $valeur;
		$this->champs_modif[] = 'valeur';
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

		//fonction
}
?>
