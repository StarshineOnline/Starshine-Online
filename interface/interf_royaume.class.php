<?php
/**
 * @file interf_royaume.class.php
 * Interface principale du jeu
 */
include_once(root.'interface/interf_sso.class.php');


/**
 * Classe pour l'interface de jeu
 */
class interf_royaume extends interf_sso_int
{
	protected $contenu;
	protected $cont_gestion;
	const prefixe_fichiers = '../';
	
  function __construct($css)
  {
  	global $Trace;
    interf_sso_int::__construct($css);
    $this->css(static::prefixe_fichiers.'css/roi.css');
    $perso = joueur::get_perso();
    $royaume = new royaume($Trace[$perso->get_race()]['numrace']);
    $vivant = $perso->get_hp() > 0;
    if( $perso->get_rang() == 6 )
    	$roi = $eco = $mil = true;
    else
    {
    	$roi = false;
    	$eco = $royaume->get_ministre_economie() == $perso->get_id();
    	$mil = $royaume->get_ministre_militaire() == $perso->get_id();
		}
		if( $capitale = verif_ville($perso->get_x(), $perso->get_y(), $royaume->get_id()) )
		{
			$lieu = true;
		}
		else if($batiment = verif_batiment($perso->get_x(), $perso->get_y(), $royaume->get_id()))
		{
			if($batiment['type'] == 'fort' OR $batiment['type'] == 'bourg')
			{
				$bourg = new batiment($batiment['id_batiment']);
				$lieu = $bourg->has_bonus('royaume');
			}
		}
		$non_raz = !$royaume->is_raz();
    
    /*$msg = $this->menu->add_elt( new interf_elt_menu('Messages', 'messagerie.php', 'return charger(this.href);') );
    $nbr_msg = messagerie::get_non_lu_total($_SESSION['ID']);
    $msg->get_lien()->add( new interf_bal_smpl('span', $nbr_msg ? $nbr_msg : '', 'nbr_msg', 'badge') );*/
    if( $eco && $non_raz )
    {
	    $rsrc = $this->menu->add_elt( new interf_nav_deroul('Ressources') );
	    $rsrc->add( new interf_elt_menu('Bourse', 'bourse.php') );
	    $rsrc->add( new interf_elt_menu('Échanges', 'echanges.php') );//gestion_royaume.php?direction=echange
	    $rsrc->add( new interf_elt_menu('Ressources', 'ressources.php') );
	    $rsrc->add( new interf_elt_menu('Mines', 'mine.php') );
	    $economie = $this->menu->add_elt( new interf_nav_deroul('Économie') );
	    $economie->add( new interf_elt_menu('Bâtiments de la ville', 'batiments_ville.php') );
	    $economie->add( new interf_elt_menu('Entretien & taxe', 'entretien.php') );//+ taxe.php
	    if( $roi && $capitale && $vivant )
	    	$economie->add( new interf_elt_menu('Drapeaux', 'drapeaux.php') );
	    $economie->add( new interf_elt_menu('Quêtes', 'quete.php') );
		}
    $militaire = $this->menu->add_elt( new interf_nav_deroul('Militaire') );
    if( $lieu && $vivant )
    	$militaire->add( new interf_elt_menu('Bâtiments hors ville', 'construction.php') );
    if( $non_raz )
    {
	    $militaire->add( new interf_elt_menu('Boutique militaire', 'boutique_militaire.php') );
	    $militaire->add( new interf_elt_menu('Buffs & debuffs bâtiments', 'buffs_batiments.php') );
	    if( $mil )
	    	$militaire->add( new interf_elt_menu('Batailles', 'gestion_bataille.php') );
		}
    $com = $this->menu->add_elt( new interf_nav_deroul('Communication') );
    $com->add( new interf_elt_menu('Diplomatie', 'diplomatie.php') );
    if( $mil )
    	$com->add( new interf_elt_menu('Mot du roi', 'motk.php') );
    $com->add( new interf_elt_menu('Propagande', 'propagande.php') );
    $com->add( new interf_elt_menu('Groupes', 'gestion_groupe.php') );
    $divers = $this->menu->add_elt( new interf_nav_deroul('Divers') );
    $divers->add( new interf_elt_menu('Carte', 'carte.php') );
    if( $roi )
    {
	    $divers->add( new interf_elt_menu('Criminels', 'criminels.php') );
	    if( $non_raz )
	    	$divers->add( new interf_elt_menu('Points de victoire', 'point_victoire.php') );
		}
    $divers->add( new interf_elt_menu('Affaires du royaume', 'gestion_royaume.php') );
    
    $this->contenu = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $this->contenu->add( new interf_bal_cont('header', 'royaume') );
    $perso->add( new interf_barre_royaume() );
    $cont_jeu = $this->contenu->add( new interf_bal_cont('main', 'contenu_jeu') );
    $this->cont_gestion = $cont_jeu->add( new interf_bal_cont('section', 'gestion_royaume') );
  }
  protected function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Jeu', false, 'http:://www.starshine-online.com/interface.php'), false);
    $this->menu->add_elt(new interf_elt_menu('Aide', 'http://wiki.starshine-online.com/'), false);
    $forum = $this->menu->add_elt(new interf_elt_menu('Forum', 'http://forum.starshine-online.com/'), false);
    $nbr_posts = get_nbr_posts_forum(joueur::get_perso());
    $forum->get_lien()->add( new interf_bal_smpl('span', $nbr_posts ? $nbr_posts : '', 'nbr_posts', 'badge') );
	}
	protected function menu_joueur($menu_joueur)
	{
	}
	function set_gestion($fils)
	{
    return $this->cont_gestion->add($fils);
	}
  function maj_royaume($complet=false)
  {
	}
}

class interf_barre_royaume extends interf_bal_cont
{
	protected $royaume;
	protected $ressources;
  function __construct()
  {
  	global $Trace;
  	$perso = joueur::get_perso();
  	$this->royaume = new royaume($Trace[$perso->get_race()]['numrace']);
    interf_bal_cont::__construct('div', 'perso_contenu');
    
    $this->aff_infos();
    $this->aff_ressources();
  }
  protected function aff_infos()
  {
  	$div = $this->add( new interf_bal_cont('div', 'infos_royaume') );
  	$lst1 = $div->add( new interf_bal_cont('ul') );
  	$habitants = $lst1->add( new interf_bal_cont('li') );
  	$habitants->add( new interf_bal_smpl('strong', 'Nombre d\'habitants : ') );
  	$habitants->add( new interf_bal_smpl('span', $this->royaume->get_habitants(), 'habitants') );
  	$actifs = $lst1->add( new interf_bal_cont('li') );
  	$actifs->add( new interf_bal_smpl('strong', 'Habitants très actifs : ') );
  	$actifs->add( new interf_bal_smpl('span', $this->royaume->get_habitants_actif(), 'actifs') );
  	$faacteur = $lst1->add( new interf_bal_cont('li') );
  	$faacteur->add( new interf_bal_smpl('strong', 'Facteur d\'entretien : ') );
  	$faacteur->add( new interf_bal_smpl('span', $this->royaume->get_facteur_entretien(), 'facteur') );
  	$lst2 = $div->add( new interf_bal_cont('ul') );
  	$taxe = $lst2->add( new interf_bal_cont('li') );
  	$taxe->add( new interf_bal_smpl('strong', 'Taux de taxe : ') );
  	$taxe->add( new interf_bal_smpl('span', $this->royaume->get_taxe().'%', 'taxe') );
  	$entretien = $lst2->add( new interf_bal_cont('li') );
  	$entretien->add( new interf_bal_smpl('strong', 'Entretien : ') );
  	$entretien->add( new interf_bal_smpl('span', /*$this->royaume->get_star()*/'à faire', 'entretien') );
  	$conso = $lst2->add( new interf_bal_cont('li') );
  	$conso->add( new interf_bal_smpl('strong', 'Consommation : ') );
  	$conso->add( new interf_bal_smpl('span', $this->royaume->get_conso_food(), 'nouriture_besoin') );
  	$lst3 = $div->add( new interf_bal_cont('ul') );
  	$rang = $lst3->add( new interf_bal_cont('li') );
  	$rang->add( new interf_bal_smpl('strong', 'Rang : ') );
  	$rang->add( new interf_bal_smpl('span', /*$this->royaume->get_star()*/'à faire', 'rang') );
  	$pv = $lst3->add( new interf_bal_cont('li') );
  	$pv->add( new interf_bal_smpl('strong', 'Points de victoire : ') );
  	$pv->add( new interf_bal_smpl('span', $this->royaume->get_point_victoire_total(), 'pts_victoire') );
  	$pr = $lst3->add( new interf_bal_cont('li') );
  	$pr->add( new interf_bal_smpl('strong', 'Points de royaume : ') );
  	$pr->add( new interf_bal_smpl('span', $this->royaume->get_point_victoire(), 'pts_royaume') );
	}
	protected function aff_ressources()
	{
  	$this->ressources = $this->add( new interf_bal_cont('div', 'ressources') );
  	$this->ajout_ressource('star', 'Stars', $this->royaume->get_star());
  	$this->ajout_ressource('bois', 'Bois', $this->royaume->get_bois());
  	$this->ajout_ressource('pierre', 'Pierre', $this->royaume->get_pierre());
  	$this->ajout_ressource('essence', 'Essence Magique', $this->royaume->get_essence());
  	$this->ajout_ressource('nourriture', 'Nourriture', $this->royaume->get_food());
  	$this->ajout_ressource('eau', 'Eau', $this->royaume->get_eau());
  	$this->ajout_ressource('sable', 'Sable', $this->royaume->get_sable());
  	$this->ajout_ressource('charbon', 'Charbon', $this->royaume->get_charbon());
	}
	protected function ajout_ressource($type, $nom, $quantite)
	{
  	$span = $this->ressources->add( new interf_bal_smpl('span', $quantite, false, 'ressource '.$type) );
  	$span->set_tooltip($nom);
	}
}
?>