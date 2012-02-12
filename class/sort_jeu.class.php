<?php
/**
 * @file comp_jeu.class.php
 * Définition de la classe comp_sort servant de base aux sorts hors combat
 */

/**
 * Classe comp_jeu
 * Classe comp_jeu servant de base aux sorts dhors combat
 */
class sort_jeu extends sort
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $pa;   ///< Coût en PA de la compétence
	protected $portee;   ///< Portée du sort
	protected $special;   ///< Indique si c'est un sort spécial (auquel cas l'affinité ne joue pas)

	/// Renvoie le coût de la comptétence
	function get_pa()
	{
		return $this->pa;
	}
	/// Modifie le coût de la comptétence
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

  /// Renvoie la portée du sort
	function get_portee()
	{
		return $this->portee;
	}
	/// Modifie la portée du sort
	function set_portee($portee, $param = 'set')
	{
		$this->portee = $portee;
		$this->champs_modif[] = 'portee';
	}

  /// Renvoie si c'est un sort spécial
	function get_special()
	{
		return $this->special;
	}
	/// Modifie si c'est un sort spécial
	function set_special($special, $param = 'set')
	{
		$this->special = $special;
		$this->champs_modif[] = 'special';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id             Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type           Type générique.
	 * @param effet          Effet principal.
	 * @param duree          Durée
	 * @param comp_assoc     Compétence associée
	 * @param carac_assoc    Caractéristique associée
	 * @param comp_requis    Requis dans la compétence
	 * @param carac_requis   Requis dans la caractéristique (inutilisé)
	 * @param effet2         Deuxième effet
	 * @param requis         Compétence ou sort requis pour apprendre celui-ci
	 * @param cible          Cible de la compétence ou du sort
	 * @param description    Description du buff
	 * @param mp             Coût en MP ou en RM
	 * @param prix           Prix de la compétence ou le sort
	 * @param lvl_batiment   Niveau de l'école qui vent la compétence ou le sort
	 * @param incantation    Requis en incantation
	 * @param difficulte     Difficulté de lancé
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $incantation=0, $difficulte=0, $pa=0, $portee=0, $special=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      sort::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $incantation, $difficulte);
			$this->pa = $pa;
			$this->portee = $portee;
			$this->special = $special;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    sort::init_tab($vals);
		$this->pa = $vals['pa'];
		$this->portee = $vals['portee'];
		$this->special = $vals['special'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return sort::get_liste_champs().', pa, portee, special';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return sort::get_valeurs_insert().', '.$this->pa.', '.$this->portee.', '.$this->special;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return sort::get_liste_update().', pa = '.$this->pa.', portee = '.$this->portee.', special = '.$this->special;
	}
	// @}
}
?>
