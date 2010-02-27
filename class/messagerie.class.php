<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
class messagerie
{
	public $id_perso;
	public $id_groupe_perso;
	
	/**	
	    *  	Constructeur permettant la cr?ation d'un objet pour la gestion de messagerie d'un perso.
	**/
	function __construct($id_perso = -1, $id_groupe = -1)
	{
		if($id_perso >= 0)
		{
			$this->id_perso = $id_perso;
			$this->id_groupe_perso = $id_groupe;
			if ($id_groupe == -1)
			{
				$joueur = new perso($id_perso);
				$this->id_groupe_perso = $joueur->get_groupe();
			}
		}
		else return false;
	}
	
	/**
	 * Renvoie le nombre de message non lu par type pour un perso.
	 *
	 * @param $ud_perso identifiant du perso.
	 * 
	 * @return ['total'] Nombre total de message non lu.
	 * @return ['groupe'] Nombre de message de groupe non lu.
	 * @return ['perso'] Nombre de message perso non lu.
	 */
	function get_non_lu()
	{
		global $db;
		//Initatisation
		$return = array('total' => 0, 'groupe' => 0, 'perso' => 0);
		$requete = 'SELECT COUNT(id_message_etat) AS perso, SUM(groupe) AS groupe FROM messagerie_etat WHERE etat LIKE "non_lu" AND id_dest = '.$this->id_perso."";
		$req = $db->query($requete);
		
		if($db->num_rows($req) > 0)
			$return = $db->read_assoc($req);
		$return['groupe'] = empty($return['groupe']) ? 0 : $return['groupe']; 
		$return['perso'] -= $return['groupe']; 
		$return['total'] = $return['groupe'] + $return['perso'];
		return $return;		
	}
	
	//Récupération de tous les threads pour ce perso
	function get_threads($type = 'groupe', $tri_date = 'ASC', $liste_message = false, $nombre_message = 'all')
	{
		global $db, $debut;
		$this->threads = array();
		switch($type)
		{
			case 'groupe' :
				$perso = recupperso_essentiel($this->id_perso, 'groupe');
				$where = 'id_groupe = '.$perso['groupe'].' AND id_groupe != 0';
			break;
			case 'perso' :
				$where = 'id_dest = '.$this->id_perso.' OR (id_auteur = '.$this->id_perso.' AND id_groupe = 0)';
			break;
		}
		$requete = "SELECT id_thread, id_groupe, id_dest, id_auteur, important, dernier_message, titre FROM messagerie_thread WHERE ".$where." ORDER BY important DESC, dernier_message DESC, id_thread DESC";
		//echo $requete;
		$req = $db->query($requete);
		$i = 0;
		while($row = $db->read_assoc($req))
		{
			$this->threads[$i] = new messagerie_thread($row['id_thread'], $row['id_groupe'], $row['id_dest'], $row['id_auteur'], $row['important'], $row['dernier_message'], $row['titre']);
			if($liste_message) $this->threads[$i]->get_messages($nombre_message, $tri_date);
			$i++;
		}
	}

	//Récupération du nombre de message non lu pour ce thread et ce perso
	function get_thread_non_lu($id_thread = 0)
	{
		global $db;
		if($id_thread != 0)
		{
			$requete = "SELECT messagerie_message.id_message FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE messagerie_etat.id_dest = ".$this->id_perso." AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.etat = 'non_lu'";
			$req = $db->query($requete);
			return $db->num_rows($req);
		}
		else return false;
	}

	//Marque comme lu tous les message d'un thread
	function set_thread_lu($id_thread = 0)
	{
		global $db;
		if($id_thread != 0)
		{
			$requete = "UPDATE messagerie_etat, messagerie_message SET messagerie_etat.etat = 'lu' WHERE messagerie_etat.id_message = messagerie_message.id_message AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.etat = 'non_lu' AND messagerie_etat.id_dest = ".$this->id_perso;
			$req = $db->query($requete);
		}
		else return false;
	}
	
	//Récupère le thread et les ?tats de message
	function get_thread($id_thread = 0, $nombre = 'all', $tri_date = 'ASC', $numero_page = false, $message_par_page = 10)
	{
		global $db;
		if($id_thread != 0)
		{
			$this->thread = new messagerie_thread($id_thread);
			// check droits
			if ($this->thread->id_dest != $this->id_perso &&
					$this->thread->id_auteur != $this->id_perso &&
					($this->thread->id_groupe == 0 ||
					 $this->thread->id_groupe != $this->id_groupe_perso) &&
					$this->id_perso != 0 /* magic id */)
				security_block(URL_MANIPULATION);
			if($numero_page == 'last')
			{
			    $numero_message = $this->thread->get_numero_dernier_message($this->id_perso);
			    $numero_page = ceil(($numero_message-1) / $message_par_page);
			    $this->thread->page = $numero_page;
			}
			$this->thread->get_messages($nombre, $tri_date, $this->id_perso, $numero_page, $message_par_page);
		}
		else return false;
	}
	
	function set_etat($id_message = 0, $etat = 'non_lu')
	{
		global $db;
		if($id_message != 0)
		{
			$requete = "SELECT id_message_etat, groupe FROM messagerie_etat WHERE id_dest = ".$this->id_perso." AND id_message = ".$id_message;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$etat_objet = new messagerie_etat_message($row['id_message_etat'], $id_message, $etat, $this->id_perso, $row['groupe']);
			$etat_objet->sauver();
		}
	}

	//Fonction permettant de r?cup?rer un message
	function get_message($id_message = 0)
	{
		global $db;
		if($id_message > 0)
		{
			$requete = "SELECT messagerie_message.id_message as id_message, id_auteur, messagerie_message.id_dest as id_dest, titre, message, messagerie_message.id_thread as id_thread, date, messagerie_etat.etat as metat FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE messagerie_etat.id_message = ".$id_message." AND messagerie_etat.id_dest = ".$this->id_perso;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$this->message = new messagerie_message($row['id_message'], $row['id_auteur'], $row['id_dest'], $row['titre'], $row['message'], $row['id_thread'], $row['date']);
			$this->message->etat = $row['metat'];
		}
		return $this->message;
	}

	//Envoi d'un message
	function envoi_message($id_thread = 0, $id_dest = 0, $titre = 'Titre vide', $message, $id_groupe = 0, $roi = 0)
	{
		global $db;
		//Création du thread si besoin
		if($id_thread == 0)
		{
			if($roi == 0) $important = 0;
			else $important = 1;
			$thread = new messagerie_thread(0, $id_groupe, $id_dest, $this->id_perso, $important, null, $titre);
			$thread->sauver();
			$id_thread = $thread->id_thread;
		}
		else $thread = new messagerie_thread($id_thread);

		//Création du message
		if($this->id_perso > 0)	$auteur = recupperso_essentiel($this->id_perso, 'nom');
		else $auteur['nom'] = 'Administrateur';
		if($id_dest > 0) $dest = recupperso_essentiel($id_dest, 'nom');
		else $dest['nom'] = null;
		$message = new messagerie_message(0, $this->id_perso, $id_dest, $titre, $message, $id_thread, null, $auteur['nom'], $dest['nom']);
		$message->sauver();
		//On modifie le thread
		$thread->dernier_message = date("Y-m-d H:i:s", time());
		$thread->sauver();

		//Si c'est un message de groupe
		if($id_groupe > 0)
		{
			$groupe = new groupe($id_groupe);
			$type_groupe = 1;
			$ids_dest = array();
			$groupe->get_membre();
			foreach($groupe->membre as $membre)
			{
				$ids_dest[] = $membre->get_id_joueur();
			}
		}
		else
		{
			$ids_dest = array($id_dest, $this->id_perso);
			$type_groupe = 0;
		}
		
		//On ajoute un état pour chaque membre
		foreach($ids_dest as $id)
		{
			if($id != $this->id_perso) $etat = 'non_lu';
			else $etat = 'lu';
			$etat_objet = new messagerie_etat_message(0, $message->id_message, $etat, $id, $type_groupe);
			$etat_objet->sauver();
		}
	}
}
?>
