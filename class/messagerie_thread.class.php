<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('../root.php'))
  include_once('../root.php');

class messagerie_thread extends table
{
	public $id_thread;
	public $id_groupe;
	public $id_dest;
	public $id_auteur;
	public $important;
	public $dernier_message;
	public $titre;
	protected $categorie;
	
	/// renvoie l'id du groupe
	function get_id_groupe()
	{
		return $this->id_groupe;
	}
	/// Modifie l'id du groupe
	function set_id_groupe($valeur)
	{
		$this->id_groupe = $valeur;
		$this->champs_modif[] = 'id_groupe';
	}
	
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
	
	/// renvoie l'importance du message
	function get_important()
	{
		return $this->important;
	}
	/// Modifie l'importance du message
	function set_important($valeur)
	{
		$this->important = $valeur;
		$this->champs_modif[] = 'important';
	}
	
	/// renvoie la date du dernier  message
	function get_dernier_message()
	{
		return $this->dernier_message;
	}
	/// Modifie la date du dernier  message
	function set_dernier_message($valeur)
	{
		$this->dernier_message = $valeur;
		$this->champs_modif[] = 'dernier_message';
	}
	
	/// renvoie le titre
	function get_titre()
	{
		return $this->titre;
	}
	/// Modifie le titre
	function set_titre($valeur)
	{
		$this->titre = $valeur;
		$this->champs_modif[] = 'titre';
	}
	
	/// renvoie la catégorie
	function get_categorie()
	{
		return $this->categorie;
	}
	/// Modifie la catégorie
	function set_categorie($valeur)
	{
		$this->categorie = $valeur;
		$this->champs_modif[] = 'categorie';
	}
	
	
	/**	
	    *  	Constructeur permettant la création d'un thread de messagerie.
	    *	Les valeurs par défaut sont celles de la base de donnée.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-messagerie_thread() qui construit un thread "vide".
	    *		-messagerie_thread($id) qui va chercher le thread dont l'id est $id dans la base.
	**/
	function __construct($id_thread = 0, $id_groupe = 0, $id_dest = 0, $id_auteur = 0, $important = 0, $dernier_message = '', $titre = '', $categorie = '')
	{
		//Verification du nombre et du type d'argument pour construire le message adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id_thread);
		}
		else
		{
			$this->id_groupe = $id_groupe;
			$this->id_dest = $id_dest;
			$this->id_auteur = $id_auteur;
			$this->important = $important;
			$this->dernier_message = $dernier_message ? $dernier_message : date("Y-m-d H:i:s", time());
			$this->titre = $titre;
			$this->categorie = $categorie;
		}
		/// @todo à améliorer
		$this->id_thread = $this->id;
	}
	
	/// Renvoie le nom du champ servant d'identifiant
	protected function get_champ_id()
	{
		return 'id_thread';
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_groupe = $vals['id_groupe'];
		$this->id_dest = $vals['id_dest'];
		$this->id_auteur = $vals['id_auteur'];
		$this->important = $vals['important'];
		$this->dernier_message = $vals['dernier_message'];
		$this->titre = $vals['titre'];
		$this->categorie = $vals['categorie'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		/// @todo à mettre en une seule déclaration
    $tbl['id_groupe']='i';
    $tbl['id_dest']='i';
    $tbl['id_auteur']='i';
    $tbl['important']='i';
    $tbl['dernier_message']='s';
    $tbl['titre']='s';
    $tbl['categorie']='s';
		return $tbl;
	}

	//Fonction permettant de récupérer tous les messages lié à un thread
	function get_messages($nombre = 'all', $tri_date = 'DESC', $id_dest = false, $type_dest='perso', $numero_page = false, $message_par_page = 10)
	{
		global $db;
		$this->messages = array();
		if($this->get_id() > 0)
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
			if($id_dest) 
			{
				$requete = '(SELECT messagerie_message.*, messagerie_etat.etat as metat FROM messagerie_message INNER JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE id_thread = '.$this->get_id().' AND messagerie_etat.id_dest = '.$id_dest.') UNION (select m.*, IF(m.id_message<=l.id_der_lu, "lu", "non lu") AS metat FROM messagerie_message AS m INNER JOIN messagerie_lus AS l ON m.id_thread = l.id_thread AND m.id_message >= l.id_debut AND m.id_message <= l.id_fin WHERE m.id_thread = '.$this->get_id().' AND l.id_dest = '.$id_dest.' AND type_dest = "'.$type_dest.'") ORDER BY date '.$tri_date.$limit;
			}
			else 
			{
				$requete = 'SELECT * FROM messagerie_message WHERE id_thread = '.$this->get_id().' ORDER BY date '.$tri_date.$limit;
			}
			$req = $db->query($requete);
			$i = 0;
			while($row = $db->read_assoc($req))
			{
				$this->messages[$i] = new messagerie_message($row);
				$this->messages[$i]->etat = $row['metat'];
				$i++;
			}
		}
		return $this->messages;
	}

	function get_message_total($id_joueur = '', $where=false)
	{
		global $db;
		if($id_joueur != '') $and_joueur = ' AND messagerie_etat.id_dest = '.$id_joueur;
		else $and_joueur = '';
		$count = 0;
		//Permet de trouver aussi les messages de groupe
		$requete = "SELECT id_message_etat as total FROM messagerie_etat " .
				"LEFT JOIN messagerie_message ON messagerie_message.id_message = messagerie_etat.id_message " .
				"WHERE messagerie_message.id_thread = ".$this->get_id().$and_joueur." GROUP BY messagerie_etat.id_message";
		$req = $db->query($requete);
		if( !$where )
			$where = $id_joueur ? 'type_dest = "perso" AND id_dest = '.$id_joueur : '1';
		return $db->num_rows + messagerie_lus::calcul_somme('nbr_msg', false, false, false, 'id_thread = '.$this->get_id().' AND '.$where);
	}

	function get_numero_dernier_message($id_joueur, $where=false)
	{
    global $db;
    $requete = "SELECT messagerie_etat.id_message as id_message FROM messagerie_etat " .
    		"LEFT JOIN messagerie_message ON messagerie_message.id_message = messagerie_etat.id_message " .
    		"WHERE id_thread = ".$this->get_id()." AND messagerie_etat.id_dest = ".$id_joueur.
			" AND messagerie_etat.etat NOT LIKE 'non_lu' GROUP BY messagerie_etat.id_message";
    $req = $db->query($requete);
    $nbr_anc = $db->num_rows;
		if( !$where )
			$where = $id_joueur ? 'type_dest = "perso" AND id_dest = '.$id_joueur : '1';
		$nbr = messagerie_lus::calcul_somme(array('nbr_msg',  'nbr_non_lu'), false, false, false, 'id_thread = '.$this->get_id().' AND '.$where);
		
	  return $nbr_anc + $nbr['nbr_msg'] - $nbr['nbr_non_lu'] + 1;
	}

	/*function get_titre()
	{
	    global $db;
	    $requete = "SELECT messagerie_message.titre as titre FROM messagerie_message LEFT JOIN messagerie_thread ON messagerie_message.id_thread = messagerie_thread.id_thread WHERE messagerie_message.id_thread = ".$this->id_thread." ORDER BY date ASC LIMIT 0, 1";
	    $req = $db->query($requete);
	    $row = $db->read_row($req);
	    $this->titre = $row[0];
	}*/
}
?>
