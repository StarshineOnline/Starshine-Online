<?php
class buff
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
	private $type;

	/**
    * @access private
    * @var float
    */
	private $effet;

	/**
    * @access private
    * @var int(11)
    */
	private $effet2;

	/**
    * @access private
    * @var int(11)
    */
	private $id_perso;

	/**
    * @access private
    * @var int(11)
    */
	private $fin;

	/**
    * @access private
    * @var int(10)
    */
	private $duree;

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
    * @var binary(1)
    */
	private $debuff;

	/**
    * @access private
    * @var tinyint(1)
    */
	private $supprimable;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param varchar(50) type attribut
	* @param float effet attribut
	* @param int(11) effet2 attribut
	* @param int(11) id_perso attribut
	* @param int(11) fin attribut
	* @param int(10) duree attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param binary(1) debuff attribut
	* @param tinyint(1) supprimable attribut
	* @return none
	*/
	function __construct($id = 0, $type = '', $effet = '', $effet2 = '', $id_perso = '', $fin = '', $duree = '', $nom = '', $description = '', $debuff = '', $supprimable = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT type, effet, effet2, id_perso, fin, duree, nom, description, debuff, supprimable FROM buff WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->type, $this->effet, $this->effet2, $this->id_perso, $this->fin, $this->duree, $this->nom, $this->description, $this->debuff, $this->supprimable) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->type = $id['type'];
			$this->effet = $id['effet'];
			$this->effet2 = $id['effet2'];
			$this->id_perso = $id['id_perso'];
			$this->fin = $id['fin'];
			$this->duree = $id['duree'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->debuff = $id['debuff'];
			$this->supprimable = $id['supprimable'];
			}
		else
		{
			$this->type = $type;
			$this->effet = $effet;
			$this->effet2 = $effet2;
			$this->id_perso = $id_perso;
			$this->fin = $fin;
			$this->duree = $duree;
			$this->nom = $nom;
			$this->description = $description;
			$this->debuff = $debuff;
			$this->supprimable = $supprimable;
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
				if($force) $champs = 'type = "'.mysql_escape_string($this->type).'", effet = "'.mysql_escape_string($this->effet).'", effet2 = "'.mysql_escape_string($this->effet2).'", id_perso = "'.mysql_escape_string($this->id_perso).'", fin = "'.mysql_escape_string($this->fin).'", duree = "'.mysql_escape_string($this->duree).'", nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", debuff = "'.mysql_escape_string($this->debuff).'", supprimable = "'.mysql_escape_string($this->supprimable).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE buff SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO buff (type, effet, effet2, id_perso, fin, duree, nom, description, debuff, supprimable) VALUES(';
			$requete .= '"'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->effet).'", "'.mysql_escape_string($this->effet2).'", "'.mysql_escape_string($this->id_perso).'", "'.mysql_escape_string($this->fin).'", "'.mysql_escape_string($this->duree).'", "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->debuff).'", "'.mysql_escape_string($this->supprimable).'")';
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
			$requete = 'DELETE FROM buff WHERE id = '.$this->id;
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

		$requete = "SELECT id, type, effet, effet2, id_perso, fin, duree, nom, description, debuff, supprimable FROM buff WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new buff($row);
				else $return[$row[$keys]][] = new buff($row);
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
		return 'id = '.$this->id.', type = '.$this->type.', effet = '.$this->effet.', effet2 = '.$this->effet2.', id_perso = '.$this->id_perso.', fin = '.$this->fin.', duree = '.$this->duree.', nom = '.$this->nom.', description = '.$this->description.', debuff = '.$this->debuff.', supprimable = '.$this->supprimable;
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
	* @return float $effet valeur de l'attribut effet
	*/
	function get_effet()
	{
		return $this->effet;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $effet2 valeur de l'attribut effet2
	*/
	function get_effet2()
	{
		return $this->effet2;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $id_perso valeur de l'attribut id_perso
	*/
	function get_id_perso()
	{
		return $this->id_perso;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $fin valeur de l'attribut fin
	*/
	function get_fin()
	{
		return $this->fin;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $duree valeur de l'attribut duree
	*/
	function get_duree()
	{
		return $this->duree;
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
	* @return binary(1) $debuff valeur de l'attribut debuff
	*/
	function get_debuff()
	{
		return $this->debuff;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(1) $supprimable valeur de l'attribut supprimable
	*/
	function get_supprimable()
	{
		return $this->supprimable;
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
	* @param float $effet valeur de l'attribut
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
	* @param int(11) $effet2 valeur de l'attribut
	* @return none
	*/
	function set_effet2($effet2)
	{
		$this->effet2 = $effet2;
		$this->champs_modif[] = 'effet2';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $id_perso valeur de l'attribut
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
	* @param int(11) $fin valeur de l'attribut
	* @return none
	*/
	function set_fin($fin)
	{
		$this->fin = $fin;
		$this->champs_modif[] = 'fin';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $duree valeur de l'attribut
	* @return none
	*/
	function set_duree($duree)
	{
		$this->duree = $duree;
		$this->champs_modif[] = 'duree';
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
	* @param binary(1) $debuff valeur de l'attribut
	* @return none
	*/
	function set_debuff($debuff)
	{
		$this->debuff = $debuff;
		$this->champs_modif[] = 'debuff';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(1) $supprimable valeur de l'attribut
	* @return none
	*/
	function set_supprimable($supprimable)
	{
		$this->supprimable = $supprimable;
		$this->champs_modif[] = 'supprimable';
	}

		//fonction
}
?>
