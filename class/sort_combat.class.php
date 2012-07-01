<?php
/**
 * @file sort_combat.class.php
 * Définition de la classe sort_combat servant de base aux sorts de combat
 */

/**
 * Classe sort_combat
 * Classe sort_combat servant de base aux sorts de combat
 */
class sort_combat extends sort
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
  protected $etat_lie;  ///< État lié au sort.

  /// Renvoie l'état lié au sort
	function get_etat_lie()
	{
		return $this->etat_lie;
	}
	/// Modifie l'état lié au sort
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
	 * @param incantation    Requis en incantation
	 * @param difficulte     Difficulté de lancé
	 * @param etat_lie       État lié au sort
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $incantation=0, $difficulte=0, $etat_lie='')
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      sort::__construct($id, $nom, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis,
        $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $incantation, $difficulte, $etat_lie);
			$this->etat_lie = $etat_lie;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    sort::init_tab($vals);
		$this->etat_lie = $vals['etat_lie'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return sort::get_liste_champs().', etat_lie';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return sort::get_valeurs_insert().', "'.$this->etat_lie.'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return sort::get_liste_update().', etat_lie = "'.$this->etat_lie.'"';
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM sort_combat WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      case 'debuff_enracinement': // l. 707
        return new sort_combat($row);
      case 'heresie_divine': // l. 724
        return new sort_combat($row);
      case 'encombrement_psy': // l.
        return new sort_combat($row);
      case 'tsunami': // l. 751
        return new sort_combat($row);
      case 'empalement_abomination': // l. 759
        return new sort_combat($row);
      case 'cri_abomination': // l. 776
        return new sort_combat($row);
      case 'nostalgie_karn': // l. 814
        return new sort_combat($row);
      case 'absorb_temporelle': // l. 827
        return new sort_combat($row);
      case 'degat_froid': // à modifier
      case 'degat_terre':
      case 'sphere_glace':
      case 'embrasement':
        return new sort_combat_degat_etat($row);
      case 'degat_vent':
        return new sort_combat_vent($row);
      case 'sacrifice_morbide':
        return new sort_combat_sacrifice($row);
      case 'lapidation':
        return new sort_combat_lapidation($row);
      case 'globe_foudre': // à modifier
        return new sort_combat_foudre($row);
      case 'pacte_sang':
        return new sort_combat_sang($row);
      case 'drain_vie':
        return new sort_combat_drain($row, .3);
      case 'vortex_vie':
        return new sort_combat_drain($row, .4);
      case 'vortex_mana':
        return new sort_combat_vortex_mana($row);
      case 'putrefaction':
        return new sort_combat_degat_etat($row, 'a-putrefaction', 2, 1);
      case 'brisement_os':
        return new sort_combat_bris_os($row);
      case 'brulure_mana':
        return new sort_combat_brul_mana($row);
      case 'appel_tenebre':
      case 'appel_foret':
      case 'benediction':
      case 'paralysie':
      case 'lien_sylvestre':
      case 'poison':
      case 'jet_acide':
      case 'riposte_furtive':
        return new sort_combat_etat($row);
      case 'silence':
        return new sort_combat_silence($row);
      case 'recuperation':
        return new sort_combat_recuperation($row);
      case 'aura_feu':
        return new sort_combat_aura($row, 'posture_feu');
      case 'aura_glace':
        return new sort_combat_aura($row, 'posture_glace');
      case 'aura_vent':
        return new sort_combat_aura($row, 'posture_vent');
      case 'aura_pierre':
        return new sort_combat_aura($row, 'posture_pierre');
      }
  	}
  }
	// @}


	/**
	 * @name Gestion du lancement
	 * Méthodes utilisées lors de l'utilisation (lancement) de la compétence / du sort
	 */
  // @{
  /**
   * Méthode gérant l'utilisation d'un sort
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function lance(&$actif, &$passif, &$effets)
  {
    global /*$attaquant, $defenseur, */$log_combat, /*$log_effects_attaquant, $log_effects_defenseur,*/ $G_round_total;
  	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()),
													'passif' => array('comp' => array(), 'comp_perso' => array()));
													
  	//Réctification si c'est un orc ou un donjon
  	$round = is_donjon($actif->get_x(), $actif->get_y()) ? $G_round_total * 2 : $G_round_total;
  	if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
  		$round += 1;
  	$rectif_augm = $round / $G_round_total;

		$log_combat .= 's'.$this->get_id();
  	// Application des effets de début de round
  	foreach($effets as $effet)
  		$effet->debut_round($actif, $passif);

    // Chances de lancer le sort
    $potentiel_magique = $actif->get_potentiel_lancer_magique( $this->get_comp_assoc() );
  	$de_pot = 300;
  	$de_diff = 300;
  	if($potentiel_magique > $this->get_difficulte()) $de_pot += $potentiel_magique - $this->get_difficulte();
  	else $de_diff += $this->get_difficulte() - $potentiel_magique;
    if( $this->test_potentiel($de_pot, $de_diff) )
    {
      $this->action($actif, $passif, $effets);
    }
    else
  	{
  		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' rate le lancement de '.$this->get_nom().'</span><br />';
  		$log_combat .= "~l";
  	}
  	
  	//Augmentation des compétences liées
  	$get = 'get_'.$this->get_comp_assoc();

  	$augmentation['actif']['comp'][] = array($this->get_comp_assoc(), $rectif_augm * (4.2 * sqrt(pow($actif->$get(), 1.3) / ($this->get_difficulte() / 4))));
  	$augmentation['actif']['comp'][] = array('incantation', $rectif_augm * (5 * sqrt($actif->get_incantation() / ($this->get_difficulte() / 2))));

    return $augmentation;
  }

  /**
   * Méthode gérant l'action du sort
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function action(&$actif, &$passif, &$effets)
  {
    global $log_combat;
	  // Calcul de la PM
		$pm = $passif->get_pm();
		// Application des effets de PM
		foreach($effets as $effet)
			$pm = $effet->calcul_pm($actif, $passif, $pm);
			
		if($passif->is_buff('batiment_pm')) $buff_batiment_barriere = 1 + (($passif->get_buff('batiment_pm', 'effet') / 100)); else $buff_batiment_barriere = 1;
		if($passif->is_buff('debuff_desespoir')) $debuff_desespoir = 1 + (($passif->get_buff('debuff_desespoir', 'effet')) / 100); else 	$debuff_desespoir = 1;
		if($passif->etat['posture']['type'] == 'posture_glace') $aura_glace = 1 + (($passif->etat['posture']['effet']) / 100); else $aura_glace = 1;
		$PM = $pm * $aura_glace * $buff_batiment_barriere;
		
		// Calcul des potentiels toucher et parer
		$potentiel_toucher = round($actif->get_volonte() * $actif->get_potentiel_lancer_magique( $this->get_comp_assoc() ));
		$potentiel_parer = round($passif->get_volonte() * $PM / $debuff_desespoir);
		// Application des effets de potentiel toucher
		foreach($effets as $effet)
			$potentiel_toucher = $effet->calcul_attaque_magique($actif, $passif, $potentiel_toucher);
		// Application des effets de potentiel parer
		foreach($effets as $effet)
			$potentiel_parer = $effet->calcul_defense_magique($actif, $passif, $potentiel_parer);
			
    if( $this->test_potentiel($potentiel_toucher, $potentiel_parer) )
      $this->touche($actif, $passif, $effets);
    else
		{
			echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible avec '.$this->get_nom().'</span><br />';
			$log_combat .= "~m";
		}
  }

  /**
   * Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function touche(&$actif, &$passif, &$effets)
  {
    $degats = $this->calcul_degats($actif, $passif, $effets, $this->get_effet() + $this->bonus_degats($actif, $passif, $effets));
    // Application des effets de degats magiques
    foreach($effets as $effet)
      $effet->inflige_degats_magiques($actif, $passif, $degats, $this->get_type());
		echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degats.'</strong> dégâts avec '.$this->get_nom().'</span><br />';
		$passif->set_hp($passif->get_hp() - $degats);
		return $degats;
  }

  /**
   * Méthode calculant les bonus aux dégâts avant réduction
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function bonus_degats(&$actif, &$passif, &$effets)
  {
		$bonus_degats_magique = 0;
		// Application des effets de degats magiques
		foreach($effets as $effet)
			$bonus_degats_magique = $effet->calcul_bonus_degats_magiques($actif, $passif, $bonus_degats_magique, $this->get_type());
		//$bonus_degats_magique += $facteur_degats_arme;
		$bonus_degats_magique += $actif->get_buff('buff_surpuissance', 'effet');
		return $bonus_degats_magique;
  }

  /**
   * Méthode calculant les dégâts
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   * @param  $degat   Facteur de dégats du sort.
   */
  function calcul_degats(&$actif, &$passif, &$effets, $degat)
  {
    global $db;
    if(isset($actif->enchantement) && isset($actif->enchantement['degat_magie']))
    {
      $requete = "SELECT nom, enchantement_effet FROM gemme WHERE id = ".$actif->enchantement['degat_magie']['gemme_id'];
      $req = $db->query($requete);
      $row = $db->read_assoc($req);
      $degat += $row['enchantement_effet'];
      $dbg_msg .= "La ".$row['nom'].' augmente les dégâts de '. $row['enchantement_effet'].'<br />';
    }

    $get_carac_assoc = 'get_'.$this->get_carac_assoc();
    $de_degat = $this->calcule_des($actif->$get_carac_assoc(), $degat);
    $degat = $this->lance_des($de_degat);
    $degat = $this->critiques($actif, $passif, $degat);
    //Diminution des dégâts grâce à l'armure magique
    $reduction = $this->calcul_pp(($passif->get_pm() * $passif->get_puissance()) / 12);
    $degat_avant = $degat;
    $degat = round($degat * $reduction);
    if($degat > $degat_avant)
      echo '(Réduction de '.($degat_avant - $degat).' dégâts par la PM)<br />';

    // Application des modifications des dégâts
    foreach($effets as $effet)
      $degat = $effet->calcul_degats_magiques($actif, $passif, $degat, $this->get_type());

    return $degat;
  }

  /**
   * Méthode gérant les coups critiques
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $degat   Facteur de dégats de base.
   */
  function critiques(&$actif, &$passif, $degat)
  {
  	global $debugs, $log_combat;  // Numéro des informations de debug.
  	// Calcule des chances de critique
  	$actif_chance_critique = ($actif->get_volonte() * 50);
  	if(array_key_exists('buff_furie_magique', $actif->buff))
      $actif_chance_critique = $actif_chance_critique  * (1 + ($actif->get_buff('buff_furie_magique', 'effet') / 100));
  	$debugs++;
  	if($this->test_de(10000, $actif_chance_critique))
  	{
   	  $actif->set_compteur_critique();
  		echo '&nbsp;&nbsp;<span class="coupcritique">SORT CRITIQUE !</span><br />';
  		$log_combat .= '!';
    	echo '<div id="debug'.$debugs.'" class="debug">';
    	//Les dégâts des critiques sont diminués par la puissance
    	$puissance = 1 + ($passif->get_puissance() * $passif->get_puissance() / 1000);
    	$degat *= 2;
    	$degat_avant = $degat;
    	$degat = round($degat / $puissance);
    	echo '(Réduction de '.($degat_avant - $degat).' dégâts critique par la puissance)<br />
    	</div>';
  	}
    return $degat;
  }
	// @}
}

/// Classe gérant les sorts à état en plus des dégâts
class sort_combat_degat_etat extends sort_combat
{
  protected $etat; ///< État à ajouter si le sort touche
  protected $effet; ///< Effet de l'état (null s'il faut prendre le paramètre effet2)
  protected $duree; ///< Durée de l'état (null s'il faut prendre le paramètre duree)
  function __construct($tbl, $etat=null, $effet=null,$duree=null)
  {
    $this->charger($tbl);
    $this->etat = $etat;
    $this->effet = $effet;
    $this->duree = $duree;
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
    parent::touche($actif, $passif, $effets);
    ajout_etat($actif, $passif);
  }
  
  /// Ajoute l'état
  protected ajout_etat(&$actif, &$passif)
  {
    if( $this->etat === null )
      $etat = $this->get_etat_lie();
    else
      $etat = $this->etat;
    $etat_explode = explode('-', $etat);
		$qui = $etat_explode[0];
		$etat = $etat_explode[1];
		if( $qui[0] = 'v' )
      $cible = &$actif;
    else
      $cible = &$passif;
    if( $this->effet === null )
      $cible->etat[$etat]['effet'] = $this->defaut_effet();
    else
      $cible->etat[$etat]['effet'] = $this->effet;
		if( $this->duree === null )
      $cible->etat[$etat]['duree'] =  $this->get_duree();
    else
      $cible->etat[$etat]['duree'] = $this->duree;
    $this->ajout_effet2($etat, $cible);
  }

  /// récupére la valeur par défaut de l'état
  protected defaut_effet()
  {
    return $this->get_effet2();
  }

  /// ajoute un effet2 si besoin
  protected ajout_effet2($etat, &$cible)
  {
  }
}

/// Classe gérant les sorts à état sans dégâts
class sort_combat_etat extends sort_combat_degat_etat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
    ajout_etat($actif, $passif);
		echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$this->get_nom().'<br />';
  }

  /// récupére la valeur par défaut de l'état
  protected defaut_effet()
  {
    return $this->get_effet();
  }

  /// ajoute un effet2 si besoin
  protected ajout_effet2($etat, &$cible)
  {
    $effet2 = $this->get_effet2();
    if( $effet2 )
      $cible->etat[$etat]['effet2'] = $effet2;
  }
}

/// Classe gérant les sorts cisaillement du vent
class sort_combat_vent extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
    parent::touche($actif, $passif, $effets);
		// On regarde s'il y a un gain de PA
		if( $this->test_de(100, $this->get_effet2()) )
		{
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> gagne 1 PA<br />';
			$actif->set_pa($actif->get_pa() + 1);
		}
  }
}

/// Classe gérant les sorts sacrifice morbide
class sort_combat_sacrifice extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
    parent::touche($actif, $passif, $effets);
		$actif->set_hp(0);
  }
}

/// Classe gérant les sorts lapidation
class sort_combat_lapidation extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
    parent::touche($actif, $passif, $effets);
		// On regarde si la cible est étourdie
		if( $this->test_de(100, $this->get_effet2()) )
		{
			$passif->etat['etourdit']['effet'] = 1;
			$passif->etat['etourdit']['duree'] = $this->get_duree();
		}
  }
}

/// Classe gérant les sorts globe de foudre
class sort_combat_foudre extends sort_combat
{
  /// Méthode calculant les dégâts
  function calcul_degats(&$actif, &$passif, &$effets, $degat)
  {
    $degat = parent::calcul_degats($actif, $passif, $effets, $degat);
		// On ajoute pas a la stack d'effet car on a besoin de savoir tout de suite si la foudre passe ou pas pour le +1 degats
		$foudre = new globe_foudre(15, true);
		if ($foudre->magnetise($actif, $passif) == false)
			$degat++;
		return $degat;
  }
}

/// Classe gérant les sorts pacte de sang
class sort_combat_sang extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
		$cout_hp = ceil($actif->get_hp_max() * $row['effet2'] / 100);
		// On vérifie que le personnage a assez de HP (sinon on ne fait rien)
		if($cout_hp < $actif->get_hp())
		{
			$actif->set_hp($actif->get_hp() - $cout_hp);
			parent::touche($actif, $passif, $effets);
		}
  }
}

/// Classe gérant les sorts drains et vortex de vie
class sort_combat_drain extends sort_combat
{
  protected $drain; ///< Portion de vie drainée
  function __construct($tbl, $drain)
  {
    $this->charger($tbl);
    $this->drain = $drain;
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
		$degats = parent::touche($actif, $passif, $effets);
		if ($passif->get_type() != 'batiment')
		{
      $drain = round($degat * $this->drain);
      echo 'Et gagne <strong>'.$drain.'</strong> hp grâce au drain</span><br />';
      $actif->set_hp($actif->get_hp() + $drain);
			// On vérifie que le personnage n'a pas plus de HP que son maximum
			if($actif->get_hp() > floor($actif->get_hp_max())) $actif->set_hp($actif->get_hp_max());
    }
  }
}

/// Classe gérant les sorts vortex de mana
class sort_combat_vortex_mana extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
		$degats = parent::touche($actif, $passif, $effets);
		if ($passif->get_type() != 'batiment')
		{
      $drain = round($degat * .2);
      echo 'Et gagne <strong>'.$drain.'</strong> RM grâce au drain</span><br />';
      $actif->set_rm_restant($actif->get_rm_restant() + $drain);
    }
  }
}

/// Classe gérant les sorts brisement d'os
class sort_combat_bris_os extends sort_combat
{
  /// Méthode calculant les dégâts
  function calcul_degats(&$actif, &$passif, &$effets, $degat)
  {
    $degat = parent::calcul_degats($actif, $passif, $effets, $degat);
		if($passif->etat['paralysie']['duree'] > 0)
      $degat = round($degat * 1.6);
		return $degat;
  }
}

/// Classe gérant les sorts vortex de mana
class sort_combat_bris_mana extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
		$brule_mana = $this->get_effet();
		$degat = $this->get_effet() * $this->get_effet2();
		echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> retire '.$brule_mana.' réserve de mana et inflige <strong>'.$degat.'</strong> dégâts avec '.$this->get_nom().'</span><br />';
		$passif->set_hp($passif->get_hp() - $degat);
		$passif->set_rm_restant($passif->get_rm_restant() - $brule_mana);
  }
}

/// Classe gérant les sorts vortex de mana
class sort_combat_bris_mana extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(&$actif, &$passif, &$effets)
  {
		// Calcul du potentiel paralyser
		$sm = ($actif->get_volonte() * $actif->get_sort_mort());
		// Calcul du potentiel résister, on utilise bien la PM DE BASE pour le 3eme jet
		$pm = $passif->get_volonte() * $passif->get_pm_para();
		
		// Lancer des dés
		echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> lance le sort '.$this->get_nom().'<br />';
		if( $this->test_potentiel($sm, $pm) )
		{
			echo ' et réussit !<br />';
			$passif->etat['silence']['effet'] = $this->get_effet();
			$passif->etat['silence']['duree'] = $this->get_duree();
		}
		else
		{
			echo ' et échoue...<br />';
		}
  }
}

/// Classe gérant les sorts à état sans dégâts
class sort_combat_recuperation extends sort_combat_etat
{
  /// ajoute un effet2 si besoin
  protected ajout_effet2($etat, &$cible)
  {
    $cible->etat[$etat]['hp_max'] = $cible->get_hp();
    $cible->etat[$etat]['hp_recup'] = 0;
  }
}

/// Classe gérant les sorts à état sans dégâts
class sort_combat_aura extends sort_combat_etat
{
  protected $posture; ///< Type de posture
  function __construct($tbl, $posture)
  {
    parent::__construct($tbl, 'posture');
    $this->posture = $posture;
  }
  /// ajoute un effet2 si besoin
  protected ajout_effet2($etat, &$cible)
  {
    $cible->etat[$etat]['type'] = $this->posture;
  }
}

?>
