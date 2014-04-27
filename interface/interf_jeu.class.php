<?php
/**
 * @file interf_jeu.class.php
 * Interface principale du jeu
 */
include_once(root.'interface/interf_sso.class.php');

/**
 * Classe pour l'interface de jeu
 */
class interf_jeu extends interf_sso_int
{
  protected $gauche;
  protected $droite;

  function __construct()
  {
    interf_sso_int::__construct();
    $msg = $this->menu->add_elt( new interf_elt_menu('Messages', 'messagerie.php', 'return envoiInfo(this.href, \'information\');') );
    $msg->get_lien()->add( new interf_bal_smpl('span', 12, 'nbr_msg', 'badge') );
    $ech = $this->menu->add_elt( new interf_elt_menu('Échanges', 'liste_echange.php', 'return envoiInfo(this.href, \'information\');') );
    $ech->get_lien()->add( new interf_bal_smpl('span', 1, 'nbr_echg', 'badge') );
    $this->menu->add_elt( new interf_elt_menu('Groupe', 'infogroupe.php', 'return envoiInfo(this.href, \'information\');') );
    $perso = joueur::get_perso();
    /// TODO: à améliorer
		if( $perso->get_grade() == 1 || $perso->get_grade() == 6 )
		{
			$royaume = $this->menu->add_elt( new interf_nav_deroul('Royaume') );
			$royaume->add( new interf_elt_menu('Gestion du royaume', 'roi/index.php') );
			$royaume->add( new interf_elt_menu('Vie du royaume', 'vie_royaume.php', 'return envoiInfo(this.href, \'depl_centre\');') );
		}
		else
    	$this->menu->add_elt( new interf_elt_menu('Royaume', 'vie_royaume.php', 'return envoiInfo(this.href, \'depl_centre\');') );

    $this->menu->add_elt( new interf_elt_menu('Diplomatie', 'diplomatie.php', 'return affichePopUp(this.href);') );
    $autres = $this->menu->add_elt( new interf_nav_deroul('Autres') );
    $autres->add( new interf_elt_menu('Message d\'Accueil', 'message_accueil.php?affiche=all', 'return affichePopUp(this.href);') );
    $autres->add( new interf_elt_menu('Cartes', 'royaume.php', 'return affichePopUp(this.href);') );
    $autres->add( new interf_elt_menu('Bestiaire', 'liste_monstre.php', 'return affichePopUp(this.href);') );
    $autres->add( new interf_elt_menu('Background', 'background.php', 'return affichePopUp(this.href);') );
    $autres->add( new interf_elt_menu('Statistiques', 'stats2.php?graph=carte_royaume', 'return affichePopUp(this.href);') );
    $autres->add( new interf_elt_menu('Classement', 'classement.php', 'return affichePopUp(this.href);') );

    $cont = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $cont->add( new interf_bal_cont('header', 'perso') );
    $perso->add( new interf_barre_perso() );
    $cont_jeu = $cont->add( new interf_bal_cont('main', 'contenu_jeu') );
    $this->gauche = $cont_jeu->add( new interf_bal_cont('section', 'deplacement') );
    $this->droite = $cont_jeu->add( new interf_bal_cont('section', 'information') );
  }
  function set_gauche($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_cadre_carte();
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
  }
  function verif_mort($perso) 
  {
  	if( $perso->est_mort() )
  		exit();
	}
  protected function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Wiki', 'http://wiki.starshine-online.com/'), false);
    $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
  }
  function affiche($tab = 0)
	{
		// On remplie les parties gauche et droites si elles sont vides
		if( !$this->gauche->get_fils() )
			$his->set_gauche();
		if( !$this->droite->get_fils() )
			$this->set_droite();
		parent::affiche($tab);
	}
}

class interf_jeu_ajax extends interf_princ_ob
{
  function set_gauche($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_cadre_carte();
    $cont = $this->add( new interf_bal_cont('section', 'deplacement') );
    return $cont->add($fils);
  }
  function set_droite($fils=null)
  {
  	global $G_interf;
    if( !$fils )
      $fils = $G_interf->creer_accueil();
    $cont = $this->add( new interf_bal_cont('section', 'information') );
    return $cont->add($fils);
  }
  function maj_perso($complet=false)
	{
    $cont = $this->add( new interf_bal_cont('section', 'perso') );
    $cont->add( new interf_barre_perso() );
	}
  function maj_ville($dans_ville=false)
	{
    $cont = $this->add( new interf_bal_cont('section', 'menu_ville_carte') );
    /// TODO: à centraliser
    $lien = $cont->add( new interf_bal_cont('a') );
    $lien->set_attribut('onClick', 'return charger(this.href);');
    $perso = joueur::get_perso();
    if( !$dans_ville && is_ville($perso->get_x(), $perso->get_y()) == 1 )
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
  }
  function verif_mort($perso) 
  {
  	if( $perso->est_mort() )
  		exit();
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
}
?>