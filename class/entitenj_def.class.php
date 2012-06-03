<?php
/**
 * @file entitenj_def.class.php
 * Définition de la classe entitenj_def
 */

/**
 * Classe abstraite entitenj_def
 * Classe de base pour les définitions des entités non joueurs (monstre, batiment)
 */
abstract class entitenj_def extends table
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $nom;  ///< Nom de l'entité
	protected $type;  ///< Type de l'entité
	protected $hp;  ///< HP maximum de l'entité
	protected $pp;  ///< PP de l'entité
	protected $pm;  ///< PM de l'entité
	protected $description;  ///< Description de l'entité

	/// Renvoie le nom de l'entité
	function get_nom()
	{
		return $this->nom;
	}
	/// Modifie le nom de l'entité
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/// Renvoie le type de l'entité
	function get_type()
	{
		return $this->type;
	}
	/// Modifie le type de l'entité
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/// Renvoie les HP maximum de l'entité
	function get_hp()
	{
		return $this->hp;
	}
	/// Modifie les HP maximum de l'entité
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}

	/// Renvoie la PP de l'entité
	function get_pp()
	{
		return $this->pp;
	}
	/// Modifie la PP de l'entité
	function set_pp($pp)
	{
		$this->pp = $pp;
		$this->champs_modif[] = 'pp';
	}

	/// Renvoie la PM de l'entité
	function get_pm()
	{
		return $this->pm;
	}
	/// Modifie la PM de l'entité
	function set_pm($pm)
	{
		$this->pm = $pm;
		$this->champs_modif[] = 'pm';
	}

	/// Renvoie la description de l'entité
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description de l'entité
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

	/// Renvoie le niveau du monstre
	function get_level()
	{
    return 1;
  }
	/// Renvoie les stars gagnées en tuant le monstre
	function get_star()
	{
    return 0;
  }
	/// Renvoie les points de victoire gagnés lors de la destruction du bâtiment
	function get_point_victoire()
	{
    return 0;
  }
  /// Renvoie l'espèce
	function get_espece()
	{
    return $this->type;
  }
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
  /**
   * Constructeur
   * @param  $id            Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param  $nom           Nom de l'entité
   * @param  $type          Type de l'entité
   * @param  $hp            HP maximums de l'entité
   * @param  $pp            PP de l'entité
   * @param  $pm            PM de l'entité
   * @param  $description   Description de l'entité
   */
  function __construct($id = 0, $nom = '', $type = '', $hp = 0, $pp = 0, $pm = 0, $description = '')
  {
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->nom = $nom;
			$this->type = $type;
			$this->hp = $hp;
			$this->pp = $pp;
			$this->pm = $pm;
			$this->description = $description;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->hp = $vals['hp'];
		$this->pp = $vals['pp'];
		$this->pm = $vals['pm'];
		$this->description = $vals['description'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'nom, type, hp, pp, pm, description';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return mysql_escape_string($this->nom).', '.mysql_escape_string($this->type).', '.$this->hp.', '.$this->pp.', '.$this->pm.', "'.mysql_escape_string($this->description).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", hp = '.$this->hp.', pp = '.$this->pp.', pm = '.$this->pm.', description = "'.mysql_escape_string($this->description).'"';
	}
	// @}

	/**
	 * @name Caractéristiques
	 * Données et méthodes liées aux caractéristiques du personnage : constitution,
	 * force, dextérité, puissance, volonté et énergie.
	 */
  // @{
  /// Renvoie la caractéristique constitution
  abstract function  get_constitution();
	/// Renvoie la caractéristique force
  abstract function  get_force();
	/// Renvoie la caractéristique dexterite
  abstract function  get_dexterite();
	/// Renvoie la caractéristique puissance
  abstract function  get_puissance();
	/// Renvoie la caractéristique volonté
  abstract function  get_volonte();
	/// Renvoie la caractéristique énergie
  abstract function  get_energie();
  // @}

	/**
	 * @name Compétences
	 * Données et méthodes liées aux compténtences du personnage : mêlée, esquive,
	 * incatation, …
	 */
	// @{
	/// Renvoie la compétence de mêlée
	abstract function get_melee();
  /// Renvoie la valeur de la compétence de tir
	abstract function get_distance();
	/// Renvoie la compétence d'esquive
	abstract function get_esquive();
	/// Renvoie la compétence d'incantation
	abstract function get_incantation();
	/// Renvoie la compétence de magie de la vie
	abstract function get_sort_vie();
	/// Renvoie la compétence de nécromancie
	abstract function get_sort_mort();
	/// Renvoie la compétence de magie élémentaire
	abstract function get_sort_element();
	/// Renvoie la compétence deblocage
	function get_blocage() { return 0; }
	// @}

	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	/// Renvoie l'arme utilisée
	abstract function get_arme();
	/// Renvoie le script du monstre
	abstract function get_action();
	/// Renvoie les dégâts réduit par le bouclier
	function get_bouclier() { return 0; }
	/// Renvoie la RM
	abstract function get_reserve();
	/// Renvoie le facteur de dégâts de ou des armes.
	function get_arme_degat($perso=null, $adversaire=null) { return 0; }
  /// Renvoie la distance à laquelle le personnage peut attaquer
	function get_distance_tir() { return 0; }
	// @}
}
?>
