<?php
/**
 * @file objet.class.php
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
	
	function affiche_image()
	{
		return '<img src="'.$this->image().'" alt="'.$this->nom.'" />';
	}
	
	//Infobulle de l'arme
	function infobulle()
	{
		$milieu = '<tr><td>Nombre de mains:</td><td>'.(strcmp($this->mains, 'main_droite') != -1 ? 1 : 2).'</td></tr>';
		$milieu .= '<tr><td>D&eacute;gats:</td></tr><tr><td>'.$this->degat.'</td></tr>';
		$milieu .= '<tr><td>Port&eacute;e:</td></tr><tr><td>'.$this->portee.'</td></tr>';
		$milieu .= ( !empty($this->effet) ? '<tr><td>Effet:</td></tr><tr><td>'.$this->effet.'</td></tr>' : '');
		$milieu .= '<tr><td>Force n&eacute;cessaire:</td></tr><tr><td>'.$this->forceReq.'</td></tr>';
		//Gemmage?
		return bulleBase($milieu).'<br />'.$this->effet;
	}
}