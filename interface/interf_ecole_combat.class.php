<?php
/**
 * @file interf_ecole_combat.class.php
 * Classes pour l'interface de l'école de combat
 */
include_once(root.'interface/interf_ville.class.php');
include_once(root.'interface/interf_liste_achat.class.php');

/// Classe gérant l'interface de l'école de combat
class interf_ecole_combat extends interf_ville_onglets
{
	function __construct(&$royaume, $type)
	{
		parent::__construct($royaume);
		
		// Icone
		$this->icone = $this->set_icone_centre('ecole-combat');
		$niveau = $this->recherche_batiment('ecole_combat');
		
		// Onglets
		$this->onglets->add_onglet('Comp. hors combat', 'ecole.php?type=comp_jeu&ajax=2', 'tab_ecole_comp_jeu', 'ecole_mag', $type=='comp_jeu');
		$this->onglets->add_onglet('Comp. de combat', 'ecole.php?type=comp_combat&ajax=2', 'tab_ecole_comp_combat', 'ecole_mag', $type=='comp_combat');
		
		// Filtres
		$haut = $this->onglets->get_haut();
		if( $type == 'comp_jeu' )
		{
			$li1 = $haut->add( new interf_elt_menu(new interf_img('image/dressage.png'), 'javascript:filtre_table(\'dressage\');', false, false, 'filtre') );
		}
		else
		{
			$li1 = $haut->add( new interf_elt_menu(new interf_img('image/blocage.png'), 'javascript:filtre_table(\'blocage\');', false, false, 'filtre') );
		}
		$li2 = $haut->add( new interf_elt_menu(new interf_img('image/distance.png'), 'javascript:filtre_table(\'distance\');', false, false, 'filtre') );
		$li3 = $haut->add( new interf_elt_menu(new interf_img('image/esquive.png'), 'javascript:filtre_table(\'esquive\');', false, false, 'filtre') );
		$li4 = $haut->add( new interf_elt_menu(new interf_img('image/melee.png'), 'javascript:filtre_table(\'melee\');', false, false, 'filtre') );
		
		$n = interf_alerte::aff_enregistres( $this->onglets->get_onglet('tab_'.$type) );
		interf_base::code_js('$(".tab-content .alert").on("closed.bs.alert", function(){ var obj = $("#tab_'.$type.' .dataTables_scrollBody"); obj.height( obj.height() + 30 ); });');
		// Contenu
		switch( $type )
		{
		case 'comp_jeu':
			$this->onglets->get_onglet('tab_ecole_comp_jeu')->add( new interf_achat_comp_jeu($royaume, $niveau, $n) );
			break; 
		case 'comp_combat':
			$this->onglets->get_onglet('tab_ecole_comp_combat')->add( new interf_achat_comp_combat($royaume, $niveau, $n) );
			break; 
		}
	}
}

/// Classe de base pour les listes d'achats de compétences
class interf_achat_comp extends interf_achat_compsort
{	
	function aff_titres_col()
	{
		$this->tbl->nouv_cell('Apt.');
		$this->tbl->nouv_cell('Arme');
	}
	
	function aff_cont_col(&$elt)
	{
		global $Gtrad;
		$this->tbl->nouv_cell( $elt->get_mp() );
		$methode = 'get_'.$elt->get_comp_assoc();
		$classe =  $elt->get_comp_requis() > $this->perso->$methode() ? 'text-danger' : '';
		$cell = $this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_comp_requis(), false, $classe) );
		$img = $cell->add( new interf_img('image/icone/'.$elt->get_comp_assoc().'.png') );
		$img->set_tooltip($Gtrad[$elt->get_comp_assoc()]);
		if( $elt->get_arme_requis() )
		{
			$armes = explode(';',$elt->get_arme_requis());
			$img = new interf_img('image/icone/'.$armes[0].'.png');
			$img->set_tooltip($Gtrad[$armes[0]]);
			$cell = $this->tbl->nouv_cell( $img );
			for($i=1; $i<count($armes); $i++)
			{
				$img = $cell->add( new interf_img('image/icone/'.$armes[$i].'.png') );
				$img->set_tooltip($Gtrad[$armes[$i]]);
			}
		}
		else
			$this->tbl->nouv_cell( new interf_bal_smpl('em', 'toutes') );
	}
}

/// Classe pour les listes d'achats de compétences hors combat
class interf_achat_comp_jeu extends interf_achat_comp
{
	const type = 'comp_jeu';
	function __construct(&$royaume, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'ecole_combat');
		$sorts = comp_jeu::create(null, null, 'comp_requis ASC', false, 'lvl_batiment <='.$niveau.' AND requis < 999');
		parent::__construct($royaume, 'tbl_comp_jeu', $sorts, $nbr_alertes);
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

/// Classe pour les listes d'achats de compétences hors combat
class interf_achat_comp_combat extends interf_achat_comp
{
	const type = 'comp_combat';
	function __construct(&$royaume, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'ecole_combat');
		$sorts = comp_combat::create(null, null, 'comp_requis ASC', false, 'lvl_batiment <='.$niveau.' AND requis < 999');
		parent::__construct($royaume, 'tbl_comp_combat', $sorts, $nbr_alertes);
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