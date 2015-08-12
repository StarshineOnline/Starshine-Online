<?php
/// @addtogroup Interface
/**
 * @file interf_factory.class.php
 * Fabrique (abstraite) pour les classes de l'interface
 */
 
include_once(root.'interface/interface.class.php');
include_once(root.'interface/interface_avance.class.php');

/**
 * Fabrique (abstraite) pour les classes de l'interface par défaut.
 * Correspond à l'interface par défaut.
 */
class interf_factory
{
	protected $css;
  /**
   * Fabrique de la fabrique abstraite : renvoie une instance de la bonne fabrique abstaite suivant l'interface à utiliser.
   */
  static function factory()
  {
  	global $db;
  	/// @todo passer à l'objet
  	if( array_key_exists('ID', $_SESSION) )
  	{
  		$requete = 'SELECT valeur FROM options WHERE nom = "interface" AND id_perso = '.$_SESSION['ID'];
  		$req = $db->query($requete);
  		$row = $db->read_array($req);
  		$index = $row[0];
		}
		else
			$index = 0;
		switch($index)
		{
		case 0:
		default:
    	return new interf_factory('star-jour');
    case 1:
    	return new interf_factory('star-nuit');
    case 2:
    	return new interf_factory_shine();
		}
  }
  static function get_noms()
  {
  	return array('Star - jour', 'Star - nuit', 'Shine');
	}
	protected function __construct($css)
	{
		$this->css = $css;
	}
  /**
   * interface du jeu
   */
  function creer_jeu()
  {
    include_once(root.'interface/interf_jeu.class.php');
    $ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
    switch($ajax)
    {
    case 1:
  		return new interf_jeu_ajax();
    case 2:
  		return new interf_jeu_tab();
    default:
    	return new interf_jeu($this->css);
		}
  }
  function creer_princ()
  {
		if( joueur::get_perso() )
			return $this->creer_jeu();
		else
			return $this->creer_index();
	}
  /**
   * interface de l'index
   */
  function creer_index()
  {
    $ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
    switch($ajax)
    {
    case 1:
    	include_once(root.'interface/interf_jeu.class.php');
  		return new interf_jeu_ajax(false);
    case 2:
    	include_once(root.'interface/interf_jeu.class.php');
  		return new interf_jeu_tab();
    default:
    	include_once(root.'interface/interf_index.class.php');
    	return new interf_index($this->css);
		}
  }
  function creer_index_infos()
  {
    include_once(root.'interface/interf_index.class.php');
    return new interf_index_infos();
	}
  function creer_index_captures()
  {
    include_once(root.'interface/interf_index.class.php');
    return new interf_index_captures();
	}
  function creer_index_compte()
  {
    include_once(root.'interface/interf_index.class.php');
    return new interf_index_compte();
	}
  function creer_index_perso($infos=null)
  {
    include_once(root.'interface/interf_index.class.php');
    return new interf_index_perso($infos);
	}
  function creer_index_infos_perso($race, $classe)
  {
    include_once(root.'interface/interf_index.class.php');
    return new interf_index_infos_perso($race, $classe);
	}
  /**
   * Renvoie la bonne instance de la classe pour afficher quelque chose dans le panneau droit
   * @param $titre  titre du panneau.
   */
  function creer_droite($titre)
  {
    return new interf_droite($titre);
  }
  /*
   */
  function creer_cadre_carte($carte=null)
  {
    include_once(root.'interface/interf_gauche.class.php');
    return new interf_cadre_carte($carte);
  }
  
  function creer_barre_perso()
  {
    return new interf_barre_perso();
  }
  /**
   * Méthode affichant le résultat d'une action
   */
  function afficher_resultat(&$princ, &$resultat, $ferme=true)
  {
    $msg = $resultat->get_message();
    $types = array('success','danger','warning','info');
    if( $msg )
      $princ->add( new interf_alerte($types[$resultat->get_type()],$ferme) )->add_message( nl2br($msg) );
  }
  /// Message d'accueil
  function creer_accueil()
  {
    include_once(root.'interface/interf_accueil.class.php');
    return new interf_accueil();
	}
	/// Interface de mort
	function creer_mort()
	{
    include_once(root.'interface/interf_mort.class.php');
    return new interf_mort();
	}
	/// Information sur les cases
	function creer_infos_case(&$case, $distance, $id_case, $reponse)
	{
    include_once(root.'interface/interf_infos_case.class.php');
    return new interf_infos_case($case, $distance, $id_case, $reponse);
	}
	/// Information sur les monstres
	function creer_monstre($entite, $actions)
	{
    return new interf_monstre($entite, $actions);
	}
	/// Information sur les personnages
	function creer_info_perso($entite, $actions)
	{
    return new interf_info_perso($entite, $actions);
	}
	/// Tour de guet
	function creer_tour(&$tour)
	{
    return new interf_tour($tour);
	}
	/// Bourgs & forts
	function creer_bourg_fort(&$construction)
	{
    include_once(root.'interface/interf_batiment.class.php');
    return new interf_bourg_fort($construction);
	}
	/// Armes de sièges
	function creer_arme_siege(&$construction)
	{
    return new interf_arme_siege($construction);
	}
	/// livres de sorts/compétences/artisanat
	function creer_livre($type, &$cible, $categorie, $actions)
	{
		switch( $type )
		{
		case 'alchimie':
			return $this->creer_livre_alchimie($actions);
		case 'forge':
			return $this->creer_livre_forge($actions);
		default:
			return $this->creer_livre_sortcomp($type, $cible, $categorie, $actions);
		}
    return new interf_livre_sortcomp($type, $cible, $categorie, $actions);
	}
	/// livres de sorts/compétences
	function creer_livre_sortcomp($type, &$cible, $categorie, $actions)
	{
    return new interf_livre_sortcomp($type, $cible, $categorie, $actions);
	}
	/// livres de recette d'alchimie
	function creer_livre_alchimie(&$cible, $actions)
	{
    return new interf_livre_alchimie($cible, $actions);
	}
	/// livres de recette de forge
	function creer_livre_forge(&$cible, $actions)
	{
    return new interf_livre_forge($cible, $actions);
	}
	/// Entrée dans la ville
	function creer_ville_entree(&$royaume)
	{
    return new interf_ville_entree($royaume);
	}
	/// Entrée dans la ville en cas d'amende
	function creer_ville_amende(&$royaume, &$amende, $erreur=false)
	{
    include_once(root.'interface/interf_ville_entree.class.php');
    return new interf_ville_amende($royaume, $amende, $erreur);
	}
	/// École de magie
	function creer_ecole_magie(&$royaume, $type)
	{
    return new interf_ecole_magie($royaume, $type);
	}
	/// École de combat
	function creer_ecole_combat(&$royaume, $type)
	{
    return new interf_ecole_combat($royaume, $type);
	}
	/// Forgeron
	function creer_forgeron(&$royaume, $categorie='epee')
	{
    return new interf_forgeron($royaume, $categorie);
	}
	/// Armurerie
	function creer_armurerie(&$royaume, $categorie='torse')
	{
    return new interf_armurerie($royaume, $categorie);
	}
	/// Enchanteur
	function creer_enchanteur(&$royaume, $categorie='grand')
	{
    return new interf_enchanteur($royaume, $categorie);
	}
	/// Dresseur
	function creer_dresseur(&$royaume, $categorie='cou')
	{
    return new interf_dresseur($royaume, $categorie);
	}
	/// Alchimiste
	function creer_alchimiste(&$royaume, &$case, $onglet='recherche', &$elt=null)
	{
    return new interf_alchimiste($royaume, $case, $onglet, $elt);
	}
	/// Hotel des ventes
	function creer_hotel_vente(&$royaume, $type='vente', $categorie='arme')
	{
    return new interf_hotel_vente($royaume, $type, $categorie);
	}
	/// Universite
	function creer_universite(&$royaume)
	{
    return new interf_universite($royaume);
	}
	/// Quartier général
	function creer_qg(&$royaume)
	{
    return new interf_qg($royaume);
	}
	/// Pierre de téléportaion
	function creer_tp(&$royaume, &$case)
	{
    return new interf_tp($royaume, $case);
	}
	/// Tribunal
	function creer_tribunal(&$royaume)
	{
    return new interf_tribunal($royaume);
	}
	/// Taverne
	function creer_taverne(&$royaume, &$case, &$type=null)
	{
    return new interf_taverne($royaume, $case, $type);
	}
	/// Taverne : repos
	function creer_taverne_repos(&$royaume)
	{
    include_once(root.'interface/interf_taverne.class.php');
    return new interf_taverne_repos($royaume);
	}
	/// Taverne : bar
	function creer_taverne_bar(&$royaume)
	{
    include_once(root.'interface/interf_taverne.class.php');
    return new interf_taverne_bar($royaume);
	}
	/// Taverne : jeux
	function creer_taverne_jeux($jeu=null, $mise=false, $score=0, $score_adv=0)
	{
    include_once(root.'interface/interf_taverne.class.php');
    return new interf_taverne_jeux($jeu, $mise, $score, $score_adv);
	}
	/// Ecurie
	function creer_ecurie(&$royaume, &$case)
	{
    return new interf_ecurie($royaume, $case);
	}
	/// Vente de terrain
	function creer_vente_terrain(&$royaume)
	{
    return new interf_vente_terrain($royaume);
	}
	/// Chantiers des terrains
	function creer_terrain_chantier(&$royaume)
	{
    return new interf_terrain_chantier($royaume);
	}
	/// Chantiers des terrains
	function creer_terrain(&$royaume, $id=0)
	{
    return new interf_terrain($royaume, $id);
	}
	/// Bureau des quetes
	function creer_bureau_quete(&$royaume, $case, $type='autre')
	{
    return new interf_bureau_quete($royaume, $case, $type);
	}
	/// Liste des quêtes au bureau des quetes
	function creer_tbl_quetes(&$royaume, $fournisseur='bureau_quete', $type=null)
	{
    include_once(root.'interface/interf_bureau_quete.class.php');
    return new interf_tbl_quetes($royaume, $type, $fournisseur);
	}
	/// Description d'une quête dansq le bureau des quetes
	function creer_bureau_quete_descr(&$quete, $royaume)
	{
    include_once(root.'interface/interf_bureau_quete.class.php');
    return new interf_bureau_quete_descr($quete, $royaume);
	}
	/// Liste des quêtes
	function creer_quetes(&$perso, $type='autre')
	{
    return new interf_quetes($perso, $type);
	}
	/// Liste des quêtes
	function creer_quetes_terrain(&$perso, $type='autre')
	{
    include_once(root.'interface/interf_quetes.class.php');
    return new interf_quetes_terrain($perso, $type);
	}
	/// Liste d'achat des sort hors combat
	function creer_achat_sort_jeu(&$royaume, $niveau=null)
	{
    include_once(root.'interface/interf_ecole_magie.class.php');
    return new interf_achat_sort_jeu($royaume, $niveau);
	}
	/// Liste d'achat des sort de combat
	function creer_achat_sort_combat(&$royaume, $niveau=null)
	{
    include_once(root.'interface/interf_ecole_magie.class.php');
    return new interf_achat_sort_combat($royaume, $niveau);
	}
	/// Liste d'achat des compétences hors combat
	function creer_achat_comp_jeu(&$royaume, $niveau=null)
	{
    include_once(root.'interface/interf_ecole_combat.class.php');
    return new interf_achat_comp_jeu($royaume, $niveau);
	}
	/// Liste d'achat des compétences de combat
	function creer_achat_comp_combat(&$royaume, $niveau=null)
	{
    include_once(root.'interface/interf_ecole_combat.class.php');
    return new interf_achat_comp_combat($royaume, $niveau);
	}
	/// Liste d'achat des armes
	function creer_achat_arme(&$royaume, $caterorie, $niveau=null)
	{
    include_once(root.'interface/interf_forgeron.class.php');
    return new interf_achat_arme($royaume, $caterorie, $niveau);
	}
	/// Liste d'achat des armures
	function creer_achat_armure(&$royaume, $caterorie, $niveau=null)
	{
    include_once(root.'interface/interf_armurerie.class.php');
    return new interf_achat_armure($royaume, $caterorie, $niveau);
	}
	/// Liste d'achat des accessoires
	function creer_achat_accessoire(&$royaume, $caterorie, $niveau=null)
	{
    include_once(root.'interface/interf_enchanteur.class.php');
    return new interf_achat_accessoire($royaume, $caterorie, $niveau);
	}
	/// Liste d'achat des objets de dressages
	function creer_achat_dressage(&$royaume, $caterorie, $niveau=null)
	{
    include_once(root.'interface/interf_dresseur.class.php');
    return new interf_achat_dressage($royaume, $caterorie, $niveau);
	}
	/// Liste d'achat des objets d'alchimie
	function creer_achat_alchimie(&$royaume)
	{
    include_once(root.'interface/interf_alchimiste.class.php');
    return new interf_achat_alchimie($royaume);
	}
	/// Liste d'achat des recettes d'alchimie
	function creer_achat_recette(&$royaume)
	{
    include_once(root.'interface/interf_alchimiste.class.php');
    return new interf_achat_recette($royaume);
	}
	/// Liste d'achat à l'hotel des ventes
	function creer_achat_hdv(&$royaume, $categorie)
	{
    include_once(root.'interface/interf_hotel_vente.class.php');
    return new interf_achat_hdv($royaume, $categorie);
	}
	/// Liste de vente à l'hotel des ventes
	function creer_vente_hdv(&$royaume, $categorie)
	{
    include_once(root.'interface/interf_hotel_vente.class.php');
    return new interf_vente_hdv($royaume, $categorie);
	}
	/// Boite de dialogue pour la vente d'objet en plusieur exemplaires
	function creer_vente_objets($objet, $categorie, $obj_perso)
	{
    include_once(root.'interface/interf_hotel_vente.class.php');
    return new interf_vente_objets($objet, $categorie, $obj_perso);
	}
	/// Boite de dialogue pour le dépôt d'une offre d'achat à l'hotel des ventes
	function creer_offre_achat($categorie, $taxe)
	{
    include_once(root.'interface/interf_hotel_vente.class.php');
    return new interf_offre_achat($categorie, $taxe);
	}
	/// Description des classes à l'université
	function creer_descr_classe(&$royaume, $id)
	{
    include_once(root.'interface/interf_universite.class.php');
    return new interf_descr_classe($royaume, $id);
	}
	/// Quête de donjon à l'université
	function creer_bibliotheque(&$royaume, $id)
	{
    include_once(root.'interface/interf_universite.class.php');
    return new interf_bibliotheque($royaume, $id);
	}
	/// Boite de dialogue pour les primes
	function creer_prime($id, &$cible)
	{
    include_once(root.'interface/interf_tribunal.class.php');
    return new interf_prime($id, $cible);
	}
	/// Coffre (dans le terrain de joueur)
	function creer_coffre(&$royaume, $construction)
	{
    include_once(root.'interface/interf_terrain.class.php');
    return new interf_coffre($royaume, $construction);
	}
	/// Laboratoire (dans le terrain de joueur)
	function creer_laboratoire(&$royaume, $construction)
	{
    include_once(root.'interface/interf_terrain.class.php');
    return new interf_laboratoire($royaume, $construction);
	}
	/// Grenier (dans le terrain de joueur)
	function creer_grenier(&$royaume, $construction)
	{
    include_once(root.'interface/interf_terrain.class.php');
    return new interf_grenier($royaume, $construction);
	}
  /**
   * @name Inventaire
   * Méthodes pour créer les interfaces de l'inventaire
   */
  /// @{}
  /**
   * Renvoie la bonne instance de la classe pour afficher l'inventaire
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire
   * @param $invent     Inventaire à afficher
   * @param $slot       Slot à afficher
   * @param $modif      Indique si on peut modifier l'interface.
   */
  function creer_inventaire(&$perso, $invent, $slot, $modif)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_inventaire($perso, $invent, $slot, $modif);
  }
  /**
   * Renvoie la bonne instance de la classe pour afficher les informations sur un objet (dans un popover)
   * @param $objet    objet sous forme textuelle
   */
  function creer_infos_objet($objet)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_infos_objet($objet);
  }
  /*
   */
  function creer_invent_equip(&$perso, $type, $modif)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_invent_equip($perso, $type, $modif, $id);
  }
  /*
   */
  function creer_invent_sac(&$perso, $type, $modif)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_invent_sac($perso, $type, $modif);
  }
  /*
   */
  function creer_vente_hotel(&$perso, $objet, $nombre)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_vente_hotel($perso, $objet, $nombre);
  }
  /*
   */
  function creer_enchasser(&$perso, $index)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_enchasser($perso, $index);
  }
  /*
   */
  function creer_liste_perso(&$persos, $page)
  {
    include_once(root.'interface/interf_liste_persos.class.php');
    return new interf_liste_persos($persos, $page);
  }
  /// @}
  function creer_vie_royaume()
  {
    return new interf_vie_royaume();
  }
  function creer_candidature()
  {
    include_once(root.'interface/interf_vie_royaume.class.php');
    return new interf_candidature();
  }
  function creer_vote()
  {
    include_once(root.'interface/interf_vie_royaume.class.php');
    return new interf_vote();
  }
  function creer_journal(&$perso, $options, $mois='actuel', $page=1)
  {
    return new interf_journal($perso, $options, $mois, $page);
  }
  function creer_journal_page(&$perso, $options, $mois='actuel', $page=1)
  {
    return new interf_journal_page($perso, $options, $mois, $page);
  }
  function creer_fiche_perso(&$perso, $actions=false, $onglet='carac')
  {
    return new interf_fiche_perso($perso, $actions, $onglet);
  }
  function creer_fiche_perso_carac(&$perso, $actions=false)
  {
    include_once(root.'interface/interf_fiche_perso.class.php');
    return new interf_fiche_perso_carac($perso, $actions);
  }
  function creer_fiche_perso_apt(&$perso)
  {
    include_once(root.'interface/interf_fiche_perso.class.php');
    return new interf_fiche_perso_apt($perso);
  }
  function creer_fiche_perso_stat(&$perso)
  {
    include_once(root.'interface/interf_fiche_perso.class.php');
    return new interf_fiche_perso_stat($perso);
  }
  function creer_fiche_perso_achiev(&$perso)
  {
    include_once(root.'interface/interf_fiche_perso.class.php');
    return new interf_fiche_perso_achiev($perso);
  }
  function creer_messagerie(&$perso, $type=null, $id_sujet=false, $page=null)
  {
    return new interf_messagerie($perso, $type, $id_sujet, $page);
  }
  function creer_liste_messages(&$perso, $type, $id_sujet=false, $page=null)
  {
    include_once(root.'interface/interf_messagerie.class.php');
    return new interf_liste_messages($perso, $type, $id_sujet, $page);
  }
  function creer_nouveau_message($type, $id=null)
  {
    include_once(root.'interface/interf_messagerie.class.php');
    return new interf_nouveau_message($type, $id);
  }
  function creer_echanges(&$perso, $actions=false)
  {
    return new interf_echanges($perso, $actions);
  }
  function creer_echange($id, $perso_action=false)
  {
    include_once(root.'interface/interf_echanges.class.php');
    return new interf_echange($id, $perso_action);
  }
  function creer_dressage(&$perso, $actions=true)
  {
    return new interf_dressage($perso, $actions);
  }
  function creer_liste_scripts($entite)
  {
    include_once(root.'interface/interf_script.class.php');
    return new interf_liste_scripts($entite);
  }
  function creer_script($script, $entite)
  {
    return new interf_script($script, $entite);
  }
  function creer_combat()
  {
    return new interf_combat();
  }
  function creer_groupe($id, &$groupe)
  {
    return new interf_groupe($id, $groupe);
  }
  function creer_batailles($id, &$groupe)
  {
    include_once(root.'interface/interf_groupe.class.php');
    return new interf_batailles($id, $groupe);
  }
  function creer_carte_bataille(&$bataille)
  {
    include_once(root.'interface/interf_groupe.class.php');
    return new interf_carte_bataille($bataille);
  }
  function creer_calendrier($nbr=15)
  {
    return new interf_calendrier($nbr);
  }
  function creer_carte_monde($id=false, $classe=false)
  {
    return new interf_carte_monde($id, $classe);
  }
  function creer_diplomatie()
  {
    return new interf_diplomatie();
  }
  function creer_bestiaire($terrain)
  {
    return new interf_bestiaire($terrain);
  }
  function creer_liste_monstres($terrain)
  {
    include_once(root.'interface/interf_bestiaire.class.php');
    return new interf_liste_monstres($terrain);
  }
  function creer_stats($stat='stat_lvl')
  {
  	return new interf_stats($stat);
	}
  function creer_classements($categorie, $type)
  {
  	return new interf_classements($categorie, $type);
	}
  function creer_classement_royaumes($categorie, $type)
  {
    include_once(root.'interface/interf_classements.class.php');
  	return new interf_classement_royaumes($type);
	}
  function creer_classement_groupes($type)
  {
    include_once(root.'interface/interf_classements.class.php');
  	return new interf_classement_groupes($type);
	}
  function creer_classement_perso_race($type)
  {
    include_once(root.'interface/interf_classements.class.php');
  	return new interf_classement_perso_race($type);
	}
  function creer_classement_perso_tous($type)
  {
    include_once(root.'interface/interf_classements.class.php');
  	return new interf_classement_perso_tous($type);
	}
  function creer_points_shine($categorie)
  {
  	return new interf_points_shine($categorie);
	}
  function creer_bonus_shine($categorie)
  {
    include_once(root.'interface/interf_points_shine.class.php');
  	return new interf_bonus_shine($categorie);
	}
  function creer_bonus_shine_config($id)
  {
    include_once(root.'interface/interf_points_shine.class.php');
  	return new interf_bonus_shine_config($id);
	}
  function creer_options($categorie)
  {
  	return new interf_options($categorie);
	}
  function creer_options_perso()
  {
    include_once(root.'interface/interf_options.class.php');
  	return new interf_options_perso();
	}
  function creer_options_joueur()
  {
    include_once(root.'interface/interf_options.class.php');
  	return new interf_options_joueur();
	}
  function creer_options_affichage()
  {
    include_once(root.'interface/interf_options.class.php');
  	return new interf_options_affichage();
	}
  function creer_votes_dons($categorie)
  {
  	return new interf_votes_dons($categorie);
	}
  function creer_votes()
  {
    include_once(root.'interface/interf_votes_dons.class.php');
  	return new interf_votes();
	}
  function creer_dons()
  {
    include_once(root.'interface/interf_votes_dons.class.php');
  	return new interf_dons();
	}
	
	
  /**
   * interface de gestion du royaume
   */
  function creer_royaume()
  {
    include_once(root.'interface/interf_jeu.class.php');
    $ajax = array_key_exists('ajax', $_GET) ? $_GET['ajax'] : 0;
    switch($ajax)
    {
    case 1:
  		return new interf_jeu_ajax();
    case 2:
  		return new interf_jeu_tab();
    default:
    	return new interf_royaume($this->css);
		}
  }
  ///
  function creer_bourse($onglet='achat')
  {
  	return new interf_bourse($onglet);
	}
  function creer_bourse_vente()
  {
    include_once(root.'interface/interf_bourse.class.php');
  	return new interf_bourse_vente();
	}
  function creer_bourse_achat()
  {
    include_once(root.'interface/interf_bourse.class.php');
  	return new interf_bourse_achat();
	}
  function creer_bourse_hist_vente()
  {
    include_once(root.'interface/interf_bourse.class.php');
  	return new interf_bourse_hist_vente();
	}
  function creer_bourse_hist_achat()
  {
    include_once(root.'interface/interf_bourse.class.php');
  	return new interf_bourse_hist_achat();
	}
  ///
  function creer_echange_roy($action=false)
  {
  	return new interf_echange_roy($action);
	}
  ///
  function creer_ressources($royaume)
  {
  	return new interf_ressources($royaume);
	}
  ///
  function creer_batiments_ville($royaume, $action=false)
  {
  	return new interf_batiments_ville($royaume, $action);
	}

	
  ///
  function creer_quete_royaume(&$royaume)
  {
  	return new interf_quete_royaume($royaume);
  }
  function creer_infos_quete(&$quete, &$royaume)
  {
    include_once(root.'interface/interf_quete_royaume.class.php');
  	return new interf_infos_quete($quete, $royaume);
  }

  ///
  function creer_entretien($royaume, $onglet='balance')
  {
  	return new interf_entretien($royaume, $onglet);
	}
  function creer_balance_hier(&$royaume)
  {
    include_once(root.'interface/interf_entretien.class.php');
  	return new interf_balance_hier($royaume);
	}
  function creer_recettes(&$royaume)
  {
    include_once(root.'interface/interf_entretien.class.php');
  	return new interf_recettes($royaume);
	}
  function creer_evol_gains(&$royaume)
  {
    include_once(root.'interface/interf_entretien.class.php');
  	return new interf_evol_gains($royaume);
	}
  function creer_repart_gains(&$royaume)
  {
    include_once(root.'interface/interf_entretien.class.php');
  	return new interf_repart_gains($royaume);

	}
  ///
  function creer_bat_drap($royaume, $onglet='invasions', $x=false, $y=false)
  {
  	return new interf_bat_drap($royaume, $onglet, $x, $y);
	}
  function creer_bd_invasions(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_invasions($royaume, $x, $y, $x, $y);
	}
  function creer_bd_constructions(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_constructions($royaume, $x, $y);
	}
  function creer_bd_ads(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_ads($royaume, $x, $y);
	}
  function creer_interf_bd_drapeaux(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_drapeaux($royaume, $x, $y);
	}
  function creer_bd_batiments(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_batiments($royaume, $x, $y);
	}
  function creer_bd_depot(&$royaume, $x=false, $y=false)
  {
    include_once(root.'interface/interf_bat_drap.class.php');
  	return new interf_bd_depot($royaume, $x, $y);
	}
  ///
  function creer_boutique_mil(&$royaume, $action=false)
  {
  	return new interf_boutique_mil($royaume, $action);
	}
  /// Gestion de la diplomatie
  function creer_gest_diplo(&$royaume, $change=false)
  {
  	return new interf_gest_diplo($royaume, $change);
	}
  function creer_demande_diplo(&$royaume)
  {
    include_once(root.'interface/interf_gest_diplo.class.php');
  	return new interf_demande_diplo($royaume);
	}
  /// Gestion des groupes
  function creer_roi_groupe(&$royaume, $onglet='royaume')
  {
  	return new interf_roi_groupe($royaume, $onglet);
	}
  function creer_roi_groupe_roy(&$royaume)
  {
    include_once(root.'interface/interf_roi_groupe.class.php');
  	return new interf_roi_groupe_roy($royaume);
	}
  function creer_roi_groupe_ext(&$royaume)
  {
    include_once(root.'interface/interf_roi_groupe.class.php');
  	return new interf_roi_groupe_ext($royaume);
	}
  function creer_roi_groupe_sans(&$royaume)
  {
    include_once(root.'interface/interf_roi_groupe.class.php');
  	return new interf_roi_groupe_sans($royaume);
	}
  /// Gestion des points de victoire
  function creer_points_victoire($action=true)
  {
  	return new interf_points_victoire($action);
	}
  /// Gestion des criminels
  function creer_gest_criminels(&$royaume)
  {
  	return new interf_gest_criminels($royaume);
	}
  /// Gestion 
  function creer_gestion_royaume(&$royaume, $action=true)
  {
  	return new interf_gestion_royaume($royaume, $action);
	}
  /// Gestion des bourgs & mines
  function creer_gestion_mines(&$royaume, $action=true, $carte=null)
  {
  	return new interf_gestion_mines($royaume, $action, $carte);
	}
  /// Gestion des batailles
  function creer_gest_batailles(&$royaume)
  {
  	return new interf_gest_batailles($royaume);
	}
  function creer_modif_bataille(&$bataille)
  {
    include_once(root.'interface/interf_gest_batailles.class.php');
  	return new interf_modif_bataille($bataille);
	}
  function creer_gerer_bataille(&$bataille)
  {
    include_once(root.'interface/interf_gest_batailles.class.php');
  	return new interf_gerer_bataille($bataille);
	}
  function creer_reperes(&$bataille, $x, $y, &$royaume)
  {
    include_once(root.'interface/interf_gest_batailles.class.php');
  	return new interf_reperes($bataille, $x, $y, $royaume);
	}
  function creer_drapeaux(&$royaume, $mag_factor=1, $mleft=0, $mtop=0)
  {
  	return new interf_drapeaux($royaume, $mag_factor, $mleft, $mtop);
	}
}

class interf_factory_shine extends interf_factory
{
	function __construct()
	{
		parent::__construct('shine');
	}
  
  function creer_barre_perso()
  {
    include_once(root.'interface/interf_barre_perso.class.php');
    return new interf_barre_perso_shine();
  }
  function creer_cadre_carte($carte=null)
  {
    include_once(root.'interface/interf_gauche.class.php');
    return new interf_cadre_carte_shine($carte);
  }
  /// Gestion de la diplomatie
  function creer_gest_diplo(&$royaume, $change=false)
  {
    include_once(root.'interface/interf_gest_diplo.class.php');
  	return new interf_gest_diplo_shine($royaume, $change);
	}
}
?>
