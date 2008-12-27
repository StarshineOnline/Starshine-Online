<?php
class craft_ingredient
{
	public $id;
	public $nom;
	public $description;

	/**	
		*	Constructeur permettant la création d'un ingredient.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_ingredient() qui construit un etat "vide".
		*		-craft_ingredient($id) qui va chercher l'etat dont l'id est $id
		*		-craft_ingredient($array) qui associe les chasecrets de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description FROM craft_ingredient WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description) = $db->read_row($requeteSQL);
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
		}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_ingredient SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_ingredient (nom, description) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'")';
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
			$requete = 'DELETE FROM craft_ingredient WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}
}
?>