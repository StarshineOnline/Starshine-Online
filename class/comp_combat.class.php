<?php
/**
 * @file comp_combat.class.php
 * Définition de la classe comp_combat servant de base aux compétences de combat
 */

/**
 * Classe comp_combat
 * Classe comp_combat servant de base aux compétences de combat
 */
class comp_combat extends comp
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $effet3;  ///< Troisième effet
	protected $etat_lie;  ///< État du personnage lié à cette compétence

  /// Renvoie le troisième effet
	function get_effet3()
	{
		return $this->effet3;
	}
	/// Modifie le troisième effet
	function set_effet3($effet3)
	{
		$this->effet3 = $effet3;
		$this->champs_modif[] = 'effet3';
	}

  /// Renvoie l'état du personnage lié à cette compétence
	function get_etat_lie()
	{
		return $this->etat_lie;
	}
	/// Modifie l'état du personnage lié à cette compétence
	function set_etat_lie($etat_lie)
	{
		$this->etat_lie = $etat_lie;
		$this->champs_modif[] = 'etat_lie';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id             Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type           Type générique.
	 * @param effet          Effet principal.
	 * @param duree          Durée
	 * @param comp_assoc     Compétence associée
	 * @param carac_assoc    Caractéristique associée
	 * @param comp_requis    Requis dans la compétence
	 * @param carac_requis   Requis dans la caractéristique (inutilisé)
	 * @param effet2         Deuxième effet
	 * @param requis         Compétence ou sort requis pour apprendre celui-ci
	 * @param cible          Cible de la compétence ou du sort
	 * @param description    Description du buff
	 * @param mp             Coût en MP ou en RM
	 * @param prix           Prix de la compétence ou le sort
	 * @param lvl_batiment   Niveau de l'école qui vent la compétence ou le sort
	 * @param arme_requis    Arme requise pour utiliser la compétence
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $effet3, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $arme_requis='', $etat_lie='')
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $arme_requis);
			$this->effet3 = $effet3;
			$this->etat_lie = $etat_lie;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp::init_tab($vals);
		$this->effet3 = $vals['effet3'];
		$this->etat_lie = $vals['etat_lie'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp::get_liste_champs().', effet3, ';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp::get_valeurs_insert().', '.$this->effet3.', '.$this->etat_lie;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp::get_liste_update().', effet3 = '.$this->effet3.', etat_lie = "'.mysql_escape_string($this->etat_lie).'"';
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM comp_jeu WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      default:
        return new comp_jeu($row);
      }
  	}
  }
  
  /// Méthode renvoyant une instance correspondant à une attaque simple
  static function creer_attaque()
  {
    return new comp_jeu(0, 'attaque', 'attaque');
  }
	// @}


	/**
	 * @name Gestion du lancement
	 * Méthodes utilisées lors de l'utilisation (lancement) de la compétence / du sort
	 */
  // @{
  /**
   * Méthode gérant l'utilisation d'une compétence
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function lance(&$actif, &$passif, &$effets)
  {
    global $log_combat;
  	// Application des effets de début de round
  	foreach($effets as $effet)
  		$effet->debut_round($actif, $passif);
  		
    // Test pour toucher
  	$potentiel_toucher = $actif->get_potentiel_toucher();
		foreach($effets as $effet)
			$potentiel_toucher = $effet->calcul_attaque_physique($actif, $passif, $potentiel_toucher);
  	$potentiel_parer = $passif->get_potentiel_parer();
		foreach($effets as $effet)
			$potentiel_parer = $effet->calcul_defense_physique($actif, $passif, $potentiel_parer);
    if( $this->test_potentiel($potentiel_toucher, $potentiel_parer) )
    {
      $this->touche($actif, $passif, $effects);
      $passif->precedent['esquive'] = false;
    }
  	else
    {
  		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible</span><br />';
      $passif->precedent['esquive'] = true;
  		$log_combat .= '~e';
    }
    
    /* TODO : augmentations

	//Augmentation des compétences de base
	$diff_att = (3.2 * $G_round_total / 5) * $rectif_augm;
	$augmentation['actif']['comp'][] = array($competence, $diff_att);
	$diff_esquive = (2.7 * $G_round_total / 5) * $rectif_augm;
	$augmentation['passif']['comp'][] = array('esquive', $diff_esquive);

	//Augmentation des compétences liées
	if($actif->is_competence('art_critique') && $critique)
		$augmentation['actif']['comp_perso'][] = array('art_critique', 2.5 * $rectif_augm);
	if($actif->is_competence('maitrise_critique') && $critique)
		$augmentation['actif']['comp_perso'][] = array('maitrise_critique', 2 * $rectif_augm);
	$arme = $actif->get_arme_type();
	if($actif->is_competence("maitrise_$arme"))
		$augmentation['actif']['comp_perso'][] = array("maitrise_$arme", 6 * $rectif_augm);
		
	// Enregistre si on a critiqué
	$actif->precedent['critique'] = $critique;
		*/
    
  	// Application des effets de fin de round
  	foreach($effets as $effet)
    	$effet->fin_round($actif, $passif);
  }
  /**
   * Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function touche(&$actif, &$passif, &$effets)
  {
  }
	// @}
}

?>
