<?php
/**
* @file gemmes.class.php
*/

if (file_exists('class/effect.class.php')) {
  include_once('class/effect.class.php');
} else {
  include_once('../class/effect.class.php');
}

class gemme_enchassee extends effect
{

	var $enchantement_type;
	var $enchantement_effet;
	var $enchantement_effet2;

	var $poison;
  
  function __construct($aNom) {
    parent::__construct("Gemme $aNom");
    
    $query = 'select enchantement_type, enchantement_effet, enchantement_effet2, nom from gemme where ';
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
		$this->enchantement_effet2 = $row['enchantement_effet2'];
		$this->nom = $row['nom'];

		$this->poison = 0;

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
    $actives = array('vampire', 'poison', 'divine');
    $passives = array('bouclier', 'bouclier_epine', 'blocage',
                      'parade', 'evasion', 'divine');
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
			$de = rand(1, 100);
			$this->debug('poison: d100 doit être inférieur à '.$this->enchantement_effet.": $de");
			if ($de <= $this->enchantement_effet) {
				$this->hit($passif['nom'].' est empoisonné par '.$this->nom);
				$this->poison = $this->enchantement_effet2;
				$passif['etat']['poison_lent']['effet'] = $this->enchantement_effet2;
				$passif['etat']['poison_lent']['duree'] = 5;
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
					$this->heal($actif['nom'].' gagne '.$gain.' HP par sa gemme', true);
			}
		}

		return $degats;
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
		if ($this->enchantement_type == 'parade') {
			$passif['potentiel_bloquer'] +=
				floor($passif['potentiel_bloquer'] * $this->enchantement_effet / 100);
		}
	}

  // Gemme divine
  function calcul_mp(&$actif, $mp) {
		if ($this->enchantement_type == 'divine') {
      $mp -= $this->enchantement_effet;
      if ($mp < 1) { $mp = 1; }
    }
    return $mp;
  }

}

?>