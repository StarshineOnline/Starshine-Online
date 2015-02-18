<?php // -*- php -*-
/**
 * @file entnj_def.class.php
 * Définition de la classe batiment, représentant la définition d'un bâtiment.
 */

/**
 * Classe batiment
 * Cette classe représentant la définition d'un bâtiment.
 * Correspond à la table du même nom dans la base de données
 */
class batiment extends entitenj_def
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $cout;  ///< Coût d'achat en stars du bâtiment.
	protected $entretien;  ///< Coût journalier de l'entretien du bâtiment.
	protected $cond1;   ///< Temps avant amélioration
	protected $cond2;   ///< Inutilisé ?
	protected $upgrade;   ///< ID de la version améliorée.
	protected $augmentation_pa;   ///< Augmentation du coût en PA des déplacements sur le bâtiment.
	protected $temps_construction;///< Temps de construction du bâtiment.
	protected $temps_construction_min;///< Temps de construction minimal du bâtiment.
	protected $image;  ///< Image du bâtiment.
	protected $point_victoire;  ///< Points de victoire gagnés lors de la destruction du bâtiment.

	/// Renvoie le coût d'achat en stars du bâtiment
	function get_cout()
	{
		return $this->cout;
	}
	/// Modifie le coût d'achat en stars du bâtiment
	function set_cout($cout)
	{
		$this->cout = $cout;
		$this->champs_modif[] = 'cout';
	}

	/// Renvoie le coût journalier de l'entretien du bâtiment
	function get_entretien()
	{
		return $this->entretien;
	}
	/// Modifie le coût journalier de l'entretien du bâtiment
	function set_entretien($entretien)
	{
		$this->entretien = $entretien;
		$this->champs_modif[] = 'entretien';
	}

	/// Renvoie le temps avant amélioration
	function get_cond1()
	{
		return $this->cond1;
	}
	/// Modifie le temps avant amélioration
	function set_cond1($cond1)
	{
		$this->cond1 = $cond1;
		$this->champs_modif[] = 'cond1';
	}

	/// Renvoie une variable inutilisé ?
	function get_cond2()
	{
		return $this->cond2;
	}
	/// Modifie une variable inutilisé ?
	function set_cond2($cond2)
	{
		$this->cond2 = $cond2;
		$this->champs_modif[] = 'cond2';
	}

	/// Renvoie l'ID de la version améliorée
	function get_upgrade()
	{
		return $this->upgrade;
	}
	/**
	 * Renvoie l'ID de la version améliorée
	 * @deprecated Utiliser get_upgrade() à la place
	 */
	function get_suivant()
	{
		return $this->upgrade;
	}
	/// Modifie l'ID de la version améliorée
	function set_upgrade($upgrade)
	{
		$this->upgrade = $upgrade;
		$this->champs_modif[] = 'upgrade';
	}

	/// Renvoie l'augmentation du coût en PA des déplacements sur le bâtiment
	function get_augmentation_pa()
	{
		return $this->augmentation_pa;
	}
	/// Modifie l'augmentation du coût en PA des déplacements sur le bâtiment
	function set_augmentation_pa($augmentation_pa)
	{
		$this->augmentation_pa = $augmentation_pa;
		$this->champs_modif[] = 'augmentation_pa';
	}

	/// Renvoie le temps de construction du bâtiment
	function get_temps_construction()
	{
		return $this->temps_construction;
	}
	/// Modifie le temps de construction du bâtiment
	function set_temps_construction($temps_construction)
	{
		$this->temps_construction = $temps_construction;
		$this->champs_modif[] = 'temps_construction';
	}

	/// Renvoie le temps de construction minimal du bâtiment
	function get_temps_construction_min()
	{
		return $this->temps_construction_min;
	}
	/// Modifie le temps de construction minimal du bâtiment
	function set_temps_construction_min($temps_construction_min)
	{
		$this->temps_construction_min = $temps_construction_min;
		$this->champs_modif[] = 'temps_construction_min';
	}

	/// Renvoie l'image du bâtiment
	function get_image()
	{
		return $this->image;
	}
	/**
	 * Renvoie l'image du bâtiment en haute ou basse résolution
	 * @param  $root         Chemin du répertoire de bases du jeu
	 * @param  $resolution   'high' pour la haute résolution ou 'low' pour la basse.
	 */
	function get_image_full($root, $resolution = 'high')
	{
		if($resolution != 'high') $image = $root."image/batiment_low/";
		else $image = $root."image/batiment/";

		/*if(file_exists($image.$this->image."_04.png")) 		{ $image .= $this->image."_04.png"; }
		elseif(file_exists($image.$this->image."_04.gif")) 	{ $image .= $this->image."_04.gif"; }
		else 														{ $image = ""; } //-- Si aucun des fichiers n'existe autant rien mettre...
		return $image;*/
		return $image.$this->image.'_04.png';
	}
	/// Modifie l'image du bâtiment
	function set_image($image)
	{
		$this->image = $image;
		$this->champs_modif[] = 'image';
	}

	/// Renvoie les points de victoire gagnés lors de la destruction du bâtiment
	function get_point_victoire()
	{
		return $this->point_victoire;
	}
	/// Modifie les points de victoire gagnés lors de la destruction du bâtiment
	function set_point_victoire($point_victoire)
	{
		$this->point_victoire = $point_victoire;
		$this->champs_modif[] = 'point_victoire';
	}
	
	/**
	* Renvoie la PP de l'entité
	* @deprecated Utiliser get_pp() à la place
	*/
	function get_PP()
	{
		return $this->pp;
	}
	/**
	* Modifie la PP de l'entité
	* @deprecated Utiliser set_pp() à la place
	*/
	function set_PP($PP)
	{
		$this->PP = $PP;
		$this->champs_modif[] = 'PP';
	}

	/**
	* Renvoie la PM de l'entité
	* @deprecated Utiliser get_pm() à la place
	*/
	function get_PM()
	{
		return $this->pm;
	}
	/**
	* Modifie la PM de l'entité
	* @deprecated Utiliser set_pm() à la place
	*/
	function set_PM($PM)
	{
		$this->PM = $PM;
		$this->champs_modif[] = 'PM';
	}
	
  /// Renvoie l'espèce
	function get_espece()
	{
    return 'batiment';;
  }
	// @}


	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
   * Constructeur
   * @param  $id                    Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param  $nom                   Nom de l'entité
   * @param  $description           Description de l'entité
   * @param  $type                  	Type de l'entité
	 * @param  $cout                  	Coût d'achat en stars du bâtiment.
	 * @param  $entretien             	Coût journalier de l'entretien du bâtiment.
	 * @param  $cond1                 	Temps avant amélioration
	 * @param  $cond2                 	Inutilisé ?
   * @param  $hp                    	HP maximums de l'entité
   * @param  $pp                    	PP de l'entité
   * @param  $pm                    	PM de l'entité
	 * @param  $carac                 	Valeur de base des caractéristiques du bâtiment.
	 * @param  $upgrade               	ID de la version améliorée.
	 * @param  $augmentation_pa       	Augmentation du coût en PA des déplacements sur le bâtiment.
	 * @param  $temps_construction    	Temps de construction du bâtiment.
	 * @param  $temps_construction_min    Temps de construction minimum du bâtiment.
	 * @param  $image                 	Image du bâtiment.
	 * @param  $point_victoire        	Points de victoire gagnés lors de la destruction du bâtiment.
	 */
	function __construct($id = 0, $nom = '', $description = '', $type = '', $cout = '', $entretien = '', $cond1 = '', $cond2 = '', $hp = '', $PP = '', $PM = '', $carac = '', $upgrade = '', $augmentation_pa = '', $temps_construction = '',$temps_construction_min = '', $image = '', $point_victoire = '')
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      entitenj_def::__construct($id, $nom, $type, $hp, $pp, $pm, $description);
			$this->cout = $cout;
			$this->entretien = $entretien;
			$this->cond1 = $cond1;
			$this->cond2 = $cond2;
			$this->carac = $carac;
			$this->upgrade = $upgrade;
			$this->augmentation_pa = $augmentation_pa;
			$this->temps_construction = $temps_construction;
			$this->temps_construction_min = $temps_construction_min;
			$this->image = $image;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    entitenj_def::init_tab($vals);
		$this->cout = $vals['cout'];
		$this->entretien = $vals['entretien'];
		$this->cond1 = $vals['cond1'];
		$this->cond2 = $vals['cond2'];
		$this->carac = $vals['carac'];
		$this->upgrade = $vals['upgrade'];
		$this->augmentation_pa = $vals['augmentation_pa'];
		$this->temps_construction = $vals['temps_construction'];
		$this->temps_construction_min = $vals['temps_construction_min'];
		$this->image = $vals['image'];
		$this->point_victoire = $vals['point_victoire'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return entitenj_def::get_liste_champs().', cout, entretien, cond1, cond2, carac, upgrade, augmentation_pa, temps_construction,temps_construction_min, image, point_victoire';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return entitenj_def::get_valeurs_insert().', '.$this->cout.', '.$this->entretien.', '.$this->cond1.', '.$this->cond2.', '.$this->carac.', '.$this->upgrade.', '.$this->augmentation_pa.', '.$this->temps_construction.', '.$this->temps_construction_min.', '.$this->image.', '.$this->point_victoire;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return entitenj_def::get_liste_update().', cout = '.$this->cout.', entretien = '.$this->entretien.', cond1 = '.$this->cond1.', cond2 = '.$this->cond2.', carac = '.$this->carac.', upgrade = '.$this->upgrade.', augmentation_pa = '.$this->augmentation_pa.', temps_construction = '.$this->temps_construction.', temps_construction_min = '.$this->temps_construction_min.', image = '.$this->image.', point_victoire = '.$this->point_victoire;
	}
	// @}


	/**
	 * @name Bonus des bâtiments
	 * Méthodes gérant les bonus des bâtiments : résurection, bonus de PP/PM/…, bâtiments en ville accessibles,…
	 */
  // @{
	private $boni = null;  ///< Tableau des bonus accessibles
	/// Renvoie les bonus accessibles
	function get_boni()
	{
		if ($this->boni == null) {
			global $db;
			$this->boni = array();
			$requeteSQL = $db->query('SELECT * from batiment_bonus where id_batiment = '.$this->id);
			while ($row = $db->read_assoc($requeteSQL)) {
				$valeur = $row['valeur'];
				if ($valeur === '' || $valeur === NULL)
					$valeur = true;
				else
					$valeur = (int) $valeur;
				$this->boni[$row['bonus']] = $valeur;
			}
		}
		return $this->boni;
	}
  /// Indique si un bonus donné est accessible
	function has_bonus($bonus)
	{
		if ($this->boni == null) $this->get_boni();
		if (array_key_exists($bonus, $this->boni))
			return true;
		else
			return false;
	}
  /**
   * Renvoie les informations sur un bonus donné ou false s'il n'est aps accessible
   * Si $bonus vaut null, alors tous les bonus sotn renvoyés comme pour get_boni()
   */
	function get_bonus($bonus)
	{
		if ($this->boni == null) $this->get_boni();
		if (array_key_exists($bonus, $this->boni))
			return $this->boni[$bonus];
		else
			return false;
	}
	// @}

	/**
	 * @name Caractéristiques
	 * Données et méthodes liées aux caractéristiques du personnage : constitution,
	 * force, dextérité, puissance, volonté et énergie.
	 */
  // @{
	protected $carac;  ///< Valeur de base des caractéristiques du bâtiment.

	/// Renvoie la valeur de base des caractéristiques du bâtiment
	function get_carac()
	{
		return $this->carac;
	}
	/// Modifie la valeur de base des caractéristiques du bâtiment
	function set_carac($carac)
	{
		$this->carac = $carac;
		$this->champs_modif[] = 'carac';
	}
	
  function get_constitution()
  {
    return $this->carac;
  }
  function get_force()
  {
    return $this->carac;
  }
  function get_dexterite()
  {
    return $this->carac;
  }
  function get_puissance()
  {
    return $this->carac;
  }
  function get_volonte()
  {
    return $this->carac;
  }
  function get_energie()
  {
    return $this->carac;
  }
  // @}

	/**
	 * @name Compétences
	 * Données et méthodes liées aux compténtences du personnage : mêlée, esquive,
	 * incatation, …
	 */
	// @{
	/// Renvoie la compétence de mêlée
	function get_melee()
	{
    $melee = $this->get_bonus('precision');
    if($melee)
      return $melee;
    else
      return 0;
  }
  /// Renvoie la valeur de la compétence de tir
	function get_distance()
	{
    return 100 * $this->carac;
  }
	/// Renvoie la compétence d'esquive
	function get_esquive()
	{
    if( $this->type == 'arme_de_siege' )
      return 40 * $this->carac;
    else
      return 100 * $this->carac;
  }
	/// Renvoie la compétence d'incantation
	function get_incantation()
	{
    return 0;
  }
	/// Renvoie la compétence de magie de la vie
	function get_sort_vie()
	{
    return 0;
  }
	/// Renvoie la compétence de nécromancie
	function get_sort_mort()
	{
    return 0;
  }
	/// Renvoie la compétence de magie élémentaire
	function get_sort_element()
	{
    return 0;
  }
	// @}

	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	/// Renvoie l'arme utilisée
	function get_arme()
	{
    return '';
  }
	/// Renvoie le script du monstre
	function get_action()
	{
    return '';
  }
	/// Renvoie la RM
	function get_reserve()
	{
    return 0;
  }
  /// Renvoie la distance à laquelle le personnage peut attaquer
	function get_distance_tir()
  {
    $portee = $this->get_bonus('portee');
    if( $portee === false )
      return 0;
    else
      return $portee;
  }
	/// Renvoie le facteur de dégâts de ou des armes.
	function get_arme_degat($perso=null, $adversaire=null)
  {
    if( $adversaire != null && $adversaire->get_type_def() == 'arme_de_siege')
      $degats = $this->get_bonus('degats_siege');
    else
      $degats = $this->get_bonus('degats_bat');
    if( $degats === false )
      return 0;
    elseif( $perso != null && $perso->get_race() == 'barbare' )
      return ceil($degats * 1.1);
    else
      return $degats;
  }
	// @}

}
