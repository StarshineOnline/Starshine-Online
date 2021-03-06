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
	const page = 'roi/index.php';
	
  function __construct($css)
  {
  	global $Trace;
    interf_sso_int::__construct($css);
    $this->javascript(static::prefixe_fichiers.'javascript/jquery/highcharts.js');
    $this->javascript(static::prefixe_fichiers.'javascript/roi.js');
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
    
    $msg = $this->menu->add_elt( new interf_elt_menu('Messages', '../messagerie.php') );
    $nbr_msg = messagerie::get_non_lu_total($_SESSION['ID']);
    $msg->get_lien()->add( new interf_bal_smpl('span', $nbr_msg ? $nbr_msg : '', 'nbr_msg', 'badge') );
    if( $eco && $non_raz )
    {
	    $rsrc = $this->menu->add_elt( new interf_nav_deroul('Ressources') );
	    $rsrc->add( new interf_elt_menu('Bourse', 'bourse.php', 'return charger(this.href);') );
	    $rsrc->add( new interf_elt_menu('Échanges', 'echanges.php', 'return charger(this.href);') );
	    $rsrc->add( new interf_elt_menu('Ressources', 'ressources.php', 'return charger(this.href);') );
	    $rsrc->add( new interf_elt_menu('Mines', 'mine.php', 'return charger(this.href);') );
	    $economie = $this->menu->add_elt( new interf_nav_deroul('Économie') );
	    $economie->add( new interf_elt_menu('Bâtiments de la ville', 'batiments_ville.php', 'return charger(this.href);') );
	    $economie->add( new interf_elt_menu('Entretien', 'entretien.php', 'return charger(this.href);') );
	    $economie->add( new interf_elt_menu('Gestion des taxes', 'taxe.php', 'return charger(this.href);') );
	    if( $roi && $capitale && $vivant )
	    	$economie->add( new interf_elt_menu('Drapeaux', 'drapeaux.php', 'return charger(this.href);') );
		}
    $militaire = $this->menu->add_elt( new interf_nav_deroul('Militaire') );
    if( $lieu && $vivant )
    	$militaire->add( new interf_elt_menu('Bâtiments hors ville', 'construction.php', 'return charger(this.href);') );
    if( $non_raz )
    {
	    $militaire->add( new interf_elt_menu('Boutique militaire', 'boutique_militaire.php', 'return charger(this.href);') );
	    if( $mil )
	    	$militaire->add( new interf_elt_menu('Batailles', 'gestion_bataille.php', 'return charger(this.href);') );
		}
    $militaire->add( new interf_elt_menu('Carte', 'carte.php', 'return charger(this.href);') );
    $com = $this->menu->add_elt( new interf_nav_deroul('Communication') );
    $com->add( new interf_elt_menu('Diplomatie', 'diplomatie.php', 'return charger(this.href);') );
    if( $mil )
    	$com->add( new interf_elt_menu('Mot du roi', 'motk.php', 'return charger(this.href);') );
    $com->add( new interf_elt_menu('Propagande', 'propagande.php', 'return charger(this.href);') );
    $com->add( new interf_elt_menu('Groupes', 'gestion_groupe.php', 'return charger(this.href);') );
    $divers = $this->menu->add_elt( new interf_nav_deroul('Divers') );
    $divers->add( new interf_elt_menu('Affaires du royaume', 'gestion_royaume.php', 'return charger(this.href);') );
	  $divers->add( new interf_elt_menu('Quêtes', 'quete.php', 'return charger(this.href);') );
    if( $roi )
    {
	    if( $non_raz )
	    	$divers->add( new interf_elt_menu('Points de victoire', 'point_victoire.php', 'return charger(this.href);') );
	    $divers->add( new interf_elt_menu('Criminels', 'criminels.php', 'return charger(this.href);') );
		}
    
    $this->contenu = $this->add( new interf_bal_cont('div', 'contenu') );
    $perso = $this->contenu->add( new interf_bal_cont('header', 'royaume') );
    $perso->add( new interf_barre_royaume() );
    $cont_jeu = $this->contenu->add( new interf_bal_cont('main', 'contenu_jeu') );
    $this->cont_gestion = $cont_jeu->add( new interf_bal_cont('section', 'gestion_royaume') );
  }
  protected function menu_droite()
  {
    $this->menu->add_elt(new interf_elt_menu('Jeu', '../interface.php'), false);
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
	function maj_tooltips()
	{
    $this->code_js('maj_tooltips();');
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
  	$facteur = $lst1->add( new interf_bal_cont('li') );
  	$facteur->add( new interf_bal_smpl('strong', 'Facteur d\'entretien : ') );
  	$facteur->add( new interf_bal_smpl('span', round($this->royaume->get_facteur_entretien(),2), 'facteur') );
  	if( $this->royaume->get_facteur_entretien_th() > $this->royaume->get_facteur_entretien() )
  		$facteur->add( new interf_bal_smpl('span', '', false, 'icone icone-augmente') )->set_tooltip('devrait augmenter');
  	else if( $this->royaume->get_facteur_entretien_th() < $this->royaume->get_facteur_entretien() )
  		$facteur->add( new interf_bal_smpl('span', '', false, 'icone icone-diminue') )->set_tooltip('devrait diminuer');
  	else
  		$facteur->add( new interf_bal_smpl('span', '', false, 'icone icone-stable') )->set_tooltip('devrait rester stable');
  	$lst2 = $div->add( new interf_bal_cont('ul') );
  	$taxe = $lst2->add( new interf_bal_cont('li') );
  	$taxe->add( new interf_bal_smpl('strong', 'Taux de taxe : ') );
  	$taxe->add( new interf_bal_smpl('span', $this->royaume->get_taxe().'%', 'taxe') );
  	$entretien = $lst2->add( new interf_bal_cont('li') );
  	$entretien->add( new interf_bal_smpl('strong', 'Entretien : ') );
  	$entretien->add( new interf_bal_smpl('span', $this->royaume->get_entretien(), 'entretien') );
  	$conso = $lst2->add( new interf_bal_cont('li') );
  	$conso->add( new interf_bal_smpl('strong', 'Consommation : ') );
  	$conso->add( new interf_bal_smpl('span', $this->royaume->get_conso_food(), 'nouriture_besoin') );
  	if( $this->royaume->get_conso_food_th() > $this->royaume->get_conso_food() )
  		$conso->add( new interf_bal_smpl('span', '', false, 'icone icone-augmente') )->set_tooltip('devrait augmenter');
  	else if( $this->royaume->get_conso_food_th() < $this->royaume->get_conso_food() )
  		$conso->add( new interf_bal_smpl('span', '', false, 'icone icone-diminue') )->set_tooltip('devrait diminuer');
  	else
  		$conso->add( new interf_bal_smpl('span', '', false, 'icone icone-stable') )->set_tooltip('devrait rester stable');
  	$lst3 = $div->add( new interf_bal_cont('ul') );
  	$rang = $lst3->add( new interf_bal_cont('li') );
  	$rang->add( new interf_bal_smpl('strong', 'Rang : ') );
  	$rang->add( new interf_bal_smpl('span', $this->royaume->get_rang(), 'rang') );
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
  	$span->set_tooltip($nom, 'left');
	}
}
?>
