<?php
/**
 * @file objet.class.php
 * Gestion des gemmes
 */
include_once(root.'class/effect.class.php');

/**
 * Classe gérant les gemmes
 * Correspond à la table du même nom dans la bdd.
 */
class gemme extends objet_invent
{
  protected $niveau;  ///< Niveau de la gemme
  protected $partie;  ///< Partie sur laquelle est portée l'objet
  protected $enchantement_nom;  ///< Nom de l'enchentement
	protected $description;  ///< Description de l'objet.
  protected $enchantement_type;  ///< Type de l'enchentement
  protected $enchantement_effet;  ///< Valeur du premier effet de l'enchentement
  protected $enchantement_effet2;  ///< Valeur du second effet de l'enchentement

	/// Retourne le niveau de la gemme
	function get_niveau()
	{
		return $this->niveau;
	}
	/// Modifie le niveau de la gemme
	function set_niveau($val)
	{
		$this->niveau = $val;
		$this->champs_modif[] = 'niveau';
	}

	/// Retourne la partie sur laquelle est portée l'objet
	function get_partie()
	{
		return $this->partie;
	}
	/// Modifie la partie sur laquelle est portée l'objet
	function set_partie($val)
	{
		$this->partie = $val;
		$this->champs_modif[] = 'partie';
	}

	/// Retourne le nom de l'enchentement
	function get_enchantement_nom()
	{
		return $this->enchantement_nom;
	}
	/// Modifie le nom de l'enchentement
	function set_enchantement_nom($val)
	{
		$this->enchantement_nom = $val;
		$this->champs_modif[] = 'enchantement_nom';
	}

	/// Retourne la description de l'objet
	function get_description()
	{
		return $this->description;
	}
	/// Modifie la description de l'objet
	function set_description($val)
	{
		$this->description = $val;
		$this->champs_modif[] = 'description';
	}

	/// Retourne le type de l'enchentement
	function get_enchantement_type()
	{
		return $this->enchantement_type;
	}
	/// Modifie le type de l'enchentement
	function set_enchantement_type($val)
	{
		$this->enchantement_type = $val;
		$this->champs_modif[] = 'enchantement_type';
	}

	/// Retourne la valeur du premier effet de l'enchentement
	function get_enchantement_effet()
	{
		return $this->enchantement_effet;
	}
	/// Modifie la valeur du premier effet de l'enchentement
	function set_enchantement_effet($val)
	{
		$this->enchantement_effet = $val;
		$this->champs_modif[] = 'enchantement_effet';
	}

	/// Retourne la valeur du second effet de l'enchentement
	function get_enchantement_effet2()
	{
		return $this->enchantement_effet2;
	}
	/// Modifie la valeur du second effet de l'enchentement
	function set_enchantement_effet2($val)
	{
		$this->enchantement_effet2 = $val;
		$this->champs_modif[] = 'enchantement_effet2';
	}

	/**
	 * Constructeur
	 * @param  $nom                  nom de l'objet
	 * @param  $type	               type de l'objet
	 * @param  $prix	               prix de l'objet em magasin
	 * @param  $niveau		           Niveau de la gemme
	 * @param  $partie		           Partie sur laquelle est portée l'objet
	 * @param  $enchantement_nom		 Nom de l'enchentement
	 * @param  $description		       Description de l'objet.
	 * @param  $enchantement_type		 Type de l'enchentement
	 * @param  $enchantement_effet	 Valeur du premier effet de l'enchentement
	 * @param  $enchantement_effet2  Valeur du second effet de l'enchentement
	 */
	function __construct($nom='', $type='', $prix=0, $niveau=1, $partie='', $enchantement_nom='', $description='', $enchantement_type='', $enchantement_effet=0, $enchantement_effet2=0)
	{
		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($nom);
		}
		else
		{
			$this->nom = $nom;
			$this->type = $type;
			$this->prix = $prix;
			$this->niveau = $niveau;
			$this->partie = $partie;
			$this->enchantement_nom = $enchantement_nom;
			$this->description = $description;
			$this->enchantement_type = $enchantement_type;
			$this->enchantement_effet = $enchantement_effet;
			$this->enchantement_effet2 = $enchantement_effet2;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->niveau = $vals['niveau'];
		$this->partie = $vals['partie'];
		$this->enchantement_nom = $vals['enchantement_nom'];
		$this->description = $vals['description'];
		$this->enchantement_type = $vals['enchantement_type'];
		$this->enchantement_effet = $vals['enchantement_effet'];
		$this->enchantement_effet2 = $vals['enchantement_effet2'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['niveau']='i';
    $tbl['partie']='i';
    $tbl['enchantement_nom']='s';
    $tbl['description']='s';
    $tbl['enchantement_type']='s';
    $tbl['enchantement_effet']='i';
    $tbl['enchantement_effet2']='i';
		return $tbl;
	}

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    return array('Type', 'Niveau', 'Description');
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $vals = array($this->type, $this->niveau, $this->description);
    return $vals;
  }

	function get_colone_int($partie)
  {
    if( $partie == 'artisanat' )
      return 2;
    else
      return false;
  }

  /// Indique si l'objet est slotable
  function est_enchassable() { return $this->identifie ? $this->niveau : false; }
}

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
    $actives = array('vampire', 'poison', 'divin');
    $passives = array('bouclier', 'bouclier_epine', 'blocage',
                      'parade', 'evasion', 'divin');

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
			$passif->set_potentiel_bloquer( floor($passif->get_potentiel_bloquer() + $this->enchantement_effet / 100) );
		}
	}

  // Gemme divine
  function calcul_mp(&$actif, $mp) {
		if ($this->enchantement_type == 'divin') {
      $mp -= $this->enchantement_effet;
      if ($mp < 1) { $mp = 1; }
    }
    return $mp;
  }

}

?>
