<?php
/**
 * @file event.class.php
 * Gestion des event
 * Comprend la classe principale et ses spécialisation pour les différents types d'events.
 */

// On effectue les inclusions manuellement pour pouvoir garder les spécialisations dans le même fichier que la classe mère
include_once('event_participant.class.php');
include_once('event_equipe.class.php');
include_once('event_partie.class.php');
include_once('arenes_joueur.class.php');

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
   * Crée le bonb objet en fonction d'un participant précis
   * @param $perso $objet/int   objet ou id du personnage participant à l'event
   */
  static function create_from_arenes_joueur($perso)
  {
    global $db;
    if( is_object($perso) )
      $perso = $perso->get_id();
    $requete = 'SELECT event FROM arenes_joueurs WHERE statut='.arenes_joueur::en_cours.' AND id_perso='.$perso;
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    if( $row && $row['event'] )
      return event::factory($row['event']);
    return null;
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
    if( $this->date_debut == 0 )
      return 0;
    elseif($format)
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
    if( $this->date_fin == 0 )
      return 0;
    elseif($format)
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
   * @name Interfaces
   * Gestion des interfaces utilisateur.
   */
	/// Gère l'interface d'administration (à surcharger)
	function interface_admin()
	{
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
   * @param $terminer bool   Indique s'il l'évènement doit-être terminé manuellement.
   */
  function admin_options_def($fin = false, $terminer=false)
  {
    // Modification des paramètres (statut, date de début et de fin)
    if( array_key_exists('statut', $_GET) )
    {
      switch( $_GET['statut'] )
      {
      case event::en_cours:
        $this->set_date_debut( time() );
        $this->demarer();
        break;
      case event::fini:
        $this->terminer();
        die('Event terminé !');
      case event::annule:
        $this->terminer(true);
        die('Event annulé !');
      default:
        $this->set_statut($_GET['statut']);
        $this->sauver();
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
        echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::en_cours.'" onclick="return envoiInfo(this.href, \'contenu\');"">Démarrer l\'event</a></li>';
      }
      break;
    case event::annonce:
      echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::inscriptions.'" onclick="return envoiInfo(this.href, \'contenu\');">Permettre les inscriptions</a></li>';
    case event::inscriptions:
        echo '<li><a href="event.php?event='.$this->get_id().'&page=options&statut='.event::en_cours.'" onclick="return envoiInfo(this.href, \'contenu\');"">Démarrer l\'event</a></li>';
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
        <input type="submit" value="ajouter" onclick="return envoiFormulaire('event_ajout', 'contenu');"/>
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
    global $joueur;
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
    // Supression d'un participant
    if( $admin && array_key_exists('supprimer', $_GET) )
    {
      $this->get_participant('id', $_GET['supprimer'])->supprimer();
    }
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
          echo '</td><td>'.$row['classe'].'</td><td><a href="event.php?event='.$this->get_id().'&page=participants&supprimer='.$row['id'].'" onclick="return envoiInfo(this.href, \'contenu\');">supprimer</a>';
        }
        echo '</td></tr>';
      }
      echo '</tbody></table>';
    }
  }
  
  /// Interface admin par défaut pour la gestion des équipes
  function admin_equipes_def()
  {
    global $db;

    // Création d'une équipe
    if( array_key_exists('nom_equipe', $_GET) )
    {
      $equipe = $this->nouvelle_equipe($_GET['nom_equipe']);
      $equipe->sauver();
    }
    // Supression d'une équipe
    elseif( array_key_exists('supprimer', $_GET) )
    {
      $this->get_equipe('id', $_GET['supprimer'])->supprimer();
    }
    // Supression d'un participant d'une équipe
    elseif( array_key_exists('virer', $_GET) )
    {
      $partic = $this->get_participant('id', $_GET['virer']);
      $partic->set_equipe(null);
      $partic->sauver();
    }
    // Ajout d'un participant à une équipe
    if( array_key_exists('participant', $_GET) && array_key_exists('equipe', $_GET) )
    {
      $partic = $this->get_participant('id', $_GET['participant']);
      if( $_GET['equipe'] )
        $partic->set_equipe($_GET['equipe']);
      else
        $partic->set_equipe(null);
      $partic->sauver();
    }
?>
    <div  class="event_bloc">
      <div  class="event_minibloc">
        <h3>Équipes</h3>
<?PHP
    // Équipes existantes
    $equipes = $this->get_equipe();
    $liste = array();
    foreach($equipes as $equipe)
    {
        echo '<div class="event_equipe"><b>'.$equipe->get_nom().'</b><table><tbody>';
        $requete = 'SELECT event_participant.id, event_participant.id_perso, perso.nom, perso.race, perso.classe FROM event_participant, perso WHERE event_participant.equipe = '.$equipe->get_id().' AND perso.id = event_participant.id_perso ORDER BY perso.nom';
		    $req = $db->query($requete);
		    if( $db->num_rows($req) < 5 )
          $liste[$equipe->get_id()] = $equipe->get_nom();
        while($row = $db->read_assoc($req))
        {
          echo '<tr><td><a href="admin_joueur.php?direction=info_joueur&id='.$row['id_perso'].'">'.$row['nom'].'</a></td>';
          echo '<td>'.traduit($row['race']).'</td><td>'.$row['classe'].'</td>';
          echo '<td><a href="event.php?event='.$this->get_id().'&page=equipes&virer='.$row['id'].'" onclick="return envoiInfo(this.href, \'contenu\');">supprimer</a></td></tr>';
        }
        echo '</table></tbody>';
        $equipe->interface_admin();
        echo '<a href="event.php?event='.$this->get_id().'&page=equipes&supprimer='.$equipe->get_id().'" onclick="return envoiInfo(this.href, \'contenu\');">supprimer</a></div>';
    }
?>
      <form id="nouv_equipe" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=equipes">
        Nouvelle équipe :
        <input type="text" name="nom_equipe"/>
        <input type="submit" value="Créer" onclick="return envoiFormulaire('nouv_equipe', 'contenu');"/>
      </form>
      </div>
      <div  class="event_minibloc">
        <h3>Participants sans équipe</h3>
        <table>
          <tbody>
<?PHP
        // Participants sans équipe
        $requete = 'SELECT event_participant.id, event_participant.id_perso, perso.nom, perso.race, perso.classe FROM event_participant, perso WHERE event_participant.event = '.$this->get_id().' AND event_participant.equipe IS NULL AND perso.id = event_participant.id_perso ORDER BY perso.nom';
		    $req = $db->query($requete);
        while($row = $db->read_assoc($req))
        {
          echo '<tr><td><a href="admin_joueur.php?direction=info_joueur&id='.$row['id_perso'].'">'.$row['nom'].'</a></td>';
          echo '<td>'.traduit($row['race']).'</td><td>'.$row['classe'].'</td><td><form id="aj_'.$row['id_perso'].'" method="get" action="event.php?event='.$this->get_id().'&page=equipes&participant='.$row['id'].'"><select name="equipe" onchange="return envoiFormulaire(\'aj_'.$row['id_perso'].'\', \'contenu\');"><option value="0"></option>';
          foreach($liste as $id=>$nom)
            echo '<option value="'.$id.'">'.$nom.'</option>';
          echo '</select></form></td></tr>';
        }
?>
          </tbody>
        </table>
      </div>
    </div>
<?PHP
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
  function rez_possible($id)
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
   * Renvoie les participants ou un participant en particulier (à surcharger)
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
  /**
   * Renvoie les équipes ou une équipe en particulier (à surcharger)
   * Peut être utilisé de plusieurs façons :
   * - get_equipe()                  renvoie un tableau contenant tous les équipes
   * - get_equipe($champ, $valeur)   renvoie un ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   */
  function get_equipe($champ=null, $valeur=null)
  {
    return event_equipe::creer($this, 'event_equipe', $champ, $valeur);
  }
  /**
   * Crée un nouvelle équipe (à surcharger)
   * @param $nom    nom de l'équipe
	 * @param $equipe  royaume, si l'équipe défend les couleurs d'un royaume, peut-être null.
   */
  function nouvelle_equipe($nom='', $royaume=null)
  {
    return new event_equipe($this, $nom, $royaume);
  }
  /**
   * Renvoie les parties ou une partie en particulier (à surcharger)
   * Peut être utilisé de plusieurs façons :
   * - get_partie()                  renvoie un tableau contenant tous les équipes
   * - get_partie($champ, $valeur)   renvoie un ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   * @param  $ordre   string    instructions pour le tri
   * @param  $limit   string    nombre max d'entrée
   */
  function get_partie($champ=null, $valeur=null, $ordre='id ASC', $limit=false)
  {
    return event_partie::creer($this, 'event_partie', $champ, $valeur, $ordre, $limit);
  }
  /**
   * Crée un nouvelle partie (à surcharger)
	 * @param $statut        int     statut de la partie.
	 * @param $heure_debut   int     heure du début de la partie.
	 * @param $heure_sso     int     heure sso au début de la partie.
	 * @param $arene         int     arène où a lieu la partie, peut-être null.
	 * @param $heure_fin     int     heure de la fin de la partie, peut-être null.
   */
  function nouvelle_partie($statut, $heure_debut=0, $heure_sso=0, $arene=null, $heure_fin=null)
  {
    return new event_partie($this, $statut, $heure_debut, $heure_sso, $arene, $heure_fin);
  }
  /**
   * Renvoie les objets arenes_joueur ou un objet en particulier (à surcharger)
   * Peut être utilisé de plusieurs façons :
   * - get_partie()                  renvoie un tableau contenant tous les équipes
   * - get_partie($champ, $valeur)   renvoie un ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   */
  function get_arenes_joueur($champ=null, $valeur=null)
  {
    return arenes_joueur::creer($this, 'arenes_joueur', $champ, $valeur);
  }
  /**
   * Crée un nouvel objet arenes_joueur (à surcharger)
	 * @param $perso       int/object  objet représentant le personnage ou id de celui-ci.
   * @param $statut      int         statut du personnage par rapport au passage dans l'arène (en attente, en cours, fini).
	 * @param $arene       int/object  arène où a lieu la partie.
	 * @param $partie      int/object  partie de l'event à laquelle correspond ce passage dans l'arène.
	 * @param $x           int         position horizontale du personnage où il doit être téléporté.
	 * @param $y           int         position verticale du personnage où il doit être téléporté.
	 * @param $groupe      int/object  groupe du personnage.
	 * @param $hp          int         hp du personnage après le téléport.
   */
  function nouveau_arenes_joueur($perso, $statut=0, $arene=0, $partie=0, $x=0, $y=0, $groupe =0, $hp=1)
  {
    return new arenes_joueur($this, $perso, $statut, $arene, $partie, $x, $y, $groupe, $hp);
  }
  // @}


  /**
   * @name Déroulement de l'event
   * Gestion du déroulement de l'event
   */
  // @{
  /// Début de l'event (à surcharger)
  function demarer()
  {
    $this->set_statut( event::en_cours );
    $this->sauver();
  }
  /**
   * Fin de l'event
   * @param $annule  bool     true si c'est une annulation, falsie si c'est la fin normale
   */
   function terminer($annule=false)
   {
      global $db;
      // on change le statut des parties
      $db->query('UPDATE event_parties SET statut='.event_partie::fini.' WHERE event='.$this->get_id().' AND statut < '.event_partie::fini);
      // On TP hors de l'arènes les personnages qui y sont encore pour l'event et on modifie les statuts
      $persos = $this->get_arenes_joueur('statut', arenes_joueur::en_cours);
      foreach($persos as $perso)
      {
        $perso->teleporte();
      }
      $db->query('UPDATE arenes_joueurs SET statut='.arenes_joueur::fini.' WHERE event='.$this->get_id().' AND statut < '.arenes_joueur::fini);
      // On change le statut de l'event
      if( $annule )
        $this->set_statut( event::annule );
      else
        $this->set_statut( event::fini );
      $this->sauver();
   }
   /// Méthode appelée par le script journalier (à surcharger)
   function journalier() {}
   /// Méthode appelée par le script horaire (à surcharger)
   function horaire() {}
   // @}
};


/**
 * Classe de base pour le DTE & RTE
 * Une équipe par royaume plus une équipe admin, s'affrontent en matchs sucessifs.
 */
abstract class event_dte_rte extends event
{

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $autorisations = array(1,1,1);  ///< indique ce qui est autorisé et ce qu ne l'est pas.
  const autor_rez = 0;  ///< indice des infos sur l'autorisation des rez.
  const autor_parch = 1;  ///< indice des infos sur l'autorisation des parchemisn et potions.
  const autor_obj_donj = 2;  ///< indice des infos sur l'autorisation des objets de donjons.
  protected $type_tournoi = 0;   ///< indique le type de tournoi (éliminatoires direct, poules)
  const eliminatoires = 0;  ///< valeur indiquant que le tournoi se fait par éliminatoires direct
  const poules3_elim = 1;   ///< valeur indiquant que le tournoi se fait par des poules de 3 suivies d'éliminatoires
  const manuel = -1;   ///< valeur indiquant que les matchs sont organisés manuellement à partir de l'interface admin
  protected $organis_match = array(1, false);  ///< organisation des matchs.
  const poules_match3 = 0;  ///< indice des infos sur les matchs à 3 dans les poules.
  const ptt_finale = 1;  ///< indice des infos sur la présence d'une petite finale.
  const pas_match3 = 0;  ///< indique qu'il n'y a pas de match à 3 dans les poules.
  const match3_prem = 1;  ///< indique que le match à 3 des poules à lieu en premier.
  const match3_der = 2;  ///< indique que le match à 3 des poules à lieu en dernier.
  protected $pa_matchs = array(100,100,50,100,50);   ///< pourcentage de PA total qu'auront les perso pour les matchs.
  const pa_finale = 0;  ///< pourcentage de PA pour la finale
  const pa_match2_poules = 1;  ///< pourcentage de PA pour les matchs à 2 en poules.
  const pa_match3_poules = 2;  ///< pourcentage de PA pour les matchs à 3 en poules.
  const pa_match2_elim = 3;  ///< pourcentage de PA pour les matchs à 2 en éliminatoires.
  const pa_match3_elim = 4;  ///< pourcentage de PA pour les matchs à 3 en éliminatoires.
  protected $victoire_match = 0;  ///< indique les conditions à réunir pour gagner un match.
  const dernier_vie = 0;   ///< le gagnant du match est celui qui a les derniers participants en vie.
  const nbr_meurtres = 1;   ///< le gagnant du match est celui qui a fait le plus de mort.
  const nbr_points = 2;   ///< le gagnant du match est celui qui a le plus de points.
  protected $victoire_poule = 0;  ///< indique les conditions déterminant le classement au sein des poules.
  const points_victoire = 0;  ///< seuls les points dus aux résultat des matchs comptent pour le classement dans la poule.
  const points_totaux = 1;   ///< l'intégralité des points gagnés comptent pour le classement dans la poule.
  const vict_poule_manuel = 2;   ///< le classement au sein d'une poule est défini manuellement.
  protected $points = array(3, 1, 0, 2, 1, .5, -.25, 1, 1, 1, 0, 1, -1, 0, -1, -1);  ///< points donnés par les différentes actions.
  const points_vict_match3 = 0;  ///< indice des points donnés par une victoire dans un match à 3.
  const points_deux_match3 = 1;  ///< indice des points donnés au deuxième dans un match à 3.
  const points_defaite_match3 = 2;  ///< indice des points donnés par une défaite dans un match à 3.
  const points_vict_match2 = 3;  ///< indice des points donnés par une victoire dans un match à 2.
  const points_defaite_match2 = 4;  ///< indice des points donnés par une défaite dans un match à 2.
  const points_meurtre = 5;  ///< indice des points donnés pour la mort d'un adversaire.
  const points_mort = 6;  ///< indice des points donnés pour la mort d'un membre de l'équipe.
  const points_prem_meurtre = 7;    ///< indice des points donnés pour le premier meurtre d'un adversaire.
  const points_1vs1 = 8;    ///< indice des points donnés si le match se tremine en 1 vs 1.
  const points_pas_blesse_tot = 9;   ///< indice des points donnés si au moins un membre de l'équipe n'a pas été blessé.
  const points_pas_blesse_chaque = 10;   ///< indice des points donnés par membre de l'équipe n'a pas été blessé.
  const points_retourn_match3 = 11;   ///< indice des points donnés si l'équipe s'est retrouvée en infériorité numerique face à la 2ème équipe au moment où la 3ème équipe a été éliminée et qu'elle a quand même remporté la victoire.
  const points_agit_pas_tot = 12;   ///< indice des points donnés si au moins un membre de l'équipe n'a pas agit à part des déplacement.
  const points_agit_pas_chaque = 13;   ///< indice des points donnés par membre de l'équipe qui n'a pas agit à part des déplacement.
  const points_plus48h = 14;   ///< indice des points donnés si le match dure plus de 48h.
  const points_attaque_1seule = 15;  ///< indice des points donnés si l'équipe n'attaque qu'une seule autre équipe lors d'un match à 3.
  protected $options_matchs = array(1, 1, 0);   ///< options des matchs
  const match_tp_auto = 0;  ///< indice du TP automatique.
  const match_bloque_avant = 1;    ///< indice du blocage pré-match.
  const match_temps_buff = 2;   ///< indice du temps laissé pour le buffage.
	/**
	 * Indique si une rez est possible (pour un personnage donné)
	 * @param $id    id du personnage.
	 * @return       true si la rez est possible, false sinon
	 */
  function rez_possible($id)
  {
    return $this->get_autorisations(event_dte_rte::autor_rez);
  }
  /// Indique les autorisations
  function get_autorisations($ind)
  {
    return $this->autorisations[$ind];
  }
  /// Définit les autorisations
  function set_autorisations($ind, $val)
  {
    $this->autorisations[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique le type de tournoi
  function get_type_tournoi()
  {
    return $this->type_tournoi;
  }
  /// Définit le type de tournoi
  function set_type_tournoi($type_tournoi)
  {
    $this->type_tournoi = $type_tournoi;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique les options concernant l'organisations des matchs
  function get_organis_match($ind)
  {
    return $this->organis_match[$ind];
  }
  /// Définit les options concernant l'organisations des matchs
  function set_organis_match($ind, $val)
  {
    $this->organis_match[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique le pourcentage de PA au début des matchs
  function get_pa_matchs($ind)
  {
    return $this->pa_matchs[$ind];
  }
  /// Définit le pourcentage de PA au début des matchs
  function set_pa_matchs($ind, $val)
  {
    $this->pa_matchs[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique les conditions à réunir pour gagner un match
  function get_victoire_match()
  {
    return $this->victoire_match;
  }
  /// Définit les conditions à réunir pour gagner un match.
  function set_victoire_match( $val)
  {
    $this->victoire_match = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique les conditions déterminant le calssement au sein des poules
  function get_victoire_poule()
  {
    return $this->victoire_poule;
  }
  /// Définit les conditions déterminant le calssement au sein des poules
  function set_victoire_poule($val)
  {
    $this->victoire_poule = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique points donnés par les différentes actions
  function get_points($ind)
  {
    return $this->points[$ind];
  }
  /// Définit points donnés par les différentes actions
  function set_points($ind, $val)
  {
    $this->points[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  /// Indique les options des matchs
  function get_options_matchs($ind)
  {
    return $this->options_matchs[$ind];
  }
  /// Définit les options des matchs
  function set_options_matchs($ind, $val)
  {
    $this->options_matchs[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  
	/// sérialisation des données spécifiques à chaque event (à surcharger)
	protected function serializeDonnees()
  {
    $donnees = array(implode(',', $this->autorisations), $this->get_type_tournoi(),
      implode(',', $this->organis_match), implode(',', $this->pa_matchs), $this->victoire_match,
      $this->victoire_poule, implode(',', $this->points), implode(',', $this->options_matchs));
    return implode('|', $donnees);
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees)
  {
    if( $donnees )
    {
      $vals = explode('|', $donnees);
      $this->autorisations = explode(',', $vals[0]);
      $this->type_tournoi = $vals[1];
      $this->organis_match = explode(',', $vals[2]);
      $this->pa_matchs = explode(',', $vals[3]);
      $this->victoire_match = $vals[4];
      $this->victoire_poule = $vals[5];
      $this->points = explode(',', $vals[6]);
      $this->options_matchs = explode(',', $vals[7]);
    }
  }
	// @}

  /**
   * @name Création des autres objets
   * Gestion de la création des autres objets impliqués
   */
  // @{
  /**
   * Renvoie les équipes ou une équipe en particulier
   * Peut être utilisé de plusieurs façons :
   * - get_equipe()                  renvoie un tableau contenant tous les équipes
   * - get_equipe($champ, $valeur)   renvoie un ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   */
  function get_equipe($champ=null, $valeur=null)
  {
    return event_equipe::creer($this, 'event_equipe_dte_rte', $champ, $valeur);
  }
  /**
   * Crée un nouvelle équipe
   * @param $nom    nom de l'équipe
	 * @param $equipe  royaume, si l'équipe défend les couleurs d'un royaume, peut-être null.
   */
  function nouvelle_equipe($nom='', $royaume=null)
  {
    return new event_equipe_dte_rte($this, $nom, $royaume);
  }
  /**
   * Renvoie les parties ou une partie en particulier (à surcharger)
   * Peut être utilisé de plusieurs façons :
   * - get_partie()                  renvoie un tableau contenant tous les équipes
   * - get_partie($champ, $valeur)   renvoie un ou plusieurs équipes suivant la valeur d'un champ précis
   *
   * @param  $champ   string    champ
   * @param  $valeur  string    valeur du champ demandée
   * @param  $ordre   string    instructions pour le tri
   * @param  $limit   string    nombre max d'entrée
   */
  function get_partie($champ=null, $valeur=null, $ordre='id ASC', $limit=false)
  {
    return event_partie::creer($this, 'event_partie_dte_rte', $champ, $valeur, $ordre, $limit);
  }
  /**
   * Crée un nouvelle partie (à surcharger)
	 * @param $statut        int     statut de la partie.
	 * @param $heure_debut   int     heure du début de la partie.
	 * @param $heure_sso     int     heure sso au début de la partie.
	 * @param $arene         int     arène où a lieu la partie, peut-être null.
	 * @param $heure_fin     int     heure de la fin de la partie, peut-être null.
   */
  function nouvelle_partie($statut, $heure_debut=0, $heure_sso=0, $arene=null, $heure_fin=null)
  {
    return new event_partie_dte_rte($this, $statut, $heure_debut, $heure_sso, $arene, $heure_fin);
  }
	// @}

  /**
   * @name Interfaces
   * Gestion des interfaces utilisateur.
   */
  // @{
	/// Gère l'interface d'administration commun au DTE et RTE
	function interface_admin()
	{
    echo '<h2>Evènement actuel : '.$this->get_nom().' - statut : '.$this->get_statut_nom().'</h2>';

    // menu
?>
    <div id="event_menu">
      <a href="event.php?event=<?php echo $this->get_id();?>&page=options" onclick="return envoiInfo(this.href, 'contenu');">Options</a> |
      <a href="event.php?event=<?php echo $this->get_id();?>&page=participants" onclick="return envoiInfo(this.href, 'contenu');">Participants</a> |
      <a href="event.php?event=<?php echo $this->get_id();?>&page=equipes" onclick="return envoiInfo(this.href, 'contenu');">Équipes</a> |
      <a href="event.php?event=<?php echo $this->get_id();?>&page=matchs" onclick="return envoiInfo(this.href, 'contenu');">Matchs</a>
    </div>
    <div id="event_page">
<?php
    switch( $_GET['page'] )
    {
    case 'participants':
      $this->admin_participants_def();
      break;
    case 'equipes':
      $this->admin_equipes_def();
      break;
    case 'matchs':
      $this->admin_matchs();
      break;
    default:
      $this->admin_options();
    }
    echo "</div>";
  }

  /// Page des options de l'interface d'administration
  function admin_options()
  {
    $modifie = false;
    $this->admin_options_def(false, true);
    // Modification des options
    if( array_key_exists('action', $_GET) && $_GET['action']=='options' )
    {
      $this->set_autorisations(event_dte_rte::autor_rez, array_key_exists('rez_autor', $_POST)?1:0);
      $this->set_autorisations(event_dte_rte::autor_parch, array_key_exists('parch_autor', $_POST)?1:0);
      $this->set_autorisations(event_dte_rte::autor_obj_donj, array_key_exists('obj_donj_autor', $_POST)?1:0);
      $this->set_type_tournoi($_POST['type_tournoi']);
      $this->set_organis_match(event_dte_rte::poules_match3, $_POST['poules_match3']);
      $this->set_organis_match(event_dte_rte::ptt_finale, $_POST['ptt_finale']?1:0);
      $this->set_pa_matchs(event_dte_rte::pa_match2_poules, $_POST['pa_match2_poules']);
      $this->set_pa_matchs(event_dte_rte::pa_match3_poules, $_POST['pa_match3_poules']);
      $this->set_pa_matchs(event_dte_rte::pa_match2_elim, $_POST['pa_match2_elim']);
      $this->set_pa_matchs(event_dte_rte::pa_match3_elim, $_POST['pa_match3_elim']);
      $this->set_pa_matchs(event_dte_rte::pa_finale, $_POST['pa_finale']);
      $this->set_victoire_match($_POST['victoire_match']);
      $this->set_victoire_poule($_POST['victoire_poule']);
      $this->set_points(event_dte_rte::points_vict_match2, $_POST['points_vict_match2']);
      $this->set_points(event_dte_rte::points_defaite_match2, $_POST['points_defaite_match2']);
      $this->set_points(event_dte_rte::points_vict_match3, $_POST['points_vict_match3']);
      $this->set_points(event_dte_rte::points_deux_match3, $_POST['points_deux_match3']);
      $this->set_points(event_dte_rte::points_defaite_match3, $_POST['points_defaite_match3']);
      $this->set_options_matchs(event_dte_rte::match_tp_auto, array_key_exists('match_tp_auto', $_POST)?1:0);
      $this->set_options_matchs(event_dte_rte::match_bloque_avant, array_key_exists('match_bloque_avant', $_POST)?1:0);
      $this->set_options_matchs(event_dte_rte::match_temps_buff, $_POST['match_temps_buff']);
      //$this->set_($_POST['']);
      $modifie = true;
    }
?>
    <hr class="event_sep" />
    <div class="event_bloc">
      <form id="event_options" method="post" action="event.php?event=<?php echo $this->get_id();?>&page=options&action=options">
        <div class="event_minibloc">
          Type de tournoi : <select name="type_tournoi"><option value="0" <?PHP if($this->get_type_tournoi()==0) echo 'selected="selected"'; ?> >éliminatoires</option><option value="1" <?PHP if($this->get_type_tournoi()==1) echo 'selected="selected"'; ?> >poules de 3 puis éliminatoires</option><option <?PHP if($this->get_type_tournoi()==-1) echo 'selected="selected"'; ?> value="-1">matchs définis à la main</option></select><br />
          Match à 3 pour les poules de 3 : <select name="poules_match3"><option value="1"  <?PHP if($this->get_organis_match(event_dte_rte::poules_match3)==1) echo 'selected="selected"'; ?>>en premier</option><option value="2" <?PHP if($this->get_organis_match(event_dte_rte::poules_match3)==2) echo 'selected="selected"'; ?>>en dernier</option><option value="0" <?PHP if($this->get_organis_match(event_dte_rte::poules_match3)==0) echo 'selected="selected"'; ?>>aucun</option></select><br />
          Pourcentage de PA pour les matchs de poules à 2 : <input type="text" name="pa_match2_poules" value="<?php echo $this->get_pa_matchs(event_dte_rte::pa_match2_poules); ?>" size="5" /><br />
          Pourcentage de PA pour les matchs de poules à 3 : <input type="text" name="pa_match3_poules" value="<?php echo $this->get_pa_matchs(event_dte_rte::pa_match3_poules); ?>" size="5" /><br />
          Pourcentage de PA pour les matchs d'éliminatoires à 2 : <input type="text" name="pa_match2_elim" value="<?php echo $this->get_pa_matchs(event_dte_rte::pa_match2_elim); ?>" size="5" /><br />
          Pourcentage de PA pour les matchs d'éliminatoires à 3 : <input type="text" name="pa_match3_elim" value="<?php echo $this->get_pa_matchs(event_dte_rte::pa_match3_elim); ?>" size="5" /><br />
          Pourcentage de PA pour la finale : <input type="text" name="pa_finale" value="<?php echo $this->get_pa_matchs(event_dte_rte::pa_finale); ?>" size="5" /><br />
          <input type="checkbox" name="ptt_finale" <?php if($this->get_organis_match(event_dte_rte::ptt_finale)) echo 'checked="checked"';?> />Petite finale<br />
          <input type="checkbox" name="rez_autor" <?php if($this->get_autorisations(event_dte_rte::autor_rez)) echo 'checked="checked"';?> />Rez autorisées<br />
          <input type="checkbox" name="parch_autor" <?php if($this->get_autorisations(event_dte_rte::autor_parch)) echo 'checked="checked"';?> />Parchemins et potions autorisés<br />
          <input type="checkbox" name="obj_donj_autor" <?php if($this->get_autorisations(event_dte_rte::autor_obj_donj)) echo 'checked="checked"';?> />Objets de donjons autorisés
        </div>
        <div class="event_minibloc">
          Conditions à réunir pour gagner un match :<select name="victoire_match"><option value="0"  <?PHP if($this->get_victoire_match()==0) echo 'selected="selected"'; ?>>dernier en vie</option><option value="1" <?PHP if($this->get_victoire_match()==1) echo 'selected="selected"'; ?>>nombre de meurtres</option><option value="2" <?PHP if($this->get_victoire_match()==2) echo 'selected="selected"'; ?>>points</option></select><br />
          Conditions déterminant le classement au sein des poules :<select name="victoire_poule"><option value="0"  <?PHP if($this->get_victoire_poule()==0) echo 'selected="selected"'; ?>>points des victoires</option><option value="1" <?PHP if($this->get_victoire_poule()==1) echo 'selected="selected"'; ?>>points des matchs</option><option value="2" <?PHP if($this->get_victoire_poule()==2) echo 'selected="selected"'; ?>>manuel</option></select><br />
          Points en cas de victoire dans un match à 2 : <input type="text" name="points_vict_match2" value="<?php echo $this->get_points(event_dte_rte::points_vict_match2); ?>" size="5" /><br />
          Points en cas de défaite dans un match à 2 : <input type="text" name="points_defaite_match2" value="<?php echo $this->get_points(event_dte_rte::points_defaite_match2); ?>" size="5" /><br />
          Points en cas de victoire dans un match à 3 : <input type="text" name="points_vict_match3" value="<?php echo $this->get_points(event_dte_rte::points_vict_match3); ?>" size="5" /><br />
          Points pour le second dans un match à 3 : <input type="text" name="points_deux_match3" value="<?php echo $this->get_points(event_dte_rte::points_deux_match3); ?>" size="5" /><br />
          Points en cas de défaite dans un match à 3 : <input type="text" name="points_defaite_match3" value="<?php echo $this->get_points(event_dte_rte::points_defaite_match3); ?>" size="5" />
        </div>
        <div class="event_minibloc">
          <input type="checkbox" name="match_tp_auto" <?php if($this->get_options_matchs(event_dte_rte::match_tp_auto)) echo 'checked="checked"';?> />TP automatique<br />
          <input type="checkbox" name="match_bloque_avant" <?php if($this->get_options_matchs(event_dte_rte::match_bloque_avant)) echo 'checked="checked"';?> />Blocage pré-match<br />
          Temps laissé pour le buffage : <input type="text" name="match_temps_buff" value="<?php echo $this->get_options_matchs(event_dte_rte::match_temps_buff); ?>" size="5" /> min.
        </div>
<?php
        $this->admin_options_spec();
?>
        <input class="event_submit" type="submit" onclick="return envoiFormulaire('event_options', 'contenu');"/>
      </form>
    </div>
<?PHP
    if( $modifie )
      $this->sauver();
  }
	/// Gère l'interface d'administration spécifique au DTE et au RTE (à spécialiser)
	abstract function admin_options_spec();

  /// Page des matchs de l'interface d'administration
  function admin_matchs()
  {
    $equipes = $this->get_equipe();
    $arenes = arene::create(0, 0);
    
    if( array_key_exists('nouveau', $_GET) )
    {
      if( count($equipes) < 2 )
        die('<h5>Il doit y a voir au moins 2 équipes pour créer des matchs !');
      if( ($_GET['nouveau'] == event_partie_dte_rte::match3_poule || $_GET['nouveau'] == event_partie_dte_rte::match3_elim) && count($equipes) < 3 )
        die('<h5>Il doit y a voir au moins 3 équipes pour créer des matchs à 3 !');
      $nouv = $this->nouvelle_partie(event_partie::a_venir);
      $nouv->set_type($_GET['nouveau']);
      // Arène et heure SSO au hasard
      $num_arene = rand(0, count($arenes)-1);
      $nouv->set_arene( $arenes[$num_arene] );
      switch( rand(0,3) )
      {
      case 0:
        $nouv->set_heure_sso( (6+rand(0,3))*60 );
        break;
      case 1:
        $nouv->set_heure_sso( (10+rand(0,5))*60 );
        break;
      case 2:
        $nouv->set_heure_sso( (16+rand(0,3))*60 );
        break;
      case 3:
        $nouv->set_heure_sso( (20+rand(0,9))%24*60 );
        break;
      }
      $nouv->sauver();
    }
    elseif( array_key_exists('action', $_GET) )
    {
      $match = $this->get_partie('id', $_GET['match']);
      switch($_GET['action'])
      {
      case 'supprimer':
        $match->supprimer();
      break;
      case 'modifier':
        if( $match->get_statut() < event_partie::en_cours )
        {
          $match->set_arene($_POST['arene']);
          $heure_sso = explode(':', $_POST['heure_sso']);
          $match->set_heure_sso( $heure_sso[0]*60+$heure_sso[1] );
        }
        // Heure du début
        if($match->get_statut() != event_partie::en_cours)
        {
          $heure_debut = explode(' ', $_POST['heure_debut']);
          $date = explode('/', $heure_debut[0]);
          $heure = explode(':', $heure_debut[1]);
          $match->set_heure_debut( mktime($heure[0], $heure[1], 0, $date[1], $date[0], $date[2]) );
        }
        // Heure de fin
        if( $_POST['heure_fin'] )
        {
          $heure_fin = explode(' ', $_POST['heure_fin']);
          $date = explode('/', $heure_fin[0]);
          $heure = explode(':', $heure_fin[1]);
          $match->set_heure_fin( mktime($heure[0], $heure[1], 0, $date[1], $date[0], $date[2]) );
        }
        // autres
        if( array_key_exists('gagnant', $_POST) )
          $match->set_gagnant( $_POST['gagnant'] );
        if( array_key_exists('second', $_POST) )
          $match->set_second( $_POST['second'] );
        if( $_POST['equipe0'] == $_POST['equipe1'] )
          die('<h5>Une équipe ne peut pas jouer contre elle-même !</h5>');
        $match->set_participant(0, $_POST['equipe0']);
        $match->set_participant(1, $_POST['equipe1']);
        $match->set_nbr_meurtres(0, $_POST['nbr_meurtres0']);
        $match->set_nbr_meurtres(1, $_POST['nbr_meurtres1']);
        $match->set_points_matchs(0, $_POST['points_matchs0']);
        $match->set_points_matchs(1, $_POST['points_matchs1']);
        if( array_key_exists('equipe2', $_POST) )
        {
          if( $_POST['equipe2'] == $_POST['equipe0'] || $_POST['equipe2'] == $_POST['equipe1'] )
            die('<h5>Une équipe ne peut pas jouer contre elle-même !</h5>');
          $match->set_participant(2, $_POST['equipe2']);
          $match->set_nbr_meurtres(2, $_POST['nbr_meurtres2']);
          $match->set_points_matchs(2, $_POST['points_matchs2']);
        }
        $match->sauver();
      }
    }
    elseif( array_key_exists('statut', $_GET) )
    {
      $match = $this->get_partie('id', $_GET['match']);
      switch($_GET['statut'])
      {
      case event_partie::en_cours:
        $match->set_statut( $_GET['statut'] );
        $match->set_heure_debut( time() );
        $arene = $match->get_arene();
        $arene->ouvrir();
        $match->sauver();
        break;
      case event_partie::fini:
        $match->terminer();
      }
    }
    
    $matchs = $this->get_partie();
    

    echo '<p><i>Rappel pour l\'heure SSO : 6-9h = matin, 10-15h = journée, 16-19h = soir, 20-5h = nuit</i></p>';
    
    // Affichage des matchs
    foreach($matchs as $match)
    {
?>
      <div class="event_bloc">
<?PHP
        switch($match->get_type())
        {
        case event_partie_dte_rte::match2_poule:
          echo '<b>Match #'.$match->get_id().'</b> - match de poule à 2';
          break;
        case event_partie_dte_rte::match3_poule:
          echo '<b>Match #'.$match->get_id().'</b> - match de poule à 3';
          break;
        case event_partie_dte_rte::match2_elim:
          echo '<b>Match #'.$match->get_id().'</b> - match d\'éliminatoire à 2';
          break;
        case event_partie_dte_rte::match3_elim:
          echo '<b>Match #'.$match->get_id().'</b> - match d\'éliminatoire à 3';
          break;
        case event_partie_dte_rte::finale:
          echo '<b>Match #'.$match->get_id().'</b> - finale';
          break;
        }
?>
        <form  id="match_<?PHP echo $match->get_id(); ?>" method="post" action="event.php?event=<?php echo $this->get_id();?>&page=matchs&match=<?PHP echo $match->get_id(); ?>&action=modifier">
          <div class="event_minibloc">
<?PHP
        switch($match->get_statut())
        {
        case event_partie::a_venir:
          echo '<a href="event.php?event='.$this->get_id().'&page=matchs&match='.$match->get_id().'&statut='.event_partie::en_cours.'" onclick="return envoiInfo(this.href, \'contenu\');">Démarer</a><br />';
          break;
        case event_partie::en_cours:
          echo '<a href="event.php?event='.$this->get_id().'&page=matchs&match='.$match->get_id().'&statut='.event_partie::fini.'" onclick="return envoiInfo(this.href, \'contenu\');">Terminer</a><br />';
          break;
        }
?>
            Arènes : <select name="arene">
<?PHP
        foreach($arenes as $arene)
        {
          echo '<option value="'.$arene->get_id().'"';
          if( $match->get_id_arene() == $arene->get_id() )
            echo ' selected="selected"';
          echo '>'.$arene->get_nom().'</option>';
        }
?>
            </select><br />
            Date de début : <input type="text" id="heure_debut_<?PHP echo $match->get_id(); ?>" name="heure_debut" /><br />
            Heure SSO : <input type="text" id="heure_sso_<?PHP echo $match->get_id(); ?>" name="heure_sso" /><br />
            Date de fin : <input type="text" id="heure_fin_<?PHP echo $match->get_id(); ?>" name="heure_fin" /><br />
<?PHP
        if( $match->get_statut() == event_partie::fini )
        {
          echo 'Gagnant : <select name="gagnant"><option value="0">non défini</option>';
          $id0 = $match->get_participant(0);
          echo '<option value="'.$id0.'"'.($match->get_gagnant()==$id0?' selected="selected"':'').'>'.$equipes[$id0]->get_nom().'</option>';
          $id1 = $match->get_participant(1);
          echo '<option value="'.$id1.'"'.($match->get_gagnant()==$id1?' selected="selected"':'').'>'.$equipes[$id1]->get_nom().'</option>';
          if( $match->get_type() == event_partie_dte_rte::match3_poule || $match->get_type() == event_partie_dte_rte::match3_elim )
          {
            $id2 = $match->get_participant(2);
            echo '<option value="'.$id2.'"'.($match->get_gagnant()==$id2?' selected="selected"':'').'>'.$equipes[$id2]->get_nom().'</option>';
            echo '</select><br />Second : <select name="second"><option value="0">non défini</option><option value="'.$id0.'"'.($match->get_second()==$id0?' selected="selected"':'').'>'.$equipes[$id0]->get_nom().'</option><option value="'.$id1.'"'.($match->get_second()==$id1?' selected="selected"':'').'>'.$equipes[$id1]->get_nom().'</option><option value="'.$id2.'"'.($match->get_second()==$id2?' selected="selected"':'').'>'.$equipes[$id2]->get_nom().'</option>';
          }
          echo '</select><br />';
        }
?>
            <a href="event.php?event=<?PHP echo $this->get_id(); ?>&page=matchs&match=<?PHP echo $match->get_id(); ?>&action=supprimer"  onclick="return envoiInfo(this.href, 'contenu');">Supprimer</a>
            <script type='text/javascript'>
              $('#heure_debut_<?PHP echo $match->get_id(); ?>').datetimepicker();
              $('#heure_sso_<?PHP echo $match->get_id(); ?>').timepicker({});
              $('#heure_fin_<?PHP echo $match->get_id(); ?>').datetimepicker();
<?php
        if($match->get_heure_debut())
        {
          echo '$( "#heure_debut_'.$match->get_id().'" ).datetimepicker("setDate",  new Date('.date('Y', $match->get_heure_debut()).','.(date('m', $match->get_heure_debut())-1).','.date('d', $match->get_heure_debut()).', '.date('H', $match->get_heure_debut()).', '.date('i', $match->get_heure_debut()).', '.date('s', $match->get_heure_debut()).") );\n";
        }
        if($match->get_heure_fin())
        {
          echo '$( "#heure_fin_'.$match->get_id().'" ).datetimepicker("setDate",  new Date('.date('Y', $match->get_heure_fin()).','.(date('m', $match->get_heure_fin())-1).','.date('d', $match->get_heure_fin()).', '.date('H', $match->get_heure_fin()).', '.date('i', $match->get_heure_fin()).', '.date('s', $match->get_heure_fin()).") );\n";
        }
        echo '$( "#heure_sso_'.$match->get_id().'" ).datetimepicker("setDate",  new Date(0,0,0, '.($match->get_heure_sso()/60).', '.($match->get_heure_sso()%60).", 0) );\n";
?>
            </script>
          </div>
          <div class="event_minibloc">
            Équipe 1 : <select name="equipe0">
<?PHP
      foreach($equipes as $equipe)
      {
        echo '<option value="'.$equipe->get_id().'"';
        if( $match->get_nbr_participant() > 0 && $equipe->get_id() == $match->get_participant(0) )
          echo ' selected="selected"';
        echo '>'.$equipe->get_nom().'</option>';
      }
?>
            </select><br />
            Nombre de meurtres : <input type="text" size="5" name="nbr_meurtres0" value="<?PHP echo $match->get_nbr_meurtres(0); ?>" /><br />
            Points remportés (hors victoire) : <input type="text" size="5" name="points_matchs0" value="<?PHP echo $match->get_points_matchs(0); ?>" /><br />
            </select>
          </div>
          <div class="event_minibloc">
            Équipe 2 : <select name="equipe1">
<?PHP
      foreach($equipes as $equipe)
      {
        echo '<option value="'.$equipe->get_id().'"';
        if( $match->get_nbr_participant() > 1 && $equipe->get_id() == $match->get_participant(1) )
          echo ' selected="selected"';
        echo '>'.$equipe->get_nom().'</option>';
      }
?>
            </select><br />
            Nombre de meurtres : <input type="text" size="5" name="nbr_meurtres1" value="<?PHP echo $match->get_nbr_meurtres(1); ?>" /><br />
            Points remportés (hors victoire) : <input type="text" size="5" name="points_matchs1" value="<?PHP echo $match->get_points_matchs(1); ?>" /><br />
            </select>
          </div>
<?PHP
      if( $match->get_type() == event_partie_dte_rte::match3_poule || $match->get_type() == event_partie_dte_rte::match3_elim )
      {
?>
          <div class="event_minibloc">
            Équipe 3 : <select name="equipe2">
<?PHP
        foreach($equipes as $equipe)
        {
          echo '<option value="'.$equipe->get_id().'"';
          if( $match->get_nbr_participant() > 2 && $equipe->get_id() == $match->get_participant(2) )
            echo ' selected="selected"';
          echo '>'.$equipe->get_nom().'</option>';
        }
?>
            </select><br />
            Nombre de meurtres : <input type="text" size="5" name="nbr_meurtres2" value="<?PHP echo $match->get_nbr_meurtres(2); ?>" /><br />
            Points remportés (hors victoire) : <input type="text" size="5" name="points_matchs2" value="<?PHP echo $match->get_points_matchs(2); ?>" /><br />
            </select>
          </div>
<?PHP
      }
?>
          <div class="event_minibloc">
            <input type="submit" onclick="return envoiFormulaire('match_<?PHP echo $match->get_id(); ?>', 'contenu');"/>
          </div>
        </form>
      </div>
<?PHP
    }
?>
      <div class="event_bloc">
        <form  id="nouv_match" method="get" action="event.php?event=<?php echo $this->get_id();?>&page=matchs">
        Nouveau match : <select name="nouveau"><option value="0">match de poule à 2</option><option value="1">match de poule à 3</option><option value="2">match d'éliminatoire à 2</option><option value="3">match d'éliminatoire à 3</option><option value="4">finale</option></select>
        <input type="submit" value="Créer" onclick="return envoiFormulaire('nouv_match', 'contenu');"/>
      </div>
<?PHP
  }

  /// Interface disponible en ville
  function interface_ville()
  {
    echo '<p class="ville_haut">'.$this->get_nom().'</p>';
    if( $this->get_statut() == event::inscriptions )
    {
      $this->ville_inscription();
      echo '<hr/><p><span>Liste des participants :</span><br/>';
      $this->liste_participant();
      echo '</p>';
      return;
    }
    
    // matchs en cours
    $matchs = $this->get_partie('statut', event_partie::en_cours);
    $arenes_matchs = array();
    if( $matchs )
    {
      echo '<p><b>Matchs en cours</b><ul>';
      foreach($matchs as $match)
      {
        $equipe0 = $this->get_equipe('id', $match->get_participant(0));
        $equipe1 = $this->get_equipe('id', $match->get_participant(1));
        echo '<li>'.$equipe0->get_nom().' vs '.$equipe1->get_nom();
        if( $match->get_type() == event_partie_dte_rte::match3 )
        {
          $equipe2 = $this->get_equipe('id', $match->get_participant(2));
          echo ' vs '.$equipe2->get_nom();
        }
        $arene = $match->get_arene();
        echo ' : <a href="show_arenes.php?nom_arene='.$arene->get_nom().'">'.$arene->get_nom().'</a></li>';
        $arenes_matchs[] = $arene->get_id();
      }
      echo '</ul></p>';
    }
    
    // On regarde s'il y a d'autre arènes ouvertes
    $arenes = arene::create('open', 1);
    if( count($arenes) > count($matchs) )
    {
      if( $matchs )
        echo '<p><b>Autres arènes ouvertes</b><ul>';
      else
        echo '<p><b>Arènes ouvertes</b><ul>';
      foreach($arenes as $arene)
      {
        if( !in_array($arene->get_id(), $arenes_matchs) )
        {
          echo '<li><a href="show_arenes.php?nom_arene='.$arene->get_nom().'">'.$arene->get_nom().'</a></li>';
        }
      }
      echo '</ul></p>';
    }
    
    // matchs terminés récements
    $matchs = $this->get_partie('statut', event_partie::fini, 'heure_fin DESC', 3);
    if( $matchs )
    {
      echo '<p><b>Matchs terminés</b><ul>';
      foreach($matchs as $match)
      {
        $partic = $match->get_participants();
        $gagnant = $this->get_equipe('id', $match->get_gagnant());
        $partic = array_diff($partic, array($gagnant->get_id()));
        echo '<li>1° : '.$gagnant->get_nom();
        $p = 2;
        if( $match->get_type() == event_partie_dte_rte::match3 )
        {
          $p++;
          $second = $this->get_equipe('id', $match->get_second());
          $partic = array_diff($partic, array($second->get_id()));
          echo ' - 2° : '.$second->get_nom();
        }
        $arene = $match->get_arene();
        $perdant = $this->get_equipe('id', array_pop($partic));
        echo ' - '.$p.'° : '.$perdant->get_nom().' ('.$arene->get_nom().')';
      }
      echo '</ul></p>';
    }

    // Prochains matchs
    $matchs = $this->get_partie('statut', event_partie::a_venir, 'heure_debut ASC', 2);
    if( $matchs )
    {
      echo '<p><b>Prochains matchs</b><ul>';
      foreach($matchs as $match)
      {
        $equipe0 = $this->get_equipe('id', $match->get_participant(0));
        $equipe1 = $this->get_equipe('id', $match->get_participant(1));
        echo '<li>'.$equipe0->get_nom().' vs '.$equipe1->get_nom();
        if( $match->get_type() == event_partie_dte_rte::match3 )
        {
          $equipe2 = $this->get_equipe('id', $match->get_participant(2));
          echo ' vs '.$equipe2->get_nom();
        }
        $arene = $match->get_arene();
        echo ' : '.date('d/m/Y H:i', $match->get_heure_debut()).', '.$arene->get_nom();
      }
      echo '</ul></p>';
    }
  }
	// @}
	
  /// Méthode appelée par le script horaire
  function horaire()
  {
    global $db;
    // Début des matchs
    if( $this->get_options_matchs(event_dte_rte::match_tp_auto) )
    {
      $matchs = $this->get_partie('statut = '.event_partie::a_venir.' AND heure_debut < '.(time() + 4200));
      foreach($matchs as $match)
      {
        $match->demarer();
      }
    }
    // Matchs terminés à cause de l'heure
    $matchs = $this->get_partie('statut = '.event_partie::en_cours.' AND heure_fin > 0 AND heure_fin <= UNIX_TIMESTAMP()');
    foreach($matchs as $match)
    {
      $match->terminer();
    }
    // Matchs gagnés par une équipe
    $matchs = $this->get_partie('statut', event_partie::en_cours);
    foreach($matchs as $match)
    {
      $equipes = $match->get_participants();
      $reste = 0;
      foreach($equipes as $id)
      {
        $requete = 'SELECT count(*) as nbr FROM perso, event_participant WHERE perso.id=event_participant.id_perso AND event_participant.equipe='.$id.' AND hp > 0 AND x >= 200';
        $req = $db->query($requete);
        $row = $db->read_assoc($req);
        if( $row && $row['nbr'] )
          $reste++;
      }
      if( $reste <= 1 )
      {
        $match->terminer();
      }
    }
  }
};


/**
 * Classe gérant un DTE
 * Une équipe par royaume plus une équipe admin, s'affrontent en matchs sucessifs.
 */
class event_dte extends event_dte_rte
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
	protected function unserializeDonnees($donnees)
  {
  }
	// @}
	
	/// Gère l'interface d'administration spécifique au DTE
	function admin_options_spec()
	{
  }
};



/**
 * Classe gérant un RTE
 * Similaire au DTE mais les équipes sont composés aléatoirement entre les différents participants.
 */
class event_rte extends event_dte_rte
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

  /**
   * @name Gestion interne des données
   * Méthodes, surchargées ou à surcharger, necessaire à l'initialisation de l'objet
   * et à sa sauvegarde dans la base de données.
   */
  // @{
	/// serialisation des données spécifiques à chaque event (à surcharger)
	protected function serializeDonnees()
  {
    $donnees = array(event_dte_rte::serializeDonnees(), implode(',', $this->creat_equipes));
    return implode(';', $donnees);
  }
	/// déserialisation des données spécifiques à chaque event (à surcharger)
	protected function unserializeDonnees($donnees)
  {
    if( $donnees )
    {
      $vals = explode(';', $donnees);
      event_dte_rte::unserializeDonnees($vals[0]);
      $this->creat_equipes = explode(',', $vals[1]);
    }
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $creat_equipes = array(1, 1, 1, 0);  ///< indique comment sont créées les équipes.
  const equipe_auto = 0;  ///< indice pour l'automatisation de la création des équipes.
  const equipe_refait_deux = 1;  ///< indice pour la regénération des équipe ayant terminé deuxième en poules
  const equipe_pretres  = 2;  ///< indice pour la dispersion des prêtres / clercs.
  const pretres_aleat = 0;  ///< répartition des prêtre complétement aléatoire.
  const pretres_aumoins1 = 1;  ///< au moins 1 prêtre dans chaque équipe.
  const pretres_reparti = 2;  ///< répartie tous les prêtre dans les équipes.
  const pretres_reparti_vie = 3;  ///< répartie tous les prêtre, druides et paladins dans les équipes.
  const equipe_pretres_2vie= 3;  ///< indice pour l'équivalence 1 prêtre = 2 paladins/druides
  /// Indique les options des matchs
  function get_creat_equipes($ind)
  {
    return $this->creat_equipes[$ind];
  }
  /// Définit les options des matchs
  function set_creat_equipes($ind, $val)
  {
    $this->creat_equipes[$ind] = $val;
		$this->champs_modif[] = 'donnees';
  }
  // @}

	/// Gère l'interface d'administration spécifique au DTE
	function admin_options_spec()
	{
    if( array_key_exists('action', $_GET) && $_GET['action']=='options' )
    {
      $this->set_creat_equipes(event_rte::equipe_auto, array_key_exists('equipe_auto', $_POST)?1:0);
      //$this->set_($_POST['']);
    }
?>
        <div class="event_minibloc">
          <input type="checkbox" name="equipe_auto" <?php if($this->get_creat_equipes(event_rte::equipe_auto)) echo 'checked="checked"';?> />Création automatique des équipes<br />
        </div>
<?PHP
  }
};
?>
