<?php
/**
 * @file interf_gauche.class.php
 * Classes pour la partie gauche de l'interface
 */  

/// Classe de base pour la partie gauche de l'interface
class interf_gauche extends interf_bal_cont
{
	protected $disque;
	protected $barre_haut;
	protected $barre_gauche;
	protected $centre;
	function __construct($bouton_ville=false)
	{
		parent::__construct('div', 'cadre_gauche');
		//$princ = $this->add( new interf_bal_cont('div', 'cadre_gauche') );
		$this->disque = $this->add( new interf_bal_cont('div', 'depl_disque') );
		$this->barre_haut = $this->add( new interf_bal_cont('div', 'depl_barre_haut') );
		$this->barre_gauche = $this->add( new interf_bal_cont('div', 'depl_barre_gauche') );
		$this->centre = $this->add( new interf_bal_cont('div', 'depl_centre') );
		
		// menu
		$menu = $this->barre_gauche->add( new interf_menu(false, 'menu_panneaux', false) );
		if( $bouton_ville )
		{
			$ville = $menu->add( new interf_elt_menu('', 'ville.php', 'return envoiInfo(this.href, \'depl_centre\');', 'menu_ville_carte') );
			$ville->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-ville') );
			$ville->get_lien()->add( new interf_txt('Ville') );
		}
		else
		{
			$carte = $menu->add( new interf_elt_menu('', 'deplacement.php', 'return envoiInfo(this.href, \'depl_centre\');', 'menu_ville_carte') );
			$carte->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-carte') );
			$carte->get_lien()->add( new interf_txt('Carte') );
		}
		$livres = $menu->add( new interf_elt_menu('', 'livre.php', 'return envoiInfo(this.href, \'information\');') );
		$livres->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-livres') );
		$livres->get_lien()->add( new interf_txt('Livres') );
		$quetes = $menu->add( new interf_elt_menu('', 'quete.php', 'return envoiInfo(this.href, \'information\');') );
		$quetes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-quetes') );
		$quetes->get_lien()->add( new interf_txt('Quetes') );
		$journal = $menu->add( new interf_elt_menu('', 'journal.php', 'return envoiInfo(this.href, \'information\');') );
		$journal->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-journal') );
		$journal->get_lien()->add( new interf_txt('Journal') );
		$scripts = $menu->add( new interf_elt_menu('', 'actions.php', 'return envoiInfo(this.href, \'information\');') );
		$scripts->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-scripts') );
		$scripts->get_lien()->add( new interf_txt('Scripts') );
		$inventaire = $menu->add( new interf_elt_menu('', 'inventaire.php', 'return envoiInfo(this.href, \'information\');') );
		$inventaire->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-inventaire2') );
		$inventaire->get_lien()->add( new interf_txt('Inventaire') );
		$dressage = $menu->add( new interf_elt_menu('', 'gestion_monstre.php', 'return envoiInfo(this.href, \'information\');') );
		$dressage->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-lapin') );
		$dressage->get_lien()->add( new interf_txt('Dressage') );
	}
}

/// Classe pour la partie gauche de l'interface quand il faut montrer la carte
class interf_cadre_carte extends interf_gauche
{
	function __construct($carte=null)
	{
		parent::__construct();
		// Menu carte
		$menu = $this->barre_haut->add( new interf_menu(false, 'menu_carte', false) );
		$royaumes = $menu->add( new interf_elt_menu('', 'option_map.php?action=affiche_royaumes&val=0', 'return envoiInfo(this.href, \'depl_centre\');') );
		$royaumes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-drapeau') );
		$royaumes->get_lien()->set_attribut('title', 'Afficher / masquer les royaumes');
		$jour = $menu->add( new interf_elt_menu('', 'option_map.php?action=affiche_royaumes&val=0', 'return envoiInfo(this.href, \'depl_centre\');') );
		$jour->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-lune') );
		$jour->get_lien()->set_attribut('title', 'Afficher / masquer les effets liés à l\'heure');
		$meteo = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=time&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$meteo->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-nuage') );
		$royaumes->get_lien()->set_attribut('title', 'Afficher / masquer les effets atmosphériques');
		$meteo = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=time&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$meteo->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-son-fort') );
		$royaumes->get_lien()->set_attribut('title', 'Activer / désactiver les effets sonores');
		$monstres = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=sky&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$monstres->get_lien()->add( new interf_bal_smpl('div', '', null, 'icon icon-oeil') );
		$monstres->get_lien()->set_attribut('title', 'Afficher / masquer les monstres');
		
		// Rose des vents
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut_gauche', 'icon icon-haut-gauche') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut', 'icon icon-haut') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut_droite', 'icon icon-haut-droite') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_gauche', 'icon icon-gauche') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_disque_centre', 'icon icon-rafraichir') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_droite', 'icon icon-droite') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas_gauche', 'icon icon-bas-gauche') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas', 'icon icon-bas') );
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas_droite', 'icon icon-bas-droite') );
		
		$perso = joueur::get_perso();
		$x = $perso->get_x();
		$y = $perso->get_y();
		$this->centre->add( $carte ? $carte : new interf_carte($x, $y) );
	}
}
?>