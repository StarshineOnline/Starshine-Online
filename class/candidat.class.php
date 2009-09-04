<?php
class candidat
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
	private $id_perso;

	/**
    * @access private
    * @var varchar(50)
    */
	private $date;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $royaume;

	/**
    * @access private
    * @var text
    */
	private $programme;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var int(10)
    */
	private $id_election;

	/**
    * @access private
    * @var int(10)
    */
	private $id_ministre_economie;

	/**
    * @access private
    * @var int(10)
    */
	private $id_ministre_militaire;

	/**
    * @access private
    * @var int(10)
    */
	private $duree;

	/**
    * @access private
    * @var enum('universel','nomination')
    */
	private $type;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_perso attribut
	* @param varchar(50) date attribut
	* @param tinyint(3) royaume attribut
	* @param text programme attribut
	* @param varchar(50) nom attribut
	* @param int(10) id_election attribut
	* @param int(10) id_ministre_economie attribut
	* @param int(10) id_ministre_militaire attribut
	* @param int(10) duree attribut
	* @param enum('universel','nomination') type attribut
	* @return none
	*/
	function __construct($id = 0, $id_perso = 0, $date = '', $royaume = '', $programme = '', $nom = '', $id_election = '', $id_ministre_economie = '', $id_ministre_militaire = '', $duree = '', $type = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_perso, date, royaume, programme, nom, id_election, id_ministre_economie, id_ministre_militaire, duree, type FROM candidat WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_perso, $this->date, $this->royaume, $this->programme, $this->nom, $this->id_election, $this->id_ministre_economie, $this->id_ministre_militaire, $this->duree, $this->type) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_perso = $id['id_perso'];
			$this->date = $id['date'];
			$this->royaume = $id['royaume'];
			$this->programme = $id['programme'];
			$this->nom = $id['nom'];
			$this->id_election = $id['id_election'];
			$this->id_ministre_economie = $id['id_ministre_economie'];
			$this->id_ministre_militaire = $id['id_ministre_militaire'];
			$this->duree = $id['duree'];
			$this->type = $id['type'];
			}
		else
		{
			$this->id_perso = $id_perso;
			$this->date = $date;
			$this->royaume = $royaume;
			$this->programme = $programme;
			$this->nom = $nom;
			$this->id_election = $id_election;
			$this->id_ministre_economie = $id_ministre_economie;
			$this->id_ministre_militaire = $id_ministre_militaire;
			$this->duree = $duree;
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
				if($force) $champs = 'id_perso = '.$this->id_perso.', date = "'.mysql_escape_string($this->date).'", royaume = "'.mysql_escape_string($this->royaume).'", programme = "'.mysql_escape_string($this->programme).'", nom = "'.mysql_escape_string($this->nom).'", id_election = "'.mysql_escape_string($this->id_election).'", id_ministre_economie = "'.mysql_escape_string($this->id_ministre_economie).'", id_ministre_militaire = "'.mysql_escape_string($this->id_ministre_militaire).'", duree = "'.mysql_escape_string($this->duree).'", type = "'.mysql_escape_string($this->type).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE candidat SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO candidat (id_perso, date, royaume, programme, nom, id_election, id_ministre_economie, id_ministre_militaire, duree, type) VALUES(';
			$requete .= ''.$this->id_perso.', "'.mysql_escape_string($this->date).'", "'.mysql_escape_string($this->royaume).'", "'.mysql_escape_string($this->programme).'", "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->id_election).'", "'.mysql_escape_string($this->id_ministre_economie).'", "'.mysql_escape_string($this->id_ministre_militaire).'", "'.mysql_escape_string($this->duree).'", "'.mysql_escape_string($this->type).'")';
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
			$requete = 'DELETE FROM candidat WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_perso, date, royaume, programme, nom, id_election, id_ministre_economie, id_ministre_militaire, duree, type FROM candidat WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new candidat($row);
				else $return[$row[$keys]][] = new candidat($row);
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
		return 'id = '.$this->id.', id_perso = '.$this->id_perso.', date = '.$this->date.', royaume = '.$this->royaume.', programme = '.$this->programme.', nom = '.$this->nom.', id_election = '.$this->id_election.', id_ministre_economie = '.$this->id_ministre_economie.', id_ministre_militaire = '.$this->id_ministre_militaire.', duree = '.$this->duree.', type = '.$this->type;
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
	* @return varchar(50) $date valeur de l'attribut date
	*/
	function get_date()
	{
		return $this->date;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $royaume valeur de l'attribut royaume
	*/
	function get_royaume()
	{
		return $this->royaume;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $programme valeur de l'attribut programme
	*/
	function get_programme()
	{
		return $this->programme;
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
	* @return int(10) $id_election valeur de l'attribut id_election
	*/
	function get_id_election()
	{
		return $this->id_election;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_ministre_economie valeur de l'attribut id_ministre_economie
	*/
	function get_id_ministre_economie()
	{
		return $this->id_ministre_economie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_ministre_militaire valeur de l'attribut id_ministre_militaire
	*/
	function get_id_ministre_militaire()
	{
		return $this->id_ministre_militaire;
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
	* @param varchar(50) $date valeur de l'attribut
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
	* @param tinyint(3) $royaume valeur de l'attribut
	* @return none
	*/
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $programme valeur de l'attribut
	* @return none
	*/
	function set_programme($programme)
	{
		$this->programme = $programme;
		$this->champs_modif[] = 'programme';
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
	* @param int(10) $id_election valeur de l'attribut
	* @return none
	*/
	function set_id_election($id_election)
	{
		$this->id_election = $id_election;
		$this->champs_modif[] = 'id_election';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_ministre_economie valeur de l'attribut
	* @return none
	*/
	function set_id_ministre_economie($id_ministre_economie)
	{
		$this->id_ministre_economie = $id_ministre_economie;
		$this->champs_modif[] = 'id_ministre_economie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_ministre_militaire valeur de l'attribut
	* @return none
	*/
	function set_id_ministre_militaire($id_ministre_militaire)
	{
		$this->id_ministre_militaire = $id_ministre_militaire;
		$this->champs_modif[] = 'id_ministre_militaire';
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
	* @param enum('universel','nomination') $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

		//fonction
}
?>
