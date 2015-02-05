<?php // -*- php -*-
/**
 * @file quete_etape.class.php
 * Gestion des etapes des quetes
 */
 
/**
 * Classe représentant les etapes des quetes
 */

class quete_etape extends quete
{
	protected $id;  ///< id de la etape.
	protected $id_quete;  ///< id de la quete.
	protected $id_etape;  ///< id de l'etape
	protected $variante;  
	protected $description;  ///< description
	protected $niveau;  ///< Niveu de l'etape
	protected $objectif;  ///< objectif de letape
	protected $type;  ///< type de quete : groupe/solo
	protected $gain_perso;  ///< gain solo
	protected $gain_groupe;  ///< gain de groupe
	

	/**
	* Constructeur
	*/
	function __construct($id ='', $id_quete='', $id_etape='', $variante='', $description='', $niveau= 1 , $objectif='', $type='', $gain_perso='', $gain_groupe ='')
	{
		
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->id_quete = $id_quete;
			$this->id_etape = $id_etape;
			$this->variante = $variante;
			$this->description = $description;
			$this->niveau = $niveau;
			$this->objectif = $objectif;
			$this->type = $type;
			$this->gain_perso = $gain_perso;
			$this->gain_groupe = $gain_groupe;

		}
	}	

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->id = $vals['id'];
		$this->id_quete = $vals['id_quete'];
		$this->id_etape = $vals['id_etape'];
		$this->variante = $vals['variante'];
		$this->description = $vals['description'];
		$this->niveau = $vals['niveau'];
		$this->objectif = $vals['objectif'];
		$this->type = $vals['type'];
		$this->gain_perso = $vals['gain_perso'];
		$this->gain_groupe = $vals['gain_groupe'];
	}
				
	// Renvoie le id de l'objet
	function get_id()
	{
		return $this->id;
	}
	
	/// Modifie le id de l'objet
	function set_id($id)
	{
		$this->id = $id;
		$this->champs_modif[] = 'id';
	}
	
	// Renvoie le id de la quete
	function get_id_quete()
	{
		return $this->id_quete;
	}
	
	/// Modifie le id de la quete
	function set_id_quete($id_quete)
	{
		$this->id_quete = $id_quete;
		$this->champs_modif[] = 'id_quete';
	}
	
	// Renvoie le id de l'étape
	function get_id_etape()
	{
		return $this->id_etape;
	}
	
	/// Modifie le id de l'étape
	function set_id_etape($id_etape)
	{
		$this->id_etape = $id_etape;
		$this->champs_modif[] = 'id_etape';
	}
	
	// Renvoie la variante de la quete
	function get_variante()
	{
		return $this->variante;
	}
	
	/// Modifie le variante de l'objet
	function set_variante($variante)
	{
		$this->variante = $variante;
		$this->champs_modif[] = 'variante';
	}
	
	// Renvoie la description de l'objet
	function get_description()
	{
		return $this->description;
	}
	
	/// Modifie la description de l'objet
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}
	
	// Renvoie le niveau de l'objet
	function get_niveau()
	{
		return $this->niveau;
	}
	
	/// Modifie le niveau de l'objet
	function set_niveau($niveau)
	{
		$this->niveau = $niveau;
		$this->champs_modif[] = 'niveau';
	}
	
	// Renvoie les objectifs de l'objet
	function get_objectif()
	{
		return $this->objectif;
	}
	
	/// Modifie le objectif de l'objet
	function set_objectif($objectif)
	{
		$this->objectif = $objectif;
		$this->champs_modif[] = 'objectif';
	}
	
	// Renvoie les objectifs de l'objet
	function get_objectif_txt()
	{
		global $db;
		// on décompose ici les objectifs pour l'affichage
		$objectifs = explode(';', $this->objectif);
		foreach ($objectifs as $obj)
		{
			//
			//on extrait la valeur de l'objectif
			$type = mb_substr($requis, 1);
			$valeur = explode(':', $type);
			switch ($obj['0']) {
				//tuer des monstres
				case 'm' : 
					$requete = "SELECT nom FROM monstre WHERE id = ". $valeur['1'];
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$txt = "Tuer ".$valeur['0']." ".$row['nom']." (s)";	
				//parler à un PNJ
				case 'p' : 
					$requete = "SELECT nom FROM pnj WHERE id = ". $valeur['1'];
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$txt = "Parler à ".$row['nom'];	
				//tuer des perso selon la diplomatie
				case 'j' : 
					include_once('../inc/diplo.inc.php');
					$txt = "Tuer ".$valeur['0']." personnage(s) dont la diplomatie est au moins ".$DIPLO[$valeur['1']]." (s)";		
				//trouver un objet
				case 'o' : 
					$requete = "SELECT nom FROM pnj WHERE id = ". $valeur['1'];
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$txt = "Trouver ".$valeur['0']." ";
				}		
		}
		
		return $txt;
	}
	
	
}
