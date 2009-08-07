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

	function get_potentiel_parer()
	{
		$this->potentiel_parer = round($this->get_esquive() + ($this->get_esquive() * ((pow($this->get_dexterite(), 2)) / 1000)));
		return $this->potentiel_parer;
	}

}
?>
