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
	private $action_a;
	private $action_d;
	private $arme_type;
	private $comp;
	private $x;
	private $y;
	private $hp;
	private $reserve;
	private $pa;
	private $nom;
	private $race;
	private $pp;
	private $pm;
	private $distance_tir;
	private $distance;
	private $melee;
	private $incantation;
	private $buff;
	private $etat;
	private $force;
	private $puissance;
	private $energie;
	private $vitalite;
	private $volonte;
	private $dexterite;
    //put your code here
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

}
?>
