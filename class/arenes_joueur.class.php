<?php
/**
 * @file arenes_joueur.class.php
 * Gestion des personnages dans les arènes
 */


/**
 * Classe de base pour les personnages dans les arènes, liée à la table arenes_joueurs de la base de données.
 */
class arenes_joueur extends table
{
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-arenes_joueur($event, $id) qui récupère les informations de l'objet dans la base.
		-arenes_joueur($event, $vals) qui crée l'objet à partir d'information déjà récupèrées sans la base.
		-arenes_joueur($event, $perso, $statut, $arene, $partie, $x, $y, $groupe, $hp) qui crée une nouvelle arène.

		@param $event       object      Objet représentant l'event dont le participant fait parti
		@param $id          int         Id de l'entrée dans la base.
		@param $vals        array       Tableau associatif contenant les entrées de la base de données.
		@param $perso       int/object  objet représentant le personnage ou id de celui-ci.
		@param $statut      int         statut du personnage par rapport au passage dans l'arène (en attente, en cours, fini).
		@param $arene       int/object  arène où a lieu la partie.
		@param $partie      int/object  partie de l'event à laquelle correspond ce passage dans l'arène.
		@param $x           int         position horizontale du personnage où il doit être téléporté.
		@param $y           int         position verticale du personnage où il doit être téléporté.
		@param $groupe      int/object  groupe du personnage.
		@param $hp          int         hp du personnage après le téléport.
	*/
	function __construct($event, $perso, $statut=0, $arene=0, $partie=0, $x=0, $y=0, $groupe=null, $hp=1)
	{
		global $db;
		// Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 2 && is_numeric($perso) )
		{
			$this->charger($perso);
		}
		elseif( is_array($perso) )
		{
			$this->init_tab($perso);
    }
		else
		{
			$this->id_perso = $perso;
			$this->x = $x;
			$this->y = $y;
			$this->arene = $arene;
			$this->partie = $partie;
			$this->statut = $statut;
			$this->groupe = $groupe;
			$this->hp = $hp;
		}
		$this->event = $event;
	}
  /**
   * Crée les objets ou un objet en particulier
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

		$requete = 'SELECT * FROM arenes_joueurs WHERE event = '.(is_object($event)?$event->get_id():$event);
    if( $champ )
    {
      if( $valeur )
        $requete .= ' AND '.$champ.' = "'.$valeur.'"';
      else
        $requete .= ' AND '.$champ;
    }
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
		$this->id_perso = $vals['id_perso'];
		$this->x = $vals['x'];
		$this->y = $vals['y'];
		$this->arene = $vals['arene'];
		$this->partie = $vals['partie'];
		$this->statut = $vals['statut'];
		$this->groupe = $vals['groupe'];
		$this->hp = $vals['hp'];
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
    return 'x, y, id_perso, groupe, event, partie, arene, statut, hp, donnees';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->get_x().', '.$this->get_y().', '.$this->get_id_perso().', '.($this->get_id_groupe()===null?'NULL':$this->get_id_groupe()).', '.$this->get_id_event().', '.$this->get_id_partie().', '.$this->get_id_arene().', '.$this->get_statut().', '.$this->get_hp().', "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'x = '.$this->get_x().', y = '.$this->get_y().', id_perso = '.$this->get_id_perso().', groupe = '.($this->get_id_groupe()===null?'NULL':$this->get_id_groupe()).', event = '.$this->get_id_event().', partie = '.$this->get_id_partie().', arene = '.$this->get_id_arene().', statut = '.$this->get_statut().', hp = '.$this->get_hp().', donnees = "'.mysql_escape_string($this->serializeDonnees()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'arenes_joueurs';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $event; ///< event concerné
  protected $x; ///< position horizontale où téléporter
  protected $y;  ///< position verticale où téléporter
  protected $id_perso; ///< personnage
  protected $groupe; ///< groupe du personnage
  protected $partie; ///< partie de l'event concernée
  protected $arene; ///< arène concernée
  protected $statut; ///< indique si l'arène correspond est de type donjon
  const en_attente = 10;  ///< le personnage attend d'être téléporté dans l'arène
  const en_cours = 20;  ///< le personnage est dans l'arène
  const fini = 30; ///< le personnage est sorti de l'arène
  protected $hp; ///< décalage de l'heure SSO

	/// Renvoie l'event
	function get_event()
	{
    if( $this->event )
		  return $this->event;
    return null;
	}
	/// Renvoie l'event
	function get_id_event()
	{
    if( $this->event )
		  return $this->event->get_id();
    return 0;
	}
	
	/// Renvoie la position horizontale
	function get_x()
	{
		return $this->x;
	}
	/// Modifie la position horizontale
	function set_x($x)
	{
    $this->x = $x;
		$this->champs_modif[] = 'x';
	}

	/// Renvoie la position verticale
	function get_y()
	{
    return $this->y;
	}
	/// Modifie la position verticale
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

	/// Renvoie le perso sous forme d'objet
	function get_perso()
	{
    if( !is_object($this->id_perso) )
      $this->id_perso = new perso($this->id_perso);
		return $this->id_perso;
	}
	/// Renvoie l'id du perso
	function get_id_perso()
	{
    if( is_object($this->id_perso) )
		  return $this->id_perso->get_id();
    else
      return $this->id_perso;
	}
	/// Modifie le personnage
	function set_perso($perso)
	{
    // on peut modifier le personnage que s'il n'est pas encore dans l'arène
    if( $this->statut < arenes_joueur::en_cours )
    {
  		$this->nom = $perso;
  		$this->champs_modif[] = 'id_perso';
    }
	}

	/// Renvoie le groupe sous forme d'objet
	function get_groupe()
	{
    if( !$this->groupe )
      return null;
    if( !is_object($this->groupe) )
      $this->groupe = new groupe($this->groupe);
		return $this->groupe;
	}
	/// Renvoie l'id du groupe
	function get_id_groupe()
	{
    if( is_object($this->groupe) )
		  return $this->groupe->get_id();
    elseif( !$this->groupe )
      return 0;
    else
      return $this->groupe;
	}
	/// Modifie le groupe
	function set_groupe($groupe)
	{
    $this->groupe = $groupe;
		$this->champs_modif[] = 'groupe';
	}

	/// Renvoie la partie sous forme d'objet
	function get_partie()
	{
    if( !$this->partie )
      return null;
    if( !is_object($this->partie) )
      $this->perso = new partie($this->partie);
		return $this->partie;
	}
	/// Renvoie l'id de la partie
	function get_id_partie()
	{
    if( is_object($this->partie) )
		  return $this->partie->get_id();
    elseif( !$this->partie )
      return 0;
    else
      return $this->partie;
	}
	/// Modifie la partie
	function set_partie($partie)
	{
    $this->partie = $partie;
		$this->champs_modif[] = 'partie';
	}

	/// Renvoie l'arène sous forme d'objet
	function get_arene()
	{
    if( $this->arene === false )
      return false;
    if( !$this->arene )
      return null;
    if( !is_object($this->arene) )
      $this->arene = new arene($this->arene);
		return $this->arene;
	}
	/// Renvoie l'id de l'arène
	function get_id_arene()
	{
    if( $this->arene === false )
      return false;
    if( is_object($this->arene) )
		  return $this->arene->get_id();
    elseif( !$this->arene )
      return 0;
    else
      return $this->arene;
	}
	/// Modifie l'arène
	function set_arene($arene)
	{
    $this->arene = $arene;
		$this->champs_modif[] = 'arene';
	}

	///  Renvoie le statut
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

	///  Renvoie les HP
	function get_hp()
	{
		return $this->hp;
	}
	/// Modifie les HP
	function set_hp($hp)
	{
    $this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}
	// @}
	
	/**
	 * Gestion interne du TP
	 * @param $arene   objet/null  arène où le personnage doit être téléporté, ou null s'il faut le ramener sur la carte principale
	 * @param $x       int         position horizontale (relative si le téléport à lieu vers un donjon)
	 * @param $y       int         position verticale (relative si le téléport à lieu vers un donjon)
	 * @param $groupe  objet/null  groupe du personnage après le téléport ou null si on ne le change pas de groupe
	 * @param $hp      int/bool    HP du personnage après téléport, true pour une remise au maximim, false s'il ne faut pas y toucher
	 * @param $admin   string      nom à afficher pour le téléport
	 */
  protected function tp($arene, $x, $y, $groupe, $hp, $admin)
  {
    global $db;

    $perso = $this->get_perso();
	$perso->check_perso();
    if( $arene )
    {
      $x += $arene->get_x();
      $y += $arene->get_y();
      $lieu = mysql_escape_string($arene->get_nom());
    }
    else
      $lieu = 'jeu';
    // enregistrement des données à rétablir
		$this->x = $perso->get_x();
		$this->y = $perso->get_y();
		if($groupe !== null)
		  $this->groupe = $perso->get_groupe();
    else
      $this->groupe = null;
		$this->hp = $perso->get_hp();
    // Téléport
    $perso->set_x($x);
    $perso->set_y($y);
    // Groupage
    if( $groupe !== null )
    {
      if( $arene )
      {
        $mbr_grp = new groupe_joueur(-1, $perso->get_id(), $groupe->get_id(), 'n');
        $mbr_grp->sauver();
      }
      else
      {
        $mbr_grp = groupe_joueur::create(array('id_joueur', 'id_groupe'), array($perso->get_id(), $this->groupe));
        if($mbr_grp)
          $mbr_grp[0]->supprimer();
      }
      $perso->set_groupe( $groupe?$groupe->get_id():0 );
    }
	
	// Supression des buffs et débuffs
	if($arene)
	{
		// On enregistre les buffs actuels
		$requete = 'INSERT INTO arenes_oldbuff(`type`,`effet`,`effet2`,`id_perso`,`fin`,`duree`,`nom`,`description`,`debuff`,`supprimable`) 
		SELECT buff.type, buff.effet, buff.effet2, buff.id_perso, buff.fin, buff.duree, buff.nom, buff.description, buff.debuff, buff.supprimable FROM buff WHERE id_perso = '.$perso->get_id();
		$db->query($requete);
		// On les supprime
		$db->query('DELETE FROM buff WHERE id_perso = '.$perso->get_id());
	}
	else
	{
		// On supprime les buffs de l'arene
		$db->query('DELETE FROM buff WHERE id_perso = '.$perso->get_id());
		// On retablit les anciens buffs
		$requete = 'INSERT INTO buff(`type`,`effet`,`effet2`,`id_perso`,`fin`,`duree`,`nom`,`description`,`debuff`,`supprimable`) 
		SELECT old.type, old.effet, old.effet2, old.id_perso, old.fin, old.duree, old.nom, old.description, old.debuff, old.supprimable FROM arenes_oldbuff AS old WHERE id_perso = '.$perso->get_id();
		$db->query($requete);
		$db->query('DELETE FROM arenes_oldbuff WHERE id_perso = '.$perso->get_id());
	}
	
    // HP
    if( $hp === true )
    {
      $perso->set_hp( $perso->get_hp_maximum() );
      $perso->set_mp( $perso->get_mp_maximum() );
			// Soigne aussi les pets, ya pas de raison
			$db->query('UPDATE pet SET hp = (SELECT hp FROM monstre WHERE id = pet.id_monstre), mp = (SELECT energie * 10 FROM monstre WHERE id = pet.id_monstre) WHERE id_joueur = '.$perso->get_id());
    }
    elseif( $hp !== false )
    {
      $perso->set_hp( $hp );
    }
    // sauvegarde
    $perso->sauver();
    $this->sauver();
    // Journal
    $requete = 'INSERT INTO journal VALUES("", '.$perso->get_id().', "teleport", "'.mysql_escape_string($admin).'", "'.mysql_escape_string($perso->get_nom()).'", NOW(), "'.$lieu.'", 0, 0, 0)';
    $db->query($requete);
  }
  
  /**
   * Téléporte le personnage en fonction des données objet
	 * @param $admin  string   nom à afficher pour le téléport
   */
  function teleporte($admin='SSObot')
  {
    switch($this->get_statut())
    {
    case arenes_joueur::en_attente:
      $this->set_statut(arenes_joueur::en_cours);
      $arene = $this->get_arene();
      break;
    case arenes_joueur::en_cours:
      $this->set_statut(arenes_joueur::fini);
      $arene = null;
      break;
    default:
      return;
    }
    $this->tp($arene, $this->get_x(), $this->get_y(), $this->get_groupe(), $this->get_hp(), $admin);
  }
  
  /**
   * Crée un nouvel objet et télporte le personnage dans l'arène
   * @param $perso   objet       personnage à téléporter
	 * @param $arene   objet/null  arène où le personnage doit être téléporté, ou null s'il faut le ramener sur la carte principale
	 * @param $x       int         position horizontale (relative si le téléport à lieu vers un donjon)
	 * @param $y       int         position verticale (relative si le téléport à lieu vers un donjon)
	 * @param $groupe  objet/null  groupe du personnage après le téléport ou null si on ne le change pas de groupe
	 * @param $hp      int/bool    HP du personnage après téléport, true pour une remise au maximim, false s'il ne faut pas y toucher
	 * @param $event   objet       event concerné
	 * @param $partie  objet       partie de l'event concernée
	 * @param $admin   string      nom à afficher pour le téléport.
   */
  static function tp_arene($perso, $arene, $x, $y, $groupe, $hp, $event=0, $partie=0, $admin='SSObot')
  {
    $ar_perso = new arenes_joueur($event, $perso, arenes_joueur::en_cours, $arene, $partie);
    $ar_perso->tp($arene, $x, $y, $groupe, $hp, $admin);
  }
};

?>
