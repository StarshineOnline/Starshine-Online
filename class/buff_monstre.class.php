<?php
/**
 * @file buff_monstre.class.php
 * Définition de la classe buff_monstre représentant les buffs sur les monstres
 */
if (file_exists('../root.php'))
  include_once('../root.php');

/**
 * Classe buff_monstre
 * Classe buff_monstre représentant les buffs sur les monstres
 */
class buff_monstre extends buff_base
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $id_monstre;  ///< id du monstre ayant le buff

	/// Renvoie l'id du monstre ayant le buff
	function get_id_monstre()
	{
		return $this->id_monstre;
	}
	/// Modifie l'id du monstre ayant le buff
	function set_id_monstre($id_monstre)
	{
		$this->id_monstre = $id_monstre;
		$this->champs_modif[] = 'id_monstre';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id           Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
   * @param id_monstre   Id du monstre ayant le buff
   * @param type         Type générique.
	 * @param effet        Effet principal.
	 * @param effet2       Effet secondaire
	 * @param duree        Durée
	 * @param fin          Date et heure de fin du buff
	 * @param nom          Nom du buff
	 * @param description  Description du buff
	 * @param debuff       Pour un buff,  pour un debuff
	 * @param supprimable  Si on peut supprimer le (de)buff, 0 sinon
	*/
	function __construct($id = 0, $id_monstre=0, $type='', $effet=0, $effet2=0, $duree=0, $fin=0, $nom='', $description='', $debuff=0, $supprimable=0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      buff_base::__construct($id, $type, $effet, $duree);
			$this->id_monstre = $id_monstre;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    buff_base::init_tab($vals);
		$this->id_monstre = $vals['id_monstre'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return buff_base::get_liste_champs().', id_monstre';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return buff_base::get_valeurs_insert().', '.$this->id_monstre;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return buff_base::get_liste_update().', id_monstre = '.$this->id_monstre;
	}
	// @}
}
?>
