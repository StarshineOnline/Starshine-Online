<?php


class messagerie_message extends table
{
	protected $id_auteur;
	protected $id_dest;
	protected $message;
	protected $id_thread;
	protected $date;
	protected $nom_auteur = null;
	
	/// renvoie l'id du destinataire
	function get_id_dest()
	{
		return $this->id_dest;
	}
	/// Modifie l'id du destinataire
	function set_id_dest($valeur)
	{
		$this->id_dest = $valeur;
		$this->champs_modif[] = 'id_dest';
	}
	
	/// renvoie l'id de l'auteur
	function get_id_auteur()
	{
		return $this->id_auteur;
	}
	/// Modifie l'id du groupe
	function set_id_auteur($valeur)
	{
		$this->id_auteur = $valeur;
		$this->champs_modif[] = 'id_auteur';
	}
	// renvoie le nom de l'auteur
	function get_nom_auteur()
	{
		if(!$this->nom_auteur)
			$this->nom_auteur = perso::recup_nom($this->id_auteur);
		return $this->nom_auteur;
	}
	
	/// renvoie le texte du message
	function get_message()
	{
		return $this->message;
	}
	/// Modifie le texte du message
	function set_message($valeur)
	{
		$this->message = $valeur;
		$this->champs_modif[] = 'message';
	}
	
	/// renvoie l'id du thread
	function get_id_thread()
	{
		return $this->id_thread;
	}
	/// Modifie l'id du thread
	function set_id_thread($valeur)
	{
		$this->id_thread = $valeur;
		$this->champs_modif[] = 'id_thread';
	}
	
	/// renvoie la date
	function get_date()
	{
		return $this->date;
	}
	/// Modifie la date
	function set_date($valeur)
	{
		$this->date = $valeur;
		$this->champs_modif[] = 'date';
	}
	
	/**	
	 * Constructeur permettant la création d'un message.
	 *	Les valeurs par défaut sont celles de la base de donnée.
	 *	Le constructeur accepte plusieurs types d'appels:
	 *		-messagerie_message() qui construit un message "vide".
	 *		-messagerie_message($id) qui va chercher le message dont l'id est $id dans la base.
	 **/
	function __construct($id = 0, $id_auteur = 0, $id_dest = 0, $message = '', $id_thread = 0, $date = null)
	{
		// Vérification du nombre et du type d'argument pour construire le message adéquat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			parent::__construct($id);
			
			if($date == null)
				$date = date("Y-m-d H:i:s", time());
			
			$this->id_auteur = $id_auteur;
			$this->id_dest = $id_dest;
			$this->message = $message;
			$this->id_thread = $id_thread;
			$this->date = $date;
		}
	}
	
	/// Renvoie le nom du champ servant d'identifiant
	protected function get_champ_id()
	{
		return 'id_message';
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		parent::init_tab($vals);
		
		$this->id_auteur = $vals['id_auteur'];
		$this->id_dest = $vals['id_dest'];
		$this->message = $vals['message'];
		$this->id_thread = $vals['id_thread'];
		$this->date = $vals['date'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		$tbl['id_auteur']='i';
		$tbl['id_dest']='i';
		$tbl['message']='s';
		$tbl['id_thread']='i';
		$tbl['date']='s';
		return $tbl;
	}
	
	static function recup_id_der_msg_thread($id_thread)
	{
		global $db;
		$requete = 'SELECT id_message FROM messagerie_message WHERE id_thread = '.$id_thread.' ORDER BY id_message DESC LIMIT 1';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		return $row ? $row[0] : false;
	}
}
?>