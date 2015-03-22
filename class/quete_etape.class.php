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
	protected $id;  ///< id de la étape.
	protected $id_quete;  ///< id de la quete.
	protected $id_etape;  ///< id de l'étape
	protected $variante;  
	protected $description;  ///< description
	protected $niveau;  ///< Niveu de l'étape
	protected $objectif;  ///< objectif de l'étape
	protected $collaboration;  ///< type de quete : groupe/solo
	protected $requis;  ///< requis pour réaliser le choix de la variante de l'étape.
	protected $gain_perso;  ///< gain solo
	protected $gain_groupe;  ///< gain de groupe
	

	/**
	* Constructeur
	*/
	function __construct($id ='', $id_quete='', $etape='', $variante='', $description='', $niveau= 1 , $objectif='', $collaboration='', $requis='', $gain_perso='', $gain_groupe ='')
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
			$this->etape = $etape;
			$this->variante = $variante;
			$this->description = $description;
			$this->niveau = $niveau;
			$this->objectif = $objectif;
			$this->collaboration = $collaboration;
			$this->requis = $requis;
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
		$this->etape = $vals['etape'];
		$this->variante = $vals['variante'];
		$this->description = $vals['description'];
		$this->niveau = $vals['niveau'];
		$this->objectif = $vals['objectif'];
		$this->collaboration = $vals['collaboration'];
		$this->requis = $vals['requis'];
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
	function get_etape()
	{
		return $this->etape;
	}
	
	/// Modifie le id de l'étape
	function set_etape($etape)
	{
		$this->etape = $etape;
		$this->champs_modif[] = 'etape';
	}
	
	// Renvoie la variante de l'étape
	function get_variante()
	{
		return $this->variante;
	}
	
	/// Modifie le variante de l'étape
	function set_variante($variante)
	{
		$this->variante = $variante;
		$this->champs_modif[] = 'variante';
	}
	
	// Renvoie le requis de l'objet
	function get_requis()
	{
		return $this->requis;
	}
	
	/// Modifie le requis de l'objet
	function set_requis($requis)
	{
		$this->requis = $requis;
		$this->champs_modif[] = 'requis';
	}
	
	// Renvoie la description de l'étape
	function get_description()
	{
		return $this->description;
	}
	
	/// Modifie la description de l'étape
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}
	
	// Renvoie le niveau conseillé
	function get_niveau()
	{
		return $this->niveau;
	}
	
	/// Modifie le niveau conseillé
	function set_niveau($niveau)
	{
		$this->niveau = $niveau;
		$this->champs_modif[] = 'niveau';
	}
	
	// Renvoie le type de collaboration
	function get_collaboration()
	{
		return $this->collaboration;
	}
	
	/// Modifie le type de collaboration
	function set_collaboration($collaboration)
	{
		$this->collaboration = $collaboration;
		$this->champs_modif[] = 'collaboration';
	}
	
	// Renvoie les objectifs de l'étape
	function get_objectif()
	{
		return $this->objectif;
	}
	
	/// Modifie l'objectif de l'étape
	function set_objectif($objectif)
	{
		$this->objectif = $objectif;
		$this->champs_modif[] = 'objectif';
	}
	
	// Renvoie les gains pour le personnage
	function get_gain_perso()
	{
		return $this->gain_perso;
	}
	
	/// Modifie les gains pour le personnage
	function set_gain_perso($gain_perso)
	{
		$this->gain_perso = $gain_perso;
		$this->champs_modif[] = 'gain_perso';
	}	
	
	// Renvoie les gains pour le groupe
	function get_gain_groupe()
	{
		return $this->gain_groupe;
	}
	
	/// Modifie les gains pour le groupe
	function set_gain_groupe($gain_groupe)
	{
		$this->gain_groupe = $gain_groupe;
		$this->champs_modif[] = 'gain_groupe';
	}
	
	/// Calcul le gain obtenu parmis les différentes possibilités
	static function calcul_gain($gain, &$perso)
	{
		$pref = false;
		$gains = explode('|', $gain);
		if( count($gains) == 1 )
		{
			$gains = explode('/', $gain);
			$pref = true;
		}
		$res = array();
		foreach($gains as $g)
		{
			$g = explode('#', $g);
			$ok = count($g) > 1 ? quete::verif_requis($g[1], $perso, '&') : true;
			if( $ok && $pref )
				return $g[0];
			$res[] = $g[0];
		}
		return $res;
	}
}
