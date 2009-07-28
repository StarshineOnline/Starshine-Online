<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class groupe_joueur
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
	private $id_groupe;

	/**
    * @access private
    * @var enum('y','n')
    */
	private $leader;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_joueur attribut
	* @param int(10) id_groupe attribut
	* @param enum('y','n') leader attribut
	* @return none
	*/
	function __construct($id = 0, $id_joueur = 0, $id_groupe = 0, $leader = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_joueur, id_groupe, leader FROM groupe_joueur WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_joueur, $this->id_groupe, $this->leader) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_joueur = $id['id_joueur'];
			$this->id_groupe = $id['id_groupe'];
			$this->leader = $id['leader'];
			}
		else
		{
			$this->id_joueur = $id_joueur;
			$this->id_groupe = $id_groupe;
			$this->leader = $leader;
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
				if($force) $champs = 'id_joueur = '.$this->id_joueur.', id_groupe = '.$this->id_groupe.', leader = '.$this->leader.'';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE groupe_joueur SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO groupe_joueur (id_joueur, id_groupe, leader) VALUES(';
			$requete .= ''.$this->id_joueur.', '.$this->id_groupe.', '.$this->leader.')';
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
			$requete = 'DELETE FROM groupe_joueur WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_joueur, id_groupe, leader FROM groupe_joueur WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new groupe_joueur($row);
				else $return[$row[$keys]][] = new groupe_joueur($row);
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
		return 'id = '.$this->id.', id_joueur = '.$this->id_joueur.', id_groupe = '.$this->id_groupe.', leader = '.$this->leader;
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
	* @return int(10) $id_groupe valeur de l'attribut id_groupe
	*/
	function get_id_groupe()
	{
		return $this->id_groupe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('y','n') $leader valeur de l'attribut leader
	*/
	function get_leader()
	{
		return $this->leader;
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
	* @param int(10) $id_groupe valeur de l'attribut
	* @return none
	*/
	function set_id_groupe($id_groupe)
	{
		$this->id_groupe = $id_groupe;
		$this->champs_modif[] = 'id_groupe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('y','n') $leader valeur de l'attribut
	* @return none
	*/
	function set_leader($leader)
	{
		$this->leader = $leader;
		$this->champs_modif[] = 'leader';
	}

		//fonction
}
?>
