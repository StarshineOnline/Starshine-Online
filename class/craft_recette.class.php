<?php
class craft_recette
{
	public $id;
	public $nom;
	public $description;
	public $pa;
	public $mp;
	public $type;
	public $difficulte;

	/**	
		*	Constructeur permettant la création d'une recette.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-craft_recette() qui construit un etat "vide".
		*		-craft_recette($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recette($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $nom = '', $description = '', $pa = 0, $mp = 0, $type = '', $difficulte = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT nom, description, pa, mp, type, difficulte FROM craft_recette WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->nom, $this->description, $this->pa, $this->mp, $this->type, $this->difficulte) = $db->read_row($requeteSQL);
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
			$this->pa = $id['pa'];
			$this->mp = $id['mp'];
			$this->type = $id['type'];
			$this->difficulte = $id['difficulte'];
		}
		else
		{
			$this->nom = $nom;
			$this->description = $description;
			$this->pa = $pa;
			$this->mp = $mp;
			$this->type = $type;
			$this->difficulte = $difficulte;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recette SET ';
			$requete .= 'nom = "'.$this->nom.'", description = "'.$this->description.'", pa = '.$this->pa.', mp = '.$this->mp.', type = "'.$this->type.'", difficulte = '.$this->difficulte;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recette (nom, description, pa, mp, type, difficulte) VALUES(';
			$requete .= '"'.$this->nom.'", "'.$this->description.'", '.$this->pa.', '.$this->mp.', "'.$this->type.'", '.$this->difficulte.')';
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
			$requete = 'DELETE FROM craft_recette WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}
}
?>