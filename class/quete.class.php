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
	protected $terrain;  ///< Terrain de la quete.

	/**
	 * Constructeur
	*/
	function __construct($id ='', $nom='', $fournisseur='bureau_quete', $type='g', $repetable='non', $royaume='', $requis='', $star_royaume='', $nombre_etape=0)
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
			$this->terrain = $terrain;

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
		$this->terrain = $vals['terrain'];
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
	
	// Renvoie le terrain de la quête
	function get_terrain()
	{
		return $this->terrain;
	}
	
	/// Modifie le terrain de la quête
	function set_terrain($terrain)
	{
		$this->terrain = $terrain;
		$this->champs_modif[] = 'terrain';
	}
		
	function achat(&$royaume)
	{
		global $db;
	
		//Vérifie que le royaume a assez de stars pour l'acheter
		if($royaume->get_star() >= $this->get_star_royaume())
		{
			//Ajout de la quête dans la liste des quêtes du royaume
			$requete = "INSERT INTO quete_royaume VALUES('', ".$royaume->get_id().", ".$this->get_id().")";
			$req = $db->query($requete);
			//Mis a jour des stars du royaume
			$requete = "UPDATE royaume SET star = star - ".$this->get_star_royaume()." WHERE ID = ".$royaume->get_id();
			$req = $db->query($requete);
			interf_alerte::enregistre(interf_alerte::msg_succes, 'Vous avez bien acheté la quête '.$this->get_nom());
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Votre royaume n\'a pas assez de stars pour acheter cette quête');
	}

	/**
	 * Indique si le personnage a les requis	
	 */
	function a_requis(&$perso)
	{
		return self::verif_requis($this->requis, $perso);
	}
	/**
	 * Vérifie des requis exprimé sous forme textuelle	
	 */	
	static function verif_requis($requis, &$perso, $delim=';')
	{
		$requis = explode($delim, $requis);
		foreach($requis as $req)
		{
			if( !$req ) continue;
			//$val = mb_substr($req, 1);
			$vals = explode('|', mb_substr($req, 1));
			$ok = false;
			foreach($vals as $val)
			{
				switch( $req[0] ) 
				{
				case 'q':  // une quete doit être finies avant la présente
					if( $val[0] == '!' )
						$ok |= !in_array(mb_substr($val, 1), explode(';', $perso->get_quete_fini()));
					else
						$ok |= in_array($val, explode(';', $perso->get_quete_fini()));
					break;
				case 't':  // avancement du tutoriel
					switch($val[0])
					{
					case '>':
						$ok |= $perso->get_tuto() > mb_substr($val, 1);
						break;
					case '<':
						$ok |= $perso->get_tuto() < mb_substr($val, 1);
						break;
					case '=':
						$ok |= $perso->get_tuto() == mb_substr($val, 1);
						break;
					default:
						$ok |= $perso->get_tuto() == $val;
					}
					break;
				case 'c':  // classe
					if( $val[0] == '!' )
						$ok |= in_array(mb_substr($val, 1), $perso->get_classe_id());
					else
						$ok |= !in_array($val, $perso->get_classe_id());
					break;
				case 'r':  // race
					if( $val[0] == '!' )
						$ok |= in_array(mb_substr($val, 1), $perso->get_race());
					else
						$ok |= !in_array($val, $perso->get_race());
					break;
				case 'n': // niveau
					switch($val[0])
					{
					case '>':
						$ok |= $perso->get_level()> mb_substr($val, 1);
						break;
					case '<':
						$ok |= $perso->get_level() < mb_substr($val, 1);
						break;
					case '=': 
						$ok |= $perso->get_level() == mb_substr($val, 1);
						break;
					default:
						$ok |= $perso->get_level() >= $val;
					}
					break;
				case 'h':  // honneur
					switch($val[0])
					{
					case '>':
						$ok |= $perso->get_honneur()> mb_substr($val, 1);
						break;
					case '<':
						$ok |= $perso->get_honneur() < mb_substr($val, 1);
						break;
					case '=': 
						$ok |= $perso->get_honneur() == mb_substr($val, 1);
						break;
					default:
						$ok |= $perso->get_honneur() >= $val;
					}
					break;
				case 'p':  // réputation
					switch($val[0])
					{
					case '>':
						$ok |= $perso->get_reputation()> mb_substr($val, 1);
						break;
					case '<':
						$ok |= $perso->get_reputation() < mb_substr($val, 1);
						break;
					case '=': 
						$ok |= $perso->get_reputation() == mb_substr($val, 1);
						break;
					default:
						$ok |= $perso->get_reputation() >= $val;
					}
					break;
				}
			}
			if( !$ok )
				return false;
		}
		return true;
	}
	
	static function get_quetes_dispos(&$perso, &$royaume, $fournisseur, $terrain=false)
	{
		global $db;
		$finies = $perso->get_quete_fini();
		if( $finies[0] == ';' )
			$finies = substr($finies, 1);
		$notin = strlen($finies) > 0 ? 'AND NOT (quete.repetable = "non" AND quete.id IN ('.str_replace(';', ',', $finies).') )' : '';
		$id_royaume = $royaume->get_id();
		if($id_royaume < 10)
			$id_royaume = '0'.$id_royaume;
		$requete = 'SELECT quete.* FROM quete LEFT JOIN quete_royaume ON quete.id = quete_royaume.id_quete WHERE ((quete_royaume.id_royaume = '.$royaume->get_id().') OR ( royaume LIKE "%'.$id_royaume.'%")) AND quete.fournisseur = "'.$fournisseur.'"'.($terrain ? ' AND quete.terrain = "'.$terrain.'"' : '').' AND quete.id NOT IN (SELECT id_quete FROM quete_perso WHERE id_perso = '.$perso->get_id().') '.$notin;
		$req = $db->query($requete);
		
		$quetes = array();
		while($row = $db->read_array($req))
		{
			$quete = new quete($row);
			if( $quete->a_requis($perso) )
				$quetes[] = $quete;
		}
		return $quetes;
	}
	
	/// @todo à placer dans la classe quete_royaume quand elle existera
	static function get_nombre_quetes(&$perso, &$royaume, $fournisseur)
	{
		global $db;
		$finies = $perso->get_quete_fini();
		if( $finies[0] == ';' )
			$finies = substr($finies, 1);
		$notin = strlen($finies) > 0 ? 'AND NOT (quete.repetable = "non" AND quete.id IN ('.str_replace(';', ',', $finies).') )' : '';
		$id_royaume = $royaume->get_id();
		$requete = 'SELECT COUNT(*) FROM quete LEFT JOIN quete_royaume ON quete.id = quete_royaume.id_quete WHERE ((quete_royaume.id_royaume = '.$royaume->get_id().') OR ( royaume LIKE "%'.$id_royaume.'%")) AND quete.fournisseur = "'.$fournisseur.'" AND quete.id NOT IN (SELECT id_quete FROM quete_perso WHERE id_perso = '.$perso->get_id().') '.$notin;
		$req = $db->query($requete);
		$row = $db->read_array($req);
		return $row[0];
	}
}
