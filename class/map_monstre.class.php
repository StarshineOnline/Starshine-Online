<?php
class map_monstre
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $type;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $x;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $y;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $hp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $level;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(40)
    */
	private $lib;

	/**
    * @access private
    * @var int(10)
    */
	private $mort_naturelle;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param mediumint(8) type attribut
	* @param mediumint(8) x attribut
	* @param mediumint(8) y attribut
	* @param mediumint(8) hp attribut
	* @param tinyint(3) level attribut
	* @param varchar(50) nom attribut
	* @param varchar(40) lib attribut
	* @param int(10) mort_naturelle attribut
	* @return none
	*/
	function __construct($id = 0, $type = 0, $x = 0, $y = 0, $hp = 0, $level = 0, $nom = '', $lib = '', $mort_naturelle = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT type, x, y, hp, level, nom, lib, mort_naturelle FROM map_monstre WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->type, $this->x, $this->y, $this->hp, $this->level, $this->nom, $this->lib, $this->mort_naturelle) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->type = $id['type'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->hp = $id['hp'];
			$this->level = $id['level'];
			$this->nom = $id['nom'];
			$this->lib = $id['lib'];
			$this->mort_naturelle = $id['mort_naturelle'];
			}
		else
		{
			$this->type = $type;
			$this->x = $x;
			$this->y = $y;
			$this->hp = $hp;
			$this->level = $level;
			$this->nom = $nom;
			$this->lib = $lib;
			$this->mort_naturelle = $mort_naturelle;
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
				if($force) $champs = 'type = '.$this->type.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', level = '.$this->level.', nom = "'.mysql_escape_string($this->nom).'", lib = "'.mysql_escape_string($this->lib).'", mort_naturelle = "'.mysql_escape_string($this->mort_naturelle).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE map_monstre SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO map_monstre (type, x, y, hp, level, nom, lib, mort_naturelle) VALUES(';
			$requete .= ''.$this->type.', '.$this->x.', '.$this->y.', '.$this->hp.', '.$this->level.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->lib).'", "'.mysql_escape_string($this->mort_naturelle).'")';
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
			$requete = 'DELETE FROM map_monstre WHERE id = '.$this->id;
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

		$requete = "SELECT id, type, x, y, hp, level, nom, lib, mort_naturelle FROM map_monstre WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new map_monstre($row);
				else $return[$row[$keys]][] = new map_monstre($row);
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
		return 'id = '.$this->id.', type = '.$this->type.', x = '.$this->x.', y = '.$this->y.', hp = '.$this->hp.', level = '.$this->level.', nom = '.$this->nom.', lib = '.$this->lib.', mort_naturelle = '.$this->mort_naturelle;
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
	* @return mediumint(8) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $x valeur de l'attribut x
	*/
	function get_x()
	{
		return $this->x;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $y valeur de l'attribut y
	*/
	function get_y()
	{
		return $this->y;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
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
	* @return varchar(40) $lib valeur de l'attribut lib
	*/
	function get_lib()
	{
		return $this->lib;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $mort_naturelle valeur de l'attribut mort_naturelle
	*/
	function get_mort_naturelle()
	{
		return $this->mort_naturelle;
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
	* @param mediumint(8) $type valeur de l'attribut
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
	* @param mediumint(8) $x valeur de l'attribut
	* @return none
	*/
	function set_x($x)
	{
		$this->x = $x;
		$this->champs_modif[] = 'x';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $y valeur de l'attribut
	* @return none
	*/
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $hp valeur de l'attribut
	* @return none
	*/
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
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
	* @param varchar(40) $lib valeur de l'attribut
	* @return none
	*/
	function set_lib($lib)
	{
		$this->lib = $lib;
		$this->champs_modif[] = 'lib';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $mort_naturelle valeur de l'attribut
	* @return none
	*/
	function set_mort_naturelle($mort_naturelle)
	{
		$this->mort_naturelle = $mort_naturelle;
		$this->champs_modif[] = 'mort_naturelle';
	}
}