<?php
/**
 * @file entnj_incarn.class.php
 * Définition de la classe entnj_incarn
 */
if (file_exists('../root.php'))
  include_once('../root.php');
/**
 * Classe abstraite entnj_incarn
 * Cette classe de base pour les incarnation des entité non joueurs (map_monstre, pet construction, placement)
 */
abstract class entnj_incarn extends placable
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
  protected $hp;   ///< HP actuels de l'objet
	/// Renvoie les HP actuels
	function get_hp()
	{
		return $this->hp;
	}
	/// Modifie les HP actuels
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}
	/// Renvoie l'objet représentant la définition
	abstract function get_def();
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	//! Constructeur
	/**
		Le constructeur va initialiser les attributs $id, $nom $x et $y.

		@param $nom String Le nom du personnage (vide par défaut)
		@param $x int La coordonnée x du personnage (0 par défaut).
		@param $y int La coordonnée y du personnage (0 par défaut).
		@param $hp int HP du personnage (0 par défaut).
		@param $id int L'identifiant du personnage (-1 par défaut)
	*/
	function __construct($nom = '', $x = 0, $y = 0, $hp = 0, $id = -1)
	{
    placable::__construct($nom, $x, $y, $id);
    $this->hp = $hp;
	}
	
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    placable::init_tab($vals);
		$this->hp = $vals['hp'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return placable::get_liste_champs().', hp';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return placable::get_valeurs_insert().', '.$this->hp;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return placable::get_liste_update().', hp = '.$this->hp;
	}
	// @}
}

?>