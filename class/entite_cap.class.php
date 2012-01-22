<?php
/**
 * @file entite_cap.class.php
 * Définition de la classe entite_cap
 */

/**
 * Classe abstraite entite_cap
 * Classe gérant une capitale dans les combats
 */
class entite_cap extends entite
{
  function __construct($royaume)
  {
    $coef = 1;
    $carac = 16 + $royaume->get_level_mur();
		$facteur = 40;
		$this->action = '!';
		$this->arme_type = 'epee';
		$this->comp_combat = 'melee';
		$this->comp = array();
		$this->x = $royaume->x;
		$this->y = $royaume->y;
		$this->hp = $royaume->get_capitale_hp();
		$this->hp_max = 30000;
		$this->reserve = 0;
		$this->pa = 100;
		$this->nom = "";
		$this->race = 'neutre';
		$this->pp = $royaume->get_pp();
		$this->pm = 0;
		$this->pm_para = 0;
		$this->distance_tir = 1;
		$this->esquive = 40 * $carac;
		$this->distance = 0;
		$this->melee = 0;
		$this->incantation = 0;
		$this->sort_mort = 0;
		$this->sort_vie = 0;
		$this->sort_element = 0;
		$this->buff = array();
		$this->etat = array();
		$this->force = 	$carac;
		$this->puissance = $carac;
		$this->energie = $carac;
		$this->vie = $carac;
		$this->volonte = $carac;
		$this->dexterite = $carac;
		$this->enchantement = array();
		$this->arme_degat = 0;
		$this->level = 1;
		$this->espece = 'ville';
		$this->point_victoire = 0;
  }
}