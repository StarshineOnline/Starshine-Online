<?php
/**
 * @file event.class.php
 * Gestion des event
 * Comprend la classe principale et ses spécialisation pour les différents types d'events.
 */

// On effectue les inclusions manuellement pour pouvoir garder les spécialisations dans le même fichier que la classe mère
include_once('event_participant.class.php');

/**
 * Classe de base pour les events, liée à la table event de la base de données.
 * Il s'agit de la classe principale, elle doit être spécialisée pour chaque type d'event.
 * Il ne faut la créer directement mais utiliser les méthodes statiques create,
 * factory et nouveau qui se chargeront de crée la bonne classe fille. De même la
 * création des autres objets concernant les events doit être déléguée à cette classe
 * afin de créer les bonnes classes filles si besoin.
 */
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
		-event() qui crée un event "vide".
		-event($id) qui récupère les informations de l'event dans la base.
		-event($vals) qui l'event à partir d'information déjà récupèrées sans la base.
		-event($statut, $date_debut) qui crée un nouvel event.

		@param $id int Id de l'event dans la base.
		@param $vals array Tableau associatif contenant les entrées de la base de données.
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
		$this->date_debut = strtotime($vals['date_debut']);
		$this->date_fin = $vals['date_fin']?strtotime($vals['date_fin']):null;
		$this->unserializeDonnees($vals['donnees']);
  }
  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    switch($champ)
    {
    case 'date_debut':
      return $this->get_date_debut('Y-m-d');
    case 'date_fin':
      return $this->get_date_fin('Y-m-d');
    case 'donnees':
      return $this->serializeDonnees();
    default:
      return $this->{$champ};
    }
  }
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'type, statut, date_debut, date_fin, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.substr(get_class($this),6).'", "'.$this->statut.'", "'.$this->get_date_debut('Y-m-d').'", '.($this->date_fin!==null?'"'.$this->date_fin('Y-m-d').'"':'NULL').', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'statut = '.$this->statut.', date_debut = "'.$this->get_date_debut('Y-m-d').'", date_fin = '.($this->date_fin!==null?'"'.$this->date_fin('Y-m-d').'"':'NULL').', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
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

	/**
	 * Renvoie la date du début de l'event
	 * @param  $format   - si false, renvoie le timestamp UNIX
	 *                   - si chaîne de caractère renvoie la date suivant le format demandé (cf. fonction date)
	 */
	function get_date_debut($format=false)
	{
    if($format)
      return date($format, $this->date_debut);
    else
		  return $this->date_debut;
	}
	/// Modifie la date du début de l'event
	function set_date_debut($date_debut)
	{
    if( is_string($date_debut) )
		  $this->date_debut = strtotime($date_debut);
		else
		  $this->date_debut = $date_debut;
		$this->champs_modif[] = 'date_debut';
	}

	/**
	 * Renvoie la date de la fin de l'event
	 * @param  $format   - si false, renvoie le timestamp UNIX
	 *                   - si chaîne de caractère renvoie la date suivant le format demandé (cf. fonction date)
	 */
	function get_date_fin($format=false)
	{
    if($format)
      return date($format, $this->date_fin);
    else
		  return $this->date_fin;
	}
	/// Modifie la date de la fin de l'event
	function set_date_fin($date_fin)
	{
    if( is_string($date_fin) )
		  $this->date_fin = strtotime($date_fin);
		else
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
    // contenu
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
    
    // Modification des paramètres (statut, date de début et de fin)
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
    
    // Commandes permettant la modification des paramètres
?>
    <div class="event_bloc">
      <div class="event_minibloc">
        <ul>
<?php
    // COmmandes pour la modification du statut suivant sa valeur actuelle
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
        echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::inscriptions.'" onclick="return envoiInfo(this.href, \'contenu\');"">Permettre les inscriptions</a></li>';
      }
      break;
    case event::annonce:
      echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::inscriptions.'" onclick="return envoiInfo(this.href, \'contenu\');">Permettre les inscriptions</a></li>';
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
          <input name="date_debut" type="text" id="date_debut" onchange="return envoiFormulaire('form_date_debut', 'contenu');"/>
        </form>
      </div>
<?php
    // Date de fin s'il faut en demander une
    if($fin)
    {
?>
      <div class="event_minibloc">
        <form id="form_date_fin" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=options">
          Date de fin :
          <input name="date_fin" type="text" id="date_fin" onchange="return envoiFormulaire('form_date_debut', 'contenu');"/>
        </form>
      </div>
<?php
    }
?>
      <script type="text/javascript">
        $( "#date_debut" ).datepicker();
        $( "#date_debut" ).datepicker("option", "dateFormat", "dd/mm/yy");
        $( "#date_fin" ).datepicker();
        $( "#date_fin" ).datepicker("option", "dateFormat", "dd/mm/yy");
<?php
        if($this->get_date_debut())
        {
          echo '$( "#date_debut" ).datepicker("setDate",  new Date('.$this->get_date_debut('Y').','.($this->get_date_debut('m')-1).','.$this->get_date_debut('d').") );\n";
        }
        if($this->get_date_fin())
        {
          echo '$( "#date_fin" ).datepicker("setDate",  new Date('.$this->get_date_fin('Y').','.($this->get_date_fin('m')-1).','.$this->get_date_fin('d').") );\n";
        }
?>
      </script>
    </div>
<?php
  }

  /// Page des participants par défaut de l'interface d'administration
  function admin_participants_def()
  {
    echo '<div class="event_bloc">';
    if( array_key_exists('ajout', $_GET) )
    {
      $perso = perso::create('nom', $_GET['ajout']);
      if( count($perso) )
      {
        if( $this->get_participant('id_perso', $perso[0]->get_id()) == null )
        {
          $partic = $this->nouveau_participant($perso[0]);
          $partic->sauver();
        }
        else
          echo '<h5>Personnage déjà inscrit !</h5>';
      }
      else
        echo '<h5>Personnage inexistant !</h5>';
    }
    $this->liste_participant(true);
?>
    </div>
    <div class="event_bloc">
      <form id="event_ajout" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=participants">
        Ajouter un participant :
        <input type="text" name="ajout"/>
        <input type="submit" value="ajouter"  onclick="return envoiFormulaire('event_ajout', 'contenu');"/>
      </form>
    </div>
<?php
  }
  
  /// Interface disponible en ville
  function interface_ville()
  {
    echo '<p class="ville_haut">'.$this->get_nom().'</p>';
    if( $this->get_statut() == event::inscriptions )
    {
      $this->ville_inscription();
      echo '<hr/>';
    }
    echo '<p><span>Liste des participants :</span><br/>';
    $this->liste_participant();
    echo '</p>';
  }
  
  /**
   * Partie de l'interface permettant l'inscription
   * @param  $stars  int    Stars demandé pour l'inscription
   */
  function ville_inscription($stars=0)
  {
    global $joueur, $_GET, $_SESSION;
    if( array_key_exists('action', $_GET) && $_GET['action']=='inscrire' )
    {
      if( $joueur->get_star() >= $stars )
      {
        $partic = $this->nouveau_participant($joueur);
        $partic->sauver();
        $joueur->set_star($joueur->get_star() - $stars );
        $joueur->sauver();
        echo '<h6>Vous êtes inscrit !</h6>';
      }
      else
        echo "<h5>Vous n'avez pas assez de stars !</h5>";
      exit(0);
    }
    echo '<p>La compétition débutera le '.$this->get_date_debut('j/m/Y').' - ';
    if( $this->get_participant('id_perso', $_SESSION['ID']) )
      echo 'vous êtes inscrit</p>';
    else
    {
      echo '<a href="show_arenes.php?event='.$this->get_id().'&action=inscrire"  onclick="if(confirm(\'La participation demande d\\\'y consacrer du temps, êtes vous bien sûr de vouloir vous inscrire ?\')) { return envoiInfo(this.href, \'carte\'); } else {return false;}">s\'inscrire';
      if( $stars == 1 )
        echo ' (1 star)';
      elseif( $stars > 1 )
        echo ' ('.$stars.' stars)';
      echo '</a></p>';
    }
  }
  
  /**
   * Affiche la liste des participants
   * @param  $admin    bool             indique si c'est pour afficher dans l'interface d'adminitration
   * @param  $royaume  bool/id/string   indique s'il faut afficher les royaumes ou spécifie un royaume en particulier (TODO)
   */
  function liste_participant($admin=false, $royaume=false)
  {
    global $db;
    // On effectue une requête sur les tables "event_participant" et "perso"
    $requete = 'SELECT perso.nom';
    if($admin)
      $requete .= ', event_participant.id, event_participant.id_perso, perso.race, perso.classe';
    elseif($royaume)
      $requete .= ', perso.race';
    $requete .= ' FROM event_participant, perso WHERE event_participant.event = '.$this->get_id().' AND perso.id = event_participant.id_perso ORDER BY perso.nom';
		$req = $db->query($requete);
    // Si on affiche que le nom du participant alors on le fait sous forme de liste
    if( !$admin && !$royaume)
    {
      echo '<ul>';
      while($row = $db->read_assoc($req))
      {
        echo '<li>'.$row['nom'].'</li>';
      }
      echo '</ul>';
    }
    else
    {
      echo '<table><tbody>';
      while($row = $db->read_assoc($req))
      {
        echo '<tr><td>';
        if( $admin )
          echo '<a href="admin_joueur.php?direction=info_joueur&id='.$row['id_perso'].'">'.$row['nom'].'</a>';
        else
          echo $row['nom'];
        echo '</td><td>'.traduit($row['race']);
        if( $admin )
        {
          echo '</td><td>'.$row['classe'].'</td><td><a href="event.php?event='.$this->get_id().'&page=participants&supprimer='.$row['id'].'">supprimer</a>';
        }
        echo '</td></tr>';
      }
      echo '</tbody></table>';
    }
  }
	// @}


  /**
   * @name Informations
   * Informations sur l'event et son fonctionnement
   */
  // @{
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
	// @}

  /**
   * @name Création des autres objets
   * Gestion de la création des autres objets impliqués
   */
  // @{
  /**
   * Renvoie les participants ou un participant en particulier
   * Peut être utilisé de plusieurs façons :
   * - get_participant()                  renvoie un tableau contenant tous les participants
   * - get_participant(true)              renvoie tous les participants par équipe, sous forme de tableau associatif
   * - get_participant($champ, $valeur)   renvoie un ou plusieurs participant suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   */
  function get_participant($champ=false, $valeur=null)
  {
    return event_participant::creer($this, 'event_participant', $champ, $valeur);
  }
  /**
   * Crée un nouveau participant (à surcharger)
   * @param $perso  id du personnage ou objet le représentant
   */
  function nouveau_participant($perso)
  {
    return new event_participant($this, $perso, null);
  }
  // @}
};


/**
 * Classe gérant un DTE
 * Une équipe par royaume plus une équipe admin, s'affrontent en matchs sucessifs.
 */
class event_dte extends event
{
  /**
   * @name Informations
   * Informations sur l'event et son fonctionnement
   */
  // @{
	/// Renvoie le nom de l'event (dépend du type)
  function get_nom()
  {
    return 'DTE';
  }
	// @}

  /**
   * @name Interfaces
   * Gestion des interfaces utilisateur.
   */
  // @{
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



/**
 * Classe gérant un RTE
 * Similaire au DTE mais les équipes sont composés aléatoirement entre les différents participants.
 */
class event_rte extends event_dte
{
  /**
   * @name Informations
   * Informations sur l'event et son fonctionnement
   */
  // @{
	/// Renvoie le nom de l'event (dépend du type)
  function get_nom()
  {
    return 'RTE';
  }
  // @}
};
?>
