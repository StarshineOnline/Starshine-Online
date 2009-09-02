<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class grade
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
    * @var tinyint(4)
    */
	private $facteur;

	/**
    * @access private
    * @var int(11)
    */
	private $honneur;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $rang;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param varchar(50) nom attribut
	* @param tinyint(4) facteur attribut
	* @param int(11) honneur attribut
	* @param tinyint(3) rang attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $facteur = '', $honneur = '', $rang = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, facteur, honneur, rang FROM grade WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->facteur, $this->honneur, $this->rang) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->facteur = $id['facteur'];
			$this->honneur = $id['honneur'];
			$this->rang = $id['rang'];
			}
		else
		{
			$this->nom = $nom;
			$this->facteur = $facteur;
			$this->honneur = $honneur;
			$this->rang = $rang;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", facteur = "'.mysql_escape_string($this->facteur).'", honneur = "'.mysql_escape_string($this->honneur).'", rang = "'.mysql_escape_string($this->rang).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE grade SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO grade (nom, facteur, honneur, rang) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->facteur).'", "'.mysql_escape_string($this->honneur).'", "'.mysql_escape_string($this->rang).'")';
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
			$requete = 'DELETE FROM grade WHERE id = '.$this->id;
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

		$requete = "SELECT id, nom, facteur, honneur, rang FROM grade WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new grade($row);
				else $return[$row[$keys]][] = new grade($row);
			}
		}
		else $return = false;
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
		return 'id = '.$this->id.', nom = '.$this->nom.', facteur = '.$this->facteur.', honneur = '.$this->honneur.', rang = '.$this->rang;
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
	* @return tinyint(4) $facteur valeur de l'attribut facteur
	*/
	function get_facteur()
	{
		return $this->facteur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $honneur valeur de l'attribut honneur
	*/
	function get_honneur()
	{
		return $this->honneur;
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
	* @param tinyint(4) $facteur valeur de l'attribut
	* @return none
	*/
	function set_facteur($facteur)
	{
		$this->facteur = $facteur;
		$this->champs_modif[] = 'facteur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $honneur valeur de l'attribut
	* @return none
	*/
	function set_honneur($honneur)
	{
		$this->honneur = $honneur;
		$this->champs_modif[] = 'honneur';
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

		//fonction
}
?>
