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
			$ville->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-ville') );
			$ville->get_lien()->add( new interf_txt('Ville') );
		}
		else
		{
			$carte = $menu->add( new interf_elt_menu('', 'deplacement.php', 'return envoiInfo(this.href, \'deplacement\');', 'menu_ville_carte') );
			$carte->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-carte') );
			$carte->get_lien()->add( new interf_txt('Carte') );
		}
		$livres = $menu->add( new interf_elt_menu('', 'livre.php', 'return envoiInfo(this.href, \'information\');') );
		$livres->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-livres') );
		$livres->get_lien()->add( new interf_txt('Livres') );
		$quetes = $menu->add( new interf_elt_menu('', 'quete.php', 'return envoiInfo(this.href, \'information\');') );
		$quetes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-quetes') );
		$quetes->get_lien()->add( new interf_txt('Quetes') );
		$journal = $menu->add( new interf_elt_menu('', 'journal.php', 'return envoiInfo(this.href, \'information\');') );
		$journal->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-journal') );
		$journal->get_lien()->add( new interf_txt('Journal') );
		$scripts = $menu->add( new interf_elt_menu('', 'actions.php', 'return envoiInfo(this.href, \'information\');') );
		$scripts->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-scripts') );
		$scripts->get_lien()->add( new interf_txt('Scripts') );
		$inventaire = $menu->add( new interf_elt_menu('', 'inventaire.php', 'return envoiInfo(this.href, \'information\');') );
		$inventaire->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-inventaire2') );
		$inventaire->get_lien()->add( new interf_txt('Inventaire') );
		$dressage = $menu->add( new interf_elt_menu('', 'gestion_monstre.php', 'return envoiInfo(this.href, \'information\');') );
		$dressage->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-lapin') );
		$dressage->get_lien()->add( new interf_txt('Dressage') );
	}
}

/// Classe pour la partie gauche de l'interface quand il faut montrer la carte
class interf_cadre_carte extends interf_gauche
{
	function __construct($carte=null)
	{
		$perso = joueur::get_perso();
		parent::__construct( is_ville($perso->get_x(), $perso->get_y()) == 1 );
		// Menu carte
		$menu = $this->barre_haut->add( new interf_menu(false, 'menu_carte', false) );
		$royaumes = $menu->add( new interf_elt_menu('', 'option_map.php?action=affiche_royaumes&val=0', 'return envoiInfo(this.href, \'depl_centre\');') );
		$royaumes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-drapeau') );
		$royaumes->get_lien()->set_attribut('title', 'Afficher / masquer les royaumes');
		$jour = $menu->add( new interf_elt_menu('', 'option_map.php?action=affiche_royaumes&val=0', 'return envoiInfo(this.href, \'depl_centre\');') );
		$jour->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-lune') );
		$jour->get_lien()->set_attribut('title', 'Afficher / masquer les effets liés à l\'heure');
		$meteo = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=time&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$meteo->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-nuage') );
		$royaumes->get_lien()->set_attribut('title', 'Afficher / masquer les effets atmosphériques');
		$meteo = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=time&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$meteo->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-son-fort') );
		$royaumes->get_lien()->set_attribut('title', 'Activer / désactiver les effets sonores');
		$monstres = $menu->add( new interf_elt_menu('', 'option_map.php?action=atm&effet=sky&val=1', 'return envoiInfo(this.href, \'depl_centre\');') );
		$monstres->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-oeil') );
		$monstres->get_lien()->set_attribut('title', 'Afficher / masquer les monstres');
		
		// Rose des vents
		$haut_gauche = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut_gauche', 'icone icone-haut-gauche') );
		$haut_gauche->set_attribut('href', 'deplacement.php?action=haut-gauche');
		$haut_gauche->set_attribut('onClick', 'return  charger(this.href);');
		$haut = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut', 'icone icone-haut') );
		$haut->set_attribut('href', 'deplacement.php?action=haut');
		$haut->set_attribut('onClick', 'return  charger(this.href);');
		$haut_droite = $this->disque->add( new interf_bal_smpl('a', '', 'depl_haut_droite', 'icone icone-haut-droite') );
		$haut_droite->set_attribut('href', 'deplacement.php?action=haut-droite');
		$haut_droite->set_attribut('onClick', 'return  charger(this.href);');
		$gauche = $this->disque->add( new interf_bal_smpl('a', '', 'depl_gauche', 'icone icone-gauche') );
		$gauche->set_attribut('href', 'deplacement.php?action=gauche');
		$gauche->set_attribut('onClick', 'return  charger(this.href);');
		$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_disque_centre', 'icone icone-rafraichir') );
		$recharger->set_attribut('href', 'deplacement.php?action=rafraichir');
		$recharger->set_attribut('onClick', 'return  charger(this.href);');
		$droite = $this->disque->add( new interf_bal_smpl('a', '', 'depl_droite', 'icone icone-droite') );
		$droite->set_attribut('href', 'deplacement.php?action=droite');
		$droite->set_attribut('onClick', 'return  charger(this.href);');
		$bas_gauche = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas_gauche', 'icone icone-bas-gauche') );
		$bas_gauche->set_attribut('href', 'deplacement.php?action=bas-gauche');
		$bas_gauche->set_attribut('onClick', 'return  charger(this.href);');
		$bas = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas', 'icone icone-bas') );
		$bas->set_attribut('href', 'deplacement.php?action=bas');
		$bas->set_attribut('onClick', 'return  charger(this.href);');
		$bas_droite = $this->disque->add( new interf_bal_smpl('a', '', 'depl_bas_droite', 'icone icone-bas-droite') );
		$bas_droite->set_attribut('href', 'deplacement.php?action=bas-droite');
		$bas_droite->set_attribut('onClick', 'return  charger(this.href);');
		
		$perso = joueur::get_perso();
		$x = $perso->get_x();
		$y = $perso->get_y();
		$this->centre->add( $carte ? $carte : new interf_carte($x, $y) );
	}
}
?>