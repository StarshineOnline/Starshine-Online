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
}
?>
