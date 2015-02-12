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
		$niveau = $this->recherche_batiment('dresseur');
		
		// Onglets
		$this->onglets->add_onglet('Cou', 'boutique.php?type=dressage&ajax=2&categorie=cou', 'tab_cou', 'ecole_mag', $categorie=='cou');
		/*$this->onglets->add_onglet('Selle', 'boutique.php?type=dressage&ajax=2&categorie=selle', 'tab_selle', 'ecole_mag', $categorie=='selle');
		$this->onglets->add_onglet('Dos', 'boutique.php?type=dressage&ajax=2&categorie=dos', 'tab_dos', 'ecole_mag', $categorie=='dos');
		$this->onglets->add_onglet('Arme', 'boutique.php?type=dressage&ajax=2&categorie=arme', 'tab_arme', 'ecole_mag', $categorie=='arme');*/
		$this->onglets->add_onglet('Torse', 'boutique.php?type=dressage&ajax=2&categorie=torse', 'tab_torse', 'ecole_mag', $categorie=='torse');
		//$this->onglets->add_onglet('Pattes', 'boutique.php?type=dressage&ajax=2&categorie=pattes', 'tab_pattes', 'ecole_mag', $categorie=='pattes');
		
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
		global $db;
		if( !$niveau )
			$niveau =  7;//$this->recherche_batiment($royaume, 'dressage');
		$objets = objet_pet::create(null, null, 'dressage ASC', false, 'lvl_batiment <='.$niveau.' AND type = "'.$categorie.'"');
		parent::__construct($royaume, $categorie, $objets, $nbr_alertes);
	}
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('Dressage');
		switch( $this->categorie )
		{
		case 'cou':
			$this->tbl->nouv_cell('PM');
			$this->tbl->nouv_cell('Distance');
			break;
		case 'selle':
			$this->tbl->nouv_cell('Bonus');
			break;
		case 'dos':
		case 'torse':
			$this->tbl->nouv_cell('PP');
			break;
		case 'arme':
			$this->tbl->nouv_cell('Dégâts');
			$this->tbl->nouv_cell('Lancer');
			$this->tbl->nouv_cell('Arme');
			break;
		case 'pattes':
			$this->tbl->nouv_cell('Bonus');
			break;
		}
		parent::aff_titres_col();
	}
	
	function aff_cont_col(&$elt)
	{
		global $Gtrad;
		$classe =  $elt->get_dressage() > $this->perso->get_dressage() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_dressage(), false, $classe) );
		switch( $this->categorie )
		{
		case 'cou':
			if( $elt->get_bonus() == 'pm' )
			{
				$this->tbl->nouv_cell( $elt->get_valeur() );
				$this->tbl->nouv_cell(0);
			}
			else
			{
				$this->tbl->nouv_cell(0);
				$this->tbl->nouv_cell( $elt->get_valeur() );
			}
			break;
		case 'arme':
			if( $elt->get_bonus() == 'lancer' )
			{
				$this->tbl->nouv_cell( $elt->get_valeur().'%' );
				$this->tbl->nouv_cell( 'toutes' );
			}
			else
			{
				$this->tbl->nouv_cell( $elt->get_valeur() );
				$this->tbl->nouv_cell( $Gtrad[explode('_',$elt->get_bonus())[1]] );
			}
			break;
		case 'selle':
			if( $elt->get_bonus() == 'antipation' )
				$this->tbl->nouv_cell('Anticipation +'.$elt->get_valeur().'%');
			else
				$this->tbl->nouv_cell($Gtrad[$elt->get_bonus()].' +'.$elt->get_valeur());
			break;
		default:
			$this->tbl->nouv_cell( $elt->get_valeur() );
		}
		parent::aff_cont_col($elt);
	}
}

?>