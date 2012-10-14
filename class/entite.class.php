<?php // -*- mode: php; tab-width: 2 -*-
/**
 * @file entite.class.php
 * Gestion des participants à un combat
 */

/**
 * Classe représentant les participants à un combat
 *
 * @author Florian
 */
class entite extends placable
{
	/// Pour compatibilité (le temps de refaire la hiérarchie)


	protected function get_table() { return ''; }
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : classe, rang, niveau,
	 * stars, mort, points crimes, …
	 */
  // @{
	protected $race;         ///< Race de l'entité.
	protected $level;        ///< Niveau de l'entité.
	protected $rang_royaume; ///< Grade de l'entité au sein de son royaume.
	protected $type;           ///< Type de l'entité.
	protected $espece;         ///< Espèce de l'entité.
	public $etat;            ///< État de l'entité.
	protected $point_victoire; ///< Points de victoire gagnés si l'entité est détruite.
	/// Renvoie la race
	function get_race()
	{
		return $this->race;
	}
	/// Renvoie le grade de l'entité au sein de son royaume.
	function get_rang_royaume()
	{
		return $this->rang_royaume;
	}
	/// Renvoie le grade.
	function get_rang()
	{
		if(isset($this->rang))
			return $this->rang;
		else
			return $this->rang_royaume;
	}
	/// Renvoie le grade sous forme d'objet
	function get_grade()
	{
		$grade = grade::create(array('rang'), array($this->rang_royaume));
		return $grade[0];
	}
	/// Renvoie le niveau de l'entité
	function get_level()
	{
		return $this->level;
	}
  /// Renvoie le type de l'entité
	function get_type()
	{
		return $this->type;
	}
  /// Indique si l'entité est du type demandé
	function is_type($type)
	{
		return !strcmp($this->type, $type);
	}
  /// Renvoie l'espèce de l'entité
	function get_espece()
	{
		return $this->espece;
	}
  /// Renvoie l'honneur
	function get_honneur()
	{
		if($this->type == 'joueur') return $this->objet_ref->get_honneur();
		else return null;
	}
  /// Renvoie la réputation
	function get_reputation()
	{
		if($this->type == 'joueur') return $this->objet_ref->get_reputation();
		else return null;
	}
	/// Renvoie l'état
	function get_etat()
	{
		return $this->etat;
	}
  /// Renvoie l'expérience
	function get_exp()
	{
		if($this->type == 'joueur')
			return $this->objet_ref->get_exp();
		else
			return 0;
	}
  /// Renvoie les stars
	function get_star()
	{
		if(isset($this->star))
			return $this->star;
		else
			return 0;
	}
  /// Renvoie les points de victoire gagnés
	function get_point_victoire()
	{
		return $this->point_victoire;
	}
  // @}

	/**
	 * @name Caractéristiques et affinité
	 * Données et méthodes liées aux caractéristiques de l'entité : constitution,
	 * force, dextérité, puissance, volonté et énergie.
	 */
	// @{
	protected $vie;        ///< Caractéristique "constitution".
	protected $force;      ///< Caractéristique "force".
	protected $dexterite;  ///< Caractéristique "dextérité".
	protected $puissance;  ///< Caractéristique "puissance".
	protected $volonte;    ///< Caractéristique "volonté".
	protected $energie;    ///< Caractéristique "énergie".
	/// Renvoie la constitution
	function get_vie()
	{
		return $this->vie;
	}
	/// Renvoie la force
	function get_force()
	{
		return $this->force;
	}
	/// Renvoie la dextérité
	function get_dexterite()
	{
		return $this->dexterite;
	}
	/// Modifie la dextérité
	function set_dexterite($dexterite)
	{
		$this->dexterite = max(0, $dexterite);
	}
	/// Renvoie la puissance
	function get_puissance()
	{
		return $this->puissance;
	}
	/// Renvoie la volonté
	function get_volonte()
	{
		return $this->volonte;
	}
	/// Modifie la volonté
	function set_volonte($volonte)
	{
		$this->volonte = max(0, $volonte);
	}
	/// Renvoie l'énergie
	function get_energie()
	{
		return $this->energie;
	}
	/**
	 * Renvoie Le Coefficient modifiant le coût d'un sort à cause de l'affinité
	 * @param $comp  compétence de magie correspondante
	 */
  function get_affinite($comp)
  {
    return 1;
  }
  // @}

	/**
	 * @name Compétences
	 * Données et méthodes liées aux compténtences du personnage : mêlée, esquive,
	 * incatation, …
	 */
	// @{
	protected $melee;        ///< Compétence mêlée.
	protected $distance;     ///< Compétence tir à distance.
	protected $esquive;      ///< Compétence esquive.
	protected $blocage;      ///< Compétence blocage.
	protected $incantation;  ///< Compétence incantation.
	protected $sort_vie;     ///< Compétence magie de la vie.
	protected $sort_element; ///< Compétence magie élémentaire.
	protected $sort_mort;    ///< Compétence nécromancie.
	protected $competence = array();
	/// Renvoie la mêlée
	function get_melee()
	{
		return $this->melee;
	}
	/// Renvoie le tir à distance
	function get_distance()
	{
		return $this->distance;
	}
	/// Renvoie l'esquive
	function get_esquive()
	{
		return $this->esquive;
	}
	/// Renvoie le blocage
	function get_blocage()
  {
    return $this->blocage;
  }
	/// Renvoie l'incantation
	function get_incantation()
	{
		return $this->incantation;
	}
	/// Renvoie la magie de vie
	function get_sort_vie()
	{
		return $this->sort_vie;
	}
	/// Renvoie la magie élémentaire
	function get_sort_element()
	{
		return $this->sort_element;
	}
	/// Renvoie la nécromancie
	function get_sort_mort()
	{
		return $this->sort_mort;
	}
  /**
   * Indique si l'entité posède une certaine compétence ou s'il en a de manière générale
   * @param  $nom     nom ou type de la compétence.
   * @param  $type    true si $nom est le type de la compétence, $false si c'est son nom.
   */
	function is_competence($nom = '', $type = false)
	{
		$buffe = false;
		if(is_array($this->competence))
		{
			if(!empty($nom))
			{
				foreach($this->competence as $key => $comp)
				{
					if($type)
					{
						if($key == $nom) $buffe = true;
					}
					else if($comp->get_competence() == $nom)
					{
						$buffe = true;
					}
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;

		return $buffe;
	}
   /**
   * Renvoie la compétence demandée
   * @param  $nom     nom ou type de la compétence.
   * @param  $type    true si $nom est le type de la compétence, $false si c'est son nom.
   */
	function get_competence2($nom, $type = false)
	{
		$buffe = false;
		if(is_array($this->competence))
		{
			if(!empty($nom))
			{
				foreach($this->competence as $key => $comp)
				{
					if($type)
					{
						return $comp;
					}
					else if($comp->get_competence() == $nom)
					{
						return $comp;
					}
				}
			}
		}
		return null;
	}
	/// Mise-à-jour des compétences à partir de l'objet
	function maj_comp()
	{
		if ($this->type == 'joueur' || $this->type == 'monstre')
		{
			$this->esquive = $this->objet_ref->get_esquive();
			$this->melee = $this->objet_ref->get_melee();
			$this->distance = $this->objet_ref->get_melee();
			$this->incantation = $this->objet_ref->get_incantation();
			$this->sort_mort = $this->objet_ref->get_sort_mort();
			$this->sort_vie = $this->objet_ref->get_sort_vie();
			$this->sort_element = $this->objet_ref->get_sort_element();
		}
		if ($this->type == 'joueur')
		{
			$this->comp_att = $this->objet_ref->get_comp_att();
			$this->distance = $this->objet_ref->get_distance();
			$this->competence = $this->objet_ref->get_comp_perso();
		}
	}
  /// Renvoie les compétence de combat
	function get_comp_combat()
	{
		return $this->comp_combat;
	}
  // @}

  /**
   * @name  PA, HP & MP
   * Données et méthodes ayant trait aux PA, HP & MP : valeur actuelle, maximale,
   * prochaine régénération et augmentation.
   */
  // @{
	protected $pa;       /// < Nombre de PA.
	protected $hp;       ///< HP actuels de l'entité
	protected $hp_max;   ///< HP maximums.
	/// Renvoie le nomnre de PA
	function get_pa()
	{
		return $this->pa;
	}
	/// Modifie les PA
	function set_pa($new_pa)
	{
		if($this->type == 'joueur')
		{
			$this->objet_ref->set_pa($new_pa);
			$this->pa = $new_pa;
		}
	}
	/// Renvoie les HP actuels
	function get_hp()
	{
		return $this->hp;
	}
	/// Modifie les HP actuels
	function set_hp($valeur)
	{
		$this->hp = $valeur;
	}
  /// Ajoute de HP à la valeur actuelle
  function add_hp($add_hp)
	{
		$this->hp += $add_hp;
		if ($this->hp > $this->hp_max)
			$this->hp = $this->hp_max;
	}
	/// Renvoie les HP maximaux
	function get_hp_max()
	{
		return $this->hp_max;
	}
  // @}

  /**
   * @name  Sorts, compétences & buffs
   * Données et méthodes ayant trait aux sorts et compétences de combat et hors combat.
   * Ainsi que les buffs et débuffs du actifs sur l'entité.
   */
  // @{
	protected $comp_combat;  ///< Liste des compétences de combat.
	public $buff;   ///< Buffs & débuffs actifs sur le personnage.
	/**
	 * Renvoie une propriété d'un buff / débuff particulier actif sur le personnage ou l'ensemble de ceux-ci.
	 * @param  $nom      Nom (type) du (dé)buff recherché, renvoie tous les buffs actifs si vaut false.
	 * @param  $champ    Propriété recherchée (correspond à un champ dans la bdd).
	 * @param  $type	   Si false on prend le premier buff, si true celui dont le type correspond à $nom.
	 * @return     Tableau des buffs ou valeur demandée.
	 */
	function get_buff($nom = false, $champ = false, $type = false)
	{
		if(!$nom)
		{
			return $this->buff;
		}
		else
		{
			if(!isset($this->buff)) $this->get_buff();
			if(!$type)
			{
				$get = 'get_'.$champ;
				if(method_exists($this->buff[$nom], $get)) return $this->buff[$nom]->$get();
				else return false;
			}
			else
				foreach($this->buff as $buff)
				{
					// C'est gore mais ća devrait marcher le temps que ce soit debuggué
					// pour de bon ;p
					/*if (is_array($buff) && count($buff) == 1 && is_object($buff[0]))
						$buff = $buff[0];*/
					if($buff->get_type() == $nom)
					{
						$get = 'get_'.$champ;
						return $buff->$get();
					}
				}
		}
	}
  /// Renvoie un tableau contenant uniqument les buffs (c'est-à-dire sans les débuffs)
	function get_buff_only()
	{
		$buffs = array();
		foreach($this->get_buff() as $buff)
		{
			if($buff->get_debuff() == 0)
				$buffs[] = $buff;
		}

		return $buffs;
	}
  /**
   * Ajoute un buff
   * @param  $nom     Nom du buff
   * @param  $effet   Effet principal
   * @param  $effet   Effet secondaire
   */
	function add_buff($nom, $effet, $effet2 = 0)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buff = new buff();
		$buff->set_type($nom);
		$buff->set_effet($effet);
		$buff->set_effet2($effet2);
		$this->buff[$nom] = $buff;
	}
  /**
   * Supprime un buff
   * @param  $id    id du buff.
   */
	function supprime_buff($id)
	{
		if(!isset($this->buff)) $this->get_buff();
		unset($this->buff[$id]);
	}
	/**
	 * Permet de savoir si le joueur est sous le buff nom
	 * @param $nom le nom du buff
	 * @param $type si le nom est le type du buff
	 * @return true si le perso est sous le buff false sinon.
 	*/
	function is_buff($nom = '', $type = true)
	{
		$buffe = false;
		if(is_array($this->buff))
		{
			if(!empty($nom))
			{
				foreach($this->buff as $key => $buff)
				{
					if($type)
					{
						if($key == $nom) $buffe = true;
					}
					else if($buff->get_nom() ==  $nom)
					{
						$buffe = true;
					}
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;

		return $buffe;
	}

	/// Lance un débuff sur l'entité lors d'un combat (uniquement sur un personnage)
  function lance_debuff($debuff)
  {
    return false;
  }
  // @}

  /**
   * @name Inventaires et objets
	 * Données et méthodes liées à l'inventaire et aux objets portés ou utiliser par
	 * les perosnnages.
	 */
  // @{
	protected $arme_type;          ///< Type de l'arme utilisée.
	public $pp;                  ///< Protection physique.
	public $pm;                  ///< Protection magique.
	public $pm_para;             ///< Protection magique.
	public $enchantement;        ///< Liste des enchantements de gemmes.
	protected $arme_degat;         ///< Dégâts de l'arme.
	protected $bouclier_degat = 0; ///< Dégâts bloqués par le bouclier.
	protected $malus_hache = 1;    ///< Malus d'esquive dû au port d'une hache.
	protected $malus_arc = 1;    ///< Malus d'esquive dû au port d'un arc.
	protected $saved_inventaire = null; ///< Inventaire
	/// Renvoie le type de l'arme utilisée
	function get_arme_type()
	{
    if( array_key_exists('desarme', $this->etat) )
      return '';
    else
		  return $this->arme_type;
	}
	/// Renvoie l'arme utilisée
	function get_arme()
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->get_arme();
	}
	/// Renvoie le type de bouclier ('bouclier' ou '')
	function get_bouclier_type()
	{
		return $this->bouclier() ? 'bouclier' : '';
	}
	/// Renvoie la PP
	function get_pp()
	{
		return $this->pp;
	}
	/// Modifie la PP
	function set_pp($valeur)
	{
		$this->pp = $valeur;
	}
	/// Renvoie la PM
	function get_pm()
	{
		return $this->pm;
	}
	//renvoi la PM pour resister a paralysie
	function get_pm_para()
	{
		return $this->pm_para;
	}
	/// Modifie la PM
	function set_pm($valeur)
	{
		$this->pm = $valeur;
	}
	/// Renvoie la liste des enchantements de gemmes
	function get_enchantement()
	{
		return $this->enchantement;
	}
	/// Renvoie les dégâts de l'arme
	function get_arme_degat()
	{
		if( array_key_exists('desarme', $this->etat) )
      return 0;
    else
		  return $this->arme_degat;
	}
	/// Renvoie les dégâts bloqués par le bouclier
	function get_bouclier_degat()
	{
		return $this->bouclier_degat;
	}
  /// Renvoie le bouclier
	function bouclier()
	{
		switch($this->type)
		{
			case 'joueur' :
				//$this->dump();
				return $this->objet_ref->get_bouclier();
				break;
			case 'monstre' :
			case 'pet' :
        return $this->get_blocage();
				break;
		}
		return false;
	}
  /// Renvoie l'inventaire
	function get_inventaire()
	{
		if ($this->type == 'joueur' && $this->saved_inventaire == null)
			$this->saved_inventaire = $this->objet_ref->get_inventaire();
		return $this->saved_inventaire;
	}
  // @}


  /**
   * @name  Effes parmanents
   * Effets modifiant les caractéristiques et les compétences. Ces effets peuvent
   * être dus aux bonus raciaux, aux objets portés…
   */
  /**
   * Renvoie les effets présents d'un certains type
   * @param  &$effets   tableau auquel sera ajouté les effets trouvés
   * @param  $mode      type des effets
   */
	function get_effets_permanents(&$effets, $mode)
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->get_effets_permanents($effets, $mode);
	}
  /// renvoie un bonus permanent particulier
	function get_bonus_permanents($bonus)
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->get_bonus_permanents($bonus);
		else
			return 0;
	}
  // @}

	/**
	 * @name Groupe & quêtes
	 * Données et méthodes liées au groupe et aux quêtes.
	 */
  // @{
  /// Renvoie l'id du groupe du personnage.
	function get_groupe()
	{
		if ($this->type == 'joueur') return $this->objet_ref->get_groupe();
		else return null;
	}
  /// Renvoie la liste des quêtes que possède le personnage sous forme de tableau.
	function get_liste_quete()
	{
		if ($this->type == 'joueur') return $this->objet_ref->get_liste_quete();
		else return null;
	}
	// @}

	/**
	 * @name Position & déplacement
	 * Données et méthodes liées à la position et au déplacement.
	 */
  // @{
  /// Renvoie la position sous forme d'un seul entier
	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}
  /**
   * Indique si le personnage est dans une arène.
   * @return  false s'il n'est pas dans une arène, sinon objet contenant la
   *          description de l'arène dans laquelle il se trouve.
   */
	function in_arene($filter = '')
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->in_arene($filter);
		return false;
	}
	// @}

	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	public $action;            ///< Contenu du script de combat utilisé.
	public $reserve;           ///< Réserve de mana.
	public $rm_restant;           ///< Réserve de mana restante.
	protected $distance_tir;     ///< Distance de tir
	public $potentiel_toucher;  ///< Potentiel toucher physique
	public $potentiel_parer;  ///< Potentiel parer physique
	protected $potentiel_bloquer;  ///< Potentiel bloquer
	protected $potentiel_critique;  ///< Potentiel critique physique
	protected $potentiel_magique;  ///< Potentiel lancer magique
	protected $potentiel_parer_magique;  ///< Potentiel parer magique
	protected $comp_att;       ///< Coméptence utilisé pour attaquer
	/// Renvoie le contenu du script de combat utilisé
	function get_action()
	{
		return $this->action;
	}
	/// Renvoie la réserve de mana
	function get_reserve($base = false)
	{
		return $this->reserve;
	}
	/// Modifie la réserve de mana
	function set_reserve($valeur)
	{
		if ($valeur <= 0) $valeur = 0;
		  $this->reserve = $valeur;
	}
	/// Renvoie la réserve de mana
	function get_rm_restant()
	{
		return $this->rm_restant;
	}
	/// Modifie la réserve de mana
	function set_rm_restant($valeur)
	{
		if ($valeur <= 0) $valeur = 0;
		  $this->rm_restant = $valeur;
	}
	/// Renvoie la distance de tir
	function get_distance_tir()
	{
		return $this->distance_tir;
	}
	/// Calcul et renvoie le potentiel toucher physique
	function get_potentiel_toucher()
	{
		global $G_buff, $G_debuff;
    if( isset($this->potentiel_toucher) && $this->potentiel_toucher )
      return $this->potentiel_toucher;

		if($this->get_arme_type() == 'arc')
		{
			$this->potentiel_toucher = round($this->get_distance() + ($this->get_distance() * ((pow($this->get_dexterite(), 2)) / 1000)));
		}
		else
		{
			$this->potentiel_toucher = round($this->get_melee() + ($this->get_melee() * ((pow($this->get_dexterite(), 2)) / 1000)));
		}
	  //Debuff precision
  	if($this->is_buff('debuff_aveuglement')) $this->potentiel_toucher /= 1 + (($this->get_buff('debuff_aveuglement', 'effet')) / 100);
  	if(array_key_exists('aveugle', $this->etat)) $this->potentiel_toucher /= 1 + (($this->etat['aveugle']['effet']) / 100);
  	if(array_key_exists('lien_sylvestre', $this->etat)) $this->potentiel_toucher /= 1 + (($this->etat['lien_sylvestre']['effet2']) / 100);
  	if(array_key_exists('b_toucher', $this->etat)) $this->potentiel_toucher /= 1 + ($this->etat['b_toucher']['effet'] / 100);
  	if(array_key_exists('coup_mortel', $this->etat)) $this->potentiel_toucher *= 1 - ($this->etat['coup_mortel']['effet'] / 100);
  	if(array_key_exists('glace', $this->etat)) $this->potentiel_toucher /= 1 + ($this->etat['glace']['effet'] / 100);
  	//Buff précision
  	if(array_key_exists('benediction', $this->etat))	$this->potentiel_toucher *= 1 + (($this->etat['benediction']['effet'] * $G_buff['bene_accuracy']) / 100);
  	if(array_key_exists('berzeker', $this->etat)) $this->potentiel_toucher *= 1 + (($this->etat['berzeker']['effet'] * $G_buff['berz_accuracy']) / 100);
  	if(array_key_exists('tir_vise', $this->etat)) $this->potentiel_toucher *= 1 + (($this->etat['tir_vise']['effet'] * $G_buff['vise_accuracy']) / 100);
  	if($this->is_buff('batiment_distance')) $this->potentiel_toucher *= 1 + (($this->get_buff('batiment_distance', 'effet')) / 100);
  	if($this->is_buff('buff_cri_bataille')) $this->potentiel_toucher *= 1 + (($this->get_buff('buff_cri_bataille', 'effet')) / 100);
  	if(array_key_exists('dissimulation', $this->etat)) $this->potentiel_toucher *= 1 + (($this->etat['dissimulation']['effet']) / 100);
  	if($this->is_buff('buff_position') && $this->get_arme_type() == 'arc') $this->potentiel_toucher *= 1 + (($this->get_buff('buff_position', 'effet')) / 100);
  	if(array_key_exists('a_toucher', $this->etat)) $this->potentiel_toucher *= 1 + ($this->etat['a_toucher']['effet'] / 100);
  	if($this->etat['posture']['type'] == 'posture_touche') $this->potentiel_toucher *= 1 + (($this->etat['posture']['effet']) / 100);

		return $this->potentiel_toucher;
	}
	/// Modifie le potentiel toucher physique
	function set_potentiel_toucher($valeur)
	{
		$this->potentiel_toucher = $valeur;
	}
  /**
   * Calcul et renvoie le potentiel parer physique
   * @param  $esquive   Valeur de la compétence esquive à prendre en compte si elle est différente de celle de l'entité (sinon false).
   */
	function get_potentiel_parer($esquive = false)
	{
		global $G_buff, $G_debuff;
    if( isset($this->potentiel_parer) && $this->potentiel_parer )
      return $this->potentiel_parer;

		if(!$esquive) $this->potentiel_parer = round($this->get_esquive() + ($this->get_esquive() * ((pow($this->get_dexterite(), 2)) / 1000)));
		else $this->potentiel_parer = round($esquive + ($esquive * ((pow($this->get_dexterite(), 2)) / 1000)));
		if ($this->get_arme_type() == 'hache')
			$this->potentiel_parer *= $this->malus_hache;
		if ($this->get_arme_type() == 'arc')
			$this->potentiel_parer *= $this->malus_arc;
  	if(array_key_exists('benediction', $this->etat)) $this->potentiel_parer *= 1 + (($this->etat['benediction']['effet'] * $G_buff['bene_evasion']) / 100);
   	if(array_key_exists('berzeker', $this->etat)) $this->potentiel_parer /= 1 + (($this->etat['berzeker']['effet'] * $G_buff['berz_evasion']) / 100);
  	if(array_key_exists('derniere_chance', $this->etat)) $this->potentiel_parer /= 1 + (($this->etat['derniere_chance']['effet2']) / 100);
  	if($this->etat['posture']['type'] == 'posture_esquive') $this->potentiel_parer *= 1 + (($this->etat['posture']['effet']) / 100);
  	if($this->etat['posture']['type'] == 'posture_vent') $this->potentiel_parer *= 1 + (($this->etat['posture']['effet']) / 100);
  	if($this->is_buff('buff_evasion')) $this->potentiel_parer *= 1 + ($this->get_buff('buff_evasion', 'effet') / 100);
  	if($this->is_buff('buff_cri_detresse')) $this->potentiel_parer *= 1 + (($this->get_buff('buff_cri_detresse', 'effet')) / 100);
  	if(array_key_exists('glace', $this->etat)) $this->potentiel_parer /= 1 + ($this->etat['glace']['effet'] / 100);
  	if(array_key_exists('botte_chat', $this->etat)) $this->potentiel_parer *= 1 + $this->etat['botte_chat']['effet'] / 100;

  	if($this->get_race() == 'elfebois') $this->potentiel_parer *= 1.15;

		return $this->potentiel_parer;
	}
	/// Modifie le potentiel parer physique
	function set_potentiel_parer($valeur)
	{
		$this->potentiel_parer = $valeur;
	}
	/// Modifie le potentiel bloquer
	function get_potentiel_bloquer()
	{
		global $G_buff;
    if( isset($this->potentiel_bloquer) && $this->potentiel_bloquer )
      return $this->potentiel_bloquer;
    $p_e = $this->get_enchantement();
		if(array_key_exists('blocage', $p_e)) $enchantement_blocage = ($p_e['blocage']['effet']); else $enchantement_blocage = 0;
		if($this->is_buff('buff_bouclier_sacre')) $buff_blocage = 1 + ($this->get_buff('buff_bouclier_sacre', 'effet') / 100); else $buff_blocage = 1;
		if(array_key_exists('benediction', $this->etat)) $buff_bene_blocage = 1 + (($this->etat['benediction']['effet'] * $G_buff['bene_bouclier']) / 100); else $buff_bene_blocage = 1;
  	if(array_key_exists('botte_chien', $this->etat)) $buff_blocage *= 1 + $this->etat['botte_chien']['effet'] / 100;
    $this->potentiel_bloquer = floor(($this->get_blocage() + $enchantement_blocage ) * (pow($this->get_dexterite(), 1.7) / 20) * $buff_bene_blocage * $buff_blocage);
		return $this->potentiel_bloquer;
	}
	/// Modifie le potentiel bloquer
	function set_potentiel_bloquer($valeur)
	{
		$this->potentiel_bloquer = $valeur;
	}
	/// Modifie le potentiel critique physique
	function get_potentiel_critique()
	{
		global $G_buff;
    if( isset($this->potentiel_critique) && $this->potentiel_critique )
      return $this->potentiel_critique;

    $this->potentiel_critique = ceil(pow($this->get_dexterite(), 1.5) * 10);
  	//Buff du critique
  	if($this->is_buff('buff_critique', true)) $this->potentiel_critique *= 1 + (($this->get_buff('buff_critique', 'effet', true)) / 100);
  	if($this->is_buff('buff_cri_rage', true)) $this->potentiel_critique *= 1 + (($this->get_buff('buff_cri_rage', 'effet')) / 100);
  	if(array_key_exists('benediction', $this->etat)) $this->potentiel_critique *= 1 + (($this->etat['benediction']['effet'] * $G_buff['bene_critique']) / 100);;
  	if(array_key_exists('tir_vise', $this->etat)) $this->potentiel_critique *= 1 + (($this->etat['tir_vise']['effet'] * 5) / 100);
  	if(array_key_exists('berzeker', $this->etat)) $this->potentiel_critique *= 1 + (($this->etat['berzeker']['effet'] * $G_buff['berz_critique']) / 100);
  	if(array_key_exists('coup_sournois', $this->etat)) $this->potentiel_critique *= 1 + (($this->etat['coup_sournois']['effet']) / 100);
  	if(array_key_exists('fleche_sanglante', $this->etat)) $this->potentiel_critique *= 1 + (($this->etat['fleche_sanglante']['effet']) / 100);
    //Elfe des bois
	  if($this->get_race() == 'elfebois') $this->potentiel_critique *= 1.15;
  	if(array_key_exists('coup_mortel', $this->etat) && array_key_exists('dissimulation', $this->etat))
  		$this->potentiel_critique *= 1 + ($this->etat['coup_mortel']['effet2']/100);
    //Enchantement critique
  	if(array_key_exists('critique', $this->get_enchantement())) $this->potentiel_critique *= 1 + (($this->enchantement['critique']['effet']) / 100);
    if($this->etat['posture']['type'] == 'posture_critique') $this->potentiel_critique *= 1 + (($this->etat['posture']['effet']) / 100);
		return $this->potentiel_critique;
	}
	/// Modifie le potentiel critique physique
	function set_potentiel_critique($valeur)
	{
		$this->potentiel_critique = $valeur;
	}
	/**
	 * Calcul et renvoie le potentiel toucher physique
	 * @param $comp_assoc  Coméptence associé au sort
	 */
	function get_potentiel_lancer_magique($comp_assoc)
	{
    if( isset($this->potentiel_magique) && $this->potentiel_magique )
      return $this->potentiel_magique;

    $get = 'get_'.$comp_assoc;
  	$this->potentiel_magique = floor($this->get_incantation() + 1.9 * $this->$get());
  	if($this->is_buff('batiment_incantation'))
      $this->potentiel_magique *= 1 + (($this->get_buff('batiment_incantation', 'effet') / 100));
  	if($this->is_buff('buff_meditation'))
      $this->potentiel_magique *= 1 + (($this->get_buff('buff_meditation', 'effet') / 100));
  	if(array_key_exists('lien_sylvestre', $this->etat))
      $this->potentiel_magique /= 1 + (($this->etat['lien_sylvestre']['effet2']) / 100);
  	if(array_key_exists('fleche_debilitante', $this->etat))
      $this->potentiel_magique /= 1 + ($this->etat['fleche_debilitante']['effet'] / 100);
  	if($this->etat['posture']['type'] == 'posture_feu')
      $this->potentiel_magique *= 1 + (($this->etat['posture']['effet']) / 100);
    if(array_key_exists('glace', $this->etat)) $this->potentiel_magique /= 1 + ($this->etat['glace']['effet'] / 100);
  	if($this->get_arme_type() == 'baton')
    {
      $arme = $this->get_arme();
      $this->potentiel_magique *= (1 + ($arme->var1 / 100));
    }
  	//Objets magiques
  	foreach($this->objet_effet as $effet)
  	{
  		switch($effet['id'])
  		{
  			case '11' :
  				$this->potentiel_magique += $potentiel_magique_arme * (1 + ($effet['effet'] / 100));
  			break;
  		}
  	}
  	return $this->potentiel_magique;
	}
	/**
	 * Calcul et renvoie le potentiel toucher physique
	 * @param $comp_assoc  Coméptence associé au sort
	 */
	function set_potentiel_lancer_magique($valeur)
	{
    $this->potentiel_magique = $valeur;
	}
  /**
   * Calcul et renvoie le potentiel parer physique
   * @param  $esquive   Valeur de la compétence esquive à prendre en compte si elle est différente de celle de l'entité (sinon false).
   */
	function get_potentiel_parer_magique($pm=null)
	{
		global $G_buff, $G_debuff;
    if( isset($this->potentiel_parer_magique) && $this->potentiel_parer_magique )
      return $this->potentiel_parer_magique;

		if( !$pm )
      $pm = $this->get_pm();
		if($this->is_buff('batiment_pm')) $buff_batiment_barriere = 1 + (($this->get_buff('batiment_pm', 'effet') / 100)); else $buff_batiment_barriere = 1;
		if($this->is_buff('debuff_desespoir')) $debuff_desespoir = 1 + (($this->get_buff('debuff_desespoir', 'effet')) / 100); else 	$debuff_desespoir = 1;
		if($this->etat['posture']['type'] == 'posture_glace') $aura_glace = 1 + (($this->etat['posture']['effet']) / 100); else $aura_glace = 1;
		$this->potentiel_parer_magique = round($this->get_volonte() * $pm * $aura_glace * $buff_batiment_barriere / $debuff_desespoir);
    if(array_key_exists('glace', $this->etat)) $this->potentiel_parer_magique /= 1 + ($this->etat['glace']['effet'] / 100);

		return $this->potentiel_parer_magique;
	}
	/// Modifie le potentiel parer physique
	function set_potentiel_parer_magique($valeur)
	{
		$this->potentiel_parer_magique = $valeur;
	}
	/// Renvoie la compétence utilisée pour attaquer
	function get_comp_att()
	{
		return $this->comp_att;
	}
	/// Modifie la compétence utilisée pour attaquer?
	function set_comp_att($valeur)
	{
		$this->comp_att = $valeur;
	}
	/// Initialise l'objet pour un nouveau round de combat
	function init_round()
  {
    if($this->get_arme_type() == 'arc') $this->set_comp_att('distance'); else $this->set_comp_att('melee');
    unset($this->potentiel_toucher);
    unset($this->potentiel_parer);
    unset($this->potentiel_bloquer);
    unset($this->potentiel_critique);
    unset($this->potentiel_magique);
    unset($this->potentiel_parer_magique);
		$this->degat_sup = 0;
		$this->degat_moins = 0;
  }
  /// Action effectuées à la fin d'un combat
  function fin_combat(&$perso, $degats=null) { echo "on fait rien ! ($this)<br/>"; }
  /// Actions effectuées à la fin d'un combat pour l'attaquant
  function fin_attaque(&$perso, $cout_pa) {}
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, $batiment) {}
  /// Renvoie le coût en PA de l'attaque
  function get_cout_attaque(&$perso, $cible)
  {
    $cout = $cible->get_cout_attaque_base($perso);
    if( $perso->is_buff('cout_attaque') ) $cout = ceil($cout / $perso->get_buff('cout_attaque', 'effet'));
    if( $perso->is_buff('plus_cout_attaque') ) $cout *= $perso->get_buff('plus_cout_attaque', 'effet');
    if( $perso->is_buff('buff_rapidite') ) $cout -= $perso->get_buff('buff_rapidite', 'effet');
    if( $perso->is_buff('debuff_ralentissement') ) $cout += $perso->get_buff('debuff_ralentissement', 'effet');
    if( $cout < 1 ) $cout = 1;
    return $cout;
  }
  /// Renvoie le coût en PA pour attaquer l'entité
  function get_cout_attaque_base(&$perso) { return 0; }
  /// Indique si l'entité peut attaquer
  function peut_attaquer()
  {
    return true;
  }
	// @}

	/**
	 * @name Accès à la base de données & création
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	protected $objet_ref;    ///< Référence vers l'objet source
  /**
   * Crée l'objet à partir d'un objet source
   * @param  $type    Type de l'entité
   * @objet  $objet   Référence vers l'objet source.
   */
	function __construct($type=null, &$objet=null)
	{
		$this->objet_effet = array();
		$this->type = $type;
		$this->objet_ref = &$objet;
		switch($type)
		{
			case 'joueur' :
				$objet->check_materiel();

				//my_dump($objet->get_enchantement());

				$this->id = $objet->get_id();
				$this->action = $objet->action_do;
				$this->arme_type = $objet->get_arme_type();
				switch ($this->arme_type)
					{
					case 'hache':
						// On calcule le malus d'esquive de la hache
						$hache = $objet->get_arme();
						if ($hache->mains == 'main_droite') {
							// Hache à 1 main : 5%
							$this->malus_hache = 0.95;
						}
						else {
							// Hache à 2 mains : 15%
							$this->malus_hache = 0.85;
						}
					case 'epee':
					case 'dague':
					case 'baton':
					case '': // main nues
						$this->comp_combat = 'melee';
					break;
					case 'arc':
						$this->comp_combat = 'distance';
						$this->malus_arc = 0.8;
					break;
					case 'bouclier':
						$this->comp_combat = 'blocage';
					break;
					default:
						die("Invalid arme_type ($this->arme_type) !!");
					}
				$this->comp_att = $objet->get_comp_att();
				$this->competence = $objet->get_comp_perso();
				$this->x = $objet->get_x();
				$this->y = $objet->get_y();
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->get_hp_maximum();
				$this->reserve = $objet->get_reserve_bonus();
				$this->pa = $objet->get_pa();
				$this->nom = $objet->get_nom();
				$this->race = $objet->get_race();
				$this->pp = $objet->get_pp();
				$this->pm = $objet->get_pm();
				$this->pm_para = $objet->get_pm_para();
				$this->distance_tir = $objet->get_distance_tir();
				$this->esquive = $objet->get_esquive();
				$this->distance = $objet->get_distance();
				$this->melee = $objet->get_melee();
				$this->incantation = $objet->get_incantation();
				$this->sort_mort = $objet->get_sort_mort();
				$this->sort_vie = $objet->get_sort_vie();
				$this->sort_element = $objet->get_sort_element();
				$this->buff = $objet->get_buff();
				$this->etat = array();
				$this->force = $objet->get_force();
				$this->puissance = $objet->get_puissance();
				$this->energie = $objet->get_energie();
				$this->vie = $objet->get_vie();
				$this->volonte = $objet->get_volonte();
				$this->dexterite = $objet->get_dexterite();
				$this->enchantement = $objet->get_enchantement();
				$this->arme_degat = $objet->get_arme_degat();
				if ($objet->get_bouclier())
					$this->bouclier_degat = $objet->get_bouclier()->degat;
				$this->level = $objet->get_level();
				$this->rang_royaume = $objet->get_rang_royaume();
				$this->rang = $objet->get_grade()->get_rang();
				$this->espece = 'humainoïde';
				$this->blocage = $objet->get_blocage();
				$this->star = $objet->get_star();
				$this->point_victoire = 0;
				$this->honneur = $objet->get_honneur();
			break;
			case 'monstre' :
				$this->action = $objet->get_action();
				$this->arme_type = $objet->get_arme();
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->x;
				$this->y = $objet->y;
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->hp_max;
				$this->reserve = $objet->get_reserve();
				$this->pa = 0;
				$this->nom = $objet->get_nom();
				$this->race = 'neutre';
				$this->pp = $objet->get_pp();
				$this->pm = $objet->get_pm();
				$this->pm_para = $objet->get_pm();
				$this->distance_tir = 0;
				$this->esquive = $objet->get_esquive();
				$this->distance = $objet->get_melee();
				$this->melee = $objet->get_melee();
				$this->incantation = $objet->get_incantation();
				$this->sort_mort = $objet->get_sort_mort();
				$this->sort_vie = $objet->get_sort_vie();
				$this->sort_element = $objet->get_sort_element();
				$this->buff = $objet->get_buff();
				$this->etat = array();
				$this->force = $objet->get_forcex();
				$this->puissance = $objet->get_puissance();
				$this->energie = $objet->get_energie();
				$this->vie = 15;
				$this->volonte = $objet->get_volonte();
				$this->dexterite = $objet->get_dexterite();
				$this->enchantement = array();
				$this->arme_degat = 0;
				$this->level = $objet->get_level();
				$this->rang_royaume = 0;
				$this->star = $objet->get_star();
				$this->espece = $objet->get_type();
				$this->point_victoire = 0;
			break;
			case 'pet' :
				$pet = $objet->get_pet(); // bug si pas de créature principale
				$pet->get_monstre();
				$this->action = $pet->recupaction('attaque');
				$this->arme_type = $pet->monstre->get_arme();
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->get_x();
				$this->y = $objet->get_y();
				$this->hp = $pet->get_hp();
				$this->hp_max = $pet->monstre->get_hp();
				$this->reserve = $pet->get_reserve_bonus();
				$this->pa = 0;
				$this->nom = $pet->get_nom();
				$this->race = 'neutre';
				$this->pp = $pet->get_pp();
				$this->pm = $pet->get_pm();
				$this->pm_para = $pet->get_pm();
				$this->distance_tir = $pet->get_distance_tir();
				$this->esquive = $pet->monstre->get_esquive();
				$this->distance = $pet->monstre->get_melee();
				$this->melee = $pet->monstre->get_melee();
				$this->incantation = $pet->monstre->get_incantation();
				$this->sort_mort = $pet->monstre->get_sort_mort();
				$this->sort_vie = $pet->monstre->get_sort_vie();
				$this->sort_element = $pet->monstre->get_sort_element();
				$this->buff = $objet->get_buff();
				$this->etat = array();
				$this->force = $pet->monstre->get_forcex();
				$this->puissance = $pet->monstre->get_puissance();
				$this->energie = $pet->monstre->get_energie();
				$this->vie = 15;
				$this->volonte = $pet->monstre->get_volonte();
				$this->dexterite = $pet->monstre->get_dexterite();
				$this->enchantement = array();
				$this->arme_degat = $pet->get_arme_degat();
				$this->level = $pet->monstre->get_level();
				$this->rang_royaume = 0;
				$this->star = $pet->monstre->get_star();
				$this->espece = $pet->monstre->get_type();
				$this->point_victoire = 0;
				$this->race = $objet->get_race();
			break;
			case 'batiment' :
				$this->coef_carac = $objet->coef;
				$this->facteur = 100;
				$this->action = '';
				$this->arme_type = '';
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->x;
				$this->y = $objet->y;
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->hp_max;
				$this->reserve = 0;
				$this->pa = 0;
				$this->nom = $objet->get_nom();
				$this->race = 'neutre';
				$this->pp = $objet->get_pp();
				$this->pm = $objet->get_pm();
				$this->pm_para = $objet->get_pm();
				$this->distance_tir = 1;
				$this->esquive = $this->facteur * ceil($this->coef_carac * $objet->get_carac());
				$this->distance = 0;
				$this->melee = 0;
				$this->incantation = 0;
				$this->sort_mort = 0;
				$this->sort_vie = 0;
				$this->sort_element = 0;
				$this->buff = array();
				$this->etat = array();
				$this->force = 	ceil($this->coef_carac * $objet->get_carac());
				$this->puissance = ceil($this->coef_carac * $objet->get_carac());
				$this->energie = ceil($this->coef_carac * $objet->get_carac());
				$this->vie = ceil($this->coef_carac * $objet->get_carac());
				$this->volonte = ceil($this->coef_carac * $objet->get_carac());
				$this->dexterite = ceil($this->coef_carac * $objet->get_carac());
				$this->enchantement = array();
				$this->arme_degat = 0;
				$this->level = 1;
				$this->espece = 'batiment';
				$this->point_victoire = $objet->get_point_victoire();
			break;
			case 'siege' :
				$this->coef_carac = $objet->coef;
				$this->facteur = 40;
				$this->action = '!';
				$this->arme_type = 'epee';
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->x;
				$this->y = $objet->y;
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->hp_max;
				$this->reserve = 0;
				$this->pa = 100;
				$this->nom = $objet->get_nom();
				$this->race = 'neutre';
				$this->pp = $objet->get_pp();
				$this->pm = $objet->get_pm();
				$this->pm_para = $objet->get_pm();
				$this->distance_tir = $objet->get_bonus('portee');
				$this->esquive = $this->facteur * ceil($this->coef_carac * $objet->get_carac());
				$this->distance = $objet->get_bonus('portee');
				$this->melee = $objet->get_bonus('precision') * $objet->bonus_architecture;
				$this->incantation = 0;
				$this->sort_mort = 0;
				$this->sort_vie = 0;
				$this->sort_element = 0;
				$this->buff = array();
				$this->etat = array();
				$this->force = 	ceil($this->coef_carac * $objet->get_carac());
				$this->puissance = ceil($this->coef_carac * $objet->get_carac());
				$this->energie = ceil($this->coef_carac * $objet->get_carac());
				$this->vie = ceil($this->coef_carac * $objet->get_carac());
				$this->volonte = ceil($this->coef_carac * $objet->get_carac());
				$this->dexterite = ceil($this->coef_carac * $objet->get_carac());
				$this->enchantement = array();
				$this->arme_degat = $objet->arme_degat;
				$this->level = 1;
				$this->espece = 'siege';
				$this->point_victoire = 0;
			break;
			case 'ville' :
				$this->coef_carac = $objet->coef;
				$this->facteur = 40;
				$this->action = '!';
				$this->arme_type = 'epee';
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->x;
				$this->y = $objet->y;
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->hp_max;
				$this->reserve = 0;
				$this->pa = 100;
				$this->nom = $objet->get_nom();
				$this->race = 'neutre';
				$this->pp = $objet->get_pp();
				$this->pm = $objet->get_pm();
				$this->pm_para = $objet->get_pm();
				$this->distance_tir = 1;
				$this->esquive = $this->facteur * ceil($this->coef_carac * $objet->get_carac());
				$this->distance = $objet->get_bonus('portee');
				$this->melee = $objet->get_bonus('precision') * $objet->bonus_architecture;
				$this->incantation = 0;
				$this->sort_mort = 0;
				$this->sort_vie = 0;
				$this->sort_element = 0;
				$this->buff = array();
				$this->etat = array();
				$this->force = 	ceil($this->coef_carac * $objet->get_carac());
				$this->puissance = ceil($this->coef_carac * $objet->get_carac());
				$this->energie = ceil($this->coef_carac * $objet->get_carac());
				$this->vie = ceil($this->coef_carac * $objet->get_carac());
				$this->volonte = ceil($this->coef_carac * $objet->get_carac());
				$this->dexterite = ceil($this->coef_carac * $objet->get_carac());
				$this->enchantement = array();
				$this->arme_degat = $objet->arme_degat;
				$this->level = 1;
				$this->espece = 'ville';
				$this->point_victoire = $objet->get_point_victoire();
			break;
		}
	}
  /**
   * Crée l'objet la bonne classe à partir d'un objet source
   * @param  $type    Type de l'entité
   * @objet  $objet   Référence vers l'objet source.
   */
	static function factory($type, &$src, $perso=null, $attaquant=true, $adversaire=null)
	{
    switch($type)
    {
		case 'monstre' :
		case 'pet' :
		case 'batiment' :
		case 'siege' :
      $objet = new entitenj($src, $perso, $attaquant, $adversaire);
		  $objet->objet_ref = &$src->get_def();
		  break;
    case 'joueur' :
      $objet = new perso($src->get_id());
      $objet->check_specials();
			$objet->action = $src->action_do;
			switch ($objet->arme_type)
				{
				case 'hache':
					/* On calcule le malus d'esquive de la hache */
					$hache = $src->get_arme();
					if ($hache->mains == 'main_droite') {
						/* Hache à 1 main : 5% */
						$objet->malus_hache = 0.95;
					}
					else {
						/* Hache à 2 mains : 15% */
						$objet->malus_hache = 0.85;
					}
				case 'epee':
				case 'dague':
				case 'baton':
				case '': /* main nues */
					$objet->comp_combat = 'melee';
				break;
				case 'arc':
					$objet->comp_combat = 'distance';
					$objet->malus_arc = 0.8;
				break;
				case 'bouclier':
					$objet->comp_combat = 'blocage';
				break;
				default:
					die("Invalid arme_type ($this->arme_type) !!");
				}
			$objet->competence = $src->get_comp_perso();
			$objet->hp_max = $src->get_hp_maximum();
			$objet->rm_restant = $src->get_reserve_bonus();
			$objet->get_buff();
			$objet->etat = array();
			if ($src->get_bouclier())
				$objet->bouclier_degat = $src->get_bouclier()->degat;
			$objet->rang = $src->get_grade()->get_rang();
			$objet->espece = 'humainoïde';
			$objet->point_victoire = 0;
		  $objet->objet_ref = &$src;
			break;
		case 'ville' :
      $objet = new entite_cap($src);
      break;
		default:
      return new entite($type, $src);
    }
		$objet->objet_effet = array();
		$objet->type = $type;
		return $objet;
  }
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return placable::get_liste_champs().', hp';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return placable::get_valeurs_insert().', '.$this->hp;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return placable::get_liste_update().', hp = '.$this->hp;
	}
  /// Renvoie l'objet source
	function get_objet()
	{
		$objet = null;
		switch($this->type)
		{
			case 'joueur' :
				$objet = new perso($this->id);
				break;
			case 'monstre' :
				$objet = new map_monstre($this->id);
				break;
		}
		return $objet;
	}
	/// Affiche un "dump" de l'objet
	function dump() { echo '<pre>'; var_dump($this); echo '</pre>'; }
	// @}

	/**
	 * @name Les achievements
	 * Gestion des achievements
	 */
  // @{
	protected $compteur_critique=0;  ///< Compteur de coups critiques.
  /// Revnoie la valeur d'un compteur
	function get_compteur($type)
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->get_compteur($type);
		else
			return new fake_compteur ;
	}
	/// Incrémente le compteur de critiques
	function set_compteur_critique()
	{
		$this->compteur_critique++;
	}
  /// Renvoie la valeur du compteur de critiques
	function get_compteur_critique()
	{
		return $this->compteur_critique;
	}
  // @}
}
?>
