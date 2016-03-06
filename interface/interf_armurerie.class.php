<?php
/**
 * @file interf_armurerie.class.php
 * Classes pour l'interface de l'armurerie
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'armurerie
class interf_armurerie extends interf_ville_onglets
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('casque');
		$niveau = $this->recherche_batiment('armurerie');
		
		// Onglets
		$this->onglets->add_onglet('Torse', 'boutique.php?type=armure&ajax=2&categorie=torse', 'tab_torse', 'ecole_mag', $categorie=='torse');
		$this->onglets->add_onglet('Tête', 'boutique.php?type=armure&ajax=2&categorie=tete', 'tab_tete', 'ecole_mag', $categorie=='tete');
		$this->onglets->add_onglet('Jambe', 'boutique.php?type=armure&ajax=2&categorie=jambe', 'tab_jambe', 'ecole_mag', $categorie=='jambe');
		$this->onglets->add_onglet('Taille', 'boutique.php?type=armure&ajax=2&categorie=ceinture', 'tab_ceinture', 'ecole_mag', $categorie=='ceinture');
		$this->onglets->add_onglet('Main', 'boutique.php?type=armure&ajax=2&categorie=main', 'tab_main', 'ecole_mag', $categorie=='main');
		$this->onglets->add_onglet('Pieds', 'boutique.php?type=armure&ajax=2&categorie=chaussure', 'tab_chaussure', 'ecole_mag', $categorie=='chaussure');
		$this->onglets->add_onglet('Dos', 'boutique.php?type=armure&ajax=2&categorie=dos', 'tab_dos', 'ecole_mag', $categorie=='dos');
		$this->onglets->add_onglet('Cou', 'boutique.php?type=armure&ajax=2&categorie=cou', 'tab_cou', 'ecole_mag', $categorie=='cou');
		$this->onglets->add_onglet('Doigt', 'boutique.php?type=armure&ajax=2&categorie=doigt', 'tab_doigt', 'ecole_mag', $categorie=='doigt');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_armure($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe de base pour les listes d'armures
class interf_achat_armure extends interf_achat_objet
{
	const type = 'armure';
	function __construct(&$royaume, $categorie, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'armurerie');
		$objets = armure::create(null, null, 'prix ASC', false, 'lvl_batiment <='.$niveau.' AND type = "'.$categorie.'"');
		parent::__construct($royaume, $categorie, $objets, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('PP');
		$this->tbl->nouv_cell('PM');
		$this->tbl->nouv_cell('Force');
		$this->tbl->nouv_cell('Niv.');
	}
	
	function aff_cont_col(&$elt)
	{
		$this->tbl->nouv_cell( $elt->get_pp() );
		$this->tbl->nouv_cell( $elt->get_pm() );
		$classe =  $elt->get_force() > $this->perso->get_force() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_force(), false, $classe) );
		$classe =  $elt->get_niveau() > $this->perso->get_level() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_niveau(), false, $classe) );
	}
}

?>