<?php
/**
 * @file sort.class.php
 * Définition de la classe comp_sort servant de base aux sorts de combat et hors combat
 */

/**
 * Classe sort
 * Classe sort servant de base aux sorts de combat et hors combat
 */
abstract class sort extends comp_sort
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $incantation;  ///< Requis en incantation
	protected $difficulte;  ///< Difficulté de lancé

	 /// Renvoie si c'est un sort spécial
	function get_special()
	{
		return false;
	}
  /// envoie le coût en MP en prennant en compte l'affinité
  function get_mp_final(&$perso)
  {
    global $Trace;
    $mp = $this->mp * $perso->get_facteur_magie();
    if( !$this->get_special() )
    {
	    $affinite = $Trace[$perso->get_race()]['affinite_'.$this->comp_assoc];
	    $facteur = (1 - (($affinite - 5) / 10));
	    $mp = round($this->mp * $facteur);
		}
		if($perso->is_buff('buff_concentration', true))
			$sortmp = ceil($mp * (1 - ($perso->get_buff('buff_concentration','effet') / 100)));
    return $mp;
  }

  /// Renvoie le requis en incantation
	function get_incantation()
	{
		return $this->incantation;
	}
	/// Modifie le requis en incantation
	function set_incantation($incantation, $param = 'set')
	{
		$this->incantation = $incantation;
		$this->champs_modif[] = 'incantation';
	}

  /// Renvoie la difficulté de lancé
	function get_difficulte()
	{
		return $this->difficulte;
	}
	/// Modifie la difficulté de lancé
	function set_difficulte($difficulte, $param = 'set')
	{
		$this->difficulte = $difficulte;
		$this->champs_modif[] = 'difficulte';
	}
	// @}
	
  /**
   * Vérifie si un personnage a les pré-requis pour le sort ou la compétence 
   * @param $perso   personnage concerné
   */
  function verif_prerequis(&$perso, $txt_action=false)
  {
  	global $Gtrad, $Trace;
  	$incant = $perso->get_incantation() >= $this->get_incantation();
  	if( !$incant && $txt_action !== false )
  	{
  		if( $txt_action && $txt_action !== true )
  			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$this->get_incatation().' en incantation pour '.$txt_action.' ce sort.');
  		else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$this->get_incatation().' en incantation.');
		}
  	$aptitude = $this->get_comp_assoc();
  	$methode = 'get_'.$aptitude;
  	$prerequis = $this->get_comp_requis();
  	if( !$this->get_special() )
  		$prerequis = round($prerequis *$perso->get_facteur_magie() * (1 - (($Trace[$perso->get_race()]['affinite_'.$this->get_comp_assoc()] - 5) / 10)) );
  	if( $perso->$methode() >= $prerequis )
  		return $incant;
  	if( $txt_action !== false )
  	{
  		if( $txt_action && $txt_action !== true )
  			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$prerequis.' en '.$Gtrad[$aptitude].' pour '.$txt_action.' ce sort.');
  		else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$prerequis.' en '.$Gtrad[$aptitude].'.');
		}
  	return false;
	}

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
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $incantation=0, $difficulte=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp_sort::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment);
			$this->incantation = $incantation;
			$this->difficulte = $difficulte;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp_sort::init_tab($vals);
		$this->incantation = $vals['incantation'];
		$this->difficulte = $vals['difficulte'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp_sort::get_liste_champs().', incantation, difficulte';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp_sort::get_valeurs_insert().', '.$this->incantation.', '.$this->difficulte;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp_sort::get_liste_update().', arme_requis = '.$this->incantation.', difficulte = '.$this->difficulte;
	}
	// @}
}
?>
