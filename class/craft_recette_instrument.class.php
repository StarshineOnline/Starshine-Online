<?php
class craft_recette_instrument
{
	public $id;
	public $id_recette;
	public $id_instrument;

	/**	
		*	Constructeur permettant la création d'un ingredient.
		*	Les valeurs id_recettebrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-craft_recette_instrument() qui construit un etat "vide".
		*		-craft_recette_instrument($id) qui va chercher l'etat dont l'id est $id
		*		-craft_recette_instrument($array) qui associe les chasecrets de $array à l'objet.
	**/
	function __construct($id = 0, $id_recette = 0, $id_instrument = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_recette, id_instrument FROM craft_recette_instrument WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_recette, $this->id_instrument) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_recette = $id['id_recette'];
			$this->id_instrument = $id['id_instrument'];
		}
		else
		{
			$this->id_recette = $id_recette;
			$this->id_instrument = $id_instrument;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE craft_recette_instrument SET ';
			$requete .= 'id_recette = '.$this->id_recette.', id_instrument = '.$this->id_instrument;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO craft_recette_instrument (id_recette, id_instrument) VALUES(';
			$requete .= $this->id_recette.', '.$this->id_instrument.')';
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
			$requete = 'DELETE FROM craft_recette_instrument WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}
}
?>