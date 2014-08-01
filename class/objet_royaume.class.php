<?php
/**
 * @file objet_royaume.class.php
 * Gestion des objets RvR permettant de poser des bâtiments
 */


/**
 * Classe gérant les objets RvR permettant de poser des bâtiments
 * Il s'agit des objets que l'on peut prenre au dépôts pour poser des bourgages, forts, tours, armes de sièges…
 * Correspond à la table du même nom dans la bdd.
 */
class objet_royaume extends objet_invent
{
	protected $grade;  ///< grade nécessaire pour pouvoir prendre (et poser) l'objet.
	protected $id_batiment;  ///< id du bâtiment correspondant.
  protected $pierre;  ///< coût en pierre.
  protected $bois;  ///< coût en bois.
  protected $eau;  ///< coût en eau.
  protected $sable;  ///< coût en sable.
  protected $charbon;  ///< coût en charbon.
  protected $essence;  ///< coût en essence.
	const code = 'r';   ///< Code de l'objet.

  /// Renvoie le grade nécessaire pour pouvoir prendre (et poser) l'objet.
	function get_grade()
	{
		return $this->grade;
	}
  /// Modifie le grade nécessaire pour pouvoir prendre (et poser) l'objet.
	function set_grade($grade)
	{
		$this->grade = $grade;
		$this->champs_modif[] = 'grade';
	}

  /// Renvoie l'id du bâtiment correspondant.
	function get_id_batiment()
	{
		return $this->id_batiment;
	}
  /// Modifie l'id du bâtiment correspondant.
	function set_id_batiment($id_batiment)
	{
		$this->id_batiment = $id_batiment;
		$this->champs_modif[] = 'id_batiment';
	}

  /// Renvoie l'objet batiment correspondand
  function &get_batiment()
  {
    if( !$this->batiment )
      $this->batiment = new batiment( $this->id_batiment );
    return $this->batiment;
  }

  /// Renvoie le coût en pierre.
	function get_pierre()
	{
		return $this->pierre;
	}
  /// Modifie le coût en pierre.
	function set_pierre($pierre)
	{
		$this->pierre = $pierre;
		$this->champs_modif[] = 'pierre';
	}

  /// Renvoie le coût en bois.
	function get_bois()
	{
		return $this->bois;
	}
  /// Modifie le coût en bois.
	function set_bois($bois)
	{
		$this->bois = $bois;
		$this->champs_modif[] = 'bois';
	}

  /// Renvoie le coût en eau.
	function get_eau()
	{
		return $this->eau;
	}
  /// Modifie le coût en eau.
	function set_eau($eau)
	{
		$this->eau = $eau;
		$this->champs_modif[] = 'eau';
	}

  /// Renvoie le coût en sable.
	function get_sable()
	{
		return $this->sable;
	}
  /// Modifie le coût en sable.
	function set_sable($sable)
	{
		$this->sable = $sable;
		$this->champs_modif[] = 'sable';
	}

  /// Renvoie le coût en charbon.
	function get_charbon()
	{
		return $this->charbon;
	}
  /// Modifie le coût en charbon.
	function set_charbon($charbon)
	{
		$this->charbon = $charbon;
		$this->champs_modif[] = 'charbon';
	}

  /// Renvoie le coût en essence.
	function get_essence()
	{
		return $this->essence;
	}
  /// Modifie le coût en essence.
	function set_essence($essence)
	{
		$this->essence = $essence;
		$this->champs_modif[] = 'essence';
	}

	/**
	 * Constructeur
	 * @param  $nom		       nom de l'objet.
	 * @param  $type	       type de l'objet.
	 * @param  $prix	       prix de l'objet em magasin.
	 * @param  $grade	       grade nécessaire pour pouvoir prendre (et poser) l'objet.
	 * @param  $id_batiment	 id du bâtiment correspondant.
	 * @param  $pierre	     coût en pierre.
	 * @param  $bois	       coût en bois.
	 * @param  $eau	         coût en eau.
	 * @param  $sable	       coût en sable.
	 * @param  $charbon	     coût en charbon.
	 * @param  $essence	     coût en essence.
	 */
	function __construct($nom='', $type='', $prix=0, $grade=2, $id_batiment=0, $pierre=0, $bois=0, $eau=0, $sable=0, $charbon=0, $essence=0)
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
			$this->grade = $grade;
			$this->id_batiment = $id_batiment;
			$this->pierre = $pierre;
			$this->bois = $bois;
			$this->eau = $eau;
			$this->sable = $sable;
			$this->charbon = $charbon;
			$this->essence = $essence;
		}
	}

	/**
	* Initialise les données membres à l'aide d'un tableau
	* @param array $vals Tableau contenant les valeurs des données.
	*/
	protected function init_tab($vals)
	{
		objet_invent::init_tab($vals);
		$this->grade = $vals['grade'];
		$this->id_batiment = $vals['id_batiment'];
		$this->pierre = $vals['pierre'];
		$this->bois = $vals['bois'];
		$this->eau = $vals['eau'];
		$this->sable = $vals['sable'];
		$this->charbon = $vals['charbon'];
		$this->essence = $vals['essence'];
	}

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_champs()
	{
    $tbl = objet_invent::get_champs();
    $tbl['grade']='i';
    $tbl['id_batiment']='i';
    $tbl['pierre']='i';
    $tbl['bois']='i';
    $tbl['eau']='i';
    $tbl['sable']='i';
    $tbl['charbon']='i';
    $tbl['essence']='i';
		return $tbl;
	}

	/// Méthode renvoyant l'image de l'objet
	public function get_image()
  {
    $bat = &$this->get_batiment();
    if( $bat->get_type() == 'drapeau' )
    {
      $race = joueur::get_perso()->get_race();
      $roy = royaume::create('race', $race);
      $image = 'image/drapeaux/'.$bat->get_image().'_'.$roy[0]->get_id().'.png';
    }
    else
      $image = 'image/batiment/'.$bat->get_image().'_04.png';
    if( file_exists($image) )
      return $image;
    return null;
  }

	/**
	 * Méthode renvoyant les noms des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_noms_infos($complet=true)
  {
    $noms = array('Type', 'Description');
    if( $this->type != 'drapeau' )
      $noms[] = 'Entretien';
    $noms = array_merge($noms, array('HP', 'PP', 'PM', 'Esquive', 'Caractéristiques', 'Temps de construction (base)', 'Temps de construction minimum'));
    if( $this->type != 'drapeau' )
      $noms[] = 'Points de victoire (si détruit)';
    return $noms;
  }

	/**
	 * Méthode renvoyant les valeurs des informations sur l'objet
	 * @param  $complet  true si on doit renvoyer toutes les informations.
	 */
	public function get_valeurs_infos($complet=true)
  {
    $bat = &$this->get_batiment();
    $vals = array($this->type, $bat->get_description());
    if( $this->type != 'drapeau' )
      $vals[] = $bat->get_entretien();
    $vals = array_merge($vals, array($bat->get_hp(), $bat->get_PP(),
      $bat->get_PM(), $bat->get_esquive(), $bat->get_carac(), transform_min_temp($bat->get_temps_construction()), transform_min_temp($bat->get_temps_construction_min())) );
    if( $this->type != 'drapeau' )
      $vals[] = $bat->get_point_victoire();
    return $vals;
  }

	function get_colone_int($partie)
  {
    if( $partie == 'royaume' )
    {
      switch( $this->type )
      {
      case 'arme_de_siege':
        return 0;
        break;
      case 'drapeau':
        return 1;
        break;
      default:
        return 2;
        break;
      }
    }
    else
      return false;
  }

  /// Méthode renvoyant l'info principale sur l'objet
  public function get_info_princ()
  {
    if( joueur::get_perso()->is_buff('convalescence') )
      return 'PA : 10';
    return null;
  }

  function est_utilisable() { return true; }

  function utiliser(&$perso, &$princ)
  {
  	global $db, $Trace;
    // Trêve ?
		if( $perso->is_buff('debuff_rvr' ))
		{
      $princ->add( new interf_alerte('danger', true) )->add_message('RvR impossible pendant la trêve');
			return false;
		}
    // On peut poser sur cette case ?
    $case = new map_case( $perso->get_pos() );
    if( $case->get_type() == 1 or $case->get_type() == 4 or is_donjon($perso->get_x(), $perso->get_y()) )
		{
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous ne pouvez rien poser sur ce type de terrain !');
			return false;
		}
    $R = new royaume( $case->get_royaume() );
    $batiment = &$this->get_batiment();
    // On vérifie qu'il n'y a pas déjà un batiment en construction
    $bats = placement::create(array('x','y'), array($perso->get_x(),$perso->get_y()));
    if( count($bats) )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Il y a déjà un bâtiment en construction sur cette case !');
			return false;
		}
    // On vérifie qu'il n'y a pas déjà un batiment
    $bats = construction::create(array('x','y'), array($perso->get_x(),$perso->get_y()));
    if( count($bats) )
    {
      $princ->add( new interf_alerte('danger', true) )->add_message('Il y a déjà un bâtiment sur cette case !');
			return false;
		}

    // Coût en pa
    $pa = $perso->is_buff('convalescence') ? 10 : 0;
		if( $pa && $perso->get_pa() < $pa )
		{
      $princ->add( new interf_alerte('danger', true) )->add_message('Vous n\'avez pas assez de PA !');
			return false;
		}

    // Drapeau ou autre ?
    if( $this->get_type() == 'drapeau' )
    {
      if( $R->get_nom() != 'Neutre' )
      {
        if( $R->get_diplo($perso->get_race()) <= 6 or $R->get_diplo($perso->get_race()) == 127  )
        {
          $princ->add( new interf_alerte('danger', true) )->add_message('Vous ne pouvez poser un drapeau que sur les royaumes avec lesquels vous êtes en guerre.');
    			return false;
        }
        if( $case->get_nom() == 'Petit Drapeau' )
        {
          $princ->add( new interf_alerte('danger', true) )->add_message('Vous ne pouvez pas poser de petit drapeau sur une case non neutre !');
    			return false;
        }
      }

			// Augmentation du compteur de l'achievement
			$achiev = $perso->get_compteur('pose_drapeaux');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();

      /// TODO: à simplifier
			if ($case->get_info() == 1)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_plaine');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 2)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_foret');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 3)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_sable');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 4)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_glace');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 6)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_montagne');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 7)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_marais');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 8)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_route');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			if ($case->get_info() == 9)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $perso->get_compteur('pose_drapeaux_terremaudite');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
    }
    else
    {
      // Bonne diplomatie ?
      /// TODO: remplacer les conditions sur l'id par un bonus
      if( $R->get_diplo($perso->get_race()) != 127 && $this->get_type() != 'arme_de_siege' && $batiment->get_id() != 1 )  // id=1 : poste avancé
      {
        $princ->add( new interf_alerte('danger', true) )->add_message('Vous ne pouvez poser ce bâtimentque sur un territoire qui vous appartient');
  			return false;
  		}

  		// Règles des distance entre bâtiments
      /// TODO: on peut probablement simplifier ça
  		if($this->get_type() == 'bourg' || $this->get_type() == 'fort')
      {
  			// Distance d'une capitale
  			$distanceMax = $this->get_type() == 'bourg' ? 5 : 7;

  			$requete = "SELECT 1 FROM map"
  							." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
  							." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
  							." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
  							." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
  							." AND type = 1";
  			$req = $db->query($requete);
  			if($db->num_rows > 0)
        {
          $princ->add( new interf_alerte('danger', true) )->add_message('Il y a une capitale à moins de '.$distanceMax.' cases !');
    			return false;
  			}

  			// Distance entre Bourgs
  			if($this->get_type() == 'bourg')
        {
  				// Distance entre 2 bourgs
          if( construction::batiments_proche($perso->get_x(), $perso->get_y(), 'bourg', $R->get_dist_bourgs(), $R->get_id())
            or placement::batiments_proche($perso->get_x(), $perso->get_y(), 'bourg', $R->get_dist_bourgs(), $R->get_id()) )
          {
            $princ->add( new interf_alerte('danger', true) )->add_message('Vous avez un bourg à moins de '.$R->get_dist_bourgs().' cases !');
      			return false;
          }
          else
          {
            $dist_r = $R->get_dist_bourgs(true);
            $bats = construction::batiments_proche($perso->get_x(), $perso->get_y(), 'bourg', $dist_r, $R->get_id(), true, true);
            if( !bats )
              $bats = placement::batiments_proche($perso->get_x(), $perso->get_y(), 'bourg', $dist_r, $R->get_id(), true, true);
            if( $bats )
            {
              $isOk = true;
              foreach($bats as $b)
              {
                $r_bourg = new royaume($b['royaume']);
                $d_max = min($r_bourg->get_dist_bourgs(true), $dist_r);
                $dist = detection_distance(convert_in_pos($b['x'], $b['y']), convert_in_pos($perso->get_x(), $perso->get_y()));
                if( $dist <= $d_max )
                {
                  $princ->add( new interf_alerte('danger', true) )->add_message('l y a un bourg à '.$dist.' cases !');
            			return false;
                }
              }
            }
          }
  			}
  			// Distance entre forts
  			else if($this->get_type() == 'fort')
        {
  				// Distance entre 2 forts du même royaume
          if( construction::batiments_proche($perso->get_x(), $perso->get_y(), 'fort', $R->get_dist_forts(), $R->get_id())
            or placement::batiments_proche($perso->get_x(), $perso->get_y(), 'fort', $R->get_dist_forts(), $R->get_id()) )
          {
            $princ->add( new interf_alerte('danger', true) )->add_message('Vous avez un fort à moins de '.$R->get_dist_bourgs().' cases !');
          }
          else if( construction::batiments_proche($perso->get_x(), $perso->get_y(), 'fort', $R->get_dist_forts(true), $R->get_id(), true)
            or placement::batiments_proche($perso->get_x(), $perso->get_y(), 'fort', $R->get_dist_forts(true), $R->get_id(), true) )
          {
            $princ->add( new interf_alerte('danger', true) )->add_message('Il y a un fort à moins de '.$R->get_dist_forts(true).' cases !');
      			return false;
          }
  			}
  		}
      else if($this->get_type() == 'mur' )
      {
				// On commence par extraire la position des murs ou des constructions de murs a 2 cases de distance de la case a traiter
				$position_murs=array();
				$position_murs[0]=array(0,0,0,0,0);
				$position_murs[1]=array(0,0,0,0,0);
				$position_murs[2]=array(0,0,0,0,0);
				$position_murs[3]=array(0,0,0,0,0);
				$position_murs[4]=array(0,0,0,0,0);

				// Il y a donc 25 positions à recupérer
				$requete  = 'SELECT x,y FROM construction WHERE ABS(CAST(x AS SIGNED) -'.$perso->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$perso->get_y().') <= 2 AND type LIKE "mur"';
				$requete  = 'SELECT id,x,y FROM construction WHERE ABS(CAST(x AS SIGNED) - '.$perso->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$perso->get_y().') <= 2 AND type LIKE "mur" UNION SELECT id,x,y FROM placement WHERE ABS(CAST(x AS SIGNED) - '.$perso->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$perso->get_y().') <= 2 AND type LIKE "mur"';
				$req = $db->query($requete);

				// Stockage des positions dans la matrice
				while($row = $db->read_assoc($req))
				{
					$position_murs[$row[x] - $perso->get_x()+2][$row[y] - $perso->get_y()+2]=1;
				}
				// Rajout de la position du nouveau mur dans la matrice pour les tests (il est au milieu de la matrice).
				$position_murs[2][2]=1;

				// Gestion des cardinalites (somme du nombre de murs adjacent au nord, ouest, est, sud de chaque position en comptant la position courante
				$murs_cardinalite=array();
				$murs_cardinalite[0]=array(0,0,0);
				$murs_cardinalite[1]=array(0,0,0);
				$murs_cardinalite[2]=array(0,0,0);
				$max_nb_murs=0;
				for ($x = 1; $x<=3 ; $x+=1)
				{
					for ($y = 1; $y<=3 ; $y+=1)
					{
						$murs_cardinalite[$x-1][$y-1]= $position_murs[$x][$y] ? $position_murs[$x-1][$y]+$position_murs[$x+1][$y]+$position_murs[$x][$y-1]+$position_murs[$x][$y+1]+1 : 0;
						$max_nb_murs=max($max_nb_murs,$murs_cardinalite[$x-1][$y-1]);
					}
				}
				// Il reste maintenant a verifier que toutes les conditions sont réunies
				// Si une des cases vaut 4 ou plus, alors erreur
				if( $max_nb_murs > 3 )
        {
					$princ->add( new interf_alerte('danger', true) )->add_message('Il y a déjà trop de murs autour !');
      		return false;
				}


  			// Augmentation du compteur de l'achievement
  			$achiev = $perso->get_compteur('pose_murs');
  			$achiev->set_compteur($achiev->get_compteur() + 1);
  			$achiev->sauver();
  		}
    }
		//Positionnement de la construction
		if($this->get_type() == 'arme_de_siege')
		{
      $distance = 1;
      $rez = $batiment->get_bonus('rez');
		}
		else
    {
		  $distance = calcul_distance(convert_in_pos($Trace[$perso->get_race()]['spawn_x'], $Trace[$perso->get_race()]['spawn_y']), ($perso->get_pos()));
			$rez = 0;
    }
    $time = time() + max($batiment->get_temps_construction() * $distance, $batiment->get_temps_construction_min());

    // nouveau placement
    $plac = new placement(0, $this->get_type(), $perso->get_x(), $perso->get_y(), $Trace[$perso->get_race()]['numrace'],
      time(), $time, $batiment->get_id(), $batiment->get_hp(), $batiment->get_nom(), $rez, $batiment->get_point_victoire());
    $plac->sauver();

		// Coût en PA (si en convalescence)
		if( $pa )
		{
		  $perso->add_pa( -$pa );
    }

    $princ->add( new interf_alerte('success') )->add_message($batiment->get_nom().' posé avec succès');
    $perso->supprime_objet($this->get_texte(), 1);
  }

  function deposer(&$perso, &$princ)
  {
    $case = new map_case( $perso->get_pos() );
    $R = new royaume( $case->get_royaume() );
		if ($R->get_race() != $perso->get_race())
		{
      $princ->add( new interf_alerte('danger') )->add_message('Impossible de poser au dépot '.$R->get_race());
      return false;
		}
		$requete = 'INSERT INTO depot_royaume VALUES (NULL, '.$this->id.', '.$R->get_id().')';
		$db->query($requete);
    $princ->add( new interf_alerte('success') )->add_message('Objet posé avec succès.');
    $perso->supprime_objet($this->get_texte(), 1);
    return true;
  }

  /// TODO: logguer ?
  function vendre_marchand(&$perso, &$princ) { return false; }
  function vendre_hdv(&$perso, &$princ, $prix) { return false; }
}