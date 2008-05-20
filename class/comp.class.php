<?php //  -*- tab-width:2; intent-tabs-mode:nil; buffer-file-coding-system: latin-1  -*-

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

  /* Méthodes à dériver selon les besoins */
  function debut_round(&$actif, &$passif) { }
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

  function __construct($aNom, $aTable, $aLevel) {
    parent::__construct($aNom, $aPrintUp);
    $this->table = $aTable;
    $this->level = $aLevel;
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

  function calcul_bloquage(&$actif, &$passif) {
    $this->used = true;
    //echo 'potentiel_bloquer: '.$passif['potentiel_bloquer'];
    $passif['potentiel_bloquer'] *= 1 + ($passif['competences']['maitrise_bouclier'] / 1000);
    //echo ' -> '.$passif['potentiel_bloquer'].'<br />';
    $passif['maitrise_bouclier'] = $passif['competences']['maitrise_bouclier'];
  }

  function fin_round(&$actif, &$passif) {
    if ($this->used) {
      //echo 'montée de maitrise_bouclier <br/>';
      $this->test_montee($passif, 6);
      //$this->test_montee($passif, 1);
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



?>