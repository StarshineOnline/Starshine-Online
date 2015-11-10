<?php
/**
 * @file interf_bourse.class.php
 * Interface de la bourse des royaume 
 */ 

class interf_bourse extends interf_onglets
{
	function __construct($onglet)
	{
		global $G_url;
		parent::__construct('ongl_bourse', 'bourse');
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Achat', $url->get('onglet', 'achat'), 'ongl_achat', 'ongl_gest', $onglet=='achat');
		$this->add_onglet('Vente', $url->get('onglet', 'vente'), 'ongl_vente', 'ongl_gest', $onglet=='vente');
		$this->add_onglet('Historique achat', $url->get('onglet', 'hist_achat'), 'ongl_hist_achat', 'ongl_gest', $onglet=='hist_achat');
		$this->add_onglet('Historique vente', $url->get('onglet', 'hist_vente'), 'ongl_hist_vente', 'ongl_gest', $onglet=='hist_vente');
		
		$div = $this->get_onglet('ongl_'.$onglet);
		switch($onglet)
		{
		case 'achat':
			$div->add( new interf_bourse_achat() );
			break;
		case 'vente':
			$div->add( new interf_bourse_vente() );
			break;
		case 'hist_achat':
			$div->add( new interf_bourse_hist_achat() );
			break;
		case 'hist_vente':
			$div->add( new interf_bourse_hist_vente() );
			break;
		}
	}
}

abstract class interf_bourse_liste extends interf_data_tbl
{
	protected $bourse;
	protected $royaume;
	protected $perso;
	protected $url;
	function __construct($id)
	{
  	global $Trace, $Gtrad, $G_url;
		parent::__construct($id, '', false, false);
		
		$this->perso = joueur::get_perso();
		$this->royaume = new royaume($Trace[$this->perso->get_race()]['numrace']);
		$this->bourse = new bourse($this->royaume->get_id());
		
		$this->nouv_cell('Ressource');
		$this->nouv_cell('Nombre');
		$this->nouv_cell('Prix');
		$this->aff_fin_entete();
		
		$this->get_encheres();
		foreach($this->bourse->encheres as $enchere)
		{
			$this->url = $G_url->copie('id', $enchere->id_bourse_royaume);
			$this->nouv_ligne();
			$this->nouv_cell($Gtrad[$enchere->ressource], false, 'ressource '.$enchere->ressource);
			$this->aff_fin_enchere($enchere);
		}
	}
	abstract protected function get_encheres();
	abstract protected function aff_fin_entete();
	abstract protected function aff_fin_enchere(&$enchere);
}

class interf_bourse_achat extends interf_bourse_liste
{
	function __construct()
	{
		parent::__construct('bourse_achat');
	}
	protected function get_encheres()
	{
		$this->bourse->get_encheres('DESC', 'actif = 1 AND type = "vente"');
	}
	protected function aff_fin_entete()
	{
		$this->nouv_cell('Statut');
	}
	protected function aff_fin_enchere(&$enchere)
	{
		$this->nouv_cell($enchere->nombre);
		$this->nouv_cell($enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
		if( $enchere->id_royaume == $this->royaume->get_id() )
			$this->nouv_cell('Votre vente', false, 'text-primary');
		else if( $enchere->id_royaume_acheteur & (1 << ($this->royaume->get_id()-1)) )
			$this->nouv_cell('Vous avez acheté', false, 'text-success');
		else if( $this->perso->get_hp() > 0 )
			$this->nouv_cell( new interf_lien('Acheter', $this->url->get('action', 'achat')) );
		else
			$this->nouv_cell( '&nbsp;' );
	}
}

class interf_bourse_vente extends interf_bourse_liste
{
	function __construct()
	{
		parent::__construct('bourse_vente');
	}
	protected function get_encheres()
	{
		$this->bourse->get_encheres('DESC', 'actif = 1 AND type = "achat"');
	}
	protected function aff_fin_entete()
	{
		$this->nouv_cell('Statut');
	}
	protected function aff_fin_enchere(&$enchere)
	{
		$this->nouv_cell($enchere->nombre);
		if( $enchere->prix )
			$this->nouv_cell($enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
		else
			$this->nouv_cell('-');
		if( $enchere->id_royaume == $this->royaume->get_id() )
		{
			$cell = $this->nouv_cell( new interf_lien('Annuler', $this->url->get('action', 'annuler')) );
			if( $enchere->prix > 0 )
			{
				$cell->add( new interf_txt(' / ') );
				$cell->add( new interf_lien('Acheter', $this->url->get('action', 'achat_offre')) );
			}
		}
		else if( $enchere->id_royaume_acheteur == $this->royaume->get_id() )
			$this->nouv_cell('Vous avez vendu', false, 'text-success');
		else if( $this->perso->get_hp() > 0 )
			$this->nouv_cell( new interf_lien('Vendre', $this->url->get('action', 'offre_vente')) );
		else
			$this->nouv_cell( '&nbsp;' );
	}
}

class interf_bourse_hist_achat extends interf_bourse_liste
{
	function __construct()
	{
		parent::__construct('bourse_hist_achat');
	}
	protected function get_encheres()
	{
		$this->bourse->get_encheres('DESC', 'actif = 0 AND ( (type = "vente" AND id_royaume_acheteur & '.(1 << ($this->royaume->get_id()-1)).') OR (type = "achat" AND id_royaume = '.$this->royaume->get_id().') )');
	}
	protected function aff_fin_entete()
	{
		$this->nouv_cell('Type');
		$this->nouv_cell('Date');
	}
	protected function aff_fin_enchere(&$enchere)
	{
		if( $enchere->type == 'achat' )
		{
			$this->nouv_cell($enchere->nombre);
			$this->nouv_cell($enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
			$this->nouv_cell('Offre d\'achat');
		}
		else
		{
			$nb = 0;
			for($i=1; $i<0x1000; $i<<=1)
			{
				if( $enchere->id_royaume_acheteur & $i )
					$nb++;
			}
			$this->nouv_cell(round($enchere->nombre/$nb));
			$this->nouv_cell(round($enchere->prix/$nb).' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
			$this->nouv_cell('Vente aux enchères');
		}
		$this->nouv_cell( date_texte_red(strtotime($enchere->fin_vente)) );
	}
}

class interf_bourse_hist_vente extends interf_bourse_liste
{
	function __construct()
	{
		parent::__construct('bourse_hist_vente');
	}
	protected function get_encheres()
	{
		$this->bourse->get_encheres('DESC', 'actif = 0 AND ( (type = "achat" AND id_royaume_acheteur & '.(1 << ($this->royaume->get_id()-1)).') OR (type = "vente" AND id_royaume = '.$this->royaume->get_id().') )');
	}
	protected function aff_fin_entete()
	{
		$this->nouv_cell('Type');
		$this->nouv_cell('Date');
	}
	protected function aff_fin_enchere(&$enchere)
	{
		if( $enchere->type == 'vente' )
		{
			$this->nouv_cell($enchere->nombre);
			$this->nouv_cell($enchere->prix.' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
			$this->nouv_cell('Vente aux enchères');
		}
		else
		{
			$nb = 0;
			for($i=1; $i<0x1000; $i<<=1)
			{
				if( $enchere->id_royaume_acheteur & $i )
					$nb++;
			}
			$this->nouv_cell(round($enchere->nombre/$nb));
			$this->nouv_cell(round($enchere->prix/$nb).' ('.round(($enchere->prix / $enchere->nombre), 2).' / u)');
			$this->nouv_cell('Offre d\'achat');
		}
		$this->nouv_cell( date_texte_red(strtotime($enchere->fin_vente)) );
	}
}

class interf_dlg_bourse_vente extends interf_dialogBS
{
	function __construct()
	{
		global $G_url;
		parent::__construct('Vente de ressources');
		$form = $this->add( new interf_form($G_url->get('action', 'vendre'), 'vente_rsrc') );
		$rsrc = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$rsrc->add( new interf_bal_smpl('span', 'Ressource', null, 'input-group-addon') );
		$sel = $rsrc->add( new interf_select_form('ressource', false, false, 'form-control') );
		$sel->add_option('Nourriture', 'food');
		$sel->add_option('Bois', 'bois');
		$sel->add_option('Eau', 'eau');
		$sel->add_option('Pierre', 'pierre');
		$sel->add_option('Sable', 'sable');
		$sel->add_option('Essence magique', 'essence');
		$sel->add_option('Charbon', 'charbon');
		$nbr = $form->add_champ_bs('number', 'nombre', null, 1, 'Quantité');
    $nbr->set_attribut('min', 1);
    $nbr->set_attribut('step', 1);
		$prix = $form->add_champ_bs('number', 'prix', null, 1, 'Prix de vente', 'stars');
    $prix->set_attribut('min', 1);
    $prix->set_attribut('step', 1);
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Vendre', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'vente_rsrc\');', 'primary');
	}
}

class interf_dlg_bourse_achat extends interf_dialogBS
{
	function __construct()
	{
		global $G_url;
		parent::__construct('Offre d\'achat de ressources');
		$form = $this->add( new interf_form($G_url->get('action', 'acheter'), 'vente_rsrc') );
		$rsrc = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$rsrc->add( new interf_bal_smpl('span', 'Ressource', null, 'input-group-addon') );
		$sel = $rsrc->add( new interf_select_form('ressource', false, false, 'form-control') );
		$sel->add_option('Nourriture', 'food');
		$sel->add_option('Bois', 'bois');
		$sel->add_option('Eau', 'eau');
		$sel->add_option('Pierre', 'pierre');
		$sel->add_option('Sable', 'sable');
		$sel->add_option('Essence magique', 'essence');
		$sel->add_option('Charbon', 'charbon');
		$nbr = $form->add_champ_bs('number', 'nombre', null, 1, 'Quantité');
    $nbr->set_attribut('min', 1);
    $nbr->set_attribut('step', 1);
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Déposer l\'offre', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'vente_rsrc\');', 'primary');
	}
}

class interf_dlg_bourse_cours extends interf_dialogBS
{
	protected $ventes = array();
	protected $achats = array();
	protected $tbl;
	function __construct()
	{
		global $db;
		parent::__construct('Cours des ressources', true);
		/// @todo passer à l'objet
		$requete = "SELECT *, (SUM(prix / nombre) / COUNT(*)) as moyenne, COUNT(*) as tot, type FROM `bourse_royaume` WHERE `id_royaume_acheteur` != 0 AND `actif` = 0 AND fin_vente > DATE_SUB(NOW(), INTERVAL 31 DAY) GROUP BY ressource, type";
		
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			if( $type == 'vente' )
				$this->ventes[$row['ressource']] = round($row['moyenne'], 2).' ('.$row['tot'].' ventes)';
			else
				$this->achats[$row['ressource']] = round($row['moyenne'], 2).' ('.$row['tot'].' achats)';
		}
		$this->tbl = $this->add( new interf_data_tbl('bourse_cours', '', false, false) );
		$this->tbl->nouv_cell('Ressource');
		$this->tbl->nouv_cell('Vente');
		$this->tbl->nouv_cell('Achat');
		$this->aff_ressource('food');
		$this->aff_ressource('bois');
		$this->aff_ressource('eau');
		$this->aff_ressource('pierre');
		$this->aff_ressource('sable');
		$this->aff_ressource('essence');
		$this->aff_ressource('charbon');
	}
	protected function aff_ressource($rsrc)
	{
		global $Gtrad;
		$this->tbl->nouv_ligne();
		$this->tbl->nouv_cell($Gtrad[$rsrc], false, 'ressource '.$rsrc);
		$this->tbl->nouv_cell(array_key_exists($rsrc, $this->vente) ? $this->ventes[$rsrc] : '-');
		$this->tbl->nouv_cell(array_key_exists($rsrc, $this->achats) ? $this->achats[$rsrc] : '-');
		
	}
}

class interf_dlg_bourse_offre extends interf_dialogBS
{
	function __construct(&$enchere)
	{
		global $G_url;
		parent::__construct('Offre de vente');
		$form = $this->add( new interf_form($G_url->get(array('action'=>'offre_vente2','id'=>$enchere->id_bourse_royaume)), 'vente_rsrc') );
		$prix = $form->add_champ_bs('number', 'prix', null, round(.9 * $enchere->prix), 'Prix de vente', 'stars');
    $prix->set_attribut('min', 1);
    $prix->set_attribut('step', 1);
    if($enchere->prix > 0)
    	$prix->set_attribut('max', $enchere->prix-1);
    
    $this->ajout_btn('Annuler', 'fermer');
    $this->ajout_btn('Déposer l\'offre', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'vente_rsrc\');', 'primary');
	}
}

?>