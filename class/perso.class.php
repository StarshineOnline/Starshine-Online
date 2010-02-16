<?php
class perso extends entite
{
  const table = "perso";  ///< Nom de la table correspondante.
  
	/**
    * @access private
    * @var int(10)
    */
	private $mort;

	/**
    * @access private
    * @var varchar(40)
    */
	private $password;

	/**
    * @access private
    * @var email(100)
    */
	private $email;

	/**
    * @access private
    * @var int(11)
    */
	private $exp;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $honneur;

	/**
    * @access private
    * @var int(10)
    */
	private $reputation;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $forcex;

	/**
    * @access private
    * @var varchar(20)
    */
	private $classe;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $classe_id;

	/**
    * @access private
    * @var text
    */
	private $inventaire;

	/**
    * @access private
    * @var text
    */
	private $inventaire_slot;

	/**
    * @access private
    * @var int(11)
    */
	private $dernieraction;

	/**
    * @access private
    * @var int(10)
    */
	private $action_a;

	/**
    * @access private
    * @var int(10)
    */
	private $action_d;

	/**
    * @access private
    * @var text
    */
	private $sort_jeu;

	/**
    * @access private
    * @var text
    */
	private $sort_combat;

	/**
    * @access private
    * @var text
    */
	private $comp_jeu;

	/**
    * @access private
    * @var int(11)
    */
	private $star;

	/**
    * @access private
    * @var int(11)
    */
	private $groupe;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

	/**
    * @access private
    * @var float
    */
	private $mp_max;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $identification;

	/**
    * @access private
    * @var int(10)
    */
	private $craft;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $alchimie;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $architecture;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $forge;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $survie;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $dressage;

	/**
    * @access private
    * @var double
    */
	private $facteur_magie;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_vie;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_mort;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_element;

	/**
    * @access private
    * @var int(10)
    */
	private $regen_hp;

	/**
    * @access private
    * @var int(10)
    */
	private $maj_hp;

	/**
    * @access private
    * @var int(10)
    */
	private $maj_mp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $point_sso;

	/**
    * @access private
    * @var text
    */
	private $quete;

	/**
    * @access private
    * @var text
    */
	private $quete_fini;

	/**
    * @access private
    * @var int(10)
    */
	private $dernier_connexion;

	/**
    * @access private
    * @var varchar(50)
    */
	private $statut;

	/**
    * @access private
    * @var int(11)
    */
	private $fin_ban;

	/**
    * @access private
    * @var int(10)
    */
	private $frag;

	/**
    * @access private
    * @var float
    */
	private $crime;

	/**
    * @access private
    * @var int(10)
    */
	private $amende;

	/**
    * @access private
    * @var enum('true','false')
    */
	private $teleport_roi;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_classe;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_stat;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_niveau;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $max_pet;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $beta;

	
	public $poscase;
	public $pospita;
	public $share_xp;

	/**
	* @access public

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
	function __construct($id = 0, $mort = 0, $nom = '', $password = '', $email = '', $exp = 0, $honneur = 0, $reputation = 0, $level = '', $rang_royaume = '', $vie = '', $forcex = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $race = '', $classe = '', $classe_id = '', $inventaire = '', $inventaire_slot = '', $pa = '', $dernieraction = '', $action_a = 0, $action_d = 0, $sort_jeu = '', $sort_combat = '', $comp_combat = '', $comp_jeu = '', $star = '', $x = '', $y = '', $groupe = 0, $hp = '', $hp_max = '', $mp = '', $mp_max = '', $melee = '', $distance = '', $esquive = '', $blocage = '', $incantation = '', $sort_vie = '', $sort_element = '', $sort_mort = '', $identification = '', $craft = '', $alchimie = '', $architecture = '', $forge = '', $survie = '', $dressage = '', $facteur_magie = '', $facteur_sort_vie = 0, $facteur_sort_mort = 0, $facteur_sort_element = 0, $regen_hp = '', $maj_hp = '', $maj_mp = '', $point_sso = 0, $quete = '', $quete_fini = '', $dernier_connexion = 0, $statut = '', $fin_ban = 0, $frag = 0, $crime = 0, $amende = 0, $teleport_roi = 'false', $cache_classe = 0, $cache_stat = 0, $cache_niveau = 0, $max_pet = 0, $beta = 0)
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT mort, nom, password, email, exp, honneur, reputation, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, dressage, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, max_pet, beta FROM perso WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->mort, $this->nom, $this->password, $this->email, $this->exp, $this->honneur, $this->reputation, $this->level, $this->rang_royaume, $this->vie, $this->forcex, $this->dexterite, $this->puissance, $this->volonte, $this->energie, $this->race, $this->classe, $this->classe_id, $this->inventaire, $this->inventaire_slot, $this->pa, $this->dernieraction, $this->action_a, $this->action_d, $this->sort_jeu, $this->sort_combat, $this->comp_combat, $this->comp_jeu, $this->star, $this->x, $this->y, $this->groupe, $this->hp, $this->hp_max, $this->mp, $this->mp_max, $this->melee, $this->distance, $this->esquive, $this->blocage, $this->incantation, $this->sort_vie, $this->sort_element, $this->sort_mort, $this->identification, $this->craft, $this->alchimie, $this->architecture, $this->forge, $this->survie, $this->dressage, $this->facteur_magie, $this->facteur_sort_vie, $this->facteur_sort_mort, $this->facteur_sort_element, $this->regen_hp, $this->maj_hp, $this->maj_mp, $this->point_sso, $this->quete, $this->quete_fini, $this->dernier_connexion, $this->statut, $this->fin_ban, $this->frag, $this->crime, $this->amende, $this->teleport_roi, $this->cache_classe, $this->cache_stat, $this->cache_niveau, $this->max_pet, $this->beta) = $db->read_array($requeteSQL);
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
			$this->inventaire_slot = $inventaire_slot;
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
			$this->id = $id;
		}

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
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'mort = '.$this->mort.', nom = "'.mysql_escape_string($this->nom).'", password = "'.mysql_escape_string($this->password).'", email = "'.mysql_escape_string($this->email).'", exp = "'.mysql_escape_string($this->exp).'", honneur = "'.mysql_escape_string($this->honneur).'", reputation = "'.mysql_escape_string($this->reputation).'", level = "'.mysql_escape_string($this->level).'", rang_royaume = "'.mysql_escape_string($this->rang_royaume).'", vie = "'.mysql_escape_string($this->vie).'", forcex = "'.mysql_escape_string($this->forcex).'", dexterite = "'.mysql_escape_string($this->dexterite).'", puissance = "'.mysql_escape_string($this->puissance).'", volonte = "'.mysql_escape_string($this->volonte).'", energie = "'.mysql_escape_string($this->energie).'", race = "'.mysql_escape_string($this->race).'", classe = "'.mysql_escape_string($this->classe).'", classe_id = "'.mysql_escape_string($this->classe_id).'", inventaire = "'.mysql_escape_string($this->inventaire).'", inventaire_slot = "'.mysql_escape_string($this->inventaire_slot).'", pa = "'.mysql_escape_string($this->pa).'", dernieraction = "'.mysql_escape_string($this->dernieraction).'", action_a = "'.mysql_escape_string($this->action_a).'", action_d = "'.mysql_escape_string($this->action_d).'", sort_jeu = "'.mysql_escape_string($this->sort_jeu).'", sort_combat = "'.mysql_escape_string($this->sort_combat).'", comp_combat = "'.mysql_escape_string($this->comp_combat).'", comp_jeu = "'.mysql_escape_string($this->comp_jeu).'", star = "'.mysql_escape_string($this->star).'", x = "'.mysql_escape_string($this->x).'", y = "'.mysql_escape_string($this->y).'", groupe = "'.mysql_escape_string($this->groupe).'", hp = "'.mysql_escape_string($this->hp).'", hp_max = "'.mysql_escape_string($this->hp_max).'", mp = "'.mysql_escape_string($this->mp).'", mp_max = "'.mysql_escape_string($this->mp_max).'", melee = "'.mysql_escape_string($this->melee).'", distance = "'.mysql_escape_string($this->distance).'", esquive = "'.mysql_escape_string($this->esquive).'", blocage = "'.mysql_escape_string($this->blocage).'", incantation = "'.mysql_escape_string($this->incantation).'", sort_vie = "'.mysql_escape_string($this->sort_vie).'", sort_element = "'.mysql_escape_string($this->sort_element).'", sort_mort = "'.mysql_escape_string($this->sort_mort).'", identification = "'.mysql_escape_string($this->identification).'", craft = "'.mysql_escape_string($this->craft).'", alchimie = "'.mysql_escape_string($this->alchimie).'", architecture = "'.mysql_escape_string($this->architecture).'", forge = "'.mysql_escape_string($this->forge).'", survie = "'.mysql_escape_string($this->survie).'", dressage = "'.mysql_escape_string($this->dressage).'", facteur_magie = "'.mysql_escape_string($this->facteur_magie).'", facteur_sort_vie = "'.mysql_escape_string($this->facteur_sort_vie).'", facteur_sort_mort = "'.mysql_escape_string($this->facteur_sort_mort).'", facteur_sort_element = "'.mysql_escape_string($this->facteur_sort_element).'", regen_hp = "'.mysql_escape_string($this->regen_hp).'", maj_hp = "'.mysql_escape_string($this->maj_hp).'", maj_mp = "'.mysql_escape_string($this->maj_mp).'", point_sso = "'.mysql_escape_string($this->point_sso).'", quete = "'.mysql_escape_string($this->quete).'", quete_fini = "'.mysql_escape_string($this->quete_fini).'", dernier_connexion = "'.mysql_escape_string($this->dernier_connexion).'", statut = "'.mysql_escape_string($this->statut).'", fin_ban = "'.mysql_escape_string($this->fin_ban).'", frag = "'.mysql_escape_string($this->frag).'", crime = "'.mysql_escape_string($this->crime).'", amende = "'.mysql_escape_string($this->amende).'", teleport_roi = "'.mysql_escape_string($this->teleport_roi).'", cache_classe = "'.mysql_escape_string($this->cache_classe).'", cache_stat = "'.mysql_escape_string($this->cache_stat).'", cache_niveau = "'.mysql_escape_string($this->cache_niveau).'", max_pet = "'.mysql_escape_string($this->max_pet).'", beta = "'.mysql_escape_string($this->beta).'"';
				else
				{
					$champs = '';
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
			$requete = 'INSERT INTO perso (mort, nom, password, email, exp, honneur, reputation, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, dressage, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, max_pet, beta) VALUES(';
			$requete .= ''.$this->mort.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->password).'", "'.mysql_escape_string($this->email).'", "'.mysql_escape_string($this->exp).'", "'.mysql_escape_string($this->honneur).'", "'.mysql_escape_string($this->reputation).'", "'.mysql_escape_string($this->level).'", "'.mysql_escape_string($this->rang_royaume).'", "'.mysql_escape_string($this->vie).'", "'.mysql_escape_string($this->forcex).'", "'.mysql_escape_string($this->dexterite).'", "'.mysql_escape_string($this->puissance).'", "'.mysql_escape_string($this->volonte).'", "'.mysql_escape_string($this->energie).'", "'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->classe).'", "'.mysql_escape_string($this->classe_id).'", "'.mysql_escape_string($this->inventaire).'", "'.mysql_escape_string($this->inventaire_slot).'", "'.mysql_escape_string($this->pa).'", "'.mysql_escape_string($this->dernieraction).'", "'.mysql_escape_string($this->action_a).'", "'.mysql_escape_string($this->action_d).'", "'.mysql_escape_string($this->sort_jeu).'", "'.mysql_escape_string($this->sort_combat).'", "'.mysql_escape_string($this->comp_combat).'", "'.mysql_escape_string($this->comp_jeu).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->x).'", "'.mysql_escape_string($this->y).'", "'.mysql_escape_string($this->groupe).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->hp_max).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->mp_max).'", "'.mysql_escape_string($this->melee).'", "'.mysql_escape_string($this->distance).'", "'.mysql_escape_string($this->esquive).'", "'.mysql_escape_string($this->blocage).'", "'.mysql_escape_string($this->incantation).'", "'.mysql_escape_string($this->sort_vie).'", "'.mysql_escape_string($this->sort_element).'", "'.mysql_escape_string($this->sort_mort).'", "'.mysql_escape_string($this->identification).'", "'.mysql_escape_string($this->craft).'", "'.mysql_escape_string($this->alchimie).'", "'.mysql_escape_string($this->architecture).'", "'.mysql_escape_string($this->forge).'", "'.mysql_escape_string($this->survie).'", "'.mysql_escape_string($this->dressage).'", "'.mysql_escape_string($this->facteur_magie).'", "'.mysql_escape_string($this->facteur_sort_vie).'", "'.mysql_escape_string($this->facteur_sort_mort).'", "'.mysql_escape_string($this->facteur_sort_element).'", "'.mysql_escape_string($this->regen_hp).'", "'.mysql_escape_string($this->maj_hp).'", "'.mysql_escape_string($this->maj_mp).'", "'.mysql_escape_string($this->point_sso).'", "'.mysql_escape_string($this->quete).'", "'.mysql_escape_string($this->quete_fini).'", "'.mysql_escape_string($this->dernier_connexion).'", "'.mysql_escape_string($this->statut).'", "'.mysql_escape_string($this->fin_ban).'", "'.mysql_escape_string($this->frag).'", "'.mysql_escape_string($this->crime).'", "'.mysql_escape_string($this->amende).'", "'.mysql_escape_string($this->teleport_roi).'", "'.mysql_escape_string($this->cache_classe).'", "'.mysql_escape_string($this->cache_stat).'", "'.mysql_escape_string($this->cache_niveau).'", "'.mysql_escape_string($this->max_pet).'", "'.mysql_escape_string($this->beta).'")';
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

		$requete = "SELECT id, mort, nom, password, email, exp, honneur, reputation, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, dressage, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, max_pet, beta FROM perso WHERE ".$where." ORDER BY ".$ordre;
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
		return 'id = '.$this->id.', mort = '.$this->mort.', nom = '.$this->nom.', password = '.$this->password.', email = '.$this->email.', exp = '.$this->exp.', honneur = '.$this->honneur.', reputation = '.$this->reputation.', level = '.$this->level.', rang_royaume = '.$this->rang_royaume.', vie = '.$this->vie.', forcex = '.$this->forcex.', dexterite = '.$this->dexterite.', puissance = '.$this->puissance.', volonte = '.$this->volonte.', energie = '.$this->energie.', race = '.$this->race.', classe = '.$this->classe.', classe_id = '.$this->classe_id.', inventaire = '.$this->inventaire.', inventaire_slot = '.$this->inventaire_slot.', pa = '.$this->pa.', dernieraction = '.$this->dernieraction.', action_a = '.$this->action_a.', action_d = '.$this->action_d.', sort_jeu = '.$this->sort_jeu.', sort_combat = '.$this->sort_combat.', comp_combat = '.$this->comp_combat.', comp_jeu = '.$this->comp_jeu.', star = '.$this->star.', x = '.$this->x.', y = '.$this->y.', groupe = '.$this->groupe.', hp = '.$this->hp.', hp_max = '.$this->hp_max.', mp = '.$this->mp.', mp_max = '.$this->mp_max.', melee = '.$this->melee.', distance = '.$this->distance.', esquive = '.$this->esquive.', blocage = '.$this->blocage.', incantation = '.$this->incantation.', sort_vie = '.$this->sort_vie.', sort_element = '.$this->sort_element.', sort_mort = '.$this->sort_mort.', identification = '.$this->identification.', craft = '.$this->craft.', alchimie = '.$this->alchimie.', architecture = '.$this->architecture.', forge = '.$this->forge.', survie = '.$this->survie.', dressage = '.$this->dressage.', facteur_magie = '.$this->facteur_magie.', facteur_sort_vie = '.$this->facteur_sort_vie.', facteur_sort_mort = '.$this->facteur_sort_mort.', facteur_sort_element = '.$this->facteur_sort_element.', regen_hp = '.$this->regen_hp.', maj_hp = '.$this->maj_hp.', maj_mp = '.$this->maj_mp.', point_sso = '.$this->point_sso.', quete = '.$this->quete.', quete_fini = '.$this->quete_fini.', dernier_connexion = '.$this->dernier_connexion.', statut = '.$this->statut.', fin_ban = '.$this->fin_ban.', frag = '.$this->frag.', crime = '.$this->crime.', amende = '.$this->amende.', teleport_roi = '.$this->teleport_roi.', cache_classe = '.$this->cache_classe.', cache_stat = '.$this->cache_stat.', cache_niveau = '.$this->cache_niveau.', max_pet = '.$this->max_pet.', beta = '.$this->beta;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $mort valeur de l'attribut mort
	*/
	function get_mort()
	{
		return $this->mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(40) $password valeur de l'attribut password
	*/
	function get_password()
	{
		return $this->password;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(100) $email valeur de l'attribut email
	*/
	function get_email()
	{
		return $this->email;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $exp valeur de l'attribut exp
	*/
	function get_exp()
	{
		return $this->exp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $honneur valeur de l'attribut honneur
	*/
	function get_honneur()
	{
		return $this->honneur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $reputation valeur de l'attribut reputation
	*/
	function get_reputation()
	{
		return $this->reputation;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $vie valeur de l'attribut vie
	*/
	function get_vie($base = false)
	{
		if ($base)
			return $this->vie;
		else
			return $this->vie + $this->get_bonus_permanents('vie');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $forcex valeur de l'attribut forcex
	*/
	function get_forcex($base = false)
	{
		if ($base)
			return $this->forcex;
		else
			return $this->forcex + $this->get_bonus_permanents('forcex');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $dexterite valeur de l'attribut dexterite
	*/
	function get_dexterite($base = false)
	{
		if ($base)
			return $this->dexterite;
		else
			return $this->dexterite + $this->get_bonus_permanents('dexterite');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $puissance valeur de l'attribut puissance
	*/
	function get_puissance($base = false)
	{
		if ($base)
			return $this->puissance;
    else
      return $this->puissance + $this->get_bonus_permanents('puissance');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $volonte valeur de l'attribut volonte
	*/
	function get_volonte($base = false)
	{
		if ($base)
			return $this->volonte;
		else
			return $this->volonte + $this->get_bonus_permanents('volonte');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $energie valeur de l'attribut energie
	*/
	function get_energie($base = false)
	{
		if ($base)
			return $this->energie;
    else
      return $this->energie + $this->get_bonus_permanents('energie');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(20) $classe valeur de l'attribut classe
	*/
	function get_classe()
	{
		return $this->classe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $classe_id valeur de l'attribut classe_id
	*/
	function get_classe_id()
	{
		return $this->classe_id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $inventaire valeur de l'attribut inventaire
	*/
	function get_inventaire()
	{
		return $this->inventaire;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $inventaire_slot valeur de l'attribut inventaire_slot
	*/
	function get_inventaire_slot()
	{
		return $this->inventaire_slot;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $dernieraction valeur de l'attribut dernieraction
	*/
	function get_dernieraction()
	{
		return $this->dernieraction;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $action_a valeur de l'attribut action_a
	*/
	function get_action_a()
	{
		return $this->action_a;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $action_d valeur de l'attribut action_d
	*/
	function get_action_d()
	{
		return $this->action_d;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $sort_jeu valeur de l'attribut sort_jeu
	*/
	function get_sort_jeu()
	{
		return $this->sort_jeu;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $sort_combat valeur de l'attribut sort_combat
	*/
	function get_sort_combat()
	{
		return $this->sort_combat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $comp_jeu valeur de l'attribut comp_jeu
	*/
	function get_comp_jeu()
	{
		return $this->comp_jeu;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $star valeur de l'attribut star
	*/
	function get_star()
	{
		return $this->star;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $groupe valeur de l'attribut groupe
	*/
	function get_groupe()
	{
		return $this->groupe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return float $hp_max valeur de l'attribut hp_max
	*/
	function get_hp_max($base = false)
	{
		if ($base)
			return $this->hp_max;
		else
			return $this->hp_max + $this->get_bonus_permanents('hp_max');
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
	* @return float $mp_max valeur de l'attribut mp_max
	*/
	function get_mp_max($base = false)
	{
		if ($base)
			return $this->mp_max;
		else
			return $this->mp_max + $this->get_bonus_permanents('mp_max');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $melee valeur de l'attribut melee
	*/
	function get_melee($base = false)
	{
		if ($base)
			return $this->melee;
		else
			return $this->melee + $this->get_bonus_permanents('melee');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $distance valeur de l'attribut distance
	*/
	function get_distance($base = false)
	{
		if ($base)
			return $this->distance;
		else
			return $this->distance + $this->get_bonus_permanents('distance');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $esquive valeur de l'attribut esquive
	*/
	function get_esquive($base = false)
	{
		if ($base)
			return $this->esquive;
		else
			return $this->esquive + $this->get_bonus_permanents('esquive');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $blocage valeur de l'attribut blocage
	*/
	function get_blocage($base = false)
	{
		if ($base)
			return $this->blocage;
		else
			return $this->blocage + $this->get_bonus_permanents('blocage');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $incantation valeur de l'attribut incantation
	*/
	function get_incantation($base = false)
	{
		if ($base)
			return $this->incantation;
		else
			return $this->incantation + $this->get_bonus_permanents('incantation');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $sort_vie valeur de l'attribut sort_vie
	*/
	function get_sort_vie($base = false)
	{
		if ($base)
			return $this->sort_vie;
		else
			return $this->sort_vie + $this->get_bonus_permanents('sort_vie');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $sort_element valeur de l'attribut sort_element
	*/
	function get_sort_element($base = false)
	{
		if ($base)
			return $this->sort_element;
		else
			return $this->sort_element + $this->get_bonus_permanents('sort_element');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $sort_mort valeur de l'attribut sort_mort
	*/
	function get_sort_mort($base = false)
	{
		if ($base)
			return $this->sort_mort;
		else
			return $this->sort_mort + $this->get_bonus_permanents('sort_mort');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $identification valeur de l'attribut identification
	*/
	function get_identification($base = false)
	{
		if ($base)
			return $this->identification;
		else
			return $this->identification + $this->get_bonus_permanents('identification');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $craft valeur de l'attribut craft
	*/
	function get_craft($base = false)
	{
		if ($base)
			return $this->craft;
		else
			return $this->craft + $this->get_bonus_permanents('craft');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $alchimie valeur de l'attribut alchimie
	*/
	function get_alchimie($base = false)
	{
		if ($base)
			return $this->alchimie;
		else
			return $this->alchimie + $this->get_bonus_permanents('alchimie');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $architecture valeur de l'attribut architecture
	*/
	function get_architecture($base = false)
	{
		if ($base)
			return $this->architecture;
		else
			return $this->architecture + $this->get_bonus_permanents('architecture');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $forge valeur de l'attribut forge
	*/
	function get_forge($base = false)
	{
		if ($base)
			return $this->forge;
		else
			return $this->forge + $this->get_bonus_permanents('forge');
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $survie valeur de l'attribut survie
	*/
	function get_survie()
	{
		return $this->survie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $dressage valeur de l'attribut survie
	*/
	function get_dressage()
	{
		return $this->dressage;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_magie valeur de l'attribut facteur_magie
	*/
	function get_facteur_magie()
	{
		return $this->facteur_magie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_vie valeur de l'attribut facteur_sort_vie
	*/
	function get_facteur_sort_vie()
	{
		return $this->facteur_sort_vie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_mort valeur de l'attribut facteur_sort_mort
	*/
	function get_facteur_sort_mort()
	{
		return $this->facteur_sort_mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_element valeur de l'attribut facteur_sort_element
	*/
	function get_facteur_sort_element()
	{
		return $this->facteur_sort_element;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $regen_hp valeur de l'attribut regen_hp
	*/
	function get_regen_hp()
	{
		return $this->regen_hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $maj_hp valeur de l'attribut maj_hp
	*/
	function get_maj_hp()
	{
		return $this->maj_hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $maj_mp valeur de l'attribut maj_mp
	*/
	function get_maj_mp()
	{
		return $this->maj_mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $point_sso valeur de l'attribut point_sso
	*/
	function get_point_sso()
	{
		return $this->point_sso;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $quete valeur de l'attribut quete
	*/
	function get_quete()
	{
		return $this->quete;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $quete_fini valeur de l'attribut quete_fini
	*/
	function get_quete_fini()
	{
		return $this->quete_fini;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $dernier_connexion valeur de l'attribut dernier_connexion
	*/
	function get_dernier_connexion()
	{
		return $this->dernier_connexion;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $statut valeur de l'attribut statut
	*/
	function get_statut()
	{
		return $this->statut;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $fin_ban valeur de l'attribut fin_ban
	*/
	function get_fin_ban()
	{
		return $this->fin_ban;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $frag valeur de l'attribut frag
	*/
	function get_frag()
	{
		return $this->frag;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return float $crime valeur de l'attribut crime
	*/
	function get_crime()
	{
		return $this->crime;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $amende valeur de l'attribut amende
	*/
	function get_amende()
	{
		return $this->amende;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('true','false') $teleport_roi valeur de l'attribut teleport_roi
	*/
	function get_teleport_roi()
	{
		return $this->teleport_roi;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_classe valeur de l'attribut cache_classe
	*/
	function get_cache_classe()
	{
		return $this->cache_classe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_stat valeur de l'attribut cache_stat
	*/
	function get_cache_stat()
	{
		return $this->cache_stat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_niveau valeur de l'attribut cache_niveau
	*/
	function get_cache_niveau()
	{
		return $this->cache_niveau;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $max_pet valeur de l'attribut max_pet
	*/
	function get_max_pet()
	{
		return $this->max_pet;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $beta valeur de l'attribut beta
	*/
	function get_beta()
	{
		return $this->beta;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $mort valeur de l'attribut
	* @return none
	*/
	function set_mort($mort)
	{
		$this->mort = $mort;
		$this->champs_modif[] = 'mort';
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
	* @param varchar(40) $password valeur de l'attribut
	* @return none
	*/
	function set_password($password)
	{
		$this->password = $password;
		$this->champs_modif[] = 'password';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(100) $email valeur de l'attribut
	* @return none
	*/
	function set_email($email)
	{
		$this->email = $email;
		$this->champs_modif[] = 'email';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $exp valeur de l'attribut
	* @return none
	*/
	function set_exp($exp)
	{
		$this->exp = $exp;
		$this->champs_modif[] = 'exp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $honneur valeur de l'attribut
	* @return none
	*/
	function set_honneur($honneur)
	{
		$this->honneur = $honneur;
		$this->champs_modif[] = 'honneur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $reputation valeur de l'attribut
	* @return none
	*/
	function set_reputation($reputation)
	{
		$this->reputation = $reputation;
		$this->champs_modif[] = 'reputation';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $level valeur de l'attribut
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
	* @param mediumint(9) $rang_royaume valeur de l'attribut
	* @return none
	*/
	function set_rang_royaume($rang_royaume)
	{
		$this->rang_royaume = $rang_royaume;
		$this->champs_modif[] = 'rang_royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $vie valeur de l'attribut
	* @return none
	*/
	function set_vie($vie)
	{
		$this->vie = $vie;
		$this->champs_modif[] = 'vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $forcex valeur de l'attribut
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
	* @param mediumint(9) $dexterite valeur de l'attribut
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
	* @param mediumint(9) $puissance valeur de l'attribut
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
	* @param mediumint(9) $volonte valeur de l'attribut
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
	* @param mediumint(9) $energie valeur de l'attribut
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
	* @param varchar(20) $race valeur de l'attribut
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
	* @param varchar(20) $classe valeur de l'attribut
	* @return none
	*/
	function set_classe($classe)
	{
		$this->classe = $classe;
		$this->champs_modif[] = 'classe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $classe_id valeur de l'attribut
	* @return none
	*/
	function set_classe_id($classe_id)
	{
		$this->classe_id = $classe_id;
		$this->champs_modif[] = 'classe_id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $inventaire valeur de l'attribut
	* @return none
	*/
	function set_inventaire($inventaire)
	{
		$this->inventaire = $inventaire;
		$this->champs_modif[] = 'inventaire';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $inventaire_slot valeur de l'attribut
	* @return none
	*/
	function set_inventaire_slot($inventaire_slot)
	{
		$this->inventaire_slot = $inventaire_slot;
		$this->champs_modif[] = 'inventaire_slot';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param smallint(6) $pa valeur de l'attribut
	* @return none
	*/
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $dernieraction valeur de l'attribut
	* @return none
	*/
	function set_dernieraction($dernieraction)
	{
		$this->dernieraction = $dernieraction;
		$this->champs_modif[] = 'dernieraction';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $action_a valeur de l'attribut
	* @return none
	*/
	function set_action_a($action_a)
	{
		$this->action_a = $action_a;
		$this->champs_modif[] = 'action_a';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $action_d valeur de l'attribut
	* @return none
	*/
	function set_action_d($action_d)
	{
		$this->action_d = $action_d;
		$this->champs_modif[] = 'action_d';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $sort_jeu valeur de l'attribut
	* @return none
	*/
	function set_sort_jeu($sort_jeu)
	{
		$this->sort_jeu = $sort_jeu;
		$this->champs_modif[] = 'sort_jeu';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $sort_combat valeur de l'attribut
	* @return none
	*/
	function set_sort_combat($sort_combat)
	{
		$this->sort_combat = $sort_combat;
		$this->champs_modif[] = 'sort_combat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $comp_combat valeur de l'attribut
	* @return none
	*/
	function set_comp_combat($comp_combat)
	{
		$this->comp_combat = $comp_combat;
		$this->champs_modif[] = 'comp_combat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $comp_jeu valeur de l'attribut
	* @return none
	*/
	function set_comp_jeu($comp_jeu)
	{
		$this->comp_jeu = $comp_jeu;
		$this->champs_modif[] = 'comp_jeu';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $star valeur de l'attribut
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
	* @param int(11) $groupe valeur de l'attribut
	* @return none
	*/
	function set_groupe($groupe)
	{
		$this->groupe = $groupe;
		$this->champs_modif[] = 'groupe';
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
	* @param float $hp_max valeur de l'attribut
	* @return none
	*/
	function set_hp_max($hp_max)
	{
		$this->hp_max = $hp_max;
		$this->champs_modif[] = 'hp_max';
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
	* @param float $mp_max valeur de l'attribut
	* @return none
	*/
	function set_mp_max($mp_max)
	{
		$this->mp_max = $mp_max;
		$this->champs_modif[] = 'mp_max';
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
	* @param mediumint(9) $distance valeur de l'attribut
	* @return none
	*/
	function set_distance($distance)
	{
		$this->distance = $distance;
		$this->champs_modif[] = 'distance';
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
	* @param mediumint(8) $blocage valeur de l'attribut
	* @return none
	*/
	function set_blocage($blocage)
	{
		$this->blocage = $blocage;
		$this->champs_modif[] = 'blocage';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $incantation valeur de l'attribut
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
	* @param mediumint(9) $sort_vie valeur de l'attribut
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
	* @param int(11) $sort_element valeur de l'attribut
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
	* @param int(11) $sort_mort valeur de l'attribut
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
	* @param mediumint(8) $identification valeur de l'attribut
	* @return none
	*/
	function set_identification($identification)
	{
		$this->identification = $identification;
		$this->champs_modif[] = 'identification';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $craft valeur de l'attribut
	* @return none
	*/
	function set_craft($craft)
	{
		$this->craft = $craft;
		$this->champs_modif[] = 'craft';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $alchimie valeur de l'attribut
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
	* @param mediumint(8) $architecture valeur de l'attribut
	* @return none
	*/
	function set_architecture($architecture)
	{
		$this->architecture = $architecture;
		$this->champs_modif[] = 'architecture';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $forge valeur de l'attribut
	* @return none
	*/
	function set_forge($forge)
	{
		$this->forge = $forge;
		$this->champs_modif[] = 'forge';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $survie valeur de l'attribut
	* @return none
	*/
	function set_survie($survie)
	{
		$this->survie = $survie;
		$this->champs_modif[] = 'survie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $dressage valeur de l'attribut
	* @return none
	*/
	function set_dressage($dressage)
	{
		$this->dressage = $dressage;
		$this->champs_modif[] = 'dressage';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_magie valeur de l'attribut
	* @return none
	*/
	function set_facteur_magie($facteur_magie)
	{
		$this->facteur_magie = $facteur_magie;
		$this->champs_modif[] = 'facteur_magie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_vie valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_vie($facteur_sort_vie)
	{
		$this->facteur_sort_vie = $facteur_sort_vie;
		$this->champs_modif[] = 'facteur_sort_vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_mort valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_mort($facteur_sort_mort)
	{
		$this->facteur_sort_mort = $facteur_sort_mort;
		$this->champs_modif[] = 'facteur_sort_mort';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_element valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_element($facteur_sort_element)
	{
		$this->facteur_sort_element = $facteur_sort_element;
		$this->champs_modif[] = 'facteur_sort_element';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $regen_hp valeur de l'attribut
	* @return none
	*/
	function set_regen_hp($regen_hp)
	{
		$this->regen_hp = $regen_hp;
		$this->champs_modif[] = 'regen_hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $maj_hp valeur de l'attribut
	* @return none
	*/
	function set_maj_hp($maj_hp)
	{
		$this->maj_hp = $maj_hp;
		$this->champs_modif[] = 'maj_hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $maj_mp valeur de l'attribut
	* @return none
	*/
	function set_maj_mp($maj_mp)
	{
		$this->maj_mp = $maj_mp;
		$this->champs_modif[] = 'maj_mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $point_sso valeur de l'attribut
	* @return none
	*/
	function set_point_sso($point_sso)
	{
		$this->point_sso = $point_sso;
		$this->champs_modif[] = 'point_sso';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $quete valeur de l'attribut
	* @return none
	*/
	function set_quete($quete)
	{
		$this->quete = $quete;
		$this->champs_modif[] = 'quete';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $quete_fini valeur de l'attribut
	* @return none
	*/
	function set_quete_fini($quete_fini)
	{
		$this->quete_fini = $quete_fini;
		$this->champs_modif[] = 'quete_fini';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $dernier_connexion valeur de l'attribut
	* @return none
	*/
	function set_dernier_connexion($dernier_connexion)
	{
		$this->dernier_connexion = $dernier_connexion;
		$this->champs_modif[] = 'dernier_connexion';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $statut valeur de l'attribut
	* @return none
	*/
	function set_statut($statut)
	{
		$this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $fin_ban valeur de l'attribut
	* @return none
	*/
	function set_fin_ban($fin_ban)
	{
		$this->fin_ban = $fin_ban;
		$this->champs_modif[] = 'fin_ban';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $frag valeur de l'attribut
	* @return none
	*/
	function set_frag($frag)
	{
		$this->frag = $frag;
		$this->champs_modif[] = 'frag';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param float $crime valeur de l'attribut
	* @return none
	*/
	function set_crime($crime)
	{
		$this->crime = $crime;
		$this->champs_modif[] = 'crime';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $amende valeur de l'attribut
	* @return none
	*/
	function set_amende($amende)
	{
		$this->amende = $amende;
		$this->champs_modif[] = 'amende';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('true','false') $teleport_roi valeur de l'attribut
	* @return none
	*/
	function set_teleport_roi($teleport_roi)
	{
		$this->teleport_roi = $teleport_roi;
		$this->champs_modif[] = 'teleport_roi';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_classe valeur de l'attribut
	* @return none
	*/
	function set_cache_classe($cache_classe)
	{
		$this->cache_classe = $cache_classe;
		$this->champs_modif[] = 'cache_classe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_stat valeur de l'attribut
	* @return none
	*/
	function set_cache_stat($cache_stat)
	{
		$this->cache_stat = $cache_stat;
		$this->champs_modif[] = 'cache_stat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_niveau valeur de l'attribut
	* @return none
	*/
	function set_cache_niveau($cache_niveau)
	{
		$this->cache_niveau = $cache_niveau;
		$this->champs_modif[] = 'cache_niveau';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $max_pet valeur de l'attribut
	* @return none
	*/
	function set_max_pet($max_pet)
	{
		$this->max_pet = $max_pet;
		$this->champs_modif[] = 'max_pet';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $beta valeur de l'attribut
	* @return none
	*/
	function set_beta($beta)
	{
		$this->beta = $beta;
		$this->champs_modif[] = 'beta';
	}
//fonction

	function get_comp($comp_assoc = '', $base = false)
	{
		$get = 'get_'.$comp_assoc;
		if(method_exists($this, $get)) return $this->$get($base);
		else return $this->get_competence($comp_assoc, false, $base);
	}

	function set_comp($comp_assoc = '', $valeur = '')
	{
		$set = 'set_'.$comp_assoc;
		if(method_exists($this, $set)) $this->$set($valeur);
		else $this->set_competence($comp_assoc, $valeur);
	}

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

	function is_comp_perso($nom = '')
	{
		if(!isset($this->comp_perso)) $this->get_comp_perso();

		return array_key_exists($nom, $this->comp_perso);
	}

	function get_buff($nom = false, $champ = false, $type = true)
	{
		if(!$nom)
		{
			$this->buff = buff::create('id_perso', $this->id, 'id ASC', 'type');
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
	
	function add_buff($nom, $effet, $effet2 = 0)
	{
		if(!isset($this->buff)) $this->get_buff();
		$buff = new buff();
		$buff->set_type($nom);
		$buff->set_effet($effet);
		$buff->set_effet2($effet2);
		$this->buff[] = $buff;
	}

	public $grade;
	function get_grade()
	{
		if(!isset($this->grade)) $this->grade = new grade($this->rang_royaume);
		return $this->grade;
	}

	public $enchantement = array();
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

	function is_enchantement($nom = false)
	{
		if ($nom === false)
			return (count($this->enchantement) != 0);
		else
			return array_key_exists($nom, $this->enchantement);
	}

	/**
	 * Permet de savoir si le joueur possède la compétence nom
	 * @param $nom le nom de la compétence
	 * @return true si le perso est sous le debuff false sinon.
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

	public $competences;
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

	function set_competence($nom, $valeur = '')
	{
		if(array_key_exists($nom, $this->competences))
		{
			$this->competences[$nom]->set_valeur($valeur);
			$this->competences[$nom]->sauver();
		}
	}

	/**
   * La plupart du temps on s'en fiche, de la main, on veut les degats
   * donc si $main == false : cumul, si $main == 'droite' || 'gauche' : detail
   */
	function get_arme_degat($main = false)
	{
		$degats = 0;
		if ($main == false || $main == 'droite')
			if ($this->get_arme())
				$degats += $this->arme->degat;
		if ($main == false || $main == 'gauche')
			if ($this->get_arme_gauche())
				$degats += $this->arme_gauche->degat;
		return $degats;
	}

	function get_artisanat()
	{
		return round(sqrt(($this->architecture + $this->forge + $this->alchimie) * 10));
	}

	public $inventaire_array;
	function get_inventaire_partie($partie)
	{
		if(!isset($this->inventaire_array)) $this->inventaire_array = unserialize($this->get_inventaire());
		return $this->inventaire_array->$partie;
	}

	public $inventaire_slot_array;
	function get_inventaire_slot_partie($partie = false, $force = false)
	{
		if(!isset($this->inventaire_slot_array) OR !$force) $this->inventaire_slot_array = unserialize($this->get_inventaire_slot());
		if($partie === false) return $this->inventaire_slot_array;
		else return $this->inventaire_slot_array[$partie];
	}

	function set_inventaire_slot_partie($objet, $partie)
	{
		$this->inventaire_slot_array[$partie] = $objet;
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
			$this->pp = 0;
			$this->pm = 0;
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
						// Effets magiques
						$effet = explode(';', $row[2]);
						foreach($effet as $eff)
						{
							$explode = explode('-', $eff);
							$this->register_item_effet($explode[0], $explode[1]);
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
			if($this->get_race() == 'mortvivant' AND moment_jour() == 'Soir')
			{
				$this->pp = round($this->pp * 1.15);
				$this->pm = round($this->pm * 1.15);
			}

			//Effets des enchantements
			if (isset($this->enchantement['pourcent_pm'])) $this->pm += floor($this->pm * $this->enchantement['pourcent_pm']['effet'] / 100);
			if (isset($this->enchantement['pourcent_pp']))	$this->pp += floor($this->pp * $this->enchantement['pourcent_pp']['effet'] / 100);

			//Buffs
			if($this->is_buff('buff_bouclier')) $this->pp = round($this->pp * (1 + ($this->get_buff('buff_bouclier', 'effet') / 100)));
			if($this->is_buff('buff_barriere')) $this->pm = round($this->pm * (1 + ($this->get_buff('buff_barriere', 'effet') / 100)));
			if($this->is_buff('buff_forteresse'))
			{
				$this->pp = round($this->pp * (1 + (($this->get_buff('buff_forteresse', 'effet')) / 100)));
				$this->pm = round($this->pm * (1 + (($this->get_buff('buff_forteresse', 'effet2')) / 100)));
			}
			if($this->is_buff('buff_cri_protecteur')) $this->pp = round($this->pp * (1 + ($this->get_buff('buff_cri_protecteur', 'effet') / 100)));
			if($this->is_buff('debuff_desespoir')) $this->pm = round($this->pm / (1 + (($this->get_buff('debuff_desespoir', 'effet')) / 100)));
			//Maladie suppr_defense
			if($this->is_buff('suppr_defense')) $this->pp = 0;
		}
		$this->armure=true;
	}

	function register_item_effet($id, $effet, $item = null)
	{
		switch ($id)
			{ // TODO: les autres, c'est quoi donc ?
			case 1:
				$this->add_bonus_permanents('regen_hp', $effet);
				break;
			case 5:
				$this->add_bonus_permanents('volonte', $effet);
				break;
			case 7:
				$this->add_bonus_permanents('dexterite', $effet);
				break;
			case 9:
				$ep = new effet_vampirisme($effet, $item->nom);
				if ($item->type == 'hache' || $item->type == 'dague' ||
						($item->type == 'epee' && eregi('^lame', $item->nom))) {
					$ep->pos = 'sa';
				}
				$this->add_effet_permanent('attaquant', $ep);
																	 
			default:
				break;
			}
	}

	private $effet_permanents_attaquant = array();
	private $effet_permanents_defenseur = array();
	function add_effet_permanent($mode, $effet)
	{
		$effets_mode = "effet_permanents_$mode";
		array_push($this->$effets_mode, $effet);
	}

	function get_effets_permanents(&$effets, $mode)
	{
		$effets_mode = "effet_permanents_$mode";
		foreach ($this->$effets_mode as $ep) {
			array_push($effets, $ep);
		}
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

	function get_reserve($base = false)
	{
		if (!isset($this->reserve))
			$this->reserve = ceil(2.1 * ($this->energie + floor(($this->energie - 8) / 2)));
		if (!$base) return $this->reserve + $this->get_bonus_permanents('reserve');
		else return $this->reserve;
	}

	public $coef_melee;
	function get_coef_melee()
	{
		if(!isset($this->coef_melee))
			$this->coef_melee = $this->forcex * $this->get_melee();
		return $this->coef_melee;
	}

	public $coef_incantation;
	function get_coef_incantation()
	{
		if(!isset($this->coef_incantation))
			$this->coef_incantation = $this->get_puissance() * $this->get_incantation();
		return $this->coef_incantation;
	}

	public $coef_distance;
	function get_coef_distance()
	{
		if(!isset($this->coef_distance))
			$this->coef_distance = round(($this->get_forcex() + $this->get_dexterite()) / 2) * $this->get_distance();
		return $this->coef_distance;
	}

	public $coef_blocage;
	function get_coef_blocage()
	{
		if(!isset($this->coef_blocage)) $this->coef_blocage = round(($this->forcex + $this->dexterite) / 2) * $this->blocage;
		return $this->coef_blocage;
	}

	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}

	function get_distance_joueur($joueur)
	{
		return calcul_distance($this->get_pos(), $joueur->get_pos());
	}

	function get_distance_pytagore($joueur)
	{
		return calcul_distance_pytagore($this->get_pos(), $joueur->get_pos());	
	}
	
	function get_force() 
	{ 
		return $this->get_forcex(); 
	}
	
	function set_force($force) 
	{ 
		$this->set_forcex($force); 
	}

	//Récupération des HP max après bonus, famine etc
	public $hp_maximum;
	function get_hp_maximum()
	{
		$this->hp_maximum = floor($this->get_hp_max());
		//Famine
		if($this->is_buff('famine')) $this->hp_maximum = $this->hp_maximum - ($this->hp_maximum * ($this->get_buff('famine', 'effet') / 100));
		return $this->hp_maximum;
	}

	//Récupération des MP max après bonus, famine etc
	public $mp_maximum;
	function get_mp_maximum()
	{
		$this->mp_maximum = floor($this->get_mp_max());
		//Famine
		if($this->is_buff('famine')) $this->mp_maximum = $this->mp_maximum - ($this->mp_maximum * ($this->get_buff('famine', 'effet') / 100));
		return $this->mp_maximum;
	}

	public $reserve_bonus;
	function get_reserve_bonus()
	{
		$this->reserve_bonus = $this->get_reserve();
		if($this->is_buff('buff_inspiration')) $this->reserve_bonus += $this->get_buff('buff_inspiration', 'effet');
		if($this->is_buff('buff_sacrifice')) $this->reserve_bonus += $this->get_buff('buff_sacrifice', 'effet');
		// Les bonus raciaux sont comptés dans les bonus perm
		return $this->reserve_bonus;
	}

	function inventaire()
	{
		return unserialize($this->inventaire);
	}

	public $arme;
	function get_arme()
	{
		if(!isset($this->arme))
		{
			global $db;
			$arme = $this->inventaire()->main_droite;
			if($arme != '')
			{
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
					//my_dump($this->enchantement);
				}
        if ($this->arme->effet)
        {
          $effets = split(';', $this->arme->effet);
          foreach ($effets as $effet)
          {
            $d_effet = split('-', $effet);
						$this->register_item_effet($d_effet[0], $d_effet[1], $this->arme);
          }
        }
        //my_dump($this->arme);
			}
			else $this->arme = false;
		}
		return $this->arme;
	}

	public $arme_gauche;
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
				else if ($arme_d['enchantement'] != null)
				{
					$gemme = new gemme_enchassee($arme_d['enchantement']);
					if ($gemme->enchantement_type == 'degat')
						$this->arme_gauche->degat += $gemme->enchantement_effet;
					$this->register_gemme_enchantement($gemme);
					//my_dump($this->enchantement);
				}
			}
			else $this->arme_gauche = false;
		}
		return $this->arme_gauche;
	}

	public $bouclier;
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
					//my_dump($this->enchantement);
				}
			}
			else $this->bouclier = false;
		}
		return $this->bouclier;
	}

	public $accessoire;
	function get_accessoire()
	{
		if(!isset($this->accessoire))
		{
			global $db;
			$accessoire = $this->inventaire()->accessoire;
			if($accessoire != '' AND $accessoire != 'lock')
			{
				$acc = decompose_objet($accessoire);
				$q = "SELECT * FROM $acc[table_categorie] WHERE id = $acc[id_objet]";
				$req = $db->query($q);
				$this->accessoire = $db->read_object($req);
				if ($acc['enchantement'] != null)
				{
					$gemme = new gemme_enchassee($acc['enchantement']);
					$this->register_gemme_enchantement($gemme);
					//my_dump($this->enchantement);
				}
				switch ($this->accessoire->type) {
				case 'rm':
					$this->add_bonus_permanents('reserve', $this->accessoire->effet);
					break;
				case 'chance_debuff':
				case 'buff':
				case 'fabrication':
					$this->add_bonus_permanents($this->accessoire->type,
																			$this->accessoire->effet);
					break;
				}
			}
			else $this->accessoire = false;
		}
		return $this->accessoire;
	}

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

	function get_distance_tir()
	{
		$arme = $this->inventaire()->main_droite;
		if(!isset($this->arme)) $this->get_arme();
		if($this->arme)
		{
			$arme = $this->arme->distance_tir;
			if($this->is_buff('longue_portee')) $bonus = $this->get_buff('longue_portee', 'effet');
			else $bonus = 0;
			return ($arme + $bonus + $this->get_bonus_permanents('portee'));
		}
		return 0;
	}

	function get_arme_type()
	{
		if(!isset($this->arme)) $this->get_arme();
		return $this->arme->type;
	}

	function get_liste_quete()
	{
		$this->liste_quete = unserialize($this->quete);
		return $this->liste_quete;
	}

	function prend_quete($quete)
	{
		global $db;
		$valid = true;
		$requete = "SELECT id, objectif FROM quete WHERE id = ".$quete;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		//Vérifie si le joueur n'a pas déjà pris la quète.
		if($this->get_quete() != '')
		{
			foreach($this->get_liste_quete() as $quest)
			{
				if($quest['id_quete'] == $_GET['id']) $valid = false;
			}
			$numero_quete = (count($this->liste_quete));
		}
		else
		{
			$numero_quete = 0;
		}
		if($valid)
		{
			$quete = unserialize($row['objectif']);
			$count = count($quete);
			$i = 0;
			while($i < $count)
			{
				$this->liste_quete[$numero_quete]['objectif'][$i]->cible = $quete[$i]->cible;
				$this->liste_quete[$numero_quete]['objectif'][$i]->requis = $quete[$i]->requis;
				$this->liste_quete[$numero_quete]['objectif'][$i]->nombre = 0;
				$this->liste_quete[$numero_quete]['id_quete'] = $row['id'];
				$i++;
			}
			$this->set_quete(serialize($this->liste_quete));
			$this->sauver();
			return true;
		}
		else
		{
			$G_erreur = 'Vous avez déjà cette quète en cours !';
			return false;
		}
	}

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

	function is_groupe()
	{
		return !empty($this->groupe);
	}

	public $action;
	public $action_do;
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

  function check_materiel()
  {
    $this->get_arme();
    $this->get_arme_gauche();
    $this->get_bouclier();
    $this->get_armure();
    $this->get_accessoire();
  }

	function check_perso($last_action = true)
	{
    $this->check_materiel();
		$modif = false;	 // Indique si le personnage a été modifié.
		global $db, $G_temps_regen_hp, $G_temps_maj_hp, $G_temps_maj_mp, $G_temps_PA, $G_PA_max, $G_pourcent_regen_hp, $G_pourcent_regen_mp;
		// On vérifie que le personnage est vivant
		if($this->hp > 0)
		{
			// On augmente les HP max si nécessaire
			$temps_maj = time() - $this->get_maj_hp(); // Temps écoulé depuis la dernière augmentation de HP.
			$temps_hp = $G_temps_maj_hp;  // Temps entre deux augmentation de HP.

			If ($temps_maj > $temps_hp && $temps_hp > 0) // Pour ne jamais diviser par 0...
			{
				$time = time();
				$nb_maj = floor($temps_maj / $temps_hp);
				$hp_gagne = $nb_maj * (sqrt($this->get_vie(true)) * 2.7);
				$this->set_hp_max($this->get_hp_max(true) + $hp_gagne);
				$this->set_maj_hp($this->get_maj_hp() + $nb_maj * $temps_hp);
				$modif = true;
			}
			// On augmente les MP max si nécessaire
			$temps_maj = time() - $this->get_maj_mp(); // Temps écoulé depuis la dernière augmentation de MP.
			$temps_mp = $G_temps_maj_mp;  // Temps entre deux augmentation de MP.
			if ($temps_maj > $temps_mp)
			{
				$time = time();
				$nb_maj = floor($temps_maj / $temps_mp);
				$mp_gagne = $nb_maj * (($this->get_energie(true) - 3) / 4);
				$this->set_mp_max($this->get_mp_max(true) + $mp_gagne);
				$this->set_maj_mp($this->get_maj_mp() + $nb_maj * $temps_mp);
				$modif = true;
			}
			// Régénération des HP et MP
			$temps_regen = time() - $this->get_regen_hp(); // Temps écoulé depuis la dernière régénération.

			// Gemme du troll
			if (array_key_exists('regeneration', $this->get_enchantement())) {
				$bonus_regen = $this->get_enchantement('regeneration', 'effet') * 60;
				if ($G_temps_regen_hp <= $bonus_regen) {
					$bonus_regen = $G_temps_regen_hp - 3600; // 1h min de regen
				}
			} else $bonus_regen = 0;

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
				$accessoire = $this->get_accessoire();
				if($accessoire !== false)
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
				}
				$bonus_arme = $this->get_bonus_permanents('regen_hp');
				$bonus_arme_mp = $this->get_bonus_permanents('regen_mp');
				// Effets magiques des objets
				/*foreach($this['objet_effet'] as $effet)
				{
					switch($effet['id'])
					{
						case '1' :
							$bonus_accessoire += $effet['effet'];
						break;
						case '10' :
							$bonus_accessoire_mp += $effet['effet'];
						break;
					}
				}*/
				// Calcul des HP et MP récupérés
				$hp_gagne = $nb_regen * (floor($this->get_hp_maximum() * $regen_hp) + $bonus_accessoire + $bonus_arme);
				$mp_gagne = $nb_regen * (floor($this->get_mp_maximum() * $regen_mp) + $bonus_accessoire_mp + $bonus_arme_mp);
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
					$hp_gagne = $hp_gagne * $malus_agonie;
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
				$this->set_hp($this->get_hp() + $hp_gagne);
				if ($this->get_hp() > $this->get_hp_maximum()) $this->set_hp(floor($this->get_hp_maximum()));
				// Mise à jour des MP
				$this->set_mp($this->get_mp() + $mp_gagne);
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
			
			// Mise-à-jour du personnage dans la base de donnée
			$this->sauver();
		} // if($this->get_hp() > 0)
		// On supprime tous les buffs périmés
		$requete = "DELETE FROM buff WHERE fin <= ".time();
		$req = $db->query($requete);
		// On enlève le ban s'il y en a un et qu'il est fini
		$requete = "UPDATE perso SET statut = 'actif' WHERE statut = 'ban' AND fin_ban <= ".time();
		$db->query($requete);
	}

	function recherche_objet($id_objet)
	{
		global $G_place_inventaire;
		$objet_d = decompose_objet($id_objet);
		$trouver =  false;
		//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
		$i = 0;
		$partie = $this->get_inventaire_slot_partie();
		while(($i < $G_place_inventaire) AND !$trouver)
		{
			$objet_i = decompose_objet($partie[$i]);
			if($objet_i['sans_stack'] == $objet_d['sans_stack'])
			{
				$trouver = true;
			}
			else $i++;
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

	function supprime_objet($id_objet, $nombre)
	{
		global $db;
		$i = $nombre;
    $inventaire = $this->get_inventaire_slot_partie();
		while($i > 0)
		{
			$objet = $this->recherche_objet($id_objet);
			//Vérification si objet "stacké"
			//print_r($objet);
			$stack = explode('x', $inventaire[$objet[1]]);
			if($stack[1] > 1) $inventaire[$objet[1]] = $stack[0].'x'.($stack[1] - 1);
			else array_splice($inventaire, $objet[1], 1);
			$i--;
		}
		$this->set_inventaire_slot(serialize($inventaire));
		$this->sauver();
	}

	function prend_objet($id_objet)
	{
		if(!isset($this->inventaire_perso)) $this->inventaire_perso = new inventaire($this->inventaire, $this->inventaire_slot);
		if($this->inventaire_perso->prend_objet($id_objet))
		{
			if(is_array($this->inventaire_perso->slot_liste)) $this->set_inventaire_slot(serialize($this->inventaire_perso->slot_liste));
			else $this->set_inventaire_slot($this->inventaire_perso->slot_liste);
			$this->sauver();
			return true;
		}
		else return false;
	}

	function desequip($type)
	{
		global $db, $G_erreur, $G_place_inventaire;
		$inventaire = $this->inventaire();
		if($inventaire->$type !== 0 AND $inventaire->$type != '')
		{
			//Inventaire plein
			if(!$this->prend_objet($inventaire->$type))
			{
				$G_erreur = 'Vous n\'avez plus de place dans votre inventaire<br />';
				return false;
			}
			else
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
		}
		return true;
	}

	function equip_objet($objet)
	{
		global $db, $G_erreur;
		$equip = false;
		$conditions = array();
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
						$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
					}
					elseif($row['type'] == 'bouclier')
					{
						$conditions[0]['attribut']	= 'coef_blocage';
						$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
					}
					else
					{
						$conditions[0]['attribut']	= 'coef_melee';
						$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
					}
					$conditions[1]['attribut']	= 'coef_distance';
					$conditions[1]['valeur']	= $row['forcex'] * $row['distance'];
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
					$type = 'accessoire';
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
			if($type == 'main_droite' AND $row['type'] != 'dague')
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
			//Verifie si il a déjà un objet de ce type sur lui
			if ($type != '')
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

			if($desequip)
			{
				//On équipe
				$inventaire = $this->inventaire();
				$inventaire->$type = $objet;
				if($categorie == 'a' AND $count == 2) $inventaire->main_gauche = 'lock';
				$this->set_inventaire(serialize($inventaire));
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

	private $bonus_permanents = array();
	function get_bonus_permanents($bonus)
	{
		if (array_key_exists($bonus, $this->bonus_permanents))
			return $this->bonus_permanents[$bonus];
		return 0;
	}

	function add_bonus_permanents($bonus, $value)
	{
		if (array_key_exists($bonus, $this->bonus_permanents))
			$this->bonus_permanents[$bonus] += $value;
		else
			$this->bonus_permanents[$bonus] = $value;
	}

	function get_bonus_shine($id_bonus = false)
	{
		if(!$id_bonus)
		{
			$this->bonus_shine = bonus_perso::create(array('id_perso'), array($this->id));
			return $this->bonus_shine;
		}
		else
		{
			if(!isset($this->bonus_shine)) $this->get_bonus_shine();
			foreach($this->bonus_shine as $bonus)
			{
				if($bonus->get_id_bonus() == $id_bonus)
					return $bonus;						
			}
		}
		return false;
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

	function add_pa($add_pa)
  {
    $this->set_pa($this->pa + $add_pa);
    if ($this->pa < 0)
      $this->pa = 0;
  }

	function add_star($add_star)
  {
    $this->set_star($this->star + $add_star);
    if ($this->star < 0)
      $this->star = 0;
  }

  function add_hp($add_hp) 
  {
    $this->set_hp($this->hp + $add_hp);
    if ($this->hp > $this->get_hp_maximum())
      $this->hp = floor($this->get_hp_maximum());
    if ($this->hp < 0)
      $this->hp = 0;
  }

  function add_mp($add_mp) 
  {
    $this->set_mp($this->mp + $add_mp);
    if ($this->mp > $this->get_mp_maximum())
      $this->mp = floor($this->get_mp_maximum());
    if ($this->mp < 0)
      $this->mp = 0;
  }

  function supprime_rez()
  {
	  global $db;
	  $requete = "DELETE FROM rez WHERE id_rez = ".$this->id;
	  $db->query($requete);
  }

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

	/** on ne m'aura plus avec les machins déclarés depuis dehors */
	//function __get($name) { $debug = debug_backtrace(); die('fuck: '.$debug[0]['file'].' line '.$debug[0]['line']); }
	//function __set($name, $value) { $debug = debug_backtrace(); die('fuck: '.$debug[0]['file'].' line '.$debug[0]['line']); }

	function get_pets($force = false)
	{
		if(!isset($this->pets) OR $force) $this->pets = pet::create(array('id_joueur', 'ecurie'), array($this->id, 0), 'principale DESC');
		return $this->pets;
	}

	function get_pet()
	{
		if(!isset($this->pet))
		{
			$pet = pet::create(array('id_joueur', 'principale', 'ecurie'), array($this->id, 1, 0), 'principale DESC');
			$this->pet = $pet[0];
		}
		return $this->pet;
	}

	function get_ecurie($force = false)
	{
		if(!isset($this->ecurie) OR $force) $this->ecurie = pet::create(array('id_joueur', 'ecurie'), array($this->id, 1), 'principale DESC');
		return $this->ecurie;
	}

	function get_ecurie_self($force = false)
	{
		if(!isset($this->ecurie_self) OR $force) $this->ecurie_self = pet::create(array('id_joueur', 'ecurie'), array($this->id, 2), 'principale DESC');
		return $this->ecurie_self;
	}

	function add_pet($id_monstre, $hp = false)
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
			$pet->set_mp(0);
			if(count($this->get_pets()) == 0) $pet->set_principale(1);
			else $pet->set_principale(0);
			$pet->sauver();
			return true;
		}
	}

	function nb_pet()
	{
		return count($this->get_pets());
	}

	function nb_pet_ecurie()
	{
		return count($this->get_ecurie());
	}

	function set_pet_principale($pet_id)
	{
		global $db;
		$requete = "UPDATE pet SET principale = 0 WHERE id_joueur = ".$this->id;
		$db->query($requete);
		$pet = new pet($pet_id);
		$pet->set_principale(1);
		$pet->sauver();
	}

	function pet_to_ecurie($pet_id, $type_ecurie = 1, $max_ecurie = 10)
	{
		$pet = new pet($_GET['d']);
		if($pet->get_id_joueur() == $this->get_id())
		{
			if($this->get_star() >= $pet->get_cout_depot() OR $type_ecurie == 2)
			{
				if($this->nb_pet_ecurie() < $max_ecurie)
				{
					$pet->set_ecurie($type_ecurie);
					$pet->set_principale(0);
					$pet->sauver();
					if($type_ecurie == 1)
					{
						$this->set_star($this->get_star() - $pet->get_cout_depot());
						$this->sauver();
					}
				}
				else
				{
					echo '<h5>L\'écurie ne peut pas prendre plus de vis créatures.</h5>';
				}
			}
			else
			{
				echo '<h5>Vous n\'avez pas assez de stars !</h5>';
			}
		}
		else
		{
			echo '<h5>Cette créature ne vous appartient pas !</h5>';
		}
	}

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
			{
				echo '<h5>Vous ne pouvez pas prendre plus de créature avec vous.</h5>';
			}
		}
		else
		{
			echo '<h5>Cette créature ne vous appartient pas !</h5>';
		}
	}

	function max_dresse()
	{
		$max = floor(sqrt($this->get_dressage())) - 5;
		if($max < 1) $max = 1;
		return $max;
	}

	function can_dresse($monstre)
	{
		if($this->max_dresse() >= $monstre->get_level()) return true;
		else return false;
	}
}
?>
