<?php
/**
 * @file event_partie.class.php
 * Gestion des parties lors des events
 * Comprend la classe principale et ses spécialisations éventuelle pour les différents types d'events.
 */


/**
 * Classe de base pour les parties des events, liée à la table event_partie de la base de données.
 * Cette classe doit être spécialisée s'il faut enregistrer des données nécessaires,
 * sinon ce n'est pas obligatoire.
 * Elle ne doit pas être crée directement mais par l'intermédiare de l'objet gérant l'event
 * (classe event et dérivées), de manière à créer la bonne classe en cas de spécialisation.
 */
class event_partie extends table
{
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-event_partie($event, $id) qui récupère les informations de l'objet dans la base.
		-event_partie($event, $vals) qui crée l'objet à partir d'information déjà récupèrées sans la base.
		-event_partie($event, $statut, $heure_debut, $heure_sso, $arene, $heure_fin) qui crée une nouvelle partie.

		@param $event object Objet représentant l'event dont le participant fait parti
		@param $id            int     Id de l'entrée dans la base.
		@param $vals          array   Tableau associatif contenant les entrées de la base de données.
		@param $statut        int     statut de la partie.
		@param $heure_debut   int     heure du début de la partie.
		@param $heure_sso     int     heure sso au début de la partie.
		@param $arene         int     arène où a lieu la partie, peut-être null.
		@param $heure_fin     int     heure de la fin de la partie, peut-être null.
	*/
	function __construct($event, $statut, $heure_debut=0, $heure_sso=0, $arene=null, $heure_fin=null)
	{
		global $db;
		// Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 2 && is_numeric($statut) )
		{
			$this->charger($statut);
		}
		elseif( is_array($statut) )
		{
			$this->init_tab($statut);
    }
		else
		{
			$this->statut = $statut;
			$this->heure_debut = $heure_debut;
			$this->heure_sso = $heure_sso;
			$this->arene = $arene;
			$this->heure_fin = $heure_fin;
			$this->gagnant = 0;
		}
		$this->event = $event;
	}
  /**
   * Crée les équipes ou une équipe en particulier
   * Peut être utilisé de plusieurs façons :
   * - creer($event, $classe)                    renvoie un tableau contenant tous les équipes
   * - creer($event, $classe, $champ, $valeur)   renvoie une ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $event  string    objet event
   * @param  $classe  string    classe à créer
   * @param  $champ   string    champ sur lequel porte la condition
   * @param  $valeur  string    valeur du champ demandée
   */
  static function creer($event, $classe, $champ=null, $valeur=null)
  {
		global $db;
		$keys = false;
    $return = array();

		$requete = 'SELECT * FROM event_partie WHERE event = '.$event->get_id();
    if( $champ )
      $requete .= ' AND '.$champ.' = "'.$valeur.'"';
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
      if( $champ == 'id' )
        return new $classe($event, $db->read_assoc($req));
      else
      {
  			while($row = $db->read_assoc($req))
  			{
  				$return[] = new $classe($event, $row);
  			}
      }
		}
		if( $champ == 'id' )
      return null;
		return $return;
  }

  /**
   * @name Gestion interne des données
   * Méthodes, surchargées ou à surcharger, necessaire à l'initialisation de l'objet
   * et à sa sauvegarde dans la base de données.
   */
  // @{
	/// serialisation des données spécifiques à chaque event (à surcharger)
	protected function serializeDonnees()
  {
    return implode(',',$this->participants);
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees)
  {
    if( $donnees )
      $this->participants = explode(',', $donnees);
  }
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->event = $vals['event'];
		$this->statut = $vals['statut'];
		$this->arene = $vals['arene'] ? $vals['arene'] : null;
		$this->heure_sso = $vals['heure_sso'];
		$this->heure_debut = $vals['heure_debut'];
		$this->heure_fin = $vals['heure_fin'] ? $vals['heure_fin'] : null;
		$this->gagnant = $vals['gagnant'];
		$this->unserializeDonnees($vals['donnees']);
  }
  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    if($champ == 'donnees')
      return $this->serializeDonnees();
    else
      return $this->{$champ};
  }
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'event, statut, arene, heure_sso, heure_debut, heure_fin, gagnant, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->get_id_event().', "'.$this->get_statut().'", '.($this->get_id_arene()!==null?$this->get_id_arene():'NULL').', '.$this->get_heure_sso().', '.$this->get_heure_debut().', '.($this->get_heure_fin()!==null?$this->get_heure_fin():'NULL').', '.$this->get_gagnant().', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'event = '.$this->get_id_event().', statut = "'.$this->get_statut().'", arene = .'.($this->get_id_arene()!==null?$this->get_id_arene():'NULL').', heure_sso = '.$this->get_heure_sso().', heure_debut = '.$this->get_heure_debut().', heure_fin = '.($this->get_heure_fin()!==null?$this->get_heure_fin():'NULL').', gagnant = '.$this->get_gagnant().', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'event_partie';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $event; ///< event en question
  protected $statut; ///< nom de l'équipe
  const a_venir = 10; ///< la partie n'a pas encore commencée
  const en_cours = 20; ///< la partie est en cours
  const fini = 30; ///< la partie est terminée
  protected $arene;  ///< arène où a lieu la partie
  protected $heure_sso; ///< heure SSO au début de la partie (en minutes)
  protected $heure_debut; ///< heure du début de la partie
  protected $heure_fin; ///< heure de la fin de la partie
  protected $gagnant; ///< gangant de la partie (participant ou équipe)
  protected $participants = array();  ///< listes des particpants à la partie.

	/// Renvoie l'event
	function get_event()
	{
		return $this->event;
	}
	/// Renvoie l'event
	function get_id_event()
	{
		return $this->event->get_id();
	}

	/// Renvoie le statut
	function get_statut()
	{
		return $this->statut;
	}
	/// Modifie le statut
	function set_statut($statut)
	{
    $this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}

	/// Renvoie l'arene
	function get_arene()
	{
    if( is_numeric($this->arene) )
		  return new arene($this->royaume);
    else
      return $this->arene;
	}
	/// Renvoie l'id de l'arene
	function get_id_arene()
	{
    if( is_numeric($this->arene) or $this->arene === null )
      return $this->arene;
    else
		  return $this->arene->get_id();
	}
	/// Modifie l'arene
	function set_arene($arene)
	{
		$this->arene = $arene;
		$this->champs_modif[] = 'arene';
	}

	/// Renvoie l'heure SSO (en minutes)
	function get_heure_sso()
	{
		return $this->heure_sso;
	}
	/// Modifie l'heure SSO (en minutes)
	function set_heure_sso($heure_sso)
	{
    $this->heure_sso = $heure_sso;
		$this->champs_modif[] = 'heure_sso';
	}

	/// Renvoie l'heure du début
	function get_heure_debut()
	{
		return $this->heure_debut;
	}
	/// Modifie l'heure du début
	function set_heure_debut($heure_debut)
	{
    $this->heure_debut = $heure_debut;
		$this->champs_modif[] = 'heure_debut';
	}

	/// Renvoie l'heure de la fin
	function get_heure_fin()
	{
		return $this->heure_fin;
	}
	/// Modifie l'heure de la fin
	function set_heure_fin($heure_fin)
	{
    $this->heure_fin = $heure_fin;
		$this->champs_modif[] = 'heure_fin';
	}

	/// Renvoie le gagnant
	function get_gagnant()
	{
		return $this->gagnant;
	}
	/// Modifie le gagnant
	function set_gagnant($gagnant)
	{
    $this->gagnant = $gagnant;
		$this->champs_modif[] = 'gagnant';
	}

	/// Renvoie le nombre de participants
	function get_nbr_participant()
	{
		return count($this->participants);
	}
	/// Renvoie les participants sous forme de tableau
	function get_participants()
	{
		return $this->participants;
	}
	/// Renvoie les participants individuellement
	function get_participant($num)
	{
		return $this->participants[$num];
	}
	/// Modifie les participants
	function set_participants($paticipants)
	{
    $this->participants = $paticipants;
		$this->champs_modif[] = 'donnees';
	}
	/// Modifie les participants individuellement
	function set_participant($num, $paticipant)
	{
    $this->participants[$num] = $paticipant;
		$this->champs_modif[] = 'donnees';
	}
	/// Ajoute un le participant
	function add_participant($paticipant)
	{
    $this->participants[] = $paticipant;
		$this->champs_modif[] = 'donnees';
	}
	// @}
};

/// Classe pour les mathcs des DTE & RTE
class event_partie_dte_rte extends event_partie
{
  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $type;  ///< type de match
  const match2 = 0;  ///< match à 2
  const match3 = 1;  ///< match à 3
  const finale = 2;  ///< finale
  protected $second;  ///< équipe ayant terminé deuxième lors des match à 3.
  protected $nbr_meurtres=array(0,0);  ///< nombre de meurtes lors du match pour chaque équipe
  protected $points_matchs=array(0,0);  ///< points gagnés lors du match pour chaque équipe


	/// Renvoie le type
	function get_type()
	{
		return $this->type;
	}
	/// Modifie le second
	function set_type($type)
	{
    $this->type = $type;
		$this->champs_modif[] = 'donnees';
	}
	/// Renvoie le second
	function get_second()
	{
		return $this->second;
	}
	/// Modifie le second
	function set_second($second)
	{
    $this->second = $second;
		$this->champs_modif[] = 'donnees';
	}
	/// Renvoie le nombre de meurtes d'une équipe lors du match
	function get_nbr_meurtres($num)
	{
		return $this->nbr_meurtres[$num];
	}
	/// Modifie le nombre de meurtes d'une équipe lors du match
	function set_nbr_meurtres($num, $nbr_meurtres)
	{
    $this->nbr_meurtres[$num] = $nbr_meurtres;
		$this->champs_modif[] = 'donnees';
	}
	/// Renvoie le nombre de meurtes d'une équipe lors du match
	function get_points_matchs($num)
	{
		return $this->points_matchs[$num];
	}
	/// Modifie le nombre de meurtes d'une équipe lors du match
	function set_points_matchs($num, $points_matchs)
	{
    $this->points_matchs[$num] = $points_matchs;
		$this->champs_modif[] = 'donnees';
	}

	/// serialisation des données spécifiques à chaque event (à surcharger)
	protected function serializeDonnees()
  {
    $donnees = array(event_partie::serializeDonnees(), $this->type, $this->second,
      implode(',', $this->nbr_meurtres), implode(',', $this->points_matchs));
    return implode('|',$donnees);
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees)
  {
    if( $donnees )
    {
      $vals = explode('|', $donnees);
      event_partie::unserializeDonnees($vals[0]);
      $this->type = $vals[1];
      $this->second = $vals[2];
      $this->nbr_meurtres = explode(',', $vals[3]);
      $this->points_matchs = explode(',', $vals[4]);
    }
  }
	// @}
};

?>
