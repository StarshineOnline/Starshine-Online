<?php
class messagerie_etat
{
	public $id_etat;
	public $id_message;
	public $etat;
	public $id_dest;
	public $groupe;
	
	/**	
	    *  	Constructeur permettant la création d'un etat de message.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-messagerie_etat() qui construit un etat "vide".
	    *		-messagerie_etat($id) qui va chercher l'etat dont l'id est $id_etat dans la base.
	**/
	function __construct($id_etat = 0, $id_message = 0, $etat = 'non_lu', $id_dest = 0, $groupe = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id_etat) )
		{
			$requeteSQL = $db->query('SELECT id_message, etat, id_dest, groupe FROM messagerie_etat WHERE id_etat = '.$id_etat);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_message, $this->etat, $this->id_dest, $this->groupe) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			$this->id_message = $id_message;
			$this->etat = $etat;
			$this->id_dest = $id_dest;
			$this->groupe = $groupe;
		}
		$this->id_etat = $id_etat;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $this->id_etat > 0 )
		{
			$requete = 'UPDATE messagerie_etat SET ';
			$requete .= 'id_message = '.$this->id_message.', etat = "'.$this->etat.'", id_dest = '.$this->id_dest.', groupe = '.$this->groupe;
			$requete .= ' WHERE id_message_etat = '.$this->id_etat;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO messagerie_etat (id_message, etat, id_dest, groupe) VALUES(';
			$requete .= $this->id_message.', "'.$this->etat.'", '.$this->id_dest.', '.$this->groupe.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			list($this->id_etat) = $db->last_insert_id();
		}
	}
	
	//supprimer l'etat de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_etat > 0 )
		{
			$requete = 'DELETE FROM messagerie_etat WHERE id_etat = '.$this->id_etat;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_message.', '.$this->etat.', '.$this->id_dest.', '.$this->groupe;
	}
}
?>