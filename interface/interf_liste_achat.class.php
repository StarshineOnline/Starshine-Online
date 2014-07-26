<?php
/**
 * @file interf_liste_achat.class.php
 * Classes pour les listes d'achats
 */

/// Classe de base pour les listes d'achats
abstract class interf_liste_achat extends interf_cont
{
	protected $tbl;
	protected $perso;
	protected $ordre = 3;
	protected $categorie = false;
	const type = false;
	function __construct(&$royaume, $id_tbl, $elts, $nbr_alertes=0)
	{
		$this->perso = &joueur::get_perso();
		$this->tbl = $this->add( new interf_data_tbl($id_tbl, '', false, false, 383 - $nbr_alertes * 30, $this->ordre) );
		$this->tbl->nouv_cell('Nom');
		$this->aff_titres_col();
		$this->tbl->nouv_cell('Stars');
		$this->tbl->nouv_cell('Achat');
		
		$url_base = $this::url.'?';
		if( $this::type )
			$url_base .= 'type='.$this::type.'&';
		if( $this->categorie )
			$url_base .= '&categorie='.$this->categorie.'&';
		// Contenu
		foreach($elts as $e)
		{
			$achat = $this->peut_acheter($e);
			$this->tbl->nouv_ligne(false, $achat ? '' : 'non-achetable');
			$lien = new interf_bal_smpl('a', $e->get_nom(), 'elt'.$e->get_id());
			$this->tbl->nouv_cell( $lien );
			$url = $url_base.'action=infos&id='.$e->get_id();
			$lien->set_attribut('onclick', 'chargerPopover(\'elt'.$e->get_id().'\', \'info_elt'.$e->get_id().'\', \'right\', \''.$url.'\', \''.$e->get_nom().'\');');
			$this->aff_cont_col($e);
			$prix = $e->get_prix() + ceil($e->get_prix() * $royaume->get_taxe_diplo($this->perso->get_race()) / 100);
			$classe =  $prix > $this->perso->get_star() ? 'text-danger' : '';
			$this->tbl->nouv_cell( new interf_bal_smpl('span', $prix, false, $classe) );
			if( $achat === null )
				$this->tbl->nouv_cell( new interf_bal_smpl('span', 'Connu', false, 'connu') );
			else if( $achat )
				$this->tbl->nouv_cell( new interf_lien('Achat', $url_base.'action=achat&id='.$e->get_id()) );
			else
				$this->tbl->nouv_cell('&nbsp;');
		}
		
	}
	
	protected function recherche_batiment(&$royaume, $batiment)
	{
		global $db;
		///TODO: à améliorer
		$requete = 'SELECT level, statut, c.hp, b.hp AS hp_max, nom FROM construction_ville AS c LEFT JOIN batiment_ville AS b ON c.id_batiment = b.id WHERE b.type = "'.$batiment.'" AND c.id_royaume = '.$royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		// Si le batiment est inactif, on le met au niveau 1
		return $row['statut'] == 'inactif' ? 1 : $row['level'];
	}
	
	protected function aff_filtres() {}
	abstract protected function aff_titres_col();
	abstract protected function aff_cont_col(&$elt);
	protected function peut_acheter(&$elt)
	{	
		return $this->perso->get_star() >= $elt->get_prix();
	}
}

/// Classe de base pour les listes d'achats de sorts
abstract class interf_achat_compsort extends interf_liste_achat
{
	const url = 'ecole.php';
	function peut_acheter(&$elt)
	{
		if( $elt->est_connu($this->perso) )
			return null;
		else
			return $elt->verif_prerequis($this->perso) && parent::peut_acheter($elt);
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
		$this->tbl->nouv_cell( $elt->get_mp() );
		$requis = $elt->get_incantation() * $this->perso->get_facteur_magie();
		$classe =  $requis > $this->perso->get_incantation() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $requis, false, $classe) );
		$methode = 'get_'.$elt->get_comp_assoc();
		$requis = round( $elt->get_comp_requis() * $this->perso->get_facteur_magie() * (1 - (($Trace[$this->perso->get_race()]['affinite_'.$elt->get_comp_assoc()] - 5) / 10)) );
		$classe =  $requis > $this->perso->$methode() ? 'text-danger' : '';
		$cell = $this->tbl->nouv_cell( new interf_bal_smpl('span', $requis, false, $classe) );
		$cell->add( new interf_img('image/icone/'.$elt->get_comp_assoc().'.png') );
	}
}

/// Classe pour les listes d'achats de sorts hors combat
class interf_achat_sort_jeu extends interf_achat_sort
{
	const type = 'sort_jeu';
	function __construct(&$royaume, $niveau, $nbr_alertes=0)
	{
		global $db;
		if( !$niveau )
			$niveau =  $this->recherche_batiment($royaume, 'ecole_magie');
		$sorts = sort_jeu::create(null, null, 'incantation ASC', false, 'lvl_batiment <='.$niveau.' AND requis < 999');
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

/// Classe de base pour les listes d'objets
abstract class interf_achat_objet extends interf_liste_achat
{
	const url = 'boutique.php';
	function __construct(&$royaume, $categorie, $objets, $nbr_alertes=0)
	{
		$this->categorie = $categorie;
		parent::__construct($royaume, 'tbl_'.$categorie, $objets, $nbr_alertes);
	}
	function peut_acheter(&$elt)
	{
		return $elt->peut_utiliser($this->perso, false) && parent::peut_acheter($elt);
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
		///TODO: à améliorer
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
		//parent::aff_titres_col();
	}
	
	function aff_cont_col(&$elt)
	{
		$this->tbl->nouv_cell( $elt->get_pp() );
		$this->tbl->nouv_cell( $elt->get_pm() );
		$classe =  $elt->get_force() > $this->perso->get_force() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_force(), false, $classe) );
		//parent::aff_cont_col($elt);
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

/// Classe de base pour les listes d'objet d'alchimie
class interf_achat_alchimie extends interf_liste_achat
{
	const url='alchimiste.php';
	function __construct(&$royaume, $nbr_alertes=0)
	{
		global $db;
		$this->categorie = 'objet';
		$objets = objet::create(null, null, 'prix ASC', false, 'achetable = "y"');
		parent::__construct($royaume, 'tbl_objet', $objets, $nbr_alertes);
	}
	function aff_titres_col()
	{
	}
	
	function aff_cont_col(&$elt)
	{
	}
}

/// Classe de base pour les listes de recettes d'alchimie
class interf_achat_recette extends interf_liste_achat
{
	const url='alchimiste.php';
	function __construct(&$royaume, $nbr_alertes=0)
	{
		global $db;
		$this->categorie = 'recette';
		$recettes = alchimie_recette::create(null, null, 'royaume_alchimie ASC', false, 'royaume_alchimie < '.$royaume->get_alchimie());
		parent::__construct($royaume, 'tbl_recette', $recettes, $nbr_alertes);
	}
	function aff_titres_col()
	{
	}
	function aff_cont_col(&$elt)
	{
	}
	protected function peut_acheter(&$elt)
	{
		if( perso_recette::recov($this->perso->get_id(), $elt->get_id()) )
			return null;
		else
			return parent::peut_acheter($elt);
	}
}

?>