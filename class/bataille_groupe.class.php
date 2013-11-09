<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bataille_groupe
{
	private $id;
	private $id_bataille;
	private $id_groupe;
	private $id_thread;

	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_groupe() qui construit un etat "vide".
		*		-bataille_groupe($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_groupe($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_bataille = 0, $id_groupe = 0, $id_thread = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_bataille, id_groupe, id_thread FROM bataille_groupe WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_bataille, $this->id_groupe, $this->id_thread) = $db->read_row($requeteSQL);
				
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
			$this->id_thread = $id['id_thread'];
		}
		elseif ((func_num_args() >= 1) AND $id == 0 AND $id_bataille == 0 AND is_numeric($id_groupe))
		{
			$requeteSQL = $db->query('SELECT bataille_groupe.id, id_bataille, id_thread, bataille.id, bataille.etat FROM bataille_groupe LEFT JOIN bataille ON bataille_groupe.id_bataille = bataille.id WHERE id_groupe = '.$id_groupe.' AND bataille.etat != 2');
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id, $this->id_bataille, $this->id_thread) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id_groupe = $id_groupe;
		}
		else
		{
			$this->id_bataille = $id_bataille;
			$this->id_groupe = $id_groupe;
			$this->id_thread = $id_thread;
			$this->id = $id;
		}		
	}

	//Fonction d'ajout / modification.
	function sauver()
	{
		$b = sSQL($this->id_bataille, SSQL_INTEGER);
		$g = sSQL($this->id_groupe, SSQL_INTEGER);
		$t = sSQL($this->id_thread, SSQL_INTEGER);
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE bataille_groupe SET ';
			$requete .= 'id_bataille = '.$b.', id_groupe = '.$g.', id_thread = '.$t;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille_groupe (id_bataille, id_groupe, id_thread) VALUES(';
			$requete .= $b.', '.$g.', '.$t.')';
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
	
	function get_id_groupe()
	{
		return $this->id_groupe;
	}
	function set_id_groupe($id_groupe)
	{
		$this->id_groupe = $id_groupe;
	}
	
	function get_id_thread()
	{
		return $this->id_thread;
	}
	function set_id_thread($id_thread)
	{
		$this->id_thread = $id_thread;
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
		return $this->reperes;
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
	
	function is_bataille()
	{
		if($this->id_bataille > 0)
		{
			$bataille = new bataille($this->id_bataille);
			if($bataille->get_etat() == 2) // La bataille est finie
				return false;
			else
				return true;
		}
		else
			return false;
	}
}
?>
