<?php
/**
 * @file interf_ecole_magie.class.php
 * Classes pour l'interface de l'école de magie
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'école de magie
class interf_ecole_magie extends interf_ville_onglets
{
	function __construct(&$royaume, $type)
	{
		parent::__construct($royaume);
		$perso = joueur::get_perso();
		
		// Icone
		$this->icone = $this->set_icone_centre('sorts');
		$niveau = $this->recherche_batiment('ecole_magie');
		
		// Onglets
		$this->onglets->add_onglet('Sorts hors combat', 'ecole.php?type=sort_jeu&ajax=2', 'tab_ecole_sort_jeu', 'ecole_mag', $type=='sort_jeu');
		$this->onglets->add_onglet('Sorts de combat', 'ecole.php?type=sort_combat&ajax=2', 'tab_ecole_sort_combat', 'ecole_mag', $type=='sort_combat');
		if( !$perso->get_sort_element() || !$perso->get_sort_mort() || !$perso->get_sort_vie() )
			$this->onglets->add_onglet('Magies', 'ecole.php?type=magie&ajax=2', 'tab_ecole_magie', 'ecole_mag', $type=='magie');
		
		// Filtres
		/*$haut = $this->onglets->get_haut();
		$li1 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magievie.png'), 'javascript:filtre_table(\'sort_vie\');', false, false, 'filtre') );
		$li2 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magiemort.png'), 'javascript:filtre_table(\'sort_mort\');', false, false, 'filtre') );
		$li3 = $haut->add( new interf_elt_menu(new interf_img('image/icone/magieelementaire.png'), 'javascript:filtre_table(\'sort_element\');', false, false, 'filtre') );
		*/
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_ecole_'.$type) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$type.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		// Contenu
		switch( $type )
		{
		case 'sort_jeu':
			$this->onglets->get_onglet('tab_ecole_sort_jeu')->add( new interf_achat_sort_jeu($royaume, $niveau, $n) );
			break; 
		case 'sort_combat':
			$this->onglets->get_onglet('tab_ecole_sort_combat')->add( new interf_achat_sort_combat($royaume, $niveau, $n) );
			break; 
		}
		if( !$perso->get_sort_element() || !$perso->get_sort_mort() || !$perso->get_sort_vie() )
		{
			$cout_app = 500;
			$taxe = ceil($cout_app * $royaume->get_taxe_diplo($perso->get_race()) / 100);
			$cout = $cout_app + $taxe;
			$div = $this->onglets->get_onglet('tab_ecole_magie')->add( new interf_bal_cont('div', 'achat_magie') );
			if( !$perso->get_sort_vie() )
				$div->add( new interf_lien('Acheter magie de la vie ('.$cout.' stars)', 'ecole.php?type=magie&action=magie&magie=sort_vie', false, 'btn btn-default') );
			if( !$perso->get_sort_mort() )
				$div->add( new interf_lien('Acheter magie de la mort ('.$cout.' stars)', 'ecole.php?type=magie&action=magie&magie=sort_mort', false, 'btn btn-default') );
			if( !$perso->get_sort_element() )
				$div->add( new interf_lien('Acheter magie élémentaire ('.$cout.' stars)', 'ecole.php?type=magie&action=magie&magie=sort_element', false, 'btn btn-default') );
		}
	}
}

/// Classe de base pour les listes d'achats de sorts
class interf_achat_sort extends interf_achat_compsort
{	
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('Incant.');
		$this->tbl->nouv_cell('Apt.');
	}
	
	function aff_cont_col(&$elt)
	{
		global $Trace;
		$this->tbl->nouv_cell( $elt->get_mp_final($this->perso) );
		$requis = $elt->get_incantation() * $this->perso->get_facteur_magie();
		$classe =  $requis > $this->perso->get_incantation() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $requis, false, $classe) );
		$methode = 'get_'.$elt->get_comp_assoc();
		$requis = round( $elt->get_comp_requis() * $this->perso->get_facteur_magie() * (1 - (($Trace[$this->perso->get_race()]['affinite_'.$elt->get_comp_assoc()] - 5) / 10)) );
		$classe =  $requis > $this->perso->$methode() ? 'text-danger' : '';
		$cell = $this->tbl->nouv_cell( new interf_bal_smpl('span', $requis, false, $classe) );
		$cell->add( new interf_img('image/icone/'.$elt->get_comp_assoc().'.png') );
	}
	
	function peut_acheter($elt)
	{
		global $Trace;
		$achat = parent::peut_acheter($elt);
		if( !$achat )
			return $achat;
		$requis = $elt->get_incantation() * $this->perso->get_facteur_magie();
		if( $requis > $this->perso->get_incantation() )
			return false;
		$methode = 'get_'.$elt->get_comp_assoc();
		$requis = round( $elt->get_comp_requis() * $this->perso->get_facteur_magie() * (1 - (($Trace[$this->perso->get_race()]['affinite_'.$elt->get_comp_assoc()] - 5) / 10)) );
		if( $requis > $this->perso->$methode() )
			return false;
		return true;
	}
}

/// Classe pour les listes d'achats de sorts hors combat
class interf_achat_sort_jeu extends interf_achat_sort
{
	const type = 'sort_jeu';
	function __construct(&$royaume, $niveau, $nbr_alertes=0)
	{
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'ecole_magie');
		$sorts = sort_jeu::create(null, null, 'incantation ASC', false, 'lvl_batiment <='.($niveau?$niveau:'0').' AND requis < 999');
		parent::__construct($royaume, 'tbl_sort_jeu', $sorts, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('PA');
		$this->tbl->nouv_cell('MP');
		parent::aff_titres_col();
	}
	
	function aff_cont_col(&$elt)
	{
		$this->tbl->nouv_cell( $elt->get_pa() );
		parent::aff_cont_col($elt);
	}
}

/// Classe pour les listes d'achats de sorts hors combat
class interf_achat_sort_combat extends interf_achat_sort
{
	const type = 'sort_combat';
	function __construct(&$royaume, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'ecole_magie');
		$sorts = sort_combat::create(null, null, 'incantation ASC', false, 'lvl_batiment <='.$niveau.' AND requis < 999');
		parent::__construct($royaume, 'tbl_sort_combat', $sorts, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('Effet');
		$this->tbl->nouv_cell('RM');
		parent::aff_titres_col();
	}
	
	function aff_cont_col(&$elt)
	{
		$this->tbl->nouv_cell( $elt->get_effet() );
		parent::aff_cont_col($elt);
	}
}


?>