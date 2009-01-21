<?php
class messagerie_message
{
	public $id_message;
	public $id_auteur;
	public $id_dest;
	public $titre;
	public $message;
	public $id_thread;
	public $date;
	public $nom_auteur;
	public $nom_dest;
	
	/**	
	    *  	Constructeur permettant la cr?ation d'un message.
	    *	Les valeurs par d?faut sont celles de la base de donn?e.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-messagerie_message() qui construit un message "vide".
	    *		-messagerie_message($id) qui va chercher le message dont l'id est $id_message dans la base.
	**/
	function __construct($id_message = 0, $id_auteur = 0, $id_dest = 0, $titre = 'Sans titre', $message = '', $id_thread = 0, $date = null, $nom_auteur = null, $nom_dest = null)
	{
		global $db;
		if($date == null) $date = date("Y-m-d H:i:s", time());
		//Verification du nombre et du type d'argument pour construire le message adequat.
		if( (func_num_args() == 1) && is_numeric($id_message) )
		{
			$requeteSQL = $db->query('SELECT id_auteur, id_dest, titre, message, id_thread, date, nom_auteur, nom_dest FROM messagerie_message WHERE id_message = '.$id_message);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_auteur, $this->id_dest, $this->titre, $this->message, $this->id_thread, $this->date, $this->nom_auteur, $nom_dest) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			$this->id_auteur = $id_auteur;
			$this->id_dest = $id_dest;
			$this->titre = $titre;
			$this->message = $message;
			$this->id_thread = $id_thread;
			$this->date = $date;
			if(is_null($nom_auteur))
			{
				$auteur = recupperso_essentiel($id_auteur, 'nom');
				$this->nom_auteur = $auteur['nom'];
			}
			if(is_null($nom_dest) && $id_dest > 0)
			{
				$dest = recupperso_essentiel($id_dest, 'nom');
				$this->nom_dest = $auteur['dest'];
			}
		}
		$this->id_message = $id_message;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $this->id_message > 0 )
		{
			$requete = 'UPDATE messagerie_message SET ';
			$requete .= 'id_auteur = '.$this->id_auteur.', id_dest = '.$this->id_dest.', titre = "'.$this->dest.'", message = "'.$this->message.'", id_thread = '.$this->id_thread.', date = "'.$this->date.'", nom_auteur = "'.$this->nom_auteur.'", nom_dest = "'.$this->nom_dest;
			$requete .= ' WHERE id_message = '.$this->id_message;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO messagerie_message (id_auteur, id_dest, titre, message, id_thread, date, nom_auteur, nom_dest) VALUES(';
			$requete .= $this->id_auteur.', '.$this->id_dest.', "'.$this->titre.'", "'.$this->message.'", '.$this->id_thread.', "'.$this->date.'", "'.$this->nom_auteur.'", "'.$this->nom_dest.'")';
			$db->query($requete);
			//R?cuperation du dernier ID ins?r?.
			$this->id_message = $db->last_insert_id();
		}
	}
	
	//supprimer le message de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_message > 0 )
		{
			//Suppression du message
			$requete = 'DELETE FROM messagerie_message WHERE id_message = '.$this->id_message;
			$db->query($requete);
			//Suppression de tous les ?tats associ?s
			$requete = 'DELETE FROM messagerie_etat WHERE id_message = '.$this->id_message;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_auteur.', '.$this->id_dest.', '.$this->titre.', '.$this->message.', '.$this->id_thread.', '.$this->date.', '.$this->nom_auteur.', '.$this->nom_dest;
	}
}
?>