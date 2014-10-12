<?php // -*- mode: php; -*-
/**
 * @file entitenj_constr.class.php
 * Définition de la classe de base pour les bâtiments sur la carte, en construction ou non
 */

/**
 * Classe entitenj_constr
 * Classe de base pour les bâtiments sur la carte, en construction ou non
 */
abstract class entitenj_constr extends entnj_incarn
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $batiment = null; ///< Objet de classe 'batiment' : définition du bâtiment
	protected $id_batiment; ///< Id de la définition du bâtiment
	protected $royaume; ///< Royaume auquel appartient le bâtiment
	protected $type; ///< Type de bâtiment
	protected $rez; ///< Pourcentage de HP/MP à la rez ou distance de vision des tours (encore utilisé ?)
	protected $point_victoire; ///< Nombre de points de victoire gagnés lorsque le bâtiment est détruit

	/// Alias de get_batiment()
	/// Implémentation de la fonction abstraite get_def() de la classe entnj_incarn
	function get_def()
	{
		return $this->get_batiment();
	}
	/// Renvoie l'objet représentant la définition du bâtiment
	function get_batiment()
	{
		if(is_null($this->batiment))
		{
			$this->batiment = new batiment($this->id_batiment);
		}
		return $this->batiment;
	}
	/// Modifie l'objet représentant la définition du bâtiment
	function set_batiment($batiment)
	{
		$this->batiment = $batiment;
		if( $batiment->get_id() != $this->id_batiment )
		{
			$this->set_id_batiment($batiment->get_id());
		}
	}
	/// Renvoie l'id de la définition du bâtiment
	function get_id_batiment()
	{
		return $this->id_batiment;
	}
	/// Modifie l'id de la définition du bâtiment
	function set_id_batiment($id_batiment)
	{
		$this->id_batiment = $id_batiment;
		$this->champs_modif[] = 'id_batiment';
		if( !is_null($this->batiment) && $this->batiment->get_id() != $id_batiment )
		{
			$this->set_batiment(new batiment($id_batiment));
		}
	}

	/// Renvoie le royaume auquel appartient le bâtiment
	function get_royaume()
	{
		return $this->royaume;
	}
	/// Modifie le royaume auquel appartient le bâtiment
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
	}

	/// Renvoie le type du bâtiment
	function get_type()
	{
		return $this->type;
	}
	/// Modifie le type du bâtiment
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	//// Renvoie le pourcentage de HP/MP à la rez ou distance de vision des tours
	function get_rez()
	{
		return $this->rez;
	}
	/// Modifie le pourcentage de HP/MP à la rez ou distance de vision des tours
	function set_rez($rez)
	{
		$this->rez = $rez;
		$this->champs_modif[] = 'rez';
	}

	/// Renvoie le nombre de points de victoire gagnés lorsque le bâtiment est détruit
	function get_point_victoire()
	{
		return $this->point_victoire;
	}
	/// Modifie le nombre de points de victoire gagnés lorsque le bâtiment est détruit
	function set_point_victoire($point_victoire)
	{
		$this->point_victoire = $point_victoire;
		$this->champs_modif[] = 'point_victoire';
	}
	
	/// Date de pose ou construction de l'entité
	abstract function get_date_debut();
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	* Constructeur
	* @param id                  Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	* @param $id_batiment        Id de la définition du bâtiment
	* @param x                   Position x du bâtiment
	* @param y                   Position y du bâtiment
	* @param royaume             Royaume auquel appartient le bâtiment
	* @param hp                  HP du bâtiment
	* @param nom                 Nom du bâtiment
	* @param type                Type du bâtiment
	* @param rez                 Pourcentage de HP/MP à la rez ou distance de vision des tours
	* @param point_victoire      Nombre de points de victoire gagnés lorsque le bâtiment est détruit
	*/
	function __construct($id = 0, $id_batiment = 0, $x = 0, $y = 0, $royaume = 0, $hp = 0, $nom = '', $type = '', $rez = 0, $point_victoire = 0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			entnj_incarn::__construct($nom, $x, $y, $hp, $id);
			$this->id_batiment = $id_batiment;
			$this->royaume = $royaume;
			$this->type = $type;
			$this->rez = $rez;
			$this->point_victoire = $point_victoire;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    entnj_incarn::init_tab($vals);
		$this->id_batiment = $vals['id_batiment'];
		$this->royaume = $vals['royaume'];
		$this->type = $vals['type'];
		$this->rez = $vals['rez'];
		$this->point_victoire = $vals['point_victoire'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return entnj_incarn::get_liste_champs().', id_batiment, royaume, type, rez, point_victoire';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return entnj_incarn::get_valeurs_insert().', '.$this->id_batiment.', '.$this->royaume.', "'.mysql_escape_string($this->type).'", '.$this->rez.', '.$this->point_victoire;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return entnj_incarn::get_liste_update().', id_batiment = '.$this->id_batiment.', royaume = '.$this->royaume.', type = "'.mysql_escape_string($this->type).'", rez = '.$this->rez.', point_victoire = '.$this->point_victoire;
	}
	// @}

  /**
   * @name  Buffs
   * Données et méthodes ayant trait aux buffs et débuffs actifs sur le monstre.
   */
  // @{
	protected $buff = null;  ///< Liste des buffs actifs sur le monstre
	/// Récupère les buffs du bâtiment
	abstract protected function constr_buff();
	/**
	 * Renvoie l'ensemble des buffs / débuffs actif sur le bâtiment.
	 * @return     Tableau des buffs.
	 */
	function get_buff($nom = false, $champ = false, $type = true)
	{
		if( $this->buff == null )
			$this->constr_buff();
		return $this->buff;
	}
	/**
	 * Renvoie l'ensemble des buffs / débuffs actif sur un bâtiment.
	 * @param  $id   Id du bâtiment
	 * @return     Tableau des buffs.
	 */
	static function get_construction_buff($id)
	{
		global $db;
		$buff = array();
		$req = $db->query("select * from buff_batiment where id_construction = '$id'");
		if ($req) {
			while ($b = $db->read_object($req)) {
				$buff[$b->type] = $b;
			}
		}
		return $buff;
	}
  /// Supprime les buffs périmés actifs sur le monstre
	static function check_buff()
	{
		$req = $db->query("delete from buff_batiment where date_fin <= ".time());
	}
	// @}
	
	static function factory($type, $id)
	{
		switch($type)
		{
		case 'construction':
			return new construction($id);
		case 'placement':
			return new placement($id);
		default:
			return null;
		}
	}
	
	/// Indique que l'entité est morte
	function mort(&$perso)
	{
    global $Trace;
    $this->supprimer();
		//On supprime un bourg au compteur
		if($this->get_type() == 'bourg')
		{
			royaume::supprime_bourg( $this->get_royaume() );
		}
		//On retrouve les points de victoire
		$royaume = new royaume($Trace[$perso->get_race()]['numrace']);
    $mult = $royaume->get_mult_victoire( new royaume($this->get_royaume()) );
		$royaume->add_point_victoire( ceil($this->get_point_victoire() * $mult) );
		$royaume->sauver();
		//On efface le batiment
		$this->supprimer();
  }
  /// Renvoie le coût en PA pour attaquer l'entité
  function get_cout_attaque_base(&$perso)
  {
    global $G_PA_attaque_batiment;
    if( $perso->is_buff('convalescence') )
    {
    	interf_alerte::enregistre(interf_alerte::msg_avertis, 'Le coût en PA est doublé à cause de votre convalescence.');
      return $G_PA_attaque_batiment*2;
		}
    else
      return $G_PA_attaque_batiment;
  }

  /**
   * Renvoie le nombre de bâtiments proche d'une certaine position
   *
   * @var  $x         Position x
   * @var  $y         Position y
   * @var  $type      Type de bâtiment
   * @var  $distance  Distance à respecter
   * @var  $royaume   Id du royaume du bâtiment ou null si le royaume n'importe pas
   * @var  $diff_roy  True si on cherche un bâtiment d'un royaume différent, false c'est du même royaume
   * @var  $liste     True si on renvoie la liste s'il y a des bâtiments, sinon renvoie le nombre de bâtiments
   */
  function batiments_proche($x, $y, $type, $distance, $royaume=null, $diff_roy=false, $liste=false)
  {
    global $db, $G_max_x, $G_max_y;
    if( $royaume )
      $roy = 'AND royaume '.($diff_roy?'!=':'=').$royaume;
    else
      $roy = '';
		$requete = 'SELECT id, x, y, royaume FROM '.static::get_table()
			.' WHERE x >= '.max(($x - $distance), 1)
			.' AND x <= '.min(($x + $distance), $G_max_x)
			.' AND y >= '.max(($y - $distance), 1)
			.' AND y <= '.min(($y + $distance), $G_max_y)
			.' AND type = "'.$type.'"'.$roy;
		$req = $db->query($requete);
    $num = $db->num_rows;
    if( $liste && $num>0)
    {
      $bats = array();
      while( $row = $db->read_assoc($req) )
      {
        $bats[] = $row;
      }
      return $bats;
    }
    else
      return $num;
  }
}
?>
