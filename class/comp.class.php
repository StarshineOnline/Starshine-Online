<?php //  -*- tab-width:2; intent-tabs-mode:nil;  -*-

/**
* Classe de base pour la gestion des effets en combat
*/
class effect
{
  var $nom;
  var $order; // Sert à déterminer l'ordre des effets
  var $used;

  function __construct($aNom) {
    $this->nom = $aNom;
    $this->order = 1;
    $this->used = false;
  }

  function compare_effects($a, $b) {
    if ($a->order == $b->order) {
      return 0;
    }
    return ($a->order > $b->order) ? -1 : 1;
  }

	function hit($aMessage) {
		echo "<span class=\"degat\">$aMessage</span>";
	}

	function notice($aMessage) {
		echo "<span class=\"small\">$aMessage</span>";
	}

	function debug($aMessage) {
		global $debugs;
		echo "<div class=\"debug\" id=\"debug${debugs}\">$aMessage</div>";
		$debug++;
	}

	function message($aMessage) {
		echo $aMessage."<br />\n";
	}

	static function factory(&$effects, &$actif, &$passif, $acteur) {}

  /* Méthodes à dériver selon les besoins */
  function debut_round(&$actif, &$passif) { }
	function calcul_attaque_magique(&$actif, &$passif, $att) { return $att; }
	function calcul_defense_magique(&$actif, &$passif, $def) { return $def; }
  function calcul_arme(&$actif, &$passif, $arme) { return $arme; }
  function calcul_degats(&$actif, &$passif, $degats) { return $degats; }
  function calcul_bloquage(&$actif, &$passif) { }
  function applique_bloquage(&$actif, &$passif, $degats) { return $degats; }
  function calcul_pp(&$actif, &$passif, $pp) { return $pp; }
  function calcul_critique(&$actif, &$passif, $chance) { return $chance; }
  function calcul_mult_critique(&$actif, &$passif, $mult) { return $mult; }
  function inflige_degats(&$actif, &$passif, $degats) { }
  function fin_round(&$actif, &$passif) { }
  function fin_combat(&$actif, &$passif) { }
}

function sort_effects(array& $effects) {
  usort($effects, array('effect', 'compare_effects'));
}

/**
* Compétence : c'est un effet qui peut s'améliorer
*/
class competence extends effect
{
  var $print_up;

  function __construct($aNom, $aPrintUp = false) {
    parent::__construct($aNom);
    $this->print_up = $aPrintUp;
  }

  function test_montee(&$acteur, $diff) {
    global $Gtrad;
    global $db;
    $augmentation = augmentation_competence($this->nom, $acteur, $diff);
    if ($augmentation[1] == true) {
      global $ups;
      if ($this->print_up)
        echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.
          $augmentation[0].' en '.$Gtrad[$this->nom].'</span><br />';
      $requete = "UPDATE comp_perso SET valeur = ".$augmentation[0].
	" WHERE id_perso = ".$acteur['ID'].
	" AND competence = '".$this->nom."'";
      //echo "$requete<br/>";
      $db->query($requete);
      $acteur['competences'][$this->nom] = $augmentation[0];
      return true;
    }
    return false;
  }
}

/**
* Effect actif : buff ou compétence de combat
*/
class active_effect extends effect
{
  var $table;
  var $level;
	var $effet = null;
	var $effet2 = null;
	var $effet3 = null;
	var $duree = null;
	var $requis = null;

  function __construct($aNom, $aTable, $aLevel = null) {
    parent::__construct($aNom, false);
    global $db;
    $this->table = $aTable;
    $this->level = $aLevel;
		$sNom = $aNom;
		$requete = "select * from $aTable where type = '$aNom'";
		if ($aLevel != null) $requete .= " and level = '$aLevel'";
		//echo "<small>$requete</small><br />";
		$db->query($requete);
		if ($db->num_rows < 0) throw new Exception("Cannot find $sNom");
		$row = $db->read_assoc($req);
		if (isset($row['effet'])) $this->effet = $row['effet'];
		if (isset($row['effet2'])) $this->effet2 = $row['effet2'];
		if (isset($row['effet3'])) $this->effet3 = $row['effet3'];
		if (isset($row['duree'])) $this->duree = $row['duree'];
		if (isset($row['arme_requis'])) $this->requis = $row['arme_requis'];
  }
}

/**
 * Competence de combat
 */
class comp_combat extends active_effect
{
  function __construct($aNom, $aLevel) {
    parent::__construct($aNom, 'comp_combat', $aLevel);
  }
}


/**
 * botte : competence de combat avec une condition
 */
class botte extends comp_combat
{
	var $condition;
	
  function __construct($aCondition, $aNom, $aLevel) {
    parent::__construct($aNom, $aLevel);
		$this->condition = $aCondition;
	}

	function canUse(&$actif) {
		return true; /* debug */
		if (isset($actif['precedent'][$this->condition]))
			return $actif['precedent'][$this->condition];
		else
			return false;
	}
}

/***************************************************************************/
/**************       Définition des effets en eux-même       **************/
/***************************************************************************/

/* Maitrise du bouclier */
class maitrise_bouclier extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_bouclier', $aPrintUp);
    //echo 'instance de maitrise_bouclier <br/>';
  }

	static function factory(&$effects, &$actif, &$passif, $acteur) {
		if ($passif['bouclier'] AND 
				array_key_exists('maitrise_bouclier', $passif['competences']))
			$effects[] = new maitrise_bouclier($acteur != 'attaquant');
	}

  function calcul_bloquage(&$actif, &$passif) {
    $this->used = true;
    $passif['potentiel_bloquer'] *= 1 + ($passif['competences']['maitrise_bouclier'] / 1000);
    $passif['maitrise_bouclier'] = $passif['competences']['maitrise_bouclier'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($passif, 6);
    }
  }
}

/* Maitrise des dagues */
class maitrise_dague extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_dague', $aPrintUp);
  }

	static function factory(&$effects, &$actif, &$passif) {
	}

  function debut_round(&$actif, &$passif) {
    $this->used = true;
    $actif['potentiel_toucher'] *= 1 + ($passif['competences']['maitrise_dague']/ 1000);
    $passif['maitrise_dague'] = $passif['competences']['maitrise_dague'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($passif, 6);
    }
  }
}

/* Art du critique */
class art_critique extends competence
{
  var $critique;

  function __construct($aPrintUp = false) {
    parent::__construct('art_critique', $aPrintUp);
    $this->critique = false;
    $this->begin = 0; // S'applique en premier, puisque addition
  }

  function fin_round(&$actif, &$passif) {
    if ($this->critique) {
      $this->test_montee($actif, 3.5);
    }
  }

  function calcul_mult_critique(&$actif, &$passif, $mult) {
    $this->critique = true;
    return $mult + $actif['competences']['art_critique'];
  }
}

/* Préparation */
class preparation extends comp_combat
{
  function __construct($aLevel = 1) {
    parent::__construct('posture_esquive', $aLevel + 1);
		$this->effet = $this->effet / 100 + 1;
	}

  function debut_round(&$actif, &$passif) {
    //echo 'potentiel_parer: '.$passif['potentiel_parer'];
		$passif['potentiel_parer'] *= $this->effet;
    //echo ' -> '.$passif['potentiel_parer'];
	}
}

/* Précision chirurgicale */
class precision_chirurgicale extends comp_combat
{
  function __construct($aLevel = 1) {
    parent::__construct('posture_critique', $aLevel + 2);
		$this->effet = $this->effet / 100 + 1;
	}

  function calcul_critique(&$actif, &$passif, $chance) {
		//echo "Chance : $chance -> ".($chance * $this->effet);
		return $chance * $this->effet;
	}
}

/* Botte du scorpion */
class botte_scorpion extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_scorpion', $aLevel);
		$this->effet = $this->effet / 100 + 1;
	}

  function calcul_critique(&$actif, &$passif, $chance) {
		if ($this->canUse($actif))
			return $chance * $this->effet;
		else
			return $chance;
	}	
}

/* Botte du crabe */
class botte_crabe extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_crabe', $aLevel);
	}

  function inflige_degats(&$actif, &$passif, $degats) {
		if ($this->canUse($actif)) {
			$this->debug("Chance de desarmer : $test doit être inférieur à ".
									 $this->effet);
			if ($res < $this->effet) {
				$passif['etat']['desarme']['effet'] = true;
				$passif['etat']['desarme']['duree'] = $this->effet2;
				echo "La botte désarme ".$passif['nom'].'<br/>';
			}
		}
	}
}

/* Botte de l'aigle */
class botte_aigle extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_aigle', $aLevel);
		$this->effet = $this->effet / 100 + 1;
	}

  function debut_round(&$actif, &$passif) {
		if ($this->canUse($actif))
			$actif['potentiel_toucher'] *= $this->effect;
	}
}

/* Botte du chat */
class botte_chat extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('critique', 'botte_chat', $aLevel);
		$this->effet = $this->effet / 100 + 1;
	}

  function debut_round(&$actif, &$passif) {
		if ($this->canUse($actif))
			$passif['potentiel_parer'] *= $this->effet;
	}
}

/* Botte du tigre */
class botte_tigre extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('critique', 'botte_tigre', $aLevel);
	}

  function calcul_degats(&$actif, &$passif, $degats) {
		if ($this->canUse($actif)) {
			$this->debug("Chance de multiplier : $test doit être inférieur à "
									 .$this->effet);
			if ($test < $this->effet)
				return $degats * $this->effet2;
		}
		return $degats;
	}
}

/* Botte de l'ours */
class botte_ours extends botte
{
  function __construct($aLevel = 1) {
    parent::__construct('critique', 'botte_ours', $aLevel);
	}
		
	function etourdit($actif, $passif) {
		global $debugs;
	  $pot_et = $actif['force'];
		$pot_res = $passif['vie'] + $passif['PP_effective'] / 100;
		switch ($this->level) {
		case 1:
			$pot_et += $actif['esquive'] / 80;
			break;
		case 2:
			$pot_et += $actif['esquive'] / 70;
			break;
		case 3:
			$pot_et += $actif['esquive'] / 60;
			break;
		default:
			die('bad level');
		}
		$et = rand(0, $pot_et);
		$res = rand(0, $pot_res);
		echo "<div class=\"debug\" id=\"debug${debugs}\">Potentiel étourdir : $pot_et<br/>".
			"Potentiel résister : $pot_res<br/>Chance d'étourdir : ".
			"$et doit être supérieur à $res<br /></div>";
		$debug++;
		return $et >= $res;
	}

  function inflige_degats(&$actif, &$passif, $degats) {
		if ($this->canUse($actif)) {
			if ($this->etourdit($actif, $passif)) {
				$passif['etat']['etourdit']['effet'] = true;
				$passif['etat']['etourdit']['duree'] = 1;
				echo "La botte étourdit ".$passif['nom'].'<br/>';
			}
		}
	}
}

class fleche_poison extends comp_combat {
	var $poison;

  function __construct($aLevel = 1) {
    parent::__construct('fleche_poison', $aLevel);
		if ($this->duree < 1) 
			$this->duree = 1;
		$this->poison = false;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur) {
		if (array_key_exists('fleche_poison', $actif['etat']))
			$effects[] = new fleche_poison($actif['etat']['fleche_poison']['level']);
	}

  function calcul_arme(&$actif, &$passif, $arme) {
		return $arme + $this->effet;
	}

  function inflige_degats(&$actif, &$passif, $degats) {
		$this->poison = true;
		return $degats;
	}

  function fin_round(&$actif, &$passif) {
		if ($this->poison) {
			$passif['etat']['empoisonne']['effet'] = $this->effet2;
			$passif['etat']['empoisonne']['duree'] = $this->duree;
		}
	}
}

class magnetique extends effect {
	var $nb;
	var $chance;
	var $titre;
	var $hit;

	function __construct($aName, $aNb) {
		parent::__construct($aName);
		$this->nb = $aNb;
		$this->chance = 15;
		$this->titre = 'L\'effet magnétique';
		$this->hit = false;
	}

	function calcul_degats(&$actif, &$passif, $degats) {
		$this->hit = true;
		return $degats;
	}
	function fin_round(&$actif, &$passif) { 
		if ($this->hit)
			$this->magnetise($actif, $passif);
	}

	function magnetise(&$actif, &$passif) {
		//chance de débuffer
		$rand = rand(0, 100);
		//Le débuff marche
		if($rand <= $this->chance) {
			$nb_buff_suppr = rand(1, $this->nb);
			//echo $nb_buff_suppr.'<br />';
			for($i = 0; $i < $nb_buff_suppr; $i++) {
				// BD: on doit ne prendre que les vrais
				$keys = array();
				foreach ($passif['buff'] as $nbuff => $buff) {
					if (isset($buff['id'])) {
						// Voir si on peut enlever un debuff
						//if ($buff['debuff'] == 1) continue;
						$keys[] = $nbuff;
					}
				}
				$count = count($keys);
				//echo $count.'<br />';
				if($count > 0) {
					$rand = rand(0, ($count - 1));
					if ($passif['buff'][$keys[$rand]]['id'] == '') {
						// Ne doit pas arriver, mais arrive parfois
						error_log('On ne va pas reussir a supprimer le buff');
						error_log('Buffs: '.print_r($passif['buff'], true));
						error_log('Rand: '.$rand);
						error_log('Actif: '.print_r($actif, true));
						error_log('Passif: '.print_r($passif, true));
						$this->notice($this->titre.' aurait du supprimer un buff, '
													.'mais une erreur est survenue. Pr&eacute;venez un '
													.'administrateur. (Irulan si possible)');
					}
					else {
						global $db;
						$this->message($this->titre.' supprime le buff '.
													 $passif['buff'][$keys[$rand]]['nom']);
						$requete = "DELETE FROM buff WHERE id = ".
							$passif['buff'][$keys[$rand]]['id'];
						$db->query($requete);
						unset($passif['buff'][$keys[$rand]]);
					}
				}
				else {
					$this->message($this->titre.' ne supprime pas de buff');
				}
			}
		}
		else {
			$this->message($this->titre.' ne supprime pas de buff');
		}
	}

}

class globe_foudre extends magnetique {
	function __construct($aNb, $achance, $aHit = false) {
		parent::__construct('globe_foudre', $aNb);
    $this->titre = 'Le globe de foudre';
		$this->hit = $aHit;
		//$this->message("Construction de ".$this->titre);
  }
}

class fleche_magnetique extends magnetique {
	static function factory(&$effects, &$actif, &$passif, $acteur) {
		if (array_key_exists('fleche_magnetique', $passif['etat']))
			$effects[] = new fleche_magnetique($actif['etat']['fleche_magnetique']
																				 ['effet'],
																				 $actif['etat']['fleche_magnetique']
																				 ['effet2']);
	}

	function __construct($aNb, $achance) {
		parent::__construct('fleche_magnetique', $aNb);
		$this->titre = 'La flèche magnétique';
	}
}

class empoisonne extends effect {
	var $vigueur;

  function __construct($aVigueur) {
    parent::__construct('poison');
		$this->vigueur = $aVigueur;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur) {
		if (array_key_exists('empoisonne', $actif['etat']))
			$effects[] = new empoisonne($actif['etat']['empoisonne']['effet']);
	}

  function fin_round(&$actif, &$passif) {
		$this->hit($actif['nom'].' perd '.$this->vigueur.' à cause du poison');
		$actif['hp'] -= $this->vigueur;
		$actif['etat']['empoisonne']['duree'] -= 1;
		$actif['etat']['empoisonne']['effet'] -= 1;
		if ($actif['etat']['empoisonne']['duree'] < 1)
			unset($actif['etat']['empoisonne']);
	}
}

?>