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
	
	function fin(&$perso, $cache=false)
	{
		global $Gtrad;
		$texte = array();
		$nom = $this->get_nom();
		if( !$nom )
		{
			$quete = new quete( $this->id_quete );
			$nom = $quete->get_nom();
		}
		// Gains du personnage
		$gains = explode(';', $this->get_gain_perso());
		foreach($gains as $recomp)
		{
			$valeurs = mb_substr($recomp, 1);
			$gains = self::calcul_gain($valeurs, $perso);
			if( is_array($gains) )
			{
				$i = rand(0, count($gains)-1);
				$gains = $gains[$i];
			}
			switch($recomp[0])
			{
			case 'o': // objet
				if( $gains == '?' )
				{
					$objs = objet::create(false, false, 'RAND() LIMIT 1', false, 'type != "objet_quete"');
					$obj = $objs[0];
				}
				else
					$obj = objet::factory($gains);
				$perso->prend_objet( $obj->get_texte() );
				$texte[] = 'un(e) '.$obj->get_nom();
				break;
			case 's': // stars
				if($perso->get_race() == 'nain')
					$gains *= 1.05;
				$perso->get_grade();
				$gains = round($gains * (1 + ($perso->grade->get_rang() * 2 / 100)));
				$perso->add_star($gains);
				$texte[] = $gains.' stars';
				break;
			case 'e':  // expérience
				$perso->set_experience( $perso->get_experience() + $gains );
				$texte[] = $gains.' points d\'expérience';
				break;
			case 'h':  // honneur
				if($joueur->is_buff('moral'))
					$gains = round($gains * (1 + ($perso->get_buff('moral', 'effet') / 100)));
				$perso->set_honneur( $perso->get_honneur() + $gains );
				$texte[] = $gains.' points d\'honneur';
				break;
			case 'p':  // réputation
				$perso->set_reputation( $perso->get_reputation() + $gains );
				$texte[] = $gains.' points de réputation';
				break;
			case 'a':  // points d'aptitude
				$apt = explode(':', $gains);
				switch($apt[1)
				{
				case 'melee':
					$perso->set_melee($perso->get_melee() + $apt[1]);
					break;
				case 'distance':
					$perso->set_distance($perso->get_distance() + $apt[1]);
					break;
				case 'esquive':
					$perso->set_esquive($perso->get_esquive() + $apt[1]);
					break;
				case 'blocage':
					$perso->set_blocage($perso->get_blocage() + $apt[1]);
					break;
				case 'incantation':
					$perso->set_incantation($perso->get_incantation() + $apt[1]);
					break;
				case 'sort_element':
					$perso->set_sort_element($perso->get_sort_element() + $apt[1]);
					break;
				case 'sort_vie':
					$perso->set_sort_vie($perso->get_sort_vie() + $apt[1]);
					break;
				case 'sort_mort':
					$perso->set_sort_mort($perso->get_sort_mort() + $apt[1]);
					break;
				case 'dressage':
					$perso->set_dressage($perso->get_dressage() + $apt[1]);
					break;
				case 'survie':
					$perso->set_survie($perso->get_survie() + $apt[1]);
					break;
				case 'architecture':
					$perso->set_architecture($perso->get_architecture() + $apt[1]);
					break;
				case 'forge':
					$perso->set_forge($perso->get_forge() + $apt[1]);
					break;
				case 'alchimie':
					$perso->set_alchimie($perso->get_alchimie() + $apt[1]);
					break;
				case 'identification':
					$perso->set_identification($perso->get_identification() + $apt[1]);
					break;
				default:
					$valeur = $perso->get_competence($apt[0]) + $apt[1];
					$perso->set_competence($apt[0], $valeur);
				}
				$texte[] = $gains.' points de en '.$Gtrad[$apt[0]];
				break;
			case 'r':  // recette d'alchimie
				$requete = "SELECT id FROM perso_recette WHERE id_recette = ".$gains." AND id_perso = ".$perso->get_id();
				//echo $requete;
				$req_r = $db->query($requete);
				if($db->num_rows == 0)
				{
					$requete = "SELECT * FROM recette WHERE id = ".$gains;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					$texte[] = ' recette de '.$row_r['nom'].' X '.$reward_nb;
					//On lui donne la recette
					$requete = "INSERT INTO perso_recette VALUES(NULL, ".$reward_id_objet.", ".$joueur->get_id().", 0)";
					$db->query($requete);
				}
				break;
			/*case 'f':  // recette de forge
				break;*/
			// @todo b : (de)buff
			case 't': // achievement
				$joueur->unlock_achiev($gains);
				break;
			}
		}
		/// @todo gains du groupe
		// Texte
		if( !$cache )
			interf_alerte::enregistre(interf_alerte::succes, $perso->get_nom().' finit la quête "'.$nom.'", et gagne '.implode(', ',$texte).'.';);
		// Mis dans le journal
		$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'f_quete', '".$perso->get_nom()."', '', NOW(), '".addslashes($nom)."', 0, 0, 0)";
		$db->query($requete);
		// On vérifie si la quête a déjà était fini, si non, on la mets dans les quêtes finies
		$quete_fini = explode(';', $perso->get_quete_fini());
		if(!in_array($id_quete, $quete_fini))
		{
			$quete_fini[] = $id_quete;
			$perso->set_quete_fini(implode(';', $quete_fini));
		}
		$perso->sauver();
	}
	function verif_inventaire(&$perso)
	{
		$objectifs = explode(';', $this->objectif);
		$objets = array();
		foreach($objectifs as $obj)
		{
			$type = mb_substr($obj, 1);
			$valeur = explode(':', $type);
			if( $type == 'o' )
			{
				$res = $perso->recherche_objet($valeur[0]);
				if(!$res || $res[0] < $valeur[1])
				{
					$check = false;
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous manque au moins un objet');
					return false;
				}
				else
					$objets[$valeur[0]] = $valeur[1];
			}
		}
		return $objets;
	}
}
