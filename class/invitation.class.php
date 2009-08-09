<?php
class invitation
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var int(11)
    */
	private $inviteur;

	/**
    * @access private
    * @var int(11)
    */
	private $receveur;

	/**
    * @access private
    * @var int(11)
    */
	private $time;

	/**
    * @access private
    * @var int(11)
    */
	private $groupe;

	
	/**
	* @access public

	* @param int(10) id attribut
	* @param int(11) inviteur attribut
	* @param int(11) receveur attribut
	* @param int(11) time attribut
	* @param int(11) groupe attribut
	* @return none
	*/
	function __construct($id = 0, $inviteur = 0, $receveur = 0, $time = 0, $groupe = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT inviteur, receveur, time, groupe FROM invitation WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->inviteur, $this->receveur, $this->time, $this->groupe) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->inviteur = $id['inviteur'];
			$this->receveur = $id['receveur'];
			$this->time = $id['time'];
			$this->groupe = $id['groupe'];
			}
		else
		{
			$this->inviteur = $inviteur;
			$this->receveur = $receveur;
			$this->time = $time;
			$this->groupe = $groupe;
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
				if($force) $champs = 'inviteur = '.$this->inviteur.', receveur = '.$this->receveur.', time = '.$this->time.', groupe = '.$this->groupe.'';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					foreach($this->champs_add as $champ)
					{
						$champs[] .= $champ['nom'].' = '.$champ['nom'].' + '.$champ['valeur'];
					}
					foreach($this->champs_del as $champ)
					{
						$champs[] .= $champ['nom'].' = '.$champ['nom'].' - '.$champ['valeur'];
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE invitation SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO invitation (inviteur, receveur, time, groupe) VALUES(';
			$requete .= ''.$this->inviteur.', '.$this->receveur.', '.$this->time.', '.$this->groupe.')';
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
			$requete = 'DELETE FROM invitation WHERE id = '.$this->id;
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

		$requete = "SELECT id, inviteur, receveur, time, groupe FROM invitation WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new invitation($row);
				else $return[$row[$keys]][] = new invitation($row);
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
		return 'id = '.$this->id.', inviteur = '.$this->inviteur.', receveur = '.$this->receveur.', time = '.$this->time.', groupe = '.$this->groupe;
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
	* @return int(11) $inviteur valeur de l'attribut inviteur
	*/
	function get_inviteur()
	{
		return $this->inviteur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $receveur valeur de l'attribut receveur
	*/
	function get_receveur()
	{
		return $this->receveur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $time valeur de l'attribut time
	*/
	function get_time()
	{
		return $this->time;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $groupe valeur de l'attribut groupe
	*/
	function get_groupe()
	{
		return $this->groupe;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id valeur de l'attribut
	* @return none
	*/
	function set_id($id, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->id = $id;
				$this->champs_modif[] = 'id';
			break;
			case 'add' :
				$this->id += $id;
				$this->champs_add[] = array('nom' => 'id', 'valeur' => $id);
			break;
			case 'del' :
				$this->id -= $id;
				$this->champs_del[] = array('nom' => 'id', 'valeur' => $id);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $inviteur valeur de l'attribut
	* @return none
	*/
	function set_inviteur($inviteur, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->inviteur = $inviteur;
				$this->champs_modif[] = 'inviteur';
			break;
			case 'add' :
				$this->inviteur += $inviteur;
				$this->champs_add[] = array('nom' => 'inviteur', 'valeur' => $inviteur);
			break;
			case 'del' :
				$this->inviteur -= $inviteur;
				$this->champs_del[] = array('nom' => 'inviteur', 'valeur' => $inviteur);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $receveur valeur de l'attribut
	* @return none
	*/
	function set_receveur($receveur, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->receveur = $receveur;
				$this->champs_modif[] = 'receveur';
			break;
			case 'add' :
				$this->receveur += $receveur;
				$this->champs_add[] = array('nom' => 'receveur', 'valeur' => $receveur);
			break;
			case 'del' :
				$this->receveur -= $receveur;
				$this->champs_del[] = array('nom' => 'receveur', 'valeur' => $receveur);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $time valeur de l'attribut
	* @return none
	*/
	function set_time($time, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->time = $time;
				$this->champs_modif[] = 'time';
			break;
			case 'add' :
				$this->time += $time;
				$this->champs_add[] = array('nom' => 'time', 'valeur' => $time);
			break;
			case 'del' :
				$this->time -= $time;
				$this->champs_del[] = array('nom' => 'time', 'valeur' => $time);
			break;
		}
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $groupe valeur de l'attribut
	* @return none
	*/
	function set_groupe($groupe, $param = 'set')
	{
		switch($param)
		{
			case 'set' :
				$this->groupe = $groupe;
				$this->champs_modif[] = 'groupe';
			break;
			case 'add' :
				$this->groupe += $groupe;
				$this->champs_add[] = array('nom' => 'groupe', 'valeur' => $groupe);
			break;
			case 'del' :
				$this->groupe -= $groupe;
				$this->champs_del[] = array('nom' => 'groupe', 'valeur' => $groupe);
			break;
		}
	}
}