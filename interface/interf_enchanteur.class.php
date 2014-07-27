<?php
/**
 * @file interf_enchanteur.class.php
 * Classes pour l'interface de l'enchanteur
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'enchanteur
class interf_enchanteur extends interf_ville_onglets
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('pentacle');
		//$niveau = $this->recherche_batiment('enchanteur');
		
		// Onglets
		$this->onglets->add_onglet('Grands accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=grand', 'tab_grand', 'ecole_mag', $categorie=='grand');
		$this->onglets->add_onglet('Moyens accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=moyen', 'tab_moyen', 'ecole_mag', $categorie=='moyen');
		$this->onglets->add_onglet('Petits accessoires', 'boutique.php?type=accessoire&ajax=2&categorie=petit', 'tab_petit', 'ecole_mag', $categorie=='petit');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_accessoire($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe de base pour les listes d'accessoires
class interf_achat_accessoire extends interf_achat_objet
{
	const type = 'accessoire';
	function __construct(&$royaume, $categorie, $niveau, $nbr_alertes=0)
	{
		/*global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'accessoire');
		$objets = accessoire::create(null, null, 'prix ASC', false, 'lvl_batiment <='.$niveau.' AND type = "'.$categorie.'"');
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