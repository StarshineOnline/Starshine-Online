<?php
class bataille_groupe
{
	public $id;
	public $id_bataille;
	public $id_groupe;

	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_groupe() qui construit un etat "vide".
		*		-bataille_groupe($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_groupe($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_bataille = 0, $id_groupe = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_bataille, id_groupe FROM bataille_groupe WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_bataille, $this->id_groupe) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_bataille = $id['id_bataille'];
			$this->id_groupe = $id['id_groupe'];
		}
		else
		{
			$this->id_bataille = $id_bataille;
			$this->id_groupe = $id_groupe;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE bataille_groupe SET ';
			$requete .= 'id_bataille = '.$this->id_bataille.', id_groupe = '.$this->id_groupe;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille_groupe (id_bataille, id_groupe) VALUES(';
			$requete .= $this->id_bataille.', '.$this->id_groupe.')';
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
			$requete = 'DELETE FROM bataille_groupe WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	function __toString()
	{
		return $this->id;
	}

	function get_reperes()
	{
		global $db;
		$this->reperes = array();

		$requete = "SELECT id, id_repere, id_groupe, accepter FROM bataille_groupe_repere WHERE id_groupe = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->reperes[] = new bataille_groupe_repere($row);
		}
	}

	function get_nom()
	{
		if(!isset($this->nom))
		{
			global $db;
			$requete = "SELECT nom FROM groupe WHERE id = ".$this->id_groupe;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			if($row['nom'] == '') $row['nom'] = 'Groupe n°'.$this->id_groupe;
			$this->nom =$row['nom'];
		}
		return $this->nom;
	}
}
?>