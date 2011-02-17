<?php
/**
 * @file arene.class.php
 * Gestion des arènes
 */

require_once(root.'arenes/gen_arenes.php');

/**
 * Classe de base pour les arènes, liée à la table arenes de la base de données.
 */
class arene extends table
{
	/**
		Constructeur.
		Le constructeur peut être utilisé de plusieurs façons:
		-arene($id) qui récupère les informations de l'objet dans la base.
		-arene($vals) qui crée l'objet à partir d'information déjà récupèrées sans la base.
		-arene($nom, $x, $y, $size, $file, $donj, $positions) qui crée une nouvelle arène.

		@param $id          int     Id de l'entrée dans la base.
		@param $vals        array   Tableau associatif contenant les entrées de la base de données.
		@param $nom         string  statut de la partie.
		@param $x           int     heure du début de la partie.
		@param $y           int     heure sso au début de la partie.
		@param $size        int     arène où a lieu la partie, peut-être null.
		@param $file        string  heure de la fin de la partie, peut-être null.
		@param $donj        int     heure de la fin de la partie, peut-être null.
		@param $positions   string  heure de la fin de la partie, peut-être null.
	*/
	function __construct($nom, $x=0, $y=0, $size=0, $file='', $donj=false, $positions='')
	{
		global $db;
		// Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 && is_numeric($nom) )
		{
			$this->charger($nom);
		}
		elseif( is_array($nom) )
		{
			$this->init_tab($nom);
    }
		else
		{
			$this->nom = $nom;
			$this->x = $x;
			$this->y = $y;
			$this->size = $size;
			$this->file = $file;
			$this->donj = $donj;
			$this->set_positions($positions);
			$this->open = false;
			$this->decal = 0;
		}
	}
	
	/**
	* Crée un tableau d'objets respectant certains critères
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

		$requete = 'SELECT * FROM arenes WHERE '.$where.' ORDER BY '.$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new arene($row);
				else $return[$row[$keys]][] = new arene($row);
			}
		}
		else $return = array();
		return $return;
	}

  /**
   * @name Gestion interne des données
   * Méthodes, surchargées ou à surcharger, necessaire à l'initialisation de l'objet
   * et à sa sauvegarde dans la base de données.
   */
  // @{
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->x = $vals['x'];
		$this->y = $vals['y'];
		$this->size = $vals['size'];
		$this->file = $vals['file'];
		$this->open = $vals['open'];
		$this->donj = $vals['donj'];
		$this->decal = $vals['decal'];
		$this->set_positions($vals['positions']);
  }
  /// Renvoie la valeur d'un champ de la base de donnée
  protected function get_champ($champ)
  {
    if($champ == 'positions')
      return $this->get_positions();
    else
      return $this->{$champ};
  }
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'nom, x, y, size, file, open, donj, decal, positions';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.$this->get_nom().'", '.$this->get_x().', '.$this->get_y().', '.$this->get_size().', "'.mysql_escape_string($this->get_file()).'", '.($this->get_open()?1:0).', '.($this->get_donj()?1:0).', '.$this->get_decal().', "'.mysql_escape_string($this->get_positions()).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'nom = "'.$this->get_nom().'", x = '.$this->get_x().', y = '.$this->get_y().', size = '.$this->get_size().', file = "'.mysql_escape_string($this->get_file()).'", open = '.($this->get_open()?1:0).', donj = '.($this->get_donj()?1:0).', decal = '.$this->get_decal().', donnees = "'.mysql_escape_string($this->get_positions()).'"';
	}
  /**
   * Renvoie le nom de la table.
   * Doit être redéfinie à cause des classes filles
   */
  protected function get_table()
  {
    return 'arenes';
  }
	// @}

  /**
   * @name Paramètres
   * Paramètres de l'event et accesseurs.
   */
  // @{
  protected $nom; ///< nom de l'arène
  protected $x; ///< position horizontale du début de l'arène
  protected $y;  ///< position verticale du début de l'arène
  protected $size; ///< taille de l'arène
  protected $file; ///< fichier xml pour l'affichage
  protected $open; ///< indique si l'arène est ouverte
  protected $donj; ///< indique si l'arène correspond est de type donjon
  protected $decal; ///< décalage de l'heure SSO
  protected $positions; ///< tableau des positions de départs de personnages

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

	/// Renvoie la position horizontale du début de l'arène
	function get_x()
	{
		return $this->x;
	}
	/// Modifie la position horizontale du début de l'arène
	function set_x($x)
	{
    $this->x = $x;
		$this->champs_modif[] = 'x';
	}

	/// Renvoie la position verticale du début de l'arène
	function get_y()
	{
    return $this->y;
	}
	/// Modifie la position verticale du début de l'arène
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

	/// Renvoie la taille de l'arène
	function get_size()
	{
		return $this->size;
	}
	/// Modifie la taille de l'arène
	function set_size($size)
	{
    $this->size = $size;
		$this->champs_modif[] = 'size';
	}

	/// Renvoie le fichier xml
	function get_file()
	{
		return $this->file;
	}
	/// Modifie le fichier xml
	function set_file($file)
	{
    $this->file = $file;
		$this->champs_modif[] = 'file';
	}

	/// Indique si l'arène est ouverte
	function get_open()
	{
		return $this->open;
	}
	/// Définit si l'arène est ouverte
	function set_open($open)
	{
    $this->open = $open;
		$this->champs_modif[] = 'open';
	}
	// Ouvre l'arène
	function ouvrir()
	{
    $this->set_open(1);
		$this->sauver();
    gen_all();
  }
  // Ferme l'arène
  function fermer()
  {
    $this->set_open(0);
		$this->sauver();
    unlink(root.'/arenes/'.$this->get_file());
    unlink(root.'/arenes/admin/'.$this->get_file());
  }

	/// Indique si l'arène est de type donjon
	function get_donj()
	{
		return $this->donj;
	}
	/// Définit si l'arène est de type donjon
	function set_donj($donj)
	{
    $this->donj = $donj;
		$this->champs_modif[] = 'donj';
	}

	/// Renvoie le décalage de l'heure SSO
	function get_decal()
	{
		return $this->decal;
	}
	/// Modifie le décalage de l'heure SSO
	function set_decal($decal)
	{
    $this->decal = $decal;
		$this->champs_modif[] = 'decal';
	}
	/**
	 * Calcul le décalage et change en fonction de l'heure voulue
	 * @param  $heure_reelle int   heure dans le monde réel
	 * @param  $heure_sso    int   heure sso
	 */
	 function calcul_decal($heure_reelle, $heure_sso)
	 {
      // On reprend l'alogrithme d'Irulan :
      $date_visee = date("H:i:s", $heure_sso);
      $decal = 0;
      // Comme je ne sais pas calculer ca, je vais chercher par dichotomie
      // Date trouvée en ~16 iterations
      $tdsso = date_sso($heure_reelle + $decal);
      $step = 57600; // demi-jour SSO
      while ($tdsso != $date_visee) {
        if (($tdsso > $date_visee && $step > 0) ||
            ($tdsso < $date_visee && $step < 0)) {
          $step *= -1;
        }
        $step = round($step / 2);
        $decal += $step;
        //echo "step is $step, decal is $decal \n";
        $tdsso = date_sso($heure_reelle + $decal);
      }
      
      // On modifie le décalage
      $this->decal = $decal;
  		$this->champs_modif[] = 'decal';
   }

	/**
	 * Renvoie les positions de départs de personnages
	 * -get_positions()        renvoie l'ensemble des positions sous forme tectuelle (pour la base de donnée)
	 * -get_positions($type)   renvoie les positions sous forme de tableau pour un type d'épreuve particulier, ou false si il n'y en pa pas pour ce dernier
	 * Les types d'épreuves possibles sont :
   * - m2 : match à 2, le tableau de retour est alors de la forme (x1,y1,x2,y2) en position relative
   * - m3 : match à 3, le tableau de retour est alors de la forme (x1,y1,x2,y2,x3,y3) en position relative
   * - f3 : match de finale (à 2), le tableau de retour est alors de la forme (x1,y1,x2,y2) en position relative
	 */
	function get_positions($type='')
	{
    if( $type )
      return $this->positions[$type];
    else
    {
      $vals = array();
      foreach($this->positions as $type=>$pos)
      {
        $vals[] = $type.':'.implode(',',$pos);
      }
  		return implode('|',$vals);
    }
	}
	/**
	 * Modifie les positions de départs de personnages
	 * @param $positions array/string  ensemble des positions sous forme tectuelle si $type n'est pas renséigné, tableau des coordonnées (relatives) sinon
	 * @param $type  string  type d'épreuve dont on veut modifier, vide si l'on veut tout modifier
	 */
	function set_positions($positions, $type='')
	{
    if( $type )
    {
       $this->positions[$type] = $positions;
    }
    else
    {
      $this->positions = array();
      $vals = explode('|',$positions);
      foreach($vals as $val)
      {
        $pos = explode(':', $val);
        $this->positions[$pos[0]] = explode(',', $pos[1]);
      }
    }
		$this->champs_modif[] = 'positions';
	}
	// @}
};

?>
