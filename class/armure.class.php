<?php
/**
 * @file objet.class.php
 * Gestion des armures
 */

/**
 * Classe gérant les armures
 * Correspond à la table du même nom dans la bdd.
 */
class armure extends objet_invent
{
	protected $PP;  ///< PP de l'armure
	protected $PM;  ///< PM de l'armure
	protected $forcex;  ///< force nécessaire pour utiliser l'armure
	protected $puissance;  ///< Puissance nécessaire pour porter l'objet.

	/// Retourne la force nécessaire pour utiliser l'arme
	function get_pp()
	{
		return $this->PP;
	}
	/// Modifie la PP de l'armure
	function set_pp($pp)
	{
		$this->PP = $pp;
		$this->champs_modif[] = 'PP';
	}

	/// Retourne la force nécessaire pour utiliser l'arme
	function get_pm()
	{
		return $this->PM;
	}
	/// Modifie la force nécessaire pour utiliser l'arme
	function set_pm($pm)
	{
		$this->PM = $pm;
		$this->champs_modif[] = 'PM';
	}

	/// Retourne la force nécessaire pour utiliser l'armure
	function get_force()
	{
		return $this->forcex;
	}
	/// Modifie la force nécessaire pour utiliser l'armure
	function set_force($force)
	{
		$this->forcex = $force;
		$this->champs_modif[] = 'forcex';
	}

	/// Retourne la puissance nécessaire pour porter l'objet.
	function get_puissance()
	{
		return $this->puissance;
	}
	/// Modifie la puissance nécessaire pour porter l'objet.
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet
	 * @param  $type	         type de l'objet
	 * @param  $prix	         prix de l'objet em magasin
	 * @param  $effet	         valeur de l'effet de l'objet
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible
	 * @param  $pp       	     PP de l'armure.
	 * @param  $pm       	     PM de l'armure.
	 * @param  $force	         force nécessaire pour utiliser l'armure
	 * @param  $puissance	     puissance nécessaire pour porter l'objet.
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $pp=0, $pm=0, $force=0, $puissance=0)
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
			$this->PP = $pp;
			$this->PM = $pm;
			$this->forcex = $force;
			$this->puissance = $puissance;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_equip::init_tab($vals);
		$this->PP = $vals['PP'];
		$this->PM = $vals['PM'];
		$this->forcex = $vals['forcex'];
		$this->puissance = $vals['puissance'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['PP']='i';
    $tbl['PM']='i';
    $tbl['forcex']='i';
    $tbl['puissance']='i';
		return $tbl;
	}

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $image = 'image/armure/'.$this->get_type().'/'.$this->get_type().$this->get_id().'.png';
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
    $noms = array('PP', 'PM');
    $noms[] = $complet ? 'Force nécessaire' : 'Force';
    $noms[] = $complet ? 'Prix HT (en magasin)' : 'Stars';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $vals = array($this->PP, $this->PM, $this->forcex, $this->prix);
    return $vals;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
    return 'PP : '.$this->PP.' - PM : '.$this->PM;
  }

	function get_colone_int($partie)
  {
    if( $partie == 'equipement' )
      return 1;
    else
      return false;
  }

  function get_emplacement()
  {
    return $this->type;
  }
  
  function peut_utiliser(&$perso, $msg=true)
  {
    if( $perso->get_puissance() < $this->puissance )
    {
			if( $msg )
	    	interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de puissance.');
    	return false;
		}
    if( $perso->get_force() >= $this->forcex )
    	return true;
    if( $msg )
    	interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de force.');
    return false;
	}
}
?>