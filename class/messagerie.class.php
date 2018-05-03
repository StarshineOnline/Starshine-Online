<?php

class messagerie
{
	protected $perso;
	protected $id_royaume;
	protected $special = false;
	private $condition = false;
	
	
	/**	
	 * Constructeur permettant la création d'un objet pour la gestion de messagerie d'un perso.
	 */
	function __construct(&$perso)
	{
		$this->perso = &$perso;
		if( $perso->get_rang() == 6 || $perso->get_rang() == 1 )
		{
			$royaume = $perso->get_royaume();
			$this->id_royaume = $royaume->get_id();
			if( $perso->get_rang() == 6 )
				$this->special = 'roi';
			else if( $royaume->get_ministre_economie() == $perso->get_id() )
				$this->special = 'eco';
			else if( $royaume->get_ministre_militaire() == $perso->get_id() )
				$this->special = 'mil';
			else
				log_admin::log('erreur', '[messagerie]Personnage général sans être ministre');
		}
	}
	
	function get_condition()
	{
		if( !$this->condition )
		{
			$this->condition = '( (id_dest = '.$this->perso->get_id().' AND type_dest = "perso")';
			if( $this->special )
				$this->condition .= ' OR (id_dest = '.$this->id_royaume.' AND type_dest = "'.$this->special.'")';
			$this->condition .= ' )';
		}
		return $this->condition;
	}
	
	/**
	 * Renvoie le nombre de message non lu par type pour un perso.
	 *
	 * @param $ud_perso identifiant du perso.
	 * 
	 * @return ['total'] Nombre total de message non lu.
	 * @return ['groupe'] Nombre de message de groupe non lu.
	 * @return ['perso'] Nombre de message perso non lu.
	 * @return ['echange'] Nombre de message perso non lu.
	 */
	function get_non_lu()
	{
		global $db;
		//Initatisation
		$return = array('total' => 0, 'groupe' => 0, 'perso' => 0, 'echange' => 0);
		// ancien système
		$requete = 'SELECT COUNT(id_message_etat) AS perso, SUM(groupe) AS groupe, SUM(echange) AS echange FROM messagerie_etat WHERE etat LIKE "non_lu" AND id_dest = '.$this->perso->get_id()."";
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
			$return = $db->read_assoc($req);
		$return['groupe'] = empty($return['groupe']) ? 0 : $return['groupe']; 
		$return['echange'] = empty($return['echange']) ? 0 : $return['echange']; 
		$return['perso'] -= ($return['groupe'] + $return['echange']); 
		$return['total'] = $return['groupe'] + $return['echange'] + $return['perso'];
		// nouveau système
		$cond = '(l.id_dest = '.$this->perso->get_id().' AND type_dest = "perso")';
		if( $this->special )
			$cond .= ' OR (l.id_dest = '.$this->id_royaume.' AND type_dest = "'.$this->special.'")';
		$requete = 'SELECT categorie, SUM(nbr_non_lu) AS nbr FROM messagerie_lus AS l INNER JOIN messagerie_thread AS t ON l.id_thread = t.id_thread WHERE '.$cond.' GROUP BY categorie';
		$req = $db->query($requete);
		while( $row = $db->read_assoc($res) )
		{
			$return[ $row['categorie'] ] += $row['nbr'];
			$return['total'] += $row['nbr'];
		}
		return $return;		
	}
	
	// Renvoie le nombre total de message non lu à partir de l'id du personnage
	static function get_non_lu_total($id)
	{
		$messagerie = new messagerie(new perso($id));
		$nb_mess = $messagerie->get_non_lu();
		return $nb_mess['total'];
	}
	
	//Récupération de tous les threads pour ce perso
	function get_threads($categorie = 'groupe', $tri_date = 'ASC', $liste_message = false, $nombre_message = 'all')
	{
		global $Trace, $db;
		// nouveau système
		switch( $categorie )
		{
		case 'perso':
		case 'groupe':
			$id_perso = $this->perso->get_id();
			break;
		case 'royaume':
		case 'diplomatie':
			$id_perso = $Trace[$this->perso->get_race()]['numrace'];
			break;
		}
		$this->threads = messagerie_thread::create(false, false, 'important DESC, dernier_message DESC, id_thread DESC', false, '(id_groupe = '.$this->perso->get_id_groupe().' OR id_dest = '.$id_perso.' OR id_auteur = '.$id_perso.') AND categorie = "'.$categorie.'"');
		if( $liste_message )
		{
			foreach($this->threads as &$thread)
			{
				$thread->get_messages($nombre_message, $tri_date);
			}
		}
		// ancien système
		/*switch($categorie)
		{
		case 'groupe' :
			$where = 'id_groupe = '.$this->perso->get_id_groupe().' AND id_groupe != 0';
			break;
		case 'perso' :
			$where = '(id_dest = '.$id_perso.' OR (id_auteur = '.$id_perso.' AND id_groupe = 0)) AND (titre NOT LIKE "%vous propose un échange" AND titre NOT LIKE "Finalisation de l\'échange avec%")';
			break;
		default:
			$where = false;
		}
		if( $where )
		{
			$requete = "SELECT * FROM messagerie_thread WHERE ".$where." ORDER BY important DESC, dernier_message DESC, id_thread DESC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$thread = new messagerie_thread($row);
				if($liste_message) $thread->get_messages($nombre_message, $tri_date);
				$this->threads[] = $thread;
			}
		}*/
	}

	//Récupération du nombre de message non lu pour ce thread et ce perso
	function get_thread_non_lu($id_thread)
	{
		global $db;
		if($id_thread != 0)
		{
			$requete = "SELECT messagerie_message.id_message FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE messagerie_etat.id_dest = ".$this->perso->get_id()." AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.etat = 'non_lu'";
			$req = $db->query($requete);
			return $db->num_rows($req) + messagerie_lus::calcul_somme('nbr_non_lu', false, false, false, 'id_thread = '.$id_thread.' AND '.$this->get_condition());
		}
		else return false;
	}

	//Marque comme lu tous les messages d'un thread
	function set_thread_lu($id_thread)
	{
		global $db;
		if($id_thread != 0)
		{
			// ancien système
			$requete = "UPDATE messagerie_etat, messagerie_message SET messagerie_etat.etat = 'lu' WHERE messagerie_etat.id_message = messagerie_message.id_message AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.etat = 'non_lu' AND messagerie_etat.id_dest = ".$this->perso->get_id();
			$req = $db->query($requete);
			// nouveau système
			$requete = 'UPDATE messagerie_lus SET nbr_non_lu = 0, id_der_lu = id_fin WHERE id_thread = '.$id_thread.' AND '.$this->get_condition();
			$req = $db->query($requete);
		}
		else return false;
	}
	
	//Marque comme lu tous les messages du joueur
	function set_thread_lu_all()
	{
		global $db;
		// ancien système
		$requete = "UPDATE messagerie_etat SET etat = 'lu' WHERE etat = 'non_lu' AND id_dest = ".$this->perso->get_id();
		$req = $db->query($requete);
		// nouveau système
		$requete = 'UPDATE messagerie_lus SET nbr_non_lu = 0, id_der_lu = id_fin WHERE '.$this->get_condition();
		$req = $db->query($requete);
	}
	
	//Récupération du nombre de message masqués pour ce thread et ce perso
	function get_thread_masque($id_thread)
	{
		global $db;
		if($id_thread != 0)
		{
			$requete = "SELECT messagerie_message.id_message FROM messagerie_message LEFT JOIN messagerie_etat ON messagerie_message.id_message = messagerie_etat.id_message WHERE messagerie_etat.id_dest = ".$this->perso->get_id()." AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.etat = 'masque'";
			$req = $db->query($requete);
			/// @todo ajouter la nouvelle version
			return $db->num_rows($req);
		}
		else return false;
	}
	
	// Marque comme masqué tous les messages d'un thread
	function set_thread_masque($id_thread)
	{
		global $db;
		
		if( $id_thread != 0 )
		{
			$requete =
				"UPDATE messagerie_etat, messagerie_message
				SET messagerie_etat.etat = 'masque'
				WHERE messagerie_etat.id_message = messagerie_message.id_message AND messagerie_message.id_thread = ".$id_thread." AND messagerie_etat.id_dest = ".$this->perso->get_id()
			;
			$req = $db->query($requete);
			return $req;
		}
		else
			return false;
	}
	
	//Récupère le thread et les états de message
	function get_thread($id_thread = 0, $nombre = 'all', $tri_date = 'ASC', $numero_page = false, $message_par_page = 10)
	{
		global $db;
		if($id_thread != 0)
		{
			$this->thread = new messagerie_thread($id_thread);
			// check droits
			/// @todo refaire tests
			/*if ($this->thread->get_id_dest() != $this->perso->get_id() &&
					$this->thread->get_id_auteur() != $this->perso->get_id() &&
					($this->thread->get_id_groupe() == 0 ||
					 $this->thread->get_id_groupe() != $this->perso->get_id_groupe()) &&
					$this->id_perso != 0 /* magic id /)
				security_block(URL_MANIPULATION);*/
			if($numero_page == 'last')
			{
			    $numero_message = $this->thread->get_numero_dernier_message($this->perso->get_id(), $this->get_condition());
			    $numero_page = ceil(($numero_message-1) / $message_par_page);
			    $this->thread->page = $numero_page;
			}
			switch( $this->thread->get_categorie() )
			{
			case 'perso':
			case 'groupe':
				$id_dest = $this->perso->get_id();
				$type_dest = 'perso';
				break;
			case 'royaume':
			case 'diplomatie':
				$id_dest = $this->id_royaume;
				$type_dest = $this->special;
				break;
			}
			$this->thread->get_messages($nombre, $tri_date, $id_dest, $type_dest, $numero_page, $message_par_page);
		}
		else return false;
	}
	
	function set_etat($id_message = 0, $etat = 'non_lu')
	{
		global $db;
		if($id_message != 0)
		{
			$requete = "SELECT id_message_etat, groupe, echange FROM messagerie_etat WHERE id_dest = ".$this->id_perso." AND id_message = ".$id_message;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$etat_objet = new messagerie_etat_message($row['id_message_etat'], $id_message, $etat, $this->id_perso, $row['groupe'], $row['echange']);
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
	function envoi_message($id_thread = 0, $id_dest = 0, $titre = 'Titre vide', $message, $id_groupe = 0, $important = 0, $categorie = 'perso')
	{
		global $db;
		//Création du thread si besoin
		if($id_thread == 0)
		{
			$auteur = $categorie == 'diplomatie' ? $this->id_royaume : $this->perso->get_id();
			$thread = new messagerie_thread(0, $id_groupe, $id_dest, $auteur, $important, null, $titre, $categorie);
			$thread->sauver();
			$id_thread = $thread->get_id();
		}
		else $thread = new messagerie_thread($id_thread);

		// id du dernier message
		$id_prec = messagerie_message::recup_id_der_msg_thread($id_thread);

		//Création du message
		$message = new messagerie_message(0, $this->perso->get_id(), $id_dest, $message, $id_thread, null);
		$message->sauver();
		//On modifie le thread
		$thread->dernier_message = date("Y-m-d H:i:s", time());
		$thread->sauver();

		switch($categorie)
		{
		case 'perso':
			$ids_dest = array(array('id'=>$id_dest,'type'=>'perso'), array('id'=>$this->perso->get_id(), 'type'=>'perso'));
			break;
		case 'groupe':
			$groupe = new groupe($id_groupe);
			$groupe->get_membre();
			foreach($groupe->membre as $membre)
			{
				$ids_dest[] = array('id'=>$membre->get_id_joueur(), 'type'=>'perso');
			}
			break;
		case 'royaume':
			if( $id_groupe > 0 )
			{
				$groupe = new groupe($id_groupe);
				$ids_dest = array(array('id'=>$this->id_royaume,'type'=>'roi'), array('id'=>$this->id_royaume,'type'=>'eco'), array('id'=>$this->id_royaume,'type'=>'mil'));
			}
			else
			{
				$groupe = new groupe( $this->perso->get_id_groupe() );
				$ids_dest = array(array('id'=>$id_dest,'type'=>'roi'), array('id'=>$id_dest,'type'=>'eco'), array('id'=>$id_dest,'type'=>'mil'));
			}
			$groupe->get_membre();
			foreach($groupe->membre as $membre)
			{
				$ids_dest[] = array('id'=>$membre->get_id_joueur(),'type'=>'perso');
			}
			break;
		case 'diplomatie':
			$ids_dest = array(array('id'=>$this->id_royaume,'type'=>'roi'), array('id'=>$this->id_royaume,'type'=>'eco'), array('id'=>$this->id_royaume,'type'=>'mil'), array('id'=>$id_dest,'type'=>'roi'), array('id'=>$id_dest,'type'=>'eco'), array('id'=>$id_dest,'type'=>'mil'));
			break;
		}
		
		// On met à jour les compteurs de lecture
		foreach($ids_dest as $dest)
		{
			$lu = messagerie_lus::create(array('id_thread', 'id_dest', 'type_dest', 'id_fin'), array($id_thread, $dest['id'], $dest['type'], $id_prec) );
			$auteur = ($dest['type'] == 'perso' && $dest['id'] == $this->perso->get_id()) || ($dest['type'] == $this->special && $dest['id'] == $this->id_royaume);
			if( $lu )
			{
				$lu = $lu[0];
				$lu->set_id_fin($message->get_id());
				$lu->add_nbr_msg(1);
				if( $auteur )
				{
					$lu->set_id_der_lu($message->get_id());
					$lu->set_nbr_non_lu(0);
				}
				else
					$lu->add_nbr_non_lu(1);
			}
			else if($auteur)
				$lu = new messagerie_lus(0, $id_thread, $message->get_id(), $message->get_id(), $message->get_id(), $dest['id'], $dest['type'], 1, 0);
			else
				$lu = new messagerie_lus(0, $id_thread, $message->get_id(), $message->get_id(), 0, $dest['id'], $dest['type'], 1, 1);
			$lu->sauver();
		}
		return $id_thread;
	}
}
?>
