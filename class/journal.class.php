<?php
class journal
{
	private $id;
	private $id_perso;
	private $action;
	private $actif;
	private $passif;
	private $time;
	private $valeur;
	private $valeur2;
	private $x;
	private $y;
	
	/**	
		*	Constructeur permettant la création d'un combat
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-journal() qui construit un journal "vide".
		*		-journal($id) qui va chercher le journal dont l'id est $id
		*		-journal($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_perso = 0, $action = '', $actif = '', $passif = '', $time = '', $valeur = 0, $valeur2 = 0, $x = 0, $y = 0)
	{
		global $db;
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requete = $db->query('SELECT id, id_perso, action, actif, passif, time, valeur, valeur2, x, y FROM journal WHERE id = '.$id);
			if( $db->num_rows($requete) > 0 )
			{
				list($this->id, $this->id_perso, $this->action, $this->actif, $this->passif, $this->time, $this->valeur, $this->valeur2, $this->x, $this->y) = $db->read_row($requete);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_perso = $id['id_perso'];
			$this->action = $id['action'];
			$this->actif = $id['actif'];
			$this->passif = $id['passif'];
			$this->time = $id['time'];
			$this->valeur = $id['valeur'];
			$this->valeur2 = $id['valeur2'];
			$this->x = $id['x'];
			$this->y = $id['y'];
		}
		else
		{
			$this->id = $id;
			$this->id_perso = $id_perso;
			$this->action = $action;
			$this->actif = $actif;
			$this->passif = $passif;
			$this->time = $time;
			$this->valeur = $valeur;
			$this->valeur2 = $valeur2;
			$this->x = $x;
			$this->y = $y;
		}		
	}
	
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE journal SET 
			id_perso = '.$this->id_perso.', action = "'.$this->action.'", actif = '.$this->actif.', passif = '.$this->passif.', time = "'.$this->time.'", valeur = "'.$this->valeur.'", valeur2 = "'.$this->valeur2.'", x = '.$this->x.', y = '.$this->y.'
			WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO journal (id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(
			'.$this->id.', '.$this->id_perso.', "'.$this->action.'", '.$this->actif.', '.$this->passif.', "'.$this->time.'", "'.$this->valeur.'", "'.$this->valeur2.'", '.$this->x.', '.$this->y.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id) = $db->last_insert_id();
		}
	}
	
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM journal WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id.', '.$this->id_perso.', '.$this->action.', '.$this->actif.', '.$this->passif.', '.$this->time.', '.$this->valeur.', '.$this->valeur2.', '.$this->x.', '.$this->y;
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_id_perso()
	{
		return $this->id_perso;
	}
	
	function get_action()
	{
		return $this->action;
	}
	
	function get_actif()
	{
		return $this->actif;
	}
	
	function get_passif()
	{
		return $this->passif;
	}
	
	function get_time()
	{
		return $this->time;
	}
	
	function get_valeur()
	{
		return $this->valeur;
	}
	function get_valeur2()
	{
		return $this->valeur2;
	}
	
	function get_x()
	{
		return $this->x;
	}
	
	function get_y()
	{
		return $this->y;
	}
	
	//Renvoie le journal suivant du joueur vérifiant $where
	function get_suivant($where = 1)
	{
		global $db;
		$requete = 'SELECT * FROM journal WHERE id_perso = '.$this->id_perso.' AND id > '.$this->id.' AND ('.$where.') ORDER BY id ASC';
		$req = $db->query($requete);
		if ($db->num_rows > 0)
		{
			$row = $db->read_assoc($req);
			return new journal($row);
		}
		else
			return false;
	}
	
	//Renvoie le journal précédent du joueur vérifiant $where
	function get_precedent($where = 1)
	{
		global $db;
		$requete = 'SELECT * FROM journal WHERE id_perso = '.$this->id_perso.' AND id < '.$this->id.' AND ('.$where.') ORDER BY id DESC';
		$req = $db->query($requete);
		if ($db->num_rows > 0)
		{
			$row = $db->read_assoc($req);
			return new journal($row);
		}
		else
			return false;
	}
	
}
?>
