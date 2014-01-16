<?php
/**
 * @file objet.class.php
 * Gestion des accessoires
 */

/**
 * Classe gérant les accessoires
 * Correspond à la table du même nom dans la bdd.
 */
class accessoire extends objet_equip
{
	protected $description;  ///< Description de l'objet.
	protected $puissance;  ///< Puissance nécessaire pour porter l'objet.
	protected $taille;  ///< Taille de l'accessoire.

	/// Retourne la description de l'objet.
	function get_description()
	{
		return str_replace('%effet%', $this->effet, $this->description);
	}
	/// Modifie la description de l'objet.
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
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

	/// Retourne la taille de l'accessoire.
	function get_taille()
	{
		return $this->taille;
	}
	/// Modifie la taille de l'accessoire.
	function set_taille($taille)
	{
		$this->taille = $taille;
		$this->champs_modif[] = 'taille';
	}

	/**
	 * Constructeur
	 * @param  $nom		         nom de l'objet
	 * @param  $type	         type de l'objet (epee, hache, dos, potion_hp,… )
	 * @param  $prix	         prix de l'objet em magasin
	 * @param  $effet	         valeur de l'effet de l'objet
	 * @param  $lvl_batiment   niveau du bâtiment à partir duquel l'objet est disponible
	 * @param  $effet	         description de l'objet.
	 * @param  $puissance	     puissance nécessaire pour porter l'objet.
	 */
	function __construct($nom='', $type='', $prix=0, $effet=0, $lvl_batiment=9, $description='', $puissance=0)
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
			$this->description = $description;
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
		$this->description = $vals['description'];
		$this->puissance = $vals['puissance'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_equip::get_champs();
    $tbl['description']='s';
    $tbl['puissance']='i';
		return $tbl;
	}

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $image = 'image/accessoire/accessoire'.$this->get_id().'.png';
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
    $noms = array('Taille', 'Description');
    $noms[] = $complet ? 'Puissance nécessaire' : 'Puissance';
    $noms[] = $complet ? 'Prix HT (en magasin)' : 'Stars';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $vals = array($this->taille, $this->get_description(), $this->puissance, $this->prix);
    return $vals;
  }

	function get_colone_int($partie)
  {
    if( $partie == 'equipement' )
      return 2;
    else
      return false;
  }

  function get_emplacement()
  {
    return 'accessoire';
  }
}
?>
