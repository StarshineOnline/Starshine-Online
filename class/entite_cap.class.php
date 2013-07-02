<?php // -*- mode: php; tab-width: 2 -*-
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
  protected $royaume;
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
		$this->royaume = $royaume;
  }

  /// Action effectuées à la fin d'un combat
  function fin_combat(&$actif, &$passif) {
    // TODO ??
    // A priori on fait rien, mais on ne le dit pas à l'écran
  }

  /// Action effectuées à la fin d'un combat
  function fin_defense(&$perso, $R, $pet, $degats=null)
  {
  		global $Trace;
		//hasard pour différente actions de destruction sur la ville.
		//Si il y a assez de ressources en ville
		$suppr_hp = true;
		if($this->royaume->total_ressources() > 1000)
		{
			$rand = rand(1, 100);
			//Premier cas, on supprime les ressources
			if($rand >= 50)
			{
				$suppr_hp = false;
				$this->royaume->supprime_ressources($degats / 100);
				echo '<h6>L\'attaque détruit des ressources au royaume '.$Gtrad[$this->royaume->get_race()].'</h6><br />';
			}
		}
		//Sinon on attaque les batiments ou la ville
		if($suppr_hp)
		{
			print_debug("Les infrastructures sont touchées<br/>");
			$this->royaume->get_constructions_ville(true);
			$count = count($this->royaume->constructions_ville);
			//Si on peut détruire des bâtiments en ville
			if($count > 0)
			{
				$rand = rand(0, $count - 1);
				//On attaque la construction $rand du tableau
				$construction_ville = new construction_ville($this->royaume->constructions_ville[$rand]['id']);
				$return = $construction_ville->suppr_hp($degats);
				echo '<h6>Attaque d\'un batiment en ville</h6>';
				//On a downgrade un batiment, on gagne des points de victoire
				if($return > 0)
				{
					$royaume_attaquant = new royaume($Trace[$perso->get_race()]['numrace']);
          $mult = $royaume_attaquant->get_mult_victoire($this->royaume);
					$royaume_attaquant->add_point_victoire( ceil($return*$mult) );
					$royaume_attaquant->sauver();
					echo '<h6>Une construction a été détruite ! Votre royaume gagne '.$return .' points de victoire.</h6><br />';
				}
			}
			else
			{
				echo '<h6>Le coeur même de la ville est attaqué</h6>';
				$this->royaume->set_capitale_hp($this->get_hp());
				//Si la capitale n'a plus de vie, on met le royaume en raz
				if($this->royaume->get_capitale_hp() < 0)
				{
					$time = time() + 3600 * 24 * 31;
					$this->royaume->set_fin_raz_capitale($time);
					$royaume_attaquant = new royaume($Trace[$perso->get_race()]['numrace']);
          $mult = $royaume_attaquant->get_mult_victoire($this->royaume);
					$royaume_attaquant->add_point_victoire( ceil(100*$mult) );
					$royaume_attaquant->sauver();
					echo '<h6>La capitale est détruite ! Votre royaume gagne 100 points de victoire.</h6><br />';

					// On debloque l'achievement
					$perso->unlock_achiev('capitale_detruite');
				}
			}
		}
		$this->royaume->sauver();
	}

  // à améliorer
  function get_type_def()
  {
    return 'capitale';
  }
}
