<?php
/**
 * @file interf_gest_criminels.class.php
 * Interface de 
 */ 

class interf_gest_criminels extends interf_data_tbl
{
	function __construct(&$royaume)
	{
  	global $G_url, $db, $Gtrad;
		parent::__construct('gest_crimes', '', false, false);
		
		$this->nouv_cell('Nom');
		$this->nouv_cell('Grade');
		$this->nouv_cell('Niveau');
		$this->nouv_cell('Pts crime');
		$this->nouv_cell('Statut');
		$this->nouv_cell('Amande');
		$this->nouv_cell('&nbsp;');
		
    //Sélection de tous les joueurs ayant des points de crime
    $persos = perso::create(false, false, 'crime DESC', false, 'crime > 0 AND race = "'.$royaume->get_race().'" AND perso.statut = "actif"');
    foreach($persos as $p)
    {
			$this->nouv_ligne();
			$this->nouv_cell( $lien = new interf_bal_smpl('a', $p->get_nom()) );
			$lien->set_attribut('href', '../messagerie.php?action=nouveau&type=perso&id='.$p->get_id());
			$lien->set_tooltip('Cliquer pour envoyer un message');
			$this->nouv_cell( $p->get_grade()->get_nom() );
			$this->nouv_cell( $p->get_level() );
			$this->nouv_cell( $p->get_crime() );
			
			/// @todo passer à l'objet
	    $requete = "SELECT montant, statut FROM amende WHERE id_joueur = ".$p->get_id();
	    $req_a = $db->query($requete);
      if( $row_a = $db->read_assoc($req_a) )
			{
      	$amende = $row_a['montant'];
				$this->nouv_cell( $row_a['statut'] );
				$this->nouv_cell( $row_a['montant'] );
      }
    	else
    	{
				$this->nouv_cell('&nbsp;');
				$this->nouv_cell(0);
			}
			$this->nouv_cell( new interf_lien('Gérer', $G_url->get( array('action'=>'gerer', 'id'=>$p->get_id()) )) );
    }
	}
}

class interf_modif_criminel extends interf_dialogBS
{
	function __construct(&$criminel)
	{
		global $db, $G_url, $Trace, $Gtrad;
		parent::__construct('Gestion des criminels : '.$criminel->get_nom(), true, 'dlg_modif_crimes');
		$G_url->add('id', $criminel->get_id());
		
    //Récupère l'amende
    $amende = recup_amende($criminel->get_id());
    $amende_max = ($criminel->get_crime() * $criminel->get_crime()) * 10;
    $etats = array('normal');
    if($criminel->get_crime() > 30) $etats[] = 'bandit';
    if($criminel->get_crime() > 60) $etats[] = 'criminel';
    
		$form = $this->add( new interf_form($G_url->get('action', 'modifier'), 'modif_crimes') );
		$montant = $form->add_champ_bs('number', 'montant', null, $amende['montant'], 'Montant de l\'amende (max : '.$amende_max.')');
		$montant->set_attribut('min', 1);
		$montant->set_attribut('max', $amende_max);
		$montant->set_attribut('step', 1);
		$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$span = $div->add( new interf_bal_cont('span', false, 'input-group-addon') );
		$acces_ville = $span->add( new interf_chp_form('checkbox', 'acces_ville') );
		//$acces_ville = $form->add_champ_bs('checkbox', 'acces_ville', null, null, null, 'Empèche le joueur d\'accéder à la ville');
		if( $amende['acces_ville'] )
			$acces_ville->set_attribut('checked', 'checked');
		$div->add( new interf_bal_smpl('span', 'Empèche le joueur d\'accéder à la ville', false, 'input-group-addon') );
		$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$span = $div->add( new interf_bal_cont('span', false, 'input-group-addon') );
		$raz_ville = $span->add( new interf_chp_form('checkbox', 'raz_ville') );
		//$raz_ville = $form->add_champ_bs('checkbox', 'raz_ville', null, null, null, 'Empèche de renaître à la ville');
		if( $amende['respawn_ville'] )
			$raz_ville->set_attribut('checked', 'checked');
		if( $criminel->get_crime() > 30 )
			$raz_ville->set_attribut('disabled', 'disabled');
		$div->add( new interf_bal_smpl('span', 'Empèche de renaître à la ville', false, 'input-group-addon') );
		$div = $form->add( new interf_bal_cont('div', false, 'input-group') );
		$div->add( new interf_bal_smpl('span', 'Statut', false, 'input-group-addon') );
		$sel = $div->add( new interf_select_form('statut', false, null, 'form-control') );
		foreach($etats as $etat)
			$sel->add_option($etat, $etat, $amende['statut'] == $etat);
		$this->ajout_btn('Annuler', 'fermer');
		if( count(amende) )
			$this->ajout_btn('Supprimer', '$(\'#modal\').modal(\'hide\'); return verif_charger(\''.$G_url->get('action', 'modifier').'\', \'Êtes-vous sûr de vouloir supprimer  l\'amande ?\');', 'danger');
		$this->ajout_btn('Valider', '$(\'#modal\').modal(\'hide\'); return charger_formulaire(\'modif_crimes\');', 'primary');
	}
}

?>