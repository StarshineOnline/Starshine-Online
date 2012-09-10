<?php
class batiment_ville
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var int(10)
    */
	private $cout;

	/**
    * @access private
    * @var int(10)
    */
	private $entretien;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $level;

	/**
    * @access private
    * @var int(10)
    */
	private $hp;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param varchar(50) nom attribut
	* @param int(10) cout attribut
	* @param int(10) entretien attribut
	* @param varchar(50) type attribut
	* @param tinyint(3) level attribut
	* @param int(10) hp attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $cout = '', $entretien = '', $type = '', $level = '', $hp = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, cout, entretien, type, level, hp FROM batiment_ville WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->cout, $this->entretien, $this->type, $this->level, $this->hp) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->cout = $id['cout'];
			$this->entretien = $id['entretien'];
			$this->type = $id['type'];
			$this->level = $id['level'];
			$this->hp = $id['hp'];
			}
		else
		{
			$this->nom = $nom;
			$this->cout = $cout;
			$this->entretien = $entretien;
			$this->type = $type;
			$this->level = $level;
			$this->hp = $hp;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", cout = "'.mysql_escape_string($this->cout).'", entretien = "'.mysql_escape_string($this->entretien).'", type = "'.mysql_escape_string($this->type).'", level = "'.mysql_escape_string($this->level).'", hp = "'.mysql_escape_string($this->hp).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE batiment_ville SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO batiment_ville (nom, cout, entretien, type, level, hp) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->cout).'", "'.mysql_escape_string($this->entretien).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->level).'", "'.mysql_escape_string($this->hp).'")';
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
			$requete = 'DELETE FROM batiment_ville WHERE id = '.$this->id;
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

		$requete = "SELECT id, nom, cout, entretien, type, level, hp FROM batiment_ville WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new batiment_ville($row);
				else $return[$row[$keys]][] = new batiment_ville($row);
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
		return 'id = '.$this->id.', nom = '.$this->nom.', cout = '.$this->cout.', entretien = '.$this->entretien.', type = '.$this->type.', level = '.$this->level.', hp = '.$this->hp;
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
	* @return int(10) $cout valeur de l'attribut cout
	*/
	function get_cout()
	{
		return $this->cout;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $entretien valeur de l'attribut entretien
	*/
	function get_entretien()
	{
		return $this->entretien;
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
	* @return tinyint(3) $level valeur de l'attribut level
	*/
	function get_level()
	{
		return $this->level;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
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
	* @param int(10) $cout valeur de l'attribut
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
	* @param int(10) $entretien valeur de l'attribut
	* @return none
	*/
	function set_entretien($entretien)
	{
		$this->entretien = $entretien;
		$this->champs_modif[] = 'entretien';
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
	* @param tinyint(3) $level valeur de l'attribut
	* @return none
	*/
	function set_level($level)
	{
		$this->level = $level;
		$this->champs_modif[] = 'level';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $hp valeur de l'attribut
	* @return none
	*/
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}

		//fonction
}
?>
