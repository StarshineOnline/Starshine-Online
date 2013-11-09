<?php
/**
 * @file objet_invent.class.php
 * Contient la définition de la classe objet_invent qui représente un objet
 * qu'un personnage peut avoir dans son inventaire.
 */

/**
	Classe abstraite représentant un objet qu'un personnage peut avoir dans son inventaire
*/
abstract class objet_invent extends table
{
	protected $nom;  ///< Nom de l'objet.
	protected $type;  ///< Type de l'objet (epee, hache, dos, potion_hp,… ).
	protected $prix;  ///< Prix de l'objet em magasin.
	
	// Renvoie le nom de l'objet
	function get_nom()
	{
		return $this->nom;
	}
	
	/// Modifie le nom de l'objet
	function set_nom($newNom)
	{
		$this->nom = $newNom;
	}
	
	// Renvoie le type de l'objet
	function get_type()
	{
		return $this->type;
	}
	
	/// Modifie le type de l'objet
	function set_type($newType)
	{
		$this->type = $newType;
	}
	
	// Renvoie le prix de l'objet em magasin
	function get_prix()
	{
		return $this->prix;
	}
	
	/// Modifie le prix de l'objet em magasin
	function set_prix($newPrix)
	{
		$this->prix = $newPrix;
	}
	
	/**
	 * Constructeur
	 * @param  $nom		nom de l'objet
	 * @param  $type	type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	prix de l'objet em magasin
	 */
	function __construct($nom='', $type='', $prix=0)
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
		}
	}
	
	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		table::init_tab($vals);
		$this->nom = $vals['nom'];
		$this->type = $vals['type'];
		$this->prix = $vals['prix'];
	}
	
	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
		return array('nom'=>'s', 'type'=>'s', 'prix'=>'i');
	}
	
	//Fonction permettant d'ajouter un nouvel objet dans la base
	abstract public function infobulle();
	
	//Retourne le début et la fin de la chaine de l'infobulle.
	protected function bulleBase($middle)
	{
		$infobulle = '<strong>'.$this->nom.'</strong><br />';
		$infobulle .= '<table><tr><td>Type:</td><td>'.$this->type.'</td></tr>'.$middle;
		$infobulle = '<tr><td>Prix HT:<br /><span class=\'xsmall\'>(en magasin)</span></td><td>'.$this->prix.'</td></tr></table>';
		return $infobulle;
	}
}
?>
