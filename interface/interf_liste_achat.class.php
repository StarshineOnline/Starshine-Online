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
	protected $txt_achat = 'Achat';
	const taxe = true;
	const descr = true;
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
			if( $this::descr )
			{
				$lien = new interf_bal_smpl('a', $e->get_nom(), 'elt_'.$this::type.'_'.$e->get_id());
				$this->tbl->nouv_cell( $lien );
				$url = $url_base.'action=infos&id='.$e->get_id();
				$lien->set_attribut('onclick', 'chargerPopover(\'elt_'.$this::type.'_'.$e->get_id().'\', \'info_elt_'.$this::type.'_'.$e->get_id().'\', \'right\', \''.$url.'\', \''.$e->get_nom().'\');');
			}
			else
				$this->tbl->nouv_cell( $e->get_nom() );
			$this->aff_cont_col($e);
			$taxe = $this::taxe ? ceil($e->get_prix() * $royaume->get_taxe_diplo($this->perso->get_race()) / 100) : 0;
			$prix = $e->get_prix() + $taxe;
			$classe =  $prix > $this->perso->get_star() ? 'text-danger' : '';
			$this->tbl->nouv_cell( new interf_bal_smpl('span', $prix, false, $classe) );
			if( $achat === null )
				$this->tbl->nouv_cell( new interf_bal_smpl('span', 'Connu', false, 'connu') );
			else if( $achat )
				$this->tbl->nouv_cell( new interf_lien($this->txt_achat, $url_base.'action=achat&id='.$e->get_id()) );
			else
				$this->tbl->nouv_cell('&nbsp;');
		}
	}
	
	protected function recherche_batiment(&$royaume, $batiment)
	{
		global $db;
		///@todo à améliorer
		$requete = 'SELECT level, statut, c.hp, b.hp AS hp_max, nom FROM construction_ville AS c LEFT JOIN batiment_ville AS b ON c.id_batiment = b.id WHERE b.type = "'.$batiment.'" AND c.id_royaume = '.$royaume->get_id();
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		// Si le batiment est inactif, on le met au niveau 1
		return $row['statut'] == 'inactif' ? 1 : $row['level'];
	}
	
	protected function aff_filtres() {}
	protected function aff_titres_col() {}
	protected function aff_cont_col(&$elt) {}
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

?>