<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Inclusion de la classe abstraite personnage
include_once(root.'personnage.class.php');

//! Class PNJ
/**
   * Classe PNJ
   *
   * Classe représentant un PNJ et permettant de l'ajouter, le modifier ou le supprimer de la base.
   *
*/
class pnj extends placable
{
  const table = 'pnj';  ///< Nom de la table correspondante.
  
	private $image;
	private $texte;
	
	//! Constructeur
	/**
		Constructeur.
		
		Le constructeur peut être utilisé de plusieurs façons:
		-PNJ() qui crée un pnj "vide".
		-PNJ($id) qui récupère les informations du pnj dans la base.
		-PNJ($nom, $x, $y, $image, $texte) qui crée un nouveau pnj.
		
		@param $id int Id du pnj dans la base.
		@param $nom String Nom du pnj.
		@param $x int Coordonnée en X du pnj.
		@param $y int Coordonnée en Y du pnj.
		@param $image String L'adresse? de l'image du pnj.
		@param $texte String
	*/
	function __construct($nom = '', $x = 0, $y = 0, $image = '', $texte = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$this->id = $nom;
			$requete = 'SELECT nom, x, y, image, texte FROM pnj WHERE id = '.$this->getId();
			$requeteSQL = $db->query($requete);
			list($this->nom, $this->x, $this->y, $this->image, $this->texte) = $db->read_row($requeteSQL);
		}
		else
		{
			parent::__construct($nom, $x, $y);
			$this->image = $image;
			$this->texte = $texte;
		}
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return placable::get_liste_champs().', image, texte';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return placable::get_valeurs_insert().', "$this->image", "$this->texte"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return placable::get_liste_update().', image = "$this->image", texte = "$this->texte"';
	}
	
	//! Accesseur $image
	function getImage()
	{
		return $this->image;
	}
	
	//! Modifieur $image
	function setImage($image)
	{
		$this->image = $image;
	}
	
	//! Afficher l'image du pnj
	function AfficheImage()
	{
		return '<img src="'.$this->image().'" alt="'.$this->nom.'" />';
	}
	
	//! Accesseur $texte
	function getTexte()
	{
		return $this->texte;
	}
	
	//! Modifieur $texte
	function setTexte($texte)
	{
		$this->texte = $texte;
	}
}
?>