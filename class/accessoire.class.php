<?php
class accessoire extends objet_equip
{
	private $description;
	private $puissance;
	private $achetable;

	
	/**
	* @access public

	* @param mediumint(8) id attribut
	* @param varchar(50) nom attribut
	* @param text description attribut
	* @param tinyint(3) puissance attribut
	* @param varchar(50) type attribut
	* @param int(10) effet attribut
	* @param int(10) prix attribut
	* @param tinyint(3) lvl_batiment attribut
	* @param enum('y','n') achetable attribut
	* @return none
	*/
	function __construct($id = 0, $nom = '', $description = '', $puissance = '', $type = '', $effet = '', $prix = '', $lvl_batiment = '', $achetable = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT nom, description, puissance, type, effet, prix, lvl_batiment, achetable FROM accessoire WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->puissance, $this->type, $this->effet, $this->prix, $this->lvl_batiment, $this->achetable) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->puissance = $id['puissance'];
			$this->type = $id['type'];
			$this->effet = $id['effet'];
			$this->prix = $id['prix'];
			$this->lvl_batiment = $id['lvl_batiment'];
			$this->achetable = $id['achetable'];
			}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->puissance = $puissance;
			$this->type = $type;
			$this->effet = $effet;
			$this->prix = $prix;
			$this->lvl_batiment = $lvl_batiment;
			$this->achetable = $achetable;
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
				if($force) $champs = 'nom = "'.mysql_escape_string($this->nom).'", description = "'.mysql_escape_string($this->description).'", puissance = "'.mysql_escape_string($this->puissance).'", type = "'.mysql_escape_string($this->type).'", effet = "'.mysql_escape_string($this->effet).'", prix = "'.mysql_escape_string($this->prix).'", lvl_batiment = "'.mysql_escape_string($this->lvl_batiment).'", achetable = "'.mysql_escape_string($this->achetable).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE accessoire SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO accessoire (nom, description, puissance, type, effet, prix, lvl_batiment, achetable) VALUES(';
			$requete .= '"'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->description).'", "'.mysql_escape_string($this->puissance).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->effet).'", "'.mysql_escape_string($this->prix).'", "'.mysql_escape_string($this->lvl_batiment).'", "'.mysql_escape_string($this->achetable).'")';
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
			$requete = 'DELETE FROM accessoire WHERE id = '.$this->id;
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

		$requete = "SELECT id, nom, description, puissance, type, effet, prix, lvl_batiment, achetable FROM accessoire WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new accessoire($row);
				else $return[$row[$keys]][] = new accessoire($row);
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
		return 'id = '.$this->id.', nom = '.$this->nom.', description = '.$this->description.', puissance = '.$this->puissance.', type = '.$this->type.', effet = '.$this->effet.', prix = '.$this->prix.', lvl_batiment = '.$this->lvl_batiment.', achetable = '.$this->achetable;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $id valeur de l'attribut id
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
	* @return tinyint(3) $puissance valeur de l'attribut puissance
	*/
	function get_puissance()
	{
		return $this->puissance;
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
	* @return int(10) $effet valeur de l'attribut effet
	*/
	function get_effet()
	{
		return $this->effet;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $prix valeur de l'attribut prix
	*/
	function get_prix()
	{
		return $this->prix;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $lvl_batiment valeur de l'attribut lvl_batiment
	*/
	function get_lvl_batiment()
	{
		return $this->lvl_batiment;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('y','n') $achetable valeur de l'attribut achetable
	*/
	function get_achetable()
	{
		return $this->achetable;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $id valeur de l'attribut
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
	* @param tinyint(3) $puissance valeur de l'attribut
	* @return none
	*/
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
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
	* @param int(10) $effet valeur de l'attribut
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
	* @param int(10) $prix valeur de l'attribut
	* @return none
	*/
	function set_prix($prix)
	{
		$this->prix = $prix;
		$this->champs_modif[] = 'prix';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $lvl_batiment valeur de l'attribut
	* @return none
	*/
	function set_lvl_batiment($lvl_batiment)
	{
		$this->lvl_batiment = $lvl_batiment;
		$this->champs_modif[] = 'lvl_batiment';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('y','n') $achetable valeur de l'attribut
	* @return none
	*/
	function set_achetable($achetable)
	{
		$this->achetable = $achetable;
		$this->champs_modif[] = 'achetable';
	}

	/**
	    * La fonction prend comme argument un booleen.
	**/
	function setAchetable($bool)
	{
		$this->achetable = $bool ? 'y' : 'n';
	}

	/**
	    *Retourne un booleen
	    *True si achetable est 'y'.
	    *False sinon.
	**/
	function isAchetable()
	{
		return !strcmp($this->achetable, 'y');
	}

	//Infobulle de l'accessoire
	function infobulle()
	{
		$milieu = '<tr><td>Effet:</td><td>'.$this->description.'</td></tr>';
		$milieu .= '<tr><td>Puissance n&eacute;cessaire:</td></tr><tr><td>'.$this->puissance.'</td></tr>';
		return bulleBase($milieu).'<br />';
	}

		//fonction
}
?>
