<?php // -*- php -*-
/**
 * @file placement.class.php
 * Définition de la classe construction représentant un bâtiment en construction sur la carte
 */

/**
 * Classe placement
 * Classe représentant un bâtiment en cosntruction sur la carte
 */
class placement extends entitenj_constr
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $debut_placement; ///< Date du début de la construction
	protected $fin_placement; ///< Date de la fin de la construction

  /// Renvoie l'image du bâtiment
	function get_image()
	{
		if(!isset($this->image))
		{
			global $db;
			$requete = "SELECT image FROM batiment WHERE id = ".$this->id_batiment;
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$this->image = $row['image'];
		}
		return $this->image;
	}

	/// Renvoie la date du début de la construction
	function get_debut_placement()
	{
		return $this->debut_placement;
	}
	/// Modifie la date du début de la construction
	function set_debut_placement($debut_placement)
	{
		$this->debut_placement = $debut_placement;
		$this->champs_modif[] = 'debut_placement';
	}

	/// Renvoie la date de la fin de la construction
	function get_fin_placement()
	{
		return $this->fin_placement;
	}
	/// Modifie la date de la fin de la construction
	function set_fin_placement($fin_placement)
	{
		$this->fin_placement = $fin_placement;
		$this->champs_modif[] = 'fin_placement';
	}
	
	/// Renvoie le temps total de construction nécessaire
	function get_temps_total()
	{
		return $this->fin_placement - $this->debut_placement;
	}
  /// Renvoie le temps restant de construction
	function get_temps_restant()
	{
		return time() - $this->debut_placement;
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	* Constructeur
	* @param id                  Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	* @param type                Type du bâtiment
	* @param x                   Position x du bâtiment
	* @param y                   Position y du bâtiment
	* @param royaume             Royaume auquel appartient le bâtiment
	* @param debut_placement     Date de début de la construction du bâtiment
	* @param fin_placement       Date de fin de la construction du bâtiment
	* @param $id_batiment        Id de la définition du bâtiment
	* @param hp                  HP du bâtiment
	* @param nom                 Nom du bâtiment
	* @param rez                 Pourcentage de HP/MP à la rez ou distance de vision des tours
	* @param point_victoire      Nombre de points de victoire gagnés lorsque le bâtiment est détruit
	*/
	function __construct($id = 0, $type = '', $x = 0, $y = 0, $royaume = 0, $debut_placement = 0, $fin_placement = 0, $id_batiment = 0, $hp = '', $nom = '', $rez = 0, $point_victoire = 0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      entitenj_constr::__construct($id, $id_batiment, $x, $y, $royaume, $hp, $nom, $type, $rez, $point_victoire);
			$this->debut_placement = $debut_placement;
			$this->fin_placement = $fin_placement;
		}
		// Garde-fou
		if ($this->debut_placement == 0) $this->debut_placement = time();
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    entitenj_constr::init_tab($vals);
		$this->debut_placement = $vals['debut_placement'];
		$this->fin_placement = $vals['fin_placement'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return entitenj_constr::get_liste_champs().', debut_placement, fin_placement';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return entitenj_constr::get_valeurs_insert().', '.$this->debut_placement.', '.$this->fin_placement;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return entitenj_constr::get_liste_update().', debut_placement = '.$this->debut_placement.', fin_placement = '.$this->fin_placement;
	}
	// @}
	
	/**
	 * Renvoie l'ensemble des buffs / débuffs actif sur un bâtiment.
	 * @param  $id   Id du bâtiment
	 * @return     Tableau des buffs.
	 * @deprecated Utiliser get_construction_buff à la place
	 */
	static function get_placement_buff($id)
	{
		return entitenj_constr::get_construction_buff($id);
	}
}