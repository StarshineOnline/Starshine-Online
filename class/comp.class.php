<?php //  -*- tab-width:2; intent-tabs-mode:nil;  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * @file comp.class.php
 */

  include_once(root.'class/effect.class.php');

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

  function test_montee(&$acteur, $diff)
  {
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
    if ($aTable != null) {
      global $db;
      $this->table = $aTable;
      $this->level = $aLevel;
      $sNom = $aNom;
      $requete = "select * from $aTable where type = '$aNom'";
      if ($aLevel != null) $requete .= " and level = '$aLevel'";
      //echo "<small>$requete</small><br />";
      $req = $db->query($requete);
      if ($db->num_rows < 0) throw new Exception("Cannot find $sNom");
      $row = $db->read_assoc($req);
      if (isset($row['effet'])) $this->effet = $row['effet'];
      if (isset($row['effet2'])) $this->effet2 = $row['effet2'];
      if (isset($row['effet3'])) $this->effet3 = $row['effet3'];
      if (isset($row['duree'])) $this->duree = $row['duree'];
      if (isset($row['arme_requis'])) $this->requis = $row['arme_requis'];
    }
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

	function canUse(&$actif, &$passif) {
    if (isset($passif['enchantement']['evasion'])) {
      $chance = $passif['enchantement']['evasion']['effet'];
      $de = rand(1, 100);
      $this->debug("Evasion: $de doit être inférieur à $chance");
      if ($de <= $chance) {
        $this->message($passif->get_nom().'esquive totalement la botte');
        return false;
      }
    }
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

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if ($passif->bouclier AND $passif->is_competence('maitrise_bouclier')) $effects[] = new maitrise_bouclier($acteur != 'attaquant');
	}

  function calcul_bloquage(&$actif, &$passif) {
    $this->used = true;
    $passif->potentiel_bloquer *=
      1 + ($passif->get_competence('maitrise_bouclier')->get_valeur() / 1000);
    //$passif['maitrise_bouclier'] = $passif['competences']['maitrise_bouclier'];
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

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    	if($actif->get_arme_type() == 'dague' AND $actif->is_competence('maitrise_dague'))
		$effects[] = new maitrise_dague($acteur == 'attaquant');      
	}

  function debut_round(&$actif, &$passif) {
    $this->used = true;
    $actif->potentiel_toucher *=
      1 + ($actif->get_competence('maitrise_dague')->get_valeur() / 1000);
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($actif, 6);
    }
  }
}

/* Maitrise des epees */
class maitrise_epee extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_epee', $aPrintUp);
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    if($actif->get_arme_type() == 'epee' AND $actif->is_competence('maitrise_epee'))
			$effects[] = new maitrise_epee($acteur == 'attaquant');      
	}

  function debut_round(&$actif, &$passif) {
    $this->used = true;
    $actif->potentiel_toucher *=
      1 + ($actif->get_competence('maitrise_epee')->get_valeur() / 1000);
    //$actif['maitrise_epee'] = $actif['competences']['maitrise_epee'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($actif, 6);
    }
  }
}

/* Maitrise des haches */
class maitrise_hache extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_hache', $aPrintUp);
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    if($actif->get_arme_type() == 'hache' AND $actif->is_competence('maitrise_hache'))
			$effects[] = new maitrise_hache($acteur == 'attaquant');      
	}

  function debut_round(&$actif, &$passif) {
    $this->used = true;
    $actif->potentiel_toucher *=
			1 + ($actif->get_competence('maitrise_hache')->get_valeur() / 1000);
    //$actif['maitrise_hache'] = $actif['competences']['maitrise_hache'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($actif, 6);
    }
  }
}

/* Maitrise des arcs */
class maitrise_arc extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_arc', $aPrintUp);
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    if($actif->get_arme_type() == 'arc' AND $actif->is_competence('maitrise_arc'))
			$effects[] = new maitrise_arc($acteur == 'attaquant');      
	}

  function debut_round(&$actif, &$passif) {
    $this->used = true;
    $actif->potentiel_toucher *=
      1 + ($actif->get_competence('maitrise_arc')->get_valeur() / 1000);
    //$actif['maitrise_arc'] = $actif['competences']['maitrise_arc'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($actif, 6);
    }
  }
}

/* Maitrise du critique */
class maitrise_critique extends competence
{
  function __construct($aPrintUp = false) {
    parent::__construct('maitrise_critique', $aPrintUp);
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    if($actif->is_competence('maitrise_critique'))
			$effects[] = new maitrise_critique($acteur == 'attaquant');      
	}

  function calcul_critique(&$actif, &$passif, $chance_critique) {
    $this->used = true;
    $chance_critique *=
      1 + ($actif->get_competence('maitrise_critique')->get_valeur() / 1000);
    //$actif['maitrise_critique'] = $actif['competences']['maitrise_critique'];
    return $chance_critique;
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      $this->test_montee($actif, 6);
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
    return $mult + $actif->get_competence('art_critique')->get_valeur();
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
		$passif->potentiel_parer *= $this->effet;
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
				$passif->etat['desarme']['effet'] = true;
				$passif->etat['desarme']['duree'] = $this->effet2;
				echo "La botte désarme ".$passif->get_nom().'<br/>';
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
			$actif->potentiel_toucher *= $this->effect;
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
			$passif->potentiel_parer *= $this->effet;
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
	  $pot_et = $actif->get_force();
		$pot_res = $passif->get_vie() + $passif['PP_effective'] / 100;
		switch ($this->level) {
		case 1:
			$pot_et += $actif->get_esquive() / 80;
			break;
		case 2:
			$pot_et += $actif->get_esquive() / 70;
			break;
		case 3:
			$pot_et += $actif->get_esquive() / 60;
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
				$passif->etat['etourdit']['effet'] = true;
				$passif->etat['etourdit']['duree'] = 1;
				echo "La botte étourdit ".$passif->get_nom().'<br/>';
			}
		}
	}
}

class fleche_poison extends comp_combat {
	var $poison;

  function __construct($aEffet, $aEffet2, $aDuree = 5) {
    parent::__construct('Fleche empoisonnée', null);
    $this->duree = $aDuree;
    $this->effet = $aEffet;
    $this->effet2 = $aEffet2;
		$this->poison = false;
	}

  function calcul_arme(&$actif, &$passif, $arme) {
		return $arme + $this->effet;
	}

  function inflige_degats(&$actif, &$passif, $degats) {
    $pot_att = $actif->get_force() + $this->effet;
    $pot_def = $passif->get_volonte();
    $de_att = rand(0, $pot_att);
    $de_deff = rand(0, $pot_def);
    $this->debug("Poison: dé de $pot_att doi être supérieur à dé de $pot_def");
    $this->debug("$de_att / $de_def");
    if ($de_att > $de_deff) {
      $this->poison = true;
    } else {
      $this->notice("Le poison n'agit pas");
    }
	}

  function fin_round(&$actif, &$passif) {
		if ($this->poison) {
      $this->hit('<strong>'.$passif->get_nom().'</strong> est empoisonné pour '.
                 $this->duree.' tours !');
			$passif->etat['empoisonne']['effet'] = $this->effet2;
			$passif->etat['empoisonne']['duree'] = $this->duree;
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

	function inflige_degats(&$actif, &$passif, $degats) {
		$this->hit = true;
		return $degats;
	}

	function fin_round(&$actif, &$passif) { 
		if ($this->hit)
			$this->magnetise($actif, $passif);
	}

	function magnetise(&$actif, &$passif)
	{
		//chance de débuffer
		$rand = rand(0, 100);
		//Le débuff marche
		$this->message($this->chance.'/ 100 => '.$rand);
		if($rand <= $this->chance)
		{
			$nb_buff_suppr = rand(1, $this->nb);
			//echo $nb_buff_suppr.'<br />';
			$passif_buff = $passif->get_buff();
			for($i = 0; $i < $nb_buff_suppr; $i++)
			{
				// BD: on doit ne prendre que les vrais
				$keys = array();
				foreach ($passif_buff as $nbuff => $buff)
				{
					//Pourquoi ce if ?
					/*if (isset($buff['id']))
					{*/
						// Voir si on peut enlever un debuff
						if ($buff->get_debuff() == 1) continue;
						$keys[] = $nbuff;
					//}
				}
				$count = count($keys);
				//echo $count.'<br />';
				if($count > 0)
				{
					$rand = rand(0, ($count - 1));
					if ($passif_buff[$keys[$rand]]->get_id() == '')
					{
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
					else
					{
						global $db;
						$this->message($this->titre.' supprime le buff '.$passif_buff[$keys[$rand]]->get_nom());
						$passif->supprime_buff($keys[$rand]);
						$passif_buff[$keys[$rand]]->supprimer();
						unset($passif_buff[$keys[$rand]]);
					}
				}
				else {
					$this->message($this->titre.' ne supprime pas de buff - plus de buff');
				}
			}
			return true;
		}
		else
		{
			$this->message($this->titre.' ne supprime pas de buff');
			return false;
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

	function __construct($aNb, $achance) {
		parent::__construct('fleche_magnetique', $aNb);
		$this->titre = 'La flèche magnétique';
		$this->chance = $achance;
	}
}

class fleche_sable extends comp_combat {

  function __construct($aEffet, $aEffet2, $aDuree) {
    parent::__construct('Fleche de sable', null);
    $this->duree = $aDuree;
    $this->effet = $aEffet;
    $this->effet2 = $aEffet2;
	}

	function inflige_degats(&$actif, &$passif, $degats) {
    $passif->etat['fleche_sable']['effet'] = $this->effet2;
    $passif->etat['fleche_sable']['duree'] = $this->duree;
    $this->notice($passif->get_nom().' est ensablé pour '.$this->duree.' rounds');
		return $degats - $this->effet;
	}
  
}

?>