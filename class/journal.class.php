<?php
class journal extends table
{
	protected $id;
	protected $id_perso;
	protected $action;
	protected $actif;
	protected $passif;
	protected $time;
	protected $valeur;
	protected $valeur2;
	protected $x;
	protected $y;
	
	/**	
		*	Constructeur permettant la création d'un combat
		*	Les valeurs par défaut sont celles de la base de donnée.
		*	Le constructeur accepte plusieurs types d'appels:
		*		-journal() qui construit un journal "vide".
		*		-journal($id) qui va chercher le journal dont l'id est $id
		*		-journal($array) qui associe les champs de $array à l'objet.
	**/
	function __construct($id_perso = 0, $action = '', $actif = '', $passif = '', $time = '', $valeur = '', $valeur2 = 0, $x = 0, $y = 0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id_perso);
		}
		else
		{
			$this->id = $id;
			$this->id_perso = $id_perso;
			$this->action = $action;
			$this->actif = $actif;
			$this->passif = $passif;
			$this->time = $time;
			$this->valeur = $valeur;
			$this->valeur2 = $valeur2;
			$this->x = $x;
			$this->y = $y;
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id_perso = $id['id_perso'];
		$this->action = $id['action'];
		$this->actif = $id['actif'];
		$this->passif = $id['passif'];
		$this->time = $id['time'];
		$this->valeur = $id['valeur'];
		$this->valeur2 = $id['valeur2'];
		$this->x = $id['x'];
		$this->y = $id['y'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('id_perso'=>'i', 'action'=>'s', 'actif'=>'s', 'passif'=>'s', 'time'=>'s', 'valeur'=>'s', 'valeur2'=>'i', 'x'=>'i', 'y'=>'i');
	}
	
	function get_id_perso()
	{
		return $this->id_perso;
	}
	
	function get_action()
	{
		return $this->action;
	}
	
	function get_actif()
	{
		return $this->actif;
	}
	
	function get_passif()
	{
		return $this->passif;
	}
	
	function get_time()
	{
		return $this->time;
	}
	
	function get_valeur()
	{
		return $this->valeur;
	}
	function get_valeur2()
	{
		return $this->valeur2;
	}
	
	function get_x()
	{
		return $this->x;
	}
	
	function get_y()
	{
		return $this->y;
	}
	
	//Renvoie le journal suivant du joueur vérifiant $where
	function get_suivant($where = 1)
	{
		global $db;
		$requete = 'SELECT * FROM journal WHERE id_perso = '.$this->id_perso.' AND id > '.$this->id.' AND ('.$where.') ORDER BY id ASC';
		$req = $db->query($requete);
		if ($db->num_rows > 0)
		{
			$row = $db->read_assoc($req);
			return new journal($row);
		}
		else
			return false;
	}
	
	//Renvoie le journal précédent du joueur vérifiant $where
	function get_precedent($where = 1)
	{
		global $db;
		$requete = 'SELECT * FROM journal WHERE id_perso = '.$this->id_perso.' AND id < '.$this->id.' AND ('.$where.') ORDER BY id DESC';
		$req = $db->query($requete);
		if ($db->num_rows > 0)
		{
			$row = $db->read_assoc($req);
			return new journal($row);
		}
		else
			return false;
	}

  // Attention: message est un texte, valeur2 un int
  static function write($perso, $message, $valeur2 = 0, $action = 'rp') {
    $j = new journal(null, $perso->get_id(), $action, $perso->get_nom(), '',
                     date(DATE_ATOM), $message, $valeur2, $perso->get_x(),
                     $perso->get_y());
    $j->sauver();
  }

  /**
   * Fonction pour chercher dans le journal d'un perso si il y a une ligne
   * de type passe en parametre (rp par defaut), avec les valeurs passees
   * en parametre (valeur2 puis valeur, pas de test par defaut)
   */
  static function check($perso, $valeur2 = null, $message = null, $action = 'rp') {
    global $db;
    $requete = 'SELECT * FROM journal WHERE id_perso = ? AND action = ?';
    $params = array($perso->get_id(), $action);
    if ($valeur2 !== null) {
      $requete .= ' AND valeur2 = ?';
      $params[] = $valeur2;
    }
    if ($message !== null) {
      $requete .= ' AND valeur = ?';
      $params[] = $message;
    }
    $req = $db->param_query($requete, $params);
    if ($db->stmt_read_object($req))
      return true;
    else
      return false;
  }
  
  static function get_nombre_recents(&$perso)
	{
		global $db;
		$requete = 'SELECT COUNT(*) FROM journal WHERE action IN ("defense", "mort", "loot", "f_quete", "pet_leave", "rp", "attaque", "tue") AND time > "'.date('Y-m-d G:i:s', $perso->get_dernier_connexion()).'" AND id_perso = '.$perso->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		return $row[0];
	} 
}
?>
