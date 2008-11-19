<?php
class messagerie
{
	public $id_perso;
	
	/**	
	    *  	Constructeur permettant la création d'un objet pour la gestion de messagerie d'un perso.
	**/
	function __construct($id_perso = 0)
	{
		if($id_perso > 0)
		{
			$this->id_perso = $id_perso;
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
		$requete = "SELECT COUNT(*) as tot, groupe FROM messagerie_etat WHERE etat = 'non_lu' AND id_dest = ".$this->id_perso." GROUP BY groupe";
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if($row['groupe'] == 0) $return['perso'] += $row['tot'];
				else $return['groupe'] += $row['tot'];
				$return['total'] += $row['tot'];
			}
		}
		return $return;		
	}
	
	//Récupération de tous les threads pour ce perso
	function get_threads($type = 'groupe', $tri_date = 'DESC', $liste_message = false, $nombre_message = 'all')
	{
		global $db;
		$this->threads = array();
		switch($type)
		{
			case 'groupe' :
				$perso = recupperso_essentiel($this->id_perso, 'groupe');
				$where = 'id_groupe = '.$perso['groupe'];
			break;
			case 'perso' :
				$where = 'id_dest = '.$this->id_perso.' OR id_auteur = '.$this->id_perso;
			break;
		}
		$requete = "SELECT id_thread, id_groupe, id_dest, id_auteur, important FROM messagerie_thread WHERE ".$where." ORDER BY important DESC, id_thread DESC";
		$req = $db->query($requete);
		$i = 0;
		while($row = $db->read_assoc($req))
		{
			$this->threads[$i] = new messagerie_thread($row['id_thread'], $row['id_groupe'], $row['id_dest'], $row['id_auteur'], $row['important']);
			if($liste_message) $this->threads[$i]->get_messages($nombre_message, $tri_date);
			$i++;
		}
		return $this->threads;
	}

	//Récupération d'un thread pour ce perso
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

	//Envoi d'un message
	function envoi_message($id_thread = 0, $id_auteur, $id_dest = 0, $titre = 'Titre vide', $message, $id_groupe = 0, $roi = 0)
	{
		global $db;
		//Création du thread si besoin
		if($id_thread > 0)
		{
			if($roi == 0) $important = 0;
			else $important = 1;
			$thread = new messagerie_thread(0, $id_groupe, $id_dest, $important);
			$thread->sauver();
		}

		//Création du message
		$auteur = recupperso_essentiel($id_auteur, 'nom');
		$dest = recupperso_essentiel($id_dest, 'nom');
		$message = new messagerie_message(0, $id_auteur, $id_dest, $titre, $message, $id_thread);
		$message->sauver();

		//Si c'est un message de groupe
		if($groupe = recupgroupe($id_groupe))
		{
			$groupe = 1;
			$ids_dest = array();
			foreach($groupe['membre'] as $membre)
			{
				$ids_dest[] = $membre['ID'];
			}
		}
		else
		{
			$ids_dest = array($id_dest);
		}
		
		$etat = 'non_lu';
		//On ajoute un état pour chaque membre
		foreach($ids_dest as $id)
		{
			$etat = new messagerie_etat(0, $id_message, $etat, $id, $groupe);
			$etat->sauver();
		}
	}
}
?>