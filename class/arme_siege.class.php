<?php
/**
 * @file arme_siege.class.php
 * Définition de la classe construction représentant une arme de siege sur la carte
 */

/**
 * Classe arme_siege
 * Classe représentant une arme de siege sur la carte
 */
class arme_siege extends construction
{
	/// Renvoie le coefficient pour modifier les caractéristique
  function get_coeff_carac() { return 0; }
	/// Renvoie le coefficient pour modifier les compétences
  function get_coeff_comp($perso)
  {
    return 1 + ($perso->get_architecture() / 100);
  }
	/**
	 * Renvoie le facteur de dégâts de ou des armes.
	 * La plupart du temps on s'en fiche, de la main, on veut les degats
	 * @param $main   si false : cumul, si 'droite' ou 'gauche' : detail
	 */
	function get_arme_degat($main = false, $adversaire=null)
	{
    if( $adversaire != null && $adversaire->get_type_def() == 'arme_de_siege')
    {
			if($this->is_buff('buff_degats_siege'))
				return $this->get_buff('buff_degats_siege', 'effet');
			if($this->is_buff('debuff_degats_siege'))
				return $this->get_buff('debuff_degats_siege', 'effet');
		}
    else
    {
			if($this->is_buff('buff_degats_bat'))
				return $this->get_buff('buff_degats_bat', 'effet');
			if($this->is_buff('debuff_degats_bat'))
				return $this->get_buff('debuff_degats_bat', 'effet');
		}
	}
}
?>
