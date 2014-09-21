<?php
/**
 * @file arme.class.php
 * Gestion des armes
 */

/**
 * Classe gérant les armes
 * Correspond à la table du même nom dans la bdd.
 */
class arme extends objet_equip
{
	protected $degat;  ///< dégâts de l'arme
	protected $forcex;  ///< force nécessaire pour utiliser l'arme
	protected $coefficient;   ///< coefficient minimal pour utiliser l'arme
	protected $distance_tir;  ///< distance d'attaque de l'arme
	protected $mains;  ///< main(s) utilisée(s) pour porter l'arme
	protected $var1; ///< malus d'esquive ou bonus pour le lancer des sorts
	const code = 'a';   ///< Code de l'objet.

	/// Retourne les dégâts de l'arme
	function get_degat()
	{
		return $this->degat;
	}
	/// Modifie les dégâts de l'arme
	function set_degat($degat)
	{
		$this->degat = $degat;
		$this->champs_modif[] = 'degat';
	}

	/// Retourne la force nécessaire pour utiliser l'arme
	function get_force()
	{
		return $this->forcex;
	}
	/// Modifie la force nécessaire pour utiliser l'arme
	function set_force($force)
	{
		$this->forcex = $force;
		$this->champs_modif[] = 'forcex';
	}

	/// Retourne le coefficient minimal pour utiliser l'arme
	function get_coefficient()
	{
		return $this->coefficient;
	}
	/// Modifie le coefficient minimal pour utiliser l'arme
	function set_coefficient($coefficient)
	{
		$this->coefficient = $coefficient;
		$this->champs_modif[] = 'coefficient';
	}

	/// Retourne la distance d'attaque de l'arme
	function get_distance_tir()
	{
		return $this->distance_tir;
	}
	/// Modifie la distance d'attaque de l'arme
	function set_distance_tir($distance_tir)
	{
		$this->distance_tir = $distance_tir;
		$this->champs_modif[] = 'distance_tir';
	}

	/// Retourne le(s) main(s) utilisée(s) pour porter l'arme
	function get_mains()
	{
		return $this->mains;
	}
	/// Modifie le(s) main(s) utilisée(s) pour porter l'arme
	function set_mains($mains)
	{
		$this->mains = $mains;
		$this->champs_modif[] = 'mains';
	}

	/// Retourne le malus d'esquive ou bonus pour le lancer des sorts
	function get_var1()
	{
		return $this->var1;
	}
	/// Retourne le malus d'esquive
  function get_malus_esquive()
  {
    if( $this->type == 'arc' or $this->type == 'hache' )
      return $this->var1;
    else
      return 1;
  }
	/// Retourne le bonus pour le lancer des sorts
  function get_bonus_sorts()
  {
    if( $this->type == 'baton' )
      return $this->var1;
    else
      return 1;
  }
	/// Modifie le malus d'esquive ou bonus pour le lancer des sorts
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet
	 * @param  $type	         type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	         prix de l'objet em magasin
	 * @param  $effet	         valeur de l'effet de l'objet
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible
	 * @param  $degat	         dégâts de l'arme
	 * @param  $force	         force nécessaire pour utiliser l'arme
	 * @param  $coefficient	   coefficient minimal pour utiliser l'arme
	 * @param  $distance_tir	 distance d'attaque de l'arme
	 * @param  $mains	         main(s) utilisée(s) pour porter l'arme
	 * @param  $var1	         malus d'esquive ou bonus pour le lancer des sorts
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $degat=0, $force=0, $coefficient=0, $distance_tir=0, $mains='main_droite', $var1=0)
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
			$this->effet = $effet;
			$this->lvl_batiment = $lvl_batiment;
			$this->degat = $degat;
			$this->forcex = $force;
			$this->coefficient = $coefficient;
			$this->distance_tir = $distance_tir;
			$this->mains = $mains;
			$this->var1 = $var1;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_equip::init_tab($vals);
		$this->degat = $vals['degat'];
		$this->forcex = $vals['forcex'];
		$this->coefficient = $vals['coefficient'];
		$this->distance_tir = $vals['distance_tir'];
		$this->mains = $vals['mains'];
		$this->var1 = $vals['var1'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['degat']='i';
    $tbl['forcex']='i';
    $tbl['coefficient']='i';
    $tbl['distance_tir']='i';
    $tbl['mains']='s';
    $tbl['var1']='i';
		return $tbl;
	}

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $image = 'image/arme/arme'.$this->get_id().'.png';
    if( file_exists($image) )
      return $image;
    return null;
  }

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    $noms = array('Type');
    $noms[] = $complet ? 'Nombre de mains' : 'Mains';
    $noms[] = $this->get_type() == 'bouclier' ? 'Absorption' : 'Dégâts';
    if( $this->get_type() == 'baton' )
      $noms[] =  $complet ? 'Bonus au lancé de sort' : 'Bonus';
    if( $complet )
    {
      if( $this->get_type() == 'arc' or $this->get_type() == 'hache' )
        $noms[] =  'Malus d\'esquive';
      switch( $this->get_type() )
      {
      case 'hache':
        $noms[] =  'Malus d\'esquive';
      case 'epee':
      case 'dague':
        $noms[] =  'Coefficient de mêlée';
        break;
      case 'arc':
        $noms[] =  'Malus d\'esquive';
        $noms[] =  'Coefficient de tir';
        break;
      case 'baton':
        $noms[] =  'Coefficient de magie';
        break;
      }
      $noms[] = 'Force nécessaire';
    }
    else
      $noms[] = 'Coeff.';
    $noms[] = 'Portée';
    $noms[] = $complet ? 'Prix HT (en magasin)' : 'Stars';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $vals = array($this->type, $this->mains, $this->degat);
    if( $this->get_type() == 'baton' or ($complet && ($this->get_type() == 'arc' or $this->get_type() == 'hache')) )
      $vals[] = $this->var1;
    $vals[] = $this->coefficient;
    if( $complet )
      $vals[] = $this->forcex;
    $vals[] = $this->distance_tir;
    $vals[] = $this->prix;
    return $vals;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
    if( $this->get_type() == 'baton' )
      return 'Bonus : + '.$this->var1.' %';
    else
      return 'Dégâts : '.$this->degat;
  }
	
	function get_colone_int($partie)
  {
    if( $partie == 'equipement' )
      return 0;
    else
      return false;
  }

  function get_emplacement()
  {
    $main = explode(';', $this->mains);
    return $main[0];
  }
  
  function peut_utiliser(&$perso, $msg=true)
  {
		switch($this->type)
		{
		case 'epee':
		case 'hache':
		case 'dague':
			$coeff = $perso->get_coef_melee();
			$nom = 'coeff. de mêlée';
			break;
		case 'arc':
			$coeff = $perso->get_coef_distance();
			$nom = 'coeff. de tir';
			break;
		case 'bouclier':
			$coeff = $perso->get_coef_blocage();
			$nom = 'coeff. de blocage';
			break;
		case 'baton':
			$coeff = $perso->get_coef_incantation();
			$nom = 'coeff. d\'incatation';
			break;
		}
    if( $coeff >= $this->coefficient )
    	return true;
    if( $msg )
    	interf_alerte::enregistre(interf_alerte::msg_erreur, 'Il vous faut '.$coeff.' en '.$nom.'.');
    return false;
	}
}