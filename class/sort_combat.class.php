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
      case 'debuff_enracinement':
        return new sort_combat_enracinement($row);
      case 'heresie_divine':
        return new sort_combat_debuff($row, 'debuff_antirez');
      case 'encombrement_psy':
        $buff = new buff(0, 0, $type, 8, $row['effet2'],
          $row['duree'], time()+$row['duree'], $row['nom'], 1, $row['effet']);
        return new sort_combat_debuff($row, $buff);
      case 'tsunami':
        return new sort_combat_tsunami($row);
      case 'empalement_abomination':
        return new sort_combat_empalement($row);
      case 'cri_abomination':
        return new sort_combat_cri_abom($row);
      case 'nostalgie_karn':
        return new sort_combat_nostalgie($row);
      case 'absorb_temporelle':
        return new sort_combat_absorb($row);
      case 'sphere_glace':
      case 'embrasement':
        return new sort_combat_degat_etat($row);
      case 'degat_froid': // à modifier
        return new sort_combat_degat_etat($row, 'a-glacer');
      case 'degat_terre':
        return new sort_combat_degat_etat($row, null, true);
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
        return new sort_combat_aura($row, 'posture_feu', 'Une enveloppe de feu entoure');
      case 'aura_glace':
        return new sort_combat_aura($row, 'posture_glace', 'Une enveloppe de glace entoure');
      case 'aura_vent':
        return new sort_combat_aura($row, 'posture_vent', 'Des tourbillons d\'air entourent');
      case 'aura_pierre':
        return new sort_combat_aura($row, 'posture_pierre', 'De solides pierres volent autour de');
      default:
        return new sort_combat($row);
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
   * Renvoie le coût en RM/MP de la compétence ou du sort, en prennant en compte l'affinité
   * @param   entité lançant le sort ou la compétence
   */
  function get_cout_mp(&$actif)
  {
    return round( $this->get_mp() * $actif->get_affinite( $this->get_comp_assoc() ) );
  }
  /**
   * Méthode gérant l'utilisation d'un sort
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function lance(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    global $log_combat, $G_round_total;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
  	$augmentation = array('actif' => array('comp' => array(), 'comp_perso' => array()),
													'passif' => array('comp' => array(), 'comp_perso' => array()));
													
  	//Réctification si c'est un orc ou un donjon
  	$round = ($actif->get_y() > 190) ? $G_round_total * 2 : $G_round_total;
  	if ($actif->get_race() == 'orc' || $passif->get_race() == 'orc')
  		$round += 1;
  	$rectif_augm = $round / $G_round_total;

		//$log_combat .= 's'.$this->get_id();
    $attaque->add_log_combat('s'.$this->get_id());
  	// Application des effets de début de round
  	/*foreach($effets as $effet)
  		$effet->debut_round($actif, $passif);*/
    $attaque->applique_effet('debut_round');

    // Chances de lancer le sort
    $potentiel_magique = $actif->get_potentiel_lancer_magique( $this->get_comp_assoc() );
  	$de_pot = 300;
  	$de_diff = 300;
  	if($potentiel_magique > $this->get_difficulte()) $de_pot += $potentiel_magique - $this->get_difficulte();
  	else $de_diff += $this->get_difficulte() - $potentiel_magique;
    if( $this->test_potentiel($de_pot, $de_diff) )
    {
      $this->action($attaque);
    }
    else
  	{
  		echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' rate le lancement de '.$this->get_nom().'</span><br />';
  		//$log_combat .= "~l";
      $attaque->add_log_combat('~l');
  	}
  	
  	$passif->precedent['esquive'] = false;
    $passif->precedent['bloque'] = false;
  	$actif->precedent['critique'] = false;
    $actif->precedent['touche'] = false;
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
  function action(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    global $log_combat;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
	  // Calcul de la PM
		$pm = $passif->get_pm();
		// Application des effets de PM
		/*foreach($effets as $effet)
			$pm = $effet->calcul_pm($actif, $passif, $pm);*/
    $attaque->applique_effet('calcul_pm', $pm);
		
		
		// Calcul des potentiels toucher et parer
		$potentiel_toucher = round($actif->get_volonte() * $actif->get_potentiel_lancer_magique( $this->get_comp_assoc() ));
		$potentiel_parer = $passif->get_potentiel_parer_magique($pm);
		// Application des effets de potentiel toucher
		/*foreach($effets as $effet)
			$potentiel_toucher = $effet->calcul_attaque_magique($actif, $passif, $potentiel_toucher);*/
    $attaque->applique_effet('calcul_attaque_magique', $potentiel_toucher);
		// Application des effets de potentiel parer
		/*foreach($effets as $effet)
			$potentiel_parer = $effet->calcul_defense_magique($actif, $passif, $potentiel_parer);*/
    $attaque->applique_effet('calcul_defense_magique', $potentiel_parer);
			
    if( $this->test_potentiel($potentiel_toucher, $potentiel_parer) )
      $this->touche($attaque);
    else
		{
			echo '&nbsp;&nbsp;<span class="manque">'.$actif->get_nom().' manque la cible avec '.$this->get_nom().'</span><br />';
			//$log_combat .= "~m";
      $attaque->add_log_combat('~m');
		}
  }

  /**
   * Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   */
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    global $log_combat;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    $attaque->set_degats( $this->get_effet() + $actif->get_buff('buff_surpuissance', 'effet') /*+ $this->bonus_degats($attaque)*/ );
    $attaque->applique_effet('calcul_bonus_degats_magiques');
    /*$degats = */$this->calcul_degats($attaque);
    $attaque->set_type_degats($this->get_type());
    // Application des effets de degats magiques
    /*foreach($effets as $effet)
      $effet->inflige_degats_magiques($actif, $passif, $degats, $this->get_type());*/
    $attaque->applique_effet('inflige_degats_magiques');
    $degats = $attaque->get_degats();
    echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degats.'</strong> dégâts avec '.$this->get_nom().'</span><br />';
    //$log_combat .= "~".$degats;
    $attaque->add_log_combat('~'.$degats);
    $passif->set_hp($passif->get_hp() - $degats);
		//return $degats;
  }

  /**
   * Méthode calculant les bonus aux dégâts avant réduction
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $effets  Effets
   * @deprecated
   */
  function bonus_degats(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
		$bonus_degats_magique = 0;
		// Application des effets de degats magiques
		/*foreach($effets as $effet)
			$bonus_degats_magique = $effet->calcul_bonus_degats_magiques($actif, $passif, $bonus_degats_magique, $this->get_type());*/
    $attaque->applique_effet('calcul_bonus_degats_magiques');
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
  function calcul_degats(/*&$actif, &$passif, &$effets, $degat*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    $degat = $attaque->get_degats();
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
    $attaque->set_degats( $this->lance_des($de_degat) );
    $this->critiques($attaque);
    //Diminution des dégâts grâce à l'armure magique
    $reduction = $this->calcul_pp(($passif->get_pm() * $passif->get_puissance()) / 12);
    $degat_avant = $attaque->get_degats();
    $degat = round($degat_avant * $reduction);
    if($degat < $degat_avant)
      echo '&nbsp;&nbsp;<span class="small">(Réduction de '.($degat_avant - $degat).' dégâts par la PM)</span><br />';

    $attaque->set_degats($degat);
    // Application des modifications des dégâts
    /*foreach($effets as $effet)
      $degat = $effet->calcul_degats_magiques($actif, $passif, $degat, $this->get_type());*/
    $attaque->applique_effet('calcul_degats_magiques');

    //return $degat;
  }

  /**
   * Méthode gérant les coups critiques
   * @param  $actif   Personnage utuilisant la coméptence
   * @param  $passif  Personnage adverse
   * @param  $degat   Facteur de dégats de base.
   */
  function critiques(/*&$actif, &$passif, $degat*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $degat = $attaque->get_degats();
  	global $debugs, $log_combat;  // Numéro des informations de debug.
  	// Calcule des chances de critique
  	$actif_chance_critique = ($actif->get_volonte() * 50);
  	if(array_key_exists('buff_furie_magique', $actif->buff))
      $actif_chance_critique = $actif_chance_critique  * (1 + ($actif->get_buff('buff_furie_magique', 'effet') / 100));
  	//$debugs++;
  	if($this->test_de(10000, $actif_chance_critique))
  	{
   	  $actif->set_compteur_critique();
  		echo '&nbsp;&nbsp;<span class="coupcritique">SORT CRITIQUE !</span><br />';
  		//$log_combat .= '!';
      $attaque->add_log_combat('!');
    	//Les dégâts des critiques sont diminués par la puissance
    	$puissance = 1 + ($passif->get_puissance() * $passif->get_puissance() / 1000);
      if(array_key_exists('buff_furie_magique', $actif->buff))
    	 $degat *= 2 + $actif->get_buff('buff_furie_magique', 'effet2') / 100;
      else
    	 $degat *= 2;
    	$degat_avant = $degat;
    	$degat = round($degat / $puissance);
    	echo '&nbsp;&nbsp;<span class="small">(Réduction de '.($degat_avant - $degat).' dégâts critique par la puissance)</span><br />';
  	}

    $attaque->set_degats($degat);
    return $degat;
  }
	// @}
}

/// Classe gérant les sorts à état en plus des dégâts
class sort_combat_degat_etat extends sort_combat
{
  protected $etat; ///< État à ajouter si le sort touche
  protected $effet_etat; ///< Effet de l'état (null s'il faut prendre le paramètre effet2, true s'il faut additionner la valeur actuelle à effet2)
  protected $duree_etat; ///< Durée de l'état (null s'il faut prendre le paramètre duree)
  function __construct($tbl, $etat=null, $effet=null,$duree=null)
  {
    $this->charger($tbl);
    $this->etat = $etat;
    $this->effet_etat = $effet;
    $this->duree_etat = $duree;
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    parent::touche($attaque);
    $this->ajout_etat($attaque);
  }
  
  /// Ajoute l'état
  protected function ajout_etat(/*&$actif, &$passif*/&$attaque)
  {
    if( $this->etat === null )
      $etat = $this->get_etat_lie();
    else
      $etat = $this->etat;
    $etat_explode = explode('-', $etat);
		$qui = $etat_explode[0];
		$etat = $etat_explode[1];
		if( $qui[0] == 'v' )
      $cible = &$attaque->get_actif();
    else
      $cible = &$attaque->get_passif();
    if( $this->effet_etat === null )
      $cible->etat[$etat]['effet'] = $this->defaut_effet();
    else if( $this->effet_etat === true )
      $cible->etat[$etat]['effet'] += $this->defaut_effet();
    else
      $cible->etat[$etat]['effet'] = $this->effet_etat;
		if( $this->duree_etat === null )
      $cible->etat[$etat]['duree'] =  $this->get_duree();
    else
      $cible->etat[$etat]['duree'] = $this->duree_etat;
    $this->ajout_effet2($etat, $cible);
  }

  /// récupére la valeur par défaut de l'état
  protected function defaut_effet()
  {
    return $this->get_effet2();
  }

  /// ajoute un effet2 si besoin
  protected function ajout_effet2($etat, &$cible)
  {
  }
}

/// Classe gérant les sorts à état sans dégâts
class sort_combat_etat extends sort_combat_degat_etat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function action(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    if( $this->get_cible() == comp_sort::cible_perso )
    {
      self::touche($attaque);
    }
    else
    {
      parent::action($attaque);
    }
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $this->ajout_etat($attaque);
  	echo '&nbsp;&nbsp;<strong>'.$attaque->get_actif()->get_nom().'</strong> lance le sort '.$this->get_nom().'<br />';
  }

  /// récupére la valeur par défaut de l'état
  protected function defaut_effet()
  {
    return $this->get_effet();
  }

  /// ajoute un effet2 si besoin
  protected function ajout_effet2($etat, &$cible)
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
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    parent::touche($attaque);
		// On regarde s'il y a un gain de PA
		if( $this->test_de(100, $this->get_effet2()) )
		{
      $actif = $attaque->get_actif();
			echo '&nbsp;&nbsp;<strong>'.$actif->get_nom().'</strong> gagne 1 PA<br />';
			$actif->set_pa($actif->get_pa() + 1);
		}
  }
}

/// Classe gérant les sorts sacrifice morbide
class sort_combat_sacrifice extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    parent::touche($attaque);
	$attaque->get_actif()->set_hp(0);
  }
}

/// Classe gérant les sorts lapidation
class sort_combat_lapidation extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    parent::touche($attaque);
		// On regarde si la cible est étourdie
		if( $this->test_de(100, $this->get_effet2()) )
		{
      $passif = &$attaque->get_passif();
			$passif->etat['etourdit']['effet'] = 1;
			$passif->etat['etourdit']['duree'] = $this->get_duree();
		}
  }
}

/// Classe gérant les sorts globe de foudre
class sort_combat_foudre extends sort_combat
{
  /// Méthode calculant les dégâts
  function calcul_degats(/*&$actif, &$passif, &$effets, $degat*/&$attaque)
  {
    $degat = parent::calcul_degats($attaque);
		// On ajoute pas a la stack d'effet car on a besoin de savoir tout de suite si la foudre passe ou pas pour le +1 degats
		$foudre = new globe_foudre(15, true);
		if ($foudre->magnetise($attaque) == false)
			//$degat++;
      $attaque->add_degats(1);
		//return $degat;
  }
}

/// Classe gérant les sorts pacte de sang
class sort_combat_sang extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
		$cout_hp = ceil($actif->get_hp_max() * $this->get_effet2() / 100);
		// On vérifie que le personnage a assez de HP (sinon on ne fait rien)
		if($cout_hp < $actif->get_hp())
		{
			$actif->set_hp($actif->get_hp() - $cout_hp);
			parent::touche($attaque);
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
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
		parent::touche($attaque);
		if ($attaque->get_passif()->get_type() != 'batiment')
		{
      $drain = round($attaque->get_degats() * $this->drain);
      echo 'Et gagne <strong>'.$drain.'</strong> hp grâce au drain<br />';
      $actif->set_hp($actif->get_hp() + $drain);
			// On vérifie que le personnage n'a pas plus de HP que son maximum
			if($actif->get_hp() > floor($actif->get_hp_max())) $actif->set_hp($actif->get_hp_max());
			$actif->sauver(); // étrange qu'il faille faire ça !
    }
  }
}

/// Classe gérant les sorts vortex de mana
class sort_combat_vortex_mana extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
		$degats = parent::touche($attaque);
		if ($attaque->get_passif()->get_type() != 'batiment')
		{
      $drain = round($degats * .2);
      echo 'Et gagne <strong>'.$drain.'</strong> RM grâce au drain</span><br />';
      $actif->set_rm_restant($actif->get_rm_restant() + $drain);
    }
  }
}

/// Classe gérant les sorts brisement d'os
class sort_combat_bris_os extends sort_combat
{
  /// Méthode calculant les dégâts
  function calcul_degats(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    /*$degat = */parent::calcul_degats($attaque);
		if($attaque->get_passif()->etat['paralysie']['duree'] > 0)
      //$degat = round($degat * 1.6);
      $attaque->mult_attaque(1.6);
		//return $degat;
  }
}

/// Classe gérant les sorts brulure de mana
class sort_combat_brul_mana extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $passif = &$attaque->get_passif();
		$brule_mana = $this->get_effet();
		$degat = $this->get_effet() * $this->get_effet2();
		echo '&nbsp;&nbsp;<span class="degat"><strong>'.$attaque->get_actif()->get_nom().'</strong> retire '.$brule_mana.' réserve de mana et inflige <strong>'.$degat.'</strong> dégâts avec '.$this->get_nom().'</span><br />';
		$passif->set_hp($passif->get_hp() - $degat);
		$passif->set_rm_restant($passif->get_rm_restant() - $brule_mana);
  }
}

/// Classe gérant les sorts silence
class sort_combat_silence extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
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
  protected function ajout_effet2($etat, &$cible)
  {
    $cible->etat[$etat]['hp_max'] = $cible->get_hp();
    $cible->etat[$etat]['hp_recup'] = 0;
  }
}

/// Classe gérant les sorts à état sans dégâts
class sort_combat_aura extends sort_combat_etat
{
  protected $posture; ///< Type de posture
  protected $message; ///< Message lors du lancement de la posture.
  function __construct($tbl, $posture, $message)
  {
    parent::__construct($tbl);
    $this->posture = $posture;
    $this->message = $message;
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function action(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $this->ajout_etat($attaque);
		echo '&nbsp;&nbsp;'.$this->message.' <strong>'.$attaque->get_actif()->get_nom().'</strong> !<br />';
  }
  /// ajoute un effet2 si besoin
  protected function ajout_effet2($etat, &$cible)
  {
    $cible->etat[$etat]['type'] = $this->posture;
  }
}

/// Classe gérant les sorts lançant un débuff
class sort_combat_debuff extends sort_combat
{
  protected $debuff; ///< Débuff
  function __construct($tbl, $debuff=null)
  {
    parent::__construct($tbl);
    if( !is_object($debuff) )
    {
      if( func_num_args() == 1 )
        $type = $this->get_type();
      else
        $type = $debuff;
      $this->debuff = new buff(0, 0, $type, $this->get_effet(), $this->get_effet2(),
        $this->get_duree(), time()+$this->get_duree(), $this->get_nom(), 1, 0);
    }
    else
      $this->debuff = $debuff;
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $passif = &$attaque->get_passif();
		parent::touche($attaque);
		if( $passif->lance_debuff( $this->debuff ) )
      echo '<strong>'.$passif->get_nom().'</strong> est affecté par le debuff '.$this->debuff->get_nom().'<br/>';
  }
}

/// Classe gérant les sorts lançant un débuff si un test réussi
class sort_combat_enracinement extends sort_combat_debuff
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $passif = &$attaque->get_passif();
		sort_combat::touche($attaque);
		$att = $this->get_effet2();
		$def = $passif->get_dexterite() + $passif->get_force();
		if( $this->test_potentiel($this->potentiel_att, $this->potentiel_def) )
		{
  		if( $passif->lance_buff( $this->debuff ) )
        echo '<strong>'.$passif->get_nom().'</strong> est affecté par le debuff '.$this->debuff->get_nom().'<br/>';
    }
  }
}

/// Classe gérant les sorts lançant un débuff
class sort_combat_tsunami extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/$attaque)
  {
		parent::touche($attaque);
		self::projection($attaque, $this->get_effet2());
  }
  
  protected static function projection(/*&$actif, &$passif, $effet*/&$attaque, $effet)
  {
    global $db;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();

    // Choose direction: 1->N 2->E 3->S 4->W
    $ax = $actif->get_x();
    $ay = $actif->get_y();
    $px = $passif->get_x();
    $py = $passif->get_y();
    if ($ax == $px && $ay == $py) $direction = rand(1, 4);
    elseif ($ax == $px) $direction = ($ay < $py) ? 1 : 3;
    elseif ($ay == $py) $direction = ($ax < $px) ? 2 : 4;
    else {
      $p = array();
      if ($ay > $py) $p[] = 3;
      if ($ay < $py) $p[] = 1;
      if ($ax > $px) $p[] = 4;
      if ($ax < $px) $p[] = 2;
      shuffle($p);
      $direction = array_pop($p);
    }

    print_debug("projection vers: $direction");
    $translation = 0;
    $continue_projection = true;
    do
    {
      $cur = self::translation($px, $py, $direction);
      $map = $db->query_get_object("select * from map where x = $cur[x] and y = $cur[y]");
      if ($map) {
        $info = type_terrain($map->info);
        $pa = cout_pa($info[0], $passif->get_race());
      } else {
        print_debug("BORD DE CARTE !!");
        $pa = 50;
      }
      if ($pa > 49) {
        // Infranchissable: mur
        $continue_projection = false;
        echo '<span class="degat">&nbsp;&nbsp;'.$passif->get_nom().
          ' est projeté contre un mur et perds '.$effet.
          ' points de vie!<br/></span>';
        $passif->add_hp($effet * -1);
        continue;
      }
      else {
        $px = $cur['x'];
        $py = $cur['y'];
        print_debug("déplacement en: $px/$py");
        if ($translation++ > 1) $continue_projection = false;
      }
      $def = rand(1, $passif->get_force());
      $att = rand(1, 40 / $translation);
      print_debug("resistance à la projection: $def vs $att<br/>");
      if ($def > $att) $continue_projection = false;
    } while ($continue_projection);
    if ($translation > 0) {
      $joueur = $passif->get_objet();
      $joueur->set_x($px);
      $joueur->set_y($py);
      $joueur->sauver();
      print_reload_area('deplacement.php?deplacement=centre', 'centre');
      $row = $db->query_get_object("select * from map_monstre where x = $px and y = $py");
      if ($row) {
        $_SESSION['attaque_donjon'] = 'ok';
        print_js_onload("alert('Vous êtes projeté sur un monstre!'); ".
                        "envoiInfo('attaque.php?type=monstre&".
                        "id_monstre=$row->id', 'information')");
      }
    }
  }

  protected static function translation($x, $y, $direction)
  {
    switch ($direction)
    {
      case 1:
        return array('x' => $x, 'y' => $y - 1);
        break;
      case 2:
        return array('x' => $x + 1, 'y' => $y);
        break;
      case 3:
        return array('x' => $x, 'y' => $y + 1);
        break;
      case 4:
        return array('x' => $x - 1, 'y' => $y);
        break;
    }
    return array('x' => $x, 'y' => $y);
  }
}

/// Classe gérant l'empalement de l'abomination gobelin
class sort_combat_empalement extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
    $effets = &$attaque->get_effets();
    $attaque->set_degats( $this->get_effet() + $this->bonus_degats($attaque) );
    $degat = $this->calcul_degats($attaque);
    // Application des effets de degats magiques
    /*foreach($effets as $effet)
      $effet->inflige_degats_magiques($actif, $passif, $degats, $this->get_type());*/
    $attaque->applique_effet('inflige_degats_magiques');
		echo '&nbsp;&nbsp;<span class="degat"><strong>'.$actif->get_nom().'</strong> inflige <strong>'.$degats.'</strong> dégâts avec '.$this->get_nom().'</span><br />';
		if ($passif->get_hp() > $degat)
    { // Si on survit
			$degat = $passif->get_hp() - 4; // 1 + 3 de LS
    }
		echo '&nbsp;&nbsp;<span class="degat">Une &eacute;pine jaillit de <strong>'.
			$actif->get_nom().'</strong> infligeant <strong>'.$degat.
			'</strong> dégâts, et transpercant '.$passif->get_nom().'</span><br/>';
		$passif->set_hp($passif->get_hp() - $degat);

		if ($passif->get_hp() > 0)
    {
			// On augmente d'un la marque de l'abomination
			$achiev = $passif->get_compteur('abomination_mark');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
    }
  }
}

/// Classe gérant le cri de l'abomination gobelin
class sort_combat_cri_abom extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    global $db;
    $actif = &$attaque->get_actif();
    $passif = &$attaque->get_passif();
		echo '&nbsp;&nbsp;<span class="degat">L\'abomination profère un hurlement terrifiant !</span><br/>';
		$xi = $passif->get_x() - 3;
		$xa = $passif->get_x() + 3;
		$yi = $passif->get_y() - 3;
		$ya = $passif->get_y() + 3;
		$requete_persos = "select id from perso where x >= $xi and x <= $xa and y >= $yi and y <= $ya and hp > 0 and statut = 'actif'";
		$req_persos = $db->query($requete_persos);
		while ($row_persos = $db->read_assoc($req_persos))
		{
			if ($row_persos['id'] == $passif->get_id())	continue;
			$spectateur = new perso($row_persos['id']);
			$rand = rand(0, 20);
			$final = $rand + $spectateur->get_volonte();
			print_debug("Jet de terreur pour ".$spectateur->get_nom().": $rand ($final) vs $row[effet2]<br/>");
			if ($final < $row['effet2'] && $rand != 20)
			{
				echo '<strong>'.$spectateur->get_nom().'</strong> est effray&eacute; par ce spectacle, et se glace de terreur !<br/>';
				lance_buff('debuff_enracinement', $row_persos['id'], '10', '0', 86400, 'Terreur',
									 'Vous etes terroris&eacute; par l\'affreux spectacle du supplice de '.$passif->get_nom(), 'perso', 1, 0, 0, 0);
			}
		}
		echo 'La marque de l\'abomination restera longtemps sur vous ...<br/>';
		$achiev = $passif->get_compteur('abomination_mark');
		if ($achiev->get_compteur() == 0) {
			// Premier combat contre l'abomination
			$achiev->set_compteur(1);
			$achiev->sauver();
		}

		if ($passif->get_hp() > 3)
			lance_buff('debuff_enracinement', $passif->get_id(), '10', '0', 86400, 'Terreur',
								 'Vous etes terroris&eacute; par l\'attaque de la cr&eacute;ature', 'perso', 1, 0, 0, 0);
		lance_buff('lente_agonie', $passif->get_id(), 1, 0, 2678400, 'Marque de l\\\'abomination',
							 'Les blessures engendrées par l\'épine de l\'abomination vous laissent dans une souffrance atroce. Il vous faudra du temps pour vous en remettre',
							 'perso', 1, 0, 0, 0);
  }
}

/// Classe gérant lla osalgie de Karn
class sort_combat_nostalgie extends sort_combat_debuff
{
  function __construct($tbl)
  {
    $debuff = $this->debuff = new buff(0, 0, 'maladie_degenerescence', $this->get_effet2(), 0,
        $this->get_duree(), time()+$this->get_duree(), $this->get_nom(), 1, 0);
    parent::__construct($tbl, $debuff);
  }
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $description = 'Vous sentez votre esprit vieillir, vous ne pensez quʼaux moments où vous étiez en pleine santé et vous avez du mal a vous concentrer';
		parent::touche($attaque);
		echo '<br/><em>'.$description.'</em>';
  }
}

/// Classe gérant l'absorption temporelle
class sort_combat_absorb extends sort_combat
{
  /// Méthode gérant ce qu'il se passe lorsque la coméptence à été utilisé avec succès
  function touche(/*&$actif, &$passif, &$effets*/&$attaque)
  {
    $passif = &$attaque->get_passif();
    $description = 'Vous êtes complétement déstabilisé et ne voyez plus rien pendant quelques secondes. En revenant à vous, vous avez la douloureuse impression que vos gestes vous ont échappé.';
		$perte_pa = rand(1, $row['effet2']);
		$pa = max(0, $passif->get_pa() - $perte_pa);
		$passif->set_pa($pa);
		parent::touche($attaque);
		print_debug($passif->get_nom().' perd '.$perte_pa.' PA');
		echo '<br/><em>'.$description.'</em>';
  }
}
?>
