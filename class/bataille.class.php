<?php
class bataille
{
	public $id;
	public $id_royaume;
	public $x;
	public $y;
	public $nom;
	public $description;
	public $etat;
	public $debut;
	public $fin;
	
	/**	
		*	Constructeur permettant la création d'une bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille() qui construit un etat "vide".
		*		-bataille($id) qui va chercher l'etat dont l'id est $id
		*		-bataille($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_royaume = 0, $x = 0, $y = 0, $nom = '', $description = '', $etat = 0, $debut = 0, $fin = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_royaume, x, y, nom, description, etat, debut, fin FROM bataille WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_royaume, $this->x, $this->y, $this->nom, $this->description, $this->etat, $this->debut, $this->fin) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_royaume = $id['id_royaume'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->nom = $id['nom'];
			$this->description = $id['description'];
			$this->etat = $id['etat'];
			$this->debut = $id['debut'];
			$this->fin = $id['fin'];
		}
		else
		{
			$this->id_royaume = $id_royaume;
			$this->x = $x;
			$this->y = $y;
			$this->nom = $nom;
			$this->description = $description;
			$this->etat = $etat;
			$this->debut = $debut;
			$this->fin = $fin;
			$this->id = $id;
		}		
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE bataille SET ';
			$requete .= 'id_royaume = '.$this->id_royaume.', x = '.$this->x.', y = '.$this->y.', nom = "'.$this->nom.'", description = "'.$this->description.'", etat = '.$this->etat.', debut = '.$this->debut.', fin = '.$this->fin;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille (id_royaume, x, y, nom, description, etat, debut, fin) VALUES(';
			$requete .= $this->id_royaume.', '.$this->x.', '.$this->y.', "'.$this->nom.'", "'.$this->description.'", '.$this->etat.', '.$this->debut.', '.$this->fin.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer($cascade = false)
	{
		global $db;
		if($cascade == true)
		{
			//On récupère tous les groupes associés et on les suppriment
			$requete = "SELECT id FROM bataille_groupe WHERE id_bataille = ".$this->id;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$groupes[] = $row['id'];
			}
			$in = implode(',', $groupes);
			//On efface tous les etats qui correspondent à ces messages
			$requete = "DELETE FROM bataille_groupe WHERE id_groupe IN (".$in.")";
			$db->query($requete);
			//On récupère tous les repères associés et on les suppriment
			$requete = "SELECT id FROM bataille_repere WHERE id_bataille = ".$this->id;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$reperes[] = $row['id'];
			}
			$in = implode(',', $reperes);
			//On efface tous les etats qui correspondent à ces messages
			$requete = "DELETE FROM bataille_repere WHERE id_repere IN (".$in.")";
			$db->query($requete);
			//On supprime tous les repère_groupe associés
			$requete = "DELETE FROM bataille_groupe_repere WHERE id_repere IN (".$in.")";
			$db->query($requete);
		}
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM bataille WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_royaume;
	}

	function get_groupes()
	{
		global $db;
		$this->groupes = array();

		$requete = "SELECT id, id_bataille, id_groupe FROM bataille_groupe WHERE id_bataille = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->groupes[] = new bataille_groupe($row);
		}
	}
	
	function is_groupe_in($id_groupe)
	{
		global $db;

		$requete = "SELECT id FROM bataille_groupe WHERE id_bataille = ".$this->id." AND id_groupe = ".$id_groupe;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0) return true;
		else return false;
	}

	function get_reperes()
	{
		global $db;
		$this->reperes = array();

		$requete = "SELECT id, id_bataille, id_type, x, y FROM bataille_repere WHERE id_bataille = ".$this->id;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->reperes[] = new bataille_repere($row);
		}
	}

	function etat_texte()
	{
		switch($this->etat)
		{
			case 0 :
				return 'brouillon';
			break;
			case 1 :
				return 'en cours';
			break;
			case 2 :
				return 'terminée';
			break;
		}
	}

	function get_repere_by_coord($x, $y)
	{
		global $db;

		$requete = "SELECT id, id_bataille, id_type, x, y FROM bataille_repere WHERE id_bataille = ".$this->id." AND x = ".$x." AND y = ".$y;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				return new bataille_repere($row);
			}
		}
		else return false;
	}
}
?>