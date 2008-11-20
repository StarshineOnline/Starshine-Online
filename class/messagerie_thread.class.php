<?php
class messagerie_thread
{
	public $id_thread;
	public $id_groupe;
	public $id_dest;
	public $id_auteur;
	public $important;
	
	/**	
	    *  	Constructeur permettant la cr�ation d'un thread de messagerie.
	    *	Les valeurs par d�faut sont celles de la base de donn�e.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un thread "vide".
	    *		-Objets($id) qui va chercher le thread dont l'id est $id dans la base.
	**/
	function __construct($id_thread = 0, $id_groupe = 0, $id_dest = 0, $id_auteur = 0, $important = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire le thread adequat.
		if( (func_num_args() == 1) && is_numeric($id_thread) )
		{
			$requeteSQL = $db->query('SELECT id_groupe, id_dest, important FROM messagerie_thread WHERE id_thread = '.$id_thread);
			//Si le thread est dans la base, on le charge sinon on cr�e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_groupe, $this->id_dest, $this->id_auteur, $this->important) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			$this->id_groupe = $id_groupe;
			$this->id_dest = $id_dest;
			$this->id_auteur = $id_auteur;
			$this->important = $important;
		}
		$this->id_thread = $id_thread;
	}
	
	//Fonction permettant de r�cup�rer tous les messages li� � un thread
	function get_messages($nombre = 'all', $tri_date = 'DESC', $etat = false)
	{
		global $db;
		$this->messages = array();
		if($this->id_thread > 0)
		{
			if($nombre == 'all') $limit = '';
			elseif(is_numeric($nombre)) $limit = ' LIMIT 0, '.$nombre;
			else return false;
			if($etat) $requete = "SELECT messagerie_message.id_message as id_message, id_auteur, messagerie_message.id_dest as id_dest, titre, message, date, messagerie_etat.etat as metat FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE id_thread = ".$this->id_thread." AND messagerie_etat.id_dest = ".$etat." ORDER BY date ".$tri_date.$limit;
			else $requete = "SELECT id_message, id_auteur, id_dest, titre, message, date FROM messagerie_message WHERE id_thread = ".$this->id_thread." ORDER BY date ".$tri_date.$limit;
			$req = $db->query($requete);
			$i = 0;
			while($row = $db->read_assoc($req))
			{
				$this->messages[$i] = new messagerie_message($row['id_message'], $row['id_auteur'], $row['id_dest'], $row['titre'], $row['message'], $this->id_thread, $row['date']);
				$this->messages[$i]->etat = $row['metat'];
				$i++;
			}
		}
		return $this->messages;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $id_thread > 0 )
		{
			$requete = 'UPDATE TABLE messagerie_thread SET ';
			$requete .= 'id_groupe = '.$this->id_groupe.', id_dest = '.$this->dest.', id_auteur = '.$this->id_auteur.', important = '.$this->important;
			$requete .= ' WHERE id_thread = '.$this->id_thread;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO messagerie_thread (id_groupe, id_dest, id_auteur, important) VALUES(';
			$requete .= $this->id_groupe.', '.$this->id_dest.', '.$this->id_auteur.', '.$this->important.')';
			$db->query($requete);
			//R�cuperation du dernier ID ins�r�.
			list($this->id_thread) = $db->last_insert_id();
		}
	}
	
	//supprimer le thread de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_thread > 0 )
		{
			$requete = 'DELETE FROM messagerie_thread WHERE id_thread = '.$this->id_thread;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_groupe.', '.$this->id_dest.', '.$this->id_auteur.', '.$this->important;
	}
}
?>