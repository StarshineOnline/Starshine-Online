<?php // -*- php -*-
/**
 * @file perso.class.php
 * Gestion des personnages joueurs
 */
include_once(root.'class/gemme.class.php');
 
/**
 * Classe représentant un personnage joueur
 */
class perso extends entite
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : classe, rang, niveau,
	 * stars, mort, points crimes, …
	 */
  // @{
	private $classe;       ///< Nom de la classe.
	private $classe_id;    ///< Id de la classe.
	private $exp;          ///< Points d'expérience.
	private $point_sso;    ///< Points Shines.
	private $honneur;      ///< Points d'honneur.
	private $reputation;   ///< Points de réputation.
	public $grade;         ///< Grade.
	private $star;         ///< Nombre de stars.
	private $frag;         ///< Nombre de personnages tués.
	private $mort;         ///< Nombre fois où le personnage est mort.
	private $statut;       ///< Statut du joueur.
	private $fin_ban;      ///< Date de la fin du ban.
	private $crime;        ///< Points crime.
	private $amende;       ///< Amende à payer.
	private $cache_classe; ///< Indique à qui il faut cacher la classe.
	private $cache_stat;   ///< Indique à qui il faut cacher les stats.
	private $cache_niveau; ///< Indique à qui il faut cacher le niveau.
	private $beta;         ///< Points de beta-test.
	private $options_tab = false;  ///< Tableau des options
	public $tuto; ///< étape en cours du Tutoriel
	private $date_creation;  ///< date de création du personnage
	
	function set_tuto($tuto)
	{
		$this->tuto = $tuto;
		$this->champs_modif[] = 'tuto';
	}
	
	/// Renvoie l'étape à laquelle est arreté le tutoriel du perso
	function get_tuto()
	{
		return $this->tuto;
	}
	
	/// Modifie le nom du personnage
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}
	/// Modifie la race
	function set_race($race)
	{
		$this->race = $race;
		$this->champs_modif[] = 'race';
	}
	/**
	 * Renvoie le nom de la classe du perso
	 * Si on définit un spectateur, renvoie la classe du perso visible par le perso spectateur
	 *
	 * @param perso $spectateur Personnage qui voit le perso, ou null si on ne se place pas d'un point de vue spectateur
	 * @return string
 	*/
	function get_classe($spectateur = null)
	{
		if( is_null($spectateur) )
			return $this->classe;
		else
		{
			$classe = $this->classe;
			
			if( $this->est_cache_classe($spectateur) )
			{
				$classe = "combattant";
			}
			
			return $classe;
		}
	}
	/**
	 * Renvoie l'url relative de l'image de classe du perso
	 * Si on définit un spectateur, renvoie l'url relative de l'image de classe du perso visible par le perso spectateur
	 *
	 * @param string $root Préfixe de l'url relative (utile si on veut afficher une image depuis une url appelante pointant vers un autre dossier que le dossier parent du dossier image/)
	 * @param string $resolution 'high' (par défaut) ou 'low'
	 * @param perso $spectateur Personnage qui voit le perso, ou null si on ne se place pas d'un point de vue spectateur
	 * @return string
 	*/
	function get_image($root = '', $resolution = 'high', $spectateur = null)
	{
		global $Tclasse; // $Tclasse est contenue dans ./inc/classe.inc.php
		$image = '';
		
		$dossierPersonnage = 'personnage';
		if($resolution == 'low')
		{
			$dossierPersonnage = 'personnage_low';
		}
		$race = $this->get_race_a();
		$classe = $this->get_classe($spectateur);
		
		$imageBase = $root."image/".$dossierPersonnage."/".$race."/".$race;
		
		// Vérification que l'image de classe existe
		$image = $imageBase."_".$Tclasse[$classe]["type"].".png";
		if( !file_exists($image) )
		{
			$image = $imageBase."_".$Tclasse[$classe]["type"].".gif";
			if( !file_exists($image) )
			{
				$image = $imageBase.".png";
				if( !file_exists($image) )
				{
					$image = $imageBase.".gif";
					if( !file_exists($image) )
					{
						// Si aucun des fichiers n'existe autant ne rien mettre
						$image = "";
					}
				}
			}
		}
		
		return $image;
	}
	/// Modifie le nom de la classe
	function set_classe($classe)
	{
		$this->classe = $classe;
		$this->champs_modif[] = 'classe';
	}
	/// Renvoie l'id de la classe
	function get_classe_id()
	{
		return $this->classe_id;
	}
	/// Modifie l'id de la classe
	function set_classe_id($classe_id)
	{
		$this->classe_id = $classe_id;
		$this->champs_modif[] = 'classe_id';
	}
	/// Renvoie les points d'expérience.
	function get_exp()
	{
		return $this->exp;
	}
	/// Modifie les points d'expérience.
	function set_exp($exp)
	{
		$this->exp = $exp;
		$this->champs_modif[] = 'exp';
	}
	/// Modifie le niveau
	function set_level($level)
	{
		$this->level = $level;
		$this->champs_modif[] = 'level';
	}
	/// Renvoie les points shines.
	function get_point_sso()
	{
		return $this->point_sso;
	}
	/// Modifie les points shines.
	function set_point_sso($point_sso)
	{
		$this->point_sso = $point_sso;
		$this->champs_modif[] = 'point_sso';
	}
  /**
   * Renvoie les bonus shines
   * @param  $id_bonus    Id du bonue, false s'il faut tous les renvoyer.
   * @return    bonus demandé ou tableau des bonus.    
   */  
	function get_bonus_shine($id_bonus = false)
	{
		if(!$id_bonus)
		{
			if(!isset($this->bonus_shine))
				$this->bonus_shine = bonus_perso::create(array('id_perso'), array($this->id));
			return $this->bonus_shine;
		}
		else
		{
			if(!isset($this->bonus_shine))
				$this->get_bonus_shine();
			foreach($this->bonus_shine as $bonus)
			{
				if($bonus->get_id_bonus() == $id_bonus)
					return $bonus;						
			}
		}
		return false;
	}
	/**
	 * Permet d'ajouter un bonus shine au perso
	 *
	 * @param int $idBonus Identifiant du bonus shine
	 * @param string $valeur Chaîne de caractères représentant l'état du bonus shine pour le perso
	 * @param int $etat Entier représentant l'état du bonus shine pour le perso
 	*/
	function ajout_bonus_shine($idBonus, $valeur = '', $etat = 0)
	{
		$bonusPerso = new bonus_perso(0, $this->get_id(), $idBonus, $valeur, $etat);
		if(isset($this->bonus_shine))
			$this->bonus_shine[] = $bonusPerso;
		$bonusPerso->sauver();
		
		// On modifie cache_classe, cache_stat ou cache_niveau, si nécessaire
		if($idBonus == bonus_perso::CACHE_CLASSE_ID)
			$this->set_cache_classe($etat);
		elseif($idBonus == bonus_perso::CACHE_STATS_ID)
			$this->set_cache_stat($etat);
		elseif($idBonus == bonus_perso::CACHE_NIVEAU_ID)
			$this->set_cache_niveau($etat);
		// On sauve cache_classe, cache_stat ou cache_niveau dans la bdd, si nécessaire
		$this->sauver();
	}
	/// Renvoie les points d'honneur.
	function get_honneur()
	{
		return $this->honneur;
	}
	/// Modifie les points d'honneur.
	function set_honneur($honneur)
	{
		$this->honneur = $honneur;
		$this->champs_modif[] = 'honneur';
	}
	/// Renvoie les points de réputation.
	function get_reputation()
	{
		return $this->reputation;
	}
	/// Modifie les points de réputation.
	function set_reputation($reputation)
	{
		$this->reputation = $reputation;
		$this->champs_modif[] = 'reputation';
	}
	/// Renvoie le grade
	function get_grade()
	{
		if(!isset($this->grade)) {
			$this->grade = new grade($this->rang_royaume);
			if ($this->is_buff('buff_charisme')) $this->grade->add_bonus_buff(2);
			if ($this->is_buff('potion_buff')) $this->grade->add_bonus_buff(2);
			if ($this->is_buff('debuff_charisme')) 
        $this->grade->add_bonus_buff(-$this->get_buff('debuff_charisme', 'effet'));
		}
		return $this->grade;
	}
	/// Modifie le rang au sein du royaume
	function set_rang_royaume($rang_royaume)
	{
		$this->rang_royaume = $rang_royaume;
		$this->champs_modif[] = 'rang_royaume';
	}
	/// Renvoie les stars.
	function get_star()
	{
		return $this->star;
	}
  /// Ajoute des stars. S'assure que la valeur finale ne sois pas négative.
	function add_star($add_star)
  {
    $this->set_star($this->star + $add_star);
    if ($this->star < 0)
      $this->star = 0;
  }
	/// Modifie les stars.
	function set_star($star)
	{
		$this->star = $star;
		$this->champs_modif[] = 'star';
		
		// Augmentation du compteur de l'achievement
		$achiev = $this->get_compteur('stars');
		$achiev->set_compteur($this->star);
		$achiev->sauver();
	}
  /// Renvoie le nombre de personnages tués
	function get_frag()
	{
		return $this->frag;
	}
  /// Modifie le nombre de personnages tués
	function set_frag($frag)
	{
		$this->frag = $frag;
		$this->champs_modif[] = 'frag';
		
		// Augmentation du compteur de l'achievement
		$achiev = $this->get_compteur('kill');
		$achiev->set_compteur($this->frag);
		$achiev->sauver();
		
		// Augmentation du compteur de l'achievement
		$achiev = $this->get_compteur('ratio');
		if ($this->mort == 0)
			$achiev->set_compteur($this->frag / 1);
		else
			$achiev->set_compteur($this->frag / $this->mort);
		$achiev->sauver();
	}
	/// Renvoie le nombre fois où le personnage est mort.
	function get_mort()
	{
		return $this->mort;
	}
	/// Modifie le nombre fois où le personnage est mort.
	function set_mort($mort)
	{
		$this->mort = $mort;
		$this->champs_modif[] = 'mort';
		
		// Augmentation du compteur de l'achievement
		$achiev = $this->get_compteur('mort');
		$achiev->set_compteur($this->mort);
		$achiev->sauver();
	}
	/// Renvoie le statut
	function get_statut()
	{
		return $this->statut;
	}
	/// Modifie le statut
	function set_statut($statut)
	{
		$this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}
	/// Renvoie la date de fin du ban
	function get_fin_ban()
	{
		return $this->fin_ban;
	}
	/// Modifie la date de fin du ban
	function set_fin_ban($fin_ban)
	{
		$this->fin_ban = $fin_ban;
		$this->champs_modif[] = 'fin_ban';
	}
	/// Renvoie le nombre de points crime
	function get_crime()
	{
		return $this->crime;
	}
	/// Modifie le nombre de points crime
	function set_crime($crime)
	{
		$this->crime = $crime;
		$this->champs_modif[] = 'crime';
	}
	/// Renvoie le montant de l'amende
	function get_amende()
	{
		return $this->amende;
	}
	/// Modifie le montant de l'amende
	function set_amende($amende)
	{
		$this->amende = $amende;
		$this->champs_modif[] = 'amende';
	}
	/// Renvoie à qui il faut cacher la classe
	function get_cache_classe()
	{
		return $this->cache_classe;
	}
	/// Modifie à qui il faut cacher la classe
	function set_cache_classe($cache_classe)
	{
		$this->cache_classe = $cache_classe;
		$this->champs_modif[] = 'cache_classe';
	}
	/**
	 * Définit si le perso a choisi de cacher sa classe pour le spectateur donné en paramètre
	 *
	 * @param perso $spectateur Personnage qui voit le perso
	 * @return bool Returns true si le perso a choisi de cacher sa classe au $spectateur, false sinon
 	*/
	function est_cache_classe($spectateur)
	{
		$spectateurId = 0;
		$spectateurRace = '';
		if( $spectateur instanceof perso )
		{
			$spectateurId = $spectateur->get_id();
			$spectateurRace = $spectateur->get_race();
		}
		
		if( $this->get_id() != $spectateurId )
		{
			if( $this->get_cache_classe() == 2 || ($this->get_cache_classe() == 1 && $this->get_race() != $spectateurRace) ){
				return true;
			}
		}
		
		return false;
	}
	/// Renvoie à qui il faut cacher les stats
	function get_cache_stat()
	{
		return $this->cache_stat;
	}
	/// Modifie à qui il faut cacher les stats
	function set_cache_stat($cache_stat)
	{
		$this->cache_stat = $cache_stat;
		$this->champs_modif[] = 'cache_stat';
	}
	/**
	 * Définit si le perso a choisi de cacher ses stats pour le spectateur donné en paramètre
	 *
	 * @param perso $spectateur Personnage qui voit le perso
	 * @return bool Returns true si le perso a choisi de cacher ses stats au $spectateur, false sinon
 	*/
	function est_cache_stat($spectateur)
	{
		$spectateurId = 0;
		$spectateurRace = '';
		if( $spectateur instanceof perso )
		{
			$spectateurId = $spectateur->get_id();
			$spectateurRace = $spectateur->get_race();
		}
		
		if( $this->get_id() != $spectateurId )
		{
			if( $this->get_cache_stat() == 2 || ($this->get_cache_stat() == 1 && $this->get_race() != $spectateurRace) ){
				return true;
			}
		}
		
		return false;
	}
	/// Renvoie à qui il faut cacher le niveau
	function get_cache_niveau()
	{
		return $this->cache_niveau;
	}
	/// Modifie à qui il faut cacher le niveau
	function set_cache_niveau($cache_niveau)
	{
		$this->cache_niveau = $cache_niveau;
		$this->champs_modif[] = 'cache_niveau';
	}
	/**
	 * Définit si le perso a choisi de cacher son niveau pour le spectateur donné en paramètre
	 *
	 * @param perso $spectateur Personnage qui voit le perso
	 * @return bool Returns true si le perso a choisi de cacher son niveau au $spectateur, false sinon
 	*/
	function est_cache_niveau($spectateur)
	{
		$spectateurId = 0;
		$spectateurRace = '';
		if( $spectateur instanceof perso )
		{
			$spectateurId = $spectateur->get_id();
			$spectateurRace = $spectateur->get_race();
		}
		
		if( $this->get_id() != $spectateurId )
		{
			if( $this->get_cache_niveau() == 2 || ($this->get_cache_niveau() == 1 && $this->get_race() != $spectateurRace) ){
				return true;
			}
		}
		
		return false;
	}
	/**
	 * Définit si le perso a choisi de cacher son grade pour le spectateur donné en paramètre
	 *
	 * @param perso $spectateur Personnage qui voit le perso
	 * @return bool Returns true si le perso a choisi de cacher son grade au $spectateur, false sinon
 	*/
	function est_cache_grade($spectateur)
	{
		$spectateurId = 0;
		$spectateurRace = '';
		if( $spectateur instanceof perso )
		{
			$spectateurId = $spectateur->get_id();
			$spectateurRace = $spectateur->get_race();
		}
		
		$bonusCacheGrade = $this->get_bonus_shine(bonus_perso::CACHE_GRADE_ID);
		// Si le $spectateur n'est pas le perso lui-même  &&  Si le "bonus cache grade" du perso est défini
		if( $this->get_id() != $spectateurId && $bonusCacheGrade instanceof bonus_perso )
		{
			if( $bonusCacheGrade->get_etat() == 2 || ($bonusCacheGrade->get_etat() == 1 && $this->get_race() != $spectateurRace) ){
				return true;
			}
		}
		
		return false;
	}
	/// Renvoie le nombre de points beta
	function get_beta()
	{
		return $this->beta;
	}
	/// Modifie le nombre de points beta
	function set_beta($beta)
	{
		$this->beta = $beta;
		$this->champs_modif[] = 'beta';
	}
	/// Renvoie le tableau d'options
	function get_options()
	{
		if (!$this->options_tab) {
			global $db;
			$this->options_tab = array();
			$requete = "select nom, valeur from options where id_perso = $this->id";
			$req = $db->query($requete);
			if ($req) while ($row = $db->read_row($req)) {
				$this->options_tab[$row[0]] = $row[1];
			}
		}
		return $this->options_tab;
	}
	/**
	 * Renvoie une option en particulier
	 * @param  $name   nom de l'option
	 */
	function get_option($name)
	{
		if (!$this->options_tab) {
			$this->get_options();
		}
		if (array_key_exists($name, $this->options_tab))
			return $this->options_tab[$name];
		else
			return false;
	}
	/// Renvoie la date de création du personnage
	function get_date_creation()
	{
		return $this->date_creation;
	}
	/// Modifie la date de création du personnage
	function set_date_creation($date_creation)
	{
		$this->date_creation = $date_creation;
		$this->champs_modif[] = 'date_creation';
	}
	/**
	 * Permet de savoir si le joueur est mort ou pas
	 *
	 * @return bool returns true si le perso est mort, false sinon
 	*/
	function est_mort()
	{
		return ($this->get_hp() <= 0);
	}
  // @}
  
  /**
   * @name Informations joueur
   * Informations sur le joueur : hash du mot-de-passe, e-mail, dernière connection.
   */
  // @{
	private $password;           ///< hash du mot-de-passe.
	private $email;              ///< e-mail.
	private $dernier_connexion;  ///< Date de la dernière connexion.
	private $id_joueur;       ///< Id du joueur possedant le perso
	/// Renvoie le hash du mot-de-passe.
	function get_password()
	{
		return $this->password;
	}
	/// Modifie le hash du mot-de-passe.
	function set_password($password)
	{
		$this->password = $password;
		$this->champs_modif[] = 'password';
	}
	/// Renvoie l'e-mail.
	function get_email()
	{
		return $this->email;
	}
	/// Modifie l'e-mail.
	function set_email($email)
	{
		$this->email = $email;
		$this->champs_modif[] = 'email';
	}
	/// Renvoie la date de la dernière connexion.
	function get_dernier_connexion()
	{
		return $this->dernier_connexion;
	}
	/// Modifie la date de la dernière connexion.
	function set_dernier_connexion($dernier_connexion)
	{
		$this->dernier_connexion = $dernier_connexion;
		$this->champs_modif[] = 'dernier_connexion';
	}
  /// Renvoie l'id du joueur possedant le perso
  function get_id_joueur()
  {
    return $this->id_joueur;
  }
  /// Modifie l'id du joueur possedant le perso
  function set_id_joueur($id_joueur)
  {
    $this->id_joueur = $id_joueur;
    $this->champs_modif[] = 'id_joueur';
  }
  // @}        
	
	/**
	 * @name Caractéristiques
	 * Données et méthodes liées aux caractéristiques du personnage : constitution,
	 * force, dextérité, puissance, volonté et énergie.      	
	 */	
	// @{
	private $forcex;
	/// Renvoie la constitution
	function get_vie($base = false)
	{
		if ($base)
			return $this->vie;
		else
			return $this->vie + $this->get_bonus_permanents('vie');
	}
	function get_constitution($base = false)
	{
		return $this->get_vie($base);
	}
	/// Modifie la constitution
	function set_vie($vie)
	{
		$this->vie = $vie;
		$this->champs_modif[] = 'vie';
	}
	/// Renvoie la force
	function get_forcex($base = false)
	{
		if ($base)
			return $this->forcex;
		else
			return $this->forcex + $this->get_bonus_permanents('forcex');
	}
	function get_force($base = false) 
	{ 
		return $this->get_forcex($base); 
	}
	/// Modifie la force
	function set_forcex($forcex)
	{
		$this->forcex = $forcex;
		$this->champs_modif[] = 'forcex';
	}
	function set_force($force) 
	{ 
		$this->set_forcex($force); 
	}
	/// Renvoie la dextérité
	function get_dexterite($base = false)
	{
		if ($base)
			return $this->dexterite;
		else
			return $this->dexterite + $this->get_bonus_permanents('dexterite');
	}
	/// Modifie la dextérité
	function set_dexterite($dexterite)
	{
		$this->dexterite = $dexterite;
		$this->champs_modif[] = 'dexterite';
	}
	/// Renvoie la puissance
	function get_puissance($base = false)
	{
		if ($base)
			return $this->puissance;
    else
      return $this->puissance + $this->get_bonus_permanents('puissance');
	}
	/// Modifie la puissance
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
	}
	/// Renvoie la volonté
	function get_volonte($base = false)
	{
		if ($base)
			return $this->volonte;
		else
			return $this->volonte + $this->get_bonus_permanents('volonte');
	}
	/// Modifie la volonté
	function set_volonte($volonte)
	{
		$this->volonte = $volonte;
		$this->champs_modif[] = 'volonte';
	}
	/// Renvoie l'énergie
	function get_energie($base = false)
	{
		if ($base)
			return $this->energie;
    else
      return $this->energie + $this->get_bonus_permanents('energie');
	}
	/// Modifie l'énergie
	function set_energie($energie)
	{
		$this->energie = $energie;
		$this->champs_modif[] = 'energie';
	}
	/**
	 * Renvoie Le Coefficient modifiant le coût d'un sort à cause de l'affinité
	 * @param $comp  compétence de magie correspondante
	 */
  function get_affinite($comp)
  {
    global $Trace;
    return (1 - (($Trace[$this->get_race()]['affinite_'.$comp] - 5) / 10));
  }
  // @}
	
	/**
	 * @name Compétences
	 * Données et méthodes liées aux compténtences du personnage : mêlée, esquive,
	 * incatation, …
	 */	
	// @{
	private $identification; ///< Compétence "identification".
	private $craft;          ///< Ancienne compétence de craft.
	private $alchimie;       ///< compétence "alchimie".
	private $architecture;   ///< Compétence "architecture".
	private $forge;          ///< Compétence "forge".
	private $survie;         ///< Compétence "survie".
	private $dressage;       ///< Compétence "dressage".
	public $competences;     ///< Compétences que seules certaines classes peuvent avoir.
  /// Renvoie la mêlée
	function get_melee($base = false)
	{
		if ($base)
			return $this->melee;
		else
		{
			$melee = $this->melee + $this->get_bonus_permanents('melee');
			$melee *= 1 + $this->get_bonus_permanents('mult_melee')/100;
			$melee /= 1 + $this->get_bonus_permanents('div_melee')/100;
			return round($melee);
		}
	}
	/// Modifie la mêlée
	function set_melee($melee)
	{
		$this->melee = $melee;
		$this->champs_modif[] = 'melee';
	}
  /// Renvoie le tir à distance
	function get_distance($base = false)
	{
		if ($base)
			return $this->distance;
		else
		{
			$distance = $this->distance + $this->get_bonus_permanents('distance');
			$distance *= 1 + $this->get_bonus_permanents('mult_distance')/100;
			$distance /= 1 + $this->get_bonus_permanents('div_distance')/100;
			return round($distance);
		}
	}
	/// Modifie le tir à distance
	function set_distance($distance)
	{
		$this->distance = $distance;
		$this->champs_modif[] = 'distance';
	}
  /// Renvoie l'esquive
	function get_esquive($base = false)
	{
		if ($base)
			return $this->esquive;
		else
		{
			$esquive = $this->esquive + $this->get_bonus_permanents('esquive');
			$esquive *= 1 + $this->get_bonus_permanents('mult_esquive')/100;
			$esquive /= 1 + $this->get_bonus_permanents('div_esquive')/100;
			return round($esquive);
		}
	}
	/// Modifie l'esquive
	function set_esquive($esquive)
	{
		$this->esquive = $esquive;
		$this->champs_modif[] = 'esquive';
	}
  /// Renvoie le blocage
	function get_blocage($base = false)
	{
		if ($base)
			return $this->blocage;
		else
		{
			$blocage = $this->blocage + $this->get_bonus_permanents('blocage');
			$blocage *= 1 + $this->get_bonus_permanents('mult_blocage')/100;
			$blocage /= 1 + $this->get_bonus_permanents('div_blocage')/100;
			return round($blocage);
		}
	}
	/// Modifie le blocage
	function set_blocage($blocage)
	{
		$this->blocage = $blocage;
		$this->champs_modif[] = 'blocage';
	}
  /// Renvoie l'incantation
	function get_incantation($base = false)
	{
		if ($base)
			return $this->incantation;
		else
		{
			$incantation = $this->incantation + $this->get_bonus_permanents('incantation');
			$incantation *= 1 + $this->get_bonus_permanents('mult_incantation')/100;
			$incantation /= 1 + $this->get_bonus_permanents('div_incantation')/100;
			return round($incantation);
		}
	}
	/// Modifie l'incantation
	function set_incantation($incantation)
	{
		$this->incantation = $incantation;
		$this->champs_modif[] = 'incantation';
	}
  /// Renvoie la magie de la vie
	function get_sort_vie($base = false)
	{
		if ($base)
			return $this->sort_vie;
		else
		{
			$sort_vie = $this->sort_vie + $this->get_bonus_permanents('sort_vie');
			$sort_vie *= 1 + $this->get_bonus_permanents('mult_sort_vie')/100;
			$sort_vie /= 1 + $this->get_bonus_permanents('div_sort_vie')/100;
			return round($sort_vie);
		}
	}
	/// Modifie la magie de la vie
	function set_sort_vie($sort_vie)
	{
		$this->sort_vie = $sort_vie;
		$this->champs_modif[] = 'sort_vie';
	}
  /// Renvoie la magie élémentaire
	function get_sort_element($base = false)
	{
		if ($base)
			return $this->sort_element;
		else
		{
			$sort_element = $this->sort_element + $this->get_bonus_permanents('sort_element');
			$sort_element *= 1 + $this->get_bonus_permanents('mult_sort_element')/100;
			$sort_element /= 1 + $this->get_bonus_permanents('div_sort_element')/100;
			return round($sort_element);
		}
	}
	/// Modifie la magie élémentaire
	function set_sort_element($sort_element)
	{
		$this->sort_element = $sort_element;
		$this->champs_modif[] = 'sort_element';
	}
  /// Renvoie la nécromancie
	function get_sort_mort($base = false)
	{
		if ($base)
			return $this->sort_mort;
		else
		{
			$sort_mort = $this->sort_mort + $this->get_bonus_permanents('sort_mort');
			$sort_mort *= 1 + $this->get_bonus_permanents('mult_sort_mort')/100;
			$sort_mort /= 1 + $this->get_bonus_permanents('div_sort_mort')/100;
			return round($sort_mort);
		}
	}
	/// Modifie la nécromancie
	function set_sort_mort($sort_mort)
	{
		$this->sort_mort = $sort_mort;
		$this->champs_modif[] = 'sort_mort';
	}
  /// Renvoie la coméptence identification
	function get_identification($base = false)
	{
		if ($base)
			return $this->identification;
		else
			return $this->identification + $this->get_bonus_permanents('identification');
	}
	/// Modifie la compétence d'identification
	function set_identification($identification)
	{
		$this->identification = $identification;
		$this->champs_modif[] = 'identification';
	}
  /// Renvoie l'ancienne compétence de craft
	function get_craft($base = false)
	{
		if ($base)
			return $this->craft;
		else
			return $this->craft + $this->get_bonus_permanents('craft');
	}
	/// Modifie l'ancienne compétence de craft
	function set_craft($craft)
	{
		$this->craft = $craft;
		$this->champs_modif[] = 'craft';
	}
  /// Renvoie l'alchimie
	function get_alchimie($base = false)
	{
		if ($base)
			return $this->alchimie;
		$alchimie = $this->alchimie * (1 + $this->get_bonus_permanents('alchimie') / 100);
		if ($this->get_race() == 'scavenger')
			$alchimie *= 1.40;
		if($this->is_buff('globe_alchimie'))
			$alchimie *= 1 + $this->get_buff('globe_alchimie', 'effet')/100;
		return round($alchimie);
	}
	/// Modifie l'alchimie
	function set_alchimie($alchimie)
	{
		$this->alchimie = $alchimie;
		$this->champs_modif[] = 'alchimie';
	}
  /// Renvoie l'architecture
	function get_architecture($base = false)
	{
		if ($base)
			return $this->architecture;
		if ($this->get_race() == 'scavenger')
			$architecture * 1.20;
		$architecture = $this->architecture;
		$architecture *= 1 + $this->get_bonus_permanents('architecture') / 100;
		if($this->is_buff('globe_architecture'))
			$architecture *= 1 + $this->get_buff('globe_architecture', 'effet')/100;
		return round($architecture);
	}
	/// Modifie l'architecture
	function set_architecture($architecture)
	{
		$this->architecture = $architecture;
		$this->champs_modif[] = 'architecture';
	}
  /// Renvoie la forge
	function get_forge($base = false)
	{
		if ($base)
			return $this->forge;
		$forge = $this->forge;
		$forge *= 1 + $this->get_bonus_permanents('forge') / 100;
		if ($this->get_race() == 'scavenger')
			$forge *= 1.40;
		if($this->is_buff('globe_forge'))
			$forge *= 1 + $this->get_buff('globe_forge', 'effet')/100;
		return round($forge);
	}
	/// Modifie la forge
	function set_forge($forge)
	{
		$this->forge = $forge;
		$this->champs_modif[] = 'forge';
	}
  /// Renvoie la survie
	function get_survie()
	{
		return $this->survie;
	}
	/// Modifie la survie
	function set_survie($survie)
	{
		$this->survie = $survie;
		$this->champs_modif[] = 'survie';
	}
  /// Renvoie le dressage
	function get_dressage()
	{
		return $this->dressage;
	}
	/// Modifie le dressage
	function set_dressage($dressage)
	{
		$this->dressage = $dressage;
		$this->champs_modif[] = 'dressage';
	}
  /**
   * Renvoie la valeur de l'artisanat
   * L'artisanat est calculé à partir des 3 compétences d'artisanat : c'est 10 
   * fois la racine carré de leur somme.
   */       
	function get_artisanat($base = false)
	{
		return round(sqrt(($this->get_architecture($base) + $this->get_forge($base) + $this->get_alchimie($base) + $this->get_identification($base)) * 10));
	}
	/**
	 * Renvoie la compétence demandée
	 * @param  $comp_assoc   Compétence demandée.
	 * @param  $base         true s'il faut donner la valeur de base (sans les bonus).	 
	 */	 
	function get_comp($comp_assoc = '', $base = false)
	{
		$get = 'get_'.$comp_assoc;
		if(method_exists($this, $get)) return $this->$get($base);
		else return $this->get_competence($comp_assoc, false, $base);
	}
  /**
	 * Modifie la compétence indiquée
	 * @param  $comp_assoc   Compétence à modifier.
	 * @param  $valeur       Nouvelle valeur.
	 */	 
	function set_comp($comp_assoc = '', $valeur = '')
	{
		$set = 'set_'.$comp_assoc;
		if(method_exists($this, $set)) $this->$set($valeur);
		else $this->set_competence($comp_assoc, $valeur);
	}
  /**
   * Indique si le personnage à une certaine compétence.
   * N'est valable que pour les compétences que seules certaines classes peuvent
   * avoir (maitrises, sorts de groupe, …).
   * @param  $nom   Nom de la compétence.     
   */ 
	function is_comp_perso($nom = '')
	{
		if(!isset($this->comp_perso)) $this->get_comp_perso();

		return array_key_exists($nom, $this->comp_perso);
	}
	/**
	 * Permet de savoir si le joueur possède la compétence nom
	 * @param $nom le nom de la compétence
	 * @return true si le perso al la compétence false sinon.
 	*/
	function is_competence($nom = '', $type = false)
	{
		if(!isset($this->competences)) $this->get_competence();
		$competence = false;

		if(is_array($this->competences))
		{
			$tmp = $this->competences;
			while(current($tmp) && !$competence)
			{
				if(!empty($nom))
				{
					if(strcmp(current($tmp)->get_competence(), $nom) == 0)
						$competence = true;

					next($tmp);
				}
				else
					$competence = (count($this->competences) > 0);
			}
		}
		else $competence = false;

		return $competence;
	}
  /**
   * Renvoie une compétence que seules certaines classes peuvent avoir (maitrises, sorts de groupe, …)
   * @param  $nom   Nom de la compétence.     
   */  
	function get_comp_perso($nom = '')
	{
		if(empty($nom))
		{
			$this->comp_perso = comp_perso::create(array('id_perso'), array($this->id), 'id ASC', 'competence');
			return $this->comp_perso;
		}
		else
		{
			if(!isset($this->comp_perso)) $this->get_comp_perso();
				return $this->comp_perso[$nom];
		}
	}
  /**
   * Accède aux compétences que seules certaines classes peuvent avoir (maitrises, sorts de groupe, …)
   * @param  $nom     Nom de la compétence ou false si on les veux toutes.
   * @param  $champ   Champ(de la bdd) dont ont veut connaitre la valeur.
	 * @param  $base    true s'il faut donner la valeur de base (sans les bonus).	
   */  
	function get_competence($nom = false, $champ = false, $base = false)
	{
		if(!$nom)
		{
			$this->competences = comp_perso::create(array('id_perso'), array($this->id), 'id ASC', 'competence');
			return $this->competences;
		}
		else
		{
			if ($base) $bonus = 0;
			else $bonus = $this->get_bonus_permanents($nom);
			if(!isset($this->competences)) $this->get_competence();
			if($champ)
			{
				$get = 'get_'.$champ;
				return $this->competences[$nom]->$get() + $bonus;
			}
			elseif(is_object($this->competences[$nom]))
				return $this->competences[$nom]->get_valeur() + $bonus;
			else return false;
		}
	}
  /**
   * Modifie une compétence que seules certaines classes peuvent avoir (maitrises, sorts de groupe, …)
   * @param  $nom       Nom de la compétence. 
	 * @param  $valeur    Nouvelle valeur.
   */  
	function set_competence($nom, $valeur = '')
	{
		if(array_key_exists($nom, $this->competences))
		{
			$this->competences[$nom]->set_valeur($valeur);
			$this->competences[$nom]->sauver();
		}
	}
  // @}
  
  /**
   * @name Inventaires et objets  
	 * Données et méthodes liées à l'inventaire et aux objets portés ou utiliser par
	 * les perosnnages.
	 */
  // @{
	private $inventaire;             ///< Objets équipés par le personnage (sous forme textuelle).
	private $inventaire_pet;         ///< Objets équipés par le pet du personnage (sous forme textuelle).
	private $inventaire_slot;        ///< Objets que le personnage à "dans son sac" (sous forme textuelle).
	public $inventaire_array;        ///< Objets équipés par le personnage (sous forme d'objet).
	public $inventaire_array_pet;    ///< Objets équipés par le pet du personnage (sous forme d'objet).
	public $inventaire_slot_array;   ///< Objets que le personnage à "dans son sac" (sous forme de tableau).
	public $arme;                    ///< Arme dans la main droite.
	public $arme_gauche;             ///< Arme dans la main gauche.
	public $bouclier;                ///< Bouclier.
	public $accessoire;              ///< Acessoire.
	public $enchantement = array();  ///< Liste des enchantements (gemmes incrusté dans l'équipement porté).
	public $pp_base;                 ///< PP de base (sans les buffs).
	public $pm_base;                 ///< PM de base (sans les buffs).
	public $pm_para;                 ///< PM pour la resistance à para (sans les buffs avec bonus raciaux).
	public $enchant;                 ///< plus utilisé.
	public $armure;                  ///< true si la PP et la PM on été calculées, false sinon.
	protected $encombrement;				 ///< encombrement total des objets portés dans l'inventaire
	/// Renvoie les objets équipés par le personnage sous forme textuelle.
	function get_inventaire()
	{
		return $this->inventaire;
	}
	/// Modifie les objets équipés par le personnage sous forme textuelle.
	function set_inventaire($inventaire)
	{
		$this->inventaire = $inventaire;
		$this->champs_modif[] = 'inventaire';
	}
	/// Renvoie les objets équipés par le pet du personnage sous forme textuelle.
	function get_inventaire_pet()
	{
		return $this->inventaire_pet;
	}
	/// Modifie les objets équipés par le pet dupersonnage sous forme textuelle.
	function set_inventaire_pet($inventaire)
	{
		$this->inventaire_pet = $inventaire;
		$this->champs_modif[] = 'inventaire_pet';
	}
	/// Renvoie les objets que le personnage à "dans son sac" sous forme textuelle.
	function get_inventaire_slot()
	{
		return $this->inventaire_slot;
	}
	/// Modifie les objets que le personnage à "dans son sac" sous forme textuelle.
	function set_inventaire_slot($inventaire_slot)
	{
		$this->inventaire_slot = $inventaire_slot;
		$this->champs_modif[] = 'inventaire_slot';
	}
	/// Renvoie l'encombrement total des objets portés dans l'inventaire.
	function get_encombrement()
	{
		return $this->encombrement;
	}
	/// Modifie l'encombrement total des objets portés dans l'inventaire.
	function set_encombrement($encombrement)
	{
		$this->encombrement = $encombrement;
		$this->champs_modif[] = 'encombrement';
	}
	/// Renvoie l'encombrement maximal
	function get_max_encombrement()
	{
		global $G_max_encombrement;
		return $G_max_encombrement + $this->get_bonus_permanents('encombrement');
	}
	/**
	 * Renvoie un objet équipé particulier.
	 * @param  $partie   Partie du corps dont il faut renvoyer l'objet.
	 */   	 
	function get_inventaire_partie($partie, $pet = false)
	{
		if(!$pet)
		{
			if(!isset($this->inventaire_array)) $this->inventaire_array = unserialize($this->get_inventaire());
			return $this->inventaire_array->$partie;
		}
		else
		{
			if(!isset($this->inventaire_array_pet)) $this->inventaire_array_pet = unserialize($this->get_inventaire_pet());
			return $this->inventaire_array_pet->$partie;

		}
	}
	/**
	 * Renvoie une partie de l'inventaire (utile, armes, armures ou autre) ou l'intégralité sous forme de tableau associatif.
	 * @param  $partie   partie (utile, armes, armures ou autre) à renvoyé, ou false
	 *                   s'il faut renvoyer toutes les parties dans un tableau associatif.
	 * @param  $force	   true s'il faut recharger le contenu à partie de la valeur textuelle.
	 */   	 
	function get_inventaire_slot_partie($partie = false, $force = false)
	{
		if(!isset($this->inventaire_slot_array) OR !$force) {
			$this->inventaire_slot_array = unserialize($this->get_inventaire_slot());
			$pack = false;
			for ($i = 0; $i < count($this->inventaire_slot_array); $i++) {
				$objet_d = decompose_objet($this->inventaire_slot_array[$i]);
				if (!isset($objet_d['id_objet']) || $objet_d['id_objet'] == '') {
					/*error_log("Bug inventaire: [$this->nom][$i]: pas d'id_objet (".
										$this->inventaire_slot_array[$i].")\n",3, '/tmp/bugs.log');*/
					unset($this->inventaire_slot_array[$i]);
					$pack = true;
				}
			}
			if ($pack && is_array($this->inventaire_slot_array)) {
				// On re-indexe le tableau, et on sauve
				$tmp = array_chunk($this->inventaire_slot_array,
													 count($this->inventaire_slot_array));
				$this->inventaire_slot_array = $tmp[0];
				$this->set_inventaire_slot(serialize($this->inventaire_slot_array));
				$this->sauver();
			}
		}
		if($partie === false) return $this->inventaire_slot_array;
		else return $this->inventaire_slot_array[$partie];
	}
	/**
	 * Modifie une partie de l'inventaire	(utile, armes, armures ou autre).
	 * @param  $objet    contenu de la partie de l'inventaire à modifier.	 
	 * @param  $partie   partie (utile, armes, armures ou autre) à renvoyé.
	 */	 
	function set_inventaire_slot_partie($objet, $partie)
	{
		$this->inventaire_slot_array[$partie] = $objet;
	}
	/// Renvoie l'inventaire sous forme d'objet
	function inventaire()
	{
		return unserialize($this->inventaire);
	}
	/// Renvoie l'inventaire sous forme d'objet
	function inventaire_pet()
	{
		return unserialize($this->inventaire_pet);
	}
	/// Renvoie l'arme de la main droite. Enregistre les enchantements et les effets. 	
	function get_arme()
	{
		if(!isset($this->arme))
		{
			global $db;
			$arme = $this->inventaire()->main_droite;
			if($arme != '')
			{
				/// @todo à refaire
				$arme_d = decompose_objet($arme);
				$requete = "SELECT * FROM arme WHERE id = ".$arme_d['id_objet'];
				$req = $db->query($requete);
				$this->arme = $db->read_object($req);
				if ($arme_d['enchantement'] != null)
				{
					$gemme = new gemme_enchassee($arme_d['enchantement']);
					if ($gemme->enchantement_type == 'degat')
						$this->arme->degat += $gemme->enchantement_effet;
					$this->register_gemme_enchantement($gemme);
				}
				if( $arme_d['mod'] )
				{
					$mod = new forge_recette($arme_d['mod']);
					$this->arme->degat += $mod->get_modif_degats();
				}
        if ($this->arme->effet)
        {
          $effets = explode(';', $this->arme->effet);
          foreach ($effets as $effet)
          {
            $d_effet = explode('-', $effet);
						$this->register_item_effet($d_effet[0], $d_effet[1], $this->arme);
          }
        }
			}
			else $this->arme = false;
		}
		return $this->arme;
	}
	/// Renvoie l'arme de la main gauche. Enregistre les enchantements et les effets. 
	function get_arme_gauche()
	{
		if(!isset($this->arme_gauche))
		{
			global $db;
			$arme = $this->inventaire()->main_gauche;
			if($arme != '' && $arme != 'lock')
			{
				$arme_d = decompose_objet($arme);
				$requete = "SELECT * FROM arme WHERE id = ".$arme_d['id_objet'];
				$req = $db->query($requete);
				$this->arme_gauche = $db->read_object($req);
				if($this->arme_gauche->type == 'bouclier')
					$this->arme_gauche = false;
				else
				{
					if ($arme_d['enchantement'] != null)
					{
						$gemme = new gemme_enchassee($arme_d['enchantement']);
						if ($gemme->enchantement_type == 'degat')
							$this->arme_gauche->degat += $gemme->enchantement_effet;
						$this->register_gemme_enchantement($gemme);
					}
					if( $arme_d['mod'] )
					{
						$mod = new forge_recette($arme_d['mod']);
						$this->arme_gauche->degat += $mod->get_modif_degats();
					}
				}
			}
			else $this->arme_gauche = false;
		}
		return $this->arme_gauche;
	}
	/// Renvoie l'arme du pet. Enregistre les enchantements et les effets. 	
	function get_arme_pet()
	{
		if(!isset($this->arme_pet))
		{
			global $db;
			$arme = $this->inventaire_pet()->arme_pet;
			if($arme != '')
			{
				$arme_d = decompose_objet($arme);
				$requete = "SELECT * FROM objet_pet WHERE id = ".$arme_d['id_objet'];
				$req = $db->query($requete);
				$this->arme_pet = $db->read_object($req);
				if ($arme_d['enchantement'] != null)
				{
					$gemme = new gemme_enchassee($arme_d['enchantement']);
					if ($gemme->enchantement_type == 'degat')
						$this->arme->degat += $gemme->enchantement_effet;
					$this->register_gemme_enchantement($gemme);
				}
				if ($this->arme_pet->effet)
				{
				  $effets = explode(';', $this->arme->effet);
				  foreach ($effets as $effet)
				  {
					$d_effet = explode('-', $effet);
								$this->register_item_effet($d_effet[0], $d_effet[1], $this->arme);
				  }
				}
			}
			else $this->arme_pet = false;
		}
		return $this->arme_pet;
	}
	/// Renvoie l'arme de le bouclier. Enregistre les enchantements et les effets. 
	function get_bouclier()
	{
		if(!isset($this->bouclier))
		{
			global $db;
			$bouclier = $this->inventaire()->main_gauche;
			if($bouclier != '' AND $bouclier != 'lock')
			{
				$arme_g = decompose_objet($bouclier);
				$requete = "SELECT * FROM arme WHERE id = ".$arme_g['id_objet'];
				$req = $db->query($requete);
				$this->bouclier = $db->read_object($req);
				if($this->bouclier->type != 'bouclier') $this->bouclier = false;
				else if ($arme_g['enchantement'] != null)
				{
					$gemme = new gemme_enchassee($arme_g['enchantement']);
					if ($gemme->enchantement_type == 'bouclier')
						$this->bouclier->degat += $gemme->enchantement_effet;
					$this->register_gemme_enchantement($gemme);
				}
        if ($this->bouclier->effet)
        {
          $effets = explode(';', $this->bouclier->effet);
          foreach ($effets as $effet)
          {
            $d_effet = explode('-', $effet);
						$this->register_item_effet($d_effet[0], $d_effet[1], $this->bouclier);
          }
        }
			}
			else $this->bouclier = false;
		}
		return $this->bouclier;
	}
	/// Renvoie l'accessoire. Enregistre les enchantements et les effets.
	function get_accessoires()
	{
		if(!isset($this->accessoire) || !$this->accessoire)
		{
			global $db;
			// grand accessoire 
			$grand_accessoire = $this->inventaire()->grand_accessoire;
			if( !$grand_accessoire )
				$grand_accessoire = $this->inventaire()->accessoire;
			$this->charger_accessoire( $grand_accessoire );
			// Moyen accessoire
			$this->charger_accessoire( $this->inventaire()->moyen_accessoire );
			// Petits accessoires
			$this->charger_accessoire( $this->inventaire()->petit_accessoire_1 );
			$this->charger_accessoire( $this->inventaire()->petit_accessoire_2 );
			$this->accessoire = true;
		}
	}
	protected function charger_accessoire($accessoire)
	{
		if($accessoire != '' AND $accessoire != 'lock')
		{
			$objet = objet_invent::factory($accessoire);
			if( $objet->get_enchantement() )
			{
				$gemme = new gemme_enchassee($objet->get_enchantement());
				$this->register_gemme_enchantement($gemme);
				//my_dump($this->enchantement);
			}
			$objet->agit($this);
		}
	}
	// "Charge" les objets pour faire agir leur effets
  function check_materiel()
  {
    $this->get_arme();
    $this->get_arme_gauche();
    $this->get_bouclier();
    $this->get_armure();
    $this->get_accessoires();
  }
  /// Renvoie le type de l'arme utilisé 
	function get_arme_type()
	{
		if(!isset($this->arme)) $this->get_arme();
		return $this->arme->type;
	}
	/**
	 * Renvoie le facteur de dégâts de ou des armes.	
   * La plupart du temps on s'en fiche, de la main, on veut les degats
   * @param $main   si false : cumul, si 'droite' ou 'gauche' : detail
   */
	function get_arme_degat($main = false, $adversaire=null)
	{
		$degats = 0;
		if ($main == false || $main == 'droite')
			if ($this->get_arme())
				$degats += $this->arme->degat;
		if ($main == false || $main == 'gauche')
			if ($this->get_arme_gauche())
				$degats += $this->arme_gauche->degat;
		if ($main == 'pet')
			if ($this->get_arme_pet())
				$degats += $this->arme_pet->degat;
		return $degats;
	}
	/**
	 * Recherche un objet dans l'inventaire
	 * @param  $id_objet   Id de l'objet.
	 * @return   si trouvé tableau avec en première position de le nombre d'objets
	 *           dans la pile et en deuxième la position dans l'inventaire, false
	 *           si non trouvé.   	 
	 */	
	function recherche_objet($id_objet)
	{
		global $G_place_inventaire;
		$objet_d = decompose_objet($id_objet);
		$trouver =  false;
		//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
		$i = 0;
		$partie = $this->get_inventaire_slot_partie();
		//while(($i < $G_place_inventaire) AND !$trouver)
		foreach($partie as $o)
		{
			$objet_i = decompose_objet($o);
			if($objet_i['sans_stack'] == $objet_d['sans_stack'] /*&& $objet_i['stack']>= $objet_d['stack']*/)
			{
				$trouver = true;
				break;
			}
			$i++;
		}
		if($trouver)
		{
			if($objet_i['stack'] > 1) $return[0] = $objet_i['stack'];
			else $return[0] = 1;
			$return[1] = $i;
			return $return;
		}
		else return false;
	}
	/// Liste les différentes versions d'un objet
	function liste_objet($id)
	{
		$partie = $this->get_inventaire_slot_partie();
		$res = array();
		foreach($partie as $o)
		{
			if( objet_invent::get_princ($o) == $id )
			{
				$d = objet_invent::decomp_nombre($o);
				if( array_key_exists($d[0], $res) )
					$res[$d[0]] += $d[1];
				else
					$res[$d[0]] = $d[1];				
			}
		}
		return $res;
	}
  /// Refait les piles des objets
	function restack_objet()
	{
		$partie = $this->get_inventaire_slot_partie();
		$compte_stack = array();
		$poursuite = true;
		foreach($partie as $part)
		{
			$objet = decompose_objet($part);
			if(array_key_exists($objet['sans_stack'], $compte_stack))
				$compte_stack[$objet['sans_stack']] += $objet['stack'] ? $objet['stack'] : 1;
			else
				$compte_stack[$objet['sans_stack']] = $objet['stack'] ? $objet['stack'] : 1;
			//$this->supprime_objet($objet['sans_stack'], $objet['stack']);
		}

		$inventaire = array();
		$encombrement = 0;
		foreach($compte_stack as $objet => $valeur)
		{
			$obj = objet_invent::factory($objet);
			while($valeur > 0)
			{
				$n = min($valeur, max($obj->get_stack(), 1));
				$valeur -= $n;
				$obj->set_nombre($n);
				$obj->recompose_texte();
				$inventaire[] = $obj->get_texte();
				$encombrement += $obj->get_encombrement();
			}
		}
		$this->set_inventaire_slot( serialize($inventaire) );
		$this->set_encombrement($encombrement);
		$this->sauver();
	}
  /**
   * Supprime un objet de l'inventaire
   * @param  $id_objet    Id de l'objet.
   * @param  $nombre      Nombre d'objet à supprimer, s'il peut être empiler.
   */  
	function supprime_objet($id_objet, $nombre=1)
	{
		global $db;
		$i = $nombre;
    $inventaire = $this->get_inventaire_slot_partie();
		while($i > 0)
		{
			$objet = $this->recherche_objet($id_objet);
			$obj = objet_invent::factory($inventaire[$objet[1]]);
			if( !$obj )
			{
				log_admin::log('erreur', 'Objet à supprimer non trouvé : '.$id_objet.' -> '.$objet[1].' : '.$inventaire[$objet[1]]);
				return false;
			}
			//Vérification si objet "stacké"
			/*$stack = explode('x', $inventaire[$objet[1]]);
			if($stack[1] > 1)
				$inventaire[$objet[1]] = $stack[0].'x'.($stack[1] - 1);
			else
			{
				array_splice($inventaire, $objet[1], 1);
			}*/
			$nbr = $obj->get_nombre();
			if( $nbr > 1 )
			{
				$obj->set_nombre($nbr - 1);
				$obj->recompose_texte();
				$inventaire[$objet[1]] = $obj->get_texte();
			}
			else
			{
				array_splice($inventaire, $objet[1], 1);
				$this->set_encombrement( $this->encombrement - $obj->get_encombrement() );
			}
			$i--;
		}
		$this->set_inventaire_slot(serialize($inventaire));
		unset($this->inventaire_perso);// Nécessaire pour que la suppression des objets lors des échanges marche 
		$this->sauver();
		return true;
	}
  /**
   * Ajoute un objet dans l'inventaire
   * @param  $id_objet    Id de l'objet.
   * @return    true si l'objet à pu être prit, false sinon  
   */  
	function prend_objet($id_objet)
	{
		if(!isset($this->inventaire_perso)) $this->inventaire_perso = new inventaire($this->inventaire, $this->inventaire_slot);
		if($this->inventaire_perso->prend_objet($id_objet, $this))
		{
			if(is_array($this->inventaire_perso->slot_liste)) $this->set_inventaire_slot(serialize($this->inventaire_perso->slot_liste));
			else $this->set_inventaire_slot($this->inventaire_perso->slot_liste);
			$this->sauver();
			return true;
		}
		else return false;
	}
	/**
   * Ajoute un objet dans l'inventaire
   * @param  $id_objet    Id de l'objet.
   * @return    true si l'objet à pu être prit, false sinon  
   */  
	function prend_objet_pet($id_objet)
	{
		if(!isset($this->inventaire_perso_pet)) $this->inventaire_perso_pet = new inventaire($this->inventaire_pet, $this->inventaire_slot);
		if($this->inventaire_perso_pet->prend_objet($id_objet, $this))
		{
			if(is_array($this->inventaire_perso_pet->slot_liste)) $this->set_inventaire_slot(serialize($this->inventaire_perso_pet->slot_liste));
			else $this->set_inventaire_slot($this->inventaire_perso_pet->slot_liste);
			$this->sauver();
			return true;
		}
		else return false;
	}
  /**
   * Déséquipe un objet qui était porté.
   * @param  $type    Type (emplacement) de l'objet.
   * @return    true s'il a pu être déséquipé, false sinon (plus de place).   
   */   
	function desequip($type, $pet = false)
	{
		global $db, $G_erreur, $G_place_inventaire;
		$erreur = false;
		
		if($pet) $inventaire = $this->inventaire_pet();
		else $inventaire = $this->inventaire();
		
		// temporaire : transition entre anciens et nouveaux accessoires
		if( $type == 'grand_accessoire' && !$inventaire->grand_accessoire )
			$type = 'accessoire';
		if($inventaire->$type !== 0 AND $inventaire->$type != '')
		{
			if(!$pet)
			{
				//Inventaire plein
				if($this->prend_objet($inventaire->$type))
				{
					//On enlève l'objet de l'emplacement pour le mettre dans l'inventaire
					if($type == 'main_droite')
					{
						if($inventaire->main_gauche == 'lock') $inventaire->main_gauche = 0;
					}
					$inventaire->$type = 0;
					$this->set_inventaire(serialize($inventaire));
					$this->sauver();
					return true;
				}
				else $erreur = true;
			}
			else
			{
				//Inventaire plein
				if($this->prend_objet_pet($inventaire->$type))
				{
					//On enlève l'objet de l'emplacement pour le mettre dans l'inventaire
					$inventaire->$type = 0;
					$this->set_inventaire_pet(serialize($inventaire));
					$this->sauver();
					return true;
				}
				else $erreur = true;
			}
			
			if($erreur)
			{
				$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
				return false;
			}
		}
		return true;
	}
  /**
   * Equipe un objet.
   * Vérifie toutes les conditions pour le porter et déséquipe des obkets si néecessaire.
   * @param  $objet   Objet à équiper
   * @return       true s'il a pu être équipé, false sinon. 
   */  
	function equip_objet($objet, $pet = false, $zone=null)
	{
		global $db, $G_erreur;
		$equip = false;
		$conditions = array();
    // @todo vérifier que l'objet est bien possédé et intégré sa supression du sac
		if($objet_d = decompose_objet($objet))
		{
			//print_r($objet_d);
			$id_objet = $objet_d['id_objet'];
			$categorie = $objet_d['categorie'];
			switch ($categorie)
			{
				//Si c'est une arme
				case 'a' :
					$requete = "SELECT * FROM arme WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					if($row['type'] == 'baton')
					{
						$conditions[0]['attribut']	= 'coef_incantation';
						$conditions[0]['valeur'] = $row['forcex'] * $row['melee'];
					}
					elseif($row['type'] == 'bouclier')
					{
						$conditions[0]['attribut']	= 'coef_blocage';
						$conditions[0]['valeur'] = $row['forcex'] * $row['melee'];
					}
					else
					{
						$conditions[0]['attribut'] = 'coef_melee';
						$conditions[0]['valeur'] = $row['forcex'] * $row['melee'];
					}
					$conditions[1]['attribut']	= 'coef_distance';
					$conditions[1]['valeur']	= $row['forcex'] * $row['distance'];
					$conditions[2]['attribut']	= 'force';
					$conditions[2]['valeur']	= $row['forcex'];
					$type = explode(';', $row['mains']);
					$type = $type[0];
					$mains = $row['mains'];
				break;
				//Si c'est une protection
				case 'p' :
					$requete = "SELECT * FROM armure WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$conditions[0]['attribut']	= 'force';
					$conditions[0]['valeur']	= $row['forcex'];
					$conditions[1]['attribut']	= 'puissance';
					$conditions[1]['valeur']	= $row['puissance'];
					$type = $row['type'];
				break;
				case 'd' :
					$requete = "SELECT * FROM objet_pet WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$conditions[0]['attribut']	= 'dressage';
					$conditions[0]['valeur']	= $row['dressage'];
					$type = $row['type'];
				break;
				//Si c'est un accessoire
				case 'm' :
					$requete = "SELECT * FROM accessoire WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$conditions[0]['attribut']	= 'puissance';
					$conditions[0]['valeur']	= $row['puissance'];
					$type = $zone ? $zone : $row['taille'].'_accessoire';
				break;
			}

			//Vérification des conditions
			if (is_array($conditions))
			{
				$i = 0;
				while ($i < count($conditions))
				{
					$get = 'get_'.$conditions[$i]['attribut'];
					if ($this->$get() < $conditions[$i]['valeur'])
					{
						$G_erreur = 'Vous n\'avez pas assez en '.$conditions[$i]['attribut'].'<br />';
						return false;
					}
					$i++;
				}
			}
			
			//Si c'est une dague main gauche, vérifie qu'il a aussi une dague en main droite
			if($type == 'main_gauche' AND $row['type'] == 'dague')
			{
				if($this->get_inventaire_partie('main_droite') === 0)
				{
				}
				else
				{
					$main_droite = decompose_objet($this->get_inventaire_partie('main_droite'));
					$requete = "SELECT * FROM arme WHERE ID = ".$main_droite['id_objet'];
					//Récupération des infos de l'objet
					$req_md = $db->query($requete);
					$row_md = $db->read_array($req_md);
					if($row['type'] == 'dague')
					{
						if($row_md['type'] != 'dague')
						{
							$G_erreur = 'L\'arme équipée en main droite n\'est pas une dague<br />';
							return false;
						}
					}
					elseif(count(explode(';', $row_md['mains'])) > 1)
					{
						$G_erreur = 'Vous devez enlever votre arme à 2 mains pour porter cet objet<br />';
						return false;
					}
				}
			}
			//Vérifie si il a une dague en main gauche et si c'est le cas et que l'arme n'est pas une dague, on désequipe
			if($type == 'main_droite' AND $row['type'] != 'dague' AND !$pet)
			{
				if($this->get_inventaire_partie('main_gauche') === 0 OR $this->get_inventaire_partie('main_gauche') == '')
				{
				}
				else
				{
					if($main_gauche = decompose_objet($this->get_inventaire_partie('main_gauche')))
					{
						$requete = "SELECT * FROM arme WHERE ID = ".$main_gauche['id_objet'];
						//Récupération des infos de l'objet
						$req_mg = $db->query($requete);
						$row_mg = $db->read_array($req_mg);
						if($row_mg['type'] == 'dague')
						{
							$this->desequip('main_gauche');
						}
					}
					else
					{
					}
				}
			}

			$desequip = true;
			if($categorie == 'a')
			{
				$mains = explode(';', $mains);
				$type = $mains[0];
				$count = count($mains);
			}
			
			//Verifie si il a déjà un objet de ce type sur le personnage
			if ($type != '' AND !$pet)
			{
				//Desequipement
				if($categorie == 'a')
				{
					$i = 0;
					while($desequip AND $i < $count)
					{
						if($this->get_inventaire_partie($mains[$i]) === 'lock' AND $this->get_inventaire_partie('main_droite') !== 0)
						{
							$this->desequip('main_droite');
						}
						$desequip = $this->desequip($mains[$i]);
						$i++;
					}
				}
				else
				{
					$desequip = $this->desequip($type);
				}
			}
			
			//Verifie si il a déjà un objet de ce type sur le pet
			if ($type != '' AND $pet)
				$desequip = $this->desequip($type, true);

			if($desequip)
			{
				//On équipe
				if($pet) $inventaire = $this->inventaire_pet();
				else $inventaire = $this->inventaire();
				$inventaire->$type = $objet;
				if($categorie == 'a' AND $count == 2) $inventaire->main_gauche = 'lock';
				if($pet) $this->set_inventaire_pet(serialize($inventaire));
				else $this->set_inventaire(serialize($inventaire));
				$this->sauver();
				return true;
			}
			else
			{
				return false;
			}
		}
		else return false;
	}
	/**
	 * Enregistre un enchantement.
	 * Les enchantements possible sont : hp, mp, pp, pm, portée, star, reserve,
	 * esquive, melee, distance (compétence) et incantation.
	 */	
	function register_gemme_enchantement($gemme)
	{
		switch ($gemme->enchantement_type)
		{
		case 'hp' :
			$this->add_bonus_permanents('hp_max', $gemme->enchantement_effet);
      break;
    case 'mp' :
			$this->add_bonus_permanents('mp_max', $gemme->enchantement_effet);
      break;
    case 'pp' :
      $this->pp += $gemme->enchantement_effet;
      break;
    case 'pm' :
      $this->pm += $gemme->enchantement_effet;
      break;
		case 'portee' :
		case 'star' :
   	case 'reserve' :
		/* gemmes de compétence: bonus ignoré à la montée */
		case 'esquive' :
		case 'melee' : 
		case 'distance' :
		case 'incantation' :
		case 'max_dresse':
		case 'max_pet':
			$this->add_bonus_permanents($gemme->enchantement_type,
																	$gemme->enchantement_effet);
			break;
		}
		if (array_key_exists($gemme->enchantement_type, $this->enchantement))
		{
			$this->enchantement[$gemme->enchantement_type]['gemme_id']
				.= ";$gemme->id";
			$this->enchantement[$gemme->enchantement_type]['effet'] +=
				$gemme->enchantement_effet;
		}
		else
			$this->enchantement[$gemme->enchantement_type] =
				array('gemme_id' => $gemme->id, 'effet' => $gemme->enchantement_effet);
	}

  /**
   * Renvoie au choix la liste des enchantements, un enchantement particulier ou un paramètre d'un enchantement particulier.
   * @param  $nom   false pour renvoyer la liste des enchantements, ou nom de l'enchantement.
   * @param  $key   false pour renvoyer l'enchantement en entier, ou nom du paramètre à renvoyer de l'enchantement.
   */
	function get_enchantement($nom = false, $key = false)
	{
		if ($nom === false)
			return $this->enchantement;
		else if (array_key_exists($nom, $this->enchantement))
		{
			if ($key === false)
				return $this->enchantement[$nom];
			else
				return $this->enchantement[$nom][$key];
		}
		else
			return false;
	}

  /**
   * Indique s'il y a des enchantement ou si un enchantement en particulier est présent.
   * @param  $nom   false pour renvoyer la présence d'enchantement en général, ou nom de l'enchantement.
   */
	function is_enchantement($nom = false)
	{
		if ($nom === false)
			return (count($this->enchantement) != 0);
		else
			return array_key_exists($nom, $this->enchantement);
	}
	
	/// Calcule la PP et la PM en fonction des pièces d'armures portées ainsi que des buffs et autres modificateurs.
	function get_armure()
	{
		global $db;
		if(!isset($this->armure))
		{
			$this->pp = 0;
			$this->pm = 1;
			// Pièces d'armure
			$partie_armure = array('tete', 'torse', 'main', 'ceinture', 'jambe', 'chaussure', 'dos', 'cou', 'doigt');
			foreach($partie_armure as $partie)
			{
				if($partie != '')
				{
					$partie_d = decompose_objet($this->get_inventaire_partie($partie));
					if($partie_d['id_objet'] != '')
					{
						$requete = "SELECT PP, PM, effet FROM armure WHERE id = ".$partie_d['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_row($req);
						$this->pp += $row[0];
						$this->pm += $row[1];
						if( $partie_d['mod'] )
						{
							$mod = new forge_recette($partie_d['mod']);
							$this->pp += $mod->get_modif_pp();
							$this->pm += $mod->get_modif_pm();
						}
						// Effets magiques
						if ($row[2] != '')
						{
							$effet = explode(';', $row[2]);
							foreach($effet as $eff)
							{
								$explode = explode('-', $eff);
								$this->register_item_effet($explode[0], $explode[1]);
							}
						}
					}
					// Gemmes
					if($partie_d['enchantement'] > 0)
					{
						$gemme = new gemme_enchassee($partie_d['enchantement']);
						$this->register_gemme_enchantement($gemme);
					//$this->enchant = enchant($partie_d['enchantement'], $this);
					}
				}
			}
			$this->pp_base = $this->pp;
			$this->pm_base = $this->pm;
			//Bonus raciaux
			if($this->get_race() == 'nain') $this->pm = round(($this->pm + 10) * 1.1);
			if($this->get_race() == 'barbare') $this->pp = round(($this->pp + 10) * 1.3);
			if($this->get_race() == 'scavenger')
			{
				$this->pp = round($this->pp * 1.15);
				$this->pm = round($this->pm * 1.05);
			}
			if($this->get_race() == 'mortvivant' AND moment_jour($this->id) == 'Soir')
			{
				$this->pp = round($this->pp * 1.15);
				$this->pm = round($this->pm * 1.15);
			}
			
			//Effets des enchantements
			if (isset($this->enchantement['pourcent_pm'])) $this->pm += floor($this->pm * $this->enchantement['pourcent_pm']['effet'] / 100);
			if (isset($this->enchantement['pourcent_pp']))	$this->pp += floor($this->pp * $this->enchantement['pourcent_pp']['effet'] / 100);
			
			// Malus permanents
    	$this->pp = round($this->pp / ( 1 + $this->get_bonus_permanents('div_pp')/100) );
    	$this->pm = round($this->pm / ( 1 + $this->get_bonus_permanents('div_pm')/100) );

			//pm pour le 3eme jet de para
			$this->pm_para = $this->pm;
			
			//Maladie suppr_defense
			if($this->is_buff('suppr_defense')) $this->pp = 0;
		}
		$this->armure=true;
	}

  /**
   * Renvoie la PM.
   * Appelle get_armure si elle n'a pas déjà été calculée.
   * @param  $base    true pour avoir la PM de base (sans modificateurs), false pour avoir la totale.
   */
	function get_pm($base = false)
	{
		if(!isset($this->pm))
		{
			$this->get_armure();
		}
		/*if(!$base) return $this->pm;
		else return $this->pm_base;*/
		return entite::get_pm($base);
	}

	function get_pm_para()
	{
		if(!isset($this->pm_para))
		{
			$this->get_armure();
		}
		return $this->pm_para;
	}
	
  /**
   * Renvoie la PP.
   * Appelle get_armure si elle n'a pas déjà été calculée.
   * @param  $base    true pour avoir la PP de base (sans modificateurs), false pour avoir la totale.
   */
	function get_pp($base = false)
	{
		if(!isset($this->pp))
		{
			$this->get_armure();
		}
		/*if(!$base) return $this->pp;
		else return $this->pp_base;*/
		return entite::get_pp($base);
	}
	
	/**
	 * Enregistre un effet (effets magiques des objets de donjons)
	 * @param  $id     id de l'effet
	 * @param  $effet  paramètres de l'effet
	 * @param  $item   objet concerné
	 */
	function register_item_effet($id, $effet, $item = null)
	{
		switch ($id)
			{ // @todo les autres, c'est quoi donc ?
			case 1:
				$this->add_bonus_permanents('regen_hp', $effet);
				break;
			case 5:
				$this->add_bonus_permanents('volonte', $effet);
				break;
			case 6:
				$this->add_bonus_permanents('resistance_para', $effet);
				break;
			case 7:
				$this->add_bonus_permanents('dexterite', $effet);
				break;
			case 9:
				$ep = new effet_vampirisme($effet, $item->nom);
				/*if ($item->type == 'hache' || $item->type == 'dague' ||
						($item->type == 'epee' && preg_match('/^lame/i', $item->nom))) {
					$ep->pos = 'sa';
				}*/
				$this->add_effet_permanent('attaquant', $ep);
				break;
			case 10:
				$this->add_effet_permanent('defenseur', new protection_artistique($effet, $item->nom));
				break;
			case 11:
        $this->add_bonus_permanents('potentiel_magique', $effet);
				break;
			case 13:
				$this->camouflage = $effet;
				break;
			case 14 :
				$this->add_effet_permanent('attaquant', new bonus_pinceau($effet, $item->nom));
				break;
			case 15 :
				$this->add_effet_permanent('attaquant', new bonus_pinceau_degats($effet, $item->nom));
				break;
			case 16:
				$this->add_bonus_permanents('regen_mp_add', $effet);
				break;
			case 23 :
				$this->add_effet_permanent('defenseur', new carapace_incisive($effet, $item->nom));
				break;
			case 24 : 
				$this->add_effet_permanent('attaquant', new boutte_flamme($effet, $item->nom));
				break;
			case 25 :
				$this->add_effet_permanent('defenseur', new anneau_resistance($effet, $item->nom));
				break;
			case 26 :
				$this->add_bonus_permanents('dexterite', -1);
				$this->add_effet_permanent('attaquant', new cape_troll($effet, $item->nom));
				break;
			case 27 :
				$this->add_effet_permanent('defenseur', new bouclier_pensee($effet, $item->nom));
				break;
			case 28 :
				$this->add_effet_permanent('attaquant', new arc_tung($effet, $item->nom));
				break;
			case 29 :
				$this->add_bonus_permanents('esquive', $effet*$this->get_esquive()/100);
				break;
			default:
				break;
			}
	}
  // @}


  /**
   * @name  Effets permanents
   * Effets modifiant les caractéristiques et les compétences. Ces effets peuvent
   * être dus aux bonus raciaux, aux objets portés…
   */
  // @{
	private $bonus_permanents = array();           ///< liste des effets permanents.
	private $effet_permanents_attaquant = array(); ///< .
	private $effet_permanents_defenseur = array(); ///< .
	/// renvoie un bonus permanent particulier
	function get_bonus_permanents($bonus)
	{
		if (array_key_exists($bonus, $this->bonus_permanents))
			return $this->bonus_permanents[$bonus];
		return 0;
	}
  /// Ajoute un bonus permanant
	function add_bonus_permanents($bonus, $value)
	{
		if (array_key_exists($bonus, $this->bonus_permanents))
			$this->bonus_permanents[$bonus] += $value;
		else
			$this->bonus_permanents[$bonus] = $value;
	}
  /**
   * Ajoute un effet permanent
   * @param  $mode    type de l'effet
   * @param  $effet   paramètres de l'effet
   */
	function add_effet_permanent($mode, $effet)
	{
		$effets_mode = "effet_permanents_$mode";
		array_push($this->$effets_mode, $effet);
	}
  /**
   * Renvoie les effets présents d'un certains type
   * @param  &$effets   tableau auquel sera ajouté les effets trouvés
   * @param  $mode      type des effets
   */
	function get_effets_permanents(&$effets, $mode)
	{
		$effets_mode = "effet_permanents_$mode";
		foreach ($this->$effets_mode as $ep) {
			array_push($effets, $ep);
		}
	}
	/**
   * Cette fonction permet d'appliquer a la construction les bonus
   * permanents tels les carac des vampires ou d'items
   */
	function applique_bonus()
	{
		// Bonus raciaux
		switch ($this->race)
		{
		case 'vampire':
			$this->add_bonus_permanents('reserve', 2);
			if (moment_jour($this->id) == 'Nuit')
			{
				$this->add_bonus_permanents('reserve', 3);
				$this->add_bonus_permanents('dexterite', 2);
				$this->add_bonus_permanents('volonte', 2);
			}
			elseif (moment_jour($this->id) == 'Journee')
			{
				$this->add_bonus_permanents('reserve', -1);
				$this->add_bonus_permanents('dexterite', -1);
				$this->add_bonus_permanents('volonte', -1);
			}
			break;
		case 'elfehaut':
			if (moment_jour($this->id) == 'Nuit')
			{
				$this->add_bonus_permanents('reserve', 2);
				$this->add_bonus_permanents('dexterite', 1);
				$this->add_bonus_permanents('volonte', 1);
			}
			break;
		}
	}
  // @}
  
  /**
   * @name  Coefficients et facteurs de magie
   * Données et méthodes ayant trait aux coefficients pour porter les armes et 
   * aux facteurs multipliant les pré-requis et coût en PA et MP des sorts.
   */         
  // @{
	public $coef_melee;            ///< Coefficient de mêlée.
	public $coef_distance;         ///< Coefficient de tir à distance.
	public $coef_blocage;          ///< Coefficient de blocage.
	public $coef_incantation;      ///< Coefficient d'incantation.
	private $facteur_magie;        ///< Facteur multipliant les pré-requis et coût en PA et MP des sorts.
	private $facteur_sort_vie;     ///< Facteur pour la magie de la vie (inutilisé).
	private $facteur_sort_mort;    ///< Facteur pour la nécromancie (inutilisé).
	private $facteur_sort_element; ///< Facteur pour la magie élémentaire (inutilisé).
  /// Renvoie le coefficient de mêlée.
	function get_coef_melee()
	{
		if(!isset($this->coef_melee))
			$this->coef_melee = $this->forcex * $this->get_melee(true);
		return $this->coef_melee;
	}
  /// Renvoie le coefficient de tir à distance.
	function get_coef_distance()
	{
		if(!isset($this->coef_distance))
			$this->coef_distance = round(($this->get_forcex(true) + $this->get_dexterite(true)) / 2) * $this->get_distance(true);
		return $this->coef_distance;
	}
  /// Renvoie le coefficient de blocage.
	function get_coef_blocage()
	{
		if(!isset($this->coef_blocage)) $this->coef_blocage = round(($this->forcex + $this->dexterite) / 2) * $this->blocage;
		return $this->coef_blocage;
	}
  /// Renvoie le coefficient d'incantation.
	function get_coef_incantation()
	{
		if(!isset($this->coef_incantation))
			$this->coef_incantation = $this->get_puissance(true) * $this->get_incantation(true);
		return $this->coef_incantation;
	}
	/// Renvoie le facteur multipliant les pré-requis et coût en PA et MP des sorts.
	function get_facteur_magie()
	{
		return $this->facteur_magie;
	}
	/// Modifie le facteur multipliant les pré-requis et coût en PA et MP des sorts.
	function set_facteur_magie($facteur_magie)
	{
		$this->facteur_magie = $facteur_magie;
		$this->champs_modif[] = 'facteur_magie';
	}
	/// Renvoie le facteur pour la magie de la vie (inutilisé).
	function get_facteur_sort_vie()
	{
		return $this->facteur_sort_vie;
	}
	/// Modifie le facteur pour la magie de la vie (inutilisé).
	function set_facteur_sort_vie($facteur_sort_vie)
	{
		$this->facteur_sort_vie = $facteur_sort_vie;
		$this->champs_modif[] = 'facteur_sort_vie';
	}
	/// Renvoie le facteur pour la nécromancie (inutilisé).
	function get_facteur_sort_mort()
	{
		return $this->facteur_sort_mort;
	}
	/// Modifie le facteur pour la nécromancie (inutilisé).
	function set_facteur_sort_mort($facteur_sort_mort)
	{
		$this->facteur_sort_mort = $facteur_sort_mort;
		$this->champs_modif[] = 'facteur_sort_mort';
	}
	/// Renvoie le facteur pour la magie élémentaire (inutilisé).
	function get_facteur_sort_element()
	{
		return $this->facteur_sort_element;
	}
	/// Modifie le facteur pour la magie élémentaire (inutilisé).
	function set_facteur_sort_element($facteur_sort_element)
	{
		$this->facteur_sort_element = $facteur_sort_element;
		$this->champs_modif[] = 'facteur_sort_element';
	}
  // @}
  
  /**
   * @name  PA, HP & MP
   * Données et méthodes ayant trait aux PA, HP & MP : valeur actuelle, maximale,
   * prochaine régénération et augmentation.
   */         
  // @{
	public $hp_maximum;      ///< HP maximums après bonus et malus
	private $mp;             ///< MP actuels.
	private $mp_max;         ///< MP maximaux
	public $mp_maximum;      ///< HP maximums après bonus et malus
	private $dernieraction;  ///< Date de la dernière action pour le calcul des PA.
	private $regen_hp;       ///< Date de la prochaine régénération de HP & MP
	private $maj_hp;         ///< Date de la prochaine augmentation de HP
	private $maj_mp;         ///< Date de la prochaine augmentation de MP
	/// Modifie les PA
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}
	/// Modifie les HP actuels
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}
	/// Renvoie les HP maximaux
	function get_hp_max($base = false)
	{
		if ($base)
			return $this->hp_max;
		else
			return $this->hp_max + $this->get_bonus_permanents('hp_max');
	}
	/// Modifie les HP maximaux
	function set_hp_max($hp_max)
	{
		$this->hp_max = $hp_max;
		$this->champs_modif[] = 'hp_max';
	}
	/// Renvoie les MP actuels
	function get_mp()
	{
		return $this->mp;
	}
	/// Modifie les MP actuels
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}
	/// Renvoie les MP maximaux
	function get_mp_max($base = false)
	{
		if ($base)
			return $this->mp_max;
		else
			return $this->mp_max + $this->get_bonus_permanents('mp_max');
	}
	/// Modifie les MP maximaux
	function set_mp_max($mp_max)
	{
		$this->mp_max = $mp_max;
		$this->champs_modif[] = 'mp_max';
	}
	/// Renvoie la date de la prochaine régénération de HP & MP
	function get_regen_hp()
	{
		return $this->regen_hp;
	}
	/// Modifie la date de la prochaine régénération de HP & MP
	function set_regen_hp($regen_hp)
	{
		$this->regen_hp = $regen_hp;
		$this->champs_modif[] = 'regen_hp';
	}
	/// Renvoie la date de la prochaine augmentation de HP
	function get_maj_hp()
	{
		return $this->maj_hp;
	}
	/// Modifie la date de la prochaine augmentation de HP
	function set_maj_hp($maj_hp)
	{
		$this->maj_hp = $maj_hp;
		$this->champs_modif[] = 'maj_hp';
	}
	/// Renvoie la date de la prochaine augmentation de MP
	function get_maj_mp()
	{
		return $this->maj_mp;
	}
	/// Modifie la date de la prochaine augmentation de MP
	function set_maj_mp($maj_mp)
	{
		$this->maj_mp = $maj_mp;
		$this->champs_modif[] = 'maj_mp';
	}
	/// Renvoie les HP maximums après bonus et malus
	function get_hp_maximum()
	{
		$this->hp_maximum = floor($this->get_hp_max());
		//Famine
		if($this->is_buff('famine'))
			$this->hp_maximum -= $this->hp_maximum * ($this->get_buff('famine', 'effet') / 100);
		if($this->is_buff('globe_vie'))
			$this->hp_maximum += $this->get_buff('globe_vie', 'effet');
		return $this->hp_maximum;
	}
	/// Renvoie les MP maximums après bonus et malus
	function get_mp_maximum()
	{
		$this->mp_maximum = floor($this->get_mp_max());
		//Famine
		if($this->is_buff('famine'))
			$this->mp_maximum -= $this->mp_maximum * ($this->get_buff('famine', 'effet') / 100);
		if($this->is_buff('globe_mana'))
			$this->mp_maximum += + $this->get_buff('globe_mana', 'effet');
		return $this->mp_maximum;
	}
	/// Renvoie la date de la dernière action
	function get_dernieraction()
	{
		return $this->dernieraction;
	}
	/// Modifie la date de la dernière action
	function set_dernieraction($dernieraction)
	{
		$this->dernieraction = $dernieraction;
		$this->champs_modif[] = 'dernieraction';
	}
  /// Ajoute des PA. S'assure que la valeur finale ne peut pas être négative
	function add_pa($add_pa)
  {
    global $G_PA_max;
    $this->set_pa($this->pa + $add_pa);
    if ($this->pa < 0)
      $this->pa = 0;
    else if( $this->pa > $G_PA_max )
      $this->pa = $G_PA_max;
  }
  /// Ajoute des HP. S'assure que la valeur finale ne peut pas être négative ni dépasser le maximum
  function add_hp($add_hp) 
  {
    $this->set_hp($this->hp + $add_hp);
    if ($this->hp > $this->get_hp_maximum())
      $this->hp = floor($this->get_hp_maximum());
    if ($this->hp < 0)
      $this->hp = 0;
  }
  /// Ajoute des MP. S'assure que la valeur finale ne peut pas être négative ni dépasser le maximum
  function add_mp($add_mp) 
  {
    $this->set_mp($this->mp + $add_mp);
    if ($this->mp > $this->get_mp_maximum())
      $this->mp = floor($this->get_mp_maximum());
    if ($this->mp < 0)
      $this->mp = 0;
  }
  /// Ajoute d'honneur. S'assure que la valeur finale ne peut pas être négative
  function add_honneur($add_h) 
  {
    $this->set_honneur($this->honneur + $add_h);
    if ($this->honneur < 0)
      $this->honneur = 0;
  }
  /// Supprime les rez lancées sur le personnage.
  function supprime_rez()
  {
	  global $db;
	  $requete = "DELETE FROM rez WHERE id_rez = ".$this->id;
	  $db->query($requete);
  }
  /**
   * Vérifie s'il faut mettre à jour, les HP, MP, PA,…
   * Gère les gains de PA, régénration de HP & MP, augmentation des maximums de HP & MP,
   * supprime les buffs & bans périmés ainsi que les créature de trop haut niveau dans l'écurie.
   */
	function check_perso($last_action = true)
	{
		$this->check_specials();
		
		$modif = false;	 // Indique si le personnage a été modifié.
		global $db, $G_temps_regen_hp, $G_temps_maj_hp, $G_temps_maj_mp, $G_temps_PA, $G_PA_max, $G_pourcent_regen_hp, $G_pourcent_regen_mp;
		// Passage de niveau
		if ($this->get_exp() > prochain_level($this->get_level()))
		{
			$this->set_level($this->get_level() + 1);
			$this->set_point_sso($this->get_point_sso() + 1);
			$this->sauver();
		}
		// On vérifie que le personnage est vivant
		if($this->hp > 0)
		{
			// On augmente les HP max si nécessaire
			$temps_maj = time() - $this->get_maj_hp(); // Temps écoulé depuis la dernière augmentation de HP.
			$temps_hp = $G_temps_maj_hp;  // Temps entre deux augmentation de HP.

			if ($temps_maj > $temps_hp && $temps_hp > 0) // Pour ne jamais diviser par 0…
			{
				$time = time();
				$nb_maj = floor($temps_maj / $temps_hp);
				$hp_gagne = $nb_maj * (pow($this->get_vie(true), 0.9) * 1.3);
				if (($this->get_hp_max(true) + $hp_gagne) < (120*(pow($this->get_vie(true), 0.9))))
				{
					$this->set_hp_max($this->get_hp_max(true) + $hp_gagne);
					$this->set_maj_hp($this->get_maj_hp() + $nb_maj * $temps_hp);
					$modif = true;
				}			
				else
				{
					$hp_gagne =0;
					$modif = true;
					$this->set_hp_max(120*(pow($this->get_vie(true), 0.9)));
					$this->set_maj_hp($this->get_maj_hp() + $nb_maj * $temps_hp);
				}
			} 
			// On augmente les MP max si nécessaire
			$temps_maj = time() - $this->get_maj_mp(); // Temps écoulé depuis la dernière augmentation de MP.
			$temps_mp = $G_temps_maj_mp;  // Temps entre deux augmentation de MP.
			if ($temps_maj > $temps_mp)
			{
				$time = time();
				$nb_maj = floor($temps_maj / $temps_mp);
				$mp_gagne = $nb_maj * (pow($this->get_energie(true), 1.2)/10);
				if (($this->get_mp_max(true) + $mp_gagne) < (pow($this->get_energie(true), 1.2)*13))
				{
					$this->set_mp_max($this->get_mp_max(true) + $mp_gagne);
					$this->set_maj_mp($this->get_maj_mp() + $nb_maj * $temps_mp);
					$modif = true;
				}
				else
				{
					$mp_gagne =0;
					$modif = true;
					$this->set_mp_max(pow($this->get_energie(true), 1.2)*13);
					$this->set_maj_mp($this->get_maj_mp() + $nb_maj * $temps_mp);
				}
			}
			// Régénération des HP et MP
			$temps_regen = time() - $this->get_regen_hp(); // Temps écoulé depuis la dernière régénération.

			// Gemme du troll
			$bonus_regen = array_key_exists('regeneration', $this->get_enchantement()) ? $this->get_enchantement('regeneration', 'effet') * 60 : 0;
			if($this->is_buff('potion_feu"'))
				$bonus_regen += $this->get_buff('potion_feu"', 'effet') * 60;
			// 1h min de regen
			if ($G_temps_regen_hp <= $bonus_regen)
				$bonus_regen = $G_temps_regen_hp - 3600; 

			if ($temps_regen > ($G_temps_regen_hp - $bonus_regen))
			{
				$time = time();
				$nb_regen = floor($temps_regen / ($G_temps_regen_hp - $bonus_regen));
				$regen_hp = $G_pourcent_regen_hp;
				$regen_mp = $G_pourcent_regen_mp;
				//Buff préparation du camp
				if($this->is_buff('preparation_camp'))
				{
					// Le buff a-t-il été lancé après la dernière régénération ?
					if($this->get_buff('preparation_camp', 'effet2') > $this->get_regen_hp())
					{
						// On calcule le moment où doit avoir lieu la première régénération après le lancement du buff
						$regen_cherche = $this->get_regen_hp() + (($G_temps_regen_hp - $bonus_regen) * floor(($this->get_buff('preparation_camp', 'effet2') - $this->get_regen_hp()) / $G_temps_regen_hp));
					}
					else $regen_cherche = $this->get_regen_hp();
					// Le buff s'est-il arrêté entre temps ?
					if($this->get_buff('preparation_camp', 'fin') > time()) $fin = time();
					else $fin = $this->get_buff('preparation_camp', 'fin');
					// On calcule le nombre de régénération pour lesquels le buff doit être pris en compte
					$nb_regen_avec_buff = floor(($fin - $regen_cherche) / ($G_temps_regen_hp - $bonus_regen));
					//bonus buff du camp
					$bonus_camp = 1 + ((($nb_regen_avec_buff / $nb_regen) * $this->get_buff('preparation_camp', 'effet')) / 100);
					$regen_hp = $regen_hp * $bonus_camp;
					$regen_mp = $regen_mp * $bonus_camp;
				}
				// Bonus raciaux
				if($this->get_race() == 'troll') $regen_hp = $regen_hp * 1.2;
				if($this->get_race() == 'elfehaut') $regen_mp = $regen_mp * 1.1;
				// Accessoires
				//if($this['accessoire']['id'] != '0' AND $this['accessoire']['type'] == 'regen_hp') $bonus_accessoire = $this['accessoire']['effet']; else $bonus_accessoire = 0;
				//if($this['accessoire']['id'] != '0' AND $this['accessoire']['type'] == 'regen_mp') $bonus_accessoire_mp = $this['accessoire']['effet']; else $bonus_accessoire_mp = 0;
				/*$accessoire = $this->get_accessoire();
				if($accessoire != false)
				{
					switch($accessoire->type)
					{
						case 'regen_hp':
							$bonus_accessoire = $accessoire->effet;
							break;
						case 'regen_mp':
							$bonus_accessoire_mp = $accessoire->effet;
							break;
						default:
							break;
					}
				}*/
				$bonus_arme = $this->get_bonus_permanents('regen_hp');
				$bonus_add_hp = $this->get_bonus_permanents('regen_hp_add');
				$bonus_arme_mp = $this->get_bonus_permanents('regen_mp');
				$bonus_add_mp = $this->get_bonus_permanents('regen_mp_add');
				if($this->is_buff('potion_troll'))
					$bonus_add_hp += $this->get_buff('potion_troll', 'effet');
				// Calcul des HP et MP récupérés
				$hp_gagne = $nb_regen * (floor($this->get_hp_maximum() * $regen_hp) + $bonus_arme + $bonus_add_hp);
				$mp_gagne = $nb_regen * (floor($this->get_mp_maximum() * $regen_mp) + $bonus_arme_mp + $bonus_add_mp);
				//DéBuff lente agonie
				if($this->is_buff('lente_agonie'))
				{
					// Le débuff a-t-il été lancé après la dernière régénération ?
					if($this->get_buff('lente_agonie', 'effet2') > $this->get_regen_hp())
					{
						$regen_cherche = $this->get_regen_hp() + (($G_temps_regen_hp - $bonus_regen) * floor(($this->get_buff('lente_agonie', 'effet2') - $this->get_regen_hp()) / $G_temps_regen_hp));
					}
					else $regen_cherche = $this->get_regen_hp();
					// Le débuff s'est-il arrêté entre temps ?
					if($this->get_buff('lente_agonie', 'fin') > time()) $fin = time();
					else $fin = $this->get_buff('lente_agonie', 'fin');
					// On calcule le nombre de régénération pour lesquels le débuff doit être pris en compte
					$nb_regen_avec_buff = floor(($fin - $regen_cherche) / ($G_temps_regen_hp - $bonus_regen));
					// Calcul du malus
					$malus_agonie = ((1 - ($nb_regen_avec_buff / $nb_regen)) - (($nb_regen_avec_buff / $nb_regen) * $this->get_buff('lente_agonie', 'effet')));
					$hp_gagne = round($hp_gagne * $malus_agonie);
				}
				//Maladie regen negative
				if($this->is_buff('regen_negative') AND !$this->is_buff('lente_agonie'))
				{
					$hp_gagne = $hp_gagne * -1;
					$mp_gagne = $mp_gagne * -1;
					// On diminue le nombre de régénération pendant lesquels la maladie est active ou supprime s'il n'y en  plus
					if($this->get_buff('regen_negative', 'effet') > 1)
					{
						$requete = "UPDATE buff SET effet = ".($this->get_buff('regen_negative', 'effet') - 1)." WHERE id = ".$this->get_buff('regen_negative', 'id');
					}
					else
					{
						$requete = "DELETE FROM buff WHERE id = ".$this->get_buff('regen_negative', 'id');
					}
					$db->query($requete);
				}
				//Maladie high regen
				if($this->is_buff('high_regen'))
				{
					$hp_gagne = $hp_gagne * 3;
					$mp_gagne = $mp_gagne * 3;
					// On diminue le nombre de régénération pendant lesquels la maladie est active ou supprime s'il n'y en  plus
					if($this->get_buff('high_regen', 'effet') > 1)
					{
						$requete = "UPDATE buff SET effet = ".($this->get_buff('high_regen', 'effet') - 1)." WHERE id = ".$this->get_buff('high_regen', 'id');
					}
					else
					{
						$requete = "DELETE FROM buff WHERE id = ".$this->get_buff('high_regen', 'id');
					}
					$db->query($requete);
				}
				//Maladie mort_regen
				if($this->is_buff('high_regen') AND $hp_gagne != 0 AND $mp_gagne != 0)
				{
					$hp_gagne = $this->get_hp();
				}
				// Mise à jour des HP
				
				if (($this->get_hp() + $hp_gagne) > $this->get_hp_maximum()) $this->set_hp(floor($this->get_hp_maximum()));
				else { $this->set_hp($this->get_hp() + $hp_gagne);}
				// Mise à jour des MP
				// Le nombre de MP total ne peut pas être négatif
				$this->set_mp(max(0,$this->get_mp() + $mp_gagne));
				if ($this->get_mp() > $this->get_mp_maximum()) $this->set_mp(floor($this->get_mp_maximum()));
				$this->set_regen_hp($this->get_regen_hp() + ($nb_regen * ($G_temps_regen_hp - $bonus_regen)));
			}

			if($last_action)
			{
				//Calcul des PA du joueur
				$time = time();
				$temps_pa = $G_temps_PA;
				// Nombre de PA à ajouter
				$panew = floor(($time - $this->get_dernieraction()) / $temps_pa);
				if($panew < 0) $panew = 0;
				$prochain = ($this->get_dernieraction() + $temps_pa) - $time;
				if ($prochain < 0) $prochain = 0;
				// Mise à jour des PA
				$this->set_pa($this->get_pa() + $panew);
				if ($this->get_pa() > $G_PA_max) $this->set_pa($G_PA_max);
				// Calcul du moment où a eu lieu le dernier gain de PA
				$j_d_a = (floor($time / $temps_pa)) * $temps_pa;
				if($j_d_a > $this->get_dernieraction()) $this->set_dernieraction($j_d_a);
			}

			// On ne doit pas avoir de pet indressable
			$pet_del = false;
			$ecurie = $this->get_pets();
			$max_dresse = $this->max_dresse();
			foreach ($ecurie as $pet) {
				$mob = $pet->get_monstre();
				if ($mob->get_level() > $max_dresse) {
					$journal = "INSERT INTO journal VALUES(NULL, $this->id, 'pet_leave',  '', '', NOW(), '".
						mysql_escape_string($pet->get_nom())."', 0, $this->x, $this->y)";
					$db->query($journal);
					$pet->supprimer();
					$pet_del = true;
				}
			}
			if ($pet_del) // On recharge l'ecurie
				$this->get_pets(true);

      // On ne doit pas avoir trop de pets
      if ($this->nb_pet() > $this->get_max_pet())
      {
        while ($this->nb_pet() > $this->get_max_pet())
        {
			    $ecurie = $this->get_pets();
          $pet_to_del_nb = rand(0, $this->nb_pet() - 1);
          $pet_to_del = $ecurie[$pet_to_del_nb];
					$journal = "INSERT INTO journal VALUES(NULL, $this->id, 'pet_leave',
            '', '', NOW(), '".mysql_escape_string($pet->get_nom())."', 0, 
            $this->x, $this->y)";
					$db->query($journal);
					$pet_to_del->supprimer();
          $this->get_pets(true);
        }
      }

			// Mise-à-jour du personnage dans la base de donnée
			$this->sauver();
		} // if($this->get_hp() > 0)

		// Gestion de la forme de demon
		if ($this->is_buff('debuff_forme_demon')) {
			$this->demonize();
		}

		// On supprime tous les buffs périmés
		$requete = "DELETE FROM buff WHERE fin <= ".time();
		$req = $db->query($requete);
		// On enlève le ban s'il y en a un et qu'il est fini
		$requete = "UPDATE perso SET statut = 'actif' WHERE statut = 'ban' AND fin_ban <= ".time();
		$db->query($requete);
	}
  // @}

	/**
	 * Prend en compte les effets qui peuvent agir sur le perso, notamment les effets de son inventaire
	 * 
 	*/
	function check_specials() {
		$this->check_materiel();
		// Gestion de la forme de demon
		if ($this->is_buff('debuff_forme_demon')) {
			$this->demonize();
		}
		// debuffs de sorcier
		if($this->is_buff('engloutissement'))
			$this->add_bonus_permanents('dexterite', -$this->get_buff('engloutissement', 'effet'));
		if($this->is_buff('deluge'))
			$this->add_bonus_permanents('volonte', -$this->get_buff('deluge', 'effet'));
	}

	function demonize() {
		if (isset($this->camouflage) && $this->camouflage == 'demon')
			return;
		$this->camouflage = 'demon';
		foreach (array('forcex', 'dexterite', 'vie', 'puissance', 'volonte', 'energie') as $bonus)
			$this->add_bonus_permanents($bonus, 6);
		foreach (array('melee', 'distance', 'incantation') as $bonus)
			$this->add_bonus_permanents($bonus, 400);
	}
  
  /**
   * @name  Sorts, compétences & buffs
   * Données et méthodes ayant trait aux sorts et compétences de combat et hors combat.
   * Ainsi que les buffs et débuffs du actifs sur le personnage.   
   */         
  // @{
	private $sort_jeu;     ///< Sorts hors combat.
	private $sort_combat;  ///< Sorts de combat.
	private $comp_jeu;     ///< Compétences hors combat.

  /// Vérifie si un sort hors combat est connu
	function check_sort_jeu_connu($id)
	{
		$connus = explode(';', $this->sort_jeu);
		if (!in_array($id, $connus))
			security_block(URL_MANIPULATION);
	}
	/// Renvoie les sorts hors combat.
	function get_sort_jeu()
	{
		return $this->sort_jeu;
	}
	/// Modifie les sorts hors combat.
	function set_sort_jeu($sort_jeu)
	{
		$this->sort_jeu = $sort_jeu;
		$this->champs_modif[] = 'sort_jeu';
	}
  /// Vérifie si un sort de combat est connu
	function check_sort_combat_connu($id)
	{
		$connus = explode(';', $this->sort_combat);
		if (!in_array($id, $connus))
			security_block(URL_MANIPULATION);
	}
	/// Renvoie les sorts de combat.
	function get_sort_combat()
	{
		return $this->sort_combat;
	}
	/// Modifie les sorts de combat.
	function set_sort_combat($sort_combat)
	{
		$this->sort_combat = $sort_combat;
		$this->champs_modif[] = 'sort_combat';
	}
  /// Vérifie si une compétence hors combat est connu
	function check_comp_jeu_connu($id)
	{
		$connus = explode(';', $this->comp_jeu);
		if (!in_array($id, $connus))
			security_block(URL_MANIPULATION);
	}
	/// Renvoie les compétences hors combat.
	function get_comp_jeu()
	{
		return $this->comp_jeu;
	}
	/// Modifie les compétences hors combat.
	function set_comp_jeu($comp_jeu)
	{
		$this->comp_jeu = $comp_jeu;
		$this->champs_modif[] = 'comp_jeu';
	}
  /// Vérifie si une compétence de combat est connu
	function check_comp_combat_connu($id)
	{
		$connus = explode(';', $this->comp_combat);
		if (!in_array($id, $connus))
			security_block(URL_MANIPULATION);
	}
	/// Renvoie les compétences de combat.
	function get_comp_combat()
	{
		return $this->comp_combat;
	}
	/// Modifie les compétences de combat.
	function set_comp_combat($comp_combat)
	{
		$this->comp_combat = $comp_combat;
		$this->champs_modif[] = 'comp_combat';
	}
	/**
	 * Renvoie une propriété d'un buff / débuff particulier actif sur le personnage ou l'ensemble de ceux-ci.
	 * @param  $nom      Nom (type) du (dé)buff recherché, renvoie tous les buffs actifs si vaut false.
	 * @param  $champ    Propriété recherchée (correspond à un champ dans la bdd).
	 * @param  $type	   Si false on prend le premier buff, si true celui dont le type correspond à $nom.
	 * @return     Tableau des buffs ou valeur demandée.	 
	 */	
	function get_buff($nom = false, $champ = false, $type = true)
	{
		if(!$nom)
		{
			$this->buff = buff::create('id_perso', $this->id, 'id ASC', 'type', false, true);
			return $this->buff;
		}
		else
		{
			if(!isset($this->buff)) $this->get_buff();
			if(!$type)
			{
				$get = 'get_'.$champ;
				return $this->buff[0]->$get();
			}
			else
				foreach($this->buff as $buff)
				{
					if($champ)
					{
						if($buff->get_type() == $nom)
						{
							$get = 'get_'.$champ;
							return $buff->$get();
						}
					}
					else
					{
						if($buff->get_type() == $nom)
						{
							return $buff;
						}
					}
				}
		}
		return false;
	}

	function get_nb_buff($debuff = 0)
	{
		return count(buff::create(array('id_perso', 'debuff'), array($this->id, $debuff), 'id ASC', 'type'));
	}
	/**
	 * Permet de savoir si le joueur est sous le buff nom
	 * @param $nom le nom du buff
	 * @param $type si le nom est le type du buff
	 * @return true si le perso est sous le buff false sinon.
 	*/
	function is_buff($nom = '', $type = true)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buffe = false;
		
		if(is_array($this->buff))
		{
			if(!empty($nom))
			{
				foreach($this->buff as $key => $buff)
				{
					if($type)
					{
						if($key == $nom) $buffe = true;
					}
					else if($buff->get_nom() ==  $nom)
					{
						$buffe = true;
					}
				}
			}
			else
				$buffe = (count($this->buff) > 0);
		}
		else
			$buffe = false;
			
		return $buffe;
	}
	/**
   * Ajoute un buff
   * @param  $nom     Nom du buff
   * @param  $effet   Effet principal
   * @param  $effet   Effet secondaire
   */
	function add_buff($nom, $effet, $effet2 = 0)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buff = new buff();
		$buff->set_type($nom);
		$buff->set_effet($effet);
		$buff->set_effet2($effet2);
		$this->buff[$nom] = $buff;
	}

	/// Lance un débuff sur l'entité lors d'un combat (uniquement sur un personnage)
  function lance_debuff($debuff)
  {
    $debuff->set_id_perso( $this->get_id() );
    return $debuff->lance_buff();
  }
	// @}
	
	/**
	 * @name Dresssage
	 * Données et méthodes liées au dressage.
	 */
  // @{
	private $max_pet;  ///< Nombre de créatures que le personnage peut posseder.

	/// Renvoie le potentiel de dressage pour un type de monstre donné
	function get_potentiel_dressage($type)
	{
		$dressage = $this->get_dressage();
		switch($type)
		{
		case 'bete':
			$dressage *= 1 + $this->get_bonus_permanents('dressage_bete') / 100;
			break;
		case 'humanoide':
			$dressage *= 1 + $this->get_bonus_permanents('dressage_humanoide') / 100;
			break;
		case 'magique':
			$dressage *= 1 + $this->get_bonus_permanents('dressage_magique') / 100;
		}
		return $dressage * 3 + $this->get_survie();
	}
	/// Renvoie le nombre de créatures que le personnage peut posseder.
	function get_max_pet()
	{
		if ($this->get_bonus_permanents('max_pet'))
			return $this->max_pet + $this->get_bonus_permanents('max_pet');
		return $this->max_pet;
	}
	/// Modifie le nombre de créatures que le personnage peut posseder.
	function set_max_pet($max_pet)
	{
		$this->max_pet = $max_pet;
		$this->champs_modif[] = 'max_pet';
	}
  /// Renvoie le niveau maximal des monstres que le personnage peut dresser.
	function max_dresse()
	{
		$max = (floor(pow($this->get_dressage(), 0.772) / 7) + 1);
		if($max < 1) $max = 1;
		if ($this->get_bonus_permanents('max_dresse'))
			$max += $this->get_bonus_permanents('max_dresse');
		return $max;
	}
  /// Indique si le personnage peut dresser ce monstre.
	function can_dresse($monstre)
	{
		$m = new monstre($monstre->get_type());
		if($this->max_dresse() >= $monstre->get_level() && $m->get_dressage() != 999999) return true;
		else return false;
	}
  /// Renvoie le nombre de créature possédé.
	function nb_pet()
	{
		return count($this->get_pets());
	}
  /// Renvoie le nombre de créature à l'écurie "commune"
	function nb_pet_ecurie()
	{
		return count($this->get_ecurie());
	}
  /// Renvoie le nombre de créature à l'écurie "personnel"
	function nb_pet_ecurie_self()
	{
		return count($this->get_ecurie_self());
	}
	/// Renvoie les créatures du personnage sous forme de tableau.
	function get_pets($force = false)
	{
		if(!isset($this->pets) OR $force) $this->pets = pet::create(array('id_joueur', 'ecurie'), array($this->id, 0), 'principale DESC');
		return $this->pets;
	}
  /// Renvoie la créature principale du personnage 
	function get_pet()
	{
		if(!isset($this->pet))
		{
			$pet = pet::create(array('id_joueur', 'principale', 'ecurie'), array($this->id, 1, 0), 'principale DESC');
			if (count($pet))
				$this->pet = $pet[0];
			else
				$this->pet = null;
		}
		return $this->pet;
	}
  /// Renvoie les créatures du personnages qui sont dans l'écurie "commune".
	function get_ecurie($force = false)
	{
		if(!isset($this->ecurie) OR $force) $this->ecurie = pet::create(array('id_joueur', 'ecurie'), array($this->id, 1), 'principale DESC');
		return $this->ecurie;
	}
  /// Renvoie les créatures qui sont dans l'écurie du terrain du personnage.
	function get_ecurie_self($force = false)
	{
		if(!isset($this->ecurie_self) OR $force) $this->ecurie_self = pet::create(array('id_joueur', 'ecurie'), array($this->id, 2), 'principale DESC');
		return $this->ecurie_self;
	}
  /**
   * Ajoute une créature à la liste de celle possédé
   * @param  $id_monstre    $id de la descrption du monstre.
   * @param  $hp            hp de la créature.
   * @param  $mp            mp de la créature.
   * @return    true si la créature a pu être ajoute, false sinon (plus de place).
   */        
	function add_pet($id_monstre, $hp = false, $mp = false)
	{
		if($this->nb_pet() >= $this->get_comp('max_pet')) return false;
		else
		{
			$monstre = new monstre($id_monstre);
			$pet = new pet();
			$pet->set_id_joueur($this->id);
			$pet->set_id_monstre($id_monstre);
			$pet->set_nom($monstre->get_nom());
			if(!$hp) $pet->set_hp($monstre->get_hp());
			else $pet->set_hp($hp);
			if (!$mp) $pet->set_mp($pet->get_mp_max());
			else $pet->set_mp($mp);
			if(count($this->get_pets()) == 0) $pet->set_principale(1);
			else $pet->set_principale(0);
			$pet->sauver();
			return true;
		}
	}
  /**
   * Change la créture principale
   * @param  $pet_id    Id de la créature qui doit devenir la principale.
   */   
	function set_pet_principale($pet_id)
	{
		global $db;
		$requete = "UPDATE pet SET principale = 0 WHERE id_joueur = ".$this->id;
		$db->query($requete);
		$pet = new pet($pet_id);
		$pet->set_principale(1);
		$pet->sauver();
	}
  /**
   * Met une créature à l'écurie.*
   * @param  $pet_id        Id de la créature.
   * @param  $type_ecurie   2 pour l'écurie du personnage, 1 pour l'écurie commune.
   * @param  $max_ecurie    Nombre de créature que peut contenir l'écurie.
   * @param  $taxe          taxe à appliquer pour mettre la créature à l'écurie.
   */  
	function pet_to_ecurie($pet_id, $type_ecurie = 1, $max_ecurie = 10, $taxe = 0)
	{
		$pet = new pet($pet_id);
		if($pet->get_id_joueur() == $this->get_id())
		{
			$cout = $pet->get_cout_depot() + $taxe;
			if(($this->get_star() >= $cout) OR $type_ecurie == 2)
			{
				if ($type_ecurie == 2)
				{
					$test_nb_pet = $this->nb_pet_ecurie_self();
				}
				else
				{
					$test_nb_pet = $this->nb_pet_ecurie();
				}
				
				if($test_nb_pet < $max_ecurie)
				{
					$pet->set_ecurie($type_ecurie);
					$pet->set_principale(0);
					$pet->sauver();
					if($type_ecurie == 1)
					{
						$this->set_star($this->get_star() - $cout);
						$this->sauver();
					}
					return true;
				}
				else
				{
					interf_alerte::enregistre(interf_alerte::msg_erreur, 'L\'écurie ne peut pas prendre plus de '.$max_ecurie.' créatures'); 
					return false;
				}
			}
			else
			{
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous n\'avez pas assez de stars !');
				return false;
			}
		}
		else
		{
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette créature ne vous appartient pas !');
			return false;
		}
	}
  /**
   * Récupère une créature de l'écurie
   * @param  $pet_id    Id de la créature.
   */  
	function pet_from_ecurie($pet_id)
	{
		$pet = new pet($pet_id);
		if($pet->get_id_joueur() == $this->get_id())
		{
			if($this->nb_pet() < $this->get_comp('max_pet'))
			{
				$pet->set_ecurie(0);
				$pet->sauver();
			}
			else
				interf_alerte::enregistre(interf_alerte::msg_erreur, 'Vous ne pouvez pas prendre plus de créature avec vous.');
		}
		else
			interf_alerte::enregistre(interf_alerte::msg_erreur, 'Cette créature ne vous appartient pas !');
	}
  /// Renvoie les HP redonnés à la créature par un soin
	function soin_pet()
	{
		$facteur = ($this->get_vie() + 10) / 22;
		if($this->get_race() == 'scavenger') $facteur = $facteur * 1.2;
		return floor(sqrt($this->get_dressage()) * 4 * $facteur);
	}
	/// Renvoie la distance d'attaque avec le pet
	function get_distance_pet()
	{
		global $db;
		$distance = 0;
		/*$arme = $this->inventaire_pet()->arme_pet;
		if($arme)
			$distance += $this->arme_pet->distance_tir;*/
		/// @todo à revoir
		$laisse = decompose_objet($this->get_inventaire_partie("cou", true));
		if($laisse['id_objet'] != '')
		{
			$requete = "SELECT distance_tir FROM objet_pet WHERE id = ".$laisse['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$distance += $row[0];
		}
		return $distance;
	}
  // @} 
	
	/**
	 * @name Groupe & quêtes
	 * Données et méthodes liées au groupe et aux quêtes.
	 */
  // @{
	private $groupe;     ///< Id du groupe du personnage.
	private $quete;      ///< Quêtes que possède le personnage.
	private $quete_fini; ///< Quêtes terminées.
	public  $share_xp;

  /// Indique si le personnage a un groupe
	function is_groupe()
	{
		return !empty($this->groupe);
	}
	/// Renvoie l'id du groupe du personnage.
	function get_groupe()
	{
		return $this->groupe;
	}
	/// Modifie l'id du groupe du personnage.
	function set_groupe($groupe)
	{
		$this->groupe = $groupe;
		$this->champs_modif[] = 'groupe';
	}
	
	/// Renvoie la liste des quêtes que possède le personnage sous forme textuelle.
	/// @deprecated
	function get_quete()
	{
		return $this->quete;
	}
	
  /// Renvoie la liste des quêtes que possède le personnage sous forme de tableau.
	function get_liste_quete()
	{
		/*$this->liste_quete = unserialize($this->quete);
		return $this->liste_quete;*/
		return quete_perso::create('id_perso', $this->id);
	}
	/// Modifie la liste des quêtes que possède le personnage.
	/// @deprecated
	function set_quete($quete)
	{
		$this->quete = $quete;
		$this->champs_modif[] = 'quete';
	}
	/// Renvoie la liste des quêtes que terminées.
	function get_quete_fini()
	{
		return $this->quete_fini;
	}
	/// Modifie la liste des quêtes que terminées.
	function set_quete_fini($quete_fini)
	{
		$this->quete_fini = $quete_fini;
		$this->champs_modif[] = 'quete_fini';
	}
  /// Ajoute une quête à la liste des quêtes que possède le personnage.
	function prend_quete($id_quete)
	{
		global $db;
		$quete = new quete($id_quete);
		// quêtes prises pour tout le groupe en même temps
		$num_etape = 1;
		$etape = null;
		switch( $quete->get_type() )
		{
		case 'royaume':
			/// @todo passer à l'objet
			$requete = 'SELECT id_etape, qe.id FROM quete_perso AS qp INNER JOIN perso AS p ON qp.id_perso = p.id INNER JOIN quete_etape AS qe ON qe.id = qp.id_etape WHERE p.race ="'.$this->race.'" AND qp.id_quete = '.$quete->get_id().' ORDER BY qe.etape DESC LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_array($req);
			if( $row )
					$num_etape = $row[0];
		case 'groupe':
			$id_groupe = $this->get_groupe();
			if( $id_groupe )
			{
				$groupe = new groupe($id_groupe);
				/// @todo passer à l'objet
				$requete = 'SELECT id_etape FROM quete_perso AS qp INNER JOIN perso AS p ON qp.id_perso = p.id INNER JOIN quete_etape AS qe ON qe.id = qp.id_etape WHERE p.groupe = '.$this->groupe.' AND qp.id_quete = '.$quete->get_id().' ORDER BY qe.etape DESC LIMIT 1';
				$req = $db->query($requete);
				$row = $db->read_array($req);
				if( $row )
					$etape = max($row[0], $etape);
				
				$etape = quete_etape::create(array('id_quete', 'etape'), array($quete->get_id(), $num_etape))[0];
				foreach($groupe->get_membre_joueur() as $membre)
				{
					if( $membre->get_id() == $this->id )
						continue;
					$qp = quete_perso::create(array('id_quete', 'id_perso'), array($id_quete, $membre->get_id()));
					if( !$qp && $quete->a_requis($membre) )
					{
						$qp = new quete_perso($membre->id, $quete, $etape->get_id());
						$qp->sauver();
					}
				}
			}
		}
		//Vérifie si le joueur n'a pas déjà pris la quête.
		$qp = quete_perso::create(array('id_quete', 'id_perso'), array($id_quete, $this->get_id()));
		if( $qp )
		{
			$G_erreur = 'Vous avez déjà cette quête en cours !';
			return false;
		}
		// On vérifie que la quête peut être prise
		if( $quete->a_requis($this) )
		{
			if( $etape )
				$qp = new quete_perso($this->id, $quete, $etape->get_id());
			else
				$qp = new quete_perso($this->id, $quete);
			$qp->sauver();
			if( !$etape )
				$etape = $qp->get_etape();
			$etape->initialiser();
			return true;
		}
		/// @todo loguer triche
		return false;
	}
	/// Ajoute les quête disponibles au bureau des quêtes à la liste des quêtes que possède le personnage.
	function prend_quete_tout(&$royaume, $fournisseur='bureau_quete')
	{
		$quetes = quete::get_quetes_dispos($this, $royaume, $fournisseur);
		foreach($quetes as $quete)
		{
			$qp = new quete_perso($this->id, $quete);
			$qp->sauver();
		}
		return count($quetes);
	}
	// @}
	
	/**
	 * @name Position & déplacement
	 * Données et méthodes liées à la position et au déplacement.
	 */  
  // @{
	private $teleport_roi; ///< enum('true','false'), indique si le téléport du roi a été utilisé.
	public $poscase;
	public $pospita;

	/**
	* Retourne la position
	* @access public
	* @param none
	* @return int
	*/
	function get_case()
	{
		return $this->x.$this->y;
	}
	/**
   * Retourne la position "old style"
   */
	function get_poscase()
	{
		return $this->y * 1000 + $this->x;
	}
  /// renvoie la distance avec un autre joueur
	function get_distance_joueur($joueur)
	{
		return calcul_distance($this->get_pos(), $joueur->get_pos());
	}
  /// renvoie la distance pytagorienne avec un autre joueur
	function get_distance_pytagore($joueur)
	{
		return calcul_distance_pytagore($this->get_pos(), $joueur->get_pos());
	}
	/// Indique si le téléport du roi a été utilisé.
	function get_teleport_roi()
	{
		return $this->teleport_roi;
	}
  /// Modifie l'indicateur d'utilisation du téléport du roi.
	function set_teleport_roi($teleport_roi)
	{
		$this->teleport_roi = $teleport_roi;
		$this->champs_modif[] = 'teleport_roi';
	}
  /**
   * Indique si le personnage est dans une arène.
   * @return  false s'il n'est pas dans une arène, sinon objet contenant la
   *          description de l'arène dans laquelle il se trouve.
   */  
	function in_arene($filter = '')
	{
		if ($this->x < 190 && $this->y < 190)
			return false;
		global $db;
		$q = "select * from arenes where x <= $this->x and $this->x < x + size ".
			"and y <= $this->y and $this->y < y + size $filter";
		$req = $db->query($q);
		if ($row = $db->read_object($req)) {
			return $row;
		}
		return false;
	}
  /// Met à jour les fichiers XML de l'arène avec les données du personnage.
	function trigger_arene()
	{
		$arene = $this->in_arene();
		if ($arene != false)
		{
			require_once(root.'arenes/gen_arenes.php');
			$arene_xml = gen_arene($arene->x, $arene->y, $arene->size, $arene->nom);
			$arene_file = fopen(root.'arenes/'.$arene->file.'tmp', 'w+');
			fwrite($arene_file, $arene_xml[0]);
			fclose($arene_file);
			rename(root.'arenes/'.$arene->file.'tmp', root.'arenes/'.$arene->file);
			$arene_file = fopen(root.'arenes/admin/'.$arene->file.'tmp', 'w+');
			fwrite($arene_file, $arene_xml[1]);
			fclose($arene_file);
			rename(root.'arenes/admin/'.$arene->file.'tmp', root.'arenes/admin/'.$arene->file);
		}
	}
	// @}
	
	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	private $action_a;       ///< Id du script d'attaque
	private $action_d;       ///< Id du script de défense
	public $action_do;
	public $reserve_bonus;   ///< RM avec les bonus dus aux buffs

	/// Renvoie l'id du script d'attaque.
	function get_action_a()
	{
		return $this->action_a;
	}
	/// Modifie l'id du script d'attaque.
	function set_action_a($action_a)
	{
		$this->action_a = $action_a;
		$this->champs_modif[] = 'action_a';
	}

	/// Renvoie l'id du script d'attaque.
	function get_action_d()
	{
		return $this->action_d;
	}
	/// Modifie l'id du script de défense.
	function set_action_d($action_d)
	{
		$this->action_d = $action_d;
		$this->champs_modif[] = 'action_d';
	}
	/**
	 * Récupère le contenu du script pour une action donnéd
	 * @param  $type_action    'attaque' ou 'defense'.
	 * @return     Contenu du script (sous forme textuelle).
	 */   	 
	function recupaction($type_action)
	{
		global $db;
		if($type_action == 'defense' && $this->action_d != 0) $action_id = $this->action_d;
		else $action_id = $this->action_a;
		if($action_id != 0)
		{
			$requete = "SELECT action FROM action_perso WHERE id = ".$action_id;
			$req = $db->query($requete);
			$row = $db->read_row($req);
		}
		else
		{
			$row[0] = '!';
		}
		$this->action = $row[0];
		return $this->action;
	}
	
	/**
	 * Renvoie la RM
	 * @param  $base  true s'il faut renvoyer la valeur de base, false pour renvoyer la RM avec les bonus permanents (mais sans les buffs).
	 */
	function get_reserve($base = false)
	{
		if (!isset($this->reserve))
			$this->reserve = ceil(2.1 * ($this->get_energie() + floor(($this->get_energie() - 8) / 2)));
		if (!$base) return $this->reserve + $this->get_bonus_permanents('reserve');
		else return $this->reserve;
	}
	/// Renvoie la RM avec les bonus dû aux buffs
	function get_reserve_bonus()
	{
		$this->reserve_bonus = $this->get_reserve();
		if($this->is_buff('buff_inspiration')) $this->reserve_bonus += $this->get_buff('buff_inspiration', 'effet');
		if($this->is_buff('buff_rune')) $this->reserve_bonus += $this->get_buff('buff_rune', 'effet');
		if($this->is_buff('buff_sacrifice')) $this->reserve_bonus += $this->get_buff('buff_sacrifice', 'effet');
		// Les bonus raciaux sont comptés dans les bonus perm
		return $this->reserve_bonus;
	}

  /// Renvoie la distance à laquelle le personnage peut attaquer
	function get_distance_tir()
	{
		if(!isset($this->arme)) $this->get_arme();
		if($this->arme)
		{
			$distance = $this->arme->distance_tir;
			if($this->is_buff('longue_portee') && $this->arme->type == 'arc' )
				$distance += $this->get_buff('longue_portee', 'effet');
			return $distance + $this->get_bonus_permanents('portee');
		}
		return 0;
	}
  /// Action effectuées à la fin d'un combat
  function fin_combat(&$perso, $degats=null)
  {
    /*$this->objet_ref->set_hp( $this->get_hp() );
    $this->objet_ref->sauver();*/
    $this->sauver();
  }
  /// Action effectuées à la fin d'un combat PvP
  function fin_combat_pvp(&$ennemi, $defense, $batiment=false)
  {
    global $db, $G_xp_rate, $G_range_level, $G_crime, $Gtrad;
	
	$msg_xp = '';
    if( $this->est_mort() )
    {
			$this->trigger_arene();
			//On supprime toutes les rez et (de)bufffs bâtiments
			$this->supprime_rez();
			buff_batiment::suppr_mort_perso($this);
			//Achievement
			if($this->get_hp() == 0)
				$this->unlock_achiev('near_kill');
			if($this->get_groupe() == 0)
				$this->unlock_achiev('divided_fall');

			// Augmentation du compteur de l'achievement
			$achiev = $ennemi->get_compteur('kill_'.$this->get_race());
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();
			
			if(!$defense) //Si le perso est mort en PvP en n'etant pas en defense (<=> il est mort en attaque)
			{
				// Augmentation du compteur de l'achievement
				$achiev = $ennemi->get_compteur('kill_defense');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}
			
			if ($this->get_nom() == 'Irulan')
				$ennemi->unlock_achiev('kill_bastounet');
			
			if ($this->get_crime() > 0)
			{
				$achiev = $ennemi->get_compteur('dredd');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}

			//Gain d'expérience
			$xp = $this->get_level() * 100 * $G_xp_rate;
      if( $this->get_level() > 2 )
        $xp += 100 / ($this->get_level() - 2);

			//Si le joueur a un groupe
			if($ennemi->get_groupe() > 0)
			{
				$groupe = new groupe($ennemi->get_groupe());
				$groupe->get_share_xp($ennemi->get_pos(), $this->get_level());
				//Si on tape un joueur de son groupe xp = 0
				foreach($groupe->membre_joueur as $membre_id)
				{
					if($membre_id->get_id() == $this->get_id())
					{
						$xp = 0;

						// Augmentation du compteur de l'achievement
						$achiev = $ennemi->get_compteur('kill_teammate');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
				}
			}
			//Joueur solo
			else
			{
				$groupe = new groupe();
				$groupe->level_groupe = $ennemi->get_level();
				$groupe->somme_groupe = $ennemi->get_level();
				$groupe->set_share_xp(100);
				$groupe->membre_joueur[0] = &$ennemi;//new perso();
				/*$groupe->membre_joueur[0]->set_x($ennemi->get_x());
				$groupe->membre_joueur[0]->set_y($ennemi->get_y());
				$groupe->membre_joueur[0]->set_id($ennemi->get_id());
				$groupe->membre_joueur[0]->share_xp = 100;
				$groupe->membre_joueur[0]->set_race($ennemi->get_race());
				$groupe->membre_joueur[0]->set_level($ennemi->get_level());
				$groupe->membre_joueur[0]->set_exp($ennemi->get_exp());
				$groupe->membre_joueur[0]->set_star($ennemi->get_star());
				$groupe->membre_joueur[0]->set_honneur($ennemi->get_honneur());
				$groupe->membre_joueur[0]->set_reputation($ennemi->get_reputation());*/
			}
			$G_range_level = max(ceil($this->get_level() * 0.5), 3);
			//$xp = $xp * (1 + (($this->get_level() - $ennemi->get_level()) / $G_range_level));
			if($xp < 0) $xp = 0;
			//Si il est en groupe réduction de l'xp gagné par rapport au niveau du groupe
			/*if($ennemi->get_groupe() > 0)
			{
				$xp = $xp * $ennemi->get_level() / $groupe->get_level();
			}*/
			$honneur = floor($xp * 4);

			//Partage de l'xp au groupe
			foreach($groupe->membre_joueur as $membre)
			{
				//Facteur de diplomatie
				$requete = "SELECT ".$this->get_race()." FROM diplomatie WHERE race = '".$membre->get_race()."'";
				$req_diplo = $db->query($requete);
				$row_diplo = $db->read_row($req_diplo);

				//Vérification crime
				if($membre->get_id() == $ennemi->get_id() AND $crime AND $defense)
				{
					$points = $G_crime[$row_diplo[0]];
					$ennemi->set_crime($ennemi->get_crime() + $points);
					$msg_xp .=  'Vous tuez un joueur en '.$Gtrad['diplo'.$row[0]].', vous recevez '.$points.' point(s) de crime<br />';
				}
				$star = 0;
				if ($row_diplo[0] == 127) $row_diplo[0] = 0;
				//Si le défenseur est criminel
				if($pascrime)
				{
					switch($amende['statut'])
					{
						case 'bandit' :
							$row_diplo[0] = 5;
							$statut_joueur = 'Bandit';
						break;
						case 'criminel' :
							$row_diplo[0] = 10;
							$statut_joueur = 'Criminel';
							if($amende['prime'] > 0)
							{
								$star = $amende['prime'];
								$msg_xp .=  'Vous avez tué un criminel ayant une prime sur sa tête, vous gagnez '.$star.' stars.<br />';
								$requete = "UPDATE amende SET prime = 0 WHERE id = ".$amende['id'];
								$db->query($requete);
								$requete = "DELETE FROM prime_criminel WHERE id_amende = ".$amende['id'];
								$db->query($requete);
							}
						break;
					}

					$xp = $xp / 5;
					$honneur = $honneur / 5;
				}
				$facteur_xp = $row_diplo[0] * 0.2;
				$facteur_honneur = ($row_diplo[0] * 0.2) - 0.8;
				if ($facteur_honneur < 0) $facteur_honneur = 0;
				//XP Final
				$partage = $groupe->get_share_xp($ennemi->get_pos(), $this->get_level());
				$partage = $partage == 0 ? 1 : $partage;
        if( $membre->get_id() == $ennemi->get_id() )
          $niv = $ennemi->get_level();
        else
          $niv = max($groupe->get_level(), $ennemi->get_level(), $membre->get_level());
			  $xp_gagne = $xp * (1 + (($this->get_level() - $niv) / $G_range_level));
				$xp_gagne = floor(($xp_gagne * $facteur_xp) * $membre->share_xp / $partage);
				if($xp_gagne < 0) $xp_gagne = 0;
				$honneur_gagne = floor(($honneur * $facteur_honneur) * $membre->share_xp / $partage);
				//(de)Buffs moral, pour la gloire, cacophonie
				if($membre->is_buff('moral'))
          $honneur_gagne = floor( $honneur_gagne * (1 + ($membre->get_buff('moral', 'effet') / 100)) );
				if($membre->is_buff('buff_honneur'))
          $honneur_gagne = floor( $honneur_gagne * (1 + ($membre->get_buff('buff_honneur', 'effet') / 100)) );
				if($membre->is_buff('cacophonie'))
          $honneur_gagne = floor( $honneur_gagne * (1 - ($membre->get_buff('cacophonie', 'effet') / 100)) );
				$reputation_gagne = floor($honneur_gagne / 10);

				// Pas d'honneur pour un kill de sa propre race
				if ($membre->get_race() == $this->get_race())
					$honneur_gagne = 0;

				$membre->set_star($membre->get_star() + $star);
				$membre->set_exp($membre->get_exp() + $xp_gagne);
				$membre->set_honneur($membre->get_honneur() + $honneur_gagne);
				$membre->set_reputation($membre->get_reputation() + $reputation_gagne);
				$msg_xp .= $membre->get_nom().' gagne <strong class="reward">'.$xp_gagne.' XP</strong>, <strong class="reward">'.$honneur_gagne.' points d\'honneur</strong>, et <strong class="reward">'.$reputation_gagne.' points de réputation</strong><br />';
				$membre->sauver();
				if($defense && $membre->get_id() == $ennemi->get_id()) quete_perso::verif_action('J'.$row_diplo[0], $membre, 's');
				else quete_perso::verif_action('J'.$row_diplo[0], $membre, 'g');
			}

			// Augmentation du compteur de l'achievement
			if($ennemi->get_level() >= $this->get_level()) // Kill d'un joueur d'un plus petit level
				$achiev = $ennemi->get_compteur('kill_lower');
			else
				$achiev = $ennemi->get_compteur('kill_higher');
			$achiev->set_compteur($achiev->get_compteur() + 1);
			$achiev->sauver();

			// Achievement joueur meme race
			if($ennemi->get_race() == $this->get_race())
			{
				$achiev = $ennemi->get_compteur('kill_race');
				$achiev->set_compteur($achiev->get_compteur() + 1);
				$achiev->sauver();
			}

			$achievement_bouche_oreille = achievement_type::create('variable', 'bouche_oreille');
			// Si le joueur mort l'a deja debloqué
			if($this->already_unlocked_achiev($achievement_bouche_oreille[0]))
				$ennemi->unlock_achiev('bouche_oreille');

			$ennemi->set_frag($ennemi->get_frag() + 1);
			$this->set_mort($this->get_mort() + 1);
			$ennemi->sauver();
			$this->sauver();
    }
    return $msg_xp;
  }
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, $batiment)
  {
  	global $G_no_ambiance_kill_message;
    $msg_xp = $perso->fin_combat_pvp($this, false);
    $msg_xp .= $this->fin_combat_pvp($perso, true, $batiment);
		if ( $this->est_mort() ) {
			if (!$G_no_ambiance_kill_message) {
				$txt = '';
				if ($this->get_level() < $perso->get_level() - 9)
					$txt = 'A vaincre sans péril on triomphe sans gloire.';
				elseif ($this->get_level() > $perso->get_level() + 9)
					$txt = 'Félicitation, tu es venu à bout de '.$this->get_nom().'.';
				elseif ($this->get_level() >= $perso->get_level() - 9 AND $this->get_level() <= $perso->get_level() + 9)
					$txt = 'Tu as tué '.$this->get_nom().'.';
				interf_base::add_courr( new interf_bal_smpl('p', $txt, false, 'ambiance_kill_message') );
			}
		}
    return $msg_xp;
  }
  /// Renvoie le coût en PA pour attaquer l'entité
  function get_cout_attaque_base(&$perso)
  {
    global $G_PA_attaque_joueur;
    if($this->get_race() == $perso->get_race() && $this->in_arene() == false)
    {
		$cout = 3;
		$amende = recup_amende($this->get_id());
		if($amende)
		{
			if($amende['statut'] != 'normal') $cout = 0;
		}
		return $G_PA_attaque_joueur + $cout;
	}
    else
      return $G_PA_attaque_joueur;
  }
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	* Constructeur
	* Crée l'objet à partir de l'id (en cherchant dans la base de données), d'un tableau ou des paramètres.
  *
	* @param int(11) id attribut
	* @param int(10) mort attribut
	* @param varchar(50) nom attribut
	* @param varchar(40) password attribut
	* @param varchar(100) email attribut
	* @param int(11) exp attribut
	* @param mediumint(8) honneur attribut
	* @param int(10) reputation attribut
	* @param mediumint(9) level attribut
	* @param mediumint(9) rang_royaume attribut
	* @param mediumint(9) vie attribut
	* @param mediumint(9) forcex attribut
	* @param mediumint(9) dexterite attribut
	* @param mediumint(9) puissance attribut
	* @param mediumint(9) volonte attribut
	* @param mediumint(9) energie attribut
	* @param varchar(20) race attribut
	* @param varchar(20) classe attribut
	* @param tinyint(3) classe_id attribut
	* @param text inventaire attribut
	* @param text inventaire_slot attribut
	* @param smallint(6) pa attribut
	* @param int(11) dernieraction attribut
	* @param int(10) action_a attribut
	* @param int(10) action_d attribut
	* @param text sort_jeu attribut
	* @param text sort_combat attribut
	* @param text comp_combat attribut
	* @param text comp_jeu attribut
	* @param int(11) star attribut
	* @param mediumint(9) x attribut
	* @param mediumint(9) y attribut
	* @param int(11) groupe attribut
	* @param mediumint(9) hp attribut
	* @param float hp_max attribut
	* @param mediumint(8) mp attribut
	* @param float mp_max attribut
	* @param mediumint(9) melee attribut
	* @param mediumint(9) distance attribut
	* @param mediumint(9) esquive attribut
	* @param mediumint(8) blocage attribut
	* @param mediumint(9) incantation attribut
	* @param mediumint(9) sort_vie attribut
	* @param int(11) sort_element attribut
	* @param int(11) sort_mort attribut
	* @param mediumint(8) identification attribut
	* @param int(10) craft attribut
	* @param mediumint(8) alchimie attribut
	* @param mediumint(8) architecture attribut
	* @param mediumint(8) forge attribut
	* @param mediumint(8) survie attribut
	* @param double facteur_magie attribut
	* @param double facteur_sort_vie attribut
	* @param double facteur_sort_mort attribut
	* @param double facteur_sort_element attribut
	* @param int(10) regen_hp attribut
	* @param int(10) maj_hp attribut
	* @param int(10) maj_mp attribut
	* @param tinyint(3) point_sso attribut
	* @param text quete attribut
	* @param text quete_fini attribut
	* @param int(10) dernier_connexion attribut
	* @param varchar(50) statut attribut
	* @param int(11) fin_ban attribut
	* @param int(10) frag attribut
	* @param float crime attribut
	* @param int(10) amende attribut
	* @param enum('true','false') teleport_roi attribut
	* @param tinyint(3) cache_classe attribut
	* @param tinyint(3) cache_stat attribut
	* @param tinyint(3) cache_niveau attribut
	* @param tinyint(3) max_pet attribut
	* @param tinyint(3) beta attribut
	* @return none
	*/
	function __construct($id = 0, $mort = 0, $nom = '', $password = '', $email = '', $exp = 0, $honneur = 0, $reputation = 0, $level = '', $rang_royaume = '', $vie = '', $forcex = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $race = '', $classe = '', $classe_id = '', $inventaire = '', $inventaire_pet = '', $inventaire_slot = '', $encombrement=0, $pa = '', $dernieraction = '', $action_a = 0, $action_d = 0, $sort_jeu = '', $sort_combat = '', $comp_combat = '', $comp_jeu = '', $star = '', $x = '', $y = '', $groupe = 0, $hp = '', $hp_max = '', $mp = '', $mp_max = '', $melee = '', $distance = '', $esquive = '', $blocage = '', $incantation = '', $sort_vie = '', $sort_element = '', $sort_mort = '', $identification = '', $craft = '', $alchimie = '', $architecture = '', $forge = '', $survie = '', $dressage = 0, $facteur_magie = '', $facteur_sort_vie = 0, $facteur_sort_mort = 0, $facteur_sort_element = 0, $regen_hp = '', $maj_hp = '', $maj_mp = '', $point_sso = 0, $quete = '', $quete_fini = '', $dernier_connexion = 0, $statut = '', $fin_ban = 0, $frag = 0, $crime = 0, $amende = 0, $teleport_roi = 'false', $cache_classe = 0, $cache_stat = 0, $cache_niveau = 0, $max_pet = 0, $beta = 0, $joueur=null, $tuto = 1, $date_creation = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT mort, nom, password, email, exp, honneur, reputation, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_pet, inventaire_slot, encombrement, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, dressage, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, max_pet, beta, id_joueur, tuto, date_creation FROM perso WHERE id = '$id'");
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->mort, $this->nom, $this->password, $this->email, $this->exp, $this->honneur, $this->reputation, $this->level, $this->rang_royaume, $this->vie, $this->forcex, $this->dexterite, $this->puissance, $this->volonte, $this->energie, $this->race, $this->classe, $this->classe_id, $this->inventaire, $this->inventaire_pet, $this->inventaire_slot, $this->encombrement, $this->pa, $this->dernieraction, $this->action_a, $this->action_d, $this->sort_jeu, $this->sort_combat, $this->comp_combat, $this->comp_jeu, $this->star, $this->x, $this->y, $this->groupe, $this->hp, $this->hp_max, $this->mp, $this->mp_max, $this->melee, $this->distance, $this->esquive, $this->blocage, $this->incantation, $this->sort_vie, $this->sort_element, $this->sort_mort, $this->identification, $this->craft, $this->alchimie, $this->architecture, $this->forge, $this->survie, $this->dressage, $this->facteur_magie, $this->facteur_sort_vie, $this->facteur_sort_mort, $this->facteur_sort_element, $this->regen_hp, $this->maj_hp, $this->maj_mp, $this->point_sso, $this->quete, $this->quete_fini, $this->dernier_connexion, $this->statut, $this->fin_ban, $this->frag, $this->crime, $this->amende, $this->teleport_roi, $this->cache_classe, $this->cache_stat, $this->cache_niveau, $this->max_pet, $this->beta, $this->id_joueur, $this->tuto, $this->date_creation) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->mort = $id['mort'];
			$this->nom = $id['nom'];
			$this->password = $id['password'];
			$this->email = $id['email'];
			$this->exp = $id['exp'];
			$this->honneur = $id['honneur'];
			$this->reputation = $id['reputation'];
			$this->level = $id['level'];
			$this->rang_royaume = $id['rang_royaume'];
			$this->vie = $id['vie'];
			$this->forcex = $id['forcex'];
			$this->dexterite = $id['dexterite'];
			$this->puissance = $id['puissance'];
			$this->volonte = $id['volonte'];
			$this->energie = $id['energie'];
			$this->race = $id['race'];
			$this->classe = $id['classe'];
			$this->classe_id = $id['classe_id'];
			$this->inventaire = $id['inventaire'];
			$this->inventaire_pet = $id['inventaire_pet'];
			$this->inventaire_slot = $id['inventaire_slot'];
			$this->pa = $id['pa'];
			$this->dernieraction = $id['dernieraction'];
			$this->action_a = $id['action_a'];
			$this->action_d = $id['action_d'];
			$this->sort_jeu = $id['sort_jeu'];
			$this->sort_combat = $id['sort_combat'];
			$this->comp_combat = $id['comp_combat'];
			$this->comp_jeu = $id['comp_jeu'];
			$this->star = $id['star'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->groupe = $id['groupe'];
			$this->hp = $id['hp'];
			$this->hp_max = $id['hp_max'];
			$this->mp = $id['mp'];
			$this->mp_max = $id['mp_max'];
			$this->melee = $id['melee'];
			$this->distance = $id['distance'];
			$this->esquive = $id['esquive'];
			$this->blocage = $id['blocage'];
			$this->incantation = $id['incantation'];
			$this->sort_vie = $id['sort_vie'];
			$this->sort_element = $id['sort_element'];
			$this->sort_mort = $id['sort_mort'];
			$this->identification = $id['identification'];
			$this->craft = $id['craft'];
			$this->alchimie = $id['alchimie'];
			$this->architecture = $id['architecture'];
			$this->forge = $id['forge'];
			$this->survie = $id['survie'];
			$this->dressage = $id['dressage'];
			$this->facteur_magie = $id['facteur_magie'];
			$this->facteur_sort_vie = $id['facteur_sort_vie'];
			$this->facteur_sort_mort = $id['facteur_sort_mort'];
			$this->facteur_sort_element = $id['facteur_sort_element'];
			$this->regen_hp = $id['regen_hp'];
			$this->maj_hp = $id['maj_hp'];
			$this->maj_mp = $id['maj_mp'];
			$this->point_sso = $id['point_sso'];
			$this->quete = $id['quete'];
			$this->quete_fini = $id['quete_fini'];
			$this->dernier_connexion = $id['dernier_connexion'];
			$this->statut = $id['statut'];
			$this->fin_ban = $id['fin_ban'];
			$this->frag = $id['frag'];
			$this->crime = $id['crime'];
			$this->amende = $id['amende'];
			$this->teleport_roi = $id['teleport_roi'];
			$this->cache_classe = $id['cache_classe'];
			$this->cache_stat = $id['cache_stat'];
			$this->cache_niveau = $id['cache_niveau'];
			$this->max_pet = $id['max_pet'];
			$this->beta = $id['beta'];
			$this->id_joueur = $id['id_joueur'];
			$this->tuto = $id['tuto'];
			$this->date_creation = $id['date_creation'];
			$this->encombrement = $id['encombrement'];
		}
		else
		{
			$this->mort = $mort;
			$this->nom = $nom;
			$this->password = $password;
			$this->email = $email;
			$this->exp = $exp;
			$this->honneur = $honneur;
			$this->reputation = $reputation;
			$this->level = $level;
			$this->rang_royaume = $rang_royaume;
			$this->vie = $vie;
			$this->forcex = $forcex;
			$this->dexterite = $dexterite;
			$this->puissance = $puissance;
			$this->volonte = $volonte;
			$this->energie = $energie;
			$this->race = $race;
			$this->classe = $classe;
			$this->classe_id = $classe_id;
			$this->inventaire = $inventaire;
			$this->inventaire_pet = $inventaire_pet;
			$this->inventaire_slot = $inventaire_slot;
			$this->encombrement = $encombrement;
			$this->pa = $pa;
			$this->dernieraction = $dernieraction;
			$this->action_a = $action_a;
			$this->action_d = $action_d;
			$this->sort_jeu = $sort_jeu;
			$this->sort_combat = $sort_combat;
			$this->comp_combat = $comp_combat;
			$this->comp_jeu = $comp_jeu;
			$this->star = $star;
			$this->x = $x;
			$this->y = $y;
			$this->groupe = $groupe;
			$this->hp = $hp;
			$this->hp_max = $hp_max;
			$this->mp = $mp;
			$this->mp_max = $mp_max;
			$this->melee = $melee;
			$this->distance = $distance;
			$this->esquive = $esquive;
			$this->blocage = $blocage;
			$this->incantation = $incantation;
			$this->sort_vie = $sort_vie;
			$this->sort_element = $sort_element;
			$this->sort_mort = $sort_mort;
			$this->identification = $identification;
			$this->craft = $craft;
			$this->alchimie = $alchimie;
			$this->architecture = $architecture;
			$this->forge = $forge;
			$this->survie = $survie;
			$this->dressage = $dressage;
			$this->facteur_magie = $facteur_magie;
			$this->facteur_sort_vie = $facteur_sort_vie;
			$this->facteur_sort_mort = $facteur_sort_mort;
			$this->facteur_sort_element = $facteur_sort_element;
			$this->regen_hp = $regen_hp;
			$this->maj_hp = $maj_hp;
			$this->maj_mp = $maj_mp;
			$this->point_sso = $point_sso;
			$this->quete = $quete;
			$this->quete_fini = $quete_fini;
			$this->dernier_connexion = $dernier_connexion;
			$this->statut = $statut;
			$this->fin_ban = $fin_ban;
			$this->frag = $frag;
			$this->crime = $crime;
			$this->amende = $amende;
			$this->teleport_roi = $teleport_roi;
			$this->cache_classe = $cache_classe;
			$this->cache_stat = $cache_stat;
			$this->cache_niveau = $cache_niveau;
			$this->max_pet = $max_pet;
			$this->beta = $beta;
			$this->tuto = $tuto;
			$this->id = $id;
			$this->joueur = $joueur;
			$this->date_creation = $date_creation;
		}
		$this->type = 'perso';

		$this->applique_bonus();
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
			if( $force || count($this->champs_modif) > 0 )
			{
				if($force) $champs = 'mort = '.$this->mort.', nom = "'.mysql_escape_string($this->nom).'", password = "'.mysql_escape_string($this->password).'", email = "'.mysql_escape_string($this->email).'", exp = "'.mysql_escape_string($this->exp).'", honneur = "'.mysql_escape_string($this->honneur).'", reputation = "'.mysql_escape_string($this->reputation).'", level = "'.mysql_escape_string($this->level).'", rang_royaume = "'.mysql_escape_string($this->rang_royaume).'", vie = "'.mysql_escape_string($this->vie).'", forcex = "'.mysql_escape_string($this->forcex).'", dexterite = "'.mysql_escape_string($this->dexterite).'", puissance = "'.mysql_escape_string($this->puissance).'", volonte = "'.mysql_escape_string($this->volonte).'", energie = "'.mysql_escape_string($this->energie).'", race = "'.mysql_escape_string($this->race).'", classe = "'.mysql_escape_string($this->classe).'", classe_id = "'.mysql_escape_string($this->classe_id).'", inventaire = "'.mysql_escape_string($this->inventaire).'", inventaire_pet = "'.mysql_escape_string($this->inventaire_pet).'", inventaire_slot = "'.mysql_escape_string($this->inventaire_slot).'", pa = "'.mysql_escape_string($this->pa).'", dernieraction = "'.mysql_escape_string($this->dernieraction).'", action_a = "'.mysql_escape_string($this->action_a).'", action_d = "'.mysql_escape_string($this->action_d).'", sort_jeu = "'.mysql_escape_string($this->sort_jeu).'", sort_combat = "'.mysql_escape_string($this->sort_combat).'", comp_combat = "'.mysql_escape_string($this->comp_combat).'", comp_jeu = "'.mysql_escape_string($this->comp_jeu).'", star = "'.mysql_escape_string($this->star).'", x = "'.mysql_escape_string($this->x).'", y = "'.mysql_escape_string($this->y).'", groupe = "'.mysql_escape_string($this->groupe).'", hp = "'.mysql_escape_string($this->hp).'", hp_max = "'.mysql_escape_string($this->hp_max).'", mp = "'.mysql_escape_string($this->mp).'", mp_max = "'.mysql_escape_string($this->mp_max).'", melee = "'.mysql_escape_string($this->melee).'", distance = "'.mysql_escape_string($this->distance).'", esquive = "'.mysql_escape_string($this->esquive).'", blocage = "'.mysql_escape_string($this->blocage).'", incantation = "'.mysql_escape_string($this->incantation).'", sort_vie = "'.mysql_escape_string($this->sort_vie).'", sort_element = "'.mysql_escape_string($this->sort_element).'", sort_mort = "'.mysql_escape_string($this->sort_mort).'", identification = "'.mysql_escape_string($this->identification).'", craft = "'.mysql_escape_string($this->craft).'", alchimie = "'.mysql_escape_string($this->alchimie).'", architecture = "'.mysql_escape_string($this->architecture).'", forge = "'.mysql_escape_string($this->forge).'", survie = "'.mysql_escape_string($this->survie).'", dressage = "'.mysql_escape_string($this->dressage).'", facteur_magie = "'.mysql_escape_string($this->facteur_magie).'", facteur_sort_vie = "'.mysql_escape_string($this->facteur_sort_vie).'", facteur_sort_mort = "'.mysql_escape_string($this->facteur_sort_mort).'", facteur_sort_element = "'.mysql_escape_string($this->facteur_sort_element).'", regen_hp = "'.mysql_escape_string($this->regen_hp).'", maj_hp = "'.mysql_escape_string($this->maj_hp).'", maj_mp = "'.mysql_escape_string($this->maj_mp).'", point_sso = "'.mysql_escape_string($this->point_sso).'", quete = "'.mysql_escape_string($this->quete).'", quete_fini = "'.mysql_escape_string($this->quete_fini).'", dernier_connexion = "'.mysql_escape_string($this->dernier_connexion).'", statut = "'.mysql_escape_string($this->statut).'", fin_ban = "'.mysql_escape_string($this->fin_ban).'", frag = "'.mysql_escape_string($this->frag).'", crime = "'.mysql_escape_string($this->crime).'", amende = "'.mysql_escape_string($this->amende).'", teleport_roi = "'.mysql_escape_string($this->teleport_roi).'", cache_classe = "'.mysql_escape_string($this->cache_classe).'", cache_stat = "'.mysql_escape_string($this->cache_stat).'", cache_niveau = "'.mysql_escape_string($this->cache_niveau).'", max_pet = "'.mysql_escape_string($this->max_pet).'", beta = "'.mysql_escape_string($this->beta).'", tuto = "'.mysql_escape_string($this->tuto).'", id_joueur = '.($this->id_joueur?$this->id_joueur:'NULL').', date_creation = '.$this->date_creation.'';
				else
				{
					$champs = '';
					$this->champs_modif = array_unique($this->champs_modif);
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE perso SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO perso (mort, nom, password, email, exp, honneur, reputation, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_pet, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, dressage, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, max_pet, beta, tuto, id_joueur, date_creation) VALUES(';
			$requete .= ''.$this->mort.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->password).'", "'.mysql_escape_string($this->email).'", "'.mysql_escape_string($this->exp).'", "'.mysql_escape_string($this->honneur).'", "'.mysql_escape_string($this->reputation).'", "'.mysql_escape_string($this->level).'", "'.mysql_escape_string($this->rang_royaume).'", "'.mysql_escape_string($this->vie).'", "'.mysql_escape_string($this->forcex).'", "'.mysql_escape_string($this->dexterite).'", "'.mysql_escape_string($this->puissance).'", "'.mysql_escape_string($this->volonte).'", "'.mysql_escape_string($this->energie).'", "'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->classe).'", "'.mysql_escape_string($this->classe_id).'", "'.mysql_escape_string($this->inventaire).'", "'.mysql_escape_string($this->inventaire_pet).'", "'.mysql_escape_string($this->inventaire_slot).'", "'.mysql_escape_string($this->pa).'", "'.mysql_escape_string($this->dernieraction).'", "'.mysql_escape_string($this->action_a).'", "'.mysql_escape_string($this->action_d).'", "'.mysql_escape_string($this->sort_jeu).'", "'.mysql_escape_string($this->sort_combat).'", "'.mysql_escape_string($this->comp_combat).'", "'.mysql_escape_string($this->comp_jeu).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->x).'", "'.mysql_escape_string($this->y).'", "'.mysql_escape_string($this->groupe).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->hp_max).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->mp_max).'", "'.mysql_escape_string($this->melee).'", "'.mysql_escape_string($this->distance).'", "'.mysql_escape_string($this->esquive).'", "'.mysql_escape_string($this->blocage).'", "'.mysql_escape_string($this->incantation).'", "'.mysql_escape_string($this->sort_vie).'", "'.mysql_escape_string($this->sort_element).'", "'.mysql_escape_string($this->sort_mort).'", "'.mysql_escape_string($this->identification).'", "'.mysql_escape_string($this->craft).'", "'.mysql_escape_string($this->alchimie).'", "'.mysql_escape_string($this->architecture).'", "'.mysql_escape_string($this->forge).'", "'.mysql_escape_string($this->survie).'", "'.mysql_escape_string($this->dressage).'", "'.mysql_escape_string($this->facteur_magie).'", "'.mysql_escape_string($this->facteur_sort_vie).'", "'.mysql_escape_string($this->facteur_sort_mort).'", "'.mysql_escape_string($this->facteur_sort_element).'", "'.mysql_escape_string($this->regen_hp).'", "'.mysql_escape_string($this->maj_hp).'", "'.mysql_escape_string($this->maj_mp).'", "'.mysql_escape_string($this->point_sso).'", "'.mysql_escape_string($this->quete).'", "'.mysql_escape_string($this->quete_fini).'", "'.mysql_escape_string($this->dernier_connexion).'", "'.mysql_escape_string($this->statut).'", "'.mysql_escape_string($this->fin_ban).'", "'.mysql_escape_string($this->frag).'", "'.mysql_escape_string($this->crime).'", "'.mysql_escape_string($this->amende).'", "'.mysql_escape_string($this->teleport_roi).'", "'.mysql_escape_string($this->cache_classe).'", "'.mysql_escape_string($this->cache_stat).'", "'.mysql_escape_string($this->cache_niveau).'", "'.mysql_escape_string($this->max_pet).'", "'.mysql_escape_string($this->beta).'", "'.mysql_escape_string($this->tuto).'",'.($this->id_joueur?$this->id_joueur:'NULL').','.$this->date_creation.')';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
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
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false, $key_unique = false)
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
				$where[] = $champ .' = \''.sSQL($array_valeurs[$key]).'\'';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = "SELECT * FROM perso WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new perso($row);
				else $return[$row[$keys]][] = new perso($row);
			}
		}
		else $return = array();
    /*$dbg = 'perso::create : '.$requete.' ('.var_export($array_champs, true).' - '.var_export($array_valeurs, true).')';
    log_admin::log('debug', $dbg, true);*/
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
		return 'id = '.$this->id.', mort = '.$this->mort.', nom = '.$this->nom.', password = '.$this->password.', email = '.$this->email.', exp = '.$this->exp.', honneur = '.$this->honneur.', reputation = '.$this->reputation.', level = '.$this->level.', rang_royaume = '.$this->rang_royaume.', vie = '.$this->vie.', forcex = '.$this->forcex.', dexterite = '.$this->dexterite.', puissance = '.$this->puissance.', volonte = '.$this->volonte.', energie = '.$this->energie.', race = '.$this->race.', classe = '.$this->classe.', classe_id = '.$this->classe_id.', inventaire = '.$this->inventaire.', inventaire_pet = '.$this->inventaire_pet.', inventaire_slot = '.$this->inventaire_slot.', pa = '.$this->pa.', dernieraction = '.$this->dernieraction.', action_a = '.$this->action_a.', action_d = '.$this->action_d.', sort_jeu = '.$this->sort_jeu.', sort_combat = '.$this->sort_combat.', comp_combat = '.$this->comp_combat.', comp_jeu = '.$this->comp_jeu.', star = '.$this->star.', x = '.$this->x.', y = '.$this->y.', groupe = '.$this->groupe.', hp = '.$this->hp.', hp_max = '.$this->hp_max.', mp = '.$this->mp.', mp_max = '.$this->mp_max.', melee = '.$this->melee.', distance = '.$this->distance.', esquive = '.$this->esquive.', blocage = '.$this->blocage.', incantation = '.$this->incantation.', sort_vie = '.$this->sort_vie.', sort_element = '.$this->sort_element.', sort_mort = '.$this->sort_mort.', identification = '.$this->identification.', craft = '.$this->craft.', alchimie = '.$this->alchimie.', architecture = '.$this->architecture.', forge = '.$this->forge.', survie = '.$this->survie.', dressage = '.$this->dressage.', facteur_magie = '.$this->facteur_magie.', facteur_sort_vie = '.$this->facteur_sort_vie.', facteur_sort_mort = '.$this->facteur_sort_mort.', facteur_sort_element = '.$this->facteur_sort_element.', regen_hp = '.$this->regen_hp.', maj_hp = '.$this->maj_hp.', maj_mp = '.$this->maj_mp.', point_sso = '.$this->point_sso.', quete = '.$this->quete.', quete_fini = '.$this->quete_fini.', dernier_connexion = '.$this->dernier_connexion.', statut = '.$this->statut.', fin_ban = '.$this->fin_ban.', frag = '.$this->frag.', crime = '.$this->crime.', amende = '.$this->amende.', teleport_roi = '.$this->teleport_roi.', cache_classe = '.$this->cache_classe.', cache_stat = '.$this->cache_stat.', cache_niveau = '.$this->cache_niveau.', max_pet = '.$this->max_pet.', beta = '.$this->beta.', tuto ='.$this->tuto.', date_creation ='.$this->date_creation;
	}
	// @}
	
	/**
	 * @name Les achievements
	 * Gestion des achievements
	 */
  // @{
  /// Renvoie un compteur d'achievement
  function get_compteur($variable)
  {
		global $db;
		$requete = "SELECT id, id_perso, compteur, variable FROM achievement_compteur WHERE id_perso = '".$this->id."' AND variable = '".$variable."'";
		$req = $db->query($requete);
		if ($db->num_rows($req) > 0) // Le compteur existe
		{
			$row = $db->read_array($req);
			$compteur = new achievement_compteur($row);
		}
		else // Sinon on le crée
		{
			$compteur = new achievement_compteur();
			$compteur->set_id_perso($this->id);
			$compteur->set_variable($variable);
			$compteur->set_compteur(0);
			$compteur->sauver();
		}
		return $compteur;
	}
	/// Renvoie les achievements sous forme de tableau associatif
	function get_achievement()
   {
		global $db;
		if(!isset($this->achievement))
		{
			$requete = "SELECT id_perso, id_achiev, achievement_type.id, nom, description, value, variable, secret, strong, color FROM achievement 
			LEFT JOIN achievement_type ON achievement.id_achiev = achievement_type.id
			WHERE id_perso = '".$this->id."' ORDER BY nom ASC";
			$req = $db->query($requete);
			while($row = $db->read_assoc($req))
			{
				$this->achievement[$row['id_achiev']] = $row;
			}
		}
		return $this->achievement;
	}
	/// Indique si un achievement a déjà été débloqué
	function already_unlocked_achiev($achievement_type)
	{
		global $db;
    if (is_string($achievement_type)) {
      $achievements = achievement_type::create('variable', $achievement_type);
      if (!($achievements == null || count($achievements) == 0))
        $achievement_type = $achievements[0];
    }
		$requete = "SELECT id FROM achievement WHERE id_perso = '".$this->id."' AND id_achiev = '".$achievement_type->get_id()."'";
		$req = $db->query($requete);
		if ($db->num_rows($req) > 0) // L'achievement est deja debloqué
			return true;
		else
			return false;
	}
	/// Débloque un achievement
	function unlock_achiev($variable, $hide_message = false)
	{
		$achievement_type = achievement_type::create('variable', $variable);
		// L'achiev n'existe pas
		if ($achievement_type == null || count($achievement_type) == 0)
			return;
		// Si le joueur ne l'a pas deja debloqué
		if(!$this->already_unlocked_achiev($achievement_type[0]))
		{
			// On debloque l'achievement
			$achievement = new achievement();
			$achievement->set_id_perso($this->get_id());
			$achievement->set_id_achiev($achievement_type[0]->get_id());
			$achievement->sauver();
			if ($hide_message == false)
				interf_alerte::enregistre(interf_alerte::msg_info, $this->get_nom().' debloque l\'achievement "'.$achievement_type[0]->get_nom().'" !');
		}
	}

	/** 
	 * Renvoie vrai si le joueur a debloque un achievement de type (aka 'variable') donne 
	 */
	function check_achiev_by_type($type)
	{
		global $db;
		$t = sSQL($type, SSQL_STRING);
		$requete = "SELECT id FROM achievement a, achievement_type t WHERE a.id_perso = $this->id AND a.id_achiev = t.id AND t.variable = '$t'";
		$req = $db->query($requete);
		return ($db->num_rows($req) > 0);
	}

	private $camouflage = null;
	function get_camouflage()
	{
		return $this->camouflage;
	}

	function get_race_a()
	{
		global $Trace;
		if( $this->is_buff('potion_apparence') )
			return $Trace['liste'][ $this->get_buff('potion_apparence', 'effet') ];
		else if( $this->camouflage )
			return $this->camouflage;
		else 
			return $this->get_race();
	}
	// @}
	
	static function get_perso_rumeur($champ, $vrai, $cache=false)
	{
		global $db;
		$de = rand(1, 55);
		$val = 10;
		$incr = 9;
		for($i=0; $de > $val; $i++)
		{
			$val += $incr;
			$incr--;
		}
		$race = joueur::get_perso()->get_race();
		$cond = $cache ? ' (cache_stat = 2 OR (cache_stat = 1 AND race != "'.$race.'"))' : ' (cache_stat = 0 OR (cache_stat = 1 AND race = "'.$race.'"))';
		if( $vrai )
		{
			if( $champ == 'artisanat' )
				$champ = 'SQRT( (architecture + alchimie + forge + indentification) / 10 )';
			$requete = 'SELECT nom FROM perso WHERE statut = "actif" AND level > 0 AND '.$cond.' ORDER BY '.$champ.' DESC LIMIT '.$i.', 1';
		}
		else
			$requete = 'SELECT nom FROM perso WHERE statut = "actif" AND level > 0 AND '.$cond.' ORDER BY RAND() LIMIT 1';
		$req = $db->query($requete);
		$row = $db->read_array($req);
		return $row[0];
	}
	
	static function get_royaume_rumeur($info, $plus, $class, $vrai)
	{
		global $db, $Trace;
		if( !$vrai )
		{
			$requete = 'SELECT * FROM royaume WHERE id > 0 ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			return new royaume($row);
		}
		$sens = $plus ? ' DESC' : ' ASC';
		$requete = 'SELECT race, SUM('.$info.') AS val FROM perso WHERE statut = "actif" AND level > 0 ORDER BY val'.$sens.' LIMIT '.$class.', 1';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return new royaume( $Trace[ $row['race'] ]['numrace'] );
	} 
	
	static function get_groupe_rumeur($info, $plus, $vrai)
	{
		global $db, $Trace;
		if( !$vrai )
		{
			$requete = 'SELECT nom FROM groupe ORDER BY RAND() LIMIT 1';
			$req = $db->query($requete);
			$row = $db->read_array($req);
			return $row[0];
		}
		$de = rand(1, 55);
		$val = 10;
		$incr = 9;
		for($i=0; $de > $val; $i++)
		{
			$val += $incr;
			$incr--;
		}
		if( $info == 'artisanat' )
			$info = 'SQRT( (architecture + alchimie + forge + indentification) / 10 )';
		$requete = 'SELECT g.nom, SUM('.$info.') AS val FROM perso AS p INNER JOIN groupe AS g ON p.groupe = g.id WHERE statut = "actif" AND level > 0 GROUP BY g.id ORDER BY val'.$sens.' LIMIT '.$val.', 1';
		$sens = $plus ? ' DESC' : ' ASC';
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		return $row['nom'];
	}
   
	/** on ne m'aura plus avec les machins déclarés depuis dehors */
	//function __get($name) { $debug = debug_backtrace(); die('fuck: '.$debug[0]['file'].' line '.$debug[0]['line']); }
	//function __set($name, $value) { $debug = debug_backtrace(); die('fuck: '.$debug[0]['file'].' line '.$debug[0]['line']); }

}
?>
