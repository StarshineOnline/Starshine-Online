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
	
	//Liste des Accesseurs
	function getPP()
	{
		return $this->pp;
	}
	
	function getPM()
	{
		return $this->pm;
	}
	
	function getForce()
	{
		return $this->forceReq;
	}
	
	//Liste des modifieurs
	function setPP($pp)
	{
		$this->pp = $pp;
	}
	
	function setPM($pm)
	{
		$this->pm = $pm;
	}
	
	function setForce($force)
	{
		$this->forceReq = $force;
	}
	
	//Infobulle de l'armure
	function infobulle()
	{
		$milieu = '<tr><td>PP:</td><td>'.$this->pp.'</td></tr>';
		$milieu .= '<tr><td>PM:</td></tr><tr><td>'.$this->pm.'</td></tr>';
		$milieu .= '<tr><td>Force n&eacute;cessaire:</td></tr><tr><td>'.$this->forceReq.'</td></tr>';
		//Gemmage?
		return bulleBase($milieu).'<br />'.$this->effet;
	}
}
?>