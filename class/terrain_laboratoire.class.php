<?php
class terrain_laboratoire
{
	public $id;
	public $id_laboratoire;
	public $id_instrument;
	public $type;
	
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_laboratoire() qui construit un etat "vide".
		*		-terrain_laboratoire($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_laboratoire($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_laboratoire = 0, $id_instrument = 0, $type = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_laboratoire, $this->id_instrument, $this->type) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_laboratoire = $id['id_laboratoire'];
			$this->id_instrument = $id['id_instrument'];
			$this->type = $id['type'];
					}
		else
		{
			$this->id_laboratoire = $id_laboratoire;
			$this->id_instrument = $id_instrument;
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
			$requete = 'UPDATE terrain_laboratoire SET ';
			$requete .= 'id_laboratoire = '.$this->id_laboratoire.', id_instrument = '.$this->id_instrument.', type = "'.$this->type.'"';
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_laboratoire (id_laboratoire, id_instrument, type) VALUES(';
			$requete .= $this->id_laboratoire.', '.$this->id_instrument.', "'.$this->type.'")';
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
			$requete = 'DELETE FROM terrain_laboratoire WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_laboratoire = '.$this->id_laboratoire.', id_instrument = '.$this->id_instrument.', type = '.$this->type;
	}

	function get_instrument()
	{
		global $db;
		$requete = "SELECT id, nom, type, description, requis, pa, mp, prix, alchimie FROM craft_instrument WHERE id = ".$this->id_instrument;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return new craft_instrument($row);
	}
}
?>