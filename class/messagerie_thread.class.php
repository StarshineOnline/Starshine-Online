<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class messagerie_thread
{
	public $id_thread;
	public $id_groupe;
	public $id_dest;
	public $id_auteur;
	public $important;
	public $dernier_message;
	
	/**	
	    *  	Constructeur permettant la création d'un thread de messagerie.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-messagerie_thread() qui construit un thread "vide".
	    *		-messagerie_thread($id) qui va chercher le thread dont l'id est $id dans la base.
	**/
	function __construct($id_thread = 0, $id_groupe = 0, $id_dest = 0, $id_auteur = 0, $important = 0, $dernier_message = '')
	{
		global $db;
		if($dernier_message == null) $dernier_message = date("Y-m-d H:i:s", time());
		//Verification du nombre et du type d'argument pour construire le thread adequat.
		if( (func_num_args() == 1) && is_numeric($id_thread) )
		{
			$requeteSQL = $db->query('SELECT id_groupe, id_dest, id_auteur, important, dernier_message FROM messagerie_thread WHERE id_thread = '.$id_thread);
			//Si le thread est dans la base, on le charge sinon on cr?e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_groupe, $this->id_dest, $this->id_auteur, $this->important, $this->dernier_message) = $db->read_row($requeteSQL);
			}
			else $this->__construct();
		}
		else
		{
			$this->id_groupe = $id_groupe;
			$this->id_dest = $id_dest;
			$this->id_auteur = $id_auteur;
			$this->important = $important;
			$this->dernier_message = $dernier_message;
		}
		$this->id_thread = $id_thread;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $this->id_thread > 0 )
		{
			$requete = 'UPDATE messagerie_thread SET ';
			$requete .= 'id_groupe = '.$this->id_groupe.', id_dest = '.$this->id_dest.', id_auteur = '.$this->id_auteur.', important = '.$this->important.', dernier_message = "'.$this->dernier_message.'"';
			$requete .= ' WHERE id_thread = '.$this->id_thread;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO messagerie_thread (id_groupe, id_dest, id_auteur, important, dernier_message) VALUES(';
			$requete .= $this->id_groupe.', '.$this->id_dest.', '.$this->id_auteur.', '.$this->important.', "'.$this->dernier_message.'")';
			$db->query($requete);
			//R?cuperation du dernier ID ins?r?.
			$this->id_thread = $db->last_insert_id();
		}
	}
	
	//supprimer le thread de la base.
	function supprimer($cascade = false)
	{
		global $db;
		if($cascade == true)
		{
			$messages = array();
			//On r?cup?re la liste des messages qui appartiennent ? ce thread
			$requete = "SELECT id_message FROM messagerie_message WHERE id_thread = ".$this->id_thread;
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$messages[] = $row['id_message'];
			}
			$in = implode(',', $messages);
			//On efface tous les etats qui correspondent ? ces messages
			$requete = "DELETE FROM messagerie_etat WHERE id_message IN (".$in.")";
			$db->query($requete);
			//On efface tous les messages
			$requete = "DELETE FROM messagerie_message WHERE id_thread = ".$this->id_thread;
			$db->query($requete);
		}
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

	//Fonction permettant de récupérer tous les messages lié à un thread
	function get_messages($nombre = 'all', $tri_date = 'DESC', $etat = false, $numero_page = false, $message_par_page = 10)
	{
		global $db;
		$this->messages = array();
		if($this->id_thread > 0)
		{
			if($nombre == 'all') $limit = '';
			elseif(is_numeric($nombre)) $limit = ' LIMIT 0, '.$nombre;
			else return false;
			if(is_numeric($numero_page))
			{
				$index_message = ($numero_page - 1) * $message_par_page;
				if($index_message < 0) $index_message = 0;
				$limit = ' LIMIT '.$index_message.', '.$message_par_page;
			}
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

	function get_message_total($id_joueur = '')
	{
		global $db;
		if($id_joueur != '') $and_joueur = ' AND messagerie_etat.id_dest = '.$id_joueur;
		else $and_joueur = '';
		$requete = "SELECT messagerie_etat.id_message FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE id_thread = ".$this->id_thread.$and_joueur." GROUP BY messagerie_etat.id_message";
		$req = $db->query($requete);
		return $db->num_rows;
	}

	function get_numero_dernier_message($id_joueur)
	{
	    global $db;
	    $requete = "SELECT messagerie_etat.id_message as id_message FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE id_thread = ".$this->id_thread." AND messagerie_etat.id_dest = ".$id_joueur." AND messagerie_etat.etat <> 'non_lu' GROUP BY messagerie_etat.id_message";
	    $req = $db->query($requete);
	    return ($db->num_rows + 1);
	}

	function get_titre()
	{
	    global $db;
	    $requete = "SELECT messagerie_message.titre as titre FROM messagerie_message LEFT JOIN messagerie_thread ON messagerie_message.id_thread = messagerie_thread.id_thread WHERE messagerie_message.id_thread = ".$this->id_thread." ORDER BY date ASC LIMIT 0, 1";
	    $req = $db->query($requete);
	    $row = $db->read_row($req);
	    $this->titre = $row[0];
	}
}
?>