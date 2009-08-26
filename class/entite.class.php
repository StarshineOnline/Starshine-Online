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

	function __construct($type, $objet)
	{
		$this->objet_effet = array();
		switch($type)
		{
			case 'joueur' :
				$this->action = $objet->action_do;
				$this->arme_type = $objet->get_arme_type();
				$this->comp_combat = 'melee';
				$this->comp = $objet->get_comp();
				$this->x = $objet->get_x();
				$this->y = $objet->get_y();
				$this->hp = $objet->get_hp();
				$this->hp_max = $objet->get_hp_max();
				$this->reserve = $objet->get_reserve();
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
				$this->arme_degat = 0;
				$this->level = $objet->get_level();
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
				$this->distance_tir = 1;
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
			break;
			case 'batiment' :
				$this->coef_carac = 1;
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
				$this->esquive = $facteur * ceil($this->coef_carac * $objet->get_carac());
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
			break;
		}
	}

	function get_action()
	{
		return $this->action;
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
		$this->comp = valeur;
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
	function get_buff()
	{
		return $this->buff;
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

	function get_potentiel_parer($esquive = false)
	{
		if(!$esquive) $this->potentiel_parer = round($this->get_esquive() + ($this->get_esquive() * ((pow($this->get_dexterite(), 2)) / 1000)));
		else $this->potentiel_parer = round($esquive + ($esquive * ((pow($this->get_dexterite(), 2)) / 1000)));
		return $this->potentiel_parer;
	}

	function is_buff($nom = '', $type = false)
	{
		$buffe = false;
		if(is_array($this->buff))
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
}
?>
