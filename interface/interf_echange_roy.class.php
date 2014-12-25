<?php
/**
 * @file interf_echange_roy.class.php
 * Interface de la bourse des royaume 
 */ 

class interf_echange_roy extends interf_data_tbl
{
	function __construct($action)
	{
  	global $db, $Trace, $Gtrad, $G_url;
		parent::__construct('echanges_royaumes', '', false, false);
		$perso = joueur::get_perso();
		
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Royaume');
		$this->nouv_cell('Prochain échange possible');
		$this->nouv_cell('Nombre d\'échanges finis');
		if($action)
			$this->nouv_cell('Échange');
		
		/// @todo passer à l'objet
		$requete = "SELECT ID, race FROM royaume WHERE ID != 0 AND ID != '".$Trace[$perso->get_race()]['numrace']."'";
		$req = $db->query($requete);
		while( $row = $db->read_assoc($req) )
		{
			$req_tmp = $db->query("SELECT date_fin FROM echange_royaume WHERE statut = 'fini' AND ((id_r2 = '".$Trace[$row['race']]['numrace']."' AND id_r1 = '".$Trace[$perso->get_race()]['numrace']."') OR (id_r1 = '".$Trace[$row['race']]['numrace']."' AND id_r2 = '".$Trace[$perso->get_race()]['numrace']."')) ORDER BY date_fin DESC LIMIT 0,1");
			$row_tmp = $db->read_assoc($req_tmp);
			$temps = max($row_tmp['date_fin'] + (60*60*24*7) - time(), 0);
			
			$req_ech = $db->query("SELECT id_echange FROM echange_royaume WHERE statut != 'fini' AND statut != 'annule' AND ((id_r2 = '".$Trace[$row['race']]['numrace']."' AND id_r1 = '".$Trace[$perso->get_race()]['numrace']."') OR (id_r1 = '".$Trace[$row['race']]['numrace']."' AND id_r2 = '".$Trace[$perso->get_race()]['numrace']."')) ORDER BY date_fin DESC LIMIT 0,1");
			
			$req_fin = $db->query("SELECT COUNT(*) FROM echange_royaume WHERE statut = 'fini' AND ((id_r2 = '".$Trace[$row['race']]['numrace']."' AND id_r1 = '".$Trace[$perso->get_race()]['numrace']."') OR (id_r1 = '".$Trace[$row['race']]['numrace']."' AND id_r2 = '".$Trace[$perso->get_race()]['numrace']."')) ORDER BY date_fin");
			$row_fin = $db->read_array($req_fin);
			
			$this->nouv_ligne();
			$this->nouv_cell( new interf_img('../image/g_etendard/g_etendard_'.$Trace[$row['race']]['numrace'].'.png') );
			$this->nouv_cell( $Gtrad[$row['race']] );
			$this->nouv_cell($temps ? transform_sec_temp($temps) : 'maintenant');
			$this->nouv_cell( $row_fin[0] );
			if( $action )
			{
				if( $temps > 0 )
					$this->nouv_cell('&nbsp;');
				else if( $row_ech = $db->read_assoc($req_ech) )
					$this->nouv_cell( new interf_lien('Échange en cours', $G_url->get(array('id'=>$row_ech['id_echange'], 'action'=>'echanger'))) );
				else
					$this->nouv_cell( new interf_lien('Échanger', $G_url->get(array('id'=>$row['ID'], 'action'=>'creer'))) );
			}
		}
	}
}

class interf_echg_roy_dlg extends interf_dialogBS
{
	protected $echange;
	protected $form;
	function __construct(&$echange)
	{
		global $G_url, $Trace;
		parent::__construct('Échange', true);
		$this->echange = &$echange;
		/// @todo passer à l'objet
		$r1 = new royaume($echange['id_r1']);
		$r2 = new royaume($echange['id_r2']);
		$perso = joueur::get_perso();
		
		$G_url->add('id', $echange['id_echange']);
		if( $echange['statut'] == 'proposition' || $echange['statut'] == 'finalisation' || $echange['statut'] == 'fini' )
			$this->aff_proposition($r1);
		if( $echange['statut'] == 'finalisation' || $echange['statut'] == 'fini' )
			$this->aff_proposition($r2);
		if( ($echange['statut'] == 'creation' && $echange['id_r1'] == $Trace[$perso->get_race()]['numrace']) || ($echange['statut'] == 'proposition' && $echange['id_r2'] == $Trace[$perso->get_race()]['numrace']) )
		{
			$div = $this->add( new interf_bal_cont('div') );
			$div->add( new interf_bal_smpl('h6', 'Vous proposez :') );
			$this->form = $this->add( new interf_form($G_url->get('action', 'valider'), 'echange_rsrc') );
			$this->aff_chp('star');
			$this->aff_chp('food');
			$this->aff_chp('bois');
			$this->aff_chp('eau');
			$this->aff_chp('pierre');
			$this->aff_chp('sable');
			$this->aff_chp('essence');
			$this->aff_chp('charbon');
			/// @todo ajouter la possibilité de copier un ancien échange
    
	    //$this->ajout_btn('Annuler', 'fermer');
	    $this->ajout_btn('Supprimer', '$(\'#modal\').modal(\'hide\'); return charger(\''.$G_url->get('action', 'suppr').'\');');
	    $this->ajout_btn('Proposer', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'echange_rsrc\');', 'primary');
		}
		else if($echange['id_r1'] == $Trace[$perso->get_race()]['numrace'] && $echange['statut'] != 'fini')
		{
	    $this->ajout_btn('Supprimer', '$(\'#modal\').modal(\'hide\'); return charger(\''.$G_url->get('action', 'suppr').'\');');
	    $this->ajout_btn('Finaliser', '$(\'#modal\').modal(\'hide\'); return charger(\''.$G_url->get('action', 'valider').'\');', 'primary');
		}
	}
	protected function aff_proposition(&$royaume)
	{
		$div = $this->add( new interf_bal_cont('div') );
		$div->add( new interf_bal_smpl('h6', 'Proposition de '.$royaume->get_nom()) );
		/// @todo à améliorer
		if(is_array($this->echange['ressource']))
		{
			$i = 0;
			$keys = array_keys($this->echange['ressource']);
			$count = count($this->echange['ressource']);
			$liste = $div->add( new interf_bal_cont('ul') );
			while($i < $count)
			{
				if( array_key_exists($royaume->get_id(), $this->echange['ressource'][$keys[$i]]) )
				{
					$liste->add( new interf_bal_smpl('li', $keys[$i].' : '.$this->echange['ressource'][$keys[$i]][$royaume->get_id()]['nombre']) );
				}
				$i++;
			}
		}
	}
	protected function aff_chp($nom)
	{
		global $Gtrad;
		$rsrc = $this->form->add_champ_bs('number', $nom, null, 0, $Gtrad[$nom]);
    $rsrc->set_attribut('min', 0);
    $rsrc->set_attribut('step', 1);
	}
}
?>