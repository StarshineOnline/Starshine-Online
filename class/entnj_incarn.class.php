<?php
/**
 * @file entnj_incarn.class.php
 * Définition de la classe entnj_incarn
 */

/**
 * Classe abstraite entnj_incarn
 * Classe de base pour les incarnation des entité non joueurs (map_monstre, pet construction, placement)
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

  /// Renvoie la race
	function get_race($perso)
	{
		return 'neutre';
	}
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
	
	/// Renvoie le coefficient pour modifier les caractéristique
  function get_coeff_carac() { return 1; }
	/// Renvoie le coefficient pour modifier les compétences
  function get_coeff_comp(&$perso, &$def) { return 1; }
  /// Renvoie le bonus de PM dû à l'armure
  function get_bonus_pm() { return 0; }
  /// Renvoie le bonus de PP dû à l'armure
  function get_bonus_pp() { return 0; }
  /// Renvoie la distance à laquelle le personnage peut attaquer
	function get_distance_tir() { return false; }
	/// Renvoie le script d'action
	function get_action($attaquant) { return false; }
	/**
	 * Renvoie l'ensemble des buffs / débuffs actif sur le bâtiment.
	 * @return     Tableau des buffs.
	 */
  abstract function get_buff($nom = false, $champ = false, $type = true);
	/**
	 * Renvoie le facteur de dégâts de ou des armes.
	 * La plupart du temps on s'en fiche, de la main, on veut les degats
	 * @param $main   si false : cumul, si 'droite' ou 'gauche' : detail
	 */
	function get_arme_degat($main = false) { return 0; }
	/// Indique que l'entité est morte
	function mort(&$perso)
	{
    $this->supprimer();
  }
  /// Actions effectuées à la fin d'un combat pour l'attaquant
  function fin_attaque(&$cible) {}
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, &$def) { return ""; }
  /// Renvoie le coût en PA de l'attaque
  function get_cout_attaque(&$perso, $cible=null) { return null; }
  /// Renvoie le coût en PA pour attaquer l'entité
  abstract function get_cout_attaque_base(&$perso);
  /// Indique si l'entité peut attaquer
  function peut_attaquer()
  {
    return true;
  }
}

?>