<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class achievement
{
	private $id;
	private $id_perso;
	private $id_achiev;
	
	/**	
		*	Constructeur permettant la création d'un achievement.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-achievement() qui construit un etat "vide".
		*		-achievement($id) qui va chercher l'etat dont l'id est $id
		*		-achievement($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_perso = 0, $id_achiev = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_perso, id_achiev FROM achievement WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_perso, $this->id_achiev) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_perso = $id['id_perso'];
			$this->id_achiev = $id['id_achiev'];
		}
		else
		{
			$this->id = $id;
			$this->id_perso = $id_perso;
			$this->id_achiev = $id_achiev;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE achievement SET ';
			$requete .= 'id_perso = "'.$this->id_perso.'", id_achiev = "'.$this->id_achiev.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO achievement (id_perso, id_achiev) VALUES(';
			$requete .= '"'.$this->id_perso.'", "'.$this->id_achiev.'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}
	
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
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
		
		$requete = "SELECT id, id_perso, id_achiev FROM achievement WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new achievement($row);
				else $return[$row[$keys]] = new achievement($row);
			}
		}
		else $return = array();
		return $return;
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM achievement WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_id_achiev()
	{
		return $this->id_achiev;
	}
	
	function set_id_achiev($id_achiev)
	{
		$this->id_achiev = $id_achiev;
	}
	
	function get_id_perso()
	{
		return $this->id_perso;
	}
	
	function set_id_perso($id_perso)
	{
		$this->id_perso = $id_perso;
	}
}
?>