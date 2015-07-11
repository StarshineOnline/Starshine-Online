<?php
/**
 * @file buff_batiment_def.class.php
 * Définition de la classe buff_batiment_def définissant les (de)buffs de bâtiments existants
 */

/**
 * Classe buff_batiment_def
 * Classe buff_batiment_def définissant les (de)buffs de bâtiments existants
 */
class buff_batiment_def extends comp_sort_buff
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $effet2; ///< Effet secondaire
	protected $description;  ///< Description du buff
	protected $debuff; ///< 0 pour un buff,  pour un debuff

	/// Renvoie l'effet secondaire
	function get_effet2()
	{
		return $this->effet2;
	}
	/// Modifie l'effet secondaire
	function set_effet2($effet2)
	{
		$this->effet2 = $effet2;
		$this->champs_modif[] = 'effet2';
	}
	
	/// Renvoie la description du buff
	function get_description($format=true)
	{
		if( $format )
			return $this->formate_description($this->description);
		else
			return $this->description;
	}
	/// Modifie la description du buff
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

	/// Renvoie si c'est un buff ou un debuff
	function get_debuff()
	{
		return $this->debuff;
	}
	/// Modifie si c'est un buff ou un debuff
	function set_debuff($debuff)
	{
		$this->debuff = $debuff;
		$this->champs_modif[] = 'debuff';
	}

	/// Renvoie si le buff est supprimable ou non
	function get_supprimable()
	{
		return $this->supprimable;
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
	 * @param type         Type générique.
	 * @param effet        Effet principal.
	 * @param effet2       Effet secondaire
	 * @param duree        Durée
	 * @param nom          Nom du buff
	 * @param description  Description du buff
	 * @param debuff       pour un buff,  pour un debuff
	*/
	function __construct($id = 0, $type='', $effet=0, $effet2=0, $duree=0, $nom='', $description='', $debuff=0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp_sort_buff::__construct($id, $nom, $type, $effet, $duree);
      //($id=0, $nom='', $type='', $effet=0, $duree=0)
			$this->effet2 = $effet2;
			$this->description = $description;
			$this->debuff = $debuff;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp_sort_buff::init_tab($vals);
		$this->effet2 = $vals['effet2'];
		$this->description = $vals['description'];
		$this->debuff = $vals['debuff'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp_sort_buff::get_liste_champs().', effet2, description, debuff';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp_sort_buff::get_valeurs_insert().', '.$this->effet2.', "'.mysql_escape_string($this->description).'", '.$this->debuff;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp_sort_buff::get_liste_update().', effet2 = '.$this->effet2.', description = "'.mysql_escape_string($this->description).'", debuff = '.$this->debuff;
	}
	// @}

	/**
	 * Lance le buff sur un personnage ou un monstre
	 *
	 * @return		buff
	 */
	function lance($id_constr, $id_plac, $id_perso=0)
	{
		$buff = new buff_batiment(0, $id_plac, $id_constr, $this->type, $this->effet, $this->effet2, $this->duree, time() + $this->duree, $this->nom, $this->description, $this->debuff, $id_perso);
		$buff->sauver();
		return $buff;
	}
}


?>
