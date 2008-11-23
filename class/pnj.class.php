<?php
//Inclusion de la classe abstraite personnage
include_once('personnage.class.php');

//! Class PNJ
/**
   * Classe PNJ
   *
   * Classe représentant un PNJ et permettant de l'ajouter, le modifier ou le supprimer de la base.
   *
*/
class pnj extends Personnage
{
	private $image;
	private $texte;
	
	//! Constructeur
	/**
		Constructeur.
		
		Le constructeur peut être utilisé de plusieurs façons:
		-PNJ() qui crée un pnj "vide".
		-PNJ($id) qui récupère les informations du pnj dans la base.
		-PNJ($nom, $coord_x, $coord_y, $image, $texte) qui crée un nouveau pnj.
		
		@param $id int Id du pnj dans la base.
		@param $nom String Nom du pnj.
		@param $coord_x int Coordonnée en X du pnj.
		@param $coord_y int Coordonnée en Y du pnj.
		@param $image String L'adresse? de l'image du pnj.
		@param $texte String
	*/
	function __construct($nom = '', $coord_x = 0, $coord_y = 0, $image = '', $texte = '')
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$this->id = $nom;
			$requete = 'SELECT nom, coord_x, coord_y, image, texte FROM pnj WHERE id = '.$this->getId();
			$requeteSQL = $db->query($requete);
			list($this->nom, $this->coord_x, $this->coord_y, $this->image, $this->texte) = $db->read_row($requeteSQL);
		}
		else
		{
			parent::__construct($nom, $coord_x, $coord_y);
			$this->image = $image;
			$this->texte = $texte;
		}
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
	
	//! Fonction de Suppression
	function supprimer()
	{
		parent::supprimer('pnj');
	}
	
	//! Fonction permettant d'ajouter ou modifier un atome de la table.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE TABLE pnj SET '.$this->modifBase();
			$requete .= ', image = "'.$this->image.'", texte = "'.$this->texte.'" WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO pnj(nom, x, y, image, texte) VALUES(';
			$requete .= $this->insertBase().', "'.$this->image.'", "'.$this->texte.'")';
			$db->query($requete);
			//Récupère le dernier id inséré.
			list($this->id) = mysql_fetch_row($db->query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	//! toString
	function __toString()
	{
		return parent::__toString().', '. $this->image.', '.$this->texte;
	}
}
?>