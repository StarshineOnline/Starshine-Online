<?php
/**
 * @file objet_equip.class.php
 * Contient la définition de la classe objet_equip qui représente un objet
 * qu'un personnage équiper (plus quelques autres).
 */

/**
	Classe abstraite représentant un objet qu'un personnage peut équiper (plus quelques autres)
*/
abstract class objet_equip extends objet_invent
{
	protected $effet;  ///< Valeur de l'effet de l'objet
	protected $lvl_batiment;  ///< Niveau du bâtiment à partir duquel l'objet est disponible
  protected $enchantement = null;  ///< Enchantement par gemme
  protected $modification = null;  ///< Modification par la forge
  protected $slot = null;  ///< Slot


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

  /// Renvoie l'enchantement par gemme
  function get_enchantement()
  {
		return $this->enchantement;
  }
	/// Modifie l'enchantement par gemme
	function set_enchantement($enchantement)
	{
		$this->enchantement = $enchantement;
	}

  /// Renvoie la modification par la forge
  function get_modification()
  {
		return $this->modification;
  }
	/// Modifie la modification par la forge
	function set_modification($modif)
	{
		if( is_numeric($modif) )
			$modif =  new forge_recette($modif);
		$this->modification = $modif;
	}

  /// Renvoie le slot
  function get_slot()
  {
		return $this->slot;
  }
	/// Modifie le slot
	function set_slot($slot)
	{
		$this->slot = $slot;
	}
	
	// Renvoie le nom de l'objet
	function get_nom()
	{
		if( $this->modification )
			return $this->modification->get_nom();
		return $this->nom;
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

  /**
   */
  abstract function get_emplacement();

  /// Indique si l'objet est slotable
  function est_slotable() { return $this->identifie ? $this->slot === null && $this->enchantement === null : false; }

  /// Indique si l'objet est enchassable (renvoie le niveau de la gemme)
  function est_enchassable()
  {
    if( !$this->identifie )
      return false;
    if( $this->enchantement )
      return $this->enchantement;
    return $this->slot;
  }

  /**
   * Mettre un slot
   */
  function mettre_slot(&$perso, &$princ, $niveau)
  {
    // On vérifie que le personnage a assez de PA
    if( $perso->get_pa() < 10 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous pas assez de PA !');
      return false;
    }
    // On vérifie que l'objet n'a pas déjà un slot
    if( $this->get_slot() !== null )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Cet objet &agrave; d&eacute;j&agrave; un slot !');
      return false;
    }
    /// On vérifie que le slot n'est supérieur à la valeur max
    /// @todo loguer les tentatives de triche + centraliser valeur max ?
    if( $niveau > 3 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Niveau de slot invalide !');
      return false;
    }
    // on test
    /// TODO : centraliser la difficulté
		switch($niveau)
		{
		case '1' :
			$difficulte = 10;
		break;
		case '2' :
			$difficulte = 30;
		break;
		case '3' :
			$difficulte = 100;
		break;
		}
    if( comp_sort::test_potentiel($perso->get_forge(), $difficulte) )
    {
      $this->set_slot($niveau);
      $princ->add( new interf_alerte('success', true) )->add_message('Réussite !');

			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('objets_slot');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
    }
    else
    {
      $this->set_slot(0);
      $princ->add( new interf_alerte('danger', true) )->add_message('Echec... l\'objet ne pourra plus être enchâssable.');
    }
    // Augmentation de l'attribut
		$augmentation = augmentation_competence('forge', $perso, 2);
		if ($augmentation[1] == 1)
		{
			$perso->set_forge($augmentation[0]);
			$perso->sauver();
		}
    // on recompose la version textuelle & retire les PA
    $this->recompose_texte();
		$perso->set_pa($perso->get_pa() - 10);

    return true;
  }

  /**
   * Mettre une gemme
   */
  function enchasser(&$perso, &$princ, $gemme)
  {
    if( $perso->get_pa()< 20 )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous pas assez de PA !');
      return false;
    }
    // on vérifie qu'il n'y a pas déjà de gemme
    if( $this->get_enchantement() )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Il y a déjà une gemme sur cet objet !');
      return false;
    }
    // on vérifie qu'il n'y a un slot du bon niveau
    if( $this->get_slot() != $gemme->get_niveau() )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('L\'objet n\'a pas le bon slot !');
      return false;
    }
    // on test
    /// TODO : centraliser la difficulté
		switch($niveau)
		{
		case '1' :
			$difficulte = 10;
		break;
		case '2' :
			$difficulte = 30;
		break;
		case '3' :
			$difficulte = 100;
		break;
		}
    $test = comp_sort::test_potentiel($perso->get_forge(), $difficulte);
    if( $test )
    {
			//Craft réussi
      $princ->add( new interf_alerte('success', true) )->add_message('Réussite !');
			$this->set_enchantement( $gemme->get_id() );
			$this->set_slot(null);
      //$perso->supprime_objet($perso->get_inventaire_slot_partie( $gemme->get_texte()), 1);

			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('objets_slotted');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
    }
    else
    {
      // 1 chance sur 2 que l'objet ne soit plus enchassable
      if( rand(0, 1) )
      {
        $princ->add( new interf_alerte('danger', true) )->add_message('Echec… L\'objet ne pourra plus être enchassable…');
			  $this->set_slot(0);
      }
      else
        $princ->add( new interf_alerte('danger', true) )->add_message('Echec…');
    }
    // Augmentation de l'attribut
		$augmentation = augmentation_competence('forge', $perso, 2);
		if ($augmentation[1] == 1)
			$perso->set_forge($augmentation[0]);
    // on recompose la version textuelle & retire les PA
    $this->recompose_texte();
		$perso->set_pa($perso->get_pa() - 20);
    return $test;
  }

  /**
   * Retirer une gemme
   */
  function recup_gemme(&$perso, &$princ)
  {
    return true;
  }
  
  function peut_utiliser(&$perso)
  {
    return true;
	}
}
?>