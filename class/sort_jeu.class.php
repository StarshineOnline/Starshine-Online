<?php
/**
 * @file comp_jeu.class.php
 * Définition de la classe comp_sort servant de base aux sorts hors combat
 */

/**
 * Classe comp_jeu
 * Classe comp_jeu servant de base aux sorts dhors combat
 */
class sort_jeu extends sort
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $pa;   ///< Coût en PA de la compétence
	protected $portee;   ///< Portée du sort
	protected $special;   ///< Indique si c'est un sort spécial (auquel cas l'affinité ne joue pas)

	/// Renvoie le coût de la comptétence
	function get_pa()
	{
		return $this->pa;
	}
	/// Modifie le coût de la comptétence
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

  /// Renvoie la portée du sort
	function get_portee()
	{
		return $this->portee;
	}
	/// Modifie la portée du sort
	function set_portee($portee, $param = 'set')
	{
		$this->portee = $portee;
		$this->champs_modif[] = 'portee';
	}

  /// Renvoie si c'est un sort spécial
	function get_special()
	{
		return $this->special;
	}
	/// Modifie si c'est un sort spécial
	function set_special($special, $param = 'set')
	{
		$this->special = $special;
		$this->champs_modif[] = 'special';
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
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0, $incantation=0, $difficulte=0, $pa=0, $portee=0, $special=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      sort::__construct($id, $type, $effet, $duree, $comp_assoc, $carac_assoc, $comp_requis, $carac_requis, $effet2, $requis, $cible, $description, $mp, $prix, $lvl_batiment, $incantation, $difficulte);
			$this->pa = $pa;
			$this->portee = $portee;
			$this->special = $special;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    sort::init_tab($vals);
		$this->pa = $vals['pa'];
		$this->portee = $vals['portee'];
		$this->special = $vals['special'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return sort::get_liste_champs().', pa, portee, special';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return sort::get_valeurs_insert().', '.$this->pa.', '.$this->portee.', '.$this->special;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return sort::get_liste_update().', pa = '.$this->pa.', portee = '.$this->portee.', special = '.$this->special;
	}

	/**
	 * Méthode créant l'objet adéquat à partir d'un élément de la base de donnée.
	 * @param id  id de la compétence ou du sort dans la base de donnée
	 */
  static function factory($id)
  {
    global $db;
    $requete = 'SELECT * FROM sort_jeu WHERE id = '.$id;
  	$req = $db->query($requete);
  	$row=$db->read_assoc($req);
  	if( $row )
  	{
      switch( $row['type'] )
      {
      case 'engloutissement':
      case 'deluge':
      case 'blizzard':
      case 'orage_magnetique':
      case 'debuff_aveuglement':
      case 'debuff_enracinement':
      case 'debuff_desespoir':
      case 'debuff_ralentissement':
      case 'lente_agonie':
      case 'maladie_amorphe':
      case 'maladie_degenerescence':
      case 'maladie_mollesse':
      case 'debuff_antirez':
        return new sort_debuff($row);
      case 'vie_pourcent':
        return new sort_vie_pourcent($row);
      case 'vie':
        return new sort_vie($row);
      case 'balance':
        return new sort_balance($row);
      case 'body_to_mind':
        return new sort_body_to_mind($row);
      case 'teleport':
        return new sort_teleport($row);
      case 'repos_sage':
        return new sort_repos_sage($row);
      case 'guerison':
        return new sort_guerison($row);
      case 'esprit_sacrifie':
        return new sort_esprit_sacrifie($row);
      case 'transfert_energie':
        return new sort_transfert_energie($row);
      case 'liberation':
        return new sort_liberation($row);
      case 'rez':
        return new sort_rez($row);
      default:
        return new sort_jeu($row);
      }
  	}
  }
	// @}

  /**
   * Renvoie le coût en RM/MP de la compétence ou du sort, en prennant en compte l'affinité
   * @param   entité lançant le sort ou la compétence
   */
  function get_cout_mp(&$actif)
  {
    return round( $this->get_mp() * $actif->get_affinite( $this->get_comp_assoc() ) );
  }
	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible   Cible du sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
		{
			//Mis en place du buff
			if(lance_buff($this->get_type(), $cible->get_id(), $this->get_effet(), $this->get_effet2(), $this->get_duree(), $this->get_nom(), $this->get_description(true), $cible->get_race()=='neutre'?'monstre':'perso', 0, $cible->get_nb_buff(), $cible->get_grade()->get_nb_buff()))
			{
				$action = true;
				echo $cible->get_nom().' a bien reçu le buff<br />';
				//Insertion du buff dans le journal du receveur
				if( $cible->get_id() != $perso->get_id() && $cible->get_race() != 'neutre' )
				{
  				$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", '".($groupe?'rgbuff':'rbuff')."', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', 0, 0, 0)";
  				$db->query($requete);
				}
			}
			else
			{
				if($G_erreur == 'puissant') echo $cible.' bénéficie d\'un buff plus puissant<br />';
				else echo $cible->get_nom().' a trop de buffs.<br />';
			}
		}
    if($action)
    {
      if($groupe)
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'gbuff', '".$perso->get_nom()."', 'groupe', NOW(), '".$this->get_nom()."', 0, 0, 0)";
        $db->query($requete);
      }
      else
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'buff', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), '".$this->get_nom()."', 0, ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
    }
		return $action;
  }
}

class sort_debuff extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $cibles = $this->get_liste_cibles( $cible );
    foreach($cibles as $cible)
    {
      //Si c'est pas le joueur
      if($cible->get_id() != $perso->get_id())
      {
        //Test d'esquive du sort
        $protection = $cible->get_volonte() * $cible->get_pm() / 3;
        if($cible->is_buff('bulle_sanctuaire', true)) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
        if($cible->is_buff('bulle_dephasante', true)) $protection *= $cible->get_buff('bulle_dephasante','effet');
        $puissance = $perso->get_volonte() * $perso->get_comp($this->get_comp_assoc());
        $attaque = rand(0, $puissance);
        $defense = rand(0, $protection);
        print_debug("Lance sort: $attaque ($puissance) vs $defense ($protection)");
        if ($attaque > $defense)
        {
          $duree = $this->get_duree();
          if( $soufr_ext = $perso->get_buff('souffrance_extenuante') )
            $duree *= $soufr_ext->get_effet();
          //Mis en place du debuff pour tous
          if(lance_buff($this->get_type(), $cible->get_id(), $this->get_effet(), $this->get_effet2(), $duree, $this->get_nom(), $this->get_description(true), $cible->get_race()=='neutre'?'monstre':'perso', 1, 0, 0))
          {
            echo 'Le sort '.$this->get_nom().' a été lancé avec succès sur '.$cible->get_nom().'<br />';
            //Insertion du debuff dans les journaux des 2 joueurs
            if ($cible->get_race() != 'neutre')
            {
              $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'debuff', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), '".$this->get_nom()."', 0, ".$perso->get_x().", ".$perso->get_y().")";
              $db->query($requete);
              $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rdebuff', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', 0, ".$perso->get_x().", ".$perso->get_y().")";
              $db->query($requete);

              if($this->get_type() == "debuff_enracinement")
              {
                // Augmentation du compteur de l'achievement
                $achiev = $cible->get_compteur('nbr_enracine');
                $achiev->set_compteur($achiev->get_compteur() + 1);
                $achiev->sauver();
              }
              elseif($this->get_type() == "lente_agonie")
              {
                // Augmentation du compteur de l'achievement
                $achiev = $perso->get_compteur('nbr_lenteagonie');
                $achiev->set_compteur($achiev->get_compteur() + 1);
                $achiev->sauver();
              }
            }
          }
          else
          {
            echo 'Il bénéficie d\'un debuff plus puissant.<br />';
          }
          //Suppression de MP pour orage magnétique
          if($this->get_type() == 'orage_magnetique')
          {
            if($cible->is_buff('orage_magnetique', true))
            {
              echo $cible->get_nom().' est déjà sous cet effet.<br />';
            }
            else
            {
              //Réduction des mp de la cible
              $cible->set_mp($cible->get_mp() - ($cible->get_mp_maximum() * $this->get_effet() / 100));
              if($cible->get_mp() < 0) $cible->set_mp(0);
              $cible->sauver();
            }
          }
        }
        else
        {
          echo $cible->get_nom().' résiste à votre sort !<br />';
        }
      }
    }
    return true;
  }
}

class sort_vie_pourcent extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $soin_total = 0;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
    {
      if ($cible->get_hp() <= 0) continue;
      $soin = floor($cible->get_hp_maximum() * 0.05);
      if($soin > (floor($cible->get_hp_maximum()) - $cible->get_hp()))
      $soin = floor($cible->get_hp_maximum()) - $cible->get_hp();
      if ($soin == 0) continue;
      $action = true;
      echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
      $soin_total += $soin;
      $cible->set_hp($cible->get_hp() + $soin);
      $cible->sauver();

      // Augmentation du compteur de l'achievement
      $achiev = $perso->get_compteur('total_heal');
      $achiev->set_compteur($achiev->get_compteur() + $soin);
      $achiev->sauver();

      // Augmentation du compteur de l'achievement
      $achiev = $perso->get_compteur('nbr_heal');
      $achiev->set_compteur($achiev->get_compteur() + 1);
      $achiev->sauver();

      if ($groupe)
      {
        //Insertion du soin de groupe dans le journal de la cible
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$soin.", 0, ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
      else if($cible->get_id() != $perso->get_id())
      {
        //Insertion du soin de groupe dans le journal de la cible
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$soin.", 0, ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
    }

    if($action)
    {
      //Insertion du soin de groupe dans le journal du lanceur
      if($groupe)
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'gsoin', '".$perso->get_nom()."', 'groupe', NOW(), ".$soin_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
      }
      else
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'soin', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), ".$soin_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
      }
      $db->query($requete);
    }

    if($groupe) $groupe_href = '&amp;groupe=yes';
    else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
    echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.$lanceur_url.'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
    return $action;
  }
}

class sort_vie extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $soin_total = 0;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
    {
      if($cible->get_hp() > 0)
      {
        if($cible->get_hp() < floor($cible->get_hp_maximum()))
        {
          $action = true;
          $de_degat_sort = de_soin($perso->get_comp($this->get_carac_assoc()), $this->get_effet());
          $i = 0;
          $de_degat_sort2 = array();
          while($i < count($de_degat_sort))
          {
            $de_degat_sort2[$de_degat_sort[$i]] += 1;
            $i++;
          }
          $i = 0;
          $keys = array_keys($de_degat_sort2);
          while($i < count($de_degat_sort2))
          {
            if ($i > 0) echo ' + ';
            echo $de_degat_sort2[$keys[$i]].'D'.$keys[$i];
            $i++;
          }
          echo '<br />';
          $soin = 0;
          $i = 0;
          while($i < count($de_degat_sort))
          {
            $soin += rand(1, $de_degat_sort[$i]);
            $i++;
          }
          if($soin > (floor($cible->get_hp_maximum()) - $cible->get_hp())) $soin = floor($cible->get_hp_maximum()) - $cible->get_hp();
          echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
          $soin_total += $soin;

          $cible->set_hp($cible->get_hp() + $soin);
          $cible->sauver();

          // Augmentation du compteur de l'achievement
          $achiev = $perso->get_compteur('total_heal');
          $achiev->set_compteur($achiev->get_compteur() + $soin);
          $achiev->sauver();

          // Augmentation du compteur de l'achievement
          $achiev = $perso->get_compteur('nbr_heal');
          $achiev->set_compteur($achiev->get_compteur() + 1);
          $achiev->sauver();

          if($groupe)
          {
            //Insertion du soin de groupe dans le journal de la cible
            $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$soin.", 0, ".$perso->get_x().", ".$perso->get_y().")";
            $db->query($requete);
          }
          else if($cible->get_id() != $perso->get_id())
          {
            //Insertion du soin de groupe dans le journal de la cible
            $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$soin.", 0, ".$perso->get_x().", ".$perso->get_y().")";
            $db->query($requete);
          }
        }
        else
        {
          echo 'La cible a toute sa vie<br />';
        }
      }
      else
      {
        echo $cible->get_nom().' est mort.<br />';
      }
    }
    if($action)
    {
      //Insertion du soin de groupe dans le journal du lanceur
      if($groupe)
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'gsoin', '".$perso->get_nom()."', 'groupe', NOW(), ".$soin_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
      }
      else
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'soin', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), ".$soin_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
      }
      $db->query($requete);
    }
    if($groupe) $groupe_href = '&amp;groupe=yes'; else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
    echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.$lanceur_url.'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
    return $action;
  }
}

class sort_balance extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    $nbr_membre = 0;
    $total_pourcent = 0;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
    {
      $cible->check_perso(false);
      if($cible->get_hp() > 0)
      {
        $total_pourcent += $cible->get_hp() / $cible->get_hp_max();
        $nbr_membre++;
      }
    }
    $pourcent = $total_pourcent / $nbr_membre;
    print_debug("équilibrage: $pourcent");
    foreach($cibles as $cible)
    {
      if($cible->get_hp() > 0)
      {
        $cible->set_hp(floor($cible->get_hp_max() * $pourcent));
        echo $cible->get_nom().' est équilibré à '.$cible->get_hp().
					' HP.<br />';
        $cible->sauver();
      }
      else
      {
        echo $cible->get_nom().' est mort.<br />';
      }
    }
    return true;
  }
}

class sort_body_to_mind extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    if($perso->get_hp() > $this->get_effet())
    {
      $sorthp = $this->get_effet();
      $sortmp = $this->get_effet2();
      //$sortmp_base = $this->get_effet();
      if( $perso->get_mp() + $sortmp > floor($perso->get_mp_maximum()) )
        $sortmp = floor($perso->get_mp_maximum()) - $perso->get_mp();
      $perso->set_mp($perso->get_mp() + $sortmp);
      echo 'Vous utilisez '.$sorthp.' HP pour convertir en '.$sortmp.' MP<br />';
      $perso->set_hp($perso->get_hp() - $sorthp);
      $perso->sauver();
      echo '<a href="sort.php?ID='.$_GET['ID'].$lanceur_url.'" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
      return $sortmp > 0;
    }
    else
    {
      echo 'Vous n\'avez pas assez de points de vie.';
      return false;
    }
  }
}

class sort_teleport extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $Trace;
    if($perso->get_hp() > 0)
    {
      $cibles = $this->get_liste_cibles($cible, $groupe);
      foreach($cibles as $cible)
      {
        $cible->set_x($Trace[$perso->get_race()]['spawn_x']);
        $cible->set_y($Trace[$perso->get_race()]['spawn_y']);
        $cible->sauver();
        echo $cible->get_nom().' a été téléporté dans votre capitale.<br />';
      }
      echo '<img src="image/pixel.gif" onLoad="envoiInfo(\'deplacement.php\', \'centre\');" />';
      return true;
    }
    else
    {
      echo 'Vous êtes mort.';
      return false;
    }
  }
}

class sort_repos_sage extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cibles  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    //On vérifie qu'il a pas déjà le debuff
    if(!$perso->is_buff('repos_sage', true))
    {
      //Mis en place du debuff
      lance_buff($this->get_type(), $perso->get_id(), 1, 0, $this->get_duree(), $this->get_nom(), 'Vous ne pouvez plus attaquer ni lancer le sort repos du sage', 'perso', 1, 0, 0, 0);
      $perso->set_mp( $perso->get_mp() + $this->get_effet() );
      if($perso->get_mp() > $perso->get_mp_maximum()) $perso->set_mp($perso->get_mp_maximum());
      $perso->sauver();
      return true;
    }
    else
    {
      echo 'Vous êtes déjà reposé';
    }
  }
}

class sort_guerison extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    $action = false;
    //-- Suppression d'un debuff au hasard
    foreach($cibles as $cible)
    {
      $debuff_tab = array();
      foreach($cible->get_buff() as $debuff)
      {
        if($debuff->get_debuff() == 1)
        {
          if($debuff->is_supprimable())
          {
            $debuff_tab[] = $debuff->get_id();
          }
        }
      }
      if(count($debuff_tab) > 0)
      {
        $requete = "DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";";
        $db->query($requete);
        $action = true;
      }
      else
      {
        echo "Impossible de lancer de lancer le sort. ".addslashes($cible->get_nom())." n&apos;a aucun debuff.<br/>";
      };
    }
    $type_cible = $cible->get_race()=='neutre'?'monstre':'perso';
    $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
    if ($groupe) $groupe = '&amp;groupe=yes'; else $groupe = '';
    echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$this->get_id().$groupe.$lanceur_url.$groupe_href."', 'information')\">Utiliser de nouveau cette compétence</a>";
    return $action;
  }
}

class sort_esprit_sacrifie extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    //-- Suppression d'un debuff au hasard
    if($perso->is_buff())
    {
      $debuff_tab = array();
      $buff_tab = array();
      foreach($perso->get_buff() as $buff)
      {
        if($buff->get_debuff() == 1)
        {
          if($buff->is_supprimable()) { $debuff_tab[] = $buff->get_id(); }
        }
        else
        {
          if($buff->is_supprimable()) { $buff_tab[] = $buff->get_id(); }
        }
      }
      if(count($debuff_tab) == 0)
      {
        echo "Impossible de lancer le sort. Vous n&apos;avez aucun debuff.<br/>";
      }
      elseif (count($buff_tab) == 0)
      {
        echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucun buff.<br/>";
      }
      else
      {
        $action = true;
        $db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
        $db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
      }
    }
    else { echo "Impossible de lancer de lancer le sort. Vous n&apos;avez aucun buff.<br/>"; };
    $type_cible = $cible->get_race()=='neutre'?'monstre':'perso';
    $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
    echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utiliser de nouveau cette compétence.</a>";
    return $action;
  }
}

class sort_transfert_energie extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $gain_total = 0;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
    {
      $cible_perso = new perso($cible->get_id());
      $cible_perso->check_materiel();
      $manque = $cible_perso->get_mp_maximum() - $cible_perso->get_mp();
      if ($manque < 1)
      {
        echo $cible->get_nom().' a toute sa mana.<br />';
        continue;
      }
      $gain = $this->get_effet();
      if ($gain > $manque) $gain = $manque;
      $action = true;
      $gain_total += $gain;
      $cible_perso->add_mp($gain);
      $cible_perso->sauver();
      echo $cible->get_nom().' regagne '.$gain.' MP.<br />';

      if($groupe)
      {
        //Insertion du 'soin' de groupe dans le journal de la cible
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgbuff', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', ".$gain.", ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
      else if($cible->get_id() != $perso->get_id())
      {
        //Insertion du 'soin' dans le journal de la cible
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rbuff', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), '".$this->get_nom()."', ".$gain.", ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
    }
    if ($action)
    {
      if($groupe) $groupe_href = '&amp;groupe=yes';
      else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
      echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.$lanceur_url.
    '" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';

      if($groupe)
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'rbuff', '".$perso->get_nom()."', 'groupe', NOW(), '".$this->get_nom()."', ".$gain_total.", ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
      else
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'buff', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), '".$this->get_nom()."', ".$gain_total.", ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
    }
    return $action;
  }
}

class sort_liberation extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible  Cibles su sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    $gain_total = 0;
    $cibles = $this->get_liste_cibles($cible, $groupe);
    foreach($cibles as $cible)
    {
      $buff_tab = array();
      foreach($cible->get_buff() as $buff)
      {
        if ($buff->get_debuff() == 0)
        {
          if ($buff->is_supprimable())
          {
            $buff_tab[] = $buff->get_id();
          }
        }
      }
      if (count($buff_tab) == 0)
      {
        echo 'Impossible de supprimer un buff de '.$cible->get_nom().
        ': pas de buff';
      }
      else
      {
        $cible_perso = new perso($cible->get_id());
        $cible_perso->check_materiel();
        $manque = $cible_perso->get_hp_maximum() - $cible_perso->get_hp();
        if ($manque < 1)
        {
          echo $cible->get_nom().' a toute sa vie.<br />';
          continue;
        }
        $requete = "DELETE FROM buff WHERE id=".
        $buff_tab[rand(0, count($buff_tab) - 1)].";";
        $db->query($requete);
        $gain = round($cible_perso->get_hp_maximum() * $this->get_effet() / 100);
        if ($gain > $manque) $gain = $manque;
        $action = true;
        $gain_total += $gain;
        $cible_perso->add_hp($gain);
        $cible_perso->sauver();
        echo 'Vous soignez '.$cible->get_nom().' de '.$gain.' HP.<br />';

        if($groupe)
        {
          //Insertion du soin de groupe dans le journal
          $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$gain.", 0, ".$perso->get_x().", ".$perso->get_y().")";
          $db->query($requete);
        }
        else if($cible->get_id() != $perso->get_id())
        {
          //Insertion du soin dans le journal
          $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$cible->get_id().", 'rsoin', '".$cible->get_nom()."', '".$perso->get_nom()."', NOW(), ".$gain.", 0, ".$perso->get_x().", ".$perso->get_y().")";
          $db->query($requete);
        }
      }
    }
    if ($action)
    {
      if($groupe) $groupe_href = '&amp;groupe=yes';
      else $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
      echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.$lanceur_url.
    '" onclick="return envoiInfo(this.href, \'information\')">Utiliser de nouveau ce sort</a>';
      if($groupe)
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'gsoin', '".$perso->get_nom()."', 'groupe', NOW(), ".$gain_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
      else
      {
        $requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$perso->get_id().", 'soin', '".$perso->get_nom()."', '".$cible->get_nom()."', NOW(), ".$gain_total.", 0, ".$perso->get_x().", ".$perso->get_y().")";
        $db->query($requete);
      }
    }
    return $action;
  }
}

class sort_rez extends sort_jeu
{

	/**
	 * Méthode gérant l'utilisation du sort
	 * @param $perso   Personnage lançant le sort
	 * @param $cible   Cible du sort
	 */
  function lance(&$perso, &$cible, $groupe=false, $lanceur_url='', $type_cible='')
  {
    global $db;
    $action = false;
    //Sale
    if($cible->get_race() == 'neutre')
    {
      echo 'Ce sort ne peut être utilisé que sur un joueur mort.';
      return false;
    }

    //On vérifie que le joueur est bien mort
    if($cible->get_hp() <= 0)
    {
      // On vérifie qu'il y a pas d'anti-rez
      if ($cible->is_buff('debuff_antirez'))
      {
        echo 'Ce joueur est affligé par l\'hérésie divine, il ne peut pas être rappelé à la vie';
        return false;
      }

      //On vérifie si le joueur n'a pas déjà une rez plus efficace d'active
      $requete = "SELECT pourcent FROM rez WHERE id_perso = ".$cible->get_id();
      $req_pourcent = $db->query($requete);
      $pourcent_max = 0;
      while($row_pourcent = $db->read_assoc($req_pourcent))
      {
        if($row_pourcent['pourcent'] > $pourcent_max) $pourcent_max = $row_pourcent['pourcent'];
      }
      if($this->get_effet() > $pourcent_max)
      {
        $action = true;
        //Mis en place de la résurection
        $requete = "INSERT INTO rez VALUES('', ".$cible->get_id().", ".$perso->get_id().", '".$perso->get_nom()."', ".$this->get_effet().", ".$this->get_effet2().", ".$this->get_duree().", NOW())";
        $db->query($requete);

        // Augmentation du compteur de l'achievement
        $achiev = $perso->get_compteur('rez');
        $achiev->set_compteur($achiev->get_compteur() + 1);
        $achiev->sauver();

        echo 'Résurrection bien lancée.';
      }
      else
      {
        echo 'Le joueur bénéficie d\'une résurrection plus puissante.';
      }
    }
    else
    {
      echo 'Le joueur n\'est pas mort';
    }
    return $action;
  }
}
?>
