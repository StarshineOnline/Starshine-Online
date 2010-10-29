<?php
class pet extends map_monstre
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
	private $id_joueur;

	/**
    * @access private
    * @var int(10)
    */
	private $id_monstre;

	/**
    * @access private
    * @var varchar(100)
    */
	private $nom;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $hp;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $principale;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $ecurie;
	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_joueur attribut
	* @param int(10) id_monstre attribut
	* @param varchar(100) nom attribut
	* @param mediumint(8) hp attribut
	* @param mediumint(8) mp attribut
	* @param tinyint(3) principale attribut
	* @return none
	*/
	function __construct($id = 0, $id_joueur = 0, $id_monstre = 0, $nom = '', $hp = '', $mp = '', $principale = '', $ecurie = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_joueur, id_monstre, nom, hp, mp, principale FROM pet WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_joueur, $this->id_monstre, $this->nom, $this->hp, $this->mp, $this->principale, $this->ecurie) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_joueur = $id['id_joueur'];
			$this->id_monstre = $id['id_monstre'];
			$this->nom = $id['nom'];
			$this->hp = $id['hp'];
			$this->mp = $id['mp'];
			$this->principale = $id['principale'];
			$this->ecurie = $id['ecurie'];
			}
		else
		{
			$this->id_joueur = $id_joueur;
			$this->id_monstre = $id_monstre;
			$this->nom = $nom;
			$this->hp = $hp;
			$this->mp = $mp;
			$this->principale = $principale;
			$this->ecurie = $ecurie;
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
				if($force) $champs = 'id_joueur = '.$this->id_joueur.', id_monstre = '.$this->id_monstre.', nom = "'.mysql_escape_string($this->nom).'", hp = "'.mysql_escape_string($this->hp).'", mp = "'.mysql_escape_string($this->mp).'", principale = "'.mysql_escape_string($this->principale).'", ecurie = "'.mysql_escape_string($this->ecurie).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE pet SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO pet (id_joueur, id_monstre, nom, hp, mp, principale, ecurie) VALUES(';
			$requete .= ''.$this->id_joueur.', '.$this->id_monstre.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->principale).'", "'.mysql_escape_string($this->ecurie).'")';
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
			$requete = 'DELETE FROM pet WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
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

		$requete = "SELECT id, id_joueur, id_monstre, nom, hp, mp, principale, ecurie FROM pet WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new pet($row);
				else $return[$row[$keys]] = new pet($row);
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
		return 'id = '.$this->id.', id_joueur = '.$this->id_joueur.', id_monstre = '.$this->id_monstre.', nom = '.$this->nom.', hp = '.$this->hp.', mp = '.$this->mp.', principale = '.$this->principale.', ecurie = '.$this->ecurie;
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
	* @return int(10) $id_joueur valeur de l'attribut id_joueur
	*/
	function get_id_joueur()
	{
		return $this->id_joueur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_monstre valeur de l'attribut id_monstre
	*/
	function get_id_monstre()
	{
		return $this->id_monstre;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(100) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
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
	* @return mediumint(8) $mp valeur de l'attribut mp
	*/
	function get_mp()
	{
		return $this->mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $principale valeur de l'attribut principale
	*/
	function get_principale()
	{
		return $this->principale;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $principale valeur de l'attribut ecurie
	*/
	function get_ecurie()
	{
		return $this->ecurie;
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
	* @param int(10) $id_joueur valeur de l'attribut
	* @return none
	*/
	function set_id_joueur($id_joueur)
	{
		$this->id_joueur = $id_joueur;
		$this->champs_modif[] = 'id_joueur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_monstre valeur de l'attribut
	* @return none
	*/
	function set_id_monstre($id_monstre)
	{
		$this->id_monstre = $id_monstre;
		$this->champs_modif[] = 'id_monstre';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(100) $nom valeur de l'attribut
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
	* @param mediumint(8) $mp valeur de l'attribut
	* @return none
	*/
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $principale valeur de l'attribut
	* @return none
	*/
	function set_principale($principale)
	{
		$this->principale = $principale;
		$this->champs_modif[] = 'principale';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $ecurie valeur de l'attribut
	* @return none
	*/
	function set_ecurie($ecurie)
	{
		$this->ecurie = $ecurie;
		$this->champs_modif[] = 'ecurie';
	}

	//fonction
	function get_monstre()
	{
		if(!isset($this->monstre)) $this->monstre = new monstre($this->get_id_monstre());
		return $this->monstre;
	}

	function get_mp_max()
	{
		$this->get_monstre();
		return $this->monstre->get_energie() * 10;
	}

	function get_cout_soin()
	{
		$this->get_monstre();
		return pow($this->monstre->get_level(), 2);
	}

	function get_cout_rez()
	{
		$this->get_monstre();
		return pow($this->monstre->get_level(), 2) * 20;
	}

	function get_cout_depot()
	{
		$this->get_monstre();
		return pow($this->monstre->get_level(), 2);
	}
}
?>
