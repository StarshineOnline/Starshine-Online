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
		if ($row == false) {
			$back = debug_backtrace();
      foreach ($back as $f) {
        echo $f["file"].' line '.$f["line"].'<br />';
			}
			echo $aReq.'<br />';
			die("impossible d'initialiser la gemme");
		}
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
				$gems = explode(';', $enchant['gemme_id']);
				foreach ($gems as $gem) {
					$effects[] = new gemme_enchassee($gem);
				}
      }
    }
    foreach ($passif['enchantement'] as $type => $enchant) {
      if (isset($enchant['gemme_id']) and in_array($type, $passives)) {
				$gems = explode(';', $enchant['gemme_id']);
        foreach ($gems as $gem) {
          $effects[] = new gemme_enchassee($gem);
        }
      }
    }
	}

  function inflige_degats(&$actif, &$passif, $degats) {

    // Test du poison
		if ($this->enchantement_type == 'poison' && $passif['type2'] != 'batiment') {
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
			$de = rand(1, 100);
			$this->debug("vampire: d100 doit être inférieur à 30: $de");
			if ($de <= 30) {
				$gain = min($this->enchantement_effet, $degats);
				if (($actif['hp'] + $gain) > $actif['hp_max'])
					$gain = $actif['hp_max'] - $actif['hp'];
				if ($passif['type2'] == 'batiment') 
					$gain = 0;
				$actif['hp'] += $gain;
				if ($gain > 0) 
					$this->heal($actif['nom'].' gagne '.$gain.' HP par sa '.$this->nom, true);
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
    if ($this->enchantement_type == 'parade') {
			$de = rand(1, 100);
			$this->debug("parade: d100 doit être inférieur à $this->enchantement_effet: $de");
      if ($de <= $this->enchantement_effet) {
        $this->message('La '.$this->nom.' de <strong>'.$passif['nom'].
                       '</strong> pare totalement le coup');
        $degats = 0;
      }
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