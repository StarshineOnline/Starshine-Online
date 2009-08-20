<?php
class monstre
{
/**
    * @access private
    * @var mediumint(8)
    */
	private $id;

	/**
    * @access private
    * @var varchar(50)
    */
	private $lib;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(50)
    */
	private $type;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $hp;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $pp;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $pm;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $forcex;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $dexterite;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $puissance;

	/**
    * @access private
    * @var tinyint(4)
    */
	private $volonte;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $energie;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $melee;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $esquive;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $incantation;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $sort_vie;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $sort_mort;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $sort_element;

	/**
    * @access private
    * @var varchar(10)
    */
	private $arme;

	/**
    * @access private
    * @var text
    */
	private $action;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $level;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $xp;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $star;

	/**
    * @access private
    * @var text
    */
	private $drops;

	/**
    * @access private
    * @var int(10)
    */
	private $spawn;

	/**
    * @access private
    * @var text
    */
	private $spawn_loc;

	/**
    * @access private
    * @var varchar(50)
    */
	private $terrain;

	/**
    * @access private
    * @var enum('y','n')
    */
	private $affiche;

	/**
    * @access private
    * @var text
    */
	private $description;

	
	/**
	* @access public

	* @param mediumint(8) id attribut
	* @param varchar(50) lib attribut
	* @param varchar(50) nom attribut
	* @param varchar(50) type attribut
	* @param mediumint(9) hp attribut
	* @param mediumint(9) pp attribut
	* @param mediumint(9) pm attribut
	* @param tinyint(4) forcex attribut
	* @param tinyint(4) dexterite attribut
	* @param tinyint(4) puissance attribut
	* @param tinyint(4) volonte attribut
	* @param tinyint(3) energie attribut
	* @param mediumint(9) melee attribut
	* @param mediumint(9) esquive attribut
	* @param mediumint(8) incantation attribut
	* @param mediumint(8) sort_vie attribut
	* @param mediumint(8) sort_mort attribut
	* @param mediumint(8) sort_element attribut
	* @param varchar(10) arme attribut
	* @param text action attribut
	* @param tinyint(3) level attribut
	* @param mediumint(9) xp attribut
	* @param mediumint(9) star attribut
	* @param text drops attribut
	* @param int(10) spawn attribut
	* @param text spawn_loc attribut
	* @param varchar(50) terrain attribut
	* @param enum('y','n') affiche attribut
	* @param text description attribut
	* @return none
	*/
	function __construct($id = 0, $lib = '', $nom = '', $type = '', $hp = '', $pp = '', $pm = '', $forcex = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $melee = '', $esquive = '', $incantation = '', $sort_vie = '', $sort_mort = '', $sort_element = '', $arme = '', $action = '', $level = '', $xp = '', $star = '', $drops = '', $spawn = '', $spawn_loc = '', $terrain = '', $affiche = '', $description = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT lib, nom, type, hp, pp, pm, forcex, dexterite, puissance, volonte, energie, melee, esquive, incantation, sort_vie, sort_mort, sort_element, arme, action, level, xp, star, drops, spawn, spawn_loc, terrain, affiche, description FROM monstre WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->lib, $this->nom, $this->type, $this->hp, $this->pp, $this->pm, $this->forcex, $this->dexterite, $this->puissance, $this->volonte, $this->energie, $this->melee, $this->esquive, $this->incantation, $this->sort_vie, $this->sort_mort, $this->sort_element, $this->arme, $this->action, $this->level, $this->xp, $this->star, $this->drops, $this->spawn, $this->spawn_loc, $this->terrain, $this->affiche, $this->description) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->lib = $id['lib'];
			$this->nom = $id['nom'];
			$this->type = $id['type'];
			$this->hp = $id['hp'];
			$this->pp = $id['pp'];
			$this->pm = $id['pm'];
			$this->forcex = $id['forcex'];
			$this->dexterite = $id['dexterite'];
			$this->puissance = $id['puissance'];
			$this->volonte = $id['volonte'];
			$this->energie = $id['energie'];
			$this->melee = $id['melee'];
			$this->esquive = $id['esquive'];
			$this->incantation = $id['incantation'];
			$this->sort_vie = $id['sort_vie'];
			$this->sort_mort = $id['sort_mort'];
			$this->sort_element = $id['sort_element'];
			$this->arme = $id['arme'];
			$this->action = $id['action'];
			$this->level = $id['level'];
			$this->xp = $id['xp'];
			$this->star = $id['star'];
			$this->drops = $id['drops'];
			$this->spawn = $id['spawn'];
			$this->spawn_loc = $id['spawn_loc'];
			$this->terrain = $id['terrain'];
			$this->affiche = $id['affiche'];
			$this->description = $id['description'];
			}
		else
		{
			$this->lib = $lib;
			$this->nom = $nom;
			$this->type = $type;
			$this->hp = $hp;
			$this->pp = $pp;
			$this->pm = $pm;
			$this->forcex = $forcex;
			$this->dexterite = $dexterite;
			$this->puissance = $puissance;
			$this->volonte = $volonte;
			$this->energie = $energie;
			$this->melee = $melee;
			$this->esquive = $esquive;
			$this->incantation = $incantation;
			$this->sort_vie = $sort_vie;
			$this->sort_mort = $sort_mort;
			$this->sort_element = $sort_element;
			$this->arme = $arme;
			$this->action = $action;
			$this->level = $level;
			$this->xp = $xp;
			$this->star = $star;
			$this->drops = $drops;
			$this->spawn = $spawn;
			$this->spawn_loc = $spawn_loc;
			$this->terrain = $terrain;
			$this->affiche = $affiche;
			$this->description = $description;
			$this->id = $id;
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
				if($force) $champs = 'lib = "'.mysql_escape_string($this->lib).'", nom = "'.mysql_escape_string($this->nom).'", type = "'.mysql_escape_string($this->type).'", hp = "'.mysql_escape_string($this->hp).'", pp = "'.mysql_escape_string($this->pp).'", pm = "'.mysql_escape_string($this->pm).'", forcex = "'.mysql_escape_string($this->forcex).'", dexterite = "'.mysql_escape_string($this->dexterite).'", puissance = "'.mysql_escape_string($this->puissance).'", volonte = "'.mysql_escape_string($this->volonte).'", energie = "'.mysql_escape_string($this->energie).'", melee = "'.mysql_escape_string($this->melee).'", esquive = "'.mysql_escape_string($this->esquive).'", incantation = "'.mysql_escape_string($this->incantation).'", sort_vie = "'.mysql_escape_string($this->sort_vie).'", sort_mort = "'.mysql_escape_string($this->sort_mort).'", sort_element = "'.mysql_escape_string($this->sort_element).'", arme = "'.mysql_escape_string($this->arme).'", action = "'.mysql_escape_string($this->action).'", level = "'.mysql_escape_string($this->level).'", xp = "'.mysql_escape_string($this->xp).'", star = "'.mysql_escape_string($this->star).'", drops = "'.mysql_escape_string($this->drops).'", spawn = "'.mysql_escape_string($this->spawn).'", spawn_loc = "'.mysql_escape_string($this->spawn_loc).'", terrain = "'.mysql_escape_string($this->terrain).'", affiche = "'.mysql_escape_string($this->affiche).'", description = "'.mysql_escape_string($this->description).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE monstre SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO monstre (lib, nom, type, hp, pp, pm, forcex, dexterite, puissance, volonte, energie, melee, esquive, incantation, sort_vie, sort_mort, sort_element, arme, action, level, xp, star, drops, spawn, spawn_loc, terrain, affiche, description) VALUES(';
			$requete .= '"'.mysql_escape_string($this->lib).'", "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->type).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->pp).'", "'.mysql_escape_string($this->pm).'", "'.mysql_escape_string($this->forcex).'", "'.mysql_escape_string($this->dexterite).'", "'.mysql_escape_string($this->puissance).'", "'.mysql_escape_string($this->volonte).'", "'.mysql_escape_string($this->energie).'", "'.mysql_escape_string($this->melee).'", "'.mysql_escape_string($this->esquive).'", "'.mysql_escape_string($this->incantation).'", "'.mysql_escape_string($this->sort_vie).'", "'.mysql_escape_string($this->sort_mort).'", "'.mysql_escape_string($this->sort_element).'", "'.mysql_escape_string($this->arme).'", "'.mysql_escape_string($this->action).'", "'.mysql_escape_string($this->level).'", "'.mysql_escape_string($this->xp).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->drops).'", "'.mysql_escape_string($this->spawn).'", "'.mysql_escape_string($this->spawn_loc).'", "'.mysql_escape_string($this->terrain).'", "'.mysql_escape_string($this->affiche).'", "'.mysql_escape_string($this->description).'")';
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
			$requete = 'DELETE FROM monstre WHERE id = '.$this->id;
			$db->query($requete);
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

		$requete = "SELECT id, lib, nom, type, hp, pp, pm, forcex, dexterite, puissance, volonte, energie, melee, esquive, incantation, sort_vie, sort_mort, sort_element, arme, action, level, xp, star, drops, spawn, spawn_loc, terrain, affiche, description FROM monstre WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows($req) > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new monstre($row);
				else $return[$row[$keys]][] = new monstre($row);
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
		return 'id = '.$this->id.', lib = '.$this->lib.', nom = '.$this->nom.', type = '.$this->type.', hp = '.$this->hp.', pp = '.$this->pp.', pm = '.$this->pm.', forcex = '.$this->forcex.', dexterite = '.$this->dexterite.', puissance = '.$this->puissance.', volonte = '.$this->volonte.', energie = '.$this->energie.', melee = '.$this->melee.', esquive = '.$this->esquive.', incantation = '.$this->incantation.', sort_vie = '.$this->sort_vie.', sort_mort = '.$this->sort_mort.', sort_element = '.$this->sort_element.', arme = '.$this->arme.', action = '.$this->action.', level = '.$this->level.', xp = '.$this->xp.', star = '.$this->star.', drops = '.$this->drops.', spawn = '.$this->spawn.', spawn_loc = '.$this->spawn_loc.', terrain = '.$this->terrain.', affiche = '.$this->affiche.', description = '.$this->description;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $lib valeur de l'attribut lib
	*/
	function get_lib()
	{
		return $this->lib;
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
	* @return varchar(50) $type valeur de l'attribut type
	*/
	function get_type()
	{
		return $this->type;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $pp valeur de l'attribut pp
	*/
	function get_pp()
	{
		return $this->pp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $pm valeur de l'attribut pm
	*/
	function get_pm()
	{
		return $this->pm;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $forcex valeur de l'attribut forcex
	*/
	function get_forcex()
	{
		return $this->forcex;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $dexterite valeur de l'attribut dexterite
	*/
	function get_dexterite()
	{
		return $this->dexterite;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $puissance valeur de l'attribut puissance
	*/
	function get_puissance()
	{
		return $this->puissance;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(4) $volonte valeur de l'attribut volonte
	*/
	function get_volonte()
	{
		return $this->volonte;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $energie valeur de l'attribut energie
	*/
	function get_energie()
	{
		return $this->energie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $melee valeur de l'attribut melee
	*/
	function get_melee()
	{
		return $this->melee;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $esquive valeur de l'attribut esquive
	*/
	function get_esquive()
	{
		return $this->esquive;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $incantation valeur de l'attribut incantation
	*/
	function get_incantation()
	{
		return $this->incantation;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $sort_vie valeur de l'attribut sort_vie
	*/
	function get_sort_vie()
	{
		return $this->sort_vie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $sort_mort valeur de l'attribut sort_mort
	*/
	function get_sort_mort()
	{
		return $this->sort_mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $sort_element valeur de l'attribut sort_element
	*/
	function get_sort_element()
	{
		return $this->sort_element;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(10) $arme valeur de l'attribut arme
	*/
	function get_arme()
	{
		return $this->arme;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $action valeur de l'attribut action
	*/
	function get_action()
	{
		return $this->action;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $level valeur de l'attribut level
	*/
	function get_level()
	{
		return $this->level;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $xp valeur de l'attribut xp
	*/
	function get_xp()
	{
		return $this->xp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $star valeur de l'attribut star
	*/
	function get_star()
	{
		return $this->star;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $drops valeur de l'attribut drops
	*/
	function get_drops()
	{
		return $this->drops;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $spawn valeur de l'attribut spawn
	*/
	function get_spawn()
	{
		return $this->spawn;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $spawn_loc valeur de l'attribut spawn_loc
	*/
	function get_spawn_loc()
	{
		return $this->spawn_loc;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $terrain valeur de l'attribut terrain
	*/
	function get_terrain()
	{
		return $this->terrain;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('y','n') $affiche valeur de l'attribut affiche
	*/
	function get_affiche()
	{
		return $this->affiche;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $description valeur de l'attribut description
	*/
	function get_description()
	{
		return $this->description;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $id valeur de l'attribut
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
	* @param varchar(50) $lib valeur de l'attribut
	* @return none
	*/
	function set_lib($lib)
	{
		$this->lib = $lib;
		$this->champs_modif[] = 'lib';
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
	* @param varchar(50) $type valeur de l'attribut
	* @return none
	*/
	function set_type($type)
	{
		$this->type = $type;
		$this->champs_modif[] = 'type';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $hp valeur de l'attribut
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
	* @param mediumint(9) $pp valeur de l'attribut
	* @return none
	*/
	function set_pp($pp)
	{
		$this->pp = $pp;
		$this->champs_modif[] = 'pp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $pm valeur de l'attribut
	* @return none
	*/
	function set_pm($pm)
	{
		$this->pm = $pm;
		$this->champs_modif[] = 'pm';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $forcex valeur de l'attribut
	* @return none
	*/
	function set_forcex($forcex)
	{
		$this->forcex = $forcex;
		$this->champs_modif[] = 'forcex';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $dexterite valeur de l'attribut
	* @return none
	*/
	function set_dexterite($dexterite)
	{
		$this->dexterite = $dexterite;
		$this->champs_modif[] = 'dexterite';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $puissance valeur de l'attribut
	* @return none
	*/
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(4) $volonte valeur de l'attribut
	* @return none
	*/
	function set_volonte($volonte)
	{
		$this->volonte = $volonte;
		$this->champs_modif[] = 'volonte';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $energie valeur de l'attribut
	* @return none
	*/
	function set_energie($energie)
	{
		$this->energie = $energie;
		$this->champs_modif[] = 'energie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $melee valeur de l'attribut
	* @return none
	*/
	function set_melee($melee)
	{
		$this->melee = $melee;
		$this->champs_modif[] = 'melee';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $esquive valeur de l'attribut
	* @return none
	*/
	function set_esquive($esquive)
	{
		$this->esquive = $esquive;
		$this->champs_modif[] = 'esquive';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $incantation valeur de l'attribut
	* @return none
	*/
	function set_incantation($incantation)
	{
		$this->incantation = $incantation;
		$this->champs_modif[] = 'incantation';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $sort_vie valeur de l'attribut
	* @return none
	*/
	function set_sort_vie($sort_vie)
	{
		$this->sort_vie = $sort_vie;
		$this->champs_modif[] = 'sort_vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $sort_mort valeur de l'attribut
	* @return none
	*/
	function set_sort_mort($sort_mort)
	{
		$this->sort_mort = $sort_mort;
		$this->champs_modif[] = 'sort_mort';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $sort_element valeur de l'attribut
	* @return none
	*/
	function set_sort_element($sort_element)
	{
		$this->sort_element = $sort_element;
		$this->champs_modif[] = 'sort_element';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(10) $arme valeur de l'attribut
	* @return none
	*/
	function set_arme($arme)
	{
		$this->arme = $arme;
		$this->champs_modif[] = 'arme';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $action valeur de l'attribut
	* @return none
	*/
	function set_action($action)
	{
		$this->action = $action;
		$this->champs_modif[] = 'action';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $level valeur de l'attribut
	* @return none
	*/
	function set_level($level)
	{
		$this->level = $level;
		$this->champs_modif[] = 'level';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $xp valeur de l'attribut
	* @return none
	*/
	function set_xp($xp)
	{
		$this->xp = $xp;
		$this->champs_modif[] = 'xp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $star valeur de l'attribut
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
	* @param text $drops valeur de l'attribut
	* @return none
	*/
	function set_drops($drops)
	{
		$this->drops = $drops;
		$this->champs_modif[] = 'drops';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $spawn valeur de l'attribut
	* @return none
	*/
	function set_spawn($spawn)
	{
		$this->spawn = $spawn;
		$this->champs_modif[] = 'spawn';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $spawn_loc valeur de l'attribut
	* @return none
	*/
	function set_spawn_loc($spawn_loc)
	{
		$this->spawn_loc = $spawn_loc;
		$this->champs_modif[] = 'spawn_loc';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $terrain valeur de l'attribut
	* @return none
	*/
	function set_terrain($terrain)
	{
		$this->terrain = $terrain;
		$this->champs_modif[] = 'terrain';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('y','n') $affiche valeur de l'attribut
	* @return none
	*/
	function set_affiche($affiche)
	{
		$this->affiche = $affiche;
		$this->champs_modif[] = 'affiche';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $description valeur de l'attribut
	* @return none
	*/
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}

		//fonction
}
?>
