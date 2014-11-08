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
	protected $jauge_droite = false;
	protected $jauge_gauche = false;
	function __construct($prem_bouton='carte')
	{
		parent::__construct('div', 'cadre_gauche');
		//$princ = $this->add( new interf_bal_cont('div', 'cadre_gauche') );
		$this->disque = $this->add( new interf_bal_cont('div', 'depl_disque') );
		$this->barre_haut = $this->add( new interf_bal_cont('div', 'depl_barre_haut') );
		$this->barre_gauche = $this->add( new interf_bal_cont('div', 'depl_barre_gauche') );
		$this->centre = $this->add( new interf_bal_cont('div', 'depl_centre') );
		
		// menu
		$menu = $this->barre_gauche->add( new interf_menu(false, 'menu_panneaux', false) );
		switch($prem_bouton)
		{
		case 'carte':
			$carte = $menu->add( new interf_elt_menu('', 'deplacement.php', 'return charger(this.href);', 'menu_ville_carte') );
			$carte->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-carte') );
			$carte->get_lien()->add( new interf_txt('Carte') );
			break;
		case 'ville':
			$ville = $menu->add( new interf_elt_menu('', 'ville.php', 'return charger(this.href);', 'menu_ville_carte') );
			$ville->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-ville') );
			$ville->get_lien()->add( new interf_txt('Ville') );
			break;
		case 'mort':
			$mort = $menu->add( new interf_elt_menu('', 'mort.php', 'return charger(this.href);', 'menu_ville_carte') );
			$mort->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-mort') );
			$mort->get_lien()->add( new interf_txt('Mort') );
			break;
		}
		$livres = $menu->add( new interf_elt_menu('', 'livre.php', 'return charger(this.href);') );
		$livres->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-livres') );
		$livres->get_lien()->add( new interf_txt('Livres') );
		$quetes = $menu->add( new interf_elt_menu('', 'quete.php', 'return envoiInfo(this.href, \'information\');') );
		$quetes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-quetes') );
		$quetes->get_lien()->add( new interf_txt('Quetes') );
		$journal = $menu->add( new interf_elt_menu('', 'journal.php', 'return charger(this.href);') );
		$journal->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-journal') );
		$journal->get_lien()->add( new interf_txt('Journal') );
		$scripts = $menu->add( new interf_elt_menu('', 'actions.php', 'return charger(this.href);') );
		$scripts->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-scripts') );
		$scripts->get_lien()->add( new interf_txt('Scripts') );
		$inventaire = $menu->add( new interf_elt_menu('', 'inventaire.php', 'return charger(this.href);') );
		$inventaire->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-inventaire2') );
		$inventaire->get_lien()->add( new interf_txt('Inventaire') );
		$dressage = $menu->add( new interf_elt_menu('', 'gestion_monstre.php', 'return charger(this.href);') );
		$dressage->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-lapin') );
		$dressage->get_lien()->add( new interf_txt('Dressage') );
	}
	protected function set_icone_centre($icone, $url=false)
	{
		$centre = $this->disque->add( new interf_bal_smpl($url?'a':'span', '', 'depl_disque_centre', 'icone icone-'.$icone) );
		if( $url )
		{
			$centre->set_attribut('href', $url);
			$centre->set_attribut('onClick', 'return  charger(this.href);');
		}
		return $centre;
	}
	protected function set_img_centre($img, $url=false, $alt=false)
	{
		$centre = $this->disque->add( new interf_bal_cont($url?'a':'span', 'depl_disque_centre') );
		if( $url )
		{
			$centre->set_attribut('href', $url);
			$centre->set_attribut('onClick', 'return  charger(this.href);');
		}
		$centre->add( new interf_img($img, $alt) );
		return $centre;
	}
	protected function set_jauge_ext($valeur, $max, $style, $nom=false)
	{
		$this->set_jauge('ext', $valeur, $max, $style, $nom);
	}
	protected function set_jauge_int($valeur, $max, $style, $nom=false)
	{
		$this->set_jauge('int', $valeur, $max, $style, $nom);
	}
	private function set_jauge($type, $valeur, $max, $style, $nom)
	{
		/// @todo ajuster quand ça vaut 0
		$angle = round($valeur / $max * 360) - 180; 
		if( !$this->jauge_droite )
			$this->jauge_droite = $this->disque->add( new interf_bal_cont('div', 'jauge_droite') );
		$jauge_droite = $this->jauge_droite->add( new interf_bal_cont('div', '', 'jauge_'.$type.' jauge-'.$style) );
		if( $nom )
			$jauge_droite->set_tooltip($nom.$valeur.' / '.$max, 'right', '#cadre_gauche');
		if( $angle < 0 )
			$jauge_droite->set_attribut('style', 'transform: rotate('.$angle.'deg);{-webkit-transform: rotate('.$angle.'deg);');
		else if( $angle > 0)
		{
			if( !$this->jauge_gauche )
				$this->jauge_gauche = $this->disque->add( new interf_bal_cont('div', 'jauge_gauche') );
			$jauge_gauche = $this->jauge_gauche->add( new interf_bal_cont('div', '', 'jauge_'.$type.' jauge-'.$style) );
			if( $nom )
				$jauge_gauche->set_tooltip($nom.$valeur.' / '.$max, 'right', '#cadre_gauche');
			$jauge_gauche->set_attribut('style', 'transform: rotate('.$angle.'deg);{-webkit-transform: rotate('.$angle.'deg);');
		}
		$this->disque->add( new interf_bal_cont('div', 'jauge_'.$type.'_stop', 'depl_cache') );
	}
}

/// Classe pour la partie gauche de l'interface quand il faut montrer la carte
class interf_cadre_carte extends interf_gauche
{
	function __construct($carte=null)
	{
		global $db;
		$perso = joueur::get_perso();
		parent::__construct( is_ville($perso->get_x(), $perso->get_y(), true) ? 'ville' : 'carte' );
		// Options
		/// @todo passer à l'objet
		$requete = 'select nom, valeur from options where id_perso = '.$perso->get_id().' and nom in ("affiche_royaume", "desactive_atm", "desactive_atm_all", "cache_monstre", "no_sound")';
		$req = $db->query($requete);
		$opt = array('affiche_royaume'=>1, 'desactive_atm'=>1, 'desactive_atm_all'=>1, 'cache_monstre'=>1, 'no_sound'=>1);
		while( $row = $db->read_assoc($req) )
		{
			$opt[ $row['nom'] ] = $row['valeur'] ? '0' : '1';
		}
		// Menu carte
		$menu = $this->barre_haut->add( new interf_menu(false, 'menu_carte', false) );
		$royaumes = $menu->add( new interf_elt_menu('', 'deplacement.php?action=royaumes&valeur='.$opt['affiche_royaume'], 'return charger(this.href);') );
		$royaumes->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-drapeau') );
		$royaumes->get_lien()->set_attribut('title', 'Afficher / masquer les royaumes');
		$jour = $menu->add( new interf_elt_menu('', 'deplacement.php?action=jour&valeur='.$opt['desactive_atm_all'], 'return charger(this.href);') );
		$jour->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-lune') );
		$jour->get_lien()->set_attribut('title', 'Afficher / masquer les effets liés à l\'heure');
		$meteo = $menu->add( new interf_elt_menu('', 'deplacement.php?action=meteo&valeur='.$opt['desactive_atm'], 'return charger(this.href);') );
		$meteo->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-nuage') );
		$meteo->get_lien()->set_attribut('title', 'Afficher / masquer les effets atmosphériques');
		$son = $menu->add( new interf_elt_menu('', 'deplacement.php?action=son&valeur='.$opt['no_sound'], 'return charger(this.href);') );
		$son->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-son-fort') );
		$son->get_lien()->set_attribut('title', 'Activer / désactiver les effets sonores');
		$monstres = $menu->add( new interf_elt_menu('', 'deplacement.php?action=monstres&valeur='.$opt['cache_monstre'], 'return charger(this.href);') );
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
		/*$recharger = $this->disque->add( new interf_bal_smpl('a', '', 'depl_disque_centre', 'icone icone-rafraichir') );
		$recharger->set_attribut('href', 'deplacement.php?action=rafraichir');
		$recharger->set_attribut('onClick', 'return  charger(this.href);');*/
		$this->set_icone_centre('rafraichir', 'deplacement.php?action=rafraichir');
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