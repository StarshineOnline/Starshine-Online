<?php
class pet extends map_monstre
{
/**
    * @access private
    * @var int(10)
    */
	private $id;

	/**
    * @access private
    * @var int(10)
    */
	private $id_joueur;

	/**
    * @access private
    * @var int(10)
    */
	private $id_monstre;

	/**
    * @access private
    * @var varchar(100)
    */
	private $nom;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $hp;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $principale;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $ecurie;
	
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
	function __construct($id = 0, $id_joueur = 0, $id_monstre = 0, $nom = '', $hp = '', $mp = '', $principale = '', $ecurie = '', $action_a = '', $action_d = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT id_joueur, id_monstre, nom, hp, mp, principale, ecurie, action_a, action_d FROM pet WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_joueur, $this->id_monstre, $this->nom, $this->hp, $this->mp, $this->principale, $this->ecurie, $this->action_a, $this->action_d) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->id_joueur = $id['id_joueur'];
			$this->id_monstre = $id['id_monstre'];
			$this->nom = $id['nom'];
			$this->hp = $id['hp'];
			$this->mp = $id['mp'];
			$this->principale = $id['principale'];
			$this->ecurie = $id['ecurie'];
			$this->action_a = $id['action_a'];
			$this->action_d = $id['action_d'];
			}
		else
		{
			$this->id_joueur = $id_joueur;
			$this->id_monstre = $id_monstre;
			$this->nom = $nom;
			$this->hp = $hp;
			$this->mp = $mp;
			$this->principale = $principale;
			$this->ecurie = $ecurie;
			$this->id = $id;
			$this->action_a = $action_a;
			$this->action_d = $action_d;
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
				if($force) $champs = 'id_joueur = '.$this->id_joueur.', id_monstre = '.$this->id_monstre.', nom = "'.mysql_escape_string($this->nom).'", hp = "'.mysql_escape_string($this->hp).'", mp = "'.mysql_escape_string($this->mp).'", principale = "'.mysql_escape_string($this->principale).'", ecurie = "'.mysql_escape_string($this->ecurie).'", action_a = "'.mysql_escape_string($this->action_a).'", action_d = "'.mysql_escape_string($this->action_d).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE pet SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO pet (id_joueur, id_monstre, nom, hp, mp, principale, ecurie, action_a, action_d) VALUES(';
			$requete .= ''.$this->id_joueur.', '.$this->id_monstre.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->principale).'", "'.mysql_escape_string($this->ecurie).'", "'.mysql_escape_string($this->action_a).'", "'.mysql_escape_string($this->action_d).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
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
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM pet WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Crée un tableau d'objets respectant certains critères
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

		$requete = "SELECT id, id_joueur, id_monstre, nom, hp, mp, principale, ecurie, action_a, action_d FROM pet WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new pet($row);
				else $return[$row[$keys]] = new pet($row);
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
		return 'id = '.$this->id.', id_joueur = '.$this->id_joueur.', id_monstre = '.$this->id_monstre.', nom = '.$this->nom.', hp = '.$this->hp.', mp = '.$this->mp.', principale = '.$this->principale.', ecurie = '.$this->ecurie;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_joueur valeur de l'attribut id_joueur
	*/
	function get_id_joueur()
	{
		return $this->id_joueur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $id_monstre valeur de l'attribut id_monstre
	*/
	function get_id_monstre()
	{
		return $this->id_monstre;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(100) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $mp valeur de l'attribut mp
	*/
	function get_mp()
	{
		return $this->mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $principale valeur de l'attribut principale
	*/
	function get_principale()
	{
		return $this->principale;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $principale valeur de l'attribut ecurie
	*/
	function get_ecurie()
	{
		return $this->ecurie;
	}
	
	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id valeur de l'attribut
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
	* @param int(10) $id_joueur valeur de l'attribut
	* @return none
	*/
	function set_id_joueur($id_joueur)
	{
		$this->id_joueur = $id_joueur;
		$this->champs_modif[] = 'id_joueur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $id_monstre valeur de l'attribut
	* @return none
	*/
	function set_id_monstre($id_monstre)
	{
		$this->id_monstre = $id_monstre;
		$this->champs_modif[] = 'id_monstre';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(100) $nom valeur de l'attribut
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
	* @param mediumint(8) $hp valeur de l'attribut
	* @return none
	*/
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $mp valeur de l'attribut
	* @return none
	*/
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $principale valeur de l'attribut
	* @return none
	*/
	function set_principale($principale)
	{
		$this->principale = $principale;
		$this->champs_modif[] = 'principale';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $ecurie valeur de l'attribut
	* @return none
	*/
	function set_ecurie($ecurie)
	{
		$this->ecurie = $ecurie;
		$this->champs_modif[] = 'ecurie';
	}

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
		return pow($this->monstre->get_level(), 2);
	}

	function get_cout_rez()
	{
		$this->get_monstre();
		return pow($this->monstre->get_level(), 2) * 20;
	}

	function get_cout_depot()
	{
		$this->get_monstre();
		return pow($this->monstre->get_level(), 2);
	}
	
	public $pp_base;
	public $pm_base;
	public $enchant;
	public $armure;
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
	
	function get_pm($base = false)
	{
		if(!isset($this->pm))
		{
			$this->get_armure();
		}
		if(!$base) return $this->pm;
		else return $this->pm_base;
	}

	function get_pp($base = false)
	{
		if(!isset($this->pp))
		{
			$this->get_armure();
		}
		if(!$base) return $this->pp;
		else return $this->pp_base;
	}
	
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
	
	public $reserve_bonus;
	function get_reserve_bonus()
	{
		$joueur = new perso($this->get_id_joueur());
		if(!isset($this->monstre)) $this->get_monstre();
		$this->reserve_bonus = $this->monstre->get_reserve();
		
		if($joueur->is_buff('buff_inspiration')) $this->reserve_bonus += $joueur->get_buff('buff_inspiration', 'effet');
		if($joueur->is_buff('buff_sacrifice')) $this->reserve_bonus += $joueur->get_buff('buff_sacrifice', 'effet');

		return $this->reserve_bonus;
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
	
	/**
	 * @name Combats
	 * Données et méthodes liées aux combats.
	 */
  // @{
	private $action_a;   ///< Id du script d'attaque
	private $action_d;   ///< Id du script de défense
	public $action_do;

	// Renvoie l'id du script d'attaque.
	function get_action_a()
	{
		return $this->action_a;
	}
	/**
	 * Modifie le script d'attaque.
	 * @param  $action_a     Id du nouveau script d'attaque.
	 */	 
	function set_action_a($action_a)
	{
		$this->action_a = $action_a;
		$this->champs_modif[] = 'action_a';
	}

	// Renvoie l'id du script d'attaque.
	function get_action_d()
	{
		return $this->action_d;
	}
	
	// Renvoie l'id du script de défense.
	/**
	 * Modifie le script de défense.
	 * @param  $action_d     Id du nouveau script de défense.
	 */	 
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
	
	function check_sort_combat_connu($id)
	{
		if(!isset($this->monstre)) $this->get_monstre();
		$connus = explode(';', $this->monstre->get_sort_combat());
		if (!in_array($id, $connus)) 
			security_block(URL_MANIPULATION);
	}

	function check_comp_combat_connu($id)
	{
		if(!isset($this->monstre)) $this->get_monstre();
		$connus = explode(';', $this->monstre->get_comp_combat());
		if (!in_array($id, $connus)) 
			security_block(URL_MANIPULATION);
	}

  function register_gemme_enchantement()
  {
    // TRICHE ?!
  }
	// @}
}
?>
