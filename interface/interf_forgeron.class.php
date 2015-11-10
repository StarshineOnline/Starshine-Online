<?php
/**
 * @file interf_forgeron.class.php
 * Classes pour l'interface du forgeron
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface du forgeron
class interf_forgeron extends interf_ville_onglets
{
	function __construct(&$royaume, $categorie)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('forge');
		$niveau = $this->recherche_batiment('forgeron');
		
		// Onglets
		$this->onglets->add_onglet('Épées', 'boutique.php?type=arme&ajax=2&categorie=epee', 'tab_epee', 'ecole_mag', $categorie=='epee');
		$this->onglets->add_onglet('Haches', 'boutique.php?type=arme&ajax=2&categorie=hache', 'tab_hache', 'ecole_mag', $categorie=='hache');
		$this->onglets->add_onglet('Dagues', 'boutique.php?type=arme&ajax=2&categorie=dague', 'tab_dague', 'ecole_mag', $categorie=='dague');
		$this->onglets->add_onglet('Arcs', 'boutique.php?type=arme&ajax=2&categorie=arc', 'tab_arc', 'ecole_mag', $categorie=='arc');
		$this->onglets->add_onglet('Boucliers', 'boutique.php?type=arme&ajax=2&categorie=bouclier', 'tab_bouclier', 'ecole_mag', $categorie=='bouclier');
		$this->onglets->add_onglet('Bâtons', 'boutique.php?type=arme&ajax=2&categorie=baton', 'tab_baton', 'ecole_mag', $categorie=='baton');
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$categorie) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$categorie.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		$this->onglets->get_onglet('tab_'.$categorie)->add( new interf_achat_arme($royaume, $categorie, $niveau, $n) );
	}
}

/// Classe de base pour les listes d'armes
class interf_achat_arme extends interf_achat_objet
{
	const type = 'arme';
	function __construct(&$royaume, $categorie, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'forgeron');
		$objets = arme::create(null, null, 'coefficient ASC', false, 'lvl_batiment <='.$niveau.' AND type = "'.$categorie.'"');
		if( $categorie == 'baton' )
			$this->ordre = 4;
		parent::__construct($royaume, $categorie, $objets, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('Mains');
		$this->tbl->nouv_cell( $this->categorie == 'bouclier' ? 'Absorpt.' : 'Dégâts' );
    if( $this->categorie == 'baton' )
			$this->tbl->nouv_cell('Bonus');
		$this->tbl->nouv_cell('Coeff.');
		//parent::aff_titres_col();
	}
	function aff_cont_col(&$elt)
	{
		global $Gtrad;
		$this->tbl->nouv_cell( $Gtrad[$elt->get_mains()] );
		$this->tbl->nouv_cell( $elt->get_degat() );
    if( $this->categorie == 'baton' )
			$this->tbl->nouv_cell( $elt->get_var1().'%' );
		///@todo à améliorer
		switch($this->categorie)
		{
		case 'epee':
		case 'hache':
		case 'dague':
			$coeff = $this->perso->get_coef_melee();
			break;
		case 'arc':
			$coeff = $this->perso->get_coef_distance();
			break;
		case 'bouclier':
			$coeff = $this->perso->get_coef_blocage();
			break;
		case 'baton':
			$coeff = $this->perso->get_coef_incantation();
			break;
		}
		$classe =  $elt->get_coefficient() > $coeff ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_coefficient(), false, $classe) );
		//parent::aff_cont_col($elt);
	}
}

?>