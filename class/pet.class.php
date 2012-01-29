<?php
/**
 * @file pet.class.php
 * Définition de la classe pet représentant un monstre dressé
 */

/**
 * Classe pet
 * Classe représentant un monstre dressé
 */
 
class pet extends map_monstre
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $id_joueur; ///< id du joueur propriétaire du monstre
	protected $mp;  ///< MP du monstre
	protected $principale; ///< indique si c'est la créature principale du dresseur
	protected $ecurie; ///< indique si le monstre est dans une écurie (et dans laquelle) ou non

	/// Retourne l'id du joueur propriétaire du monstre
	function get_id_joueur()
	{
		return $this->id_joueur;
	}
	/// Modifie l'id du joueur propriétaire du monstre
	function set_id_joueur($id_joueur)
	{
		$this->id_joueur = $id_joueur;
		$this->champs_modif[] = 'type';
	}

	/// Renvoie les MP du monstre
	function get_mp()
	{
		return $this->mp;
	}
	/// Modifie les MP du monstre
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/// Renvoie si c'est la créature principale du dresseur
	function get_principale()
	{
		return $this->principale;
	}
	/// Modifie si c'est la créature principale du dresseur
	function set_principale($principale)
	{
		$this->principale = $principale;
		$this->champs_modif[] = 'principale';
	}

	/// Renvoie si le monstre est dans une écurie (et dans laquelle) ou non
	function get_ecurie()
	{
		return $this->ecurie;
	}
	/// Modifie si le monstre est dans une écurie (et dans laquelle) ou non
	function set_ecurie($ecurie)
	{
		$this->ecurie = $ecurie;
		$this->champs_modif[] = 'ecurie';
	}

  /// Renvoie la race
	function get_race($perso)
	{
		return $perso->get_race();
	}
	
	/// Indique que l'entité est morte
	function mort(&$perso)
  {
    $this->sauver();
  }
  /// Action effectuées à la fin d'un combat pour le défenseur
  function fin_defense(&$perso, &$royaume, $pet, $degats, &$def)
  {
    return $perso->fin_combat_pvp($this, false);
  }
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * @access public

	 * @param int(10) id attribut
	 * @param int(10) id_joueur attribut
	 * @param int(10) id_monstre attribut
	 * @param varchar(100) nom attribut
	 * @param mediumint(8) hp attribut
	 * @param mediumint(8) mp attribut
	 * @param tinyint(3) principale attribut
	 * @return none
	 */
	function __construct($id = 0, $id_joueur = 0, $id_monstre = 0, $nom = '', $hp = 0, $mp = 0, $principale = 0, $ecurie = 0, $action_a = 0, $action_d = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query('SELECT p.id_joueur, p.id_monstre, p.nom, p.hp, p.mp, p.principale, p.ecurie, p.action_a, p.action_d, m.level, m.lib FROM pet p, monstre m WHERE p.id_monstre = m.id AND p.id = '.$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				$this->init_tab( $db->read_array($requeteSQL) );
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->init_tab($id);
		}
		else
		{
      $requeteSQL = $db->query('SELECT level, lib FROM monstre WHERE id='.$id_monstre);
      if( $db->num_rows($requeteSQL) > 0 )
      {
        $row = $db->read_array($requeteSQL);
        map_monstre::__construct($id, $nom, $id_monstre, null, null, $hp, $row['level'], $nom, $row['lib']);
      }
      else
        map_monstre::__construct($id, $nom, $id_monstre, null, null, $hp, 0, $nom);
			$this->id_joueur = $id_joueur;
			$this->id_monstre = $id_monstre;
			$this->mp = $mp;
			$this->principale = $principale;
			$this->ecurie = $ecurie;
			$this->action_a = $action_a;
			$this->action_d = $action_d;
		}
	}

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    map_monstre::init_tab($vals);
		$this->id_joueur = $vals['id_joueur'];
		$this->id_monstre = $vals['id_monstre'];
		$this->mp = $vals['mp'];
		$this->principale = $vals['principale'];
		$this->ecurie = $vals['ecurie'];
		$this->action_a = $vals['action_a'];
		$this->action_d = $vals['action_d'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'id_joueur, id_monstre, nom, hp, mp, principale, ecurie, action_a, action_d';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return $this->id_joueur.', '.$this->id_monstre.', \''.mysql_escape_string($this->nom).'\', '.$this->hp.', '.$this->mp.', '.$this->principale.', '.$this->ecurie.', '.$this->action_a.', '.$this->action_d;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'id_joueur = '.$this->id_joueur.', id_monstre = '.$this->id_monstre.', nom = \''.mysql_escape_string($this->nom).'\', hp = '.$this->hp.', mp = '.$this->mp.', principale = '.$this->principale.', ecurie = '.$this->ecurie.', action_a = '.$this->action_a.', action_d = '.$this->action_d;
	}
	// @}

  /**
   * @name  Buffs
   * Données et méthodes ayant trait aux buffs et débuffs actifs sur le monstre.
   */
  // @{
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
			$this->buff = buff::create('id_perso', $this->id_joueur, 'id ASC', 'type');
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
					if($buff->get_type() == $nom)
					{
						$get = 'get_'.$champ;
						return $buff->$get();
					}
				}
		}
	}
	// @}

	//fonction
	function get_monstre()
	{
		if(!isset($this->monstre)) $this->monstre = new monstre($this->get_id_monstre());
		return $this->monstre;
	}

	function get_mp_max()
	{
		$this->get_monstre();
		return $this->monstre->get_energie() * 10;
	}

	function get_cout_soin()
	{
		$this->get_monstre();

		return pow($this->get_level(), 2);
	}

	function get_cout_rez()
	{
		$this->get_monstre();
		return pow($this->get_level(), 2) * 20;
	}

	function get_cout_depot()
	{
		$this->get_monstre();
		return pow($this->get_level(), 2);
	}

  /**
   * @name Inventaires et objets
	 * Données et méthodes liées à l'inventaire et aux objets portés ou utiliser par
	 * les perosnnages.
	 */
  // @{
	public $pp_base; ///< PP de base (sans les buffs).
	public $pm_base; ///< PM de base (sans les buffs).
	public $enchant; ///< plus utilisé.
	public $armure;  ///< true si la PP et la PM on été calculées, false sinon.
	
	/// Calcule la PP et la PM en fonction des pièces d'armures portées ainsi que des buffs et autres modificateurs.
	function get_armure()
	{
		global $db;
		if(!isset($this->armure))
		{
			$joueur = new perso($this->get_id_joueur());
			$this->get_monstre();
			$this->pp = 0;
			$this->pm = 0;
			// Pièces d'armure
			$partie_armure = array('cou', 'selle', 'dos', 'torse');
			foreach($partie_armure as $partie)
			{
				if($partie != '')
				{
					$partie_d = decompose_objet($joueur->get_inventaire_partie($partie, true));
					if($partie_d['id_objet'] != '')
					{
						$requete = "SELECT PP, PM, effet FROM objet_pet WHERE id = ".$partie_d['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_row($req);
						$this->pp += $row[0];
						$this->pm += $row[1];
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
						//my_dump($this->enchantement);
						//$this->enchant = enchant($partie_d['enchantement'], $this);
					}
				}
			}
			$this->pp += $this->monstre->get_pp();
			$this->pm += $this->monstre->get_pm();
			$this->pp_base = $this->pp;
			$this->pm_base = $this->pm;

			//Buffs
			if($joueur->is_buff('buff_bouclier')) $this->pp = round($this->pp * (1 + ($joueur->get_buff('buff_bouclier', 'effet') / 100)));
			if($joueur->is_buff('buff_barriere')) $this->pm = round($this->pm * (1 + ($joueur->get_buff('buff_barriere', 'effet') / 100)));
			if($joueur->is_buff('buff_forteresse'))
			{
				$this->pp = round($this->pp * (1 + (($joueur->get_buff('buff_forteresse', 'effet')) / 100)));
				$this->pm = round($this->pm * (1 + (($joueur->get_buff('buff_forteresse', 'effet2')) / 100)));
			}
			if($joueur->is_buff('buff_cri_protecteur')) $this->pp = round($this->pp * (1 + ($joueur->get_buff('buff_cri_protecteur', 'effet') / 100)));
			if($joueur->is_buff('debuff_desespoir')) $this->pm = round($this->pm / (1 + (($joueur->get_buff('debuff_desespoir', 'effet')) / 100)));
			//Maladie suppr_defense
			if($joueur->is_buff('suppr_defense')) $this->pp = 0;
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
		if(!$base) return $this->pm;
		else return $this->pm_base;
	}
  /// Renvoie le bonus de PM dû à l'armure
  function get_bonus_pm()
  {
		if(!isset($this->pm))
		{
			$this->get_armure();
		}
		return $this->pm - $this->pm_base;
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
		if(!$base) return $this->pp;
		else return $this->pp_base;
	}
  /// Renvoie le bonus de PP dû à l'armure
  function get_bonus_pp()
  {
		if(!isset($this->pp))
		{
			$this->get_armure();
		}
		return $this->pp - $this->pp_base;
  }

	// Renvoie l'arme de la main droite. Enregistre les enchantements et les effets.
	function get_arme()
	{
		if(!isset($this->arme_pet))
		{
			$joueur = new perso($this->get_id_joueur());
			global $db;
			$arme = $joueur->inventaire_pet()->arme_pet;
			if($arme != '')
			{
				$arme_d = decompose_objet($arme);
				$requete = "SELECT * FROM objet_pet WHERE id = ".$arme_d['id_objet'];
				$req = $db->query($requete);
				$this->arme_pet = $db->read_object($req);
				/*if ($arme_d['enchantement'] != null)
				 {
					$gemme = new gemme_enchassee($arme_d['enchantement']);
					if ($gemme->enchantement_type == 'degat')
					$this->arme_pet->degat += $gemme->enchantement_effet;
					$this->register_gemme_enchantement($gemme);
					//my_dump($this->enchantement);
					}
					if ($this->arme_pet->effet)
					{
					$effets = split(';', $this->arme_pet->effet);
					foreach ($effets as $effet)
					{
					$d_effet = split('-', $effet);
					$this->register_item_effet($d_effet[0], $d_effet[1], $this->arme_pet);
					}
					}*/
			}
			else $this->arme_pet = false;
		}
		return $this->arme_pet;
	}

	/**
	 * Renvoie le facteur de dégâts de ou des armes.	
	 * La plupart du temps on s'en fiche, de la main, on veut les degats
	 * @param $main   si false : cumul, si 'droite' ou 'gauche' : detail
	 */
	function get_arme_degat($main = false)
	{
		$degats = 0;
		if ($main == false || $main == 'droite')
		if ($this->get_arme())
		$degats += $this->arme_pet->degat;
		return $degats;
	}

	function register_gemme_enchantement()
	{
		// TRICHE ?!
	}
	// @}

	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
	// @{
	protected $action_a;   ///< Id du script d'attaque
	protected $action_d;   ///< Id du script de défense
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
	 * Récupère le contenu du script pour une action donné
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
			$requete = "SELECT action FROM action_pet WHERE id = ".$action_id;
			$req = $db->query($requete);
			$row = $db->read_row($req);
		}
		else
		{
			if (!isset($this->monstre)) $this->get_monstre();
			$row[0] = $this->monstre->get_action();
		}
		$this->action = $row[0];
		return $this->action;
	}
	/// Renvoie le script d'action
	function get_action($attaquant)
  {
    if( $attaquant )
      return $this->recupaction('attaque');
    else
      return $this->recupaction('defense');
  }

  /// Renvoie la distance à laquelle le personnage peut attaquer
	function get_distance_tir()
	{
		global $db;
		$distance = 0;
		$joueur = new perso($this->get_id_joueur());
		$arme = $joueur->inventaire_pet()->arme_pet;
		if(!isset($this->arme_pet)) $this->get_arme();
		if($this->arme_pet)
		{
			$distance += $this->arme_pet->distance_tir;
		}
		$laisse = decompose_objet($joueur->get_inventaire_partie("cou", true));
		if($laisse['id_objet'] != '')
		{
			$requete = "SELECT distance_tir FROM objet_pet WHERE id = ".$laisse['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_row($req);
			$distance += $row[0];
		}
		return $distance;
	}
	
  /**
	 * Renvoie la RM
	 * @param  $base  true s'il faut renvoyer la valeur de base, false pour renvoyer la RM avec les bonus permanents (mais sans les buffs).
	 */
	function get_reserve_bonus()
	{
		$joueur = new perso($this->get_id_joueur());
		if(!isset($this->monstre)) $this->get_monstre();
		$this->reserve_bonus = $this->monstre->get_reserve();

		if($joueur->is_buff('buff_inspiration')) $this->reserve_bonus += $joueur->get_buff('buff_inspiration', 'effet');
		if($joueur->is_buff('buff_rune')) $this->reserve_bonus += $joueur->get_buff('buff_rune', 'effet');
		if($joueur->is_buff('buff_sacrifice')) $this->reserve_bonus += $joueur->get_buff('buff_sacrifice', 'effet');

		return $this->reserve_bonus;
	}
	// @}

	/**
	 * @name  Sorts, compétences
	 * Données et méthodes ayant trait aux sorts et compétences de combat.
	 */
	// @{
	/// Renvoie les sorts de combat.
	function get_sort_combat()
	{
		if(!isset($this->monstre)) $this->get_monstre();
		return $this->monstre->get_sort_combat();
	}
	/// Renvoie les compétences de combat.
	function get_comp_combat()
	{
		if(!isset($this->monstre)) $this->get_monstre();
		return $this->monstre->get_comp_combat();
	}

  /// Vérifie si un sort de combat est bien connu par le monstre
	function check_sort_combat_connu($id)
	{
		if(!isset($this->monstre)) $this->get_monstre();
		$connus = explode(';', $this->monstre->get_sort_combat());
		if (!in_array($id, $connus))
		security_block(URL_MANIPULATION);
	}

  /// Vérifie si une compétence de combat est bien connu par le monstre
	function check_comp_combat_connu($id)
	{
		if(!isset($this->monstre)) $this->get_monstre();
		$connus = explode(';', $this->monstre->get_comp_combat());
		if (!in_array($id, $connus))
		security_block(URL_MANIPULATION);
	}

	/// Retourne le niveau du monstre
	function get_level()
	{
		if ($this->level == 0)
		{ // pets invoqués : level perso
			global $joueur;
			if ($this->id_joueur == $joueur->get_id())
			{
				$this->level = $joueur->get_level();
			}
			else
			{
				$perso = new perso($this->id_joueur);
				$this->level = $perso->get_level();
			}
		}
		return $this->level;
	}
	// @}
	/// Renvoie le coefficient pour modifier les compétences
  function get_coeff_comp($perso)
  {
    return 1 + ($perso->get_dressage()-400)/1000;
  }
}
?>
