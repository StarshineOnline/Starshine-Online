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
   * @param  $ordre   string    instructions pour le tri
   */
  static function creer($event, $classe, $champ=null, $valeur=null, $ordre='id ASC')
  {
		global $db;
		$keys = false;
    $return = array();

		$requete = 'SELECT * FROM event_equipe WHERE event = '.$event->get_id();
    if( $champ )
      $requete .= ' AND '.$champ.' = "'.mysql_escape_string($valeur).'"';
    $requete .= ' ORDER BY '.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
      if( $champ == 'id' )
        return new $classe($event, $db->read_assoc($req));
      else
      {
  			while($row = $db->read_assoc($req))
  			{
  				$return[$row['id']] = new $classe($event, $row);
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
		$this->nom = stripslashes($vals['nom']);
		$this->royaume = $vals['royaume']!='NULL' ? $vals['royaume'] : null;
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
    return 'event, nom, royaume, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->get_id_event().', "'.mysql_escape_string($this->get_nom()).'", '.($this->get_id_royaume()!==null?$this->get_id_royaume():'NULL').', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'event = '.$this->get_id_event().', nom = "'.mysql_escape_string($this->get_nom()).'", royaume = '.($this->get_id_royaume()!==null?$this->get_id_royaume():'NULL').', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
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
	/// Modifie le nom
	function set_nom($nom)
	{
    $this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/// Renvoie le royaume
	function get_royaume()
	{
    if( !is_object($this->royaume) and $this->royaume !== null )
      $this->royaume = new royaume($this->royaume);
    return $this->royaume;
	}
	/// Renvoie l'id du royaume
	function get_id_royaume()
	{
    if( is_object($this->royaume) )
		  return $this->royaume->get_id();
    else
      return $this->royaume;
	}
	/// Modifie le royaume
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
	}
	// @}
	
	/// Infos ajouter à la fin de la liste de membres dans l'interface admin
	function interface_admin() {}
};

/// Classe pour les équipes des DTE & RTE
class event_equipe_dte_rte extends event_equipe
{
  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $poule=null;  ///< Poule à laquelle apartient l'équipe
  protected $points_victoire=0;  ///< points dus aux résultats des matchs
  protected $nbr_meurtres=0;  ///< nombre de meurtes
  protected $points_matchs=0;  ///< points gagnés lors des matchs (système complet de points)
  /// Renvoie la poule
  function get_poule()
  {
    return $this->poule;
  }
  /// Modifie la poule
  function set_poule($poule)
  {
    $this->poule = $poule;
		$this->champs_modif[] = 'donnees';
  }
  /// Renvoie les points dus aux résultats des matchs
  function get_points_victoire()
  {
    return $this->points_victoire;
  }
  /// Modifie les points dus aux résultats des matchs
  function set_points_victoire($points)
  {
    $this->points_victoire = $points;
		$this->champs_modif[] = 'donnees';
  }
  /// Ajoute des points dus aux résultats des matchs
  function add_points_victoire($points)
  {
    $this->points_victoire = $points;
		$this->champs_modif[] = 'donnees';
  }
  /// Renvoie le nombre de meurtes
  function get_nbr_meurtres()
  {
    return $this->nbr_meurtres;
  }
  /// Modifie le nombre de meurtes
  function set_nbr_meurtres($meurtres)
  {
    $this->nbr_meurtres = $meurtres;
		$this->champs_modif[] = 'donnees';
  }
  /// Ajoute des meurtes
  function add_nbr_meurtres($meurtres)
  {
    $this->nbr_meurtres = $meurtres;
		$this->champs_modif[] = 'donnees';
  }
  /// Renvoie les points gagnés lors des matchs
  function get_points_matchs()
  {
    return $this->points_matchs;
  }
  /// Modifie les points gagnés lors des matchs
  function set_points_matchs($points)
  {
    $this->points_matchs = $points;
		$this->champs_modif[] = 'donnees';
  }
  /// Ajoute des points gagnés lors des matchs
  function add_points_matchs($points)
  {
    $this->points_matchs = $points;
		$this->champs_modif[] = 'donnees';
  }
  
	/// serialisation des données spécifiques à chaque event (à surcharger)
	protected function serializeDonnees()
  {
    $donnees = array($this->poule!==null?$this->poule:'null', $this->points_victoire, $this->nbr_meurtres, $this->points_matchs);
    return implode('|', $donnees);
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees)
  {
    if( $donnees )
    {
      $vals = explode('|', $donnees);
      $this->poule = $vals[0]!='null'?$vals[0]:null;
      $this->points_victoire = $vals[1];
      $this->nbr_meurtres = $vals[2];
      $this->points_matchs = $vals[3];
    }
  }
	// @}

	/// Infos ajoutées à la fin de la liste de membres dans l'interface admin
	function interface_admin()
  {
    if( array_key_exists('equipe', $_GET) && $_GET['equipe'] == $this->get_id() )
    {
      if( array_key_exists('poule', $_GET) )
      {
        $this->set_poule($_GET['poule']);
      }
      elseif( array_key_exists('points_victoire', $_GET) )
      {
        $this->set_points_victoire($_GET['points_victoire']);
      }
      elseif( array_key_exists('nbr_meurtres', $_GET) )
      {
        $this->set_nbr_meurtres($_GET['nbr_meurtres']);
      }
      elseif( array_key_exists('points_matchs', $_GET) )
      {
        $this->set_points_matchs($_GET['points_matchs']);
      }
      $this->sauver();
    }
?>
    <form id="equipe_<?php echo $this->get_id(); ?>" method="get" action="event.php?event=<?php echo $this->get_id_event();?>&page=equipes&equipe=<?php echo $this->get_id(); ?>">
      Poule : <input type="text" name="poule" value="<?php echo $this->get_poule(); ?>"  onchange="return envoiFormulaire('equipe_<?php echo $this->get_id(); ?>', 'contenu');" /><br />
      Points des victoires : <input type="text" name="points_victoire" value="<?php echo $this->get_points_victoire(); ?>" size="5" onchange="return envoiFormulaire('equipe_<?php echo $this->get_id(); ?>', 'contenu');" /><br />
      Nombre de meurtres : <input type="text" name="nbr_meurtres" value="<?php echo $this->get_nbr_meurtres(); ?>" size="5" onchange="return envoiFormulaire('equipe_<?php echo $this->get_id(); ?>', 'contenu');" /><br />
      Points des matchs : <input type="text" name="points_matchs" value="<?php echo $this->get_points_matchs(); ?>" size="5" onchange="return envoiFormulaire('equipe_<?php echo $this->get_id(); ?>', 'contenu');" />
    </form>
<?php
  }
};

?>
