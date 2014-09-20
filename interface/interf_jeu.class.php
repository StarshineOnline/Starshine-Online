<?php
/**
 * @file interf_jeu.class.php
 * Interface principale du jeu
 */
include_once(root.'interface/interf_sso.class.php');
include_once(root.'fonction/forum.inc.php');

/**
 * Classe pour l'interface de jeu
 */
class interf_jeu extends interf_sso_int
{
	protected $contenu;
  protected $gauche;
  protected $droite;

  function __construct()
  {
  	global $db;
    interf_sso_int::__construct();
    $perso = joueur::get_perso();
    $msg = $this->menu->add_elt( new interf_elt_menu('Messages', 'messagerie.php', 'return charger(this.href);') );
    $nbr_msg = messagerie::get_non_lu_total($_SESSION['ID']);
    $msg->get_lien()->add( new interf_bal_smpl('span', $nbr_msg ? $nbr_msg : '', 'nbr_msg', 'badge') );
    $ech = $this->menu->add_elt( new interf_elt_menu('Échanges', 'echange.php', 'return charger(this.href);') );
    /// TODO: passe à l'objet
    $requete = 'SELECT COUNT(*) FROM echange WHERE nouveau = TRUE AND ( (id_j2 = '.$perso->get_id().' AND statut = "proposition") OR (id_j1 = '.$perso->get_id().' AND statut = "finalisation") )';
    $req = $db->query($requete);
    $nbr_echg = $db->read_row($req)[0];
    $ech->get_lien()->add( new interf_bal_smpl('span', $nbr_echg ? $nbr_echg : '', 'nbr_echg', 'badge') );
    $this->menu->add_elt( new interf_elt_menu('Groupe', 'infogroupe.php', 'return charger(this.href);') );
    /// TODO: à améliorer
		if( $perso->get_grade()->get_rang() >= 7 )
		{
			$royaume = $this->menu->add_elt( new interf_nav_deroul('Royaume') );
			$royaume->add( new interf_elt_menu('Gestion du royaume', 'roi/index.php') );
			$royaume->add( new interf_elt_menu('Vie du royaume', 'vie_royaume.php', 'return charger(this.href);') );
		}
		else
    	$this->menu->add_elt( new interf_elt_menu('Royaume', 'vie_royaume.php', 'return charger(this.href);') );

    $this->menu->add_elt( new interf_elt_menu('Diplomatie', 'diplomatie.php', 'return charger(this.href);') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    $autres->add( new interf_elt_menu('Message d\'Accueil', 'message_accueil.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Cartes', 'royaume.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Bestiaire', 'liste_monstre.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Background', 'background.php', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Statistiques', 'stats2.php?graph=carte_royaume', 'return charger(this.href);') );
    $autres->add( new interf_elt_menu('Classement', 'classement.php', 'return charger(this.href);') );

    $this->contenu = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $this->contenu->add( new interf_bal_cont('header', 'perso') );
    $perso->add( new interf_barre_perso() );
    $cont_jeu = $this->contenu->add( new interf_bal_cont('main', 'contenu_jeu') );
    $this->gauche = $cont_jeu->add( new interf_bal_cont('section', 'deplacement') );
    $this->droite = $cont_jeu->add( new interf_bal_cont('section', 'information') );
    
    $this->code_js('maj_tooltips();');
  }
  function set_gauche($fils=null)
  {
  	global $G_interf;
    if( !$fils )
    {
    	if( joueur::get_perso()->est_mort() )
				$fils = $G_interf->creer_mort();
      else
				$fils = $G_interf->creer_cadre_carte();
		}
    return $this->gauche->add( $fils );
  }
  function set_droite($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_accueil();
    return $this->droite->add( $fils );
  }
  function maj_perso($complet=false) {}
  function maj_ville($dans_ville=false) {}
  function set_carte($fils)
  {
  	global $G_interf;
    return $this->gauche->add( $G_interf->creer_cadre_carte($fils) );
  }
  function set_dialogue($fils)
  {
  	$dlg = $this->contenu->add( new interf_bal_cont('div', 'modal', 'modal fade') );
  	$dlg->set_attribut('role', 'dialog');
  	$dlg->set_attribut('tabindex', '-1');
  	$dlg->set_attribut('aria-labelledby', 'modalLabel');
  	$dlg->code_js('$("#modal").modal("show");');
  	return $dlg->add( $fils );
  }
  function verif_mort(&$perso, $exit=true) 
  {
  	global $G_interf;
  	if( $perso->est_mort() )
  	{
  		$this->set_gauche( $G_interf->creer_mort() );
  		if($exit)
  			exit();
  		return false;
		}
		return true;
	}
  protected function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
    $nbr_posts = get_nbr_posts_forum(joueur::get_perso());
    $forum->get_lien()->add( new interf_bal_smpl('span', $nbr_posts ? $nbr_posts : '', 'nbr_posts', 'badge') );
  }
  function affiche($tab = 0)
	{
		// On remplie les parties gauche et droites si elles sont vides
		if( !$this->gauche->get_fils() )
			$this->set_gauche();
		if( !$this->droite->get_fils() )
			$this->set_droite();
		parent::affiche($tab);
	}
	function recharger_interface()
	{
		$this->code_js('document.location="interface.php";');
	}
	function maj_tooltips()
	{
	}
}

class interf_jeu_ajax extends interf_princ_ob
{
  function set_gauche($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_cadre_carte();
		$fils->add_js();
    $cont = $this->add( new interf_bal_cont('section', 'deplacement') );
    return $cont->add($fils);
  }
  function set_droite($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_accueil();
    $cont = $this->add( new interf_bal_cont('section', 'information') );
		$fils->add_js();
    return $cont->add($fils);
  }
  function maj_perso($complet=false)
	{
		global $db;
    $cont = $this->add( new interf_bal_cont('section', 'perso') );
    $cont->add( new interf_barre_perso() );
    if($complet)
    {
			$nbr_msg = messagerie::get_non_lu_total($_SESSION['ID']);
    	$this->add( new interf_bal_smpl('section', $nbr_msg ? $nbr_msg : '', 'nbr_msg') );
	    /// TODO: passe à l'objet
	    $perso = joueur::get_perso();
	    $requete = 'SELECT COUNT(*) FROM echange WHERE nouveau = TRUE AND ( (id_j2 = '.$perso->get_id().' AND statut = "proposition") OR (id_j1 = '.$perso->get_id().' AND statut = "finalisation") )';
	    $req = $db->query($requete);
	    $nbr_echg = $db->read_row($req)[0];
    	$this->add( new interf_bal_smpl('section', $nbr_echg ? $nbr_echg : '', 'nbr_echg') );
			$nbr_posts = get_nbr_posts_forum(joueur::get_perso());
			$this->add( new interf_bal_smpl('section', $nbr_posts ? $nbr_posts : '', 'nbr_posts') );
		}
	}
  function maj_ville($dans_ville=false)
	{
    $cont = $this->add( new interf_bal_cont('section', 'menu_ville_carte') );
    /// TODO: à centraliser
    $lien = $cont->add( new interf_bal_cont('a') );
    $lien->set_attribut('onClick', 'return charger(this.href);');
    $perso = joueur::get_perso();
    if( !$dans_ville && is_ville($perso->get_x(), $perso->get_y(), true) == 1 )
    {
    	$lien->set_attribut('href', 'ville.php');
    	$lien->add( new interf_bal_smpl('div', '', null, 'icone icone-ville') );
    	$lien->add( new interf_txt('Ville') );
		}
    else
    {
    	$lien->set_attribut('href', 'deplacement.php');
    	$lien->add( new interf_bal_smpl('div', '', null, 'icone icone-carte') );
    	$lien->add( new interf_txt('Carte') );
		}
	}
  function set_carte($fils)
  {
    $cont = $this->add( new interf_bal_cont('section', 'depl_centre') );
    return $cont->add($fils);
  }
  function set_dialogue($fils)
  {
    $cont = $this->add( new interf_bal_cont('section', 'modal') );
    return $cont->add($fils);
  }
  function verif_mort($perso) 
  {
  	global $G_interf;
  	if( $perso->est_mort() )
  	{
  		$this->set_gauche( $G_interf->creer_mort() );
  		exit();
		}
	}
  /// Affiche le début de l'élément, i.e. la partie située avant les éléments fils.
  protected function debut()
  {
    $this->ouvre('main');
  }
  /// Affiche la fin de l'élément, i.e. la partie située après les éléments fils.
  protected function fin()
  {
    $this->ferme('main');
  }
	function recharger_interface($url='interface.php')
	{
    $this->add( new interf_bal_smpl('section', $url, 'recharger') );
	}
	function maj_tooltips()
	{
    $this->add( new interf_bal_smpl('section', false, 'maj_tooltips') );
	}
}

class interf_jeu_tab extends interf_princ_ob
{
  function verif_mort($perso) 
  {
  	if( $perso->est_mort() )
  	{
  		recharger_interface();
  		exit();
		}
	}
	function recharger_interface()
	{
		interf_base::code_js('document.location="interface.php";');
	}
	function maj_tooltips()
	{
		interf_base::code_js('maj_tooltips();');
	}
}
?>