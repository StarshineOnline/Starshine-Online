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
class entite
{
	private $id;
	private $action;
	private $arme_type;
	private $comp_combat;
	private $comp;
	private $x;
	private $y;
	private $hp;
	private $hp_max;
	private $reserve;
	private $pa;
	private $nom;
	private $race;
	private $pp;
	private $pm;
	private $distance_tir;
	private $esquive;
	private $distance;
	private $blocage;
	private $melee;
	private $incantation;
	private $sort_mort;
	private $sort_vie;
	private $sort_element;
	public $buff;
	public $etat;
	private $force;
	private $puissance;
	private $energie;
	private $vie;
	private $volonte;
	private $dexterite;
	private $enchantement;
	private $arme_degat;
	private $level;
	private $type;
	private $rang_royaume;
	private $espece;
	private $point_victoire;

	private $objet_ref;

	public $potentiel_bloquer;

	function __construct($type, &$objet)
	{
		$this->objet_effet = array();
		$this->type = $type;
		$this->objet_ref = &$objet;
		switch($type)
		{
			case 'joueur' :
				$this->id = $objet->get_id();
				$this->action = $objet->action_do;
				$this->arme_type = $objet->get_arme_type();
				switch ($this->arme_type)
					{
					case 'epee':
					case 'dague':
					case 'hache':
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
				$this->enchantement = array();
				$this->arme_degat = $objet->get_arme_degat();
				$this->level = $objet->get_level();
				$this->rang_royaume = $objet->get_rang_royaume();
				$this->rang = $objet->get_grade()->get_rang();
				$this->espece = 'humainoïde';
				$this->blocage = $objet->get_blocage();
				$this->star = $objet->get_star();
				$this->point_victoire = 0;
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

	function get_id()
	{
		return $this->id;
	}
	function set_id($id)
	{
		$this->id = $id;
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
		return $this->arme_type;
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
	function get_x()
	{
		return $this->x;
	}
	function get_y()
	{
		return $this->y;
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
	function get_nom()
	{
		return $this->nom;
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
	function get_dexterite()
	{
		return $this->dexterite;
	}
	function get_enchantement()
	{
		return $this->enchantement;
	}
	function get_arme_degat()
	{
		return $this->arme_degat;
	}
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
				$tmp = $this->buff;
				while(current($tmp) && !$buffe)
				{
					if($type)
					{
						if(strcmp(current($tmp)->get_type(), $nom) == 0)
							$buffe = true;
					}
					else if(strcmp(current($tmp)->get_nom(), $nom) == 0)
					{
						$buffe = true;
					}
					next($tmp);
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;

		return $buffe;
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

	function get_inventaire()
	{
		if ($this->type == 'joueur') return $this->objet_ref->get_inventaire();
		return null;
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

	function supprimer()
	{
		if ($this->type == 'batiment')
			$this->objet_ref->supprimer();
	}

}
?>
