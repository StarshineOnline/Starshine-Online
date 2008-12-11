<?php
//Inclusion de la classe abstraite personnage
include_once('personnage.class.php');

//! Classe PersoJoueur
/**
 * Classe PersoJoueur
 *
 * Classe représentant un personnage joueur permettant de modifier, d'ajouter ou de supprimer un joueur dans la base.
 * Hérite de la classe Personnage.
*/
class PersoJoueur extends Personnage
{
	private $mort; //int 0
	private $exp; //int 0
	private $honneur; //int 0
	private $niveau; //int 0
	private $mdp; //string
	private $rang; //int 7
	private $vie; //int 0
	private $force; //int 0
	private $dexterite; //int 0
	private $puissance; //int 0
	private $volonte; //int 0
	private $energie; //int 0
	private $race; //string ''
	private $classe; //string ''
	private $id_classe; //int 0
	private $inventaire; //string
	private $inventaire_slot; //string
	private $pa; //int 1
	private $dernier_action; //int 0
	private $action_a; //int 0
	private $action_d; //int 0
	private $sort_jeu; //string
	private $sort_combat; //string
	private $comp_jeu; //string
	private $comp_combat; //string
	private $star; //int 0
	private $arme; //int 0
	private $competence; //string
	private $groupe; //int 0
	private $hp; //int 0
	private $hp_max; //int 0
	private $mp; //int 0
	private $mp_max; //int 0
	private $melee; //int 1
	private $distance; //int 1
	private $esquive; //int 1
	private $blocage; //int 1
	private $incantation; //int 1
	private $sort_vie; //int 0
	private $sort_element; //int 0
	private $sort_mort; //int 0
	private $identification; //int 1
	private $forge; //int 1
	private $craft; //int 1
	private $survie; //int 1
	private $facteur_magie; //double 1
	private $facteur_vie; //double 1
	private $facteur_element; //double 1
	private $facteur_mort; //double 1
	private $resistance_magique; //int 1
	private $regen_hp; //int 0
	private $maj_hp; //int 1
	private $maj_mp; //int 1
	private $point_shine; //int
	private $quete; //string
	private $quete_fini; //string
	private $derniere_connexion; //int 0
	private $statut; //string 'actif'
	private $fin_ban; //int 0
	private $frag; //int 0
	private $crime; //float 0
	private $amende; //int 0
	private $teleport; //boolean true
	private $cache_classe; //int 0
	private $cache_stat; //int 0
	private $cache_niveau; //int 0

	
	//! Accesseur $mort
	function getMort()
	{
		return $this->mort;
	}

	//! Accesseur $exp
	function getExp()
	{
		return $this->exp;
	}
	
	//! Accesseur $honneur
	function getHonneur()
	{
		return $this->honneur;
	}
	
	//! Accesseur $niveau
	function getNiveau()
	{
		return $this->niveau;
	}
	
	//! Accesseur $mdp
	function getPassword()
	{
		return $this->mdp;
	}
	
	//! Accesseur $rang
	function getRang()
	{
		return $this->rang;
	}
	
	//! Accesseur $vie
	function getVie()
	{
		return $this->vie;
	}
	
	//! Accesseur $force
	function getForce()
	{
		return $this->force;
	}
	
	//! Accesseur $dexterite
	function getDexterite()
	{
		return $this->dexterite;
	}
	
	//! Accesseur $puissance
	function getPuissance()
	{
		return $this->puissance;
	}
	
	//! Accesseur $volonte
	function getVolonte()
	{
		return $this->volonte;
	}
	
	//! Accesseur $energie
	function getEnergie()
	{
		return $this->energie;
	}
	
	//! Accesseur $race
	function getRace()
	{
		return $this->race;
	}
	
	//! Accesseur $classe
	function getClasse()
	{
		return $this->classe;
	}
	
	//! Accesseur $id_classe
	function getIdClasse()
	{
		return $this->id_classe;
	}
	
	//! Accesseur $inventaire
	function getInventaire()
	{
		return $this->inventaire;
	}
	
	//! Accesseur $inventaire_slot
	function getInventaireSlot()
	{
		return $this->inventaire_slot;
	}
	
	//! Accesseur $pa
	function getPA()
	{
		return $this->pa;
	}
	
	//! Accesseur $dernieraction
	function getDernierAction()
	{
		return $this->dernier_action;
	}
	
	//! Accesseur $action_a
	function getActionA()
	{
		return $this->action_a;
	}
	
	//! Accesseur $action_d
	function getActionD()
	{
		return $this->action_d;
	}
	
	//! Accesseur $sort_jeu
	function getSortJeu()
	{
		return $this->sort_jeu;
	}
	
	//! Accesseur $sort_combat
	function getSortCombat()
	{
		return $this->sort_combat;
	}
	
	//! Accesseur $comp_combat
	function getCompCombat()
	{
		return $this->comp_combat;
	}
	
	//! Accesseur $comp_jeu
	function getCompJeu()
	{
		return $this->comp_jeu;
	}
	
	//! Accesseur $star
	function getStar()
	{
		return $this->star;
	}
	
	//! Accesseur $arme
	function getArme()
	{
		return $this->arme;
	}
	
	//! Accesseur $competence
	function getCompetence()
	{
		return $this->competence;
	}
	
	//! Accesseur $groupe
	function getGroupe()
	{
		return $this->groupe;
	}
	
	//! Accesseur $hp
	function getHP()
	{
		return $this->hp;
	}
	
	//! Accesseur $hp_max
	function getHPMAX()
	{
		return $this->hp_max;
	}
	
	//! Accesseur $mp
	function getMP()
	{
		return $this->mp;
	}
	
	//! Accesseur $mp_max
	function getMPMAX()
	{
		return $this->mp_max;
	}
	
	//! Accesseur $melee
	function getMelee()
	{
		return $this->melee;
	}
	
	//! Accesseur $distance
	function getDistance()
	{
		return $this->distance;
	}
	
	//! Accesseur $esquive
	function getEsquive()
	{
		return $this->esquive;
	}
	
	//! Accesseur $blocage
	function getBloquage()
	{
		return $this->blocage;
	}
	
	//! Accesseur $incantation
	function getIncantation()
	{
		return $this->incantation;
	}
	
	//! Accesseur $sort_vie
	function getSortVie()
	{
		return $this->sort_vie;
	}
	
	//! Accesseur $sort_elem
	function getSortElem()
	{
		return $this->sort_elem;
	}
	
	//! Accesseur $sort_mort
	function getSortMort()
	{
		return $this->sort_mort;
	}
	
	//! Accesseur $identification
	function getIdentification()
	{
		return $this->identification;
	}
	
	//! Accesseur $forge
	function getForge()
	{
		return $this->forge;
	}
	
	//! Accesseur $craft
	function getCraft()
	{
		return $this->craft;
	}
	
	//! Accesseur $survie
	function getSurvie()
	{
		return $this->survie;
	}
	
	//! Accesseur $facteur_magie
	function getFacteurMagie()
	{
		return $this->facteur_magie;
	}
	
	//! Accesseur $facteur_vie
	function getFacteurVie()
	{
		return $this->facteur_vie;
	}
	
	//! Accesseur $facteur_elem
	function getFacteurElem()
	{
		return $this->facteur_elem;
	}
	
	//! Accesseur $facteur_mort
	function getFacteurMort()
	{
		return $this->facteur_mort;
	}
	
	//! Accesseur $resistance_magique
	function getResistanceMagique()
	{
		return $this->resistance_magique;
	}
	
	//! Accesseur $regen_hp
	function getRegenHP()
	{
		return $this->regen_hp;
	}
	
	//! Accesseur $maj_hp
	function getMajHP()
	{
		return $this->maj_hp;
	}
	
	//! Accesseur $maj_mp
	function getMajMP()
	{
		return $this->maj_mp;
	}
	
	//! Accesseur $point_shine
	function getPointShine()
	{
		return $this->point_shine;
	}
	
	//! Accesseur $quete
	function getQuete()
	{
		return $this->quete;
	}
	
	//! Accesseur $quete_fini
	function getQueteFini()
	{
		return $this->quete_fini;
	}
	
	//! Accesseur $derniere_connexion
	function getDerniereConnexion()
	{
		return $this->derniere_connexion;
	}
	
	//! Accesseur $statut
	function getStatut()
	{
		return $this->statut;
	}
	
	//! Accesseur $fin_ban
	function getFinBan()
	{
		return $this->fin_ban;
	}
	
	//! Accesseur $frag
	function getFrag()
	{
		return $this->frag;
	}
	
	//! Accesseur $crime
	function getCrime()
	{
		return $this->crime;
	}
	
	//! Accesseur $amende
	function getAmende()
	{
		return $this->amende;
	}
	
	//! Accesseur $teleport
	function getTeleport()
	{
		return $this->teleport;
	}
	
	//! Accesseur $cache_classe
	function getCacheClasse()
	{
		return $this->cache_classe;
	}
	
	//! Accesseur $cache_stat
	function getCacheStat()
	{
		return $this->cache_stat;
	}
	
	//! Accesseur $cache_niveau
	function getCacheNiveau()
	{
		return $this->cache_niveau;
	}
	
	
	
	
	
	
	
	
	
	
	//! Modifieur $mort
	function setMort($mort)
	{
		$this->mort = mort;
	}

	//! Fonction ajoutant une mort
	function addMort()
	{
		$this->mort++;
	}

	//! Modifieur $exp
	function setExp($exp)
	{
		$this->exp = $exp;
	}
	
	//! Modifieur $honneur
	function setHonneur($honneur)
	{
		$this->honneur = $honneur;
	}
	
	//! Modifieur $niveau
	function setNiveau($niveau)
	{
		$this->niveau = $niveau;
	}
	
	//! Fonction permettant d'ajouter un niveau
	function addNiveau()
	{
		$this->niveau++;
	}
	
	//! Modifieur $mdp
	function setPassword($mdp)
	{
		$this->mdp = $mdp;
	}
	
	//! Modifieur $rang
	function setRang($rang)
	{
		$this->rang = $rang;
	}
	
	//! Modifieur $vie
	function setVie($vie)
	{
		$this->vie = $vie;
	}
	
	//! Modifieur $force
	function setForce($force)
	{
		$this->force = $force;
	}
	
	//! Modifieur $dexterite
	function setDexterite($dexterite)
	{
		$this->dexterite = $dexterite;
	}
	
	//! Modifieur $puissance
	function setPuissance($puissance)
	{
		$this->puissance = $puissance;
	}
	
	//! Modifieur $volonte
	function setVolonte($volonte)
	{
		$this->volonte = $volonte;
	}
	
	//! Modifieur $energie
	function setEnergie($energie)
	{
		$this->energie = $energie;
	}
	
	//! Modifieur $race
	function setRace($race)
	{
		$this->race = $race;
	}
	
	//! Modifieur $classe
	function setClasse($classe)
	{
		$this->classe = $classe;
	}
	
	//! Modifieur $id_classe
	function setIdClasse($id_classe)
	{
		$this->id_classe = $id_classe;
	}
	
	//! Modifieur $inventaire
	function setInventaire($inventaire)
	{
		$this->inventaire = $inventaire;
	}
	
	//! Modifieur $inventaire_slot
	function setInventaireSlot($inventaire_slot)
	{
		$this->inventaire_slot = $inventaire_slot;
	}
	
	//! Modifieur $pa
	function setPA($pa)
	{
		$this->pa = $pa;
	}
	
	//! Modifieur $dernieraction
	function setDernierAction($dernieraction)
	{
		$this->dernier_action = $dernieraction;
	}
	
	//! Modifieur $action_a
	function setActionA($action_a)
	{
		$this->action_a = $action_a;
	}
	
	//! Modifieur $action_d
	function setActionD($action_d)
	{
		$this->action_d = $action_d;
	}
	
	//! Modifieur $sort_jeu
	function setSortJeu($sort_jeu)
	{
		$this->sort_jeu = $sort_jeu;
	}
	
	//! Modifieur $sort_combat
	function setSortCombat($sort_combat)
	{
		$this->sort_combat = $sort_combat;
	}
	
	//! Modifieur $comp_combat
	function setCompCombat($com_combat)
	{
		$this->comp_combat = $comp_combat;
	}
	
	//! Modifieur $comp_jeu
	function setCompJeu($comp_jeu)
	{
		$this->comp_jeu = $comp_jeu;
	}
	
	//! Modifieur $star
	function setStar($star)
	{
		$this->star = $star;
	}
	
	//! Modifieur $arme
	function setArme($arme)
	{
		$this->arme = $arme;
	}
	
	//! Modifieur $competence
	function setCompetence($competence)
	{
		$this->competence = $competence;
	}
	
	//! Modifieur $groupe
	function setGroupe($groupe)
	{
		$this->groupe = $groupe;
	}
	
	//! Modifieur $hp
	function setHP($hp)
	{
		$this->hp = $hp;
	}
	
	//! Modifieur $hp_max
	function setHPMAX($hp_max)
	{
		$this->hp_max = $hp_max;
	}
	
	//! Modifieur $mp
	function setMP($mp)
	{
		$this->mp = $mp;
	}
	
	//! Modifieur $hp
	function setMPMAX($mp_max)
	{
		$this->mp_max = $mp_max;
	}
	
	//! Modifieur $melee
	function setMelee($melee)
	{
		$this->melee = $melee;
	}
	
	//! Modifieur $distance
	function setDistance($distance)
	{
		$this->distance = $distance;
	}
	
	//! Modifieur $esquive
	function setEsquive($esquive)
	{
		$this->esquive = $esquive;
	}
	
	//! Modifieur $blocage
	function setBloquage($blocage)
	{
		$this->blocage = $blocage;
	}
	
	//! Modifieur $incantation
	function setIncantation($incantation)
	{
		$this->incantation = $incantation;
	}
	
	//! Modifieur $sort_vie
	function setSortVie($sort_vie)
	{
		$this->sort_vie = $sort_vie;
	}
	
	//! Modifieur $sort_elem
	function setSortElem($sort_elem)
	{
		$this->sort_elem = $sort_elem;
	}
	
	//! Modifieur $sort_mort
	function setSortMort($sort_mort)
	{
		$this->sort_mort = $sort_mort;
	}
	
	//! Modifieur $identification
	function setIdentification($identification)
	{
		$this->identification = $identification;
	}
	
	//! Modifieur $forge
	function setForge($forge)
	{
		$this->forge = $forge;
	}
	
	//! Modifieur $craft
	function setCraft($craft)
	{
		$this->craft = $craft;
	}
	
	//! Modifieur $survie
	function setSurvie($survie)
	{
		$this->survie = $survie;
	}
	
	//! Modifieur $facteur_magie
	function setFacteurMagie($facteur_magie)
	{
		$this->facteur_magie = $facteur_magie;
	}
	
	//! Modifieur $facteur_vie
	function setFacteurVie($facteur_vie)
	{
		$this->facteur_vie = $facteur_vie;
	}
	
	//! Modifieur $facteur_elem
	function setFacteurElem($facteur_elem)
	{
		$this->facteur_elem = $facteur_elem;
	}
	
	//! Modifieur $facteur_mort
	function setFacteurMort($facteur_mort)
	{
		$this->facteur_mort = $facteur_mort;
	}
	
	//! Modifieur $resistance_magique
	function setResistanceMagique($resistance_magique)
	{
		$this->resistance_magique = $resistance_magique;
	}
	
	//! Modifieur $regen_hp
	function setRegenHP($regen_hp)
	{
		$this->regen_hp = $regen_hp;
	}
	
	//! Modifieur $maj_hp
	function setMajHP($maj_hp)
	{
		$this->maj_hp = $maj_hp;
	}
	
	//! Modifieur $maj_mp
	function setMajMP($maj_mp)
	{
		$this->maj_mp = $maj_mp;
	}
	
	//! Modifieur $point_shine
	function setPointShine($point_shine)
	{
		$this->point_shine = $point_shine;
	}
	
	//! Fonction ajoutant un point shine
	function addPointShine()
	{
		$this->point_shine++;
	}
	
	//! Modifieur $quete
	function setQuete($quete)
	{
		$this->quete = $quete;
	}
	
	//! Fonction ajoutant une quete a la liste
	function addQuete($quete)
	{
		$this->quete .= $quete;
	}
	
	//! Modifieur $quete_fini
	function setQueteFini($quete_fini)
	{
		$this->quete_fini = $quete_fini;
	}
	
	//! Ajoute une quete_fini a la liste
	function addQueteFini($quete_fini)
	{
		$this->quete_fini .= $quete_fini;
	}
	
	//! Modifieur $derniere_connexion
	function setDerniereConnexion($derniere_connexion)
	{
		$this->derniere_connexion = $derniere_connexion;
	}
	
	//! Modifieur $statut
	function setStatut($quo)
	{
		$this->statut = $quo;
	}
	
	//! Modifieur $fin_ban
	function setFinBan($fin_ban)
	{
		$this->fin_ban = $fin_ban;
	}
	
	//! Modifieur $frag
	function setFrag($frag)
	{
		$this->frag = $frag;
	}
	
	//! Modifieur $crime
	function setCrime($crime)
	{
		$this->crime = $crime;
	}
	
	//! Modifieur $amende
	function setAmende($amende)
	{
		$this->amende = $amende;
	}
	
	//! Modifieur $teleport
	function setTeleport($teleport)
	{
		$this->teleport = $teleport;
	}
	
	//! Modifieur $cache_classe
	function setCacheClasse($cache_classe)
	{
		$this->cache_classe = $cache_classe;
	}
	
	//! Modifieur $cache_stat
	function setCacheStat($cache_stat)
	{
		$this->cache_stat = $cache_stat;
	}
	
	//! Modifieur $cache_niveau
	function setCacheNiveau($cache_niveau)
	{
		$this->cache_niveau = $cache_niveau;
	}

	//! Constructeur
	/**
		Constructeur
		
		Le constructeur plusieurs appels:
		-Perso() Crée un perso vide
		-Perso($id) qui récupère les informations du personnage dans la base
		-Perso($nom, $coord_x, $coord_y,
		
		Les arguments suivants n'ont pas de valeur par défaut:
			$mdp $inventaire $inventaire_slot $sort_jeu $sort_combat $comp_jeu $comp_combat $competence $point_shine, $quete, $quete_fini.
			
		@param $id int Identifiant du personnage dans la base
		@param $nom String Nom du personnage.
		@param $coord_x int Coordonnée en X du personnage.
		@param $coord_y int Coordonnée en Y du personnage.
		@param $mort int Nombre de mort du personnage
		@param $exp int Experience du personnage
		@param $honneur int Honneur du personnage
		@param $niveau int Niveau du personnage
		@param $mdp string Mot de passe du joueur
		@param $rang int Rang du personnage
		@param $vie int Vie du personnage
		@param $force int Force du personnage
		@param $dexterite int Dexterité du personnage
		@param $puissance int Puissance du personnage
		@param $volonte int Volonté du personnage
		@param $energie int Energie du personnage
		@param $race string Race du personnage
		@param $classe string Classe du personnage
		@param $id_classe int Identifiant de la classe du personnage
		@param $inventaire string Inventaire du personnage
		@param $inventaire_slot string Slots inventaires du personnage
		@param $pa int Nombre de PA du personnage
		@param $dernier_action int Dernière action
		@param $action_a int Script en attaque
		@param $action_d int Script en défense
		@param $sort_jeu string Buffs connus
		@param $sort_combat string Sorts de combat connus
		@param $comp_jeu string Cris connus
		@param $comp_combat string Compétences de combat connues
		@param $star int Nombre de stars du personnage
		@param $arme int Arme équipée du personnage
		@param $competence string Compétences du personnage
		@param $groupe int Groupe du joueur
		@param $hp int Nombre de HP du personnage
		@param $hp_max int Nombre de HP maximum du personnage
		@param $mp int Nombre de MP du personnage
		@param $mp_max int Nombre de MP maximum du personnage
		@param $melee int Valeur de la compétence de mélée
		@param $distance int Valeur de la compétence de tir à distance
		@param $esquive int Valeur de la compétence d'esquive
		@param $blocage int Valeur de la compétence de blocage
		@param $incantation int  Valeur de la compétence d'incantation
		@param $sort_vie int  Valeur de la compétence de la magie de la vie
		@param $sort_element int  Valeur de la compétence de la magie élémentaire
		@param $sort_mort int  Valeur de la compétence de nécromancie
		@param $identification int  Valeur de la compétence d'identification
		@param $forge int  Valeur de la compétence ??
		@param $craft int  Valeur de la compétence de fabrication d'objet
		@param $survie int  Valeur de la compétence de survie
		@param $facteur_magie double Valeur du coeficient de magie
		@param $facteur_vie double  Valeur du coeficient de magie de la vie
		@param $facteur_element double  Valeur du coeficient de magie élémentaire
		@param $facteur_mort double  Valeur du coeficient de nécromancie
		@param $resistance_magique int  Valeur de coeficient de résitance a la magie
		@param $regen_hp int Date de régénération des hp
		@param $maj_hp int Date d'augmentation des hp
		@param $maj_mp int Date d'augmentation des hp
		@param $point_shine int Nombre de points shine du personnage
		@param $quete string Liste des quête du personnage
		@param $quete_fini string Liste des quêtes achevées du personnage
		@param $derniere_connexion int Date de la dernière connexion
		@param $statut string Indique si un personnage est actif ou non
		@param $fin_ban int Indique la fin du ban du personnage
		@param $frag int Nombre de kills du personnage
		@param $crime float Nombre de points crime
		@param $amende int Amende sur le personnage
		@param $teleport boolean Téléportation Royale
		@param $cache_classe int Bonus Shine
		@param $cache_stat int Bonus Shine
		@param $cache_niveau int Bonus Shine
	*/
	function __construct($nom = '', $coord_x = 0, $coord_y = 0, $mort = 0, $exp = 0, $honneur = 0, $niveau = 0, $mdp = '', $rang = 7, 
				     $vie = 0, $force = 0, $dexterite = 0, $puissance = 0, $volonte = 0, $energie = 0, $race = '', $classe = '', $id_classe = 0,
				     $inventaire = '', $inventaire_slot = '', $pa = 1, $dernier_action = 0, $action_a = 0, $action_d = 0, $sort_jeu = '', 
				     $sort_combat = '', $comp_jeu = '', $comp_combat = '', $star = 0, $arme = 0, $competence = '', $groupe = 0, $hp = 0,
				     $hp_max = 0, $mp = 0, $mp_max = 0, $melee = 1, $distance = 1, $esquive = 1, $blocage = 1, $incantation = 1, 
				     $sort_vie = 0, $sort_elem = 0, $sort_mort = 0, $identification = 1, $forge = 1, $craft = 1, $survie = 1, $facteur_magie = 1,
				     $facteur_vie = 1, $facteur_elem = 1, $facteur_mort = 1, $resistance_magique = 1, $regen_hp = 0, $maj_hp = 0, 
				     $maj_mp = 0, $point_shine = 0, $quete = '', $quete_fini = '', $derniere_connexion = 0, $statut = 'actif', $fin_ban = 0, 
				     $frag = 0, $crime = 0, $amende = 0, $teleport = 'true', $cache_classe = 0, $cache_stat = 0, $cache_niveau = 0)
	{
		global $db;

		//Verification du nombre et du type d'argument pour construire l'objet adequat.
		if( (func_num_args() == 1) && is_numeric($nom) )
		{
			$this->id = $nom;
			$requete = 'SELECT nom, x, y, mort, exp, honneur, level, password, rang_royaume, vie, forcex, dexterite, 
			puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d,
			sort_jeu, sort_combat, comp_jeu, comp_combat, star, arme, competence, groupe, hp, hp_max, mp, mp_max, melee,
			distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, forge, craft, survie, facteur_magie, 
			facteur_sort_vie, facteur_sort_element, facteur_sort_mort, resistmagique, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, 
			dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau FROM perso WHERE ID = '.
			$this->getId();
			$requeteSQL = $db->query($requete);
			list($this->nom, $this->coord_x, $this->coord_y, $this->mort, $this->exp, $this->honneur, $this->niveau, $this->mdp,
			$this->rang, $this->vie, $this->force, $this->dexterite, $this->puissance, $this->volonte, $this->energie, $this->race,
			$this->classe, $this->id_classe, $this->inventaire, $this->inventaire_slot, $this->pa, $this->dernier_action, $this->action_a,
			$this->action_d, $this->sort_jeu, $this->sort_combat, $this->comp_jeu, $this->comp_combat, $this->star, $this->arme,
			$this->competence, $this->groupe, $this->hp, $this->hp_max, $this->mp, $this->mp_max, $this->melee, $this->distance,
			$this->esquive, $this->blocage, $this->incantation, $this->sort_vie, $this->sort_elem, $this->sort_mort, $this->identification,
			$this->forge, $this->craft, $this->survie, $this->facteur_magie, $this->facteur_vie, $this->facteur_elem, $this->facteur_mort,
			$this->resistance_magique, $this->regen_hp, $this->maj_hp, $this->maj_mp, $this->point_shine, $this->quete, $this->quete_fini,
			$this->derniere_connexion, $this->statut, $this->fin_ban, $this->frag, $this->crime, $this->amende, $this->teleport, 
			$this->cache_classe, $this->cache_stat, $this->cache_niveau) = $db->read_row($requeteSQL);
		}
		else
		{
			parent::__construct($nom, $coord_x, $coord_y);
			$this->mort = $mort;
			$this->exp = $exp;
			$this->honneur = $honneur;
			$this->niveau = $niveau;
			$this->mdp = $mdp;
			$this->rang = $rang;
			$this->vie = $vie;
			$this->force = $force;
			$this->dexterite = $dexterite;
			$this->puissance = $puissance;
			$this->volonte = $volonte;
			$this->energie = $energie;
			$this->race = $race;
			$this->classe = $classe;
			$this->id_classe = $id_classe;
			$this->inventaire = $inventaire;
			$this->pa = $pa;
			$this->dernier_action = $dernier_action;
			$this->action_a = $action_a;
			$this->action_d = $action_d;
			$this->sort_jeu = $sort_jeu;
			$this->sort_combat = $sort_combat;
			$this->comp_jeu = $comp_jeu;
			$this->comp_combat = $comp_combat;
			$this->star = $star;
			$this->arme = $arme;
			$this->competence = $competence;
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
			$this->sort_elem = $sort_elem;
			$this->sort_mort = $sort_mort;
			$this->identification = $identification;
			$this->forge = $forge;
			$this->craft = $craft;
			$this->survie = $survie;
			$this->facteur_magie = $facteur_magie;
			$this->facteur_vie = $facteur_vie;
			$this->facteur_elem = $facteur_elem;
			$this->facteur_mort = $facteur_mort;
			$this->resistance_magique = $resistance_magique;
			$this->regen_hp = $regen_hp;
			$this->maj_hp = $maj_hp;
			$this->maj_mp = $maj_mp;
			$this->point_shine = $point_shine;
			$this->quete = $quete;
			$this->quete_fini = $quete_fini;
			$this->derniere_connexion = $derniere_connexion;
			$this->statut = $statut;
			$this->fin_ban = $fin_ban;
			$this->frag = $frag;
			$this->crime = $crime;
			$this->amende = $amende;
			$this->teleport = $teleport;
			$this->cache_classe = $cache_classe;
			$this->cache_stat = $cache_stat;
			$this->cache_niveau = $cache_niveau;
		}
	}
	
	//! Fonction de Suppression
	function supprimer()
	{
		//@TODO ?Supprimer l'id de toutes les tables.
		parent::supprimer('perso');
	}
	
	//! Fonction permettant d'ajouter ou modifier un atome de la table.
	function sauver()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'UPDATE TABLE pnj SET '.$this->modifBase();
			$requete .= ' mort = "'.$this->mort.'", exp = "'.$this->exp.'", honneur = "'.$this->honneur.'", level = "'.$this->niveau.
			'", password"'.$this->mdp.'", rang_royaume = "'.$this->rang.'", vie = "'.$this->vie.'", forcex = "'.$this->force.
			'", dexterite = "'.$this->dexterite.'", puissance = "'.$this->puissance.'", volonte = "'.$this->volonte.'", energie = "'.$this->energie.
			'", race = "'.$this->race.'", classe = "'.$this->classe.'", classe_id = "'.$this->id_classe.'", inventaire = "'.$this->inventaire.
			'", inventaire_slot = "'.$this->inventaire_slot.'", pa = "'.$this->pa.'", dernieraction = "'.$this->dernier_action.
			'", action_a = "'.$this->action_a.'", action_d = "'.$this->action_d.'", sort_jeu = "'.$this->sort_jeu.'", sprt_combat = "'.$this->sort_combat.
			'", comp_ jeu = "'.$this->comp_jeu.'", comp_combat = "'.$this->comp_combat.'", star = "'.$this->star.'", arme = "'.$this->arme.
			'", competence = "'.$this->competence.'", groupe = "'.$this->groupe.'", hp = "'.$this->hp.'", hp_max = "'.$this->hp_max.
			'", mp = "'.$this->mp.'", mp_max = "'.$this->mp_max.'", melee = "'.$this->melee.'", distance = "'.$this->distance.
			'", esquive = "'.$this->esquive.'", blocage = "'.$this->blocage.'", incantaion = "'.$this->incantation.'", sort_vie = "'.$this->sort_vie.
			'", sort_element = "'.$this->sort_elem.'", sort_mort = "'.$this->sort_mort.'", identification = "'.$this->identification.'", forge = "'.$this->forge.
			'", craft = "'.$this->craft.'", survie = "'.$this->survie.'", facteur_magie = "'.$this->facteur_magie.'", facteur_sort_vie = "'.$this->facteur_vie.
			'", facteur_sort_element = "'.$this->facteur_elem.'", facteur_sort_mort = "'.$this->facteur_mort.'", resistmagique = "'.$this->resistance_magique.
			'", regen_hp = "'.$this->regen_hp.'", maj_hp = "'.$this->maj_hp.'", maj_mp = "'.$this->maj_mp.'", point_sso = "'.$this->point_shine.
			'", quete = "'.$this->quete.'", quete_fini = "'.$this-quete_fini.'", dernier_connexion = "'.$this->derniere_connexion.
			'", statut = "'.$this->statut.'", fin_ban = "'.$this->fin_ban.'", frag = "'.$this->frag.'", crime = "'.$this->crime.'", amende = "'.$this->amende.
			'", teleport_roi = "'.$this->teleport.'", cache_classe = "'.$this->cache_classe.'", cache_stat = "'.$this->cache_stat.
			'", cache_niveau = "'.$this->cache_niveau.'" WHERE id = '.$this->id;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO perso(nom, x, y, mort, exp, honneur, level, password, rang_royaume, vie, forcex, dexterite, 
			puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d,
			sort_jeu, sort_combat, comp_jeu, comp_combat, star, arme, competence, groupe, hp, hp_max, mp, mp_max, melee,
			distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, forge, craft, survie, facteur_magie, 
			facteur_sort_vie, facteur_sort_element, facteur_sort_mort, resistmagique, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, 
			dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau) VALUES(';
			$requete .= $this->insertBase().', "'.$this->mort.'", "'.$this->exp.'", "'.$this->honneur.'", "'.$this->niveau.'", "'.$this->mdp.'", "'.
			$this->rang.'", "'.$this->vie.'", "'.$this->force.'", "'.$this->dexterite.'", "'.$this->puissance.'", "'.$this->volonte.'", "'.
			$this->energie.'", "'.$this->race.'", "'.$this->classe.'", "'.$this->id_classe.'", "'.$this->inventaire.'", "'.$this->inventaire_slot.'", "'.
			$this->pa.'", "'.$this->dernier_action.'", "'.$this->action_a.'", "'.$this->action_d.'", "'.$this->sort_jeu.'", "'.$this->sort_combat.'", "'.
			$this->comp_jeu.'", "'.$this->comp_combat.'", "'.$this->star.'", "'.$this->arme.'", "'.$this->competence.'", "'.$this->groupe.'", "'.
			$this->hp.'", "'.$this->hp_max.'", "'.$this->mp.'", "'.$this->mp_max.'", "'.$this->melee.'", "'.$this->distance.'", "'.$this->esquive.'", "'.
			$this->blocage.'", "'.$this->incantation.'", "'.$this->sort_vie.'", "'.$this->sort_elem.'", "'.$this->sort_mort.'", "'.$this->identification.'", "'.
			$this->forge.'", "'.$this->craft.'", "'.$this->survie.'", "'.$this->facteur_magie.'", "'.$this->facteur_vie.'", "'.$this->facteur_elem.'", "'.
			$this->facteur_mort.'", "'.$this->resistance_magique.'", "'.$this->regen_hp.'", "'.$this->maj_hp.'", "'.$this->maj_mp.'", "'.
			$this->point_shine.'", "'.$this->quete.'", "';
			$requete .= $this-quete_fini.'", "'.$this->derniere_connexion.'", "'.$this->statut.'", "'.$this->fin_ban.'", "'.
			$this->frag.'", "'.$this->crime.'", "'.$this->amende.'", "'.$this->teleport.'", "'.$this->cache_classe.'", "'.$this->cache_stat.'", "'.
			$this->cache_niveau.'")';
			$db->query($requete);
			//Récupère le dernier id inséré.
			list($this->id) = mysql_fetch_row($db->query('SELECT LAST_INSERT_ID()'));
		}
	}
	
	//! toString
	function __toString()
	{
		//@TODO
		return parent::__toString();
	}
}
?>