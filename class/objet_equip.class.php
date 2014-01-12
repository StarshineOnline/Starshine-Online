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
	function set_stack($enchantement)
	{
		$this->enchantement = $enchantement;
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
    /// TODO: loguer les tentatives de triche + centraliser valeur max ?
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
    $test = comp_sort::test_potentiel($perso->get_forge(), $difficulte);
    if( $test )
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
      $princ->add( new interf_alerte('danger', true) )->add_message('Echec... l\'objet ne pourra plus être enchâssable.') );
    }
    // Augmentation de l'attribut
		$augmentation = augmentation_competence('forge', $perso, 2);
		if ($augmentation[1] == 1)
		{
			$perso->set_forge($augmentation[0]);
			$perso->sauver();
		}
    // Enregistrement dans l'inventaire & on retire les PA
    $anc = $this->get_texte();
    $this->recompose_texte();
		$perso->set_inventaire_slot_partie( $this->get_texte(), $anc);
		$perso->set_inventaire_slot(serialize($perso->get_inventaire_slot_partie(false, true)));
		$perso->set_pa($perso->get_pa() - 10);
		$perso->sauver();

    return $test;
  }

  /**
   * Mettre une gemme ou en retirer une
   */
  function enchasser(&$perso, &$princ, $niveau)
  {
    return false;
  }
}
?>