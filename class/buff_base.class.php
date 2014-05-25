<?php
/**
 * @file buff_base.class.php
 * Définition de la classe buff_base servant de base aux buffs
 */

/**
 * Classe buff_base
 * Classe buff_base servant de base aux buffs sur les personnages et les monstres
 */
class buff_base extends comp_sort_buff
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $effet2; ///< Effet secondaire
	protected $fin;  ///< Date et heure de fin du buff
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

	/// Renvoie la date et heure de fin du buff
	function get_fin()
	{
		return $this->fin;
	}
	/// Modifie la date et heure de fin du buff
	function set_fin($fin)
	{
		$this->fin = $fin;
		$this->champs_modif[] = 'fin';
	}
	/// Renvoie la description du buff
	function get_description()
	{
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
	 * @param fin          Date et heure de fin du buff
	 * @param nom          Nom du buff
	 * @param description  Description du buff
	 * @param debuff       pour un buff,  pour un debuff
	*/
	function __construct($id = 0, $type='', $effet=0, $effet2=0, $duree=0, $fin=0, $nom='', $description='', $debuff=0)
	{
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp_sort_buff::__construct($id, $type, $effet, $duree);
			$this->effet2 = $effet2;
			$this->fin = $fin;
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
		$this->fin = $vals['fin'];
		$this->description = $vals['description'];
		$this->debuff = $vals['debuff'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp_sort_buff::get_liste_champs().', effet2, fin, description, debuff';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp_sort_buff::get_valeurs_insert().', '.$this->effet2.', '.$this->fin.', "'.mysql_escape_string($this->description).'", '.$this->debuff;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp_sort_buff::get_liste_update().', effet2 = '.$this->effet2.', fin = '.$this->fin.', description = "'.mysql_escape_string($this->description).'", debuff = '.$this->debuff;
	}
	// @}

	/**
 * Lance le buff sur un personnage ou un monstre
 *
 * @param  $ancien        Ancien buff
 * @param  $nb_buff       Nombre de buffs déjà actifs sur la cible.
 * @param  $nb_buff_max   Nombre de buffs max de la cible de la cible (grade+).
 *
 * @return      true si le sort a été lancé et false sinon.
 */
 protected function lance_buff_int($ancien, $nb_buff=0, $nb_buff_max=0)
 {
	global $db, $G_erreur;
	$lancement = true;
	//echo $db->num_rows;
	if( !$ancien )
	{
		// La cible n'a pas le sort d'encore lancé
		if($nb_buff < $nb_buff_max || $nb_buff_max == 0 || $this->get_debuff())
		{
      // On peut avoir autant de debuff qu'on veut
      $this->sauver();
		}
		else
		{
		  // plus de place
			$G_erreur = 'overbuff';
			$lancement = false;
		}
	}
	elseif($this->get_effet() >= $ancien->get_effet())
	{
	  // L'effet est plus grand (ou égal) : on met à jour
	  $ancien->set_effet( $this->get_effet() );
	  $ancien->set_effet2( $this->get_effet2() );
	  $ancien->set_fin( time() + $this->get_duree() );
	  $ancien->set_nom( $this->get_nom() );
	  $ancien->set_description( $this->get_description() );
	  $ancien->sauver();
	}
	else
	{
	  // La cible a déjà le sort (ou mieux).
		$G_erreur = 'puissant';
		$lancement = false;
	}
	return $lancement;
 }
}


?>
