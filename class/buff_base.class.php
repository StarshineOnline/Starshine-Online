<?php
/**
 * @file buff_base.class.php
 * Définition de la classe buff_base servant de base aux buffs
 */

/**
 * Classe buff_base
 * Classe buff_base servant de base aux buffs sur les personnages et les monstres
 */
class buff_base extends buff_batiment_def
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $fin;  ///< Date et heure de fin du buff

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

	/// Renvoie si le buff est supprimable ou non
	/// @todo Qu'est-ce que ça vient faire là ?
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
      buff_batiment_def::__construct($id, $type, $effet, $effet2, $duree, $nom, $description, $debuff);
      // ($id = 0, $type='', $effet=0, $effet2=0, $duree=00, $nom='', $description='', $debuff=0)
			$this->fin = $fin;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    buff_batiment_def::init_tab($vals);
		$this->fin = $vals['fin'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return buff_batiment_def::get_liste_champs().', fin';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return buff_batiment_def::get_valeurs_insert().', '.$this->fin;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return buff_batiment_def::get_liste_update().', fin = '.$this->fin;
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
 
	function est_actif()
	{
		return true;
	}
}


?>
