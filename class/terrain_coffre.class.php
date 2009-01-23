<?php
class terrain_coffre
{
	public $id;
	public $id_coffre;
	public $objet;
	
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_coffre() qui construit un etat "vide".
		*		-terrain_coffre($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_coffre($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_coffre = 0, $objet = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_coffre, objet FROM terrain_coffre WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_coffre, $this->objet) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_coffre = $id['id_coffre'];
			$this->objet = $id['objet'];
		}
		else
		{
			$this->id_coffre = $id_coffre;
			$this->objet = $objet;
			$this->id = $id;
		}
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain_coffre SET ';
			$requete .= 'id_coffre = '.$this->id_coffre.', objet = "'.$this->objet.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_coffre (id_coffre, objet) VALUES(';
			$requete .= $this->id_coffre.', "'.$this->objet.'")';
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
			$requete = 'DELETE FROM terrain_coffre WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_coffre = '.$this->id_coffre.', objet = '.$this->objet;
	}
}
?>