<?php
/**
 * @file interf_gest_diplo.class.php
 * Interface de gestion de la diplomatie
 */ 

class interf_gest_diplo extends interf_data_tbl
{
	function __construct(&$royaume, $change)
	{
  	global $db, $Trace, $Gtrad, $G_url, $Tclasse;
		parent::__construct('gestion_diplomatie', '', false, false);
		
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Royaume');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Diplomatie');
		if( $change )
			$this->nouv_cell('Modification');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Roi');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Ministre militaire');
		$this->nouv_cell('&nbsp;');
		$this->nouv_cell('Ministre de l\'économie');
		//$this->nouv_cell('Message');
		
		$diplo = unserialize($royaume->get_diplo_time());
		/// @todo passer à l'objet
		$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$royaume->get_race()."'");
		$row = $db->read_assoc($req);
		$i = 0;
		$keys = array_keys($row);
		$count = count($keys);
		while($i < $count)
		{
			$i++;
			if(!$keys[$i] || $keys[$i] == 'race' || $row[$keys[$i]] == 127)
				continue;
			$G_url->add('id', $Trace[$keys[$i]]['numrace']);
			$temps = $diplo[$keys[$i]] - time();
			/*if($temps > 0) $show = transform_sec_temp($temps).' avant changement possible';
			else $show = 'Modif. Possible';*/
			switch($row[$keys[$i]])
			{
			case '0' :
				$image_diplo = '../image/icone/diplomatie_paixdurable.png';					
				break;
			case '1' :
				$image_diplo = '../image/icone/diplomatie_paixdurable.png';					
				break;
			case '2' :
				$image_diplo = '../image/icone/diplomatie_paixdurable.png';
				break;
			case '3' :
				$image_diplo = '../image/icone/diplomatie_paix.png';					
				break;
			case '4' :
				$image_diplo = '../image/icone/diplomatie_bonterme.png';					
				break;
			case '5' :
				$image_diplo = '../image/icone/diplomatie_neutre.png';					
				break;
			case '6' :
				$image_diplo = '../image/icone/diplomatie_mauvaisterme.png';					
				break;
			case '7' :
				$image_diplo = '../image/icone/diplomatie_guerre.png';					
				break;
			case '8' :
				$image_diplo = '../image/icone/diplomatie_guerredurable.png';
				break;				
			case '9' :
				$image_diplo = '../image/icone/diplomatie_guerredurable.png';
				break;
			case '10' :
				$image_diplo = '../image/icone/diplomatie_guerredurable.png';
				break;
			}
			$roi = perso::create(array('rang_royaume', 'race'), array(6, $keys[$i]))[0];
			$R = new royaume($Trace[$keys[$i]]['numrace']);
			$eco = $R->get_ministre_economie() ? new perso($R->get_ministre_economie()) : null;
			$mil = $R->get_ministre_militaire() ? new perso($R->get_ministre_militaire()) : null;
			$this->nouv_ligne();
			$this->nouv_cell( new interf_img('../image/g_etendard/g_etendard_'.$Trace[$keys[$i]]['numrace'].'.png') );
			$this->nouv_cell( $Gtrad[$keys[$i]] );
			$this->nouv_cell( new interf_img($image_diplo) );
			$this->nouv_cell( $Gtrad['diplo'.$row[$keys[$i]]] );	
			if( $change )
			{
				if( $temps > 0 )
					$this->nouv_cell(transform_sec_temp($temps).' avant changement possible', false, 'xsmall');
				else
					$this->nouv_cell( new interf_lien('Modif. Possible', $G_url->get('action', 'modifier')) );
			}
			/// @todo à centraliser
			if( $roi )
			{
				$classe = $roi->get_cache_classe() >= 1 ? 'combattant' : $Tclasse[$roi->get_classe()]["type"];
				$this->nouv_cell( new interf_img('../image/personnage/'.$roi->get_race().'/'.$roi->get_race().'_'.$classe.'.png') );
				$this->nouv_cell( $lien_roi = new interf_bal_smpl('a', $roi->get_nom()) );
				$lien_roi->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$roi->get_id());
				$lien_roi->set_tooltip('Cliquer pour envoyer un message');
			}
			else
			{
				$this->nouv_cell('&nbsp;');
				$this->nouv_cell('&nbsp;');
			}
			if( $mil )
			{
				$classe = $mil->get_cache_classe() >= 1 ? 'combattant' : $Tclasse[$mil->get_classe()]["type"];
				$this->nouv_cell( new interf_img('../image/personnage/'.$mil->get_race().'/'.$mil->get_race().'_'.$classe.'.png') );
				$this->nouv_cell( $lien_mil = new interf_bal_smpl('a', $mil->get_nom()) );
				$lien_mil->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$mil->get_id());
				$lien_mil->set_tooltip('Cliquer pour envoyer un message');
			}
			else
			{
				$this->nouv_cell('&nbsp;');
				$this->nouv_cell('&nbsp;');
			}
			if( $eco )
			{
				$classe = $eco->get_cache_classe() >= 1 ? 'combattant' : $Tclasse[$eco->get_classe()]["type"];
				$this->nouv_cell( new interf_img('../image/personnage/'.$eco->get_race().'/'.$eco->get_race().'_'.$classe.'.png') );
				$this->nouv_cell( $lien_eco = new interf_bal_smpl('a', $eco->get_nom()) );
				$lien_eco->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$eco->get_id());
				$lien_eco->set_tooltip('Cliquer pour envoyer un message');
			}
			else
			{
				$this->nouv_cell('&nbsp;');
				$this->nouv_cell('&nbsp;');
			}
			//$this->aff_chp_message( $Trace[$keys[$i]]['numrace'] );
		}
	}
	protected function aff_chp_message($id)
	{
		global $G_url;
		$this->nouv_cell( $lien = new interf_bal_smpl('a', '', false, 'icone icone-message') );
		$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=diplo&id='.$id);
	}
}

class interf_gest_diplo_shine extends interf_gest_diplo
{
	protected function aff_chp_message($id)
	{
		global $G_url;
		$this->nouv_cell( $lien = new interf_bal_cont('a') );
		$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=diplo&id='.$id);
		$lien->add( new interf_img('../image/interface/message.png') );
	}
}

class interf_demande_diplo extends interf_bal_cont
{
	public function __construct(&$royaume)
	{
		global $db, $G_url, $Gtrad;
		parent::__construct('div', 'demande_diplo');
		
		/// @todo passer à l'objet
		$requete = "SELECT * FROM diplomatie_demande WHERE royaume_recois = '".$royaume->get_race()."'";
		$req = $db->query($requete);
		if($db->num_rows > 0)
		{
			$this->add( new interf_bal_smpl('h3', 'Demande(s) diplomatique(s)') );
			$liste = $this->add( new interf_bal_cont('ul') );
			while($row = $db->read_assoc($req))
			{
				$li = $liste->add( new interf_bal_cont('li', false, 'info_case') );
				$G_url->add('id', $row['id']);
				$non = $li->add( new interf_lien('', $G_url->get('action', 'refuser'), false, 'icone icone-croix') );
				$non->set_tooltip('Refuser');
				$oui = $li->add( new interf_lien('', $G_url->get('action', 'accepter'), false, 'icone icone-ok') );
				$oui->set_tooltip('Accepter');
				$li->add( new interf_bal_smpl('span', 'Le roi '.$Gtrad[$row['royaume_demande']].' vous demande de passer en <strong>'.$Gtrad['diplo'.$row['diplo']].'</strong> et vous donne <em>'.$row['stars'].'</em> stars') );
			}
		}
	}
}

class interf_choix_diplo extends interf_dialogBS
{
	function __construct($royaume, $id)
	{
		global $db, $G_url, $Trace, $Gtrad;
		parent::__construct('Modification de la diplomatie avec '.$royaume->get_nom(), true, 'dlg_choix_diplo');
		$G_url->add('id', $id);
		
		/// @todo passer à l'objet
		$req = $db->query("SELECT * FROM diplomatie WHERE race = '".$royaume->get_race()."'");
		$row = $db->read_assoc($req);
		$diplo = $row[ $Trace['liste'][$id] ];
		/// @todo ne pas proposer quand c'est interdit
		// baisser
		if($diplo < 10)
		{
			$div = $this->add( new interf_bal_cont('div', false, 'input-group') );
			$div->add( new interf_bal_smpl('span', 'Baisser la diplomatie à <strong>'.$Gtrad['diplo'.($diplo + 1)].'</strong>', false, 'input-group-addon') );
			$btns = $div->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$btn = $btns->add( new interf_lien('Ok', $G_url->get('action', 'baisser'), false, 'btn btn-default') );
			$btn->set_attribut('onclick', '$(\'#modal\').modal(\'hide\'); return charger(this.href);');
		}
		// monter
		if($diplo > 0)
		{
			$form = $this->add( new interf_form($G_url->get('action', 'monter'), 'modif_diplo', 'get', 'input-group') );
			$form->add( new interf_bal_smpl('span', 'Proposer une montée de diplomatie à <strong>'.$Gtrad['diplo'.($diplo - 1)].'</strong> et ', false, 'input-group-addon') );
			$stars = $form->add( new interf_chp_form('number', 'stars', false, 0, false, 'form-control') );
	    $stars->set_attribut('min', 0);
	    $stars->set_attribut('step', 1);
	    $stars->set_attribut('max', $royaume->get_star());
			$form->add( new interf_bal_smpl('span', 'stars', false, 'input-group-addon') );
			$btns = $form->add( new interf_bal_cont('span', false, 'input-group-btn') );
			$btn = $btns->add( new interf_chp_form('submit', false, false, 'Ok', false, 'btn btn-default') );
			$btn->set_attribut('onclick', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'modif_diplo\');');
			$p = $this->add( new interf_bal_cont('p', false, 'xsmall') );
			$p->add( new interf_bal_smpl('span', 'Vous pouvez donner des stars au royaume destinataire de la demande en échange de son acceptation.') ); 
			$p->add( new interf_bal_smpl('br') ); 
			$p->add( new interf_bal_smpl('span', 'Ces stars seront prise dès l\'envoi de la demande.') ); 
		}
	}
}

?>