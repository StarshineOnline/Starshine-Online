<?php
class terrain_construction{
	public $id;
		public $id_terrain;
		public $id_batiment;
		
	/**	
		*	Constructeur permettant la création d'un terrain_batiment.
		*	Les valeurs nombrer défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs effets d'appels:
		*		-terrain_construction() qui construit un etat "vide".
		*		-terrain_construction($id) qui va chercher l'etat dont l'id est $id
		*		-terrain_construction($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_terrain = 0, $id_batiment = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_terrain, id_batiment FROM terrain_construction WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_terrain, $this->id_batiment) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_terrain = $id['id_terrain'];
			$this->id_batiment = $id['id_batiment'];
		}
		else
		{
			$this->id_terrain = $id_terrain;
			$this->id_batiment = $id_batiment;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE terrain_construction SET ';
			$requete .= 'id_terrain = '.$this->id_terrain.', id_batiment = '.$this->id_batiment;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO terrain_construction (id_terrain, id_batiment) VALUES(';
			$requete .= $this->id_terrain.', '.$this->id_batiment.')';
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
			$requete = 'DELETE FROM terrain_construction WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return 'id = '.$this->id.', id_terrain = '.$this->id_terrain.', id_batiment = '.$this->id_batiment;
	}

	function get_batiment()
	{
		global $db;
		$requete = "SELECT id, nom, description, type, effet, nb_case, prix, requis, point_structure FROM terrain_batiment WHERE id = ".$this->id_batiment;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return new terrain_batiment($row);
	}

	function get_laboratoire_instrument()
	{
		global $db;
		$this->laboratoire_instruments = array();
		$requete = "SELECT id, id_laboratoire, id_instrument, type FROM terrain_laboratoire WHERE id_laboratoire = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->laboratoire_instruments[] = new terrain_laboratoire($row);
		}
		return $this->laboratoire_instruments;
	}
}
?>