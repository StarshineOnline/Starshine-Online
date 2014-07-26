<?php
/**
 * @file interf_boutique.class.php
 * Classes pour l'interface des boutiques
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface du dresseur
class interf_dresseur extends interf_ville_onglets
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('faucon');
		//$niveau = $this->recherche_batiment('dresseur');
		
		// Onglets
		$this->onglets->add_onglet('Cou', 'boutique.php?type=dressage&ajax=2&categorie=cou', 'tab_cou', 'ecole_mag', $categorie=='cou');
		$this->onglets->add_onglet('Selle', 'boutique.php?type=dressage&ajax=2&categorie=selle', 'tab_selle', 'ecole_mag', $categorie=='selle');
		$this->onglets->add_onglet('Dos', 'boutique.php?type=dressage&ajax=2&categorie=dos', 'tab_dos', 'ecole_mag', $categorie=='dos');
		$this->onglets->add_onglet('Arme', 'boutique.php?type=dressage&ajax=2&categorie=arme', 'tab_arme', 'ecole_mag', $categorie=='arme');
		$this->onglets->add_onglet('Torse', 'boutique.php?type=dressage&ajax=2&categorie=torse', 'tab_torse', 'ecole_mag', $categorie=='torse');
		$this->onglets->add_onglet('Pattes', 'boutique.php?type=dressage&ajax=2&categorie=pattes', 'tab_pattes', 'ecole_mag', $categorie=='pattes');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_dressage($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe de base pour les listes d'objet de dressage
class interf_achat_dressage extends interf_achat_objet
{
	const type = 'dressage';
	function __construct(&$royaume, $categorie, $niveau, $nbr_alertes=0)
	{
		/*global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'dressage');
		$objets = objet_pet::create(null, null, 'dressage ASC', false, 'lvl_batiment <='.$niveau.' AND type = "'.$categorie.'"');
		parent::__construct($royaume, $categorie, $objets, $nbr_alertes);*/
	}
	function aff_titres_col()
	{
		/*$this->tbl->nouv_cell('');
		parent::aff_titres_col();*/
	}
	
	function aff_cont_col(&$elt)
	{
		/*$this->tbl->nouv_cell( $elt->get_() );
		parent::aff_cont_col($elt);*/
	}
}

?>