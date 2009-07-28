<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class bataille_groupe_repere
{
	public $id;
	public $id_repere;
	public $id_groupe;
	public $accepter;
	
	/**	
		*	Constructeur permettant la création d'un repère de bataille.
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-bataille_groupe_repere() qui construit un etat "vide".
		*		-bataille_groupe_repere($id) qui va chercher l'etat dont l'id est $id
		*		-bataille_groupe_repere($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id = 0, $id_repere = 0, $id_groupe = 0, $accepter = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT id_repere, id_groupe, accepter FROM bataille_groupe_repere WHERE id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_repere, $this->id_groupe, $this->accepter) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_repere = $id['id_repere'];
			$this->id_groupe = $id['id_groupe'];
			$this->accepter = $id['accepter'];
		}
		else
		{
			$this->id_repere = $id_repere;
			$this->id_groupe = $id_groupe;
			$this->accepter = $accepter;
			$this->id = $id;
		}		
	}
	
	//Fonction d'ajout / modification.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE bataille_groupe_repere SET ';
			$requete .= 'id_repere = '.$this->id_repere.', id_groupe = '.$this->id_groupe.', accepter = '.$this->accepter;
			$requete .= ' WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO bataille_groupe_repere (id_repere, id_groupe, accepter) VALUES(';
			$requete .= $this->id_repere.', '.$this->id_groupe.', '.$this->accepter.')';
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
			$requete = 'DELETE FROM bataille_groupe_repere WHERE id = '.$this->id;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id;
	}

	function accepte()
	{
		$this->accepter = true;
		$this->sauver();
	}

	function get_repere()
	{
		$this->repere = new bataille_repere($this->id_repere);
	}
}
?>