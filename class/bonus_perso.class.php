<?php
class bonus_perso

{
	const CACHE_GRADE_ID = 6;
	const CACHE_CLASSE_ID = 7;
	const CACHE_STATS_ID = 8;
	const CACHE_NIVEAU_ID = 11;
	
	/**
    * @access private
    * @var int(10)
    */
	private $id_bonus_perso;

	/**
    * @access private
    * @var int(10)
    */
	private $id_perso;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $id_bonus;

	/**
    * @access private
    * @var text
    */
	private $valeur;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $etat;

	
	/**
	* @access public

	* @param int(10) id_bonus_perso attribut
	* @param int(10) id_perso attribut
	* @param tinyint(3) id_bonus attribut
	* @param text valeur attribut
	* @param tinyint(4) etat attribut
	* @return none
	*/
	function __construct($id_bonus_perso = 0, $id_perso = 0, $id_bonus = 0, $valeur = '', $etat = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_bonus_perso) )
		{
			$requeteSQL = $db->query("SELECT id_perso, id_bonus, valeur, etat FROM bonus_perso WHERE id_bonus_perso = ".$id_bonus_perso);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_perso, $this->id_bonus, $this->valeur, $this->etat) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id_bonus_perso = $id_bonus_perso;
		}
		elseif( (func_num_args() == 1) && is_array($id_bonus_perso) )
		{
			$this->id_bonus_perso = $id_bonus_perso['id_bonus_perso'];
			$this->id_perso = $id_bonus_perso['id_perso'];
			$this->id_bonus = $id_bonus_perso['id_bonus'];
			$this->valeur = $id_bonus_perso['valeur'];
			$this->etat = $id_bonus_perso['etat'];
			}
		else
		{
			$this->id_perso = $id_perso;
			$this->id_bonus = $id_bonus;
			$this->valeur = $valeur;
			$this->etat = $etat;
			$this->id_bonus_perso = $id_bonus_perso;
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
		if( $this->id_bonus_perso > 0 )
		{
			if( $force || count($this->champs_modif) > 0 )
			{
				if($force) $champs = 'id_perso = '.$this->id_perso.', id_bonus = '.$this->id_bonus.', valeur = "'.mysql_real_escape_string($this->valeur).'", etat = "'.mysql_real_escape_string($this->etat).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_real_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE bonus_perso SET ';
				$requete .= $champs;
				$requete .= ' WHERE id_bonus_perso = '.$this->id_bonus_perso;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO bonus_perso (id_perso, id_bonus, valeur, etat) VALUES(';
			$requete .= ''.$this->id_perso.', '.$this->id_bonus.', "'.mysql_real_escape_string($this->valeur).'", "'.mysql_real_escape_string($this->etat).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id_bonus_perso = $db->last_insert_id();
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
		if( $this->id_bonus_perso > 0 )
		{
			$requete = 'DELETE FROM bonus_perso WHERE id_bonus_perso = '.$this->id_bonus_perso;
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
	static function create($champs, $valeurs, $ordre = 'id_bonus_perso ASC', $keys = false, $where = false)
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
				$where[] = $champ .' = "'.mysql_real_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = "SELECT id_bonus_perso, id_perso, id_bonus, valeur, etat FROM bonus_perso WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new bonus_perso($row);
				else $return[$row[$keys]] = new bonus_perso($row);
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
		return 'id_bonus_perso = '.$this->id_bonus_perso.', id_perso = '.$this->id_perso.', id_bonus = '.$this->id_bonus.', valeur = '.$this->valeur.', etat = '.$this->etat;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_bonus_perso valeur de l'attribut id_bonus_perso
	*/
	function get_id_bonus_perso()
	{
		return $this->id_bonus_perso;
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
	* @return tinyint(3) $id_bonus valeur de l'attribut id_bonus
	*/
	function get_id_bonus()
	{
		return $this->id_bonus;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $valeur valeur de l'attribut valeur
	*/
	function get_valeur()
	{
		return $this->valeur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $etat valeur de l'attribut etat
	*/
	function get_etat()
	{
		return $this->etat;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_bonus_perso valeur de l'attribut
	* @return none
	*/
	function set_id_bonus_perso($id_bonus_perso)
	{
		$this->id_bonus_perso = $id_bonus_perso;
		$this->champs_modif[] = 'id_bonus_perso';
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
	* @param tinyint(3) $id_bonus valeur de l'attribut
	* @return none
	*/
	function set_id_bonus($id_bonus)
	{
		$this->id_bonus = $id_bonus;
		$this->champs_modif[] = 'id_bonus';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $valeur valeur de l'attribut
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
	* @param tinyint(4) $etat valeur de l'attribut
	* @return none
	*/
	function set_etat($etat)
	{
		$this->etat = $etat;
		$this->champs_modif[] = 'etat';
	}

	//fonction

}
?>
