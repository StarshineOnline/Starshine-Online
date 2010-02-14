<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of entite
 *
 * @author florian
 */
class entite extends placable
{
	private $action;
	private $arme_type;
	protected $comp_combat;  ///< Liste des compétences de combat.
	private $comp;
	protected $hp;   ///< HP actuels de l'entité
	protected $hp_max;
	public $reserve;  ///< Réesrve de mana.
	protected $pa;  /// < Nombre de PA.
	protected $race;  ///< Race de l'entité.
	public $pp;  ///< Protection physique.
	public $pm;  ///< Protection magique.
	private $distance_tir;
	protected $esquive;  ///< Compétence esquive.
	protected $distance;  ///< Compétence tir à distance.
	protected $blocage;  ///< Compétence blocage.
	protected $melee;  ///< Compétence mêlée.
	protected $incantation;  ///< Compétence incantation.
	protected $sort_mort;  ///< Compétence nécromancie.
	protected $sort_vie;  ///< Compétence magie de la vie.
	protected $sort_element;  ///< Compétence magie élémentaire.
	public $buff;
	public $etat;
	protected $force;  ///< Caractéristique force.
	protected $puissance;  ///< Caractéristique puissance.
	protected $energie;  ///< Caractéristique énergie.
	protected $vie;  ///< Caractéristique constitution.
	protected $volonte;  ///< Caractéristique volonté.
	protected $dexterite;  ///< Caractéristique dextérité.
	private $enchantement;
	private $arme_degat;
	private $bouclier_degat = 0;
	protected $level;  ///< Niveau de l'entité.
	private $type;
	protected $rang_royaume;  ///< Grade de l'entité au sein de son royaume.
	private $espece;
	private $point_victoire;
	private $competence = array();

	private $objet_ref;

	public $potentiel_bloquer;
	private $malus_hache = 1;

	function __construct($type, &$objet)
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
						/* On calcule le malus d'esquive de la hache */
						$hache = $objet->get_arme();
						if ($hache->mains == 'main_droite') {
							/* Hache à 1 main : 5% */
							$this->malus_hache = 0.95;
						}
						else {
							/* Hache à 2 mains : 15% */
							$this->malus_hache = 0.85;
						}
					case 'epee':
					case 'dague':
					case 'baton':
					case '': /* main nues */
						$this->comp_combat = 'melee';
					break;
					case 'arc':
						$this->comp_combat = 'distance';
					break;
					case 'bouclier':
						$this->comp_combat = 'blocage';
					break;
					default:
						die("Invalid arme_type ($this->arme_type) !!");
					}
				$this->comp = $objet->get_comp();
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
				$pet = $objet->get_pet();
				$pet->get_monstre();
				$this->action = $pet->monstre->get_action();
				$this->arme_type = $pet->monstre->get_arme();
				$this->comp_combat = 'melee';
				$this->comp = array();
				$this->x = $objet->get_x();
				$this->y = $objet->get_y();
				$this->hp = $pet->get_hp();
				$this->hp_max = $pet->monstre->get_hp();
				$this->reserve = $pet->monstre->get_reserve();
				$this->pa = 0;
				$this->nom = $pet->get_nom();
				$this->race = 'neutre';
				$this->pp = $pet->monstre->get_pp();
				$this->pm = $pet->monstre->get_pm();
				$this->distance_tir = 0;
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
				$this->arme_degat = 0;
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
				$this->distance_tir = $objet->get_bonus4();
				$this->esquive = $this->facteur * ceil($this->coef_carac * $objet->get_carac());
				$this->distance = $objet->get_bonus4();
				$this->melee = $objet->get_bonus5() * $objet->bonus_architecture;
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
				$this->distance_tir = 1;
				$this->esquive = $this->facteur * ceil($this->coef_carac * $objet->get_carac());
				$this->distance = $objet->get_bonus4();
				$this->melee = $objet->get_bonus5() * $objet->bonus_architecture;
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
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return placable::get_liste_champs().', hp';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return placable::get_valeurs_insert().', $this->hp';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return placable::get_liste_update().', hp = $this->hp';
	}

	function get_type()
	{
		return $this->type;
	}

	function is_type($type)
	{
		return !strcmp($this->type, $type);
	}

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

	function get_action()
	{
		return $this->action;
	}
	function get_rang_royaume()
	{
		return $this->rang_royaume;
	}
	function get_rang()
	{
		if(isset($this->rang))
			return $this->rang;
		else
			return $this->rang_royaume;
	}
	function get_arme_type()
	{
		return $this->arme_type;
	}
	function get_bouclier_type()
	{
		return $this->bouclier() ? 'bouclier' : '';
	}
	function get_comp_combat()
	{
		return $this->comp_combat;
	}
	function get_comp()
	{
		return $this->comp;
	}
	function set_comp($valeur)
	{
		$this->comp = $valeur;
	}
	function get_hp()
	{
		return $this->hp;
	}
	function set_hp($valeur)
	{
		$this->hp = $valeur;
	}
	function get_hp_max()
	{
		return $this->hp_max;
	}
	function get_reserve()
	{
		return $this->reserve;
	}
	function set_reserve($valeur)
	{
		$this->reserve = $valeur;
	}
	function get_pa()
	{
		return $this->pa;
	}
	function get_race()
	{
		return $this->race;
	}
	function get_pp()
	{
		return $this->pp;
	}
	function set_pp($valeur)
	{
		$this->pp = $valeur;
	}
	function get_pm()
	{
		return $this->pm;
	}
	function set_pm($valeur)
	{
		$this->pm = $valeur;
	}
	function get_distance_tir()
	{
		return $this->distance_tir;
	}
	function get_esquive()
	{
		return $this->esquive;
	}
	function get_distance()
	{
		return $this->distance;
	}
	function get_melee()
	{
		return $this->melee;
	}
	function get_incantation()
	{
		return $this->incantation;
	}
	function get_sort_mort()
	{
		return $this->sort_mort;
	}
	function get_sort_vie()
	{
		return $this->sort_vie;
	}
	function get_sort_element()
	{
		return $this->sort_element;
	}
	
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
					if($buff->get_type() == $nom)
					{
						$get = 'get_'.$champ;
						return $buff->$get();
					}
				}
		}
	}
	
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

	function supprime_buff($id)
	{
		if(!isset($this->buff)) $this->get_buff();
		unset($this->buff[$id]);
	}

	function get_etat()
	{
		return $this->etat;
	}
	function get_force()
	{
		return $this->force;
	}
	function get_puissance()
	{
		return $this->puissance;
	}
	function get_energie()
	{
		return $this->energie;
	}
	function get_vie()
	{
		return $this->vie;
	}
	function get_volonte()
	{
		return $this->volonte;
	}
	function set_volonte($volonte)
	{
		$this->volonte = max(0, $volonte);
	}
	function get_dexterite()
	{
		return $this->dexterite;
	}
	function set_dexterite($dexterite)
	{
		$this->dexterite = max(0, $dexterite);
	}
	function get_enchantement()
	{
		return $this->enchantement;
	}
	function get_arme_degat()
	{
		return $this->arme_degat;
	}
	function get_bouclier_degat()
	{
		return $this->bouclier_degat;
	}
	/// Renvoie le niveau de l'entité
	function get_level()
	{
		return $this->level;
	}
	function get_grade()
	{
		$grade = grade::create(array('rang'), array($this->rang_royaume));
		return $grade[0];
	}
	function get_potentiel_toucher()
	{
		if($this->get_arme_type() == 'arc')
		{
			$this->potentiel_toucher = round($this->get_distance() + ($this->get_distance() * ((pow($this->get_dexterite(), 2)) / 1000)));
		}
		else
		{
			$this->potentiel_toucher = round($this->get_melee() + ($this->get_melee() * ((pow($this->get_dexterite(), 2)) / 1000)));
		}
		return $this->potentiel_toucher;
	}
	function set_potentiel_toucher($valeur)
	{
		$this->potentiel_toucher = $valeur;
	}

	function get_potentiel_parer($esquive = false)
	{
		if(!$esquive) $this->potentiel_parer = round($this->get_esquive() + ($this->get_esquive() * ((pow($this->get_dexterite(), 2)) / 1000)));
		else $this->potentiel_parer = round($esquive + ($esquive * ((pow($this->get_dexterite(), 2)) / 1000)));
		if ($this->arme_type == 'hache') 
			$this->potentiel_parer *= $this->malus_hache;
		return $this->potentiel_parer;
	}
	function set_potentiel_parer($valeur)
	{
		$this->potentiel_parer = $valeur;
	}

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

	function get_competence($nom)
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
	
	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}

	function get_espece()
	{
		return $this->espece;
	}

	function get_groupe()
	{
		if ($this->type == 'joueur') return $this->objet_ref->get_groupe();
		else return null;
	}

	function get_liste_quete()
	{
		if ($this->type == 'joueur') return $this->objet_ref->get_liste_quete();
		else return null;
	}

	function get_honneur()
	{
		if($this->type == 'joueur') return $this->objet_ref->get_honneur();
		else return null;
	}

	function get_reputation()
	{
		if($this->type == 'joueur') return $this->objet_ref->get_reputation();
		else return null;
	}

	function dump() { echo '<pre>'; var_dump($this); echo '</pre>'; }

	function bouclier()
	{
		switch($this->type)
		{
			case 'joueur' :
				//$this->dump();
				return $this->objet_ref->get_bouclier();
				break;
			case 'monstre' : 
				break;
		}
		return false;
	}

	private $saved_inventaire = null;
	function get_inventaire()
	{
		if ($this->type == 'joueur' && $this->saved_inventaire == null)
			$this->saved_inventaire = $this->objet_ref->get_inventaire();
		return $this->saved_inventaire;
	}

	function get_blocage() { return $this->blocage; }

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
			$this->comp = $this->objet_ref->get_comp();
			$this->distance = $this->objet_ref->get_distance();
			$this->competence = $this->objet_ref->get_comp_perso();
		}
	}

	function get_exp()
	{
		if($this->type == 'joueur')
			return $this->objet_ref->get_exp();
		else
			return 0;
	}

	function get_star()
	{
		if(isset($this->star))
			return $this->star;
		else
			return 0;
	}

	function get_point_victoire()
	{
		return $this->point_victoire;
	}

	function set_pa($new_pa)
	{
		if($this->type == 'joueur')
			$this->objet_ref->set_pa($new_pa);
	}

	function get_arme()
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->get_arme();
	}

  function add_hp($add_hp) 
	{
		$this->hp += $add_hp;
		if ($this->hp > $this->hp_max)
			$this->hp = $this->hp_max;
	}

	function in_arene($filter = '')
	{
		if ($this->type == 'joueur')
			return $this->objet_ref->in_arene($filter);
		return false;
	}

}
?>
