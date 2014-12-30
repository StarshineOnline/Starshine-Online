<?php // -*- php -*-
/**
 * @file quete.class.php
 * Gestion des quetes
 */
 
/**
 * Classe représentant les quetes
 */
 
 // gerer les listes de quete

 
 class quete extends table
 {
	
	protected $id;  ///< id de la quete.
	protected $nom;  ///< Nom de la quete.
	protected $fournisseur;  ///< Fournisseur de la quete.
	protected $type;  ///< type de la quete. g/s/r
	protected $repetable;  ///< repetable ? y/n
	protected $royaume;  ///< Nom de la quete.
	protected $requis;  ///< requis pour réaliser la quete.
	protected $star_royaume;  ///< Cout de la quete pour le royaume.
	protected $nombre_etape;  ///< Nombre d'étape de la quete.
	protected $description;  ///< Nombre d'étape de la quete.

	/**
	 * Constructeur
	*/
	function __construct($id ='', $nom='', $fournisseur='bureau_quete', $type='g', $repetable='non', $royaume='', $requis='', $star_royaume='', $nombre_etape=0, $description ='')
	{
		
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->fournisseur = $fournisseur;
			$this->type = $type;
			$this->repetable = $repetable;
			$this->royaume = $royaume;
			$this->requis = $requis;
			$this->star_royaume = $star_royaume;
			$this->nombre_etape = $nombre_etape;
			$this->description = $description;

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
		$this->nom = $vals['nom'];
		$this->fournisseur = $vals['fournisseur'];
		$this->type = $vals['type'];
		$this->repetable = $vals['repetable'];
		$this->royaume = $vals['royaume'];
		$this->requis = $vals['requis'];
		$this->star_royaume = $vals['star_royaume'];
		$this->nombre_etape = $vals['nombre_etape'];
		$this->description = $vals['description'];
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
	// Renvoie le nom de l'objet
	function get_nom()
	{
		return $this->nom;
	}
	
	/// Modifie le nom de l'objet
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}
	
	// Renvoie le type de l'objet
	function get_type()
	{
		return $this->type;
	}
	
	/// Modifie le type de l'objet
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}
	
	// Renvoie le fournisseur de l'objet
	function get_fournisseur()
	{
		return $this->fournisseur;
	}
	
	/// Modifie le fournisseur de l'objet
	function set_fournisseur($fournisseur)
	{
		$this->fournisseur = $fournisseur;
		$this->champs_modif[] = 'fournisseur';
	}
	
	// Repetable ? y/n
	function get_repetable()
	{
		return $this->repetable;
	}
	
	/// Modifie repetable
	function set_repetable($repetable)
	{
		$this->repetable = $repetable;
		$this->champs_modif[] = 'repetable';
	}
	
	// Renvoie le cout pour le royaume de l'objet
	function get_star_royaume()
	{
		return $this->star_royaume;
	}
	
	/// Modifie le cout pour le royaume de l'objet
	function set_star_royaume($star_royaume)
	{
		$this->star_royaume = $star_royaume;
		$this->champs_modif[] = 'star_royaume';
	}
	
	// Renvoie le royaume de l'objet
	function get_royaume()
	{
		return $this->royaume;
	}
	
	/// Modifie le royaume de l'objet
	function set_royaume($royaume)
	{
		$this->royaume = $royaume;
		$this->champs_modif[] = 'royaume';
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
	
	// Renvoie le nombre_etape de l'objet
	function get_nombre_etape()
	{
		return $this->nombre_etape;
	}
	
	/// Modifie le nombre_etape de l'objet
	function set_nombre_etape($nombre_etape)
	{
		$this->nombre_etape = $nombre_etape;
		$this->champs_modif[] = 'nombre_etape';
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
	
	function achat(&$quete, &$royaume)
	{
		global $db;
	
		//Vérifie que le royaume a assez de stars pour l'acheter
		if($royaume->get_star() >= $quete->get_star_royaume())
		{
			//Ajout de la quête dans la liste des quêtes du royaume
			$requete = "INSERT INTO quete_royaume VALUES('', ".$royaume->get_id().", ".$quete->get_id().")";
			$req = $db->query($requete);
			//Mis a jour des stars du royaume
			$requete = "UPDATE royaume SET star = star - ".$quete->get_star_royaume()." WHERE ID = ".$royaume->get_id();
			$req = $db->query($requete);
			$cadre = new interf_dialogBS($quete->get_nom(), true, 'achat');
			$cadre->add( new interf_bal_smpl('span', 'Vous avez bien acheté la quête'.$quete->get_nom(), false, false));
		}
		else
		{
			$cadre = new interf_dialogBS($quete->get_nom(), true, 'achat');
			$cadre->add( new interf_bal_smpl('span', 'Votre royaume n\'a pas assez de stars pour acheter cette quête', false, false));
		}
	}

}
