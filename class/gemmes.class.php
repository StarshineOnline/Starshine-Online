<?php
if (file_exists('../root.php'))
  include_once('../root.php');

/**
* @file gemmes.class.php
*/

include_once(root.'class/effect.class.php');


class gemme_enchassee extends effect
{

	var $id;
	var $enchantement_type;
	var $enchantement_effet;
	var $enchantement_effet2;

	var $poison;
  
  function __construct($aNom) {
    parent::__construct("Gemme $aNom");
    
    $query = 'select id, enchantement_type, enchantement_effet, enchantement_effet2, nom from gemme where ';
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
		$this->id = $row['id'];

		$this->poison = 0;

    //var_dump($this);
	}

	/**
	 * Les gemmes d'arme (degat/reduction) de pp, pp_pourcent, pm, pm_pourcent,
   * et  competence sont gerees directement ailleurs
   * les autres (default:) posent une valeur [type]=id dans le tableau
   * des enchantement
   *
   * @see effect::factory
	 */
	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    $actives = array('vampire', 'poison', 'divine');
    $passives = array('bouclier', 'bouclier_epine', 'blocage',
                      'parade', 'evasion', 'divine');

		//my_dump($actif->get_enchantement());

    foreach ($actif->get_enchantement() as $type => $enchant) {
      if (isset($enchant['gemme_id']) and in_array($type, $actives)) {
				$gems = explode(';', $enchant['gemme_id']);
				foreach ($gems as $gem) {
					$effects[] = new gemme_enchassee($gem);
				}
      }
    }
    foreach ($passif->get_enchantement() as $type => $enchant) {
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
		if ($this->enchantement_type == 'poison' &&
				$passif->get_type() != 'batiment') {
			$de = rand(1, 100);
			$this->debug('poison: d100 doit être inférieur à '.$this->enchantement_effet.": $de");
			if ($de <= $this->enchantement_effet) {
				$this->hit($passif->get_nom().' est empoisonné par '.$this->nom);
				$this->poison = $this->enchantement_effet2;
				$passif->etat['poison_lent']['effet'] = $this->enchantement_effet2;
				$passif->etat['poison_lent']['duree'] = 5;
			}
		}

    // Vampirisme
		if ($this->enchantement_type == 'vampire') {
			/* elles sont toutes à 30%, sinon il faudra un effet 2 */
			$de = rand(1, 100);
			$this->debug("vampire: d100 doit être inférieur à 30: $de");
			if ($de <= 30) {
				$gain = min($this->enchantement_effet, $degats);
				if (($actif->get_hp() + $gain) > $actif->get_hp_max())
					$gain = $actif->get_hp_max() - $actif->get_hp();
				if ($passif->get_type() == 'batiment')
					$gain = 0;
				$actif->add_hp($gain);
				if ($gain > 0) 
					$this->heal($actif->get_nom().' gagne '.$gain.' HP par sa '.
											$this->nom, true);
			}
		}

		return $degats;
	}

	// Gemme d'epine
	function applique_bloquage(&$actif, &$passif, $degats) {
		if ($this->enchantement_type == 'bouclier_epine') {
			$actif->add_hp(-$this->enchantement_effet);
			$this->hit($actif->get_nom().' perd '.$this->enchantement_effet.
								 ' HP par la '.$this->nom.' du bouclier de '.
								 $passif->get_nom(), true);
		}
    if ($this->enchantement_type == 'parade_totale') {
			$de = rand(1, 100);
			$this->debug("parade totale: d100 doit être inférieur à $this->enchantement_effet: $de");
      if ($de <= $this->enchantement_effet) {
        $this->message('La '.$this->nom.' de <strong>'.$passif->get_nom().
                       '</strong> pare totalement le coup');
        $degats = 0;
      }
    }
		return $degats;
	}

	// Gemme de l'epervier
	function calcul_bloquage(&$actif, &$passif) {
		if ($this->enchantement_type == 'parade') {
			$passif->potentiel_bloquer +=
				floor($passif->potentiel_bloquer * $this->enchantement_effet / 100);
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