<?php
/**
 * @file placable.class.php
 * Définition de la classe placable
 */ 
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
/**
 * Classe Abstraite placable
 * Cette classe à pour but de définir ce qu'est un objet que l'on peut placer sur
 * la carte (pnj, batiment, personnage, monstre, ...).
 */
abstract class placable extends table
{
	protected $nom;  ///< Nom de l'objet.
	protected $x;  ///< Coordonnées x.
	protected $y;  ///< Coordonnées y.
	
	//! Constructeur
	/**
		Le constructeur va initialiser les attributs $id, $nom $x et $y.
		
		@param $nom String Le nom du personnage (vide par défaut)
		@param $x int La coordonnée x du personnage (0 par défaut).
		@param $y int La coordonnée y du personnage (0 par défaut).
		@param $id int L'identifiant du personnage (-1 par défaut)
	*/
	function __construct($nom = '', $x = 0, $y = 0, $id = -1)
	{
		$this->id = $id;
		$this->nom = $nom;
		$this->x = $x;
		$this->y = $y;
	}
	
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
  }
	
	//! Accesseur $nom
	function get_nom()
	{
		return $this->nom;
	}
	
	//! Modifieur $nom
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}
	
	/// Renvoie la coorodonnée x
	function get_x()
	{
		return $this->x;
	}
	
	/// Modifie la coorodonnée x
	function set_x($x)
	{
		$this->x = $x;
		$this->champs_modif[] = 'x';
	}
	
	/// Renvoie la coorodonnée y
	function get_y()
	{
		return $this->y;
	}
	
	/// Modifie la coorodonnée y
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

  /// Renvoie la position sous forme d'un seul entier
	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'nom, x, y';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.$this->nom.'", '.$this->x.', '.$this->y;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'nom = "'.$this->nom.'", x = '.$this->x.', y = '.$this->y;
	}
}

?>