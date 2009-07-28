<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//! Class Personnage
/**
 * Classe Abstraite Personnage
 * Cette classe à pour but de définir ce qu'est un personnage. Elle sera par la suite spécialisée.
 */
class MapMonstre extends Personnage
{
	protected $type;
	protected $hp;
	protected $level;
	protected $lib;
	protected $mort_naturelle;
	
	//! Constructeur
	/**
		Le constructeur accepte plusieurs types d'appels.
		-MapMonstre()
		-MapMonstre(id)
		-MapMonstre($nom,...,$mort_naturelle);
		
		@param $nom String 
		@param $coord_x int
		@param $coord_y int
		@param $type
		@param $hp
		@param $level
		@param $lib
		@param $mort_naturelle
		
	*/
	function __construct($nom = '', $coord_x = 0, $coord_y = 0, $type = 0, $hp = 0, $level = 0, $lib = '', $mort_naturelle = 0)
	{
		global $db;
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$this->id = $nom;
			$requete = 'SELECT nom, coord_x, coord_y, type, hp, level, lib, mort_naturelle FROM map_monstre WHERE id = '.$this->getId();
			$requeteSQL = $db->query($requete);
			list($this->nom, $this->coord_x, $this->coord_y, $this->type, $this->hp, $this->level, $this->lib, $this->mort_natuelle) = 
			$db->read_row($requeteSQL);
		}
		else
		{
			parent::__construct($nom, $coord_x, $coord_y);
			$this->type = $type;
			$this->hp = $hp;
			$this->level = $level;
			$this->lib = $lib;
			$this->mort_naturelle = $mort_naturelle;
		}
	}

	//! Accesseur $type
	function getType()
	{
		return $this->type;
	}
	
	//! Modifieur $type
	function setType($type)
	{
		$this->type = $type;
	}
	
	//! Accesseur $hp
	function getHP()
	{
		return $this->hp;
	}

	//! Modifieur $hp
	function setHP($hp)
	{
		$this->hp = $hp;
	}
	
	//! Accesseur $level
	function getLevel()
	{
		return $this->level;
	}

	//! Modifieur $level
	function setLevel($level)
	{
		$this->level = $level;
	}
	
	//! Accesseur $lib
	function getLib()
	{
		return $this->lib;
	}

	//! Modifieur $lib
	function setHP($lib)
	{
		$this->lib = $lib;
	}

	//! Accesseur $mort_naturelle
	function getMortNaturelle()
	{
		return $this->mort_naturelle;
	}

	//! Modifieur $mort_naturelle
	function setHP($mort)
	{
		$this->mort_naturelle = $mort;
	}

	//! Fonction de Suppression
	function supprimer()
	{
		parent::supprimer('map_monstre');
	}
	
	//! Fonction permettant d'ajouter ou modifier un atome de la table.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE TABLE map_monstre SET '.$this->modifBase();
			$requete .= ', type = "'.$this->type.'", hp = "'.$this->hp.'", level = "'.$this->level.'", lib = "'.$this->lib.
			'", mort_naturelle = "'.$this->mort_naturelle.'" WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO map_monstre(nom, x, y, type, hp, level, lib, mort_naturelle) VALUES(';
			$requete .= $this->insertBase().', "'.$this->type.'", "'.$this->hp.'", "'.$this->level.'", "'.$this->lib.'", "'.$this->mort_naturelle.'" )';
			$db->query($requete);
			//Récupère le dernier id inséré.
			$this->id = $db->last_insert_id();
		}

	}
	
?>