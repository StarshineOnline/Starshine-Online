<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bataille_repere
{
	private $id;
	private $id_bataille;
	private $type;
	private $id_type;
	private $x;
	private $y;
	
	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_repere() qui construit un etat "vide".
		*		-bataille_repere($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_repere($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_bataille = 0, $type = 'action', $id_type = 0, $x = 0, $y = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_bataille, type, id_type, x, y FROM bataille_repere WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_bataille, $this->type, $this->id_type, $this->x, $this->y) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_bataille = $id['id_bataille'];
			$this->type = $id['type'];
			$this->id_type = $id['id_type'];
			$this->x = $id['x'];
			$this->y = $id['y'];
		}
		else
		{
			$this->id_bataille = $id_bataille;
			$this->type = $type;
			$this->id_type = $id_type;
			$this->x = $x;
			$this->y = $y;
			$this->id = $id;
		}		
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE bataille_repere SET ';
			$requete .= 'id_bataille = '.$this->id_bataille.', type = "'.$this->type.'", id_type = '.$this->id_type.', x = '.$this->x.', y = '.$this->y;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille_repere (id_bataille, type, id_type, x, y) VALUES(';
			$requete .= $this->id_bataille.', "'.$this->type.'", '.$this->id_type.', '.$this->x.', '.$this->y.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer($cascade = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM bataille_repere WHERE id = '.$this->id;
			$db->query($requete);
			//Si en cascade on supprime bataille_groupe_repere
			if($cascade)
			{
				$requete = 'DELETE FROM bataille_groupe_repere WHERE id_repere = '.$this->id;
				$db->query($requete);
			}
		}
	}
	
	function __toString()
	{
		return $this->id;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_id_bataille()
	{
		return $this->id_bataille;
	}
	function set_id_bataille($id_bataille)
	{
		$this->id_bataille = $id_bataille;
	}
		
	function get_x()
	{
		return $this->x;
	}
	function set_x($x)
	{
		$this->x = $x;
	}
	
	function get_y()
	{
		return $this->y;
	}
	function set_y($y)
	{
		$this->y = $y;
	}

	function get_repere_type()
	{
		global $db;

		if($this->type == 'action')
		{
			$requete = "SELECT id, nom, description, ajout_groupe, image FROM bataille_repere_type WHERE id = ".$this->id_type;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$this->repere_type = new bataille_repere_type($row);
		}
		else
		{
			$requete = "SELECT * FROM batiment WHERE id = ".$this->id_type;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$this->repere_type = new batiment($row);
		}
		return $this->repere_type;
	}
	
	function get_type()
	{
		return $this->type;
	}
	
	function set_type($type)
	{
		$this->type = $type;
	}
	
	function get_id_type()
	{
		return $this->id_type;
	}
	function set_id_type($id_type)
	{
		$this->id_type = $id_type;
	}
	
	function get_groupes()
	{
		global $db;

		$this->groupes = array();
		$requete = "SELECT id, id_repere, id_groupe, accepter FROM bataille_groupe_repere WHERE id_repere = ".$this->id;
		$req = $db->query($requete);
		if($db->num_rows($req) == 0) return $this->groupes;
		else
		{
			while($row = $db->read_assoc($req))
			{
				$this->groupes[] =  new bataille_groupe_repere($row);
			}
		}
		return $this->groupes;
	}

	function get_groupe($id_groupe)
	{
		global $db;

		$requete = "SELECT bataille_groupe_repere.id as id, id_repere, bataille_groupe_repere.id_groupe as id_groupe, accepter FROM bataille_groupe_repere LEFT JOIN bataille_groupe ON bataille_groupe.id = bataille_groupe_repere.id_groupe WHERE bataille_groupe.id_groupe = ".$id_groupe." AND id_repere = ".$this->id;
		$req = $db->query($requete);
		if($db->num_rows($req) == 0) return false;
		else return new bataille_groupe_repere($db->read_assoc($req));
	}
}
?>