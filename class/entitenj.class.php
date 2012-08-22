<?php
/**
 * @file entitenj.class.php
 * Définition de la classe entitenj
 */

/**
 * Classe abstraite entitenj
 * Classe gérant les entité non joueurs dont les informatiosn sotn réparties sur deux tables
 */
class entitenj extends entite
{
  private $incarn;  ///< Objet contenant les informations sur une incarnation particulière de l'entité (dérivé de entnj_incarn)
  private $def;  ///< Objet contenant les informations sur la définition de l'entité (dérivé de entitenj_def)
  
  
  function __construct($incarn, $perso=null, $attaquant=true, $adversaire=null)
  {
    $def = $incarn->get_def();
    $this->id = $incarn->get_id();
    $this->incarn = &$incarn;
    $this->def = $def;
		$this->action = $incarn->get_action($attaquant);
    if( $this->action === false )
			$this->action = $def->get_action();
		$this->arme_type = $def->get_arme();
		$this->comp_combat = 'melee';
		$this->comp = array();
		$this->x = $incarn->get_x();
		$this->y = $incarn->get_y();
		if( !$this->x )
		{
      $this->x = $perso->get_x();
		  $this->y = $perso->get_y();
    }
		$this->hp = $incarn->get_hp();
		$this->hp_max = $def->get_hp();
		$this->rm_restant = $def->get_reserve();
		$this->pa = 0;
		$this->nom = $incarn->get_nom();
		$this->race = $incarn->get_race($perso);
		$this->pp = $def->get_pp() + $incarn->get_bonus_pp();
		$this->pm = $def->get_pm() + $incarn->get_bonus_pm();
		$this->pm_para = $this->pm;
		$this->distance_tir = $incarn->get_distance_tir();
    if( !$this->distance_tir )
      $this->distance_tir = $def->get_distance_tir();
		$this->esquive = ceil( $def->get_esquive() * $incarn->get_coeff_comp($perso) );
		$this->distance = ceil( $def->get_distance() * $incarn->get_coeff_comp($perso) );
		$this->melee = ceil( $def->get_melee() * $incarn->get_coeff_comp($perso) );
		$this->incantation = ceil( $def->get_incantation() * $incarn->get_coeff_comp($perso) );
		$this->sort_mort = ceil( $def->get_sort_mort() * $incarn->get_coeff_comp($perso) );
		$this->sort_vie = ceil( $def->get_sort_vie() * $incarn->get_coeff_comp($perso) );
		$this->sort_element = ceil( $def->get_sort_element() * $incarn->get_coeff_comp($perso) );
		$this->blocage = $def->get_blocage();
		$this->buff = $incarn->get_buff();
		$this->etat = array();
		$this->force = ceil( $def->get_force() * $incarn->get_coeff_carac() );
		$this->puissance = ceil( $def->get_puissance() * $incarn->get_coeff_carac() );
		$this->energie = ceil( $def->get_energie() * $incarn->get_coeff_carac() );
		$this->vie = ceil( $def->get_constitution() * $incarn->get_coeff_carac() );
		$this->volonte = ceil( $def->get_volonte() * $incarn->get_coeff_carac() );
		$this->dexterite = ceil( $def->get_dexterite() * $incarn->get_coeff_carac() );
		$this->enchantement = array();
		$this->arme_degat = $incarn->get_arme_degat() + $def->get_arme_degat($perso, $adversaire);
		$this->bouclier_degat = $def->get_bouclier();
		$this->level = $def->get_level();
		$this->rang_royaume = 0;
		$this->star = $def->get_star();
		$this->espece = $def->get_type();
		$this->point_victoire = $def->get_point_victoire();
  }
  
  /// Action effectuées à la fin d'un combat
  function fin_combat(&$perso, $degats=null)
  {
    $this->incarn->set_hp( $this->get_hp() );
    if( $this->get_hp() > 0 )
      $this->incarn->sauver();
    else
      $this->incarn->mort($perso);
  }
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, $batiment)
  {
    return $this->incarn->fin_defense($perso, $royaume, $pet, $degats, $this->def);
  }
  
  // à améliorer
  function get_type_def()
  {
    return $this->def->get_type();
  }
}

?>
