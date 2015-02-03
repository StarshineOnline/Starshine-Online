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
	function get_description()
	{
		return $this->description;
	}
	
	/// Modifie le id de l'objet
	function set_description($id)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}
	
}
