<?php
/**
* @file gemmes.class.php
*/

include_once('class/effect.class.php');

class gemme_enchassee extends effect
{

	var $enchantement_type;
	var $enchantement_effet;

	var $poison;
  
  function __construct($aNom) {
    parent::__construct("Gemme $aNom");
    
    $query = 'select enchantement_type, enchantement_effet, nom from gemme where ';
    if (is_numeric($aNom)) {
      $query .= "id = $aNom";
    }
    else {
      $query .= "nom = '$aNom'";
    }
		$this->init($query);
		
  }

	function init($aReq) {
		global $db;
		$res = $db->query($aReq);
		$row = $db->read_array($res);
		if ($row == false) die("impossible d'initialiser la gemme");
		$this->enchantement_type = $row['enchantement_type'];
		$this->enchantement_effet = $row['enchantement_effet'];
		$this->nom = $row['nom'];

		$this->poison = false;

    //var_dump($this);
	}

	/**
	 * Les gemmes d'arme (degat) de pp, pp_pourcent, pm, pm_pourcent, competence
	 * sont gerees directement dans enchant(fonction/equipement.inc.php)
   * les autres (default:) posent une valeur [type]=id dans le tableau
   * des enchantement
   *
   * @see effect::factory
	 */
	static function factory(&$effects, &$actif, &$passif, $acteur) {
    $actives = array('vampire', 'poison');
    $passives = array('bouclier', 'bouclier_epine', 'blocage',
                      'parade', 'evasion');
    foreach ($actif['enchantement'] as $type => $enchant) {
      if (isset($enchant['gemme_id']) and in_array($type, $actives)) {
        $effects[] = new gemme_enchassee($enchant['gemme_id']);
      }
    }
    foreach ($passif['enchantement'] as $type => $enchant) {
      if (isset($enchant['gemme_id']) and in_array($type, $passives)) {
        $effects[] = new gemme_enchassee($enchant['gemme_id']);
      }
    }
	}

  function inflige_degats(&$actif, &$passif, $degats) {

    // Test du poison
		if ($this->enchantement_type == 'poison') {
			$effets = explode(';', $this->enchantement_effet);
			$de = rand(1, 100);
			$this->debug('poison: de 100 < a '.$effets[0].": $de");
			if ($de <= $effets[0]) {
				$this->hit($passif['nom'].'est empoisonné');
				$this->poison = $effets[1];
			}
		}

    // Vampirisme
		if ($this->enchantement_type == 'vampire') {
			/* elles sont toutes à 30%, sinon il faudra un effet 2 */
			if (rand(1, 100) <= 30) {
				$gain = $this->enchantement_effet;
				if (($actif['hp'] + $gain) > $actif['hp_max'])
					$gain = $actif['hp_max'] - $actif['hp'];
				$actif['hp'] += $gain;
				if ($gain > 0) 
					$this->heal($actif['nom'].' gagne '.$effet.' HP par sa gemme', true);
			}
		}

		return $degats;
	}

	// Applique le poison
  function fin_round(&$actif, &$passif) {
		if ($this->poison) {
			$passif['etat']['empoisonne']['effet'] = $this->poison;
			$passif['etat']['empoisonne']['duree'] = 5;
		}
	}

	// Gemme d'epine
	function applique_bloquage(&$actif, &$passif, $degats) {
		if ($this->enchantement_type == 'bouclier_epine') {
			$actif['hp'] -= $this->enchantement_effet;
			$this->hit($actif['nom'].' perd '.$this->enchantement_effet.
								 ' HP par l\'épine du bouclier de '.$passif['nom'], true);
		}
		return $degats;
	}

	// Gemme de roc
	function calcul_bloquage_reduction(&$actif, &$passif, $reduction) {
		if ($this->enchantement_type == 'bouclier')
			$reduction += $this->enchantement_effet;
		return $reduction;
	}

	// Gemme de l'epervier
	function calcul_bloquage(&$actif, &$passif) {
		if ($this->enchantement_type == 'blocage') {
			$passif['potentiel_bloquer'] =
				floor($passif['potentiel_bloquer'] *  $this->enchantement_effet);
		}
	}

}

?>