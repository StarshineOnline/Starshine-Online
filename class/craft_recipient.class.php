class craft_recipient{
	public $id;
	public $id_recette;
	public $type;
		
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_recipient() qui construit un etat "vide".
		*		-craft_recipient($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recipient($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_recette = 0, $type = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_recette, type FROM craft_recipient WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_recette, $this->type) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_recette = $id['id_recette'];
			$this->type = $id['type'];
		}
		else
		{
			$this->id_recette = $id_recette;
			$this->type = $type;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recipient SET ';
			$requete .= 'id_recette = '.$this->id_recette.', type = "'.$this->type.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recipient (id_recette, type) VALUES(';
			$requete .= $this->id_recette.', "'.$this->type.'")';
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
			$requete = 'DELETE FROM craft_recipient WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', '.id_recette = '.$this->id_recette.', type = '.$this->type;
	}
}
