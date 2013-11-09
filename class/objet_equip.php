<?php
/**
 * @file objet_equip.class.php
 * Contient la définition de la classe objet_equip qui représente un objet
 * qu'un personnage équiper (plus quelques autres).
 */

/**
	Classe abstraite représentant un objet qu'un personnage peut équiper (plus quelques autres)
*/
class objet_equip extends objet_invent
{
	private $effet;  ///< Valeur de l'effet de l'objet
	private $lvl_batiment;  ///< Niveau du bâtiment à partir duquel l'objet est disponible


	/// Retourne la valeur de l'effet de l'objet
	function get_effet()
	{
		return $this->effet;
	}
	/// Modifie la valeur de l'effet de l'objet
	function set_effet($effet)
	{
		$this->effet = $effet;
		$this->champs_modif[] = 'effet';
	}

	///Retourne le niveau du bâtiment à partir duquel l'objet est disponible
	function get_lvl_batiment()
	{
		return $this->lvl_batiment;
	}
	/// Modifie le niveau du bâtiment à partir duquel l'objet est disponible
	function set_lvl_batiment($lvl_batiment)
	{
		$this->lvl_batiment = $lvl_batiment;
		$this->champs_modif[] = 'lvl_batiment';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet
	 * @param  $type	         type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	         prix de l'objet em magasin
	 * @param  $effet	         valeur de l'effet de l'objet
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9)
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
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->effet = $vals['effet'];
		$this->lvl_batiment = $vals['lvl_batiment'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['effet']='i';
    $tbl['lvl_batiment']='i';
		return $tbl;
	}
}
?>