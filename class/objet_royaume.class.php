<?php
/**
 * @file objet_royaume.class.php
 * Gestion des objets RvR permettant de poser des bâtiments
 */


/**
 * Classe gérant les objets RvR permettant de poser des bâtiments
 * Il s'agit des objets que l'on peut prenre au dépôts pour poser des bourgages, forts, tours, armes de sièges…
 * Correspond à la table du même nom dans la bdd.
 */
class objet_royaume extends objet_invent
{
	protected $grade;  ///< grade nécessaire pour pouvoir prendre (et poser) l'objet.
	protected $id_batiment;  ///< id du bâtiment correspondant.
  protected $pierre;  ///< coût en pierre.
  protected $bois;  ///< coût en bois.
  protected $eau;  ///< coût en eau.
  protected $sable;  ///< coût en sable.
  protected $charbon;  ///< coût en charbon.
  protected $essence;  ///< coût en essence.

  /// Renvoie le grade nécessaire pour pouvoir prendre (et poser) l'objet.
	function get_grade()
	{
		return $this->grade;
	}
  /// Modifie le grade nécessaire pour pouvoir prendre (et poser) l'objet.
	function set_grade($grade)
	{
		$this->grade = $grade;
		$this->champs_modif[] = 'grade';
	}

  /// Renvoie l'id du bâtiment correspondant.
	function get_id_batiment()
	{
		return $this->id_batiment;
	}
  /// Modifie l'id du bâtiment correspondant.
	function set_id_batiment($id_batiment)
	{
		$this->id_batiment = $id_batiment;
		$this->champs_modif[] = 'id_batiment';
	}

  /// Renvoie le coût en pierre.
	function get_pierre()
	{
		return $this->pierre;
	}
  /// Modifie le coût en pierre.
	function set_pierre($pierre)
	{
		$this->pierre = $pierre;
		$this->champs_modif[] = 'pierre';
	}

  /// Renvoie le coût en bois.
	function get_bois()
	{
		return $this->bois;
	}
  /// Modifie le coût en bois.
	function set_bois($bois)
	{
		$this->bois = $bois;
		$this->champs_modif[] = 'bois';
	}

  /// Renvoie le coût en eau.
	function get_eau()
	{
		return $this->eau;
	}
  /// Modifie le coût en eau.
	function set_eau($eau)
	{
		$this->eau = $eau;
		$this->champs_modif[] = 'eau';
	}

  /// Renvoie le coût en sable.
	function get_sable()
	{
		return $this->sable;
	}
  /// Modifie le coût en sable.
	function set_sable($sable)
	{
		$this->sable = $sable;
		$this->champs_modif[] = 'sable';
	}

  /// Renvoie le coût en charbon.
	function get_charbon()
	{
		return $this->charbon;
	}
  /// Modifie le coût en charbon.
	function set_charbon($charbon)
	{
		$this->charbon = $charbon;
		$this->champs_modif[] = 'charbon';
	}

  /// Renvoie le coût en essence.
	function get_essence()
	{
		return $this->essence;
	}
  /// Modifie le coût en essence.
	function set_essence($essence)
	{
		$this->essence = $essence;
		$this->champs_modif[] = 'essence';
	}

	/**
	 * Constructeur
	 * @param  $nom		       nom de l'objet.
	 * @param  $type	       type de l'objet.
	 * @param  $prix	       prix de l'objet em magasin.
	 * @param  $grade	       grade nécessaire pour pouvoir prendre (et poser) l'objet.
	 * @param  $id_batiment	 id du bâtiment correspondant.
	 * @param  $pierre	     coût en pierre.
	 * @param  $bois	       coût en bois.
	 * @param  $eau	         coût en eau.
	 * @param  $sable	       coût en sable.
	 * @param  $charbon	     coût en charbon.
	 * @param  $essence	     coût en essence.
	 */
	function __construct($nom='', $type='', $prix=0, $grade=2, $id_batiment=0, $pierre=0, $bois=0, $eau=0, $sable=0, $charbon=0, $essence=0)
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
			$this->grade = $grade;
			$this->id_batiment = $id_batiment;
			$this->pierre = $pierre;
			$this->bois = $bois;
			$this->eau = $eau;
			$this->sable = $sable;
			$this->charbon = $charbon;
			$this->essence = $essence;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->grade = $vals['grade'];
		$this->id_batiment = $vals['id_batiment'];
		$this->pierre = $vals['pierre'];
		$this->bois = $vals['bois'];
		$this->eau = $vals['eau'];
		$this->sable = $vals['sable'];
		$this->charbon = $vals['charbon'];
		$this->essence = $vals['essence'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['grade']='i';
    $tbl['id_batiment']='i';
    $tbl['pierre']='i';
    $tbl['bois']='i';
    $tbl['eau']='i';
    $tbl['sable']='i';
    $tbl['charbon']='i';
    $tbl['essence']='i';
		return $tbl;
	}
}