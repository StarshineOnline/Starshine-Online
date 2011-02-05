<?php
/**
 * @file event_equipe.class.php
 * Gestion des équipes lors des events
 * Comprend la classe principale et ses spécialisations éventuelle pour les différents types d'events.
 */


/**
 * Classe de base pour les équipes des events, liée à la table event_equipe de la base de données.
 * Cette classe doit être spécialisée s'il faut enregistrer des données nécessaires,
 * sinon ce n'est pas obligatoire.
 * Elle ne doit pas être crée directement mais par l'intermédiare de l'objet gérant l'event
 * (classe event et dérivées), de manière à créer la bonne classe en cas de spécialisation.
 */
class event_equipe extends table
{
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-event_equipe($event, $id) qui récupère les informations de l'objet dans la base.
		-event_equipe($event, $vals) qui crée l'objet à partir d'information déjà récupèrées sans la base.
		-event_equipe($event, $nom, $royaume) qui crée une nouvelle équipe.

		@param $event object Objet représentant l'event dont le participant fait parti
		@param $id int Id de l'entrée dans la base.
		@param $vals array Tableau associatif contenant les entrées de la base de données.
		@param $nom string nom de l'équipe.
		@param $equipe int id du royaume, si l'équipe défend les couleurs d'un royaume, peut-être null.
	*/
	function __construct($event, $nom, $royaume = null)
	{
		global $db;
		// Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 2 && is_numeric($nom) )
		{
			$this->charger($nom);
		}
		elseif( is_array($nom) )
		{
			$this->init_tab($nom);
    }
		else
		{
			$this->event = $event;
			$this->nom = $nom;
			$this->royaume = $royaume;
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

		$requete = 'SELECT * FROM event_equipe WHERE event = '.$event->get_id();
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
  
  /// Supprime l'objet de la base de donnée
  function supprimer()
  {
		global $db;
    $requete = 'UPDATE event_participant SET equipe = NULL WHERE equipe = '.$this->get_id();
    $db->query($requete);
    table:: supprimer();
  }
  
  /// Renvoie les membres d'une équipe
  function get_membres()
  {
    return event_participant::creer($this->event, 'event_participant', 'equipe', $this->get_id());
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
		$this->nom = $vals['nom'];
		$this->royaume = $vals['royaume']!='NULL' ? $vals['royaume'] : null;
		$this->unserializeDonnees($vals['donnees']);
  }
  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    if($champ == 'Donnees')
      return $this->serializeDonnees();
    else
      return $this->{$champ};
  }
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'event, nom, royaume, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->get_id_event().', "'.$this->get_nom().'", '.($this->get_id_royaume()!==null?$this->get_id_royaume():'NULL').', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'event = '.$this->get_id_event().', nom = "'.$this->get_nom.'", royaume = .'.($this->get_id_royaume()!==null?$this->get_id_royaume():'NULL').', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'event_equipe';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $event; ///< event en question
  protected $nom; ///< nom de l'équipe
  protected $royaume;  ///< royaume, si l'équipe défend les couleurs d'un royaume

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

	/// Renvoie le nom
	function get_nom()
	{
		return $this->nom;
	}
	/// Renvoie l'id du perso
	function set_perso($nom)
	{
    $this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/// Renvoie le royaume
	function get_royaume()
	{
    if( is_numeric($this->royaume) )
      return $this->royaume;
    else
		  return new royaume($this->royaume);
	}
	/// Renvoie l'id du royaume
	function get_id_royaume()
	{
    if( is_numeric($this->royaume) or $this->royaume === null )
      return $this->royaume;
    else
		  return $this->royaume->get_id();
	}
	/// Modifie le royaume
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
	}
	// @}
};

?>
