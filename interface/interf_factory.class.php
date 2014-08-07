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
  /**
   * Fabrique de la fabrique abstraite : renvoie une instance de la bonne fabrique abstaite suivant l'interface à utiliser.
   */
  static function factory()
  {
    return new interf_factory();
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
    	return new interf_jeu();
		}
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
	function creer_tour($tour)
	{
    return new interf_tour($tour);
	}
	/// livres de sorts/compétences
	function creer_livre_sortcomp($type, &$cible, $categorie, $actions)
	{
    return new interf_livre_sortcomp($type, $cible, $categorie, $actions);
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
	function creer_alchimiste(&$royaume, $onglet='recherche')
	{
    return new interf_alchimiste($royaume, $onglet);
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
	function creer_tp(&$royaume)
	{
    return new interf_tp($royaume);
	}
	/// Tribunal
	function creer_tribunal(&$royaume)
	{
    return new interf_tribunal($royaume);
	}
	/// Taverne
	function creer_taverne(&$royaume, $type='repos')
	{
    return new interf_taverne($royaume, $type);
	}
	/// Ecurie
	function creer_ecurie(&$royaume)
	{
    return new interf_ecurie($royaume);
	}
	/// Vente de terrain
	function creer_vente_terrain(&$royaume)
	{
    return new interf_vente_terrain($royaume);
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
    include_once(root.'interface/interf_liste_achat.class.php');
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
  function creer_vente_hotel(&$perso, $index)
  {
    include_once(root.'interface/interf_inventaire.class.php');
    return new interf_vente_hotel($perso, $index);
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
}
?>
