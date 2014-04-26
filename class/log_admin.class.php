<?php
class log_admin_db

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
    * @var varchar(30)
    */
	private $type;

	/**
    * @access private
    * @var text
    */
	private $message;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(10) id_joueur attribut
	* @param varchar(30) type attribut
	* @param text message attribut
	* @return none
	*/
	function __construct($id = 0, $id_joueur = 0, $type = '', $message = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_joueur, type, message, date FROM log_admin WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_joueur, $this->type, $this->message) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_joueur = $id['id_joueur'];
			$this->type = $id['type'];
			$this->message = $id['message'];
			$this->date = $id['date'];
			}
		else
		{
			$this->id_joueur = $id_joueur;
			$this->type = $type;
			$this->message = $message;
			$this->date = time();
			$this->id = $id;
		}
	}

	/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false, $debug = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'id_joueur = '.$this->id_joueur.', type = "'.mysql_escape_string($this->type).'", message = "'.mysql_escape_string($this->message).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE log_admin SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				if($debug) echo $requete.';';
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO log_admin (id_joueur, type, message, date) VALUES(';
			$requete .= ''.$this->id_joueur.', "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->message).'", NOW())';
			if($debug) echo $requete.';';
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
			$requete = 'DELETE FROM log_admin WHERE id = '.$this->id;
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

		$requete = "SELECT id, id_joueur, type, message, date FROM log_admin WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new log_admin_db($row);
				else $return[$row[$keys]] = new log_admin_db($row);
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
		return 'id = '.$this->id.', id_joueur = '.$this->id_joueur.', type = '.$this->type.', message = '.$this->message;
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
	* @return varchar(30) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
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
	* @param varchar(30) $type valeur de l'attribut
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
	* @param text $message valeur de l'attribut
	* @return none
	*/
	function set_message($message)
	{
		$this->message = $message;
		$this->champs_modif[] = 'message';
	}

}

class log_admin extends log_admin_db {
  function __construct($id = 0, $id_joueur = 0, $type = '', $message = '') {
    if( (func_num_args() == 1) && (
         is_numeric($id) || is_array($id)))
      parent::__construct($id);
    else
      parent::__construct($id, $id_joueur, $type, $message);
  }


  //fonction
  function send($id_perso, $type, $message)
  {
	  $this->set_id_joueur($id_perso);
	  $this->set_type($type);
	  $this->set_message($message);
	  $this->sauver();
  }

  static function log($type, $message, $backtrace=false)
  {
    $log = new log_admin();
    if( $backtrace )
      $message .= self::format_backtrace();
    $log->send(array_key_exists('ID', $_SESSION)?$_SESSION['ID']:0, $type, $message);
  }

  static function format_backtrace()
  {
    $backtrace = debug_backtrace();
    $res = '';
    foreach( $backtrace as $bt )
    {
      if( $bt['function'] == 'format_backtrace' or $bt['function'] == 'log' )
        continue;
      $res .= "\n".$bt['function'].'(';
      $i = 0;
      foreach( $bt['args'] as $arg )
      {
        $res .= ($i ? ', ' : '').var_export($arg, true);
        $i++;
      }
      $res .= ') - '.$bt['file'].' : '.$bt['line'];
    }
    return $res;
  }

	static function display_all($where = false, $limit = false, $table = false)
	{
		global $db;
		// left join pour les id_perso 0 (journalier)
		$requete = 'SELECT l.id, l.id_joueur, p.nom, l.type, l.message, l.date FROM log_admin l LEFT JOIN perso p ON l.id_joueur = p.id';
		if ($where != false) 
			$requete .= " AND $where ";
		$requete .= ' ORDER BY l.date DESC ';
		if ($limit != false) 
			$requete .= " LIMIT $limit";
		$req = $db->query($requete);
		if ($table)
			$out = '<table id="'.$table.'"><thead><tr><th>Date</th><th>Nom</th><th>Type</th><th>Message</th></tr></thead><tbody>';
		else
			$out = '<ul>';
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				// TODO: fonctions diverses ...
				if ($row['nom'] == null) $row['nom'] = '<em>'.$row['type'].'</em>';
				if ($table)
					$out .= "<tr id=\"id$row[id]\"><td><small>$row[date]</small></td><td>$row[nom]</td><td>$row[type]</td><td>$row[message]</td></tr>";
				else
					$out .= "<li>$row[nom] ($row[date]|$row[type]): $row[message]</li>";
			}
		} else {
			$out .= 'Aucun';
		}
		if ($table)
			$out .= '</tbody></table>';
		else
			$out .= '</ul>';
		return $out;
	}

}
?>
