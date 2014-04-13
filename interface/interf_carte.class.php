<?php
/**
 * @file interf_carte.class.php
 * Classes pour la partie gauche de l'interface
 */  

/// Classe pour afficher la carte du jeu
class interf_carte extends interf_tableau
{
	function __construct($x, $y, $champ_vision=3)
	{
		parent::__construct('carte', null, 'carte_bord_haut');
		$x_min = $x - $champ_vision;
		$x_max = $x + $champ_vision;
		$y_min = $y - $champ_vision;
		$y_max = $y + $champ_vision;
		
		// Bord haut
		$this->nouv_cell('&nbsp;', 'carte_bord_haut_gauche');
		for($j=$x_min; $j<=$x_max; $j++)
		{
			$this->nouv_cell($j, $j==$x ? 'carte_bord_haut_x' : null);
		}
		
		// On récupère les infos sur les cases
		$infos_cases = map::get_valeurs('decor,royaume,info,type', 'x >= '.$x_min.' AND x <= '.$x_max.' AND y >= '.$y_min.' AND y <= '.$y_max, array('x','y'));
		// Carte
		$cases = array();
		for($i=$y_min; $i<=$y_max; $i++)
		{
			$this->nouv_ligne();
			$this->entete = true;
			$this->nouv_cell($i, $i==$y ? 'carte_bord_haut_y' : null);
			$this->entete = false;
			for($j=$x_min; $j<=$x_max; $j++)
			{
				$cases[$i][$j] = &$this->nouv_cell('&nbsp;', null, 'decor tex'.$infos_cases[$j.'|'.$i]['decor']);
			}
		}
	}
}
?>