<?php //  -*- tab-width:2; mode: php;  -*-
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * @file competence.class.php
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
    // ce code ne devrait plus être utilisé à priori
    /*global $Gtrad;
    global $db;
    $augmentation = augmentation_competence($this->nom, $acteur, $diff);
    if ($augmentation[1] == true) {
      global $ups;
      if ($this->print_up)
        echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.
          $augmentation[0].' en '.$Gtrad[$this->nom].'</span><br />';
      $requete = "UPDATE comp_perso SET valeur = ".$augmentation[0].
	" WHERE id_perso = ".$acteur->get_id().
	" AND competence = '".$this->nom."'";
      //echo "$requete<br/>";
      $db->query($requete);
      return true;
    }*/
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
class comp_comb extends active_effect
{
  function __construct($aNom, $aLevel) {
    parent::__construct($aNom, 'comp_combat', $aLevel);
  }  

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    $etats_actif = array('vol_a_la_tire');
    $etats_passif = array();
    foreach ($etats_actif as $e) {
      if (array_key_exists($e, $actif->etat)) {
        $et = $actif->etat[$e];
        $effects[] = new $e($et['effet'], $et['effet2'], $et['duree']);
      }
    }
    foreach ($etats_passif as $e) {
      if (array_key_exists($e, $passif->etat)) {
        $et = $passif->etat[$e];
        $effects[] = new $e($et['effet'], $et['effet2'], $et['duree']);
      }
    }
  }

}


/**
 * botte : competence de combat avec une condition
 */
class botte extends effect
{
	protected $effet;
	protected $effet2;

  function __construct($effet, $effet2=0, $duree=1) {
    parent::__construct('');
		$this->effet = $effet;
		$this->effet2 = $effet2;
	}

	function peut_agir(&$actif, $cond) {
    /*if (isset($passif['enchantement']['evasion'])) {
      $chance = $passif['enchantement']['evasion']['effet'];
      $de = rand(1, 100);
      $this->debug("Evasion: $de doit être inférieur à $chance");
      if ($de <= $chance) {
        $this->message($passif->get_nom().'esquive totalement la botte');
        return false;
      }
    }*/
		if( isset($actif->precedent[$cond]) && $actif->precedent[$cond] )
		{
      $this->debug('La botte agit !');
      return true;
    }
		else
		{
      $this->debug("La botte n'agit pas.");
      return false;
    }
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
  }

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if ($passif->bouclier() AND $passif->is_competence('maitrise_bouclier')) $effects[] = new maitrise_bouclier($acteur != 'attaquant');
	}

  function calcul_bloquage(/*&$actif, &$passif*/&$attaque) {
    $passif = $attaque->get_passif();
    $this->used = true;
    $passif->set_potentiel_bloquer( $passif->get_potentiel_bloquer() *
      1 + ($passif->get_competence2('maitrise_bouclier')->get_valeur() / 1000) );
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_passif(), 6);
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

  function debut_round(/*&$actif, &$passif*/&$attaque) {
    $actif = $attaque->get_actif();
    $this->used = true;
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() *
      (1 + ($actif->get_competence2('maitrise_dague')->get_valeur() / 1000)));
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_actif(), 6);
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

  function debut_round(/*&$actif, &$passif*/&$attaque) {
    $actif = $attaque->get_actif();
    $this->used = true;
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() *
      (1 + ($actif->get_competence2('maitrise_epee')->get_valeur() / 1000)));
    //$actif['maitrise_epee'] = $actif['competences']['maitrise_epee'];
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_actif(), 6);
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

  function debut_round(/*&$actif, &$passif*/&$attaque) {
    $actif = $attaque->get_actif();
    $this->used = true;
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() *
      (1 + ($actif->get_competence2('maitrise_hache')->get_valeur() / 1000)));
    //$actif['maitrise_hache'] = $actif['competences']['maitrise_hache'];
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_actif(), 6);
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

  function debut_round(/*&$actif, &$passif*/&$attaque) {
    $actif = $attaque->get_actif();
    $this->used = true;
    $actif->set_potentiel_toucher($actif->get_potentiel_toucher() *
      (1 + ($actif->get_competence2('maitrise_arc')->get_valeur() / 1000)));
    //$actif['maitrise_arc'] = $actif['competences']['maitrise_arc'];
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_actif(), 6);
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

  function calcul_critique(/*&$actif, &$passif, $chance_critique*/&$attaque) {
    $actif = $attaque->get_actif();
    $this->used = true;
    $this->valeur *=
      1 + ($actif->get_competence2('maitrise_critique')->get_valeur() / 1000);
    //$actif['maitrise_critique'] = $actif['competences']['maitrise_critique'];
    //return $chance_critique;
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->used) {
      $this->test_montee($attaque->get_actif(), 6);
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

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    if ($this->critique) {
      $this->test_montee($attaque->get_actif(), 3.5);
    }
  }

  function calcul_mult_critique(/*&$actif, &$passif, $mult*/&$attaque) {
    $this->critique = true;
    //return $mult + $attaque->get_actif()->get_competence2('art_critique')->get_valeur();
    $this->valeur += $attaque->get_actif()->get_competence2('art_critique')->get_valeur();
  }
}

/* Préparation */
class preparation extends comp_comb
{
  function __construct($aLevel = 1) {
    parent::__construct('posture_esquive', $aLevel + 1);
		$this->effet = $this->effet / 100 + 1;
	}

  function debut_round(/*&$actif, &$passif*/&$attaque) {
		$attaque->get_passif()->potentiel_parer *= $this->effet;
	}
}

/* Précision chirurgicale */
class precision_chirurgicale extends comp_comb
{
  function __construct($aLevel = 1) {
    parent::__construct('posture_critique', $aLevel + 2);
		$this->effet = $this->effet / 100 + 1;
	}

  function calcul_critique(/*&$actif, &$passif, $chance*/&$attaque) {
		//return $chance * $this->effet;
    $this->valeur *=$this->effet;
	}
}

/* Botte du scorpion */
class botte_scorpion extends botte
{
  /*function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_scorpion', $aLevel);
		$this->effet = $this->effet / 100 + 1;
	}*/

  function calcul_critique(/*&$actif, &$passif, $chance*/&$attaque) {
		if ($this->peut_agir($actif, 'esquive'))
			$this->valeur *= (1 + $this->effet/100);
		/*else
			return $chance;*/
	}	
}

/* Botte du crabe */
class botte_crabe extends botte
{
  /*function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_crabe', $aLevel);
	}*/

  function inflige_degats(/*&$actif, &$passif, $degats*/&$attaque) {
    $actif = $attaque->get_actif();
		if ($this->peut_agir($actif, 'esquive')) {
			if ( comp_sort::test_de(100, $this->effet) ) {
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
  /*function __construct($aLevel = 1) {
    parent::__construct('esquive', 'botte_aigle', $aLevel);
		$this->effet = $this->effet / 100 + 1;
	}*/

  function debut_round(/*&$actif, &$passif*/&$attaque) {
    $actif = $attaque->get_actif();
		if ($this->peut_agir($actif, 'esquive'))
			$actif->set_potentiel_toucher( $actif->get_potentiel_toucher() * (1 + $this->effet/100) );
	}
}

/* Botte du chat */
class botte_chat extends botte
{
  /*function __construct($effet, $effet2, $duree=1) {
    parent::__construct('critique', $effet, $effet2, $duree);
	}*/

  function debut_round(/*&$actif, &$passif*/&$attaque)  {
    $actif = $attaque->get_actif();
		if ($this->peut_agir($actif, 'critique')) {
			$actif->etat['botte_chat']['effet'] = $this->effet;
			$actif->etat['botte_chat']['duree'] = 2; // dure 1 round mais le compteur sera décrémenté avant utilisation
		}
	}
}

//Botte du chien
class botte_chien extends botte
{

  function debut_round(/*&$actif, &$passif*/&$attaque)  {
    $actif = $attaque->get_actif();
		if ($this->peut_agir($actif, 'critique')) {
			$actif->etat['botte_blocage']['effet'] = $this->effet;
			$actif->etat['botte_blocage']['duree'] = 2; // dure 1 round mais le compteur sera décrémenté avant utilisation
		}
	}
}
// Botte du scolopendre
class botte_scolopendre extends botte
{
  function calcul_critique(/*&$actif, &$passif, $chance*/&$attaque)
  {
		if( $this->peut_agir($actif, 'bloque') )
			$this->valeur *= (1 + $this->effet/100);
		//return $chance;
	}
}

// Botte de la tortue
class botte_tortue extends botte
{
  function debut_round(/*&$actif, &$passif*/&$attaque)
  {
    $actif = $attaque->get_actif();
		if( $this->peut_agir($actif, 'bloque') )
			$actif->set_potentiel_toucher( $actif->get_potentiel_toucher() * (1 + $this->effet/100) );
  }
}

// Botte du rhinocéros
class botte_rhinoceros extends botte
{
  function debut_round(/*&$actif, &$passif*/&$attaque)
  {
    $actif = $attaque->get_actif();
		if( $this->peut_agir($actif, 'touche') )
		{
			$actif->etat['botte_blocage']['effet'] = $this->effet;
			$actif->etat['botte_blocage']['duree'] = 2; // dure 1 round mais le compteur sera décrémenté avant utilisation
    }
  }
}

// Botte du tigre
class botte_tigre extends botte
{
  function debut_round(/*&$actif, &$passif*/&$attaque)
  {
    $actif = $attaque->get_actif();
		if( $this->peut_agir($actif, 'touche') )
			$actif->set_potentiel_toucher( $actif->get_potentiel_toucher() * (1 + $this->effet/100) );
  }
}

// Botte de l'ours
class botte_ours extends botte
{
  function calcul_degats(/*&$actif, &$passif, $degats*/&$attaque)
  {
		if( $this->peut_agir($actif, 'touche') )
			//return $degats + $this->effet;
      $attaque->add_degats($this->effet);
		//return $degats;
	}
}

class fleche_poison extends comp_comb {
	var $poison;

  function __construct($aEffet, $aEffet2, $aDuree = 5) {
    parent::__construct('Fleche empoisonnée', null);
    $this->duree = $aDuree;
    $this->effet = $aEffet;
    $this->effet2 = $aEffet2;
		$this->poison = false;
	}

  function calcul_arme(/*&$actif, &$passif, $arme*/&$attaque) {
    $this->debug("boost du facteur d'arme - fleche empoisonnee: $arme -> ".
                 ($arme + $this->effet));
		//return $arme + $this->effet;
    $this->valeur += $this->effet;
	}

  function inflige_degats(/*&$actif, &$passif, $degats*/&$attaque) {
    $pot_att = $attaque->get_actif()->get_force() + $this->effet;
    $pot_def = $attaque->get_passif()->get_volonte();
    $de_att = rand(0, $pot_att);
    $de_deff = rand(0, $pot_def);
    $this->debug("Poison: dé de $pot_att doit être supérieur à dé de $pot_def");
    $this->debug("$de_att / $de_deff");
    if ($de_att > $de_deff) {
      $this->poison = true;
    } else {
      $this->notice("Le poison n'agit pas");
    }
	  //return $degats;
	}

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    $passif = $attaque->get_passif();
		if ($this->poison) {
      $this->hit('<strong>'.$passif->get_nom().'</strong> est empoisonné pour '.
                 $this->duree.' tours !');
			$passif->etat['empoisonne']['effet'] = $this->effet2;
			$passif->etat['empoisonne']['duree'] = $this->duree;
		}
	}
}

abstract class magnetique extends effect {
	//var $nb;
	//var $chance;
	var $titre;
	var $hit;

	function __construct($aName/*, $aNb*/) {
		parent::__construct($aName);
		//$this->nb = $aNb;
		$this->titre = 'L\'effet magnétique';
		$this->hit = false;
	}

	function inflige_degats(/*&$actif, &$passif, $degats*/&$attaque) {
		$this->hit = true;
		//return $degats;
	}

	function fin_round(/*&$actif, &$passif*/&$attaque) {
		if ($this->hit)
			$this->magnetise($actif, $passif);
	}

  /// Test si l'effet agit ou non
  abstract function test_chance(&$passif);
  /// Supprime un buff ou un niveau de buff
  abstract function suppr_buff($buff, &$passif);

	function magnetise(/*&$actif, &$passif*/&$attaque)
	{
    $passif = $attaque->get_passif();
		//chance de débuffer
		//$rand = rand(0, 100);
		//Le débuff marche
		//$this->debug($this->chance.'/ 100 => '.$rand);
		if(/*$rand <= $this->chance*/ $this->test_chance($passif) )
		{
			//$nb_buff_suppr = rand(1, $this->nb);
			//echo $nb_buff_suppr.'<br />';
			$passif_buff = $passif->get_buff();
			/*for($i = 0; $i < $nb_buff_suppr; $i++)
			{*/
				// BD: on doit ne prendre que les vrais
				$keys = array();
				foreach ($passif_buff as $nbuff => $buff)
				{
					if ($buff->get_debuff() == 1) continue;
					if ($buff->get_id() == '') continue;
					if (!$buff->get_supprimable()) continue;
					$keys[] = $nbuff;
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
						error_log('Buffs: '.print_r($passif->get_buff(), true));
						error_log('Rand: '.$rand);
						error_log('Actif: '.print_r($attaque->get_actif(), true));
						error_log('Passif: '.print_r($passif, true));
						$this->notice($this->titre.' aurait du supprimer un buff, '
													.'mais une erreur est survenue. Pr&eacute;venez un '
													.'administrateur. (Irulan si possible)');
					}
					else
					{
						/*global $db;
						$this->message($this->titre.' supprime le buff '.$passif_buff[$keys[$rand]]->get_nom());
						$passif->supprime_buff($keys[$rand]);
						$passif_buff[$keys[$rand]]->supprimer();
						unset($passif_buff[$keys[$rand]]);*/
						$this->suppr_buff($passif_buff[$keys[$rand]], $passif);
					}
				}
				else {
					//$this->message($this->titre.' ne supprime pas de buff - plus de buff');
					$this->message($this->titre.' n\'a pas de buff sur lequel agir');
				}
			//}
			return true;
		}
		else
		{
			//$this->message($this->titre.' ne supprime pas de buff');
			$this->message($this->titre.' n\'agit pas');
			return false;
		}
	}

}

class globe_foudre extends magnetique {
	private $chance;
	function __construct($achance, $aHit = false) {
		parent::__construct('globe_foudre');
    $this->titre = 'Le globe de foudre';
		$this->hit = $aHit;
		$this->chance = $achance;
		//$this->message("Construction de ".$this->titre);
  }
  /// Test si l'effet agit ou non
  function test_chance(&$passif)
  {
    $rand = rand(0, 100);
    $this->debug($this->chance.'/ 100 => '.$rand);
    return $rand <= $this->chance;
  }
  /// Supprime un buff
  function suppr_buff($buff, &$passif)
  {
    $this->message($this->titre.' supprime le buff '.$buff->get_nom());
    $passif->supprime_buff( $buff->get_type() );
		$buff->supprimer();
  }
}

class fleche_magnetique extends magnetique {
  private $potentiel; ///< potentiel de réussite
  private $nbr_max;  ///< nombre maximum de niveaux retités
	function __construct($nbr_max, $potentiel) {
		parent::__construct('fleche_magnetique'/*, $aNb*/);
		$this->titre = 'La flèche magnétique';
		$this->potentiel = $potentiel;
		$this->nbr_max = $nbr_max;
	}
  /// Test si l'effet agit ou non
  function test_chance(&$passif)
  {
    $de_att = rand(0, $this->potentiel);
    $de_deff = rand(0, $passif->get_vie());
    return $de_att >= $de_deff;
  }
  /// Supprime un niveau buff
  function suppr_buff($buff, &$passif)
  {
    //$buffs = sort_jeu::create('type', $buff->get_type(), 'incantation DESC');
    $buffs = sort_jeu::create('nom', $buff->get_nom());
    if( !$buffs )
    {
      //$buffs = comp_jeu::create('type', $buff->get_type(), 'comp_requis DESC');
      $buffs = comp_jeu::create('nom', $buff->get_nom());
    }
    if( !$buffs )
    {
      $this->notice($this->titre.' aurait dû agir mais le buff '.$buff->get_nom().' n\'est pas reconnu. Pr&eacute;venez un administrateur.');
      return;
    }

    /*$nouv = false;
    $nbr = $nbr_red = rand(1, $this->nbr_max);
    foreach($buffs as $b)
    {
      if( $nouv )
      {
        $nbr--;
        if( $nbr == 0 )
        {
          if( substr($b->get_nom(), 0, 11) != '(Personnel)' )
            $nouv = $b;
          break;
        }
      }
      else if( $b->get_nom() == $buff->get_nom() )
        $nouv = true;
    }*/
    $nouv = $buffs[0]->get_obj_requis();
    $nbr = $nbr_red = rand(1, $this->nbr_max);
    while( $nouv && $nbr )
    {
      $nouv = $nouv->get_obj_requis();
      $nbr--;
    }

    /*if( $nouv === false )
    {
      $this->notice($this->titre.' aurait dû agir mais le buff '.$buff->get_nom(). 'n\'a pu être retrouvé. Pr&eacute;venez un administrateur.');
      return;
    }
    else if( $nouv === true )*/
    if( $nouv )
    {
      $this->message($this->titre.' réduit le buff '.$buff->get_nom().' de '.$nbr_red.' niveau'.($nbr_red>1?'x.':'.'));
      $buff->set_nom( $nouv->get_nom() );
      $buff->set_effet( $nouv->get_effet() );
      $buff->set_effet2( $nouv->get_effet2() );
      $buff->set_description( $nouv->get_description() );
      $buff->sauver();
    }
    else
    {
      $this->message($this->titre.' supprime le buff '.$buff->get_nom());
      $passif->supprime_buff( $buff->get_type() );
		  $buff->supprimer();
    }
  }
}

class fleche_sable extends comp_comb {

  function __construct($aEffet, $aEffet2, $aDuree) {
    parent::__construct('Fleche de sable', null);
    $this->duree = $aDuree;
    $this->effet = $aEffet;
    $this->effet2 = $aEffet2;
	}

	function inflige_degats(/*&$actif, &$passif, $degats*/&$attaque) {
    $passif = $attaque->get_passif();
    $passif->etat['fleche_sable']['effet'] = $this->effet;
    $passif->etat['fleche_sable']['duree'] = $this->duree;
    $this->notice($passif->get_nom().' est ensablé pour '.$this->duree.' rounds');
		//return $degats - $this->effet;
	}
  
}

/**
 * Bouclier protecteur
 */
class bouclier_protecteur extends etat {

  function __construct($aEffet) {
    parent::__construct($aEffet, 'Bouclier protecteur');
    $this->order = effect::$FIN_ADD;
	}

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
		if (array_key_exists('bouclier_protecteur', $passif->etat)) {
			$effects[] = new bouclier_protecteur($passif->etat['bouclier_protecteur']['effet']);
		}
	}
	
	function calcul_pm(/*&$actif, &$passif, $pm*/&$attaque) {
    $passif = $attaque->get_passif();
    $bloque = $passif->bouclier()->degat;
		if($passif->is_buff('bouclier_terre'))
      $bloque += $passif->get_buff('bouclier_terre', 'effet');
		$pluspm = $this->effet * $bloque;
		$this->debug($this->nom.' augmente la PM de '.$pluspm);
		//return $pm + $pluspm;
    $this->valeur += $pluspm;
	}
}

class vol_a_la_tire extends comp_comb {

  function __construct($aEffet, $aEffet2, $aDuree) {
    parent::__construct('Vol à la tire', null);
    $this->duree = $aDuree;
    $this->effet = $aEffet;
    $this->effet2 = $aEffet2;
  }

  function fin_round(/*&$actif, &$passif*/&$attaque) {
    $vol = rand(1, $this->effet2);
    $this->notice($attaque->get_actif()->get_nom().' vole '.$vol.' stars !');
    $obj = $attaque->get_passif()->get_objet();
    $obj->add_star($vol * -1);
    $obj->sauver();
    return $degats;
  }

}

?>
