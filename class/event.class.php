<?php
class event extends table
{

  /**
   * @name Création
   * Gestion de la création des objets event et des classes filles.
   */
  // @{
  const types = 'dte|rte'; ///< Liste des types d'events possibles.
	/// Renvoie la liste des types d'event possible sous forme de tableau
	static function get_types()
	{
    return explode('|',event::types);
  }
  
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-event() qui crée un pnj "vide".
		-event($id) qui récupère les informations du pnj dans la base.
		-event($vals) qui récupère les informations du pnj dans la base.
		-event($statut, $date_debut) qui crée un nouvel event.

		@param $id int Id de l'event dans la base.
		@param $id array Tableau associatif contenant les entrées de la base de données.
		@param $statut String Statut de l'event (en gestation, annoncé, inscription, en cours, fini).
		@param $date_debut int Date du début de l'event.
	*/
	function __construct($statut = 0, $date_debut = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 && is_numeric($statut) )
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
			$this->date_debut = $date_debut;
			$this->date_fin = null;
			$this->unserializeDonnees('');
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
	* @param string $classe          Classe des objets à créer
	* @param array|string $champs    Champs servant a trouver les résultats
	* @param array|string  $valeurs  Valeurs servant a trouver les résultats
	* @param string  $ordre          Ordre de tri
	* @param bool|string $keys       Si false, stockage en tableau classique, si string
	*                                stockage avec sous tableau en fonction du champ $keys
	* @return array     Liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = 'SELECT * FROM event WHERE '.$where.' ORDER BY '.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
		    $classe = 'event_'.$row['type'];
				if(!$keys) $return[] = new $classe($row);
				else $return[$row[$keys]][] = new $classe($row);
			}
		}
		else $return = array();
		return $return;
	}
	
	/**
	 * Crée le bon objet en fonction du type
	 * @param $id int Id de l'event dans la base.
	 */
	static function factory($id)
	{
		global $db;
		$requete = 'SELECT * FROM event WHERE id = '.$id;
		$req = $db->query($requete);
		if( $db->num_rows($req) < 0 )
      return false;
    $row = $db->read_assoc($req);
	  $classe = 'event_'.$row['type'];

    return new $classe($row);
  }

	/**
	 * Crée un nouvel event du type donné
	 * @param $type int type de l'event à créer.
	 */
	static function nouveau($type)
	{
    $classe = 'event_'.$type;
    $obj = new $classe();
    $obj->Sauver();
    return $obj;
	}
	// @}


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
		$this->statut = $vals['statut'];
		$this->date_debut = $vals['date_debut'];
		$this->date_fin = $vals['date_fin'];
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
    return 'type, statut, date_debut, date_fin, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.substr(get_class($this),6).'", "'.$this->statut.'", '.$this->date_debut.', '.($this->date_fin!==null?$this->date_fin:'NULL').', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'statut = '.$this->statut.', date_debut = "'.$this->date_debut.'", date_fin = ."'.$this->date_fin.'", donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'event';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  const en_gestation = 0;  ///< statut indiquant que l'event est en cours de préparation.
  const annonce = 10;  ///< statut indiquant que l'event a été annoncé.
  const inscriptions = 20; ///< statut autorisant les inscrptions des joueurs.
  const en_cours = 30;  ///< statut indiquant que l'event est en cours.
  const fini = 40;  ///< statut indiquant que l'event est en fini.
  const annule = 50;  ///< statut indiquant que l'event est annulé.

  protected $statut; ///< statut de l'event
  protected $date_debut; ///< date du début de l'évènement
  protected $date_fin;  ///< date de la fin de l'évènement (s'il y en a une)
  
	/// Renvoie le statut de l'event
	function get_statut()
	{
		return $this->statut;
	}
	/// Modifie le statut de l'event
	function set_statut($statut)
	{
		$this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}
	/// Renvoie le statut de l'event sous forme textuelle
	function get_statut_nom()
	{
		switch($this->statut)
		{
	  case event::en_gestation:
      return 'en gestation';
	  case event::annonce:
      return 'annoncé';
	  case event::inscriptions:
      return 'inscriptions';
	  case event::en_cours:
      return 'en cours';
	  case event::fini:
      return 'fini';
	  case event::annule:
      return 'annulé';
    }
	}

	/// Renvoie la date du début de l'event
	function get_date_debut()
	{
		return $this->date_debut;
	}
	/// Modifie la date du début de l'event
	function set_date_debut($date_debut)
	{
		$this->date_debut = $date_debut;
		$this->champs_modif[] = 'date_debut';
	}

	/// Renvoie la date de la fin de l'event
	function get_date_fin()
	{
		return $this->date_fin;
	}
	/// Modifie la date de la fin de l'event
	function set_date_fin($date_fin)
	{
		$this->date_fin = $date_fin;
		$this->champs_modif[] = 'date_fin';
	}
	// @}


  /**
   * @name Tâches automatiques
   * Gestion des tâches automatiques.
   */
  /// Méthode appelée par le script journalier
  function journalier() {}
  /// Méthode appelée par le script horaire
  function horaire() {}
	// @}


  /**
   * @name Interfaces
   * Gestion des interfaces utilisateur.
   */
	/// Gère l'interface d'administration (à surcharger)
	function interface_admin()
	{
    global $_GET;
    
    echo '<h2>Evènement actuel : '.$this->get_nom().' - statut : '.$this->get_statut_nom().'</h2>';
    
    // menu
?>
    <div id="event_menu">
      <a href="event.php?event=<?php echo $this->get_id();?>&page=options" onclick="return envoiInfo(this.href, 'contenu');">Options</a> |
      <a href="event.php?event=<?php echo $this->get_id();?>&page=participants" onclick="return envoiInfo(this.href, 'contenu');">Participants</a>
    </div>
    <div id="event_page">
<?php
    switch( $_GET['page'] )
    {
    case 'participants':
      $this->admin_participants_def();
      break;
    default:
      $this->admin_options_def();
    }
    echo "</div>";
  }
  
  /**
   * Page des options par défaut de l'interface d'administration
   * @param $fin bool        Indique s'il faut demander la date de fin de l'évènement.
   * @param $terminer bool   Indique s'il faut l'évènement doit-être terminé manuellement.
   */
  function admin_options_def($fin = false, $terminer=false)
  {
    global $_GET;
    
    if( array_key_exists('statut', $_GET) )
    {
      $this->set_statut($_GET['statut']);
      $this->sauver();
      if( $_GET['statut'] == event::fini )
      {
        echo "Event terminé !";
        exit(0);
      }
      elseif( $_GET['statut'] == event::annule )
      {
        echo "Event annulé !";
        exit(0);
      }
    }
    if( array_key_exists('date_debut', $_GET) )
    {
      $this->set_date_debut($_GET['date_debut']);
      $this->sauver();
    }
    if( array_key_exists('date_fin', $_GET) )
    {
      $this->set_date_fin($_GET['date_fin']);
      $this->sauver();
    }
?>
    <div class="event_bloc">
      <div class="event_minibloc">
        <ul>
<?php
    switch($this->get_statut())
    {
    case event::en_gestation:
      if($fin && ($this->get_date_debut() == 0 || $this->get_date_fin() == 0))
        echo '<li>Il faut définir les dates de début et de fin !</li>';
      elseif($this->get_date_debut() == 0)
        echo '<li>Il faut définir la date de début !</li>';
      else
      {
        echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::annonce.'" onclick="return envoiInfo(this.href, \'contenu\');">Passer au statut <i>annoncé</i></a></li>';
        //echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::inscriptions.'" onclick="return envoiInfo(this.href, \'contenu\');"">Permettre les inscriptions</a></li>';
      }
      break;
    case event::annonce:
      //echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::inscriptions.'" onclick="return envoiInfo(this.href, \'contenu\');">Permettre les inscriptions</a></li>';
      break;
    case event::en_cours:
      if($terminer)
        echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::fini.'" onclick="return envoiInfo(this.href, \'contenu\');">Terminer</a></li>';
    }
?>
            <li><a href="event.php?event=<?php echo $this->get_id();?>&page=options&statut=<?php echo event::annule;?>" onclick="if(confirm('Êtes-vous sûr de vouloir annuler l\'event ?')) { return envoiInfo(this.href, 'contenu'); } else {return false;}">Annuler</a></li>
          </ul>
        </div>
      <div class="event_minibloc">
        <form id="form_date_debut" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=options">
          Date de début :
          <input name="date_debut" type="text" id="date_debut" <?php if($this->get_date_debut()!='0000-00-00') echo 'value="'.$this->get_date_debut().'"';?> onchange="return envoiFormulaire('form_date_debut', 'contenu');"/>
        </form>
      </div>
<?php
    if($fin)
    {
?>
      <div class="event_minibloc">
        <form id="form_date_fin" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=options">
          Date de fin :
          <input name="date_fin" type="text" id="date_fin" <?php if($this->get_date_fin()!='0000-00-00') echo 'value="'.$this->get_date_fin().'"';?> onchange="return envoiFormulaire('form_date_debut', 'contenu');"/>
        </form>
      </div>
<?php
    }
?>
      <script type="text/javascript">
        $( "#date_debut" ).datepicker();
        $( "#date_debut" ).datepicker("option", "dateFormat", "yy-mm-dd");
        $( "#date_fin" ).datepicker();
        $( "#date_fin" ).datepicker("option", "dateFormat", "yy-mm-dd");
      </script>
    </div>
<?php
  }

  /// Page des participants par défaut de l'interface d'administration
  function admin_participants_def()
  {
  }
	// @}

	/**
	 * Indique si une rez est possible
	 * @param $id    id du personnage.
	 * @return       true si la rez est possible, false sinon
	 */
  function rezPossible($id)
  {
    return true;
  }

	/// Renvoie le nom de l'event (dépend du type)
  function get_nom()
  {
    return substr(get_class($this),6);
  }
	
};

class event_dte extends event
{

	/// Renvoie le nom de l'event (dépend du type)
  function get_nom()
  {
    return 'DTE';
  }

  /**
   * @name Interfaces
   * Gestion des interfaces utilisateur.
   */
	/// Gère l'interface d'administration (à surcharger)
	function interface_admin()
	{
    global $_GET;

    echo '<h2>Evènement actuel : '.$this->get_nom().' - statut : '.$this->get_statut_nom().'</h2>';

    // menu
?>
    <div id="event_menu">
      <a href="event.php?event=<?php echo $this->get_id();?>&page=options" onclick="return envoiInfo(this.href, 'contenu');">Options</a>
    </div>
    <div id="event_page">
<?php
    switch( $_GET['page'] )
    {
    default:
      $this->admin_options();
    }
    echo "</div>";
  }

  /// Page des options de l'interface d'administration
  function admin_options()
  {
    $this->admin_options_def();
  }
  // @}
};

class event_rte extends event_dte
{

	/// Renvoie le nom de l'event (dépend du type)
  function get_nom()
  {
    return 'RTE';
  }
};
?>
