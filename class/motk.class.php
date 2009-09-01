<?php
if (file_exists('../root.php'))
  include_once('../root.php');

class motk
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
	private $race;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $id_royaume;

	/**
    * @access private
    * @var text
    */
	private $message;

	/**
    * @access private
    * @var text
    */
	private $propagande;

	/**
    * @access private
    * @var int(10)
    */
	private $date;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param varchar(50) race attribut
	* @param tinyint(3) id_royaume attribut
	* @param text message attribut
	* @param text propagande attribut
	* @param int(10) date attribut
	* @return none
	*/
	function __construct($id = 0, $race = '', $id_royaume = '', $message = '', $propagande = '', $date = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT race, id_royaume, message, propagande, date FROM motk WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->race, $this->id_royaume, $this->message, $this->propagande, $this->date) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->race = $id['race'];
			$this->id_royaume = $id['id_royaume'];
			$this->message = $id['message'];
			$this->propagande = $id['propagande'];
			$this->date = $id['date'];
			}
		else
		{
			$this->race = $race;
			$this->id_royaume = $id_royaume;
			$this->message = $message;
			$this->propagande = $propagande;
			$this->date = $date;
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
				if($force) $champs = 'race = "'.mysql_escape_string($this->race).'", id_royaume = "'.mysql_escape_string($this->id_royaume).'", message = "'.mysql_escape_string($this->message).'", propagande = "'.mysql_escape_string($this->propagande).'", date = "'.mysql_escape_string($this->date).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE motk SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO motk (race, id_royaume, message, propagande, date) VALUES(';
			$requete .= '"'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->id_royaume).'", "'.mysql_escape_string($this->message).'", "'.mysql_escape_string($this->propagande).'", "'.mysql_escape_string($this->date).'")';
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
			$requete = 'DELETE FROM motk WHERE id = '.$this->id;
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

		$requete = "SELECT id, race, id_royaume, message, propagande, date FROM motk WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new motk($row);
				else $return[$row[$keys]][] = new motk($row);
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
		return 'id = '.$this->id.', race = '.$this->race.', id_royaume = '.$this->id_royaume.', message = '.$this->message.', propagande = '.$this->propagande.', date = '.$this->date;
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
	* @return varchar(50) $race valeur de l'attribut race
	*/
	function get_race()
	{
		return $this->race;
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
	* @return text $message valeur de l'attribut message
	*/
	function get_message()
	{
		return $this->message;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $propagande valeur de l'attribut propagande
	*/
	function get_propagande()
	{
		return $this->propagande;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $date valeur de l'attribut date
	*/
	function get_date()
	{
		return $this->date;
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
	* @param varchar(50) $race valeur de l'attribut
	* @return none
	*/
	function set_race($race)
	{
		$this->race = $race;
		$this->champs_modif[] = 'race';
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
	* @param text $message valeur de l'attribut
	* @return none
	*/
	function set_message($message)
	{
		$this->message = $message;
		$this->champs_modif[] = 'message';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $propagande valeur de l'attribut
	* @return none
	*/
	function set_propagande($propagande)
	{
		$this->propagande = $propagande;
		$this->champs_modif[] = 'propagande';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $date valeur de l'attribut
	* @return none
	*/
	function set_date($date)
	{
		$this->date = $date;
		$this->champs_modif[] = 'date';
	}

		//fonction
}
?>
