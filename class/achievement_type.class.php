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
	
	/**	
		*	Constructeur permettant la création d'un achievement.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-achievement_type() qui construit un etat "vide".
		*		-achievement_type($id) qui va chercher l'etat dont l'id est $id
		*		-achievement_type($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '', $value = 0, $variable = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, value, variable FROM achievement_type WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->value, $this->variable) = $db->read_row($requeteSQL);
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
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->description = $description;
			$this->value = $value;
			$this->variable = $variable;
		}
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE achievement_type SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'", value = '.$this->value.', variable = '.$this->variable.'';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO achievement_type (nom, description, value, variable) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'", "'.$this->value.'", "'.$this->variable.'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM achievement_type WHERE id = '.$this->id;
			$db->query($requete);
		}
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
	
	function set_nom($nom)
	{
		$this->nom = $nom;
	}
	
	function get_description()
	{
		return $this->description;
	}
	
	function set_description($description)
	{
		$this->description = $description;
	}
	
	function get_value()
	{
		return $this->value;
	}
	
	function set_value($value)
	{
		$this->value = $value;
	}
	
	function get_variable()
	{
		return $this->variable;
	}
	
	function set_variable($variable)
	{
		$this->variable = $variable;
	}
}
?>