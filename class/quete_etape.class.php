<?php // -*- php -*-
/**
 * @file quete_etape.class.php
 * Gestion des etapes des quetes
 */
 
/**
 * Classe représentant les etapes des quetes
 */

class quete_etape extends table
{
	protected $id_quete;  ///< id de la quete.
	protected $etape;  ///< numéro de l'étape
	protected $variante;  
	protected $nom;  
	protected $description;  ///< description
	protected $niveau;  ///< Niveu de l'étape
	protected $objectif;  ///< objectif de l'étape
	protected $collaboration;  ///< type de quete : groupe/solo
	protected $requis;  ///< requis pour réaliser le choix de la variante de l'étape.
	protected $gain_perso;  ///< gain solo
	protected $gain_groupe;  ///< gain de groupe
	protected $gain_royaume;  ///< gain du royaume
	protected $init;  ///< action d'initialisation de l'étape
	

	/**
	* Constructeur
	*/
	function __construct($id ='', $id_quete='', $etape='', $variante='', $nom='', $description='', $niveau= 1 , $objectif='', $collaboration='', $requis='', $gain_perso='', $gain_groupe ='', $gain_groupe ='', $init='')
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
			$this->nom = $nom;
			$this->description = $description;
			$this->niveau = $niveau;
			$this->objectif = $objectif;
			$this->collaboration = $collaboration;
			$this->requis = $requis;
			$this->gain_perso = $gain_perso;
			$this->gain_groupe = $gain_groupe;
			$this->gain_royaume = $gain_royaume;
			$this->init = $init;
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
		$this->nom = $vals['nom'];
		$this->description = $vals['description'];
		$this->niveau = $vals['niveau'];
		$this->objectif = $vals['objectif'];
		$this->collaboration = $vals['collaboration'];
		$this->requis = $vals['requis'];
		$this->gain_perso = $vals['gain_perso'];
		$this->gain_groupe = $vals['gain_groupe'];
		$this->gain_royaume = $vals['gain_royaume'];
		$this->init = $vals['init'];
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
	
	// Renvoie le nom de l'étape
	function get_nom()
	{
		return $this->nom;
	}
	
	/// Modifie le nom de l'étape
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
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
	
	// Renvoie les gains pour le royaume
	function get_gain_royaume()
	{
		return $this->gain_royaume;
	}
	
	// Renvoie les actions d'initialisation de l'étape
	function get_init()
	{
		return $this->init;
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
		global $Gtrad, $db;
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
				$perso->set_exp( $perso->get_exp() + $gains );
				//$texte[] = $gains.' points d\'expérience';
				break;
			case 'h':  // honneur
				if($perso->is_buff('moral'))
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
				switch($apt[1])
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
				$req_r = $db->query($requete);
				if($db->num_rows == 0)
				{
					$requete = "SELECT * FROM recette WHERE id = ".$gains;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					$texte[] = ' recette de '.$row_r['nom'].' X '.$reward_nb;
					//On lui donne la recette
					$requete = "INSERT INTO perso_recette VALUES(NULL, ".$reward_id_objet.", ".$perso->get_id().", 0)";
					$db->query($requete);
				}
				break;
			case 'f':  // recette de forge
				/// @todo passer à l'objet
				$requete = 'SELECT * FROM perso_forge WHERE id_perso = '.$perso->get_id().' AND id_recette = '.$gains;
				$req = $db->query($requete);
				if( $db->num_rows == 0 )
				{
					$requete = 'INSERT INTO perso_forge (id_perso, id_recette) VALUES ('.$perso->get_id().', '.$gains.')';
					$req = $db->query($requete);
				}
				break;
			// @todo b : (de)buff
			case 't': // achievement
				$joueur->unlock_achiev($gains);
				break;
			}
		}
		// On vérifie si la quête a déjà était fini, si non, on la mets dans les quêtes finies
		$quete_fini = explode(';', $perso->get_quete_fini());
		if(!in_array($id_quete, $quete_fini))
		{
			$quete_fini[] = $id_quete;
			$perso->set_quete_fini(implode(';', $quete_fini));
		}
		$perso->sauver();
		// Texte
		if( !$cache )
		{
			// Mis dans le journal
			$requete = "INSERT INTO journal VALUES(NULL, ".$perso->get_id().", 'f_quete', '".$perso->get_nom()."', '', NOW(), '".addslashes($nom)."', 0, 0, 0)";
			$db->query($requete);
			return $perso->get_nom().' finit la quête "'.$nom.($texte ? '", et gagne '.implode(', ',$texte) : '"').'.</br>';
		}
		return '';
	}
	function gain_groupe(&$perso)
	{
		/// @todo à faire
	}
	function gain_royaume(&$perso)
	{
		global $Trace, $db;
		if( $this->get_gain_royaume() )
		{
			$royaume = new royaume( $Trace[$perso->get_race()]['numrace'] );
			$gains = explode(';', $this->get_gain_royaume());
			foreach($gains as $recomp)
			{
				$gains = mb_substr($recomp, 1);
				switch( $recomp[0] )
				{
				case 's': // stars
					$royaume->set_star( $royaume->get_star() + $gains );
					break;
				case 'p': // pierre
					$royaume->set_pierre( $royaume->get_pierre() + $gains );
					break;
				case 'a': // sable
					$royaume->set_sable( $royaume->get_sable() + $gains );
					break;
				case 'b': // bois
					$royaume->set_bois( $royaume->get_bois() + $gains );
					break;
				case 'e': // eau
					$royaume->set_eau( $royaume->get_eau() + $gains );
					break;
				case 'c': // charbon
					$royaume->set_charbon( $royaume->get_charbon() + $gains );
					break;
				case 'm': // essence magique
					$royaume->set_essence( $royaume->get_essence() + $gains );
					break;
				case 'n': // nourriture
					$royaume->set_food( $royaume->get_food() + $gains );
					break;
				case 'v': // points de victoire
					$royaume->set_point_victoire_total( $royaume->get_point_victoire_total() + $gains );
					break;
				case 'r': // points de royaume
					$royaume->set_point_victoire( $royaume->get_point_victoire() + $gains );
					break;
				case 'o': // objet dans le quartier général
					$gains = explode('*', $gains);
					$nbr = count($gains) > 1 ? $gains[1] : 1;
					for($i=0; $i<$nbr; $i++)
					{
						$requete = 'INSERT INTO depot_royaume (id_objet, id_royaume) VALUES ('.$gains[0].', '.$royaume->get_id().')';
						$db->query($requete);
					}
					break;
				case 'x': // Supprimer un monstre
					/// @todo passer à l'objet
					$requete = 'DELETE FROM map_monstre WHERE type = '.$gains;
					$db->query($requete);
					break;
				case 'X': // Supprimer un bâtiment
					/// @todo passer à l'objet
					$requete = 'DELETE FROM construction WHERE id_batiment = '.$gains;
					$db->query($requete);
					break;
				}
			}
			$royaume->sauver();
		}
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
	
	function initialiser(&$perso)
	{
		global $db;
		$init = explode(';', $this->init);
		foreach($init as $act)
		{
			$param = explode(':', substr($act, 1));
			$cond = count($param) > 1 ? explode(',', $param[1]) : array();
			switch($act[0])
			{
			case 'C': // Création d'un bâtiment
				// le bâtiment existe-t-il déjà ?
				$constr = construction::create('id_batiment', $param[0]);
				$agit = count($constr) == 0 || count( quete_etape::create('etape', $this->id) ) == 0;
				if( $agit )
				{
					// choix de la case
					$where = ' WHERE 1'.$this->cond_terrain($cond, $perso);
					$requete = 'SELECT x, y FROM map '.$where.' ORDER BY RAND() LIMIT 1';
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					if( !$row )
						return false;
					if( $constr )
					{
						$constr[0]->set_x( $row['x'] );
						$constr[0]->set_y( $row['y'] );
						$constr[0]->sauver();
					}
					else
					{
						$bat = new batiment($param[0]);
						$constr = new construction(0, $param[0], $row['x'], $row['y'], 0, $bat->get_hp(), $bat->get_nom());
						$constr->sauver();
					}
				}
				break;
			case 'c': // Création d'un monstre
				// le monstre existe-t-il déjà ?
				$mm = map_monstre::create('type', $param[0]);
				$agit = count($mm) == 0 || count( quete_etape::create('id_etape', $this->id) ) == 0;
				if( $agit )
				{
					// choix de la case
					$where = ' WHERE 1'.$this->cond_terrain($cond, $perso);
					$requete = 'SELECT x, y FROM map '.$where.' ORDER BY RAND() LIMIT 1';
					$req = $db->query();
					$row = $db->read_assoc($req);
					if( !$row )
						return false;
					if( $mm )
					{
						$mm[0]->set_x( $row['x'] );
						$mm[0]->set_y( $row['y'] );
						$mm[0]->sauver();
					}
					else
					{
						$monstre = new monstre($param[0]);
						$mm = new map_monstre(0, $param[0], $row['x'], $row['y'], 0, $monstre->get_hp(), $monstre->get_level(), $monstre->get_nom(), $monstre->get_lib(), '99999999999');
						$mm->sauver();
					}
				}
				break;
			}
		}
	}
	protected function cond_terrain($cond, &$perso)
	{
		global $Trace;
		foreach( $cond as $c )
		{
			$p = explode('|', substr($c, 1));
			switch($c[0])
			{
			case 't': // type de terrain
				// type dédoubles
				$where .= ' AND type IN ('.implode(',', $p).')';
				break;
			case 'r':  // royaume d'appartenance du terrain
				if( $p[0] == 'p' )
					$where .= ' AND royaume = '.$Trace[$perso->get_race()]['numrace'];
				else
					$where .= ' AND royaume IN ('.implode(',', $p).')';
				break;
			case 'z': // zone
				if( $p[0] <= 1 )
					$where .= ' AND x < 190 AND y < 190';
			}
		}
	}
}
