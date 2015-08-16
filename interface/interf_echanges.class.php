<?php
/**
 * @file interf_echanges.class.php
 * Classes pour les échanges
 */

/// Classe pour la liste d'échange
class interf_echanges extends interf_data_tbl
{
	protected $perso;
	function __construct(&$perso, $actions=false)
	{
		global $G_url;
		parent::__construct('echanges', '', false, false, false, 400, 2);
		$this->perso = &$perso;
		$G_url->add('action', 'voir');
		
		$this->nouv_cell('N°');
		$this->nouv_cell('Avec');
		$this->nouv_cell('Statut');
		$this->nouv_cell('Date');
		
		/// @todo paser à l'objet
		$echanges = recup_tout_echange_perso($perso->get_id());
		foreach($echanges as $ech)
		{
			if( $ech['statut'] == 'annule' || $ech['statut'] == 'fini' )
				continue;
			if( $ech['statut'] == 'creation' && $ech['id_j1'] != $perso->get_id() )
				continue;
			$this->aff_echange($ech);
		}
	}
	function aff_echange(&$echange)
	{
		global $G_url;
		$id_autre = $echange['id_j1'] == $this->perso->get_id() ? $echange['id_j2'] : $echange['id_j1'];
		/// @todo paser à l'objet
		$autre = recupperso_essentiel($id_autre);
		$this->nouv_ligne();
		$lien = new interf_lien($echange['id_echange'], $G_url->get('id', $echange['id_echange']));
		$this->nouv_cell($lien);
		$this->nouv_cell($autre['nom']);
		$this->nouv_cell($echange['statut']);
		$this->nouv_cell( date('d-m-Y', $echange['date_debut']) );
	}
}

/// classe pour un échange
class interf_echange extends interf_cont
{
	protected $perso;
	protected $echange;
	function __construct($id, $perso_action=false)
	{
		global $G_url;
		$this->perso = &joueur::get_perso();
		/// @todo paser à l'objet
		if( $id )
		{
			$G_url->add('id', $id);
			$this->echange = recup_echange(sSQL($id, SSQL_INTEGER));
			switch( $this->echange['statut'] )
			{
			case 'creation':
				if( $perso_action &&  $this->echange['id_j1'] == $this->perso->get_id()  )
				{
					$this->aff_choix( $this->echange['id_j2'] );
				}
				break;
			case 'proposition':
				$this->aff_liste('liste_gauche', $this->echange['id_j1']);
				if( $perso_action )
				{
					if( $this->echange['id_j2'] == $this->perso->get_id() )
						$this->aff_choix( $this->echange['id_j1'] );
					else
						$this->aff_annuler();
				}
				break;
			case 'finalisation':
				$this->aff_liste('liste_gauche', $this->echange['id_j1']);
				$this->aff_liste('liste_droite', $this->echange['id_j2']);
				if( $perso_action )
				{
					if( $this->echange['id_j1'] == $this->perso->get_id() )
						$this->aff_final();
					else
						$this->aff_annuler();
				}
				break;
			case 'annule':
				$this->aff_liste('liste_gauche', $this->echange['id_j1']);
				$this->aff_liste('liste_droite', $this->echange['id_j2']);
				break;
			}
		}
		else
		{
			$this->echange = null;
			$G_url->add('perso', $perso_action);
			$this->aff_choix($perso_action);
		}
	}
	function aff_liste($id_div, $id_perso)
	{
		$div = $this->add( new interf_bal_cont('div', $id_div, 'liste_case') );
		if( $id_perso == $this->perso->get_id() )
			$texte =  'Vous proposez';
		else
		{
			/// @todo paser à l'objet
			$autre = recupperso_essentiel($id_perso);
			$texte = $autre['nom'].' propose';
		}
		$div->add( new interf_bal_smpl('span', $texte, false, 'xsmall') );
		$lst = $div->add( new interf_bal_cont('ul') );
		// Stars
		$stars = $this->echange['star'][$id_perso]['objet'];
		$li = $lst->add( new interf_bal_cont('li', false, 'info_case stars') );
		$li->add( new interf_bal_smpl('span', $stars.' star'.($stars>1?'s':'')) );
		foreach($this->echange['objet'] as $cle=>$obj)
		{
			if($obj['type'] == 'objet' && $obj['id_j'] == $id_perso)
			{
				/// @todo ajouter popover d'infos sur l'objet lors d'un click (et image ?)
				$nom = $obj['objet'][0] == 'h' ? 'Objet non indentifié' : nom_objet($obj['objet'], true);
				$li = $lst->add( new interf_bal_cont('li', false, 'info_case objet') );
				$li->add( new interf_bal_smpl('span', $nom) );
			}
		}
	}
	protected function aff_choix($id_autre)
	{
		global $G_url;
		$script = $this->add( new interf_bal_smpl('script', '') );
		$script->set_attribut('type', 'text/javascript');
		$script->set_attribut('src', 'javascript/echanges.js');
		$this->add( new interf_bal_smpl('h4', 'Vous proposez') );
		$bonus_soi = recup_bonus( $this->perso->get_id() );
		$bonus_autre = recup_bonus( $id_autre );
		if( !isset($bonus_soi[3]) )
			$this->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'Vous n\'avez pas le bonus shine pour donner des stars.') );
		if( !isset($bonus_autre[1]) )
			$this->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'L\'autre n\'a pas le bonus shine pour recevoir des stars.') );
		if( !isset($bonus_soi[4]) )
			$this->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'Vous n\'avez pas le bonus shine pour donner des objets.') );
		if( !isset($bonus_autre[2]) )
			$this->add( new interf_alerte(interf_alerte::msg_avertis, true, false, 'L\'autre n\'a pas le bonus shine pour recevoir des objets.') );
		$form = $this->add( new interf_form($G_url->get('action', 'modifier'), 'echange') );
		if( isset($bonus_autre[1]) && isset($bonus_soi[3]) )
		{
			$val = $this->echange ? $this->echange['star'][$this->perso->get_id()]['objet'] : '0';
			if( !$val )
				$val = '0';
			$stars = $form->add_champ_bs('number', 'stars', null, $val, null, 'stars');
			$stars->set_attribut('min', 0);
			$stars->set_attribut('max', $this->perso->get_star());
			$stars->set_attribut('step', 1);
		}
		else
			$form->add_champ_bs('hidden', 'stars', null, '0');
		$_SESSION['objets'] = array();
		// Liste des objets de l'inventaire
		$objets = array();
		$i = 0;
		foreach($this->perso->get_inventaire_slot_partie() as $invent)
		{
			if( !$invent || $invent[0] == 'r' )
				continue;
			$o = explode('x', $invent);
			$n = count($o) > 1 ? $o[1] : 1; 
			if( array_key_exists($o[0], $objets) )
				$objets[$o[0]] += $n;
			else
				$objets[$o[0]] = $n;
		}
		// Objets déjà dans l'échange
		$div_objs = $form->add( new interf_bal_cont('div', 'liste_objets') );
		$i = 0;
		foreach($this->echange['objet'] as $cle=>$obj)
		{
			if($obj['type'] == 'objet' && $obj['id_j'] == $this->perso->get_id())
			{
				$nom = $obj['objet'][0] == 'h' ? 'Objet non indentifié' : nom_objet($obj['objet']);
				$o = explode('x', $obj['objet']);
				$div = $div_objs->add( new interf_bal_cont('div', 'obj_'.$i, 'input-group') );
      	$div->add( new interf_bal_smpl('span', $nom, null, 'input-group-addon') );
      	$_SESSION['objets'][$i] = $o[0];
    		//$chp_nom = $div->add( new interf_chp_form('hidden', 'obj_'.$i, false, $i) );
      	$div->add( new interf_bal_smpl('span', 'X', null, 'input-group-addon') );
    		$chp_nbr = $div->add( new interf_chp_form('number', 'nbr_'.$i, false, count($o) > 1 ? $o[1] : 1, false, 'form-control') );
    		$chp_nbr->set_attribut('min', 1);
    		$chp_nbr->set_attribut('max', $objets[$o[0]]);
    		$chp_nbr->set_attribut('step', 1);
    		$span = $div->add( new interf_bal_cont('span', null, 'input-group-btn') );
    		$btn = $span->add( new interf_bal_smpl('button', '', false, 'btn btn-default icone icone-croix') );
    		$btn->set_attribut('type', 'button');
    		$btn->set_attribut('onclick', 'return suppr_objet_echg('.$i.');');
    		$btn->set_tooltip('Retirer cet objet de l\'échange');
    		unset($objets[$o[0]]);
			}
			$i++;
		}
		// Nouvel objet
		if( isset($bonus_autre[2]) && isset($bonus_soi[4]) )
		{
			$div = $form->add( new interf_bal_cont('div', null, 'input-group') );
	    $div->add( new interf_bal_smpl('span', 'Ajouter un objet', null, 'input-group-addon') );
	    $sel = $div->add( new interf_select_form(/*'nouv_obj'*/false, false, 'nouv_obj', 'form-control') );
	    foreach($objets as $o=>$n)
	    {
	    	$nom = $o[0] == 'h' ? 'Objet non indentifié' : nom_objet($o);
	    	$opt = $sel->add_option($nom, $i);
	    	//$opt->set_attribut('data-nom', $nom);
	    	$opt->set_attribut('data-nbr', $n);
	    	$_SESSION['objets'][$i] = $o;
	    	$i++;
			}
	    /*$div->add( new interf_bal_smpl('span', 'X', null, 'input-group-addon') );
	    $chp_nbr = $div->add( new interf_chp_form('number', 'nbr_'.$i, false, $o[1], false, 'form-control') );*/
	    $span = $div->add( new interf_bal_cont('span', null, 'input-group-btn') );
	    $btn = $span->add( new interf_bal_smpl('button', '', false, 'btn btn-default icone icone-plus') );
	    $btn->set_attribut('onclick', 'return ajout_objet_echg();');
	    $btn->set_tooltip('Ajouter cet objet à l\'échange');
		}
    // Boutons
    $div_btns = $form->add( new interf_bal_cont('div', false, 'btn-group') );
    if( $this->echange )
    {
			$lien = $div_btns->add( new interf_bal_smpl('a', 'Annuler', false, 'btn btn-default') );
			$lien->set_attribut('href', $G_url->get('action', 'annuler'));
			$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez vous annuler cet échange ?\');');
		}
    /// @todo finir la possibilité d'enregistrer sans valider
    /*$enregistrer = $div_btns->add( new interf_chp_form('submit', 'bouton', false, 'Enregistrer', false, 'btn btn-default') );
    $enregistrer->set_attribut('type', 'button');
    $enregistrer->set_attribut('onclick', 'return charger_formulaire(\'echange\');');
    $enregistrer->set_tooltip('Enregistre la proposition sans la valider pour pouvoir la retoucher plus tard.');*/
    $valider = $div_btns->add( new interf_chp_form('submit', 'bouton', false, 'Valider', false, 'btn btn-primary') );
    $valider->set_attribut('type', 'button');
    $valider->set_attribut('onclick', 'return charger_formulaire(\'echange\');');
    $valider->set_tooltip('Valide la proposition et l\'envoie à l\'autre joueur.');
	}
	protected function aff_final()
	{
		global $G_url;
    $div_btns = $this->add( new interf_bal_cont('div', false, 'btn-group') );
		$annuler = $div_btns->add( new interf_bal_smpl('a', 'Annuler', false, 'btn btn-default') );
		$annuler->set_attribut('href', $G_url->get('action', 'annuler'));
		$annuler->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez vous annuler cet échange ?\');');
    $valider = $div_btns->add( new interf_lien('Valider', $G_url->get('action', 'valider'), false, 'btn btn-primary') );
    $valider->set_tooltip('Effectue l\'échange.');
	}
	protected function aff_annuler()
	{
		global $G_url;
		$lien = $this->add( new interf_bal_smpl('a', 'Annuler', false, 'btn btn-default') );
		$lien->set_attribut('href', $G_url->get('action', 'annuler'));
		$lien->set_attribut('onclick', 'return verif_charger(this.href, \'Voulez vous annuler cet échange ?\');');
	}
}

?>