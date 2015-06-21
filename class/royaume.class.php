<?php // -*- mode: php; tab-width:2 -*-
class royaume
{
/**
    * @access private
    * @var int(11)
    */
	private $id;

	/**
    * @access private
    * @var int(10)
    */
	private $taxe_time;

	/**
    * @access private
    * @var varchar(50)
    */
	private $race;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(50)
    */
	private $capitale;

	/**
    * @access private
    * @var int(10)
    */
	private $star;

	/**
    * @access private
    * @var int(10)
    */
	private $point_victoire;

	/**
    * @access private
    * @var int(10)
    */
	private $point_victoire_total;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $star_nouveau_joueur;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $taxe;

	/**
    * @access private
    * @var text
    */
	private $diplo_time;

	/**
    * @access private
    * @var int(10)
    */
	private $honneur_candidat;

	/**
    * @access private
    * @var int(10)
    */
	private $bourg;

	/**
    * @access private
    * @var int(10)
    */
	private $pierre;

	/**
    * @access private
    * @var int(10)
    */
	private $bois;

	/**
    * @access private
    * @var int(10)
    */
	private $eau;

	/**
    * @access private
    * @var int(10)
    */
	private $sable;

	/**
    * @access private
    * @var int(10)
    */
	private $charbon;

	/**
    * @access private
    * @var int(10)
    */
	private $essence;

	/**
    * @access private
    * @var int(10)
    */
	private $food;

	/**
    * @access private
    * @var int(10)
    */
	private $alchimie;

	/**
    * @access private
    * @var int(10)
    */
	private $roi;

	/**
    * @access private
    * @var int(10)
    */
	private $ministre_economie;

	/**
    * @access private
    * @var int(10)
    */
	private $ministre_militaire;

	/**
    * @access private
    * @var int(10)
    */
	private $capitale_hp;

	/**
    * @access private
    * @var int(10)
    */
	private $fin_raz_capitale;
	private $facteur_entretien;  ///< facteur d'entretien
	private $conso_food;  ///< Consommation de nourriture
	private $rang;  ///< Rang du royaume
	
	/// Renvoie le rang
	function get_rang()
	{
    return $this->rang;
  }
	function set_rang($rang)
	{
		$this->rang = $rang;
		$this->champs_modif[] = 'rang';
	}
	
	/// Renvoie le facteur d'entretien
	function get_facteur_entretien()
	{
    return $this->facteur_entretien;
  }
  /// Modifie le facteur d'entretien
	function set_facteur_entretien($facteur_entretien)
	{
    $this->facteur_entretien = $facteur_entretien;
		$this->champs_modif[] = 'facteur_entretien';
  }
	/// Renvoie le facteur d'entretien théorique
	function get_facteur_entretien_th()
	{
    global $db, $ref_actifs;
    $date = time() - self::duree_actif;
    if( !isset($ref_actifs) )
    {
      $requete = 'SELECT COUNT(*) as tot FROM perso WHERE statut like "actif" AND level >= '.self::get_niveau_ref_actifs().' AND dernier_connexion > '.$date.' GROUP BY race ORDER BY tot DESC';
      $res = $db->query($requete);
      $ref_actifs = 1;
      while( $row = $db->read_array($res) )
      {
        $ref_actifs *= $row['tot'];
        $min = $row['tot'];
      }
      $ref_actifs = (pow($ref_actifs, 1/11) + $min)/2;
    }
    $facteur = $this->get_habitants_actif() / $ref_actifs;
    if( $facteur < .3 ) $facteur = .3;
    else if( $facteur > 3 ) $facteur = 3;
    return $facteur;
  }
  /// Met-à-jour le facteur d'entretien
  function maj_facteur_entretien()
  {
    $diff = $this->get_facteur_entretien_th() - $this->get_facteur_entretien();
    if($diff < - .1) $diff = -.1;
    else if($diff > .05) $diff = .05;
    $this->set_facteur_entretien( $this->get_facteur_entretien() + $diff);
  }

	/// Renvoie la consommation de nourriture
	function get_conso_food()
	{
    return $this->conso_food;
  }
  /// Modifie la consommation de nourriture
	function set_conso_food($conso_food)
	{
    $this->conso_food = $conso_food;
		$this->champs_modif[] = 'conso_food';
  }
	/// Renvoie la consommation de nourriture théorique
	function get_conso_food_th()
	{
    global $db, $conso_food_tot, $cases_tot;
    if( !isset($conso_food_tot) )
    {
      $cases_tot = $this->get_nbr_cases();
      // Consommation totale
      $requete = 'SELECT SUM(food) AS food FROM royaume WHERE id > 0';
      $res = $db->query($requete);
      $row = $db->read_array($res);
      $stocks = $row['food'];
      $requete = 'SELECT SUM(nombre) as food FROM bourse_royaume WHERE actif = 1 AND ressource = "food"';
      $res = $db->query($requete);
      $row = $db->read_array($res);
      $stocks += $row['food'];
      $facteur = $stocks / (2 * $cases_tot);
      if( $facteur < .7 ) $facteur = .7;
      else  if( $facteur > 1.5 ) $facteur = 1.5;
      $date_hier = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 2, date("Y")));
      $requete = 'SELECT food FROM stat_jeu WHERE date = "'.$date_hier.'"';
      $res = $db->query($requete);
      $row = $db->read_array($res);
      $prod = $row['food'];
      $conso_food_tot = $prod * $facteur;
    }
    $requete = 'SELECT COUNT(*) AS tot FROM map WHERE royaume = '.$this->get_id().' AND x <= 190 AND y <= 190';
    $res = $db->query($requete);
    $row = $db->read_array($res);
    return $conso_food_tot * $row['tot'] / $cases_tot;
  }
  /// Met-à-jour la consommation de nourriture
  function maj_conso_food()
  {
  	$cons_th = $this->get_conso_food_th();
    $anc_diff = $diff = $cons_th - $this->get_conso_food();
    if($diff < -50) $diff = -50;
    else if($diff > 25) $diff = 25;
    echo 'maj conso '.$this->race.' : théorique='.$cons_th.' - actuel='.$this->get_conso_food().' - écart='.$anc_diff.' - ajustement='.$diff;
    $this->set_conso_food( $this->get_conso_food() + $diff);
    echo ' - nouvelle conso='.$this->get_conso_food()."\n";
  }
  /// Renvoie le nombre de case contrôlées
  function get_nbr_cases()
  {
    global $db;
    // nombre de cases totales
    $requete = 'SELECT COUNT(*) AS tot FROM map WHERE royaume > 0 AND x <= 190 AND y <= 190';
    $res = $db->query($requete);
    $row = $db->read_array($res);
    return $row['tot'];
	}

  /**
   * Renvoie la distance minimale entre deux bourgs
   *
   * @var  $diff  true s'il agit de bourgs de différents royaumes
   */
  function get_dist_bourgs($diff = false)
  {
    return 7;//ceil(pow($this->get_facteur_entretien(), .9) * 7);
  }

  /// Renvoie la distance minimale entre un bourg et une capitale
  function get_dist_bourg_capitale()
  {
    return 5;
  }

  /**
   * Renvoie la distance minimale entre deux forts
   *
   * @var  $diff  true s'il agit de forts de différents royaumes
   */
  function get_dist_forts($diff = false)
  {
    //if( $diff )
      return 4;
    /*else
      return ceil(pow($this->get_facteur_entretien(), .9) * 4);*/
  }

  /// Renvoie la distance minimale entre un fort et une capitale
  function get_dist_fort_capitale()
  {
    return 7;
  }

	
	/**
	* @access public

	* @param int(11) id attribut
	* @param int(10) taxe_time attribut
	* @param varchar(50) race attribut
	* @param varchar(50) nom attribut
	* @param varchar(50) capitale attribut
	* @param int(10) star attribut
	* @param int(10) point_victoire attribut
	* @param int(10) point_victoire_total attribut
	* @param mediumint(8) star_nouveau_joueur attribut
	* @param tinyint(3) taxe attribut
	* @param text diplo_time attribut
	* @param int(10) honneur_candidat attribut
	* @param int(10) bourg attribut
	* @param int(10) pierre attribut
	* @param int(10) bois attribut
	* @param int(10) eau attribut
	* @param int(10) sable attribut
	* @param int(10) charbon attribut
	* @param int(10) essence attribut
	* @param int(10) food attribut
	* @param int(10) alchimie attribut
	* @param int(10) roi attribut
	* @param int(10) ministre_economie attribut
	* @param int(10) ministre_militaire attribut
	* @param int(10) capitale_hp attribut
	* @param int(10) fin_raz_capitale attribut
	* @param int(10) $facteur_entretien attribut
	* @param int(10) $conso_food attribut
	* @return none
	*/
	function __construct($id = 0, $taxe_time = 0, $race = '', $nom = '', $capitale = '', $star = '', $point_victoire = '', $point_victoire_total = '', $star_nouveau_joueur = '', $taxe = '', $diplo_time = '', $honneur_candidat = '', $bourg = '', $pierre = '', $bois = '', $eau = '', $sable = '', $charbon = '', $essence = '', $food = '', $alchimie = '', $roi = '', $ministre_economie = '', $ministre_militaire = '', $capitale_hp = '', $fin_raz_capitale = '', $facteur_entretien=1, $conso_food =0, $rang=1)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT taxe_time, race, nom, capitale, star, point_victoire, point_victoire_total, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie, rang, roi, ministre_economie, ministre_militaire, capitale_hp, fin_raz_capitale, facteur_entretien, conso_food FROM royaume WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->taxe_time, $this->race, $this->nom, $this->capitale, $this->star, $this->point_victoire, $this->point_victoire_total, $this->star_nouveau_joueur, $this->taxe, $this->diplo_time, $this->honneur_candidat, $this->bourg, $this->pierre, $this->bois, $this->eau, $this->sable, $this->charbon, $this->essence, $this->food, $this->alchimie, $this->rang, $this->roi, $this->ministre_economie, $this->ministre_militaire, $this->capitale_hp, $this->fin_raz_capitale, $this->facteur_entretien, $this->conso_food) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->taxe_time = $id['taxe_time'];
			$this->race = $id['race'];
			$this->nom = $id['nom'];
			$this->capitale = $id['capitale'];
			$this->star = $id['star'];
			$this->point_victoire = $id['point_victoire'];
			$this->point_victoire_total = $id['point_victoire_total'];
			$this->star_nouveau_joueur = $id['star_nouveau_joueur'];
			$this->taxe = $id['taxe'];
			$this->diplo_time = $id['diplo_time'];
			$this->honneur_candidat = $id['honneur_candidat'];
			$this->bourg = $id['bourg'];
			$this->pierre = $id['pierre'];
			$this->bois = $id['bois'];
			$this->eau = $id['eau'];
			$this->sable = $id['sable'];
			$this->charbon = $id['charbon'];
			$this->essence = $id['essence'];
			$this->food = $id['food'];
			$this->alchimie = $id['alchimie'];
			$this->rang = $id['rang'];
			$this->roi = $id['roi'];
			$this->ministre_economie = $id['ministre_economie'];
			$this->ministre_militaire = $id['ministre_militaire'];
			$this->capitale_hp = $id['capitale_hp'];
			$this->fin_raz_capitale = $id['fin_raz_capitale'];
			$this->facteur_entretien = $id['facteur_entretien'];
			$this->conso_food = $id['conso_food'];
			}
		else
		{
			$this->taxe_time = $taxe_time;
			$this->race = $race;
			$this->nom = $nom;
			$this->capitale = $capitale;
			$this->star = $star;
			$this->point_victoire = $point_victoire;
			$this->point_victoire_total = $point_victoire_total;
			$this->star_nouveau_joueur = $star_nouveau_joueur;
			$this->taxe = $taxe;
			$this->diplo_time = $diplo_time;
			$this->honneur_candidat = $honneur_candidat;
			$this->bourg = $bourg;
			$this->pierre = $pierre;
			$this->bois = $bois;
			$this->eau = $eau;
			$this->sable = $sable;
			$this->charbon = $charbon;
			$this->essence = $essence;
			$this->food = $food;
			$this->alchimie = $alchimie;
			$this->rang = $rang;
			$this->roi = $roi;
			$this->ministre_economie = $ministre_economie;
			$this->ministre_militaire = $ministre_militaire;
			$this->capitale_hp = $capitale_hp;
			$this->fin_raz_capitale = $fin_raz_capitale;
			$this->id = $id;
			$this->facteur_entretien = $facteur_entretien;
			$this->conso_food = $conso_food;
		}
	}

	/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'taxe_time = '.$this->taxe_time.', race = "'.mysql_escape_string($this->race).'", nom = "'.mysql_escape_string($this->nom).'", capitale = "'.mysql_escape_string($this->capitale).'", star = "'.mysql_escape_string($this->star).'", point_victoire = "'.mysql_escape_string($this->point_victoire).'", point_victoire_total = "'.mysql_escape_string($this->point_victoire_total).'", star_nouveau_joueur = "'.mysql_escape_string($this->star_nouveau_joueur).'", taxe = "'.mysql_escape_string($this->taxe).'", diplo_time = "'.mysql_escape_string($this->diplo_time).'", honneur_candidat = "'.mysql_escape_string($this->honneur_candidat).'", bourg = "'.mysql_escape_string($this->bourg).'", pierre = "'.mysql_escape_string($this->pierre).'", bois = "'.mysql_escape_string($this->bois).'", eau = "'.mysql_escape_string($this->eau).'", sable = "'.mysql_escape_string($this->sable).'", charbon = "'.mysql_escape_string($this->charbon).'", essence = "'.mysql_escape_string($this->essence).'", food = "'.mysql_escape_string($this->food).'", alchimie = "'.mysql_escape_string($this->alchimie).'", roi = "'.mysql_escape_string($this->roi).'", ministre_economie = "'.mysql_escape_string($this->ministre_economie).'", ministre_militaire = "'.mysql_escape_string($this->ministre_militaire).'", capitale_hp = "'.mysql_escape_string($this->capitale_hp).'", fin_raz_capitale = "'.mysql_escape_string($this->fin_raz_capitale).'", facteur_entretien = '.$this->facteur_entretien.'", conso_food = '.$this->conso_food;
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE royaume SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO royaume (taxe_time, race, nom, capitale, star, point_victoire, point_victoire_total, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie, rang, roi, ministre_economie, ministre_militaire, capitale_hp, fin_raz_capitale, facteur_entretien, conso_food) VALUES(';
			$requete .= ''.$this->taxe_time.', "'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->capitale).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->point_victoire).'", "'.mysql_escape_string($this->point_victoire_total).'", "'.mysql_escape_string($this->star_nouveau_joueur).'", "'.mysql_escape_string($this->taxe).'", "'.mysql_escape_string($this->diplo_time).'", "'.mysql_escape_string($this->honneur_candidat).'", "'.mysql_escape_string($this->bourg).'", "'.mysql_escape_string($this->pierre).'", "'.mysql_escape_string($this->bois).'", "'.mysql_escape_string($this->eau).'", "'.mysql_escape_string($this->sable).'", "'.mysql_escape_string($this->charbon).'", "'.mysql_escape_string($this->essence).'", "'.mysql_escape_string($this->food).'", "'.mysql_escape_string($this->alchimie).'", "'.mysql_escape_string($this->rang).'", "'.mysql_escape_string($this->roi).'", "'.mysql_escape_string($this->ministre_economie).'", "'.mysql_escape_string($this->ministre_militaire).'", "'.mysql_escape_string($this->capitale_hp).'", "'.mysql_escape_string($this->fin_raz_capitale).'", '.$this->facteur_entretien.','.$this->conso_food.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();

      log_admin::log('debug', 'Création d\'un royaume : '.$requete, true);
		}
	}

	/**
	* Supprime de la base de donnée
	* @access public
	* @param none
	* @return none
	*/
	function supprimer()
	{
		/*global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM royaume WHERE id = '.$this->id;
			$db->query($requete);
		}*/
	}

	/**
	* Supprime de la base de donnée
	* @access static
	* @param array|string $champs champs servant a trouver les résultats
	* @param array|string $valeurs valeurs servant a trouver les résultats
	* @param string $ordre ordre de tri
	* @param bool|string $keys Si false, stockage en tableau classique, si string stockage avec sous tableau en fonction du champ $keys
	* @return array $return liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}
	
		$requete = "SELECT id, taxe_time, race, nom, capitale, star, point_victoire, point_victoire_total, star_nouveau_joueur, taxe, diplo_time, honneur_candidat, bourg, pierre, bois, eau, sable, charbon, essence, food, alchimie, roi, ministre_economie, ministre_militaire, capitale_hp, fin_raz_capitale, facteur_entretien, conso_food FROM royaume WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new royaume($row);
				else $return[$row[$keys]][] = new royaume($row);
			}
		}
		else $return = array();
		return $return;
	}

	/**
	* Affiche l'objet sous forme de string
	* @access public
	* @param none
	* @return string objet en string
	*/
	function __toString()
	{
		return 'id = '.$this->id.', taxe_time = '.$this->taxe_time.', race = '.$this->race.', nom = '.$this->nom.', capitale = '.$this->capitale.', star = '.$this->star.', point_victoire = '.$this->point_victoire.', point_victoire_total = '.$this->point_victoire_total.', star_nouveau_joueur = '.$this->star_nouveau_joueur.', taxe = '.$this->taxe.', diplo_time = '.$this->diplo_time.', honneur_candidat = '.$this->honneur_candidat.', bourg = '.$this->bourg.', pierre = '.$this->pierre.', bois = '.$this->bois.', eau = '.$this->eau.', sable = '.$this->sable.', charbon = '.$this->charbon.', essence = '.$this->essence.', food = '.$this->food.', alchimie = '.$this->alchimie.', roi = '.$this->roi.', ministre_economie = '.$this->ministre_economie.', ministre_militaire = '.$this->ministre_militaire.', capitale_hp = '.$this->capitale_hp.', fin_raz_capitale = '.$this->fin_raz_capitale.', facteur_entretien = '.$this->facteur_entretien.', conso_food = '.$this->conso_food;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $taxe_time valeur de l'attribut taxe_time
	*/
	function get_taxe_time()
	{
		return $this->taxe_time;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $race valeur de l'attribut race
	*/
	function get_race()
	{
		return $this->race;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $capitale valeur de l'attribut capitale
	*/
	function get_capitale()
	{
		return $this->capitale;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $star valeur de l'attribut star
	*/
	function get_star()
	{
		return $this->star;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $point_victoire valeur de l'attribut point_victoire
	*/
	function get_point_victoire()
	{
		return $this->point_victoire;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $point_victoire_total valeur de l'attribut point_victoire_total
	*/
	function get_point_victoire_total()
	{
		return $this->point_victoire_total;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $star_nouveau_joueur valeur de l'attribut star_nouveau_joueur
	*/
	function get_star_nouveau_joueur()
	{
		return $this->star_nouveau_joueur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $taxe valeur de l'attribut taxe
	*/
	function get_taxe()
	{
		return $this->taxe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $diplo_time valeur de l'attribut diplo_time
	*/
	function get_diplo_time()
	{
		return $this->diplo_time;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $honneur_candidat valeur de l'attribut honneur_candidat
	*/
	function get_honneur_candidat()
	{
		return $this->honneur_candidat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $bourg valeur de l'attribut bourg
	*/
	function get_bourg()
	{
		return $this->bourg;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $pierre valeur de l'attribut pierre
	*/
	function get_pierre()
	{
		return $this->pierre;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $bois valeur de l'attribut bois
	*/
	function get_bois()
	{
		return $this->bois;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $eau valeur de l'attribut eau
	*/
	function get_eau()
	{
		return $this->eau;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $sable valeur de l'attribut sable
	*/
	function get_sable()
	{
		return $this->sable;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $charbon valeur de l'attribut charbon
	*/
	function get_charbon()
	{
		return $this->charbon;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $essence valeur de l'attribut essence
	*/
	function get_essence()
	{
		return $this->essence;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $food valeur de l'attribut food
	*/
	function get_food()
	{
		return $this->food;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $alchimie valeur de l'attribut alchimie
	*/
	function get_alchimie()
	{
		return $this->alchimie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $roi valeur de l'attribut roi
	*/
	function get_roi()
	{
		return $this->roi;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $ministre_economie valeur de l'attribut ministre_economie
	*/
	function get_ministre_economie()
	{
		return $this->ministre_economie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $ministre_militaire valeur de l'attribut ministre_militaire
	*/
	function get_ministre_militaire()
	{
		return $this->ministre_militaire;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $capitale_hp valeur de l'attribut capitale_hp
	*/
	function get_capitale_hp()
	{
		return $this->capitale_hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $fin_raz_capitale valeur de l'attribut fin_raz_capitale
	*/
	function get_fin_raz_capitale()
	{
		return $this->fin_raz_capitale;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $id valeur de l'attribut
	* @return none
	*/
	function set_id($id)
	{
		$this->id = $id;
		$this->champs_modif[] = 'id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $taxe_time valeur de l'attribut
	* @return none
	*/
	function set_taxe_time($taxe_time)
	{
		$this->taxe_time = $taxe_time;
		$this->champs_modif[] = 'taxe_time';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $race valeur de l'attribut
	* @return none
	*/
	function set_race($race)
	{
		$this->race = $race;
		$this->champs_modif[] = 'race';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $capitale valeur de l'attribut
	* @return none
	*/
	function set_capitale($capitale)
	{
		$this->capitale = $capitale;
		$this->champs_modif[] = 'capitale';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $star valeur de l'attribut
	* @return none
	*/
	function set_star($star)
	{
		$this->star = $star;
		$this->champs_modif[] = 'star';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $point_victoire valeur de l'attribut
	* @return none
	*/
	function set_point_victoire($point_victoire)
	{
		$this->point_victoire = $point_victoire;
		$this->champs_modif[] = 'point_victoire';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $point_victoire_total valeur de l'attribut
	* @return none
	*/
	function set_point_victoire_total($point_victoire_total)
	{
		$this->point_victoire_total = $point_victoire_total;
		$this->champs_modif[] = 'point_victoire_total';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $star_nouveau_joueur valeur de l'attribut
	* @return none
	*/
	function set_star_nouveau_joueur($star_nouveau_joueur)
	{
		$this->star_nouveau_joueur = $star_nouveau_joueur;
		$this->champs_modif[] = 'star_nouveau_joueur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $taxe valeur de l'attribut
	* @return none
	*/
	function set_taxe($taxe)
	{
		$this->taxe = $taxe;
		$this->champs_modif[] = 'taxe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $diplo_time valeur de l'attribut
	* @return none
	*/
	function set_diplo_time($diplo_time)
	{
		$this->diplo_time = $diplo_time;
		$this->champs_modif[] = 'diplo_time';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $honneur_candidat valeur de l'attribut
	* @return none
	*/
	function set_honneur_candidat($honneur_candidat)
	{
		$this->honneur_candidat = $honneur_candidat;
		$this->champs_modif[] = 'honneur_candidat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $bourg valeur de l'attribut
	* @return none
	*/
	function set_bourg($bourg)
	{
		$this->bourg = $bourg;
		$this->champs_modif[] = 'bourg';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $pierre valeur de l'attribut
	* @return none
	*/
	function set_pierre($pierre)
	{
		$this->pierre = $pierre;
		$this->champs_modif[] = 'pierre';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $bois valeur de l'attribut
	* @return none
	*/
	function set_bois($bois)
	{
		$this->bois = $bois;
		$this->champs_modif[] = 'bois';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $eau valeur de l'attribut
	* @return none
	*/
	function set_eau($eau)
	{
		$this->eau = $eau;
		$this->champs_modif[] = 'eau';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $sable valeur de l'attribut
	* @return none
	*/
	function set_sable($sable)
	{
		$this->sable = $sable;
		$this->champs_modif[] = 'sable';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $charbon valeur de l'attribut
	* @return none
	*/
	function set_charbon($charbon)
	{
		$this->charbon = $charbon;
		$this->champs_modif[] = 'charbon';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $essence valeur de l'attribut
	* @return none
	*/
	function set_essence($essence)
	{
		$this->essence = $essence;
		$this->champs_modif[] = 'essence';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $food valeur de l'attribut
	* @return none
	*/
	function set_food($food)
	{
		$this->food = $food;
		$this->champs_modif[] = 'food';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $alchimie valeur de l'attribut
	* @return none
	*/
	function set_alchimie($alchimie)
	{
		$this->alchimie = $alchimie;
		$this->champs_modif[] = 'alchimie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $roi valeur de l'attribut
	* @return none
	*/
	function set_roi($roi)
	{
		$this->roi = $roi;
		$this->champs_modif[] = 'roi';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $ministre_economie valeur de l'attribut
	* @return none
	*/
	function set_ministre_economie($ministre_economie)
	{
		$this->ministre_economie = $ministre_economie;
		$this->champs_modif[] = 'ministre_economie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $ministre_militaire valeur de l'attribut
	* @return none
	*/
	function set_ministre_militaire($ministre_militaire)
	{
		$this->ministre_militaire = $ministre_militaire;
		$this->champs_modif[] = 'ministre_militaire';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $capitale_hp valeur de l'attribut
	* @return none
	*/
	function set_capitale_hp($capitale_hp)
	{
		$this->capitale_hp = $capitale_hp;
		$this->champs_modif[] = 'capitale_hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $fin_raz_capitale valeur de l'attribut
	* @return none
	*/
	function set_fin_raz_capitale($fin_raz_capitale)
	{
		$this->fin_raz_capitale = $fin_raz_capitale;
		$this->champs_modif[] = 'fin_raz_capitale';
	}
//fonction
	function get_diplo($race_joueur)
	{
		if (!isset($this->diplos))
		{
			$this->get_diplos();
		}
		return $this->diplos[$race_joueur];
	}

	function get_diplos()
	{
		global $db;
		if (!isset($this->diplos))
		{
			if($this->id != 0)
			{
				//Sélection de la diplomatie
				$requete_diplo = "SELECT * FROM diplomatie where race = '$this->race'";
				$req_diplo = $db->query($requete_diplo);
				$this->diplos = $db->read_assoc($req_diplo);
			}
			else
				$this->diplos = false;
		}
		return $this->diplos;
	}

	function get_taxe_diplo($race_joueur)
	{
		$diplo = $this->get_diplo($race_joueur);
		//Calcul de la taxe
		switch($diplo)
		{
			case 127 : // même royaume
				$this->taxe_diplo = floor($this->taxe / 4);
			break;
			case 0 : // Alliance fraternelle
				$this->taxe_diplo = ceil($this->taxe / 4);
			break;
			case 1 : // Alliance
				$this->taxe_diplo = floor($this->taxe / 3);
			break;
			case 2 : // Paix durable
				$this->taxe_diplo = ceil($this->taxe / 3);
			break;
			case 3 : // Paix
				$this->taxe_diplo = floor($this->taxe / 2);
			break;
			case 4 : // En bons termes
				$this->taxe_diplo = ceil($this->taxe / 2);
			break;
			case 5 : // Neutre
				$this->taxe_diplo = ceil($this->taxe / 1.5);
			break;
			case 6 : // Mauvais termes
				$this->taxe_diplo = ceil($this->taxe / 1);
			break;
			case 7 : // Guerre
				$this->taxe_diplo = 0;
			break;
			case 8 : // Guerre durable
				$this->taxe_diplo = 0;
			break;
			case 9 : // Ennemis
				$this->taxe_diplo = 0;
			break;
			case 10 : // Ennemis eternels
				$this->taxe_diplo = 0;
			break;
			case 11 : // ?
				$this->taxe_diplo = 0;
			break;
		}
		return $this->taxe_diplo;
	}

	function change_diplo($royaume, $changement = '+')
	{
		if($this->diplo_time[$royaume->get_nom()] > time()) return false;
		else
		{
			$diplo = $this->get_diplo($royaume->get_nom());
			if($changement == '+' AND $diplo >= 9)
			{
				if(verif_nb_diplo(array(10, 11)) > 2) return false;
				elseif($diplo == 10 AND verif_nb_diplo(array(11)) > 0) return false;
				return true;
			}
			elseif($changement == '-' AND $diplo <= 3)
			{
				if($diplo == 3 AND verif_nb_diplo(array(1, 2)) > 2) return false;
				elseif($diplo == 2 AND verif_nb_diplo(array(1))) return false;
				return true;
			}
			return true;
		}
	}

	function verif_nb_diplo($valeurs)
	{
		$nb = 0;
		foreach($valeurs as $diplomatie)
		{
			if(in_array($diplomatie, $valeurs)) $nb++;
		}
		return $nb;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return le nombre d'habitants
	*/
	function get_habitants()
	{
		global $db;
		$requete = $db->query("SELECT COUNT(id) as count FROM perso WHERE race = '".$this->get_race()."' AND statut = 'actif' AND level > 0");
		$row = $db->read_row($requete);
		return $row[0];
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return le nombre d'habitants très actif
	*/
	function get_habitants_actif()
	{
		global $db;
		$semaine = time() - self::duree_actif;
		$ref_ta = self::get_niveau_ref_actifs();

		$requete = $db->query("SELECT COUNT(*) as count FROM perso WHERE race = '".$this->get_race()."' AND level >= $ref_ta AND dernier_connexion > ".$semaine." AND statut = 'actif'");
		$row = $db->read_row($requete);
		return $row[0];

	}
	
	static function get_niveau_ref_actifs()
	{
		global $db;
		$requete = $db->query("select sum(level)/count(id) moy from perso WHERE statut = 'actif'");
		$row = $db->read_row($requete);
		$ref_ta = floor($row[0] - 1.5); // Bastien : on fait -1.5 pour eviter
		if ($ref_ta < 1)                // les escaliers, il faut qu'une race
		  $ref_ta = 1;                  // soit vraiment a la bourre pour
		elseif($ref_ta > 4)             // creer des grosses marches
		   $ref_ta = 4;                 // On récupère le nombre d'habitants très
                                    // actifs suivant le niveau moyen
    return $ref_ta;
  }
  ///@todo à centraliser
  const duree_actif = 172800;//3600 * 24 * 2

	function add_point_victoire($nombre)
	{
		$this->set_point_victoire($this->point_victoire + $nombre);
		$this->set_point_victoire_total($this->point_victoire_total + $nombre);
	}

	function add_forgeron($valeur)
	{
		global $db;
		$requete = "UPDATE argent_royaume SET forgeron = forgeron + ".$valeur." WHERE race = '".$this->race."'";
		$db->query($requete);
	}

	function add_armurerie($valeur)
	{
		global $db;
		$requete = "UPDATE argent_royaume SET armurerie = armurerie + ".$valeur." WHERE race = '".$this->race."'";
		$db->query($requete);
	}
	
	function add_star_taxe($taxe, $source=null)
	{
		global $db;
		$this->set_star( $this->star + $taxe );
		switch($source)
		{
		case 'arme':
			$champ = 'forgeron';
			break;
		case 'armure':
			$champ = 'armurerie';
			break;
		case 'accessoire':
			$champ = 'enchanteur';
			break;
		case 'sort_jeu':
		case 'sort_combat':
			$champ = 'ecole_magie';
			break;
		case 'comp_jeu':
		case 'comp_combat':
			$champ = 'ecole_combat';
			break;
		case 'teleport':
			$champ = 'teleport';
			break;
		case 'taverne':
			$champ = 'taverne';
			break;
		case 'dressage':
		case 'alchimiste':
			$champ = 'magasin';
			break;
		case 'ecurie':
		case 'terrain':
		default:
			$champ = false;		
		}
		if( $champ )
		{
			$requete = 'UPDATE argent_royaume SET '.$champ.' = '.$champ.' + '.$taxe.' WHERE race = "'.$this->race.'"';
			$db->query($requete);
		}
	}

	function total_ressources()
	{
		return ($this->bois + $this->charbon + $this->eau + $this->essence + $this->food + $this->pierre + $this->sable);
	}

	function supprime_ressources($perte)
	{
    $res = array('bois', 'charbon', 'eau', 'essence', 'food', 'pierre', 'sable');
    $types = array();
    foreach( $res as $type )
    {
      if( $this->$type >= 1000 )
        $types[] = $type;
    }
    if( count($types) == 0 )
      return false;
    $type = $types[rand(0,count($types)-1)];
    $methode = 'set_'.$type;
    $this->$methode($this->$type - $perte);
    return $type;
	}

	function verif_hp()
	{
		if($this->id && $this->capitale_hp <= 0 && $this->fin_raz_capitale < time())
		{
			$this->set_capitale_hp(30000);
			$this->sauver();
		}
	}

	function get_constructions_ville($destructible = false)
	{
		global $db;
		if($destructible) $and = ' AND construction_ville.hp > 0 AND (batiment_ville.level > 1 OR construction_ville.statut = \'actif\')';
		else $and = '';
		$requete = "SELECT construction_ville.id as id, id_batiment, statut, construction_ville.hp as hp, level FROM `construction_ville` RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE `id_royaume` = ".$this->id.$and;
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$this->constructions_ville[] = $row;
		}
		return $this->constructions_ville;
	}

  private $level_mur;
	function get_pp()
	{
		global $db;
		$requete = "SELECT statut, level FROM `construction_ville` RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE `id_royaume` = ".$this->id.$and." AND type = 'mur'";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		if($row['statut'] == 'actif')
		{
			$this->pp = 250 * ($row['level'] * $row['level']) + 1000 * $row['level'];
      $this->level_mur = $row['level'];
		}
		else
    {
      $this->pp = 100;
      print_debug("Le niveau du mur est réduit à 0 car il est détruit<br/>");
      $this->level_mur = 0;
    }
		return $this->pp;
	}

	function get_motk()
	{
		if(!isset($this->motk))
		{
			$this->motk_array = motk::create('id_royaume', $this->id);
			$this->motk = $this->motk_array[0];
		}
		return $this->motk;
	}

	function set_motk($texte)
	{
		if(!isset($this->motk)) $this->get_motk();
		$this->motk->set_message($texte);
		$this->motk->set_date(time());
		$this->motk->sauver();
	}

	function set_propagande($texte)
	{
		if(!isset($this->motk)) $this->get_motk();
		$this->motk->set_propagande($texte);
		$this->motk->sauver();
	}

	function gain_star($star, $source)
	{
		$this->set_star($this->star + $star);
		$requete = "UPDATE argent_royaume SET $source = $source + $star WHERE race = '$this->race'";
		global $db;
		$db->query($requete);
	}

  function get_level_mur()
  {
    if (!isset($this->level_mur))
    {
      $this->get_pp();
    }
    print_debug("Le mur est à $this->level_mur <br/>");
    return $this->level_mur;
  }

	function is_raz()
	{
		return $this->get_fin_raz_capitale() > time();
	}
	
  static function supprime_bourg($royaume)
  {
  	global $db;
  	$requete = "UPDATE royaume SET bourg = bourg - 1 WHERE ID = ".$royaume." AND bourg > 0";
  	$db->query($requete);
  }

  function get_mult_victoire($roy_def)
  {
    return (100 + $roy_def->get_point_victoire_total()) / (100 + $this->get_point_victoire_total());
  }
  
	function get_niveau_batiment($type)
	{
		global $db;
		switch($type)
		{
		case 'comp':
		case 'comp_combat':
		case 'comp_jeu':
			$batiment = 'ecole_combat';
			break;
		case 'sort':
		case 'sort_combat':
		case 'sort_jeu':
			$batiment = 'ecole_magie';
			break;
		case 'arme':
			$batiment = 'forgeron';
			break;
		case 'armure':
			$batiment = 'armurerie';
			break;
		case 'accessoire':
			$batiment = 'enchanteur';
			return 1;
			break;
		case 'dressage':
			$batiment = 'dresseur';
			return 1;
			break;
		}
		///@todo à améliorer
		$requete = 'SELECT level, statut, c.hp, b.hp AS hp_max, nom FROM construction_ville AS c LEFT JOIN batiment_ville AS b ON c.id_batiment = b.id WHERE b.type = "'.$batiment.'" AND c.id_royaume = '.$this->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		// Si le batiment est inactif, on le met au niveau 1
		return $row['statut'] == 'inactif' ? 1 : $row['level'];
	}
	function get_entretien()
	{
		global $db;
		/// @todo passer à l'objet.
		// Bâtiments internes
		$requete = "SELECT SUM(entretien) FROM construction_ville RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE construction_ville.statut = 'actif' AND id_royaume = ".$this->get_id();
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$entretien = $row[0];
		// Bâtiments externes
		$requete = "SELECT SUM(entretien) FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id WHERE royaume = ".$this->get_id()." AND x <= 190 AND y <= 190 ORDER BY entretien DESC";
		$req = $db->query($requete);
		$row = $db->read_array($req);
		$entretien += $row[0];
		
		return $entretien * $this->facteur_entretien;
	}
	
	static function get_royaume_rumeur($info, $plus, $class, $vrai)
	{
		global $db;
		if( !$vrai )
		{
			$requete = 'SELECT * FROM royaume WHERE id > 0 ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			return new royaume($row);
		}
		$sens = $plus ? ' DESC' : ' ASC';
		switch($info)
		{
		case 'batint':
			$requete = 'SELECT r.*, SUM(level) as niv FROM royaume AS r INNER JOIN construction_ville AS c ON c.id_royaume=r.id INNER JOIN batiment_ville AS b ON b.id = c.id_batiment WHERE r.id > 0 GROUP BY r.id ORDER BY niv '.$sens.' LIMIT '.$class.', 1';
			break;
		case 'bourg':
		case 'mine':
		case 'fort':
		case 'tour':
		case 'mur':
		case 'arme_de_siege':
			$requete = 'SELECT r.*, COUNT(*) as nbr FROM royaume AS r INNER JOIN construction AS c ON c.id_royaume=r.id INNER JOIN batiment AS b ON b.id = c.id_batiment WHERE r.id > 0 AND b.type = "'.$info.'" GROUP BY r.id ORDER BY nbr '.$sens.' LIMIT '.$class.', 1';
			break;
		case 'entretien':
			$requete = 'SELECT r.*, SUM(entr)*facteur_entretien AS entr_tot FROM (SELECT c.id_royaume, SUM(entretien) AS entr FROM construction AS c INNER JOIN batiment AS b ON c.id_batiment=b.id GROUP BY c.id_royaume UNION ALL SELECT c.id_royaume, SUM(entretien) AS entr FROM construction_ville AS c INNER JOIN batiment_ville AS b ON c.id_batiment=b.id GROUP BY c.id_royaume) INNER JOIN royaume AS r ON r.id = id_royaume GROUP by r.id ORDER BY nbr '.$sens.' LIMIT '.$class.', 1';
			break;
		case 'case':
			$requete = 'SELECT r.*, COUNT(*) AS nbr FROM map as m INNER JOIN royaume AS r ON r.id = m.royaume WHERE x <= 190 AND y <= 190 GROUP BY r.id ORDER BY nbr '.$sens.' LIMIT '.$class.', 1';
			break;
		default:
			$requete = 'SELECT * FROM royaume WHERE id > 0 ORDER BY '.$info.$sens.' LIMIT '.$class.', 1';
		}
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return new royaume($row);
	}
}
?>
