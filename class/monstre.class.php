<?php // -*- mode: php; tab-width:2 -*-
/**
 * @file monstre.class.php
 * Définition de la classe monstre, représentant la définition d'un monstre.
 */

/**
 * Classe monstre
 * Cette classe représentant la définition d'un monstre.
 * Correspond à la table du même nom dans la base de données
 */
class monstre extends entitenj_def
{
	protected $lib;  ///< Nom interne du monstre
	protected $level;  ///< Niveau du monstre
	protected $xp;  ///< XP gagnés en combattan le monstre
	protected $star;  ///< Stars gagnées en tuant le monstre
	protected $drops;  ///< Objets gagnées en tuant le monstre
	protected $spawn;  ///< ?
	protected $spawn_loc;  ///< Lieu particulier d'apparition du monstre
	protected $terrain;  ///< Types de terrain où on peut trouver le monstre
	protected $affiche;  ///< Affichage des caractéristiques du monstre : y: affiche tout - n: n'affiche pas la description - h: cache aussi level, hp, caracs, et autres infos survie

	/// Renvoie le nom interne du monstre
	function get_lib()
	{
		return $this->lib;
	}
	/// Modifie le nom interne du monstre
	function set_lib($lib)
	{
		$this->lib = $lib;
		$this->champs_modif[] = 'lib';
	}

	/// Renvoie le niveau du monstre
	function get_level()
	{
		return $this->level;
	}
	/// Modifie le niveau du monstre
	function set_level($level)
	{
		$this->level = $level;
		$this->champs_modif[] = 'level';
	}

	/// Renvoie les XP gagnés en combattan le monstre
	function get_xp()
	{
		return $this->xp;
	}
	/// Modifie les XP gagnés en combattan le monstre
	function set_xp($xp)
	{
		$this->xp = $xp;
		$this->champs_modif[] = 'xp';
	}

	/// Renvoie les stars gagnées en tuant le monstre
	function get_star()
	{
		return $this->star;
	}
	/// Modifie les stars gagnées en tuant le monstre
	function set_star($star)
	{
		$this->star = $star;
		$this->champs_modif[] = 'star';
	}

	/// Renvoie les objets gagnées en tuant le monstre
	function get_drops()
	{
		return $this->drops;
	}
	/// Modifie les objets gagnées en tuant le monstre
	function set_drops($drops)
	{
		$this->drops = $drops;
		$this->champs_modif[] = 'drops';
	}

	/// Renvoie ?
	function get_spawn()
	{
		return $this->spawn;
	}
	/// Modifie ?
	function set_spawn($spawn)
	{
		$this->spawn = $spawn;
		$this->champs_modif[] = 'spawn';
	}

	/// Renvoie le lieu particulier d'apparition du monstre
	function get_spawn_loc()
	{
		return $this->spawn_loc;
	}
	/// Modifie le lieu particulier d'apparition du monstre
	function set_spawn_loc($spawn_loc)
	{
		$this->spawn_loc = $spawn_loc;
		$this->champs_modif[] = 'spawn_loc';
	}

	/// Renvoie le stypes de terrain où on peut trouver le monstre
	function get_terrain()
	{
		return $this->terrain;
	}
	/// Modifie les types de terrain où on peut trouver le monstre
	function set_terrain($terrain)
	{
		$this->terrain = $terrain;
		$this->champs_modif[] = 'terrain';
	}

	/// Renvoie l'affichage des caractéristiques du monstre
	function get_affiche()
	{
		return $this->affiche;
	}
	/// Modifie l'affichage des caractéristiques du monstre
	function set_affiche($affiche)
	{
		$this->affiche = $affiche;
		$this->champs_modif[] = 'affiche';
  }
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
   * Constructeur
   * @param  $id               Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param  $lib              Nom interne du monstre
   * @param  $nom              Nom de l'entité
   * @param  $type             Type de l'entité
   * @param  $hp               HP maximums de l'entité
   * @param  $pp               PP de l'entité
   * @param  $pm               PM de l'entité
	 * @param  $force            Attribut force
	 * @param  $dexterite        Attribut dexterite
	 * @param  $puissance        Attribut puissance
	 * @param  $volonte          Attribut volonté
	 * @param  $energie          Attribut énergie
	 * @param  $melee            Compétence de mêlée/tir
	 * @param  $esquive          Compétence d'esquive
	 * @param  $incantation      Compétence d'incantation
	 * @param  $sort_vie         Compétence de magie de la vie
	 * @param  $sort_mort        Compétence de nécromancie
	 * @param  $sort_element     Compétence de magie élémentaire
	 * @param  $dressage         Difficulté de dressage du monstre
	 * @param  $sort_dressage    Sorts et compétences hors combat disponibles une fois desssé
	 * @param  $arme             Arme utilisée
	 * @param  $action           Script du monstre
	 * @param  $level            Niveau du monstre
	 * @param  $xp               XP gagné en combattan le monstre
	 * @param  $star             Star gagnées en tuant le monstre
	 * @param  $drops            Objets gagnées en tuant le monstre
	 * @param  $spawn            ?
	 * @param  $spawn_loc        Lieu particulier d'apparition du monstre
	 * @param  $terrain          Types de terrain où on peut trouver le monstre
	 * @param  $affiche          Affichage des caractéristiques du monstre
   * @param  $description      Description de l'entité
   * @param  $sort_combat      Sorts de combats disponibles une fois desssé
   * @param  $comp_combat      Compétences de combats disponibles une fois desssé
	*/
	function __construct($id = 0, $lib = '', $nom = '', $type = '', $hp = '', $pp = '', $pm = '', $force = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $melee = '', $esquive = '', $incantation = '', $sort_vie = '', $sort_mort = '', $sort_element = '', $dressage = '', $sort_dressage = '', $arme = '', $action = '', $level = '', $xp = '', $star = '', $drops = '', $spawn = '', $spawn_loc = '', $terrain = '', $affiche = '', $description = '', $sort_combat = '', $comp_combat = '')
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      entitenj_def::__construct($id, $nom, $type, $hp, $pp, $pm, $description);
			$this->lib = $lib;
			$this->forcex = $forcex;
			$this->dexterite = $dexterite;
			$this->puissance = $puissance;
			$this->volonte = $volonte;
			$this->energie = $energie;
			$this->melee = $melee;
			$this->esquive = $esquive;
			$this->incantation = $incantation;
			$this->sort_vie = $sort_vie;
			$this->sort_mort = $sort_mort;
			$this->sort_element = $sort_element;
			$this->dressage = $dressage;
			$this->sort_dressage = $sort_dressage;
			$this->arme = $arme;
			$this->action = $action;
			$this->level = $level;
			$this->xp = $xp;
			$this->star = $star;
			$this->drops = $drops;
			$this->spawn = $spawn;
			$this->spawn_loc = $spawn_loc;
			$this->terrain = $terrain;
			$this->affiche = $affiche;
		  $this->sort_combat = $sort_combat;
		  $this->comp_combat = $comp_combat;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    entitenj_def::init_tab($vals);
		$this->lib = $vals['lib'];
		$this->forcex = $vals['forcex'];
		$this->dexterite = $vals['dexterite'];
		$this->puissance = $vals['puissance'];
		$this->volonte = $vals['volonte'];
		$this->energie = $vals['energie'];
		$this->melee = $vals['melee'];
		$this->esquive = $vals['esquive'];
		$this->incantation = $vals['incantation'];
		$this->sort_vie = $vals['sort_vie'];
		$this->sort_mort = $vals['sort_mort'];
		$this->sort_element = $vals['sort_element'];
		$this->dressage = $vals['dressage'];
		$this->sort_dressage = $vals['sort_dressage'];
		$this->arme = $vals['arme'];
		$this->action = $vals['action'];
		$this->level = $vals['level'];
		$this->xp = $vals['xp'];
		$this->star = $vals['star'];
		$this->drops = $vals['drops'];
		$this->spawn = $vals['spawn'];
		$this->spawn_loc = $vals['spawn_loc'];
		$this->terrain = $vals['terrain'];
		$this->affiche = $vals['affiche'];
		$this->sort_combat = $vals['sort_combat'];
		$this->comp_combat = $vals['comp_combat'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return entitenj_def::get_liste_champs().', lib, forcex, dexterite, puissance, volonte, energie, melee, esquive, incantation, sort_vie, sort_mort, sort_element, dressage, sort_dressage, arme, action, level, xp, star, drops, spawn, spawn_loc, terrain, affiche, sort_combat, comp_combat';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{//.', "'.mysql_escape_string($this->type).'", '.$this->rez
		return entitenj_def::get_valeurs_insert().', '.$this->lib.', '.$this->forcex.', '.$this->dexterite.', '.$this->volonte.', '.$this->energie.', '.$this->esquive.', '.$this->incantation.', '.$this->sort_vie.', '.$this->sort_mort.', '.$this->sort_element.', '.$this->dressage.', "'.mysql_escape_string($this->sort_dressage).'", "'.mysql_escape_string($this->arme).'", "'.mysql_escape_string($this->action).'", '.$this->level.', '.$this->xp.', '.$this->star.', "'.mysql_escape_string($this->drops).'", '.$this->spawn.', "'.mysql_escape_string($this->spawn_loc).'", "'.mysql_escape_string($this->terrain).'", "'.mysql_escape_string($this->affiche).'", "'.mysql_escape_string($this->sort_combat).'", "'.mysql_escape_string($this->comp_combat).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{//.', type = "'.mysql_escape_string($this->type).'", rez = '.$this->rez
		return entitenj_def::get_liste_update().', lib = "'.mysql_escape_string($this->lib).'", forcex = '.$this->forcex.', dexterite = '.$this->dexterite.', volonte = '.$this->volonte.', energie = '.$this->energie.', esquive = '.$this->esquive.', incantation = '.$this->incantation.', sort_vie = '.$this->sort_vie.', sort_mort = '.$this->sort_mort.', sort_element = '.$this->sort_element.', dressage = '.$this->dressage.', sort_dressage = "'.mysql_escape_string($this->sort_dressage).'", arme = "'.mysql_escape_string($this->arme).'", action = "'.mysql_escape_string($this->action).'", level = '.$this->level.', xp = '.$this->xp.', stars = '.$this->star.', drops = "'.mysql_escape_string($this->drops).'", spawn = '.$this->spawn.', spawn_loc = "'.mysql_escape_string($this->spawn_loc).'", terrain = "'.mysql_escape_string($this->terrain).'", affiche = "'.mysql_escape_string($this->affiche).'", sort_combat = "'.mysql_escape_string($this->sort_combat).'", comp_combat = "'.mysql_escape_string($this->comp_combat).'"';
	}
	// @}

	/**
	 * @name Caractéristiques
	 * Données et méthodes liées aux caractéristiques du personnage : constitution,
	 * force, dextérité, puissance, volonté et énergie.
	 */
  // @{
	protected $forcex;  ///< Caractéristique force
	protected $dexterite;  ///< Caractéristique dexterite
	protected $puissance;  ///< Caractéristique puissance
	protected $volonte;  ///< Caractéristique volonté
	protected $energie;  ///< Caractéristique énergie

  /// Renvoie la caractéristique constitution
  function  get_constitution()
  {
    return 15;
  }
  
	/// Renvoie la caractéristique force
	function get_force()
	{
		return $this->forcex;
	}
	/**
	 * Renvoie la caractéristique force
	 * @deprecated Utiliser get_force() à la place
	 */
	function get_forcex()
	{
		return $this->forcex;
	}
	/// Modifie la caractéristique force
	function set_force($force)
	{
		$this->forcex = $force;
		$this->champs_modif[] = 'forcex';
	}
	/**
	 * Modifie la caractéristique force
	 * @deprecated Utiliser set_force() à la place
	 */
	function set_forcex($forcex)
	{
		$this->forcex = $forcex;
		$this->champs_modif[] = 'forcex';
	}

	/// Renvoie la caractéristique dexterite
	function get_dexterite()
	{
		return $this->dexterite;
	}
	/// Modifie la caractéristique dexterite
	function set_dexterite($dexterite)
	{
		$this->dexterite = $dexterite;
		$this->champs_modif[] = 'dexterite';
	}

	/// Renvoie la caractéristique puissance
	function get_puissance()
	{
		return $this->puissance;
	}
	/// Modifie la caractéristique puissance
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
	}

	/// Renvoie la caractéristique volonté
	function get_volonte()
	{
		return $this->volonte;
	}
	/// Modifie la caractéristique volonté
	function set_volonte($volonte)
	{
		$this->volonte = $volonte;
		$this->champs_modif[] = 'volonte';
	}

	/// Renvoie la caractéristique énergie
	function get_energie()
	{
		return $this->energie;
	}
	/// Modifie la caractéristique énergie
	function set_energie($energie)
	{
		$this->energie = $energie;
		$this->champs_modif[] = 'energie';
	}
  // @}

	/**
	 * @name Compétences
	 * Données et méthodes liées aux compténtences du personnage : mêlée, esquive,
	 * incatation, …
	 */
	// @{
	protected $melee;  ///< Compétence de mêlée/tir
	protected $esquive;  ///< Compétence d'esquive
	protected $incantation;  ///< Compétence d'incantation
	protected $sort_vie;  ///< Compétence de magie de la vie
	protected $sort_mort;  ///< Compétence de nécromancie
	protected $sort_element;  ///< Compétence de magie élémentaire

	/// Renvoie la compétence de mêlée/tir
	function get_melee()
	{
		return $this->melee;
	}
  /// Renvoie la valeur de la compétence de tir
	function get_distance()
	{
		if ($this->arme == 'arc')
			return $this->melee;
		else
			return 0;
	}
	/// Modifie la compétence de mêlée/tir
	function set_melee($melee)
	{
		$this->melee = $melee;
		$this->champs_modif[] = 'melee';
	}

	/// Renvoie la compétence d'esquive
	function get_esquive()
	{
		return $this->esquive;
	}
	/// Modifie la compétence d'esquive
	function set_esquive($esquive)
	{
		$this->esquive = $esquive;
		$this->champs_modif[] = 'esquive';
	}

	/// Renvoie la compétence d'incantation
	function get_incantation()
	{
		return $this->incantation;
	}
	/// Modifie la compétence d'incantation
	function set_incantation($incantation)
	{
		$this->incantation = $incantation;
		$this->champs_modif[] = 'incantation';
	}

	/// Renvoie la compétence de magie de la vie
	function get_sort_vie()
	{
		return $this->sort_vie;
	}
	/// Modifie la compétence de magie de la vie
	function set_sort_vie($sort_vie)
	{
		$this->sort_vie = $sort_vie;
		$this->champs_modif[] = 'sort_vie';
	}

	/// Renvoie la compétence de nécromancie
	function get_sort_mort()
	{
		return $this->sort_mort;
	}
	/// Modifie la compétence de nécromancie
	function set_sort_mort($sort_mort)
	{
		$this->sort_mort = $sort_mort;
		$this->champs_modif[] = 'sort_mort';
	}

	/// Renvoie la compétence de magie élémentaire
	function get_sort_element()
	{
		return $this->sort_element;
	}
	/// Modifie la compétence de magie élémentaire
	function set_sort_element($sort_element)
	{
		$this->sort_element = $sort_element;
		$this->champs_modif[] = 'sort_element';
	}

	/**
	 * Modifie la valeur s'une compétence
	 * @param  $comp_assoc   Compétence à modifier
	 * @param  $valeur       Nouvelle valeur
	 */
	function set_comp($comp_assoc = '', $valeur = '')
	{
		$set = 'set_'.$comp_assoc;
		if(method_exists($this, $set)) $this->$set($valeur);
		else $this->set_competence($comp_assoc, $valeur);
	}
	// @}

	/**
	 * @name Dresssage
	 * Données et méthodes liées au dressage.
	 */
  // @{
	protected $dressage;  ///< Difficulté de dressage du monstre
	protected $sort_dressage;  ///< Sorts et compétences hors combat disponibles une fois desssé
	protected $sort_combat;  ///< Sorts de combat.
	protected $comp_combat;     ///< Compétences de combat.

	/// Renvoie la difficulté de dressage du monstre
	function get_dressage()
	{
		return $this->dressage;
	}
	/// Modifie la difficulté de dressage du monstre
	function set_dressage($dressage)
	{
		$this->dressage = $dressage;
		$this->champs_modif[] = 'dressage';
	}

	/// Renvoie les sorts et compétences hors combat disponibles une fois desssé
	function get_sort_dressage()
	{
		return $this->sort_dressage;
	}
	/// Modifie les sorts et compétences hors combat disponibles une fois desssé
	function set_sort_dressage($sort_dressage)
	{
		$this->sort_dressage = $sort_dressage;
		$this->champs_modif[] = 'sort_dressage';
	}

	/// Renvoie les sorts de combat disponibles une fois desssé.
	function get_sort_combat()
	{
		return $this->sort_combat;
	}
	/// Modifie les sorts de combat disponibles une fois desssé
	function set_sort_combat($sort_combat)
	{
		$this->sort_combat = $sort_combat;
		$this->champs_modif[] = 'sort_combat';
	}
	
	/// Renvoie les compétences de combat disponibles une fois desssé.
	function get_comp_combat()
	{
		return $this->comp_combat;
	}
	/// Modifie les compétences de combat disponibles une fois desssé
	function set_comp_combat($comp_combat)
	{
		$this->comp_combat = $comp_combat;
		$this->champs_modif[] = 'comp_combat';
	}

  /// Renvoie les informations sur le sort ou la compétence hors combat disponible une fois desssé
	function get_infos_sort_dressage()
	{
		if($this->sort_dressage[0] == 's')
		{
			return new sort_jeu(substr($this->sort_dressage, 1));
		}
		else
		{
			return new comp_jeu(substr($this->sort_dressage, 1));
		}
	}
	// @}

	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	protected $arme;  ///< Arme utilisée
	protected $action;  ///< Script du monstre

	/// Renvoie l'arme utilisée
	function get_arme()
	{
		return $this->arme;
	}
	/// Modifie l'arme utilisée
	function set_arme($arme)
	{
		$this->arme = $arme;
		$this->champs_modif[] = 'arme';
	}

	/// Renvoie le script du monstre
	function get_action()
	{
		return $this->action;
	}
	/// Modifie le script du monstre
	function set_action($action)
	{
		$this->action = $action;
		$this->champs_modif[] = 'action';
	}
	
	/// Renvoie la RM
	function get_reserve()
	{
		return ceil(2.1 * ($this->energie + floor(($this->energie - 8) / 2)));
	}
	// @}

  /// Est-ce vraiment utile ?
	function get_buff()
	{
		if(isset($this->buff))
			return $this->buff;
		else
			return false;
	}

}
?>
