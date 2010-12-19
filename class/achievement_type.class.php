<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class achievement_type
{
	private $id;
	private $nom;
	private $description;
	private $value;
	private $variable;
	private $secret;
	
	/**	
		*	Constructeur permettant la création d'un achievement.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-achievement_type() qui construit un etat "vide".
		*		-achievement_type($id) qui va chercher l'etat dont l'id est $id
		*		-achievement_type($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '', $value = 0, $variable = '', $secret = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, value, variable, secret FROM achievement_type WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->value, $this->variable, $this->secret) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->value = $id['value'];
			$this->variable = $id['variable'];
			$this->secret = $id['secret'];
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->description = $description;
			$this->value = $value;
			$this->variable = $variable;
			$this->secret = $secret;
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

		$requete = "SELECT id, nom, description, value, variable, secret FROM achievement_type WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new achievement_type($row);
				else $return[$row[$keys]] = new achievement_type($row);
			}
		}
		else $return = array();
		return $return;
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_nom()
	{
		return $this->nom;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_description()
	{
		return $this->description;
	}
	
	function get_value()
	{
		return $this->value;
	}
	
	function get_variable()
	{
		return $this->variable;
	}
	
	function get_secret()
	{
		return $this->secret;
	}
}
?>