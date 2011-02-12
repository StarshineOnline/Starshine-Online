<?php
/**
 * @file event_participant.class.php
 * Gestion des participants aux events
 * Comprend la classe principale et ses spécialisations éventuelle pour les différents types d'events.
 */
 

/**
 * Classe de base pour les participants aux events, liée à la table event_participant de la base de données.
 * Cette classe doit être spécialisée s'il faut enregistrer des données nécessaires,
 * sinon ce n'est pas obligatoire.
 * Elle ne doit pas être crée directement mais par l'intermédiare de l'objet gérant l'event
 * (classe event et dérivées), de manière à créer la bonne classe en cas de spécialisation.
 */
class event_participant extends table
{
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-event_participant($event, $id) qui récupère les informations de l'objet dans la base.
		-event_participant($event, $vals) qui crée l'objet à partir d'information déjà récupèrées sans la base.
		-event_participant($event, $perso, $equipe) qui crée un nouveau participant.

		@param $event object Objet représentant l'event dont le participant fait parti
		@param $id int Id de l'entrée dans la base.
		@param $vals array Tableau associatif contenant les entrées de la base de données.
		@param $perso int/object objet représentant le personnage ou id de celui-ci.
		@param $equipe int/object objet représentant l'équipe ou id de celle-ci, peut-être null.
	*/
	function __construct($event, $partic, $equipe = false)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 2 && is_numeric($partic) )
		{
			$this->charger($partic);
		}
		elseif( is_array($partic) )
		{
			$this->init_tab($partic);
    }
		else
		{
			$this->event = $event;
			$this->perso = $partic;
			$this->equipe = $equipe;
		}
		$this->event = $event;
	}
  /**
   * Créer les participants ou un participant en particulier
   * Peut être utilisé de plusieurs façons :
   * - creer($event, $classe)                    renvoie un tableau contenant tous les participants
   * - creer($event, $classe, true)              renvoie tous les participants par équipe, sous forme de tableau associatif
   * - creer($event, $classe, $champ, $valeur)   renvoie un ou plusieurs participant suivant la valeur d'un champ précis
   *
   * @param  $event  string    objet event
   * @param  $classe  string    classe à créer
   * @param  $champ   string    champ sur lequel porte la condition
   * @param  $valeur  string    valeur du champ demandée
   */
  static function creer($event, $classe, $champ=false, $valeur=null)
  {
		global $db;
		$keys = false;
		
		$requete = 'SELECT * FROM event_participant WHERE event = '.$event->get_id();
    if( is_string($champ) )
      $requete .= ' AND '.$champ.' = "'.$valeur.'"';
    elseif($champ)
      $keys = 'equipe';
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
      if( $champ == 'id' or $champ == 'id_perso' )
        return new $classe($event, $db->read_assoc($req));
      else
      {
        $return = array();
  			while($row = $db->read_assoc($req))
  			{
  				if(!$keys)
            $return[] = new $classe($event, $row);
  				else
            $return[$row[$keys]][] = new $classe($event, $row);
  			}
  		  return $return;
      }
		}
		else
      return null;
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
    return '';
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees) {}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->event = $vals['event'];
		$this->perso = $vals['perso'];
		$this->equipe = $vals['royaume']!='NULL' ? $vals['royaume'] : null;
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
    return 'event, id_perso, equipe, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->get_id_event().', '.$this->get_id_perso().', '.($this->get_id_equipe()!==null?$this->get_id_equipe():'NULL').', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'event = '.$this->get_id_event().', id_perso = '.$this->get_id_perso().', equipe = .'.($this->get_id_equipe()!==null?$this->get_id_equipe():'NULL').', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'event_participant';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $event; ///< event en question
  protected $perso; ///< perso participant
  protected $equipe;  ///< equipe du participant

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

	/// Renvoie le perso sous forme d'objet
	function get_perso()
	{
    if( is_numeric($this->perso) )
      $this->perso = new perso($this->perso);
		return $this->perso;
	}
	/// Renvoie l'id du perso
	function get_id_perso()
	{
    if( is_numeric($this->perso) )
      return $this->perso;
    else
		  return $this->perso->get_id();
	}

	/// Renvoie l'id de l'équipe
	function get_id_equipe()
	{
    if( is_numeric($this->equipe) or $this->equipe === null )
      return $this->equipe;
    else
		  return $this->equipe->get_id();
	}
	/// Modifie l'équipe
	function set_equipe($equipe)
	{
		$this->equipe = $equipe;
		$this->champs_modif[] = 'equipe';
	}
	// @}
};
?>
