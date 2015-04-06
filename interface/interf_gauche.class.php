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
		$this->disque = $this->add( new interf_bal_cont('div', 'depl_disque', 'aide') );
		$this->barre_haut = $this->add( new interf_bal_cont('div', 'depl_barre_haut') );
		$this->barre_gauche = $this->add( new interf_bal_cont('div', 'depl_barre_gauche') );
		$this->centre = $this->add( new interf_bal_cont('div', 'depl_centre') );
		
		// menu
		$menu = $this->barre_gauche->add( new interf_menu(false, 'menu_panneaux', 'aide') );
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
		$quetes = $menu->add( new interf_elt_menu('', 'quete.php', 'return charger(this.href);') );
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
		// max pour ajuster quand ça vaut 0
		if( $max == '%' )
			$angle = max(round($valeur / 100 * 360) - 180, -177); 
		else
			$angle = max(round($valeur / $max * 360) - 180, -177); 
		if( !$this->jauge_droite )
			$this->jauge_droite = $this->disque->add( new interf_bal_cont('div', 'jauge_droite') );
		$jauge_droite = $this->jauge_droite->add( new interf_bal_cont('div', '', 'jauge_'.$type.' jauge-'.$style) );
		if( $nom )
			$jauge_droite->set_tooltip($nom.($valeur?$valeur:'0').($max=='%'?' %':' / '.$max), 'right', '#cadre_gauche');
		if( $angle < 0 )
			$jauge_droite->set_attribut('style', 'transform: rotate('.$angle.'deg);{-webkit-transform: rotate('.$angle.'deg);');
		else if( $angle > 0)
		{
			if( !$this->jauge_gauche )
				$this->jauge_gauche = $this->disque->add( new interf_bal_cont('div', 'jauge_gauche') );
			$jauge_gauche = $this->jauge_gauche->add( new interf_bal_cont('div', '', 'jauge_'.$type.' jauge-'.$style) );
			if( $nom )
				$jauge_gauche->set_tooltip($nom.($valeur?$valeur:'0').($max=='%'?' %':' / '.$max), 'right', '#cadre_gauche');
			$jauge_gauche->set_attribut('style', 'transform: rotate('.$angle.'deg);{-webkit-transform: rotate('.$angle.'deg);');
		}
		$this->disque->add( new interf_bal_cont('div', 'jauge_'.$type.'_stop', 'depl_cache') );
	}
}

/// Classe pour la partie gauche de l'interface quand il faut montrer la carte
class interf_cadre_carte extends interf_gauche
{
	protected $menu;
	protected $options;
	function __construct($carte=null)
	{
		global $db, $Gtrad;
		$perso = joueur::get_perso();
		parent::__construct( is_ville($perso->get_x(), $perso->get_y(), true) ? 'ville' : 'carte' );
		// Options
		/// @todo passer à l'objet
		$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_min_monstres"');
		$row = $db->read_array($req);
		$niv_min = $row ? $row[0] : '0';
		$req = $db->query('select valeur from options where id_perso = '.$perso->get_id().' and nom = "niv_max_monstres"');
		$row = $db->read_array($req);
		$niv_max = $row ? $row[0] : 255;
		$this->options  = interf_carte::calcul_options( $perso->get_id() );
		// Menu carte
		$this->menu = $this->barre_haut->add( new interf_menu(false, 'menu_carte', 'aide') );
		// ajout_option($action, $drapeau, $icone_1, $icone_0, $texte, $inv=true, $affiche=true)
		$this->ajout_option('royaumes', interf_carte::aff_royaumes, 'drapeau', 'drapeau-non', 'les royaumes');
		$this->ajout_option('monstres', interf_carte::aff_monstres, 'oeil', 'oeil-bare', 'les monstres', true);
		$this->ajout_option('jour', interf_carte::aff_jour, 'lune', 'jour-non', 'les effets liés à l\'heure', true);
		$this->ajout_option('meteo', interf_carte::aff_atmosphere, 'nuage', 'meteo-non', 'les effets atmosphériques', true);
		$this->ajout_option('son', interf_carte::act_sons, 'son-fort', 'son-coupe', 'les effets sonores', true, false);
		$this->ajout_option('ads', interf_carte::aff_ads, 'ads', 'ads-non', 'l\'appartenance des armes de siège');
		// Options supplémentaires
		$li = $this->menu->add( new interf_bal_cont('li') );
		$lien = $li->add( new interf_bal_cont('a', 'opt_carte') );
		$min = $niv_min == 255 ? '25+' : $niv_min;
		$max = $niv_max == 255 ? '25+' : $niv_max;
		$ordre = array(interf_carte::aff_pcb=>'p, c, b', interf_carte::aff_cbp=>'c, b', p, interf_carte::aff_cpb=>'c, p, b', (interf_carte::aff_pcb|interf_carte::aff_pnj)=>'p+, c, b', (interf_carte::aff_cbp|interf_carte::aff_pnj)=>'c, b, p+', (interf_carte::aff_cpb|interf_carte::aff_pnj)=>'c, p+, b');
		$diplos = array('AF', 'A', 'PD', 'P', 'BT', 'N', 'MT', 'G', 'GD', 'E', 'EE', 'VR');
		$lien->add( new interf_bal_smpl('span', 'm : '.$min.' à '.$max, false, 'dropdown-toggle') );
		$txt = $ordre[$this->options&(interf_carte::masque_ordre|interf_carte::aff_pnj)].($this->options&interf_carte::aff_diplo_sup?' ≥ ':' ≤ ').$diplos[($this->options&interf_carte::masque_diplo)>>8];
		$lien->add( new interf_bal_smpl('span', $txt) );
		$lien->set_attribut('onclick', 'return toggle(\'options_carte\');');
		$li->set_tooltip('Réglages divers (cliquez pour plus de détails).');
		$dropdown = $li->add( new interf_bal_cont('div', 'options_carte') );
		$form = $dropdown->add( new interf_form('deplacement.php?action=options', 'form_opt_carte') );
		$div_niv = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div_niv->add( new interf_bal_smpl('span', 'Monstres de niveau ', false, 'input-group-addon') );
		$sel_min = $div_niv->add( new interf_select_form('niv_min', false, false, 'form-control') );
		for($i=0; $i<=25; $i++)
			$sel_min->add_option($i?$i:'0', $i?$i:'0', $i==$niv_min);
		$sel_min->add_option('25 et +', 255, $niv_min>25);
		$div_niv->add( new interf_bal_smpl('span', ' à ', false, 'input-group-addon') );
		$sel_max = $div_niv->add( new interf_select_form('niv_max', false, false, 'form-control') );
		for($i=0; $i<=25; $i++)
			$sel_max->add_option($i?$i:'0', $i?$i:'0', $i==$niv_max);
		$sel_max->add_option('25 et +', 255, $niv_max>25);
		$div_ordre = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div_ordre->add( new interf_bal_smpl('span', 'Priorités ', false, 'input-group-addon') );
		$ordre = $div_ordre->add( new interf_select_form('ordre', false, false, 'form-control') );
		$ordre->add_option('Constructions > personnages > bâtiments', interf_carte::aff_cpb, ($this->options&interf_carte::masque_ordre)==interf_carte::aff_cpb);
		$ordre->add_option('Personnages > constructions > bâtiments', interf_carte::aff_pcb, ($this->options&interf_carte::masque_ordre)==interf_carte::aff_pcb);
		$ordre->add_option('Constructions > bâtiments > personnages', interf_carte::aff_cbp, ($this->options&interf_carte::masque_ordre)==interf_carte::aff_cbp);
		$div_diplo = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div_diplo->add( new interf_bal_smpl('span', 'Diplomatie ', false, 'input-group-addon') );
		$diplo_rel = $div_diplo->add( new interf_select_form('diplo_rel', false, 'diplo_rel', 'form-control') );
		$diplo_rel->add_option('<', '0', !($this->options & interf_carte::aff_diplo_sup));
		$diplo_rel->add_option('>', '1', $this->options & interf_carte::aff_diplo_sup);
		$diplo = $div_diplo->add( new interf_select_form('diplo', false, 'diplo_lim', 'form-control') );
		$diplo->add_option($Gtrad['diplo127'], interf_carte::aff_diplo_vr, ($this->options & interf_carte::masque_diplo)==interf_carte::aff_diplo_vr);
		$lst_diplos = array(interf_carte::aff_diplo_af, interf_carte::aff_diplo_a, interf_carte::aff_diplo_p, interf_carte::aff_diplo_pd, interf_carte::aff_diplo_bt, interf_carte::aff_diplo_n, interf_carte::aff_diplo_mt, interf_carte::aff_diplo_g, interf_carte::aff_diplo_gd, interf_carte::aff_diplo_e, interf_carte::aff_diplo_ee, interf_carte::aff_diplo_vr);
		for($i=0; $i<=10; $i++)
			$diplo->add_option($Gtrad['diplo'.($i?$i:'0')], $i?$lst_diplos[$i]:'0', ($this->options & interf_carte::masque_diplo)==$lst_diplos[$i]);
		$div_pnj = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$span_pnj = $div_pnj->add( new interf_bal_cont('span', 'opt_pnj', 'input-group-addon') );
		$check = $span_pnj->add( new interf_chp_form('checkbox', 'pnj', false, false) );
		if( $this->options & interf_carte::aff_pnj )
			$check->set_attribut('checked', 'checked');
		$span_pnj->add( new interf_txt('Afficher les PNJs.') );
		$btn = $form->add( new interf_chp_form('submit', false, false, 'Modifier', false, 'btn btn-default') );
		$btn->set_attribut('onclick', 'toggle(\'options_carte\'); return charger_formulaire(\'form_opt_carte\');');
		
		
		// Rose des vents
		$this->ajout_fleche('haut-gauche', 'depl_haut_gauche', 'haut-gauche', 'rel_-1_-1');
		$this->ajout_fleche('haut', 'depl_haut', 'haut', 'rel_0_-1');
		$this->ajout_fleche('haut-droite', 'depl_haut_droite', 'haut-droite', 'rel_1_-1');
		$this->ajout_fleche('gauche', 'depl_gauche', 'gauche', 'rel_-1_0');
		$this->set_icone_centre('rafraichir', 'deplacement.php?action=rafraichir');
		$this->ajout_fleche('droite', 'depl_droite', 'droite', 'rel_1_0');
		$this->ajout_fleche('bas-gauche', 'depl_bas_gauche', 'bas-gauche', 'rel_-1_1');
		$this->ajout_fleche('bas', 'depl_bas', 'bas', 'rel_0_1');
		$this->ajout_fleche('bas-droite', 'depl_bas_droite', 'bas-droite', 'rel_1_1');
		
		$perso = joueur::get_perso();
		$x = $perso->get_x();
		$y = $perso->get_y();
		$this->centre->add( $carte ? $carte : new interf_carte($x, $y, $this->options, 3, 'carte', $niv_min, $niv_max, $this->centre) );
	}
	protected function ajout_option($action, $drapeau, $icone_1, $icone_0, $texte, $inv=false, $affiche=true)
	{
		$val = $this->options & $drapeau;
		if( $inv )
			$valeur = $val ? '1' : '0';
		else
			$valeur = $val ? '0' : '1';
		$url = 'deplacement.php?action='.$action.'&valeur='.$valeur;
		$icone = $val ? $icone_1 : $icone_0;
		if( $affiche )
			$deb_txt = $val ? 'Masquer' : 'Afficher';
		else
			$deb_txt = $val ? 'Désactiver' : 'Activer';
		$elt = $this->menu->add( new interf_elt_menu('', $url, 'return charger(this.href);') );
		$elt->get_lien()->add( new interf_bal_smpl('div', '', null, 'icone icone-'.$icone) );
		$elt->get_lien()->set_tooltip($deb_txt.' '.$texte);
		return $elt;
	}
	protected function ajout_fleche($action, $id, $icone, $pos)
	{
		$fleche = $this->disque->add( new interf_bal_smpl('a', '', $id, 'icone icone-'.$icone) );
		$fleche->set_attribut('href', 'deplacement.php?action='.$action);
		$fleche->set_attribut('onClick', 'return  charger(this.href);');
		$fleche->set_attribut('onmouseover', '$(\'#pos_'.$pos.'\').addClass(\'pos_over\');');
		$fleche->set_attribut('onmouseout', '$(\'#pos_'.$pos.'\').removeClass(\'pos_over\');');
	}
}

class interf_cadre_carte_shine extends interf_cadre_carte
{
	protected function set_icone_centre($icone, $url=false)
	{
		$centre = $this->disque->add( new interf_bal_smpl($url?'a':'span', '', 'depl_disque_centre') );
		if( $url )
		{
			$centre->set_attribut('href', $url);
			$centre->set_attribut('onClick', 'return  charger(this.href);');
		}
		return $centre;
	}
	protected function ajout_fleche($action, $id, $icone, $pos)
	{
		$fleche = $this->disque->add( new interf_bal_smpl('a', false, $id) );
		$fleche->set_attribut('href', 'deplacement.php?action='.$action);
		$fleche->set_attribut('onClick', 'return  charger(this.href);');
		$fleche->set_attribut('onmouseover', '$(\'#pos_'.$pos.'\').addClass(\'pos_over\');');
		$fleche->set_attribut('onmouseout', '$(\'#pos_'.$pos.'\').removeClass(\'pos_over\');');
	}
}
?>