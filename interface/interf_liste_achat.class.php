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
	const ordre = 3;
	function __construct($id_tbl, $elts, $nbr_alertes=0)
	{
		$this->perso = &joueur::get_perso();
		$this->tbl = $this->add( new interf_data_tbl($id_tbl, '', false, false, 383 - $nbr_alertes * 30, $this::ordre) );
		$this->tbl->nouv_cell('Nom');
		$this->aff_titres_col();
		$this->tbl->nouv_cell('Stars');
		$this->tbl->nouv_cell('Achat');
		
		// Contenu
		foreach($elts as $e)
		{
			$achat = $this->peut_acheter($e);
			$this->tbl->nouv_ligne('elt'.$e->get_id(), $achat ? '' : 'non-achetable');
			$lien = new interf_bal_smpl('a', $e->get_nom());
			$this->tbl->nouv_cell( $lien );
			$url = $this::url.'?type='.$this::type.'&action=infos&id='.$e->get_id();
			$lien->set_attribut('onclick', 'chargerPopover(\'elt'.$e->get_id().'\', \'info_elt'.$e->get_id().'\', \'right\', \''.$url.'\', \''.$e->get_nom().'\');');
			$this->aff_cont_col($e);
			$classe =  $e->get_prix() > $this->perso->get_star() ? 'text-danger' : '';
			$this->tbl->nouv_cell( new interf_bal_smpl('span', $e->get_prix(), false, $classe) );
			if( $achat === null )
				$this->tbl->nouv_cell( new interf_bal_smpl('span', 'Connu', false, 'connu') );
			else if( $achat )
				$this->tbl->nouv_cell( new interf_lien('Achat', $this::url.'?type='.$this::type.'&action=achat&id='.$e->get_id()) );
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
	abstract protected function peut_acheter(&$elt);
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
			return $elt->verif_prerequis($this->perso);
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
		$this->tbl->nouv_cell( $elt->get_mp() );
		$classe =  $elt->get_incantation() > $this->perso->get_incantation() ? 'text-danger' : '';
		$this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_incantation(), false, $classe) );
		$methode = 'get_'.$elt->get_comp_assoc();
		$classe =  $elt->get_comp_requis() > $this->perso->$methode() ? 'text-danger' : '';
		$cell = $this->tbl->nouv_cell( new interf_bal_smpl('span', $elt->get_comp_requis(), false, $classe) );
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
		$sorts = sort_jeu::create(null, null, 'incantation ASC', false, 'lvl_batiment <='.$niveau);
		parent::__construct('tbl_sort_jeu', $sorts, $nbr_alertes);
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
		$sorts = sort_combat::create(null, null, 'incantation ASC', false, 'lvl_batiment <='.$niveau);
		parent::__construct('tbl_sort_combat', $sorts, $nbr_alertes);
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
		$sorts = comp_jeu::create(null, null, 'comp_requis ASC', false, 'lvl_batiment <='.$niveau);
		parent::__construct('tbl_comp_jeu', $sorts, $nbr_alertes);
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
		$sorts = comp_combat::create(null, null, 'comp_requis ASC', false, 'lvl_batiment <='.$niveau);
		parent::__construct('tbl_comp_combat', $sorts, $nbr_alertes=0);
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