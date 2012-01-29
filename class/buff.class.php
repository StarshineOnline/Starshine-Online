<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php //  -*- tab-width:2  -*-
/**
* @file buff.class.php
*/

include_once(root.'class/effect.class.php');

class buff_actif extends effect
{

	private static $esquive_buff = array('buff_evasion', 'buff_cri_detresse', 'batiment_esquive', 'virtuose_sexe');
	private static $esquive_magique_buff = array('virtuose_sexe');

	static function factory(&$effects, &$actif, &$passif, $acteur = '') {
    $acti = array();
		$actives = array_merge($acti);
		$pass = array();
    $passives = array_merge($pass, self::$esquive_buff);
    $passives = array_merge($pass, self::$esquive_magique_buff);
    foreach ($actif->get_buff() as $type => $buff) {
      if (in_array($type, $actives)) {
				$effects[] = new buff_actif($type, $buff, 'actif');
      }
    }
    foreach ($passif->get_buff() as $type => $buff) {
      if (in_array($type, $passives)) {
				$effects[] = new buff_actif($type , $buff, 'passif');
      }
    }
	}

	var $type;
	var $buff;
	var $acteur;

  function __construct($type, $buff, $acteur) {
		if (method_exists($buff, 'get_nom'))
			$nom = $buff->get_nom();
		else
			$nom = $type;
    parent::__construct($nom);
		$this->type = $type;
		$this->buff = $buff;
		$this->acteur = $acteur;
    $this->effet = $buff->get_effet();
		//$this->notice("Activation de $type");
	}

  function calcul_defense_physique(&$actif, &$passif) {
		if (in_array($this->type, self::$esquive_buff)) {
			$passif->potentiel_parer *= (1 + ($this->effet / 100));
      $this->debug("augmentation de l'esquive de $this->effet% ($this->type)");
    }
    return $passif->potentiel_parer;
	}

	function calcul_defense_magique(&$actif, &$passif, $def) {
		if (in_array($this->type, self::$esquive_magique_buff)) {
			$def *= (1 + ($this->effet / 100));
      $this->debug("augmentation de l'esquive magique de $this->effet% ($this->type)");
    }
    return $def;
  }
}

class buff extends buff_base
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $id_perso;  ///< id du perso ayant le buff
	
	/// Renvoie l'id du perso ayant le buff
	function get_id_perso()
	{
		return $this->id_perso;
	}
	/// Modifie l'id du perso ayant le buff
	function set_id_perso($id_perso)
	{
		$this->id_perso = $id_perso;
		$this->champs_modif[] = 'id_perso';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id           Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param id_perso     id du perso ayant le buff
   * @param type         Type générique.
	 * @param effet        Effet principal.
	 * @param effet2       Effet secondaire
	 * @param duree        Durée
	 * @param fin          Date et heure de fin du buff
	 * @param nom          Nom du buff
	 * @param description  Description du buff
	 * @param debuff       pour un buff,  pour un debuff
	 * @param supprimable  si on peut supprimer le (de)buff, 0 sinon
	*/
	function __construct($id = 0, $id_perso=0, $type='', $effet=0, $effet2=0, $duree=0, $fin=0, $nom='', $description='', $debuff=0, $supprimable=0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      buff_base::__construct($id, $type, $effet, $duree);
			$this->id_perso = id_perso;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    buff_base::init_tab($vals);
		$this->id_perso = $vals['id_perso'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return buff_base::get_liste_champs().', id_perso';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return buff_base::get_valeurs_insert().', '.$this->id_perso;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return buff_base::get_liste_update().', id_perso = '.$this->id_perso;
	}
	// @}
}

?>
