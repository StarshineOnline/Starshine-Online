<?php
class construction_ville
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
	private $id_royaume;

	/**
    * @access private
    * @var int(10)
    */
	private $id_batiment;

	/**
    * @access private
    * @var enum('actif','inactif')
    */
	private $statut;

	/**
    * @access private
    * @var int(10)
    */
	private $dette;

	/**
    * @access private
    * @var int(10)
    */
	private $hp;
	protected $date;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param tinyint(3) id_royaume attribut
	* @param int(10) id_batiment attribut
	* @param enum('actif','inactif') statut attribut
	* @param int(10) dette attribut
	* @param int(10) hp attribut
	* @return none
	*/
	function __construct($id = 0, $id_royaume = 0, $id_batiment = 0, $statut = 0, $dette = 0, $hp = 0, $date=0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_royaume, id_batiment, statut, dette, hp FROM construction_ville WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->id_batiment, $this->statut, $this->dette, $this->hp) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['id_royaume'];
			$this->id_batiment = $id['id_batiment'];
			$this->statut = $id['statut'];
			$this->dette = $id['dette'];
			$this->hp = $id['hp'];
			$this->date = $id['date'];
			}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->id_batiment = $id_batiment;
			$this->statut = $statut;
			$this->dette = $dette;
			$this->hp = $hp;
			$this->id = $id;
			$this->date = $date;
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
				if($force) $champs = 'id_royaume = '.$this->id_royaume.', id_batiment = '.$this->id_batiment.', statut = '.$this->statut.', dette = '.$this->dette.', hp = '.$this->hp.'';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE construction_ville SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO construction_ville (id_royaume, id_batiment, statut, dette, hp) VALUES(';
			$requete .= ''.$this->id_royaume.', '.$this->id_batiment.', '.$this->statut.', '.$this->dette.', '.$this->hp.')';
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
			$requete = 'DELETE FROM construction_ville WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_royaume, id_batiment, statut, dette, hp FROM construction_ville WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new construction_ville($row);
				else $return[$row[$keys]][] = new construction_ville($row);
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
		return 'id = '.$this->id.', id_royaume = '.$this->id_royaume.', id_batiment = '.$this->id_batiment.', statut = '.$this->statut.', dette = '.$this->dette.', hp = '.$this->hp;
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
	* @return tinyint(3) $id_royaume valeur de l'attribut id_royaume
	*/
	function get_id_royaume()
	{
		return $this->id_royaume;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_batiment valeur de l'attribut id_batiment
	*/
	function get_id_batiment()
	{
		return $this->id_batiment;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('actif','inactif') $statut valeur de l'attribut statut
	*/
	function get_statut()
	{
		return $this->statut;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $dette valeur de l'attribut dette
	*/
	function get_dette()
	{
		return $this->dette;
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
	* @param tinyint(3) $id_royaume valeur de l'attribut
	* @return none
	*/
	function set_id_royaume($id_royaume)
	{
		$this->id_royaume = $id_royaume;
		$this->champs_modif[] = 'id_royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_batiment valeur de l'attribut
	* @return none
	*/
	function set_id_batiment($id_batiment)
	{
		$this->id_batiment = $id_batiment;
		$this->champs_modif[] = 'id_batiment';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('actif','inactif') $statut valeur de l'attribut
	* @return none
	*/
	function set_statut($statut)
	{
		$this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $dette valeur de l'attribut
	* @return none
	*/
	function set_dette($dette)
	{
		$this->dette = $dette;
		$this->champs_modif[] = 'dette';
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
	function get_batiment_level($level)
	{
    global $db;
    $query = "select * from batiment_ville where level = $level and type = ".
      "(select type from batiment_ville where id = $this->id_batiment )";
    $res = $db->query($query);
    if ($res && $db->num_rows($res))
      return new batiment_ville($db->read_array($res));
		else return false;
	}

	function get_batiment_inferieur()
	{
		if($this->get_level() > 1)
      return $this->get_batiment_level($this->get_level() - 1);
		else return false;
	}

	function get_batiment_superieur()
	{
		return $this->get_batiment_level($this->get_level() + 1);
	}

	function suppr_hp($degat)
	{
		$this->set_hp($this->hp - $degat);
		//La construction n'a plus de vie, soit on la réduit d'un rang, soit on la rend inactive
		if($this->hp <= 0)
		{
			//Réduction de level
			if($this->get_level() > 1)
			{
				$batiment = $this->get_batiment_inferieur();
				$this->set_id_batiment($batiment->get_id());
				$this->set_hp($batiment->get_hp() + $this->hp);
				$this->level = $batiment->get_level();
				//Points de victoire
				$return = ($batiment->get_level() * $batiment->get_level());
			}
			else
			{
				$this->set_statut('inactif');
				//Points de victoire
				$return = 1;
			}
		}
		else $return = 0;
		$this->sauver();
		return $return;
	}

  function get_level()
  {
    if (!isset($this->level))
    {
      global $db;
			$requete = 'SELECT level from batiment_ville WHERE id = '.
        $this->id_batiment;
			$req = $db->query($requete);
      if ($row = $db->read_assoc($req))
        $this->level = $row['level'];
    }
    return $this->level;
  }
  
	function get_date()
	{
		return $this->date;
	}
	function set_date($date)
	{
		$this->date = $date;
		$this->champs_modif[] = 'date';
	}
}
?>
