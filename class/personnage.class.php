<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//! Class Personnage
/**
 * Classe Abstraite Personnage
 * Cette classe à pour but de définir ce qu'est un personnage. Elle sera par la suite spécialisée.
 */
abstract class Personnage
{
	protected $id;
	protected $nom;
	protected $coord_x;
	protected $coord_y;
	
	//! Constructeur
	/**
		Le constructeur va initialiser les attributs $id, $nom $coord_x et $coord_y.
		
		@param $nom String Le nom du personnage (vide par défaut)
		@param $coord_x int La coordonnée x du personnage (0 par défaut).
		@param $coord_y int La coordonnée y du personnage (0 par défaut).
		@param $id int L'identifiant du personnage (-1 par défaut)
	*/
	function __construct($nom = '', $coord_x = 0, $coord_y = 0, $id = -1)
	{
		$this->id = $id;
		$this->nom = $nom;
		$this->coord_x = $coord_x;
		$this->coord_y = $coord_y;
	}
	
	//! Accesseur de $id
	function getId()
	{
		return $this->id;
	}
	
	//! Modifieur $id
	function setId($id)
	{
		$this->id = $id;
	}
	
	//! Accesseur $nom
	function getNom()
	{
		return $this->nom;
	}
	
	//! Modifieur $nom
	function setNom($nom)
	{
		$this->nom = $nom;
	}
	
	//! Accesseur $coord_x
	function getX()
	{
		return $this->coord_x;
	}
	
	//! Modifieur $coord_x
	function setX($coord_x)
	{
		$this->coord_x = $coord_x;
	}
	
	//! Accesseur $coord_y
	function getY()
	{
		return $this->coord_y;
	}
	
	//! Modifieur $coord_y
	function setY($coord_y)
	{
		$this->coord_y = $coord_y;
	}
	
	//! Fonction abstraite permettant l'ajout du personnage dans la base.
	abstract function sauver();
	
	
	//! Fonction permettant la suppression d'un personnage de la base
	/**
		@param $table String La table dans laquelle il faudra supprimer l'élément.
	*/
	protected function supprimer($table)
	{
		global $db;
		if( $this->id != -1 )
		{
			$requete = 'DELETE FROM '.$table.' WHERE ';
			//Prise en compte de la casse.
			if( !strcmp('perso', $table) )
				$requete .= 'ID = ';
			else
				$requete .= 'id = ';
			$requete .= $this->id;
			$db->query($requete);
		}
	}
	
	//! Fonction facilitant l'ajout dans la base.
	protected function insertBase()
	{
		return '"'.$this->nom.'", "'.$this->coord_x.'", "'.$this->coord_y.'"';
	}
	
	//! Fonction facilitant la modification dans la base.
	protected function modifBase()
	{
		return 'nom = "'.$this->nom.'", x = "'.$this->coord_x.'", y = "'.$this->coord_y.'"';
	}
	
	//! toString
	function __toString()
	{
		return $this->id.', '.$this->nom.', '.$this->coord_x.' : '.$this->coord_y;
	}
}

?>