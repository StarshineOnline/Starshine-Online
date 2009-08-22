<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class map_case
{
/**
    * @access private
    * @var mediumint(9)
    */
	private $id;

	/**
    * @access private
    * @var text
    */
	private $info;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $decor;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $royaume;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $type;

	
	/**
	* @access public

	* @param mediumint(9) id attribut
	* @param text info attribut
	* @param mediumint(9) decor attribut
	* @param tinyint(3) royaume attribut
	* @param tinyint(3) type attribut
	* @return none
	*/
	function __construct($id = 0, $info = '', $decor = '', $royaume = '', $type = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT info, decor, royaume, type FROM map WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->info, $this->decor, $this->royaume, $this->type) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->info = $id['info'];
			$this->decor = $id['decor'];
			$this->royaume = $id['royaume'];
			$this->type = $id['type'];
			}
		else
		{
			$this->info = $info;
			$this->decor = $decor;
			$this->royaume = $royaume;
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
				if($force) $champs = 'info = "'.mysql_escape_string($this->info).'", decor = "'.mysql_escape_string($this->decor).'", royaume = "'.mysql_escape_string($this->royaume).'", type = "'.mysql_escape_string($this->type).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE map SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO map (info, decor, royaume, type) VALUES(';
			$requete .= '"'.mysql_escape_string($this->info).'", "'.mysql_escape_string($this->decor).'", "'.mysql_escape_string($this->royaume).'", "'.mysql_escape_string($this->type).'")';
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
			$requete = 'DELETE FROM map WHERE id = '.$this->id;
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

		$requete = "SELECT id, info, decor, royaume, type FROM map WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows() > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new map($row);
				else $return[$row[$keys]][] = new map($row);
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
		return 'id = '.$this->id.', info = '.$this->info.', decor = '.$this->decor.', royaume = '.$this->royaume.', type = '.$this->type;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $info valeur de l'attribut info
	*/
	function get_info()
	{
		return $this->info;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $decor valeur de l'attribut decor
	*/
	function get_decor()
	{
		return $this->decor;
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
	* @return tinyint(3) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $id valeur de l'attribut
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
	* @param text $info valeur de l'attribut
	* @return none
	*/
	function set_info($info)
	{
		$this->info = $info;
		$this->champs_modif[] = 'info';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $decor valeur de l'attribut
	* @return none
	*/
	function set_decor($decor)
	{
		$this->decor = $decor;
		$this->champs_modif[] = 'decor';
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
	* @param tinyint(3) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	//fonction
	function coord()
	{
		$this->coord = convert_in_coord($this->id);
	}

	function get_x()
	{
		if(!isset($this->coord)) $this->coord();
		return $this->coord['x'];
	}

	function get_y()
	{
		if(!isset($this->coord)) $this->coord();
		return $this->coord['y'];
	}

	/**
	 * Vérifie les modifications de la case.
	 * 
	 */ 
	function check_case($check = false)
	{
		global $db, $Gtrad;
		// Toutes les cases ou seulement une en particulier ?
		if($check == 'all')
		{
			$where = '1';
		}
		else $where = '(x = '.$this->get_x().') AND (y = '.$this->get_y().')';
		// Recherche des constructions terminées
		$requete = "SELECT * FROM placement WHERE ".$where." AND fin_placement <= ".time();
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			//echo time().' > '.$row['fin_placement'];
			//Si c'est un drapeau, on transforme le royaume
			if($row['type'] == 'drapeau')
			{
				$pos = convert_in_pos($row['x'], $row['y']);
				//Mis à jour de la carte
				$requete = "UPDATE map SET royaume = ".$row['royaume']." WHERE ID = ".$pos;
				//echo $requete;
				$db->query($requete);
				//Suppression du drapeau
				$requete = "DELETE FROM placement WHERE id = ".$row['id'];
				$db->query($requete);
			}
			//Si c'est un bâtiment ou une arme de siège, on le construit
			elseif($row['type'] == 'fort' OR $row['type'] == 'tour' OR $row['type'] == 'bourg' OR $row['type'] == 'mur' OR $row['type'] == 'arme_de_siege' OR $row['type'] == 'mine')
			{
				$construction = new construction();
				$construction->set_rechargement(time());
				//Cas spécifique des mines
				if($row['type'] == 'mine')
				{
					$construction->set_rechargement($row['rez']);
					$row['rez'] = 0;
				}
				$construction->set_id_batiment($row['id_batiment']);
				$construction->set_x($row['x']);
				$construction->set_y($row['y']);
				$construction->set_royaume($row['royaume']);
				$construction->set_hp($row['hp']);
				$construction->set_nom($row['nom']);
				$construction->set_type($row['type']);
				$construction->set_rez($row['rez']);
				$construction->set_image($Gtrad[$row['nom']]);
				$construction->set_date_construction(time());
				$construction->set_point_victoire($row['point_victoire']);
				//Insertion de la construction
				$construction->sauver();
				//Suppression du placement
				$requete = "DELETE FROM placement WHERE id = ".$row['id'];
				$db->query($requete);
			}
		}
	}
}
?>
